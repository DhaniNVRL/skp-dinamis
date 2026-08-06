document.addEventListener("DOMContentLoaded", function () {

    const createBody = document.getElementById(
        "createOptionBody"
    );

    const rowTemplate = document.getElementById(
        "createOptionRowTemplate"
    );

    let createOptionTrigger = null;


    function syncOptionRowNames() {

        if (!createBody) return;

        const rows = createBody.querySelectorAll(
            ".option-create-row"
        );

        rows.forEach(function (row, index) {

            const numberInput = row.querySelector(
                '[data-option-field="no"]'
            );

            const answerInput = row.querySelector(
                '[data-option-field="answer_text"]'
            );

            const childToggle = row.querySelector(
                "[data-option-child-toggle]"
            );

            const childHidden = row.querySelector(
                "[data-option-child-hidden]"
            );

            const childLabel = row.querySelector(
                '[data-option-field="answer_text2"]'
            );


            if (numberInput) {
                numberInput.name = `no[${index}]`;
            }

            if (answerInput) {
                answerInput.name =
                    `answer_text[${index}]`;
            }

            if (childHidden) {
                childHidden.name =
                    `has_child[${index}]`;
            }

            if (childToggle) {
                childToggle.name =
                    `has_child_checkbox[${index}]`;
            }

            if (childLabel) {
                childLabel.name =
                    `answer_text2[${index}]`;
            }

        });

    }


    function updateOptionRowNumbers() {

        if (!createBody) return;

        const rows = createBody.querySelectorAll(
            ".option-create-row"
        );

        rows.forEach(function (row, index) {

            const numberInput = row.querySelector(
                '[data-option-field="no"]'
            );

            if (
                numberInput &&
                numberInput.value === ""
            ) {
                numberInput.value = index + 1;
            }

        });

    }


    function addOptionRow() {

        if (!createBody || !rowTemplate) {
            console.error(
                "Create Option body atau template tidak ditemukan."
            );

            return;
        }

        const clone =
            rowTemplate.content.cloneNode(true);

        createBody.appendChild(clone);

        syncOptionRowNames();
        updateOptionRowNumbers();

    }


    /*
    |--------------------------------------------------------------------------
    | Simpan tombol pembuka modal
    |--------------------------------------------------------------------------
    */

    document.addEventListener(
        "click",
        function (event) {

            const button = event.target.closest(
                '[data-modal-open="createOptionModal"]'
            );

            if (!button) return;

            createOptionTrigger = button;

        },
        true
    );


    /*
    |--------------------------------------------------------------------------
    | Persiapkan modal setelah terbuka
    |--------------------------------------------------------------------------
    */

    document.addEventListener(
        "modal:opened",
        function (event) {

            if (
                event.detail.id !==
                "createOptionModal"
            ) {
                return;
            }

            if (!createOptionTrigger) return;

            const modal = event.detail.modal;

            const form = modal.querySelector(
                "#createOptionForm"
            );

            const questionInput = modal.querySelector(
                "#create_option_question_id"
            );

            const questionName = modal.querySelector(
                "#create_option_question_name"
            );


            if (form) {
                form.action =
                    createOptionTrigger.dataset.action || "";
            }

            if (questionInput) {
                questionInput.value =
                    createOptionTrigger.dataset.questionId || "";
            }

            if (questionName) {
                questionName.textContent =
                    createOptionTrigger.dataset.questionName || "-";
            }

            if (createBody) {
                createBody.innerHTML = "";
                addOptionRow();
            }

        }
    );


    /*
    |--------------------------------------------------------------------------
    | Tambah baris
    |--------------------------------------------------------------------------
    */

    document.addEventListener(
        "click",
        function (event) {

            const button = event.target.closest(
                "#addOptionRow"
            );

            if (!button) return;

            addOptionRow();

        }
    );


    /*
    |--------------------------------------------------------------------------
    | Hapus baris
    |--------------------------------------------------------------------------
    */

    document.addEventListener(
        "click",
        function (event) {

            const button = event.target.closest(
                "[data-remove-option-row]"
            );

            if (!button || !createBody) return;

            const rows = createBody.querySelectorAll(
                ".option-create-row"
            );

            if (rows.length <= 1) {
                alert(
                    "Minimal harus ada satu option."
                );

                return;
            }

            const row = button.closest(
                ".option-create-row"
            );

            if (row) {
                row.remove();

                syncOptionRowNames();
                updateOptionRowNumbers();
            }

        }
    );


    /*
    |--------------------------------------------------------------------------
    | Aktif/nonaktif child answer
    |--------------------------------------------------------------------------
    */

    document.addEventListener(
        "change",
        function (event) {

            const checkbox = event.target.closest(
                "[data-option-child-toggle]"
            );

            if (!checkbox) return;

            const row = checkbox.closest(
                ".option-create-row"
            );

            if (!row) return;

            const hiddenInput = row.querySelector(
                "[data-option-child-hidden]"
            );

            const childLabel = row.querySelector(
                ".option-child-label"
            );


            if (checkbox.checked) {

                if (hiddenInput) {
                    hiddenInput.value = "1";
                }

                if (childLabel) {
                    childLabel.disabled = false;
                    childLabel.required = true;
                    childLabel.classList.remove(
                        "bg-gray-100"
                    );
                    childLabel.classList.add(
                        "bg-white"
                    );
                }

            } else {

                if (hiddenInput) {
                    hiddenInput.value = "0";
                }

                if (childLabel) {
                    childLabel.value = "";
                    childLabel.disabled = true;
                    childLabel.required = false;
                    childLabel.classList.add(
                        "bg-gray-100"
                    );
                    childLabel.classList.remove(
                        "bg-white"
                    );
                }

            }

        }
    );

});

let editOptionTrigger = null;

/*
|--------------------------------------------------------------------------
| Simpan tombol edit yang diklik
|--------------------------------------------------------------------------
*/

document.addEventListener(
    "click",
    function (event) {

        const button = event.target.closest(
            '[data-modal-open="editOptionModal"]'
        );

        if (!button) return;

        editOptionTrigger = button;

    },
    true
);


/*
|--------------------------------------------------------------------------
| Isi modal edit
|--------------------------------------------------------------------------
*/

document.addEventListener(
    "modal:opened",
    function (event) {

        if (event.detail.id !== "editOptionModal") {
            return;
        }

        if (!editOptionTrigger) {
            console.error(
                "Tombol pembuka Edit Option tidak ditemukan."
            );

            return;
        }

        const modal = event.detail.modal;

        const form = modal.querySelector(
            "#editOptionForm"
        );

        const idInput = modal.querySelector(
            "#edit_option_id"
        );

        const questionInput = modal.querySelector(
            "#edit_option_question_id"
        );

        const noInput = modal.querySelector(
            "#edit_option_no"
        );

        const answerInput = modal.querySelector(
            "#edit_option_answer_text"
        );

        const childCheckbox = modal.querySelector(
            "#edit_option_has_child_checkbox"
        );

        const childHidden = modal.querySelector(
            "#edit_option_has_child"
        );

        const childLabel = modal.querySelector(
            "#edit_option_answer_text2"
        );

        const hasChild =
            editOptionTrigger.dataset.hasChild === "1";

        if (form) {
            form.action =
                editOptionTrigger.dataset.action || "";
        }

        if (idInput) {
            idInput.value =
                editOptionTrigger.dataset.id || "";
        }

        if (questionInput) {
            questionInput.value =
                editOptionTrigger.dataset.questionId || "";
        }

        if (noInput) {
            noInput.value =
                editOptionTrigger.dataset.no || "";
        }

        if (answerInput) {
            answerInput.value =
                editOptionTrigger.dataset.answerText || "";
        }

        if (childCheckbox) {
            childCheckbox.checked = hasChild;
        }

        if (childHidden) {
            childHidden.value =
                hasChild ? "1" : "0";
        }

        if (childLabel) {
            childLabel.value =
                editOptionTrigger.dataset.answerText2 || "";

            childLabel.disabled = !hasChild;
            childLabel.required = hasChild;

            childLabel.classList.toggle(
                "bg-gray-100",
                !hasChild
            );

            childLabel.classList.toggle(
                "bg-white",
                hasChild
            );
        }

    }
);


/*
|--------------------------------------------------------------------------
| Aktif/nonaktif child pada modal edit
|--------------------------------------------------------------------------
*/

document.addEventListener(
    "change",
    function (event) {

        if (
            event.target.id !==
            "edit_option_has_child_checkbox"
        ) {
            return;
        }

        const checkbox = event.target;

        const modal = checkbox.closest(
            "#editOptionModal"
        );

        if (!modal) return;

        const hiddenInput = modal.querySelector(
            "#edit_option_has_child"
        );

        const childLabel = modal.querySelector(
            "#edit_option_answer_text2"
        );

        const isChecked = checkbox.checked;

        if (hiddenInput) {
            hiddenInput.value =
                isChecked ? "1" : "0";
        }

        if (childLabel) {
            childLabel.disabled = !isChecked;
            childLabel.required = isChecked;

            childLabel.classList.toggle(
                "bg-gray-100",
                !isChecked
            );

            childLabel.classList.toggle(
                "bg-white",
                isChecked
            );

            if (!isChecked) {
                childLabel.value = "";
            }
        }

    }
);

let deleteOptionTrigger = null;


/*
|--------------------------------------------------------------------------
| Simpan tombol delete yang diklik
|--------------------------------------------------------------------------
*/

document.addEventListener(
    "click",
    function (event) {

        const button = event.target.closest(
            '[data-modal-open="deleteOptionModal"]'
        );

        if (!button) return;

        deleteOptionTrigger = button;

    },
    true
);


/*
|--------------------------------------------------------------------------
| Isi modal delete
|--------------------------------------------------------------------------
*/

document.addEventListener(
    "modal:opened",
    function (event) {

        if (
            event.detail.id !==
            "deleteOptionModal"
        ) {
            return;
        }

        if (!deleteOptionTrigger) {
            console.error(
                "Tombol pembuka Delete Option tidak ditemukan."
            );

            return;
        }

        const modal = event.detail.modal;

        const form = modal.querySelector(
            "#deleteOptionForm"
        );

        const idInput = modal.querySelector(
            "#delete_option_id"
        );

        const nameElement = modal.querySelector(
            "#delete_option_name"
        );

        if (form) {
            form.action =
                deleteOptionTrigger.dataset.action || "";
        }

        if (idInput) {
            idInput.value =
                deleteOptionTrigger.dataset.id || "";
        }

        if (nameElement) {
            nameElement.textContent =
                deleteOptionTrigger.dataset.name || "-";
        }

    }
);