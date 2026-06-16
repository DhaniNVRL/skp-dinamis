document.addEventListener('alpine:init', () => {

    // =========================
    // MODAL
    // =========================
    const modal = document.getElementById('globalModal');

    if (!modal) {
        console.error('globalModal not found');
        return;
    }

    const title = document.getElementById('modalTitle');
    const content = document.getElementById('modalContent');

    // =========================
    // OPEN MODAL
    // =========================
    window.addEventListener('open-modal-tab', (e) => {

        // RESET CONTENT
        content.innerHTML = '';

        // SET TITLE
        title.innerText = e.detail.title ?? '';

        // SET CONTENT
        content.innerHTML = e.detail.content ?? '';

        // SHOW MODAL
        modal.classList.remove('hidden');
        modal.classList.add('flex');

        // =========================
        // FORM REFERENCES
        // =========================
        const manualForm = content.querySelector('#manualForm');
        const excelForm = content.querySelector('#excelForm');

        // =========================
        // SET ACTION
        // =========================
        if (manualForm && e.detail.manual) {
            manualForm.action = e.detail.manual;
        }

        if (excelForm && e.detail.excel) {
            excelForm.action = e.detail.excel;
        }

        // =========================
        // SET GROUP ID
        // =========================
        content.querySelectorAll('[name="id_groups"]').forEach(input => {
            input.value = e.detail.group ?? '';
        });

        // =========================
        // SET FORM ID
        // =========================
        const formInput = content.querySelector('[name="form_id"]');

        if (formInput && e.detail.form) {
            formInput.value = e.detail.form;
        }

        // =========================
        // SET QUESTION ID
        // =========================
        const questionInput = content.querySelector('[name="question_id"]');

        if (questionInput && e.detail.question) {
            questionInput.value = e.detail.question;
        }

        // =========================
        // TAB SYSTEM
        // =========================
        const tabButtons = content.querySelectorAll('.tab-btn');

        tabButtons.forEach(btn => {

            btn.addEventListener('click', () => {

                // RESET BUTTON
                tabButtons.forEach(b => {
                    b.classList.remove(
                        'border-blue-600',
                        'text-blue-600'
                    );
                });

                // HIDE CONTENT
                content.querySelectorAll('[data-content]')
                    .forEach(c => c.classList.add('hidden'));

                // ACTIVE BUTTON
                btn.classList.add(
                    'border-blue-600',
                    'text-blue-600'
                );

                // SHOW TARGET
                const target = content.querySelector(
                    `[data-content="${btn.dataset.tab}"]`
                );

                if (target) {
                    target.classList.remove('hidden');
                }

            });

        });

        // =========================
        // UNIT ROW SYSTEM
        // =========================
        const rows = content.querySelector('#rows');
        const addRow = content.querySelector('#addRow');

        if (rows && addRow) {

            addRow.addEventListener('click', () => {

                const firstRow = rows.querySelector('.row');

                if (!firstRow) return;

                const clone = firstRow.cloneNode(true);

                clone.querySelectorAll('input').forEach(input => {
                    input.value = '';
                });

                rows.appendChild(clone);

            });

            rows.addEventListener('click', (ev) => {

                if (ev.target.classList.contains('remove')) {

                    const allRows = rows.querySelectorAll('.row');

                    if (allRows.length <= 1) {
                        return;
                    }

                    ev.target.closest('.row').remove();

                }

            });

        }

    });

    // =========================
    // CLOSE MODAL
    // =========================
    modal.querySelectorAll('[data-close]').forEach(btn => {

        btn.addEventListener('click', () => {

            modal.classList.add('hidden');
            modal.classList.remove('flex');

            content.innerHTML = '';

        });

    });

});