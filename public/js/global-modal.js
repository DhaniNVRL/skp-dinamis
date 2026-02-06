const modal = document.getElementById('globalModal');
if (!modal) {
    console.error('globalModal not found');
} else {

    const title = document.getElementById('modalTitle');
    const manualForm = document.getElementById('manualForm');
    const excelForm = document.getElementById('excelForm');
    const groupInput = document.getElementById('groupId');

    // OPEN MODAL
    document.querySelectorAll('.openModal').forEach(btn => {
        btn.addEventListener('click', () => {
            title.innerText = btn.dataset.title ?? '';
            manualForm.action = btn.dataset.manual ?? '';
            excelForm.action = btn.dataset.excel ?? '';
            groupInput.value = btn.dataset.group ?? '';

            modal.classList.remove('hidden');
            modal.classList.add('flex');
        });
    });

    // CLOSE MODAL
    modal.querySelectorAll('[data-close]').forEach(btn => {
        btn.addEventListener('click', () => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        });
    });

    // TABS
    document.querySelectorAll('.tab-btn').forEach(tab => {
        tab.addEventListener('click', () => {
            document.querySelectorAll('.tab-btn')
                .forEach(t => t.classList.remove('border-blue-600','text-blue-600'));

            document.querySelectorAll('[data-content]')
                .forEach(c => c.classList.add('hidden'));

            tab.classList.add('border-blue-600','text-blue-600');
            document
                .querySelector(`[data-content="${tab.dataset.tab}"]`)
                .classList.remove('hidden');
        });
    });

    // ADD ROW
    document.getElementById('addRow').addEventListener('click', () => {
        const row = document.querySelector('.row').cloneNode(true);
        row.querySelector('input').value = '';
        row.querySelector('.remove').onclick = () => row.remove();
        document.getElementById('rows').appendChild(row);
    });
x
    document.querySelectorAll('.remove').forEach(btn => {
        btn.onclick = () => btn.closest('.row').remove();
    });
}
