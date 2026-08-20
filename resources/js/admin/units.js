document.addEventListener("DOMContentLoaded", function () {
    const page = document.getElementById("unitMainPage");

    if (!page) {
        return;
    }

    initializeUnitTabs(page);
    initializeCreateUnit();
    initializeEditUnit();
    initializeDeleteUnit();
    initializeUnitBulkDelete();
    initializeUnitImport();
    initializeUnitFilter(page);
    initializeUnitAlerts();
});

/*
|--------------------------------------------------------------------------
| TAB UNIT & PERTANYAAN
|--------------------------------------------------------------------------
*/
function initializeUnitTabs(page) {
    const tabButtons = Array.from(
        page.querySelectorAll("[data-unit-tab]")
    );

    const tabContents = Array.from(
        page.querySelectorAll("[data-unit-tab-content]")
    );

    if (tabButtons.length === 0 || tabContents.length === 0) {
        return;
    }

    function activateTab(tabName, updateUrl = true) {
        const allowedTabs = tabButtons.map(function (button) {
            return button.dataset.unitTab;
        });

        if (!allowedTabs.includes(tabName)) {
            tabName = "unit";
        }

        tabButtons.forEach(function (button) {
            const active = button.dataset.unitTab === tabName;

            button.classList.toggle("border-blue-600", active);
            button.classList.toggle("text-blue-600", active);
            button.classList.toggle("bg-white", active);
            button.classList.toggle("border-transparent", !active);
            button.classList.toggle("text-gray-600", !active);
            button.setAttribute("aria-selected", active ? "true" : "false");
        });

        tabContents.forEach(function (content) {
            const active =
                content.dataset.unitTabContent === tabName;

            content.classList.toggle("hidden", !active);
        });

        if (updateUrl) {
            const url = new URL(window.location.href);
            url.searchParams.set("tab", tabName);
            window.history.replaceState({}, "", url.toString());
        }
    }

    tabButtons.forEach(function (button) {
        button.addEventListener("click", function (event) {
            event.preventDefault();
            activateTab(button.dataset.unitTab);
        });
    });

    const requestedTab = new URLSearchParams(
        window.location.search
    ).get("tab");

    activateTab(requestedTab === "question" ? "question" : "unit", false);
}

/*
|--------------------------------------------------------------------------
| CREATE UNIT
|--------------------------------------------------------------------------
*/
function initializeCreateUnit() {
    const modal = document.getElementById("createUnitModal");
    const tableBody = document.getElementById("createUnitBody");
    const template = document.getElementById(
        "createUnitRowTemplate"
    );

    const addButton = document.querySelector(
        '[data-add-row="createUnitBody"]'
    );

    if (!modal) {
        console.warn(
            "Modal #createUnitModal tidak ditemukan."
        );
        return;
    }

    if (!tableBody) {
        console.warn(
            "Table body #createUnitBody tidak ditemukan."
        );
        return;
    }

    if (!template) {
        console.warn(
            "Template #createUnitRowTemplate tidak ditemukan."
        );
        return;
    }

    function getRows() {
        return Array.from(
            tableBody.querySelectorAll(
                ".create-unit-row"
            )
        );
    }

    function refreshRows() {
        getRows().forEach(function (row, index) {
            const number = row.querySelector(
                "[data-row-number]"
            );

            const input = row.querySelector(
                'input[name^="name"]'
            );

            if (number) {
                number.textContent = index + 1;
            }

            if (input) {
                input.name = `name[${index}]`;
            }
        });
    }

    function addRow() {
        const fragment = template.content.cloneNode(true);

        tableBody.appendChild(fragment);

        refreshRows();

        const rows = getRows();
        const lastRow = rows[rows.length - 1];

        const input = lastRow?.querySelector(
            'input[name^="name"]'
        );

        if (input) {
            input.focus();
        }
    }

    function ensureRowExists() {
        if (getRows().length === 0) {
            addRow();
        }
    }

    /*
    |--------------------------------------------------------------------------
    | TAMBAH BARIS
    |--------------------------------------------------------------------------
    */
    if (addButton) {
        addButton.addEventListener(
            "click",
            function () {
                addRow();
            }
        );
    }

    /*
    |--------------------------------------------------------------------------
    | HAPUS BARIS
    |--------------------------------------------------------------------------
    */
    tableBody.addEventListener(
        "click",
        function (event) {
            const removeButton = event.target.closest(
                "[data-remove-unit-row]"
            );

            if (!removeButton) {
                return;
            }

            const row = removeButton.closest(
                ".create-unit-row"
            );

            if (!row) {
                return;
            }

            const rows = getRows();

            /*
             * Apabila hanya tersisa satu baris,
             * jangan hapus barisnya. Kosongkan input.
             */
            if (rows.length === 1) {
                const input = row.querySelector(
                    'input[name^="name"]'
                );

                if (input) {
                    input.value = "";
                    input.focus();
                }

                return;
            }

            row.remove();
            refreshRows();
        }
    );

    /*
    |--------------------------------------------------------------------------
    | SAAT MODAL DIBUKA
    |--------------------------------------------------------------------------
    */
    modal.addEventListener(
        "modal:opened",
        function () {
            ensureRowExists();
        }
    );

    /*
     * Membuat baris pertama langsung ketika halaman selesai dimuat.
     */
    ensureRowExists();
}

/*
|--------------------------------------------------------------------------
| EDIT UNIT
|--------------------------------------------------------------------------
*/
function initializeEditUnit() {
    const form = document.getElementById("editUnitForm");
    const input =
        document.getElementById("editUnitName") ||
        document.getElementById("edit_name") ||
        document.getElementById("edit_unit_name");
    const idInput = document.getElementById("edit_unit_id");

    if (!form || !input) {
        return;
    }

    document.addEventListener("click", function (event) {
        const button = event.target.closest(
            '[data-modal-open="editUnitModal"]'
        );

        if (!button) {
            return;
        }

        form.action = button.dataset.action || "";
        input.value = button.dataset.name || "";
        if (idInput) {
            idInput.value = button.dataset.id || "";
        }

        window.setTimeout(function () {
            input.focus();
            input.select();
        }, 100);
    });
}

/*
|--------------------------------------------------------------------------
| DELETE UNIT
|--------------------------------------------------------------------------
*/
function initializeDeleteUnit() {
    const form = document.getElementById("deleteUnitForm");
    const name = document.getElementById("deleteUnitName");

    if (!form || !name) {
        return;
    }

    document.addEventListener("click", function (event) {
        const button = event.target.closest(
            '[data-modal-open="deleteUnitModal"]'
        );

        if (!button) {
            return;
        }

        form.action = button.dataset.action || "";
        name.textContent = button.dataset.name || "-";
    });
}

/*
|--------------------------------------------------------------------------
| BULK DELETE UNIT
|--------------------------------------------------------------------------
*/
function initializeUnitBulkDelete() {
    const selectAll = document.getElementById("selectAllUnit");
    const deleteButton = document.getElementById(
        "btnDeleteSelectedUnit"
    );

    const idsContainer = document.getElementById(
        "bulkUnitIds"
    );

    const modalCount = document.getElementById(
        "bulkDeleteUnitCount"
    );

    const modal = document.getElementById(
        "bulkDeleteUnitModal"
    );
    const bulkAction = document.getElementById("unitBulkAction");
    const selectedCount = document.getElementById("selectedUnitCount");

    if (!deleteButton) {
        console.warn(
            "Button #btnDeleteSelectedUnit tidak ditemukan."
        );
        return;
    }

    if (!modal) {
        console.warn(
            "Modal #bulkDeleteUnitModal tidak ditemukan."
        );
        return;
    }

    function getCheckboxes() {
        return Array.from(
            document.querySelectorAll(".unit-checkbox")
        );
    }

    function getSelectedCheckboxes() {
        return getCheckboxes().filter(function (checkbox) {
            return checkbox.checked;
        });
    }

    function refreshSelection() {
        const allCheckboxes = getCheckboxes();
        const selectedCheckboxes =
            getSelectedCheckboxes();

        deleteButton.disabled =
            selectedCheckboxes.length === 0;

        if (bulkAction) {
            bulkAction.classList.toggle(
                "hidden",
                selectedCheckboxes.length === 0
            );
        }

        if (selectedCount) {
            selectedCount.textContent = String(selectedCheckboxes.length);
        }

        if (selectAll) {
            selectAll.checked =
                allCheckboxes.length > 0 &&
                selectedCheckboxes.length ===
                    allCheckboxes.length;

            selectAll.indeterminate =
                selectedCheckboxes.length > 0 &&
                selectedCheckboxes.length <
                    allCheckboxes.length;
        }
    }

    function openBulkDeleteModal() {
        modal.classList.remove("hidden");
        modal.classList.add("flex");

        document.body.classList.add(
            "overflow-hidden"
        );
    }

    function closeBulkDeleteModal() {
        modal.classList.add("hidden");
        modal.classList.remove("flex");

        document.body.classList.remove(
            "overflow-hidden"
        );
    }

    if (selectAll) {
        selectAll.addEventListener(
            "change",
            function () {
                getCheckboxes().forEach(
                    function (checkbox) {
                        checkbox.checked =
                            selectAll.checked;
                    }
                );

                refreshSelection();
            }
        );
    }

    getCheckboxes().forEach(function (checkbox) {
        checkbox.addEventListener(
            "change",
            refreshSelection
        );
    });

    deleteButton.addEventListener(
        "click",
        function () {
            const checked =
                getSelectedCheckboxes();

            if (checked.length === 0) {
                return;
            }

            if (idsContainer) {
                idsContainer.innerHTML = "";

                checked.forEach(function (checkbox) {
                    const input =
                        document.createElement("input");

                    input.type = "hidden";
                    input.name = "ids[]";
                    input.value = checkbox.value;

                    idsContainer.appendChild(input);
                });
            }

            if (modalCount) {
                modalCount.textContent =
                    checked.length;
            }

            openBulkDeleteModal();
        }
    );

    document
        .querySelectorAll(
            '[data-modal-close="bulkDeleteUnitModal"]'
        )
        .forEach(function (button) {
            button.addEventListener(
                "click",
                closeBulkDeleteModal
            );
        });

    modal.addEventListener(
        "click",
        function (event) {
            if (event.target === modal) {
                closeBulkDeleteModal();
            }
        }
    );

    document.addEventListener(
        "keydown",
        function (event) {
            if (
                event.key === "Escape" &&
                !modal.classList.contains("hidden")
            ) {
                closeBulkDeleteModal();
            }
        }
    );

    refreshSelection();
}

/*
|--------------------------------------------------------------------------
| IMPORT UNIT
|--------------------------------------------------------------------------
*/
function initializeUnitImport() {
    const fileInput = document.getElementById("unitImportFile");
    const fileName = document.getElementById("unitImportFileName");
    const importButton = document.getElementById("btnImportUnit");
    const importForm = document.getElementById("importUnitForm");

    if (!fileInput || !fileName || !importButton || !importForm) {
        return;
    }

    importButton.disabled = true;

    fileInput.addEventListener("change", function () {
        const file = fileInput.files?.[0];

        if (!file) {
            fileName.textContent = "Pilih File";
            importButton.disabled = true;
            return;
        }

        const extension = file.name.split(".").pop().toLowerCase();

        if (!["xlsx", "xls"].includes(extension)) {
            window.alert("File Unit harus berformat XLSX atau XLS.");
            fileInput.value = "";
            fileName.textContent = "Pilih File";
            importButton.disabled = true;
            return;
        }

        fileName.textContent = file.name;
        importButton.disabled = false;
    });

    importForm.addEventListener("submit", function (event) {
        if (!fileInput.files?.length) {
            event.preventDefault();
            importButton.disabled = true;
            window.alert("Pilih file Excel terlebih dahulu.");
            return;
        }

        importButton.disabled = true;
        importButton.innerHTML = `
            <i class="fa-solid fa-spinner fa-spin"></i>
            Mengimport...
        `;
    });
}

/*
|--------------------------------------------------------------------------
| FILTER UNIT
|--------------------------------------------------------------------------
*/
function initializeUnitFilter(page) {
    const input = document.getElementById("unitSearch");
    const searchButton = document.getElementById("btnSearchUnit");

    if (!input) {
        return;
    }

    function filterRows() {
        const keyword = input.value.trim().toLowerCase();

        page.querySelectorAll(".unit-row").forEach(function (row) {
            const name =
                row.dataset.unitName ||
                row.dataset.name ||
                row.textContent.toLowerCase();

            row.classList.toggle("hidden", !name.includes(keyword));
        });
    }

    input.addEventListener("input", filterRows);

    if (searchButton) {
        searchButton.addEventListener("click", filterRows);
    }
}

/*
|--------------------------------------------------------------------------
| ALERT
|--------------------------------------------------------------------------
*/
function initializeUnitAlerts() {
    document.addEventListener("click", function (event) {
        const closeButton = event.target.closest("[data-alert-close]");

        if (!closeButton) {
            return;
        }

        closeButton.closest("[data-alert]")?.remove();
    });
}
