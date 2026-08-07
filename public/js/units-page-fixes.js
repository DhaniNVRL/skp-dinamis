(() => {
    const initialize = () => {
        const page = document.getElementById('unitMainPage');
        if (!page) return;

        const editForm = document.getElementById('editUnitForm');
        const editId = document.getElementById('edit_unit_id');
        const editName = document.getElementById('edit_unit_name');
        const selectAll = document.getElementById('selectAllUnit');
        const checkboxes = [...document.querySelectorAll('.unit-checkbox')];
        const bulkAction = document.getElementById('unitBulkAction');
        const selectedCount = document.getElementById('selectedUnitCount');

        document.addEventListener('click', (event) => {
            const button = event.target.closest('[data-modal-open="editUnitModal"]');
            if (!button || !editForm || !editName) return;

            editForm.action = button.dataset.action ?? '';
            editName.value = button.dataset.name ?? '';
            if (editId) editId.value = button.dataset.id ?? '';
        });

        const refreshSelection = () => {
            const count = checkboxes.filter((checkbox) => checkbox.checked).length;
            bulkAction?.classList.toggle('hidden', count === 0);
            bulkAction?.classList.toggle('flex', count > 0);
            if (selectedCount) selectedCount.textContent = String(count);

            if (selectAll) {
                selectAll.checked = checkboxes.length > 0 && count === checkboxes.length;
                selectAll.indeterminate = count > 0 && count < checkboxes.length;
            }
        };

        selectAll?.addEventListener('change', () => {
            const shouldSelectAll = selectAll.checked;
            checkboxes.forEach((checkbox) => {
                checkbox.checked = shouldSelectAll;
            });
            refreshSelection();
        });
        checkboxes.forEach((checkbox) => checkbox.addEventListener('change', refreshSelection));

        refreshSelection();
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initialize, { once: true });
    } else {
        initialize();
    }
})();
