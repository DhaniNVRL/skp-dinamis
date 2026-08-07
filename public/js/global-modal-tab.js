document.addEventListener('alpine:init', () => {

    const modal = document.getElementById('globalModal');

    if (!modal) {
        return;
    }

    const title = document.getElementById('modalTitle');
    const content = document.getElementById('modalContent');

    let editorInitialized = false;

    // =========================
    // OPEN MODAL
    // =========================
    window.addEventListener('open-modal-tab', (e) => {

        content.innerHTML = '';
        title.innerText = e.detail.title ?? '';
        content.innerHTML = e.detail.content ?? '';

        modal.classList.remove('hidden');
        modal.classList.add('flex');

        const manualForm = content.querySelector('#manualForm');
        const excelForm = content.querySelector('#excelForm');

        if (manualForm && e.detail.manual) {
            manualForm.action = e.detail.manual;
        }

        if (excelForm && e.detail.excel) {
            excelForm.action = e.detail.excel;
        }

        content.querySelectorAll('[name="group_id"]').forEach(input => {
            input.value = e.detail.group ?? '';
        });

        const formInput = content.querySelector('[name="form_id"]');
        if (formInput && e.detail.form) {
            formInput.value = e.detail.form;
        }

        const questionInput = content.querySelector('[name="question_id"]');
        if (questionInput && e.detail.question) {
            questionInput.value = e.detail.question;
        }

        const tabButtons = content.querySelectorAll('.tab-btn');

        tabButtons.forEach(btn => {

            btn.onclick = () => {

                tabButtons.forEach(b => {
                    b.classList.remove('border-blue-600', 'text-blue-600');
                });

                content.querySelectorAll('[data-content]')
                    .forEach(c => c.classList.add('hidden'));

                btn.classList.add('border-blue-600', 'text-blue-600');

                const target = content.querySelector(
                    `[data-content="${btn.dataset.tab}"]`
                );

                if (target) target.classList.remove('hidden');

            };

        });

        const rows = content.querySelector('#rows');
        const addRow = content.querySelector('#addRow');

        if (rows && addRow) {

            addRow.onclick = () => {

                const firstRow = rows.querySelector('.row');
                if (!firstRow) return;

                const clone = firstRow.cloneNode(true);

                clone.querySelectorAll('input').forEach(input => {
                    input.value = '';
                });

                rows.appendChild(clone);
            };

            rows.onclick = (ev) => {

                if (ev.target.classList.contains('remove')) {

                    const allRows = rows.querySelectorAll('.row');

                    if (allRows.length <= 1) return;

                    ev.target.closest('.row').remove();
                }

            };
        }

        setTimeout(() => {
            initEditor();
        }, 200);

    });

    // =========================
    // CLOSE MODAL
    // =========================
    modal.querySelectorAll('[data-close]').forEach(btn => {

        btn.onclick = () => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            content.innerHTML = '';
            editorInitialized = false;
        };

    });

    // =========================
    // EDITOR ENGINE
    // =========================
    function initEditor() {

        if (editorInitialized) return;
        editorInitialized = true;

        const editor = document.getElementById('editor');
        const input = document.getElementById('contentInput');

        if (!editor) return;

        const buttons = content.querySelectorAll('.cmd');
        const tableBtn = document.getElementById('insertTable');

        editor.focus();

        // =========================
        // COMMAND BUTTONS
        // =========================
        buttons.forEach(btn => {

            btn.onclick = () => {

                const cmd = btn.dataset.cmd;

                // WAJIB fokus dulu
                editor.focus();

                // ambil selection sebelum delay hilang
                const selection = window.getSelection();

                setTimeout(() => {

                    // =========================
                    // LIST FIX FINAL (STABLE)
                    // =========================
                    if (cmd === 'insertUnorderedList' || cmd === 'insertOrderedList') {

                        // 🔥 restore selection (INI YANG KAMU BUTUH)
                        if (selection.rangeCount > 0) {
                            const range = selection.getRangeAt(0);
                            editor.focus();
                            selection.removeAllRanges();
                            selection.addRange(range);
                        }

                        document.execCommand(cmd, false, null);

                    }
                    else if (cmd === 'formatBlock') {

                        document.execCommand(cmd, false, btn.value);

                    }
                    else {

                        document.execCommand(cmd, false, null);

                    }

                    updateActiveState();

                }, 0);

            };

        });

        // =========================
        // TABLE
        // =========================
        if (tableBtn) {

            tableBtn.onclick = () => {

                const row = prompt('Rows?', 2);
                const col = prompt('Cols?', 2);

                if (!row || !col) return;

                let table = "<table style='width:100%; border-collapse:collapse; border:1px solid #000;'>";

                for (let r = 0; r < row; r++) {
                    table += "<tr>";

                    for (let c = 0; c < col; c++) {
                        table += "<td style='padding:8px; border:1px solid #000;'>...</td>";
                    }

                    table += "</tr>";
                }

                table += "</table><br>";

                editor.focus();

                setTimeout(() => {
                    document.execCommand('insertHTML', false, table);
                }, 10);
            };
        }

        // =========================
        // ACTIVE STATE (FIXED)
        // =========================
        function updateActiveState() {

            buttons.forEach(btn => {

                const cmd = btn.dataset.cmd;
                if (!cmd) return;

                let state = false;

                try {
                    state = document.queryCommandState(cmd);
                } catch (e) {}

                if (state) {
                    btn.classList.add('bg-blue-500', 'text-white');
                } else {
                    btn.classList.remove('bg-blue-500', 'text-white');
                }

            });
        }

        // =========================
        // TRACK
        // =========================
        editor.addEventListener('keyup', updateActiveState);
        editor.addEventListener('mouseup', updateActiveState);
        editor.addEventListener('input', updateActiveState);

        // =========================
        // SYNC FORM
        // =========================
        const form = content.querySelector('#manualForm');

        if (form && !form.dataset.editorBound) {

            form.dataset.editorBound = "true";

            form.addEventListener('submit', () => {
                input.value = editor.innerHTML;
            });

        }

    }

});
