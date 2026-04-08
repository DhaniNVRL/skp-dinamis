document.addEventListener('alpine:init', () => {

    const modal = document.getElementById('globalModal');
    if (!modal) return console.error('globalModal not found');

    const title = document.getElementById('modalTitle');
    const content = document.getElementById('modalContent');

    function updateNumbers(rowsContainer) {
        rowsContainer.querySelectorAll('.row').forEach((row, i) => {
            const dragHandle = row.querySelector('.drag');
            if (dragHandle) dragHandle.innerText = i + 1;
        });
    }

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

    window.addEventListener('open-modal-tab', (e) => {

        // Set title
        title.innerText = e.detail.title ?? '';
        // Inject template
        content.innerHTML = e.detail.content ?? '';

        const manualForm = content.querySelector('form:not(#excelForm)');
        const groupInput = content.querySelector('[name="id_groups"]');
        const formIdInput = content.querySelector('[name="form_id"]');
        const questionInput = content.querySelector('[name="question_id"]');

        const rowsContainer = content.querySelector('#answerRows');
        const previewContainer = content.querySelector('#answerPreview');
        const addRowBtn = content.querySelector('#addAnswerRow');

        // Set hidden inputs
        if (groupInput && e.detail.group) groupInput.value = e.detail.group;
        if (formIdInput && e.detail.form) formIdInput.value = e.detail.form;
        if (questionInput && e.detail.question) questionInput.value = e.detail.question;

        // Show modal
        modal.classList.remove('hidden');
        modal.classList.add('flex');

        // ===============================
        // 🔹 Add row
        // ===============================
        addRowBtn?.addEventListener('click', () => {
            const row = rowsContainer.querySelector('.row').cloneNode(true);
            row.querySelectorAll('input').forEach(input => input.value = '');
            rowsContainer.appendChild(row);
            updateNumbers(rowsContainer);
            updatePreview(rowsContainer, previewContainer);
        });

        // ===============================
        // 🔹 Remove row (delegation)
        // ===============================
        rowsContainer?.addEventListener('click', (evt) => {
            if (evt.target.classList.contains('remove')) {
                evt.target.closest('.row').remove();
                updateNumbers(rowsContainer);
                updatePreview(rowsContainer, previewContainer);
            }
        });

        // ===============================
        // 🔹 Update preview on input change
        // ===============================
        rowsContainer.querySelectorAll('.answer-text').forEach(input => {
            input.addEventListener('input', () => updatePreview(rowsContainer, previewContainer));
        });

        // Event delegation for dynamically added rows
        rowsContainer.addEventListener('input', (evt) => {
            if (evt.target.classList.contains('answer-text')) {
                updatePreview(rowsContainer, previewContainer);
            }
        });

        // ===============================
        // 🔹 SortableJS
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
        // 🔹 Update order before submit
        // ===============================
        if (manualForm) {
            manualForm.addEventListener('submit', () => {
                rowsContainer.querySelectorAll('.row').forEach((row, i) => {
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

    });

    // ===============================
    // CLOSE MODAL
    // ===============================
    modal.querySelectorAll('[data-close]').forEach(btn => {
        btn.addEventListener('click', () => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            content.innerHTML = '';
        });
    });

});