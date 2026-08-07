document.addEventListener('DOMContentLoaded', () => {
    MonitoringDashboard.init();
});

const MonitoringDashboard = {
    activitySelect: null,
    groupSelect: null,
    unitSelect: null,

    init() {
        if (!document.getElementById('monitoringDashboard')) {
            return;
        }

        this.activitySelect = document.getElementById('activityFilter');
        this.groupSelect = document.getElementById('groupFilter');
        this.unitSelect = document.getElementById('unitFilter');

        this.bindFilterDependencies();
        this.bindDetailButtons();

        // Saat halaman pertama kali dibuka / reload setelah filter
        this.refreshGroupOptions(false);
        this.refreshUnitOptions(false);
    },

    bindFilterDependencies() {
        // ACTIVITY BERUBAH
        this.activitySelect?.addEventListener('change', () => {
            if (this.groupSelect) {
                this.groupSelect.value = '';
            }

            if (this.unitSelect) {
                this.unitSelect.value = '';
            }

            this.refreshGroupOptions(true);
            this.refreshUnitOptions(true);
        });

        // GROUP BERUBAH
        this.groupSelect?.addEventListener('change', () => {
            if (this.unitSelect) {
                this.unitSelect.value = '';
            }

            this.refreshUnitOptions(true);
        });
    },

    refreshGroupOptions(resetInvalid = false) {
        if (!this.activitySelect || !this.groupSelect) {
            return;
        }

        const activityId = String(this.activitySelect.value || '');

        const options = Array.from(this.groupSelect.options);

        options.forEach((option, index) => {
            // option pertama = Semua Group
            if (index === 0) {
                option.hidden = false;
                option.disabled = false;
                return;
            }

            const optionActivityId = String(
                option.dataset.activityId || ''
            );

            // Kalau Activity belum dipilih,
            // sembunyikan semua Group
            const visible =
                activityId !== '' &&
                optionActivityId === activityId;

            option.hidden = !visible;
            option.disabled = !visible;
        });

        // Kalau activity belum dipilih
        if (!activityId) {
            this.groupSelect.value = '';
            this.groupSelect.disabled = true;

            if (this.unitSelect) {
                this.unitSelect.value = '';
                this.refreshUnitOptions(true);
            }

            return;
        }

        this.groupSelect.disabled = false;

        // Cek apakah selected group masih valid
        if (
            resetInvalid &&
            this.groupSelect.value &&
            this.groupSelect.selectedOptions[0]?.disabled
        ) {
            this.groupSelect.value = '';
        }
    },

    refreshUnitOptions(resetInvalid = false) {
        if (!this.groupSelect || !this.unitSelect) {
            return;
        }

        const groupId = String(this.groupSelect.value || '');

        const options = Array.from(this.unitSelect.options);

        options.forEach((option, index) => {
            // option pertama = Semua Unit
            if (index === 0) {
                option.hidden = false;
                option.disabled = false;
                return;
            }

            const optionGroupId = String(
                option.dataset.groupId || ''
            );

            // Kalau Group belum dipilih,
            // sembunyikan semua Unit
            const visible =
                groupId !== '' &&
                optionGroupId === groupId;

            option.hidden = !visible;
            option.disabled = !visible;
        });

        // Kalau group belum dipilih
        if (!groupId) {
            this.unitSelect.value = '';
            this.unitSelect.disabled = true;
            return;
        }

        this.unitSelect.disabled = false;

        // Cek apakah selected unit masih valid
        if (
            resetInvalid &&
            this.unitSelect.value &&
            this.unitSelect.selectedOptions[0]?.disabled
        ) {
            this.unitSelect.value = '';
        }
    },

    bindDetailButtons() {
        document.addEventListener('click', (event) => {
            const button = event.target.closest(
                '[data-respondent-detail]'
            );

            if (!button) {
                return;
            }

            this.openRespondentDetail(
                button.dataset.url
            );
        });
    },

    async openRespondentDetail(url) {
        const loading = document.getElementById(
            'respondentDetailLoading'
        );

        const content = document.getElementById(
            'respondentDetailContent'
        );

        const error = document.getElementById(
            'respondentDetailError'
        );

        loading?.classList.remove('hidden');
        content?.classList.add('hidden');
        error?.classList.add('hidden');

        if (
            typeof Modal !== 'undefined' &&
            typeof Modal.open === 'function'
        ) {
            Modal.open('respondentDetailModal');
        }

        try {
            const response = await fetch(url, {
                headers: {
                    Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });

            if (!response.ok) {
                throw new Error(
                    'Data responden tidak dapat dimuat.'
                );
            }

            const data = await response.json();

            this.renderProfile(data.profile);
            this.renderSurvey(data.survey);
            this.renderAnswers(data.answers || []);

            loading?.classList.add('hidden');
            content?.classList.remove('hidden');

        } catch (exception) {
            loading?.classList.add('hidden');

            if (error) {
                error.textContent =
                    exception.message ||
                    'Terjadi kesalahan saat mengambil data.';

                error.classList.remove('hidden');
            }
        }
    },

    renderProfile(profile) {
        const grid = document.getElementById(
            'respondentProfileGrid'
        );

        if (!grid) return;

        grid.innerHTML = '';

        const fields = [
            ['Username', profile.username],
            ['Nama Lengkap', profile.fullname],
            ['Email', profile.email],
            ['No. Handphone', profile.no_handphone],
            ['Role', profile.role],
            ['Activity', profile.activity],
            ['Group', profile.group],
            ['Unit', profile.unit],
        ];

        fields.forEach(([label, value]) => {
            const wrapper = document.createElement('div');
            const term = document.createElement('dt');
            const description = document.createElement('dd');

            term.className =
                'text-xs font-medium uppercase tracking-wide text-gray-400';

            description.className =
                'mt-1 font-medium text-gray-800';

            term.textContent = label;
            description.textContent = value || '-';

            wrapper.append(
                term,
                description
            );

            grid.appendChild(wrapper);
        });
    },

    renderSurvey(survey) {
        const summary = document.getElementById(
            'respondentSurveySummary'
        );

        if (!summary) return;

        summary.innerHTML = '';

        const statusColor = {
            completed:
                'bg-emerald-100 text-emerald-700',

            in_progress:
                'bg-amber-100 text-amber-700',

            not_started:
                'bg-slate-100 text-slate-600',
        }[survey.status] ||
            'bg-slate-100 text-slate-600';

        const status = document.createElement('span');

        status.className =
            `inline-flex rounded-full px-3 py-1 text-xs font-semibold ${statusColor}`;

        status.textContent =
            survey.status_label;

        summary.appendChild(status);

        [
            ['Mulai', survey.started_at],
            ['Selesai', survey.finished_at],
            ['Jumlah Jawaban', survey.answers_count],
        ].forEach(([label, value]) => {

            const row =
                document.createElement('div');

            row.className =
                'flex items-center justify-between gap-3';

            const labelElement =
                document.createElement('span');

            const valueElement =
                document.createElement('strong');

            labelElement.className =
                'text-indigo-600';

            valueElement.className =
                'text-right text-indigo-900';

            labelElement.textContent = label;
            valueElement.textContent =
                value ?? '-';

            row.append(
                labelElement,
                valueElement
            );

            summary.appendChild(row);
        });
    },

    renderAnswers(answers) {
        const body = document.getElementById(
            'respondentAnswerBody'
        );

        if (!body) return;

        body.innerHTML = '';

        if (!answers.length) {
            const row =
                document.createElement('tr');

            const cell =
                document.createElement('td');

            cell.colSpan = 4;

            cell.className =
                'px-4 py-10 text-center text-sm text-gray-500';

            cell.textContent =
                'Responden belum mempunyai jawaban.';

            row.appendChild(cell);
            body.appendChild(row);

            return;
        }

        answers.forEach((answer, index) => {
            const row =
                document.createElement('tr');

            row.className =
                'align-top hover:bg-gray-50';

            const numberCell =
                this.createCell(
                    index + 1,
                    'text-gray-500'
                );

            const formCell =
                this.createCell(
                    answer.form || '-',
                    'font-medium text-gray-700'
                );

            const questionCell =
                this.createCell(
                    `${answer.question_no
                        ? `${answer.question_no} - `
                        : ''
                    }${answer.question || '-'}`,
                    'text-gray-700'
                );

            const answerCell =
                this.createCell(
                    this.formatAnswer(answer.answer),
                    'whitespace-pre-wrap text-gray-700'
                );

            if (
                answer.competitor ||
                answer.subunit
            ) {
                const meta =
                    document.createElement('p');

                meta.className =
                    'mt-1 text-xs text-gray-400';

                meta.textContent = [
                    answer.competitor,
                    answer.subunit,
                ]
                    .filter(Boolean)
                    .join(' • ');

                answerCell.appendChild(meta);
            }

            row.append(
                numberCell,
                formCell,
                questionCell,
                answerCell
            );

            body.appendChild(row);
        });
    },

    createCell(value, extraClass = '') {
        const cell =
            document.createElement('td');

        cell.className =
            `px-4 py-3 text-sm ${extraClass}`;

        cell.textContent = value;

        return cell;
    },

    formatAnswer(value) {
        if (
            value === null ||
            value === undefined ||
            value === ''
        ) {
            return '-';
        }

        if (typeof value !== 'object') {
            return String(value);
        }

        if (Array.isArray(value)) {
            return value
                .map((item) =>
                    this.formatAnswer(item)
                )
                .join(', ');
        }

        return Object.entries(value)
            .map(([key, item]) =>
                `${this.humanize(key)}: ${this.formatAnswer(item)}`
            )
            .join('\n');
    },

    humanize(value) {
        return String(value)
            .replaceAll('_', ' ')
            .replace(
                /\b\w/g,
                (character) =>
                    character.toUpperCase()
            );
    },
};
