(() => {
    const initializeGroupBulkSelection = () => {
        const table = document.getElementById('GroupsTable');
        const selectAll = table?.querySelector('.bulk-select-all');
        const checkboxes = [...(table?.querySelectorAll('.bulk-checkbox') ?? [])];
        const deleteButton = document.getElementById('btnDeleteSelected');
        const bulkAction = document.getElementById('groupBulkAction');
        const selectedCount = document.getElementById('selectedGroupCount');
        const bulkDeleteForm = document.getElementById('bulkDeleteForm');

        if (!table || !selectAll || !deleteButton) return;

        const updateState = () => {
            const checkedCount = checkboxes.filter((checkbox) => checkbox.checked).length;
            const hasSelection = checkedCount > 0;

            selectAll.checked = checkboxes.length > 0 && checkedCount === checkboxes.length;
            selectAll.indeterminate = checkedCount > 0 && checkedCount < checkboxes.length;
            deleteButton.disabled = !hasSelection;
            bulkAction?.classList.toggle('hidden', !hasSelection);

            if (selectedCount) selectedCount.textContent = String(checkedCount);
        };

        selectAll.addEventListener('change', () => {
            checkboxes.forEach((checkbox) => {
                checkbox.checked = selectAll.checked;
            });
            updateState();
        });

        checkboxes.forEach((checkbox) => {
            checkbox.addEventListener('change', updateState);
        });

        document.addEventListener('modal:closed', (event) => {
            if (event.detail?.id === 'bulkDeleteModal') updateState();
        });

        bulkDeleteForm?.addEventListener('submit', () => {
            const selectedIds = [...new Set(
                checkboxes.filter((checkbox) => checkbox.checked).map((checkbox) => checkbox.value)
            )];

            checkboxes.forEach((checkbox) => {
                checkbox.removeAttribute('name');
                checkbox.removeAttribute('form');
            });
            bulkDeleteForm.querySelectorAll('input[name="ids[]"]').forEach((input) => input.remove());
            selectedIds.forEach((id) => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'ids[]';
                input.value = id;
                bulkDeleteForm.appendChild(input);
            });
        });

        updateState();
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initializeGroupBulkSelection, { once: true });
    } else {
        initializeGroupBulkSelection();
    }
})();
