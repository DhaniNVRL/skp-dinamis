document.addEventListener('alpine:init', () => {

    // ===============================
    // 🔹 GLOBAL MODAL REFERENCES
    // ===============================
    const modal = document.getElementById('globalModal');
    if (!modal) return console.error('globalModal not found');

    const title = document.getElementById('modalTitle');
    const content = document.getElementById('modalContent');

    // ===============================
    // 🔹 UTILITY: Update row numbers
    // ===============================
    function updateNumbers(rowsContainer) {
        rowsContainer.querySelectorAll('.row').forEach((row, i) => {
            const dragHandle = row.querySelector('.drag');
            if (dragHandle) dragHandle.innerText = i + 1;
        });
    }

    // ===============================
    // 🔹 UTILITY: Update preview for manual form
    // ===============================
    function updatePreview(rowsContainer, previewContainer) {
        if (!previewContainer) return;
        previewContainer.innerHTML = '';
        rowsContainer.querySelectorAll('.row').forEach(row => {
            const text = row.querySelector('.answer-text')?.value || '';
            if (text) {
                const div = document.createElement('div');
                div.innerHTML = `<label><input type="radio" disabled> ${text}</label>`;
                previewContainer.appendChild(div);
            }
        });
    }

    // ===============================
    // 🔹 EVENT: Open Modal
    // ===============================
    window.addEventListener('open-modal-tab', (e) => {

        // -------------------------------
        // Set modal title & content
        // -------------------------------
        title.innerText = e.detail.title ?? '';
        content.innerHTML = e.detail.content ?? '';

        // -------------------------------
        // Form references
        // -------------------------------
        const manualForm = content.querySelector('form:not(#excelForm)');
        const groupInput = content.querySelector('[name="id_groups"]');
        const formIdInput = content.querySelector('[name="form_id"]');
        const questionInput = content.querySelector('[name="question_id"]');

        const rowsContainer = content.querySelector('#answerRows');
        const previewContainer = content.querySelector('#answerPreview');
        const addRowBtn = content.querySelector('#addAnswerRow');

        // -------------------------------
        // ✅ Set form action dynamically
        // -------------------------------
        if (manualForm && e.detail.manual) {
            manualForm.action = e.detail.manual;
        }

        // -------------------------------
        // Set hidden input values
        // -------------------------------
        if (groupInput && e.detail.group) groupInput.value = e.detail.group;
        if (formIdInput && e.detail.form) formIdInput.value = e.detail.form;
        if (questionInput && e.detail.question) questionInput.value = e.detail.question;

        // -------------------------------
        // Show modal
        // -------------------------------
        modal.classList.remove('hidden');
        modal.classList.add('flex');

        // ===============================
        // 🔹 Add Row Handler
        // ===============================
        addRowBtn?.addEventListener('click', () => {
            const row = rowsContainer.querySelector('.row').cloneNode(true);

            // Reset inputs
            row.querySelectorAll('input, select, textarea').forEach(input => input.value = '');

            // Hide child input by default
            const childDiv = row.querySelector('.child-input');
            if (childDiv) childDiv.classList.add('hidden');

            rowsContainer.appendChild(row);
            updateNumbers(rowsContainer);
            updatePreview(rowsContainer, previewContainer);
        });

        // ===============================
        // 🔹 Remove Row Handler
        // ===============================
        rowsContainer?.addEventListener('click', (evt) => {
            if (evt.target.classList.contains('remove')) {
                evt.target.closest('.row').remove();
                updateNumbers(rowsContainer);
                updatePreview(rowsContainer, previewContainer);
            }
        });

        // ===============================
        // 🔹 Update Preview on Input
        // ===============================
        rowsContainer?.addEventListener('input', (evt) => {
            if (evt.target.classList.contains('answer-text')) {
                updatePreview(rowsContainer, previewContainer);
            }
        });

        // ===============================
        // 🔹 SortableJS for rows
        // ===============================
        if (rowsContainer) {
            new Sortable(rowsContainer, {
                animation: 150,
                handle: '.drag',
                onEnd: () => {
                    updateNumbers(rowsContainer);
                    updatePreview(rowsContainer, previewContainer);
                }
            });
        }

        // ===============================
        // 🔹 Before Submit: add order inputs
        // ===============================
        if (manualForm) {
            manualForm.addEventListener('submit', () => {
                rowsContainer?.querySelectorAll('.row').forEach((row, i) => {
                    let orderInput = row.querySelector('.order-input');
                    if (!orderInput) {
                        orderInput = document.createElement('input');
                        orderInput.type = 'hidden';
                        orderInput.name = 'orders[]';
                        orderInput.classList.add('order-input');
                        row.appendChild(orderInput);
                    }
                    orderInput.value = i + 1;
                });
            });
        }

        // ===============================
        // 🔹 Child Answer Logic (NEW)
        // -------------------------------
        // Menampilkan textarea jika select 'has_child' = 1
        // ===============================
        rowsContainer?.addEventListener('change', (evt) => {
            if (evt.target.matches('select[name="has_child[]"]')) {
                const childDiv = evt.target.closest('.row').querySelector('.child-input');
                if (childDiv) {
                    if (evt.target.value === "1") {
                        childDiv.classList.remove('hidden');
                    } else {
                        childDiv.classList.add('hidden');
                    }
                }
            }
        });

    });

    // ===============================
    // 🔹 Close Modal Handler
    // ===============================
    modal.querySelectorAll('[data-close]').forEach(btn => {
        btn.addEventListener('click', () => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            content.innerHTML = '';
        });
    });

});
