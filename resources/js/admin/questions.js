document.addEventListener("DOMContentLoaded", function () {
    /*
    |--------------------------------------------------------------------------
    | Guard
    |--------------------------------------------------------------------------
    | Mencegah question.js dijalankan dua kali.
    */
    if (window.__questionJsInitialized) {
        return;
    }

    window.__questionJsInitialized = true;

    /*
    |--------------------------------------------------------------------------
    | Elemen Create Question
    |--------------------------------------------------------------------------
    */
    const createModal = document.getElementById(
        "createQuestionModal"
    );

    const createForm = document.getElementById(
        "createQuestionForm"
    );

    const createBody = document.getElementById(
        "createQuestionBody"
    );

    const rowTemplate = document.getElementById(
        "createQuestionRowTemplate"
    );

    const groupInput = document.getElementById(
        "create_question_group_id"
    );

    const formInput = document.getElementById(
        "create_question_form_id"
    );

    const formTypeInput = document.getElementById(
        "create_question_formtype_id"
    );

    const formNameElement = document.getElementById(
        "create_question_form_name"
    );

    let createQuestionTrigger = null;

    /*
    |--------------------------------------------------------------------------
    | Pilih template option berdasarkan formtype_id
    |--------------------------------------------------------------------------
    */
    function getQuestionTypeOptionTemplate(formTypeId) {
        switch (Number(formTypeId)) {
            case 1:
                return document.getElementById(
                    "generalQuestionnaireTypeOptions"
                );

            case 2:
                return document.getElementById(
                    "customerAssessment15TypeOptions"
                );

            case 3:
                return document.getElementById(
                    "customerAssessment17TypeOptions"
                );

            case 4:
                return document.getElementById(
                    "engagementAssessment15TypeOptions"
                );

            case 5:
                return document.getElementById(
                    "engagementAssessment17TypeOptions"
                );

            case 6:
                return document.getElementById(
                    "ranking13TypeOptions"
                );

            case 7:
                return document.getElementById(
                    "ranking15TypeOptions"
                );

            case 8:
                return document.getElementById(
                    "strengthComplaintSuggestionTypeOptions"
                );

            case 9:
                return document.getElementById(
                    "complaintSuggestionTypeOptions"
                )

            case 10:
                return document.getElementById(
                    "suggestionTypeOptions"
                );   
            case 11:
                return document.getElementById(
                    "competitorTypeOptions"
                );
            case 13:
                return document.getElementById(
                    "competitor17TypeOptions"
                );
            case 14:
                return document.getElementById(
                    "respondentCompetitorTypeOptions"
                );         

            default:
                return null;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Isi select jenis pertanyaan
    |--------------------------------------------------------------------------
    */
    function fillQuestionTypeSelect(select, formTypeId) {
        if (!select) {
            return;
        }

        select.innerHTML = "";

        const placeholder = document.createElement(
            "option"
        );

        placeholder.value = "";
        placeholder.textContent = "Pilih tipe";

        select.appendChild(placeholder);

        const optionTemplate =
            getQuestionTypeOptionTemplate(formTypeId);

        if (!optionTemplate) {
            console.error(
                "Template tipe pertanyaan tidak ditemukan.",
                {
                    formTypeId: formTypeId
                }
            );

            const unavailableOption =
                document.createElement("option");

            unavailableOption.value = "";
            unavailableOption.textContent =
                "Template belum tersedia";

            unavailableOption.disabled = true;

            select.appendChild(unavailableOption);

            return;
        }

        const optionFragment =
            optionTemplate.content.cloneNode(true);

        select.appendChild(optionFragment);
    }

    /*
    |--------------------------------------------------------------------------
    | Sinkronisasi nama input
    |--------------------------------------------------------------------------
    */
    function syncQuestionRowNames() {
        if (!createBody) {
            return;
        }

        const rows = createBody.querySelectorAll(
            ".question-create-row"
        );

        rows.forEach(function (row, index) {
            const fields = row.querySelectorAll(
                "[data-question-field]"
            );

            fields.forEach(function (field) {
                const fieldName =
                    field.dataset.questionField;

                if (!fieldName) {
                    return;
                }

                field.name =
                    `questions[${index}][${fieldName}]`;
            });
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Tambah baris
    |--------------------------------------------------------------------------
    */
    function addQuestionRow() {
        if (!createBody) {
            console.error(
                "Element #createQuestionBody tidak ditemukan."
            );

            return;
        }

        if (!rowTemplate) {
            console.error(
                "Template #createQuestionRowTemplate tidak ditemukan."
            );

            return;
        }

        if (!formTypeInput) {
            console.error(
                "Input #create_question_formtype_id tidak ditemukan."
            );

            return;
        }

        const fragment =
            rowTemplate.content.cloneNode(true);

        const row = fragment.querySelector(
            ".question-create-row"
        );

        if (!row) {
            console.error(
                ".question-create-row tidak ditemukan di dalam template."
            );

            return;
        }

        const typeSelect = row.querySelector(
            "[data-question-type-select]"
        );

        fillQuestionTypeSelect(
            typeSelect,
            formTypeInput.value
        );

        createBody.appendChild(fragment);

        syncQuestionRowNames();
    }

    /*
    |--------------------------------------------------------------------------
    | Simpan tombol pembuka modal
    |--------------------------------------------------------------------------
    | Capture true memastikan tombol tersimpan sebelum GlobalModal
    | mengirim event modal:opened.
    */
    document.addEventListener(
        "click",
        function (event) {
            const button = event.target.closest(
                '[data-modal-open="createQuestionModal"]'
            );

            if (!button) {
                return;
            }

            createQuestionTrigger = button;
        },
        true
    );

    /*
    |--------------------------------------------------------------------------
    | Isi Create Question Modal
    |--------------------------------------------------------------------------
    | Semua inisialisasi create dilakukan hanya di sini.
    */
    document.addEventListener(
        "modal:opened",
        function (event) {
            if (
                !event.detail ||
                event.detail.id !== "createQuestionModal"
            ) {
                return;
            }

            if (!createQuestionTrigger) {
                console.error(
                    "Tombol pembuka Create Question tidak ditemukan."
                );

                return;
            }

            if (
                !createForm ||
                !createBody ||
                !groupInput ||
                !formInput ||
                !formTypeInput
            ) {
                console.error(
                    "Elemen Create Question Modal belum lengkap."
                );

                return;
            }

            createForm.reset();

            createForm.action =
                createQuestionTrigger.dataset.action || "";

            groupInput.value =
                createQuestionTrigger.dataset.groupId || "";

            formInput.value =
                createQuestionTrigger.dataset.formId || "";

            formTypeInput.value =
                createQuestionTrigger.dataset.formTypeId || "";

            if (formNameElement) {
                formNameElement.textContent =
                    createQuestionTrigger.dataset.formName ||
                    "-";
            }

            createBody.innerHTML = "";

            addQuestionRow();
        }
    );

    /*
    |--------------------------------------------------------------------------
    | Tombol tambah baris
    |--------------------------------------------------------------------------
    */
    document.addEventListener(
        "click",
        function (event) {
            const button = event.target.closest(
                "#addQuestionRow"
            );

            if (!button) {
                return;
            }

            if (
                createModal &&
                !button.closest("#createQuestionModal")
            ) {
                return;
            }

            addQuestionRow();
        }
    );

    /*
    |--------------------------------------------------------------------------
    | Tombol hapus baris
    |--------------------------------------------------------------------------
    */
    document.addEventListener(
        "click",
        function (event) {
            const button = event.target.closest(
                "[data-remove-question-row]"
            );

            if (!button || !createBody) {
                return;
            }

            const row = button.closest(
                ".question-create-row"
            );

            if (!row || !createBody.contains(row)) {
                return;
            }

            const rows = createBody.querySelectorAll(
                ".question-create-row"
            );

            if (rows.length <= 1) {
                alert(
                    "Minimal harus ada satu baris pertanyaan."
                );

                return;
            }

            row.remove();

            syncQuestionRowNames();
        }
    );

    /*
    |--------------------------------------------------------------------------
    | Keterangan jenis pertanyaan
    |--------------------------------------------------------------------------
    */
    document.addEventListener(
        "change",
        function (event) {
            const select = event.target.closest(
                "[data-question-type-select]"
            );

            if (!select) {
                return;
            }

            const row = select.closest(
                ".question-create-row"
            );

            if (!row) {
                return;
            }

            const help = row.querySelector(
                "[data-question-type-help]"
            );

            if (!help) {
                return;
            }

            const selectedOption =
                select.options[select.selectedIndex];

            help.textContent =
                selectedOption?.dataset.description || "";
        }
    );

    /*
    |--------------------------------------------------------------------------
    | Bersihkan data ketika modal ditutup
    |--------------------------------------------------------------------------
    */
    document.addEventListener(
        "click",
        function (event) {
            const closeButton = event.target.closest(
                '[data-modal-close="createQuestionModal"]'
            );

            if (!closeButton) {
                return;
            }

            if (createForm) {
                createForm.reset();
                createForm.removeAttribute("action");
            }

            if (createBody) {
                createBody.innerHTML = "";
            }

            if (formNameElement) {
                formNameElement.textContent = "";
            }

            createQuestionTrigger = null;
        }
    );
});

document.addEventListener("DOMContentLoaded", function () {
    if (window.__editQuestionInitialized) {
        return;
    }

    window.__editQuestionInitialized = true;

    let editQuestionTrigger = null;

    const editForm = document.getElementById(
        "editQuestionForm"
    );

    const idInput = document.getElementById(
        "edit_question_id"
    );

    const groupInput = document.getElementById(
        "edit_question_group_id"
    );

    const formInput = document.getElementById(
        "edit_question_form_id"
    );

    const formTypeInput = document.getElementById(
        "edit_question_formtype_id"
    );

    const headerInput = document.getElementById(
        "edit_question_header"
    );

    const noInput = document.getElementById(
        "edit_question_no"
    );

    const nameInput = document.getElementById(
        "edit_question_name"
    );

    const typeInput = document.getElementById(
        "edit_question_type"
    );

    const typeHelp = document.getElementById(
        "edit_question_type_help"
    );

    const typeWarning = document.getElementById(
        "edit_question_type_warning"
    );

    /*
    |--------------------------------------------------------------------------
    | Ambil template berdasarkan jenis form
    |--------------------------------------------------------------------------
    */
    function getQuestionTypeTemplate(formTypeId) {
        switch (Number(formTypeId)) {
            case 1:
                return document.getElementById(
                    "generalQuestionnaireTypeOptions"
                );

            case 2:
                return document.getElementById(
                    "customerAssessment15TypeOptions"
                );

            case 3:
                return document.getElementById(
                    "customerAssessment17TypeOptions"
                );

            case 4:
                return document.getElementById(
                    "engagementAssessment15TypeOptions"
                );

            case 5:
                return document.getElementById(
                    "engagementAssessment17TypeOptions"
                );

            case 6:
                return document.getElementById(
                    "ranking13TypeOptions"
                );

            case 7:
                return document.getElementById(
                    "ranking15TypeOptions"
                );

            case 8:
                return document.getElementById(
                    "strengthComplaintSuggestionTypeOptions"
                );

            case 9:
                return document.getElementById(
                    "complaintSuggestionTypeOptions"
                )
            case 10:
                return document.getElementById(
                    "suggestionTypeOptions"
                );
            case 11:
                return document.getElementById(
                    "competitorTypeOptions"
                );
            case 13:
                return document.getElementById(
                    "competitor17TypeOptions"
                );
            case 14:
                return document.getElementById(
                    "respondentCompetitorTypeOptions"
                );

            default:
                return null;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Tampilkan deskripsi tipe terpilih
    |--------------------------------------------------------------------------
    */
    function updateQuestionTypeHelp() {
        if (!typeInput || !typeHelp) {
            return;
        }

        const selectedOption =
            typeInput.options[typeInput.selectedIndex];

        typeHelp.textContent =
            selectedOption?.dataset.description || "";
    }

    /*
    |--------------------------------------------------------------------------
    | Isi pilihan tipe pertanyaan
    |--------------------------------------------------------------------------
    */
    function fillQuestionTypeOptions(
        formTypeId,
        selectedQuestionTypeId
    ) {
        if (!typeInput) {
            return;
        }

        typeInput.innerHTML = "";

        const placeholder =
            document.createElement("option");

        placeholder.value = "";
        placeholder.textContent = "Pilih tipe";

        typeInput.appendChild(placeholder);

        const template =
            getQuestionTypeTemplate(formTypeId);

        if (!template) {
            console.error(
                "Template pilihan tipe tidak ditemukan.",
                {
                    formTypeId: formTypeId
                }
            );

            return;
        }

        typeInput.appendChild(
            template.content.cloneNode(true)
        );

        /*
         * Nilai harus diatur setelah option dimasukkan.
         */
        typeInput.value = String(
            selectedQuestionTypeId || ""
        );

        typeInput.dataset.originalValue = String(
            selectedQuestionTypeId || ""
        );

        updateQuestionTypeHelp();
    }

    /*
    |--------------------------------------------------------------------------
    | Simpan tombol edit yang diklik
    |--------------------------------------------------------------------------
    */
    document.addEventListener(
        "click",
        function (event) {
            const button = event.target.closest(
                '[data-modal-open="editQuestionModal"]'
            );

            if (!button) {
                return;
            }

            /*
             * Mencegah navigasi apabila tanpa sengaja masih berada
             * di dalam tag <a>.
             */
            event.preventDefault();

            editQuestionTrigger = button;
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
            if (
                !event.detail ||
                event.detail.id !== "editQuestionModal"
            ) {
                return;
            }

            if (!editQuestionTrigger) {
                console.error(
                    "Tombol edit pertanyaan tidak ditemukan."
                );

                return;
            }

            if (
                !editForm ||
                !idInput ||
                !groupInput ||
                !formInput ||
                !formTypeInput ||
                !headerInput ||
                !noInput ||
                !nameInput ||
                !typeInput
            ) {
                console.error(
                    "Elemen modal edit pertanyaan belum lengkap."
                );

                return;
            }

            editForm.reset();

            editForm.action =
                editQuestionTrigger.dataset.action || "";

            idInput.value =
                editQuestionTrigger.dataset.id || "";

            groupInput.value =
                editQuestionTrigger.dataset.groupId || "";

            formInput.value =
                editQuestionTrigger.dataset.formId || "";

            formTypeInput.value =
                editQuestionTrigger.dataset.formTypeId || "";

            headerInput.value =
                editQuestionTrigger.dataset.header || "";

            noInput.value =
                editQuestionTrigger.dataset.no || "";

            nameInput.value =
                editQuestionTrigger.dataset.name || "";

            fillQuestionTypeOptions(
                formTypeInput.value,
                editQuestionTrigger.dataset.questionTypeId
            );

            if (typeWarning) {
                typeWarning.classList.add("hidden");
            }
        }
    );

    /*
    |--------------------------------------------------------------------------
    | Saat tipe pertanyaan berubah
    |--------------------------------------------------------------------------
    */
    typeInput?.addEventListener(
        "change",
        function () {
            updateQuestionTypeHelp();

            const originalValue = String(
                typeInput.dataset.originalValue || ""
            );

            const currentValue = String(
                typeInput.value || ""
            );

            if (typeWarning) {
                typeWarning.classList.toggle(
                    "hidden",
                    originalValue === currentValue
                );
            }
        }
    );

    /*
    |--------------------------------------------------------------------------
    | Reset ketika modal ditutup
    |--------------------------------------------------------------------------
    */
    document.addEventListener(
        "click",
        function (event) {
            const closeButton = event.target.closest(
                '[data-modal-close="editQuestionModal"]'
            );

            if (!closeButton) {
                return;
            }

            editForm?.reset();
            editForm?.removeAttribute("action");

            if (typeInput) {
                typeInput.innerHTML =
                    '<option value="">Pilih tipe</option>';

                delete typeInput.dataset.originalValue;
            }

            if (typeHelp) {
                typeHelp.textContent = "";
            }

            if (typeWarning) {
                typeWarning.classList.add("hidden");
            }

            editQuestionTrigger = null;
        }
    );
});

document.addEventListener("DOMContentLoaded", function () {
    if (window.__deleteQuestionInitialized) {
        return;
    }

    window.__deleteQuestionInitialized = true;

    const deleteForm = document.getElementById(
        "deleteQuestionForm"
    );

    const deleteIdInput = document.getElementById(
        "delete_question_id"
    );

    const deleteNameElement = document.getElementById(
        "delete_question_name"
    );

    const optionCountElement = document.getElementById(
        "delete_question_option_count"
    );

    const optionWarningElement = document.getElementById(
        "delete_question_option_warning"
    );

    document.addEventListener(
        "click",
        function (event) {
            const button = event.target.closest(
                '[data-modal-open="deleteQuestionModal"]'
            );

            if (!button) {
                return;
            }

            event.preventDefault();

            const deleteAction =
                button.dataset.action || "";

            const questionId =
                button.dataset.id || "";

            const questionName =
                button.dataset.name || "-";

            const optionCount = Number(
                button.dataset.optionCount || 0
            );

            if (!deleteForm) {
                console.error(
                    "#deleteQuestionForm tidak ditemukan."
                );

                return;
            }

            if (!deleteAction) {
                console.error(
                    "data-action tombol delete kosong."
                );

                return;
            }

            deleteForm.setAttribute(
                "action",
                deleteAction
            );

            if (deleteIdInput) {
                deleteIdInput.value = questionId;
            }

            if (deleteNameElement) {
                deleteNameElement.textContent =
                    questionName;
            }

            if (optionCountElement) {
                optionCountElement.textContent =
                    optionCount;
            }

            if (optionWarningElement) {
                optionWarningElement.classList.toggle(
                    "hidden",
                    optionCount === 0
                );
            }

            console.log(
                "Delete question action:",
                deleteForm.action
            );
        },
        true
    );

    deleteForm?.addEventListener(
        "submit",
        function (event) {
            const action =
                deleteForm.getAttribute("action");

            if (!action) {
                event.preventDefault();

                alert(
                    "URL penghapusan belum tersedia."
                );

                return;
            }

            console.log(
                "Submitting delete to:",
                action
            );
        }
    );
});

document.addEventListener("DOMContentLoaded", function () {
    /*
    |--------------------------------------------------------------------------
    | Guard
    |--------------------------------------------------------------------------
    */
    if (window.__createCompetitorInitialized) {
        return;
    }

    window.__createCompetitorInitialized = true;

    /*
    |--------------------------------------------------------------------------
    | Elemen modal competitor
    |--------------------------------------------------------------------------
    */
    const competitorModal = document.getElementById(
        "createCompetitorModal"
    );

    const competitorForm = document.getElementById(
        "createCompetitorForm"
    );

    const competitorBody = document.getElementById(
        "createCompetitorBody"
    );

    const competitorTemplate = document.getElementById(
        "createCompetitorRowTemplate"
    );

    const competitorGroupInput = document.getElementById(
        "create_competitor_group_id"
    );

    const competitorFormInput = document.getElementById(
        "create_competitor_form_id"
    );

    let createCompetitorTrigger = null;

    /*
    |--------------------------------------------------------------------------
    | Sinkronisasi nama input
    |--------------------------------------------------------------------------
    */
    function syncCompetitorRowNames() {
        if (!competitorBody) {
            return;
        }

        const rows = competitorBody.querySelectorAll(
            ".competitor-create-row"
        );

        rows.forEach(function (row) {
            const nameInput = row.querySelector(
                '[data-competitor-field="name"]'
            );

            if (nameInput) {
                /*
                 * Controller lama menerima name[].
                 */
                nameInput.name = "name[]";
            }
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Tambah row competitor
    |--------------------------------------------------------------------------
    */
    function addCompetitorRow() {
        if (!competitorBody) {
            console.error(
                "#createCompetitorBody tidak ditemukan."
            );

            return;
        }

        if (!competitorTemplate) {
            console.error(
                "#createCompetitorRowTemplate tidak ditemukan."
            );

            return;
        }

        const fragment =
            competitorTemplate.content.cloneNode(true);

        const row = fragment.querySelector(
            ".competitor-create-row"
        );

        if (!row) {
            console.error(
                ".competitor-create-row tidak ditemukan di template."
            );

            return;
        }

        competitorBody.appendChild(fragment);

        syncCompetitorRowNames();
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
                '[data-modal-open="createCompetitorModal"]'
            );

            if (!button) {
                return;
            }

            createCompetitorTrigger = button;
        },
        true
    );

    /*
    |--------------------------------------------------------------------------
    | Inisialisasi ketika modal dibuka
    |--------------------------------------------------------------------------
    */
    document.addEventListener(
        "modal:opened",
        function (event) {
            if (
                !event.detail ||
                event.detail.id !== "createCompetitorModal"
            ) {
                return;
            }

            if (!createCompetitorTrigger) {
                console.error(
                    "Tombol pembuka modal competitor tidak ditemukan."
                );

                return;
            }

            if (
                !competitorForm ||
                !competitorBody ||
                !competitorTemplate ||
                !competitorGroupInput ||
                !competitorFormInput
            ) {
                console.error(
                    "Elemen create competitor belum lengkap."
                );

                return;
            }

            competitorForm.reset();

            competitorForm.action =
                createCompetitorTrigger.dataset.action || "";

            competitorGroupInput.value =
                createCompetitorTrigger.dataset.groupId || "";

            competitorFormInput.value =
                createCompetitorTrigger.dataset.formId || "";

            /*
             * Bersihkan row lama, lalu buat satu row awal.
             */
            competitorBody.innerHTML = "";

            addCompetitorRow();
        }
    );

    /*
    |--------------------------------------------------------------------------
    | Tombol tambah baris
    |--------------------------------------------------------------------------
    */
    document.addEventListener(
        "click",
        function (event) {
            const button = event.target.closest(
                "#addCompetitorRow"
            );

            if (!button) {
                return;
            }

            if (
                competitorModal &&
                !button.closest("#createCompetitorModal")
            ) {
                return;
            }

            addCompetitorRow();
        }
    );

    /*
    |--------------------------------------------------------------------------
    | Tombol hapus baris
    |--------------------------------------------------------------------------
    */
    document.addEventListener(
        "click",
        function (event) {
            const button = event.target.closest(
                "[data-remove-competitor-row]"
            );

            if (!button || !competitorBody) {
                return;
            }

            const row = button.closest(
                ".competitor-create-row"
            );

            if (!row || !competitorBody.contains(row)) {
                return;
            }

            const rows = competitorBody.querySelectorAll(
                ".competitor-create-row"
            );

            if (rows.length <= 1) {
                alert(
                    "Minimal harus ada satu baris kompetitor."
                );

                return;
            }

            row.remove();

            syncCompetitorRowNames();
        }
    );

    /*
    |--------------------------------------------------------------------------
    | Validasi sebelum submit
    |--------------------------------------------------------------------------
    */
    competitorForm?.addEventListener(
        "submit",
        function (event) {
            const action =
                competitorForm.getAttribute("action");

            if (!action) {
                event.preventDefault();

                alert(
                    "URL penyimpanan kompetitor belum tersedia."
                );

                return;
            }

            const nameInputs =
                competitorBody?.querySelectorAll(
                    '[data-competitor-field="name"]'
                ) || [];

            let hasEmptyInput = false;

            nameInputs.forEach(function (input) {
                if (!input.value.trim()) {
                    hasEmptyInput = true;
                }
            });

            if (hasEmptyInput) {
                event.preventDefault();

                alert(
                    "Nama kompetitor wajib diisi."
                );
            }
        }
    );

    /*
    |--------------------------------------------------------------------------
    | Reset ketika modal ditutup
    |--------------------------------------------------------------------------
    */
    document.addEventListener(
        "click",
        function (event) {
            const button = event.target.closest(
                '[data-modal-close="createCompetitorModal"]'
            );

            if (!button) {
                return;
            }

            competitorForm?.reset();
            competitorForm?.removeAttribute("action");

            if (competitorBody) {
                competitorBody.innerHTML = "";
            }

            if (competitorGroupInput) {
                competitorGroupInput.value = "";
            }

            if (competitorFormInput) {
                competitorFormInput.value = "";
            }

            createCompetitorTrigger = null;
        }
    );
});

document.addEventListener("DOMContentLoaded", function () {
    if (window.__editCompetitorInitialized) {
        return;
    }

    window.__editCompetitorInitialized = true;

    const editForm = document.getElementById(
        "editCompetitorForm"
    );

    const editIdInput = document.getElementById(
        "edit_competitor_id"
    );

    const editGroupInput = document.getElementById(
        "edit_competitor_group_id"
    );

    const editFormInput = document.getElementById(
        "edit_competitor_form_id"
    );

    const editNameInput = document.getElementById(
        "edit_competitor_name"
    );

    document.addEventListener(
        "click",
        function (event) {
            const button = event.target.closest(
                '[data-modal-open="editCompetitorModal"]'
            );

            if (!button) {
                return;
            }

            event.preventDefault();

            if (
                !editForm ||
                !editIdInput ||
                !editGroupInput ||
                !editFormInput ||
                !editNameInput
            ) {
                console.error(
                    "Elemen modal edit competitor belum lengkap."
                );

                return;
            }

            editForm.action =
                button.dataset.action || "";

            editIdInput.value =
                button.dataset.id || "";

            editGroupInput.value =
                button.dataset.groupId || "";

            editFormInput.value =
                button.dataset.formId || "";

            editNameInput.value =
                button.dataset.name || "";
        },
        true
    );

    editForm?.addEventListener(
        "submit",
        function (event) {
            const action =
                editForm.getAttribute("action");

            if (!action) {
                event.preventDefault();

                alert(
                    "URL pembaruan kompetitor tidak ditemukan."
                );

                return;
            }

            if (!editNameInput.value.trim()) {
                event.preventDefault();

                alert(
                    "Nama kompetitor wajib diisi."
                );
            }
        }
    );

    document.addEventListener(
        "click",
        function (event) {
            const closeButton = event.target.closest(
                '[data-modal-close="editCompetitorModal"]'
            );

            if (!closeButton) {
                return;
            }

            editForm?.reset();
            editForm?.removeAttribute("action");
        }
    );
});

document.addEventListener("DOMContentLoaded", function () {
    if (window.__deleteCompetitorInitialized) {
        return;
    }

    window.__deleteCompetitorInitialized = true;

    const deleteForm = document.getElementById(
        "deleteCompetitorForm"
    );

    const deleteIdInput = document.getElementById(
        "delete_competitor_id"
    );

    const deleteNameElement = document.getElementById(
        "delete_competitor_name"
    );

    document.addEventListener(
        "click",
        function (event) {
            const button = event.target.closest(
                '[data-modal-open="deleteCompetitorModal"]'
            );

            if (!button) {
                return;
            }

            event.preventDefault();

            if (!deleteForm) {
                console.error(
                    "#deleteCompetitorForm tidak ditemukan."
                );

                return;
            }

            const action =
                button.dataset.action || "";

            if (!action) {
                console.error(
                    "data-action delete competitor kosong."
                );

                return;
            }

            deleteForm.action = action;

            if (deleteIdInput) {
                deleteIdInput.value =
                    button.dataset.id || "";
            }

            if (deleteNameElement) {
                deleteNameElement.textContent =
                    button.dataset.name || "-";
            }
        },
        true
    );

    deleteForm?.addEventListener(
        "submit",
        function (event) {
            const action =
                deleteForm.getAttribute("action");

            if (!action) {
                event.preventDefault();

                alert(
                    "URL penghapusan kompetitor tidak ditemukan."
                );
            }
        }
    );

    document.addEventListener(
        "click",
        function (event) {
            const closeButton = event.target.closest(
                '[data-modal-close="deleteCompetitorModal"]'
            );

            if (!closeButton) {
                return;
            }

            deleteForm?.reset();
            deleteForm?.removeAttribute("action");

            if (deleteNameElement) {
                deleteNameElement.textContent = "";
            }
        }
    );
});

document.addEventListener("click", function (event) {
    const button = event.target.closest(
        '[data-modal-open="importQuestionModal"]'
    );

    if (!button) {
        return;
    }

    const importForm = document.getElementById(
        "importQuestionForm"
    );

    const groupInput = document.getElementById(
        "importQuestionGroupId"
    );

    const formInput = document.getElementById(
        "importQuestionFormId"
    );

    const formName = document.getElementById(
        "importQuestionFormName"
    );

    const fileInput = document.getElementById(
        "importQuestionFile"
    );

    const fileName = document.getElementById(
        "importQuestionFileName"
    );

    if (importForm) {
        importForm.action = button.dataset.action;
    }

    if (groupInput) {
        groupInput.value = button.dataset.groupId || "";
    }

    if (formInput) {
        formInput.value = button.dataset.formId || "";
    }

    if (formName) {
        formName.textContent =
            button.dataset.formName || "-";
    }

    if (fileInput) {
        fileInput.value = "";
    }

    if (fileName) {
        fileName.textContent = "";
        fileName.classList.add("hidden");
    }
});


document.addEventListener("change", function (event) {
    if (event.target.id !== "importQuestionFile") {
        return;
    }

    const fileInput = event.target;

    const fileName = document.getElementById(
        "importQuestionFileName"
    );

    if (!fileName) {
        return;
    }

    const selectedFile = fileInput.files[0];

    if (!selectedFile) {
        fileName.textContent = "";
        fileName.classList.add("hidden");

        return;
    }

    fileName.textContent = selectedFile.name;
    fileName.classList.remove("hidden");
});


document.addEventListener("submit", function (event) {
    if (event.target.id !== "importQuestionForm") {
        return;
    }

    const submitButton = document.getElementById(
        "importQuestionSubmitButton"
    );

    if (!submitButton) {
        return;
    }

    submitButton.disabled = true;

    submitButton.classList.add(
        "cursor-not-allowed",
        "opacity-60"
    );

    submitButton.innerHTML = `
        <i class="fa-solid fa-spinner fa-spin"></i>
        Memproses Import...
    `;
});