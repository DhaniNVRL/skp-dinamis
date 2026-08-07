document.addEventListener('DOMContentLoaded', () => {
    const dashboard = document.getElementById('monitoringDashboard');
    if (!dashboard) return;

    const activity = document.getElementById('activityFilter');
    const group = document.getElementById('groupFilter');
    const unit = document.getElementById('unitFilter');

    const filterOptions = (select, attribute, value) => {
        if (!select) return;
        [...select.options].forEach((option, index) => {
            if (index === 0) return;
            const visible = !value || option.dataset[attribute] === value;
            option.hidden = !visible;
            option.disabled = !visible;
        });
        if (select.selectedOptions[0]?.disabled) select.value = '';
    };

    activity?.addEventListener('change', () => {
        if (group) group.value = '';
        if (unit) unit.value = '';
        filterOptions(group, 'activityId', activity.value);
        filterOptions(unit, 'groupId', '');
    });
    group?.addEventListener('change', () => {
        if (unit) unit.value = '';
        filterOptions(unit, 'groupId', group.value);
    });

    filterOptions(group, 'activityId', activity?.value ?? '');
    filterOptions(unit, 'groupId', group?.value ?? '');

    const setText = (element, value) => {
        if (element) element.textContent = value === null || value === undefined || value === '' ? '-' : String(value);
    };

    const formatAnswer = (value) => {
        if (value === null || value === undefined || value === '') return '-';
        if (Array.isArray(value)) return value.map(formatAnswer).join(', ');
        if (typeof value === 'object') {
            return Object.entries(value)
                .map(([key, item]) => `${key.replaceAll('_', ' ')}: ${formatAnswer(item)}`)
                .join('\n');
        }
        return String(value);
    };

    const appendDefinition = (container, label, value) => {
        const wrapper = document.createElement('div');
        const term = document.createElement('dt');
        const description = document.createElement('dd');
        term.className = 'text-xs font-medium uppercase tracking-wide text-gray-400';
        description.className = 'mt-1 font-medium text-gray-800';
        term.textContent = label;
        setText(description, value);
        wrapper.append(term, description);
        container.appendChild(wrapper);
    };

    const renderProfile = (profile = {}) => {
        const container = document.getElementById('respondentProfileGrid');
        if (!container) return;
        container.replaceChildren();
        [
            ['Username', profile.username],
            ['Nama Lengkap', profile.fullname],
            ['Email', profile.email],
            ['No. Handphone', profile.no_handphone],
            ['Role', profile.role],
            ['Activity', profile.activity],
            ['Group', profile.group],
            ['Unit', profile.unit],
        ].forEach(([label, value]) => appendDefinition(container, label, value));
    };

    const renderSurvey = (survey = {}) => {
        const container = document.getElementById('respondentSurveySummary');
        if (!container) return;
        container.replaceChildren();

        const status = document.createElement('span');
        const color = {
            completed: 'bg-emerald-100 text-emerald-700',
            in_progress: 'bg-amber-100 text-amber-700',
            not_started: 'bg-slate-100 text-slate-600',
        }[survey.status] ?? 'bg-slate-100 text-slate-600';
        status.className = `inline-flex rounded-full px-3 py-1 text-xs font-semibold ${color}`;
        setText(status, survey.status_label);
        container.appendChild(status);

        [['Mulai', survey.started_at], ['Selesai', survey.finished_at], ['Jumlah Jawaban', survey.answers_count]]
            .forEach(([label, value]) => {
                const row = document.createElement('div');
                const labelElement = document.createElement('span');
                const valueElement = document.createElement('strong');
                row.className = 'flex items-center justify-between gap-3';
                labelElement.className = 'text-indigo-600';
                valueElement.className = 'text-right text-indigo-900';
                labelElement.textContent = label;
                setText(valueElement, value);
                row.append(labelElement, valueElement);
                container.appendChild(row);
            });
    };

    const createCell = (value, className = '') => {
        const cell = document.createElement('td');
        cell.className = `px-4 py-3 text-sm ${className}`;
        setText(cell, value);
        return cell;
    };

    const renderAnswers = (answers = []) => {
        const body = document.getElementById('respondentAnswerBody');
        if (!body) return;
        body.replaceChildren();

        if (!answers.length) {
            const row = document.createElement('tr');
            const cell = createCell('Responden belum mempunyai jawaban.', 'py-10 text-center text-gray-500');
            cell.colSpan = 4;
            row.appendChild(cell);
            body.appendChild(row);
            return;
        }

        answers.forEach((answer, index) => {
            const row = document.createElement('tr');
            row.className = 'align-top hover:bg-gray-50';
            const answerCell = createCell(formatAnswer(answer.answer), 'whitespace-pre-wrap text-gray-700');
            const metadata = [answer.competitor, answer.subunit].filter(Boolean);
            if (metadata.length) {
                const meta = document.createElement('p');
                meta.className = 'mt-1 text-xs text-gray-400';
                meta.textContent = metadata.join(' • ');
                answerCell.appendChild(meta);
            }
            row.append(
                createCell(index + 1, 'text-gray-500'),
                createCell(answer.form, 'font-medium text-gray-700'),
                createCell(`${answer.question_no ? `${answer.question_no} - ` : ''}${answer.question ?? '-'}`, 'text-gray-700'),
                answerCell,
            );
            body.appendChild(row);
        });
    };

    document.addEventListener('click', async (event) => {
        const button = event.target.closest('[data-respondent-detail]');
        if (!button) return;

        const loading = document.getElementById('respondentDetailLoading');
        const content = document.getElementById('respondentDetailContent');
        const error = document.getElementById('respondentDetailError');
        loading?.classList.remove('hidden');
        content?.classList.add('hidden');
        error?.classList.add('hidden');
        window.Modal?.open('respondentDetailModal');

        try {
            const response = await fetch(button.dataset.url, {
                credentials: 'same-origin',
                headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            });
            if (!response.ok) throw new Error('Data responden tidak dapat dimuat.');
            const data = await response.json();
            renderProfile(data.profile);
            renderSurvey(data.survey);
            renderAnswers(data.answers);
            loading?.classList.add('hidden');
            content?.classList.remove('hidden');
        } catch (exception) {
            loading?.classList.add('hidden');
            if (error) {
                error.textContent = exception instanceof Error ? exception.message : 'Terjadi kesalahan saat mengambil data.';
                error.classList.remove('hidden');
            }
        }
    });
});
