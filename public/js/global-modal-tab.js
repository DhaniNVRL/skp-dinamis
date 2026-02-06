// 🔥 FILE FINAL: global-modal-tab.js (SUDAH DIPERBAIKI)
document.addEventListener('alpine:init', () => {

    const modal = document.getElementById('globalModal');
    if (!modal) {
        console.error('globalModal not found');
        return;
    }

    const title = document.getElementById('modalTitle');
    const content = document.getElementById('modalContent');

    // ===============================
    // OPEN MODAL
    // ===============================
    window.addEventListener('open-modal-tab', (e) => {

        // 1️⃣ Set title
        title.innerText = e.detail.title ?? '';

        // 2️⃣ Inject HTML dari template
        content.innerHTML = e.detail.content ?? '';

        // 3️⃣ Ambil form dan input group
        const manualForm = content.querySelector('form:not(#excelForm)');
        const excelForm  = content.querySelector('#excelForm');
        const groupInput = content.querySelector('[name="id_groups"]');

        // ===============================
        // 🔧 FIX 1: MANUAL FORM ACTION
        // ===============================
        if (manualForm && e.detail.manual) {
            manualForm.action = e.detail.manual;
        }

        // ===============================
        // 🔧 FIX 2: EXCEL FORM ACTION (INI BUG UTAMA)
        // ===============================
        if (excelForm && e.detail.excel) {
            excelForm.action = e.detail.excel; // ✅ SEBELUMNYA SALAH
        }

        // ===============================
        // SET GROUP ID
        // ===============================
        if (groupInput && e.detail.group) {
            groupInput.value = e.detail.group;
        }

        // ===============================
        // SHOW MODAL
        // ===============================
        modal.classList.remove('hidden');
        modal.classList.add('flex');

        // ===============================
        // TAB SWITCHING
        // ===============================
        content.querySelectorAll('.tab-btn').forEach(tab => {
            tab.addEventListener('click', () => {

                content.querySelectorAll('.tab-btn')
                    .forEach(t => t.classList.remove('border-blue-600', 'text-blue-600'));

                content.querySelectorAll('[data-content]')
                    .forEach(c => c.classList.add('hidden'));

                tab.classList.add('border-blue-600', 'text-blue-600');

                const target = content.querySelector(
                    `[data-content="${tab.dataset.tab}"]`
                );
                if (target) target.classList.remove('hidden');
            });
        });

        // ===============================
        // ADD ROW
        // ===============================
        const addRowBtn = content.querySelector('#addRow');
        const rowsContainer = content.querySelector('#rows');

        if (addRowBtn && rowsContainer) {
            addRowBtn.addEventListener('click', () => {
                const row = rowsContainer.querySelector('.row').cloneNode(true);
                row.querySelector('input').value = '';

                const removeBtn = row.querySelector('.remove');
                if (removeBtn) {
                    removeBtn.onclick = () => row.remove();
                }

                rowsContainer.appendChild(row);
            });
        }

        // ===============================
        // REMOVE ROW
        // ===============================
        content.querySelectorAll('.remove').forEach(btn => {
            btn.onclick = () => btn.closest('.row').remove();
        });

    });

    // ===============================
    // CLOSE MODAL
    // ===============================
    modal.querySelectorAll('[data-close]').forEach(btn => {
        btn.addEventListener('click', () => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            content.innerHTML = ''; // reset modal
        });
    });

});
