window.BulkSelect = {

    selectAlls: [],
    checkboxes: [],

    init({
        selectAll = '.bulk-select-all',
        checkbox = '.bulk-checkbox'
    } = {}) {

        this.selectAlls = document.querySelectorAll(selectAll);
        this.checkboxes = document.querySelectorAll(checkbox);

        if (!this.selectAlls.length || !this.checkboxes.length) {
            return;
        }

        this.selectAlls.forEach(selectAll => {

            selectAll.addEventListener('change', (e) => {

                this.checkboxes.forEach(cb => {
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

        const checked = [...this.checkboxes].filter(cb => cb.checked);

        const allChecked = checked.length === this.checkboxes.length;
        const indeterminate =
            checked.length > 0 &&
            checked.length < this.checkboxes.length;

        this.selectAlls.forEach(selectAll => {
            selectAll.checked = allChecked;
            selectAll.indeterminate = indeterminate;
        });

    },

    getSelectedIds() {

        return [...this.checkboxes]
            .filter(cb => cb.checked)
            .map(cb => cb.value);

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