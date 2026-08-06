const FormPage = {

    init() {
        this.bindEvents();
        this.bindModalEvents();
    },


    /**
     * ================================
     * CREATE FORM EVENT
     * ================================
     */
    bindEvents() {

        /**
         * ADD ROW
         */
        document.addEventListener("click", (e) => {

            const addButton = e.target.closest(
                '[data-add-row="formCreateBody"]'
            );

            if (!addButton) {
                return;
            }


            DynamicTable.addRow("formCreateTable");

            this.syncCreateRowNames();

        });


        /**
         * REMOVE ROW
         *
         * DynamicTable yang menghapus row.
         * FormPage hanya merapikan kembali
         * index input setelah row dihapus.
         */
        document.addEventListener("click", (e) => {

            const removeButton = e.target.closest(
                "#formCreateTable [data-remove-row]"
            );

            if (!removeButton) {
                return;
            }


            setTimeout(() => {

                this.syncCreateRowNames();

            }, 0);

        });

    },


    /**
     * ================================
     * MODAL EVENT
     * ================================
     */
    bindModalEvents() {

        document.addEventListener("modal:opened", (e) => {

            if (e.detail.id !== "createFormModal") {
                return;
            }


            this.initCreateTable();

        });

    },


    /**
     * ================================
     * INIT CREATE TABLE
     * ================================
     */
    initCreateTable() {

        const table =
            document.getElementById("formCreateTable");

        if (!table) {
            return;
        }


        const rows =
            table.querySelectorAll("tbody tr");


        /**
         * Tambahkan row pertama
         * apabila tabel masih kosong.
         */
        if (rows.length === 0) {

            DynamicTable.addRow("formCreateTable");

        }


        this.syncCreateRowNames();

    },


    /**
     * ================================
     * SYNC CREATE INPUT NAME
     * ================================
     */
    syncCreateRowNames() {

        const table =
            document.getElementById("formCreateTable");

        if (!table) {
            return;
        }


        const rows =
            table.querySelectorAll("tbody tr");


        rows.forEach((row, index) => {

            /**
             * NO URUT
             */
            const noUrut = row.querySelector(
                '[data-form-field="no_urut"]'
            );

            if (noUrut) {

                noUrut.name =
                    `forms[${index}][no_urut]`;

            }


            /**
             * NAMA FORM
             */
            const name = row.querySelector(
                '[data-form-field="name"]'
            );

            if (name) {

                name.name =
                    `forms[${index}][name]`;

            }


            /**
             * FORM TYPE
             */
            const formType = row.querySelector(
                '[data-form-field="formtype_id"]'
            );

            if (formType) {

                formType.name =
                    `forms[${index}][formtype_id]`;

            }

        });

    }

};


/**
 * =========================================
 * INIT FORM PAGE
 * =========================================
 */
document.addEventListener("DOMContentLoaded", () => {

    FormPage.init();

});


/**
 * =========================================
 * EDIT FORM
 * =========================================
 */
let editFormTrigger = null;


/*
|--------------------------------------------------------------------------
| Simpan tombol Edit Form
|--------------------------------------------------------------------------
*/

document.addEventListener(
    "click",
    function (event) {

        const button = event.target.closest(
            '[data-modal-open="editFormModal"]'
        );

        if (!button) {
            return;
        }

        editFormTrigger = button;

    },
    true
);


/*
|--------------------------------------------------------------------------
| Isi Modal Edit Form
|--------------------------------------------------------------------------
*/

document.addEventListener(
    "modal:opened",
    function (event) {

        if (event.detail.id !== "editFormModal") {
            return;
        }

        if (!editFormTrigger) {
            console.error(
                "Tombol pembuka Edit Form tidak ditemukan."
            );

            return;
        }

        const modal = event.detail.modal;

        const form = modal.querySelector(
            "#editFormForm"
        );

        const idInput = modal.querySelector(
            "#edit_form_id"
        );

        const groupInput = modal.querySelector(
            "#edit_form_group_id"
        );

        const noUrutInput = modal.querySelector(
            "#edit_form_no_urut"
        );

        const nameInput = modal.querySelector(
            "#edit_form_name"
        );

        const typeInput = modal.querySelector(
            "#edit_form_type"
        );

        const warning = modal.querySelector(
            "#edit_form_type_warning"
        );

        if (form) {
            form.action =
                editFormTrigger.dataset.action || "";
        }

        if (idInput) {
            idInput.value =
                editFormTrigger.dataset.id || "";
        }

        if (groupInput) {
            groupInput.value =
                editFormTrigger.dataset.groupId || "";
        }

        if (noUrutInput) {
            noUrutInput.value =
                editFormTrigger.dataset.noUrut || "";
        }

        if (nameInput) {
            nameInput.value =
                editFormTrigger.dataset.name || "";
        }

        if (typeInput) {
            const formTypeId =
                editFormTrigger.dataset.formtypeId || "";

            typeInput.value = formTypeId;
            typeInput.dataset.originalValue = formTypeId;
        }

        if (warning) {
            warning.classList.add("hidden");
        }

    }
);


/*
|--------------------------------------------------------------------------
| Tampilkan peringatan saat tipe form berubah
|--------------------------------------------------------------------------
*/

document.addEventListener(
    "change",
    function (event) {

        if (event.target.id !== "edit_form_type") {
            return;
        }

        const select = event.target;

        const modal = select.closest(
            "#editFormModal"
        );

        if (!modal) {
            return;
        }

        const warning = modal.querySelector(
            "#edit_form_type_warning"
        );

        if (!warning) {
            return;
        }

        const originalValue =
            select.dataset.originalValue || "";

        const hasChanged =
            select.value !== originalValue;

        warning.classList.toggle(
            "hidden",
            !hasChanged
        );

    }
);


let deleteFormTrigger = null;


/*
|--------------------------------------------------------------------------
| Simpan tombol Hapus Form
|--------------------------------------------------------------------------
*/

document.addEventListener(
    "click",
    function (event) {

        const button = event.target.closest(
            '[data-modal-open="deleteFormModal"]'
        );

        if (!button) {
            return;
        }

        deleteFormTrigger = button;

    },
    true
);


/*
|--------------------------------------------------------------------------
| Isi Modal Hapus Form
|--------------------------------------------------------------------------
*/

document.addEventListener("click", function (event) {
    const button = event.target.closest(
        '[data-modal-open="deleteFormModal"]'
    );

    if (!button) {
        return;
    }

    const modal = document.getElementById(
        "deleteFormModal"
    );

    const deleteForm = document.getElementById(
        "deleteFormForm"
    );

    const nameElement = document.getElementById(
        "delete_form_name"
    );

    const questionCountElement = document.getElementById(
        "delete_form_question_count"
    );

    const optionCountElement = document.getElementById(
        "delete_form_option_count"
    );

    if (!modal || !deleteForm) {
        console.error(
            "Modal atau form delete tidak ditemukan."
        );

        return;
    }

    deleteForm.action =
        button.dataset.action || "";

    if (nameElement) {
        nameElement.textContent =
            button.dataset.name || "-";
    }

    if (questionCountElement) {
        questionCountElement.textContent =
            button.dataset.questionCount || "0";
    }

    if (optionCountElement) {
        optionCountElement.textContent =
            button.dataset.optionCount || "0";
    }

    console.log(
        "DELETE FORM ACTION:",
        deleteForm.action
    );
});

document
    .getElementById("deleteFormForm")
    ?.addEventListener("submit", function (event) {

        console.log(
            "SUBMIT DELETE FORM:",
            this.action
        );

        const methodInput = this.querySelector(
            'input[name="_method"]'
        );

        console.log(
            "METHOD OVERRIDE:",
            methodInput?.value
        );

        if (!this.action) {
            event.preventDefault();

            console.error(
                "Action delete form masih kosong."
            );
        }

    });