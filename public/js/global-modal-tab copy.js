document.addEventListener('alpine:init', () => {

    const modal = document.getElementById('globalModal');
    if (!modal) return;

    const title = document.getElementById('modalTitle');
    const manualForm = document.getElementById('manualForm');
    const excelForm = document.getElementById('excelForm');
    const groupInput = document.getElementById('groupId');

    // ===============================
    // OPEN MODAL (FROM ALPINE EVENT)
    // ===============================
    window.addEventListener('open-modal-tab', (e) => {

        title.innerText = e.detail.title ?? '';
        manualForm.action = e.detail.manual ?? '';
        excelForm.action = e.detail.excel ?? '';
        groupInput.value = e.detail.group ?? '';

        // reset tab modal
        document.querySelectorAll('[data-content]')
            .forEach(c => c.classList.add('hidden'));

        document.querySelector('[data-content="manual"]')
            .classList.remove('hidden');

        document.querySelectorAll('.tab-btn')
            .forEach(t => t.classList.remove('border-blue-600','text-blue-600'));

        document.querySelector('.tab-btn[data-tab="manual"]')
            .classList.add('border-blue-600','text-blue-600');

        // 🔥 PENTING: pastikan flex aktif
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    });

    // ===============================
    // CLOSE MODAL
    // ===============================
    modal.querySelectorAll('[data-close]').forEach(btn => {
        btn.addEventListener('click', () => {
            modal.classList.add('hidden');
            modal.classList.remove('flex'); // optional (boleh ada / tidak)
        });
    });

});
