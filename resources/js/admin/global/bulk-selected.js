window.BulkSelect = {

    selectAlls: [],
    checkboxes: [],
    onlyVisible: false,

    init({
        selectAll = '.bulk-select-all',
        checkbox = '.bulk-checkbox',
        onlyVisible = false
    } = {}) {

        this.selectAlls = document.querySelectorAll(selectAll);
        this.checkboxes = document.querySelectorAll(checkbox);
        this.onlyVisible = onlyVisible;

        if (!this.selectAlls.length || !this.checkboxes.length) {
            return;
        }

        this.selectAlls.forEach(selectAll => {

            selectAll.addEventListener('change', (e) => {

                this.getAvailableCheckboxes().forEach(cb => {
                    cb.checked = e.target.checked;
                });

                this.updateState();

            });

        });

        this.checkboxes.forEach(cb => {

            cb.addEventListener('change', () => {
                this.updateState();
            });

        });

        this.updateState();

    },

    updateState() {

        const availableCheckboxes = this.getAvailableCheckboxes();
        const checked = availableCheckboxes.filter(cb => cb.checked);

        const allChecked = availableCheckboxes.length > 0
            && checked.length === availableCheckboxes.length;
        const indeterminate =
            checked.length > 0 &&
            checked.length < this.checkboxes.length;

        this.selectAlls.forEach(selectAll => {
            selectAll.checked = allChecked;
            selectAll.indeterminate = indeterminate;
        });

        document.querySelectorAll('#btnDeleteSelected').forEach(button => {
            button.disabled = checked.length === 0;
        });

    },

    getSelectedIds() {

        return this.getAvailableCheckboxes()
            .filter(cb => cb.checked)
            .map(cb => cb.value);

    },

    getSelectedCheckboxes() {

        return this.getAvailableCheckboxes()
            .filter(cb => cb.checked);

    },

    getAvailableCheckboxes() {

        if (!this.onlyVisible) {
            return [...this.checkboxes];
        }

        return [...this.checkboxes].filter((checkbox) => {
            const row = checkbox.closest('tr');

            return !row || row.style.display !== 'none';
        });

    },

    clear() {

        this.checkboxes.forEach(cb => cb.checked = false);
        this.updateState();

    },

    selectAll() {

        this.checkboxes.forEach(cb => cb.checked = true);
        this.updateState();

    },

    count() {

        return this.getSelectedIds().length;

    }

};
