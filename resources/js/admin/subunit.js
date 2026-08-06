document.addEventListener("DOMContentLoaded", function () {
    const page = document.getElementById(
        "subUnitPage"
    );

    if (!page) {
        return;
    }

    /*
     * Hanya terdapat satu proses inisialisasi.
     */
    initializeSubUnitTabs();
    initializeSubUnitAlerts();
    initializeCreateSubUnit();
    initializeEditSubUnit();
    initializeDeleteSubUnit();
    initializeBulkDeleteSubUnit();
    initializeImportSubUnit();
});

/*
|--------------------------------------------------------------------------
| TAB
|--------------------------------------------------------------------------
*/
function initializeSubUnitTabs() {
    const page = document.getElementById(
        "subUnitPage"
    );

    const navigation = document.getElementById(
        "subUnitTabNavigation"
    );

    if (!page || !navigation) {
        console.error(
            "Halaman atau navigation tab Sub Unit tidak ditemukan."
        );

        return;
    }

    const buttons = Array.from(
        navigation.querySelectorAll(
            "[data-subunit-tab]"
        )
    );

    const contents = Array.from(
        page.querySelectorAll(
            "[data-subunit-content]"
        )
    );

    if (
        buttons.length === 0 ||
        contents.length === 0
    ) {
        console.error(
            "Button atau content tab Sub Unit tidak ditemukan."
        );

        return;
    }

    function tabIsValid(tabName) {
        return buttons.some(function (button) {
            return (
                button.dataset.subunitTab ===
                tabName
            );
        });
    }

    function activateTab(
        requestedTab,
        updateUrl = true
    ) {
        const tabName = tabIsValid(requestedTab)
            ? requestedTab
            : "subunit";

        /*
         * Update button.
         */
        buttons.forEach(function (button) {
            const active =
                button.dataset.subunitTab ===
                tabName;

            button.classList.toggle(
                "border-blue-600",
                active
            );

            button.classList.toggle(
                "text-blue-600",
                active
            );

            button.classList.toggle(
                "bg-white",
                active
            );

            button.classList.toggle(
                "border-transparent",
                !active
            );

            button.classList.toggle(
                "text-gray-500",
                !active
            );

            button.classList.toggle(
                "hover:text-gray-700",
                !active
            );

            button.setAttribute(
                "aria-selected",
                active ? "true" : "false"
            );
        });

        /*
         * Update content.
         */
        contents.forEach(function (content) {
            const active =
                content.dataset.subunitContent ===
                tabName;

            content.classList.toggle(
                "hidden",
                !active
            );
        });

        page.dataset.activeTab = tabName;

        if (!updateUrl) {
            return;
        }

        const url = new URL(
            window.location.href
        );

        url.searchParams.set(
            "tab",
            tabName
        );

        /*
         * subunit_id hanya digunakan pada tab Hide and Show.
         */
        if (tabName !== "hide-show") {
            url.searchParams.delete(
                "subunit_id"
            );
        }

        window.history.replaceState(
            {},
            "",
            url.toString()
        );
    }

    /*
     * Event dipasang langsung pada masing-masing button.
     */
    buttons.forEach(function (button) {
        button.addEventListener(
            "click",
            function (event) {
                event.preventDefault();

                /*
                 * Hentikan global tab agar tidak ikut memproses.
                 */
                event.stopPropagation();
                event.stopImmediatePropagation();

                activateTab(
                    button.dataset.subunitTab,
                    true
                );
            }
        );
    });

    /*
     * Ambil tab awal dari URL.
     */
    const url = new URL(
        window.location.href
    );

    const tabFromUrl =
        url.searchParams.get("tab");

    const initialTab =
        tabFromUrl ||
        page.dataset.activeTab ||
        "subunit";

    activateTab(initialTab, false);
}

/*
|--------------------------------------------------------------------------
| ALERT
|--------------------------------------------------------------------------
*/
function initializeSubUnitAlerts() {
    document.addEventListener(
        "click",
        function (event) {
            const closeButton = event.target.closest(
                "[data-alert-close]"
            );

            if (!closeButton) {
                return;
            }

            const alert = closeButton.closest(
                "[data-alert]"
            );

            if (alert) {
                alert.remove();
            }
        }
    );
}

/*
|--------------------------------------------------------------------------
| OPEN MODAL PROGRAMMATICALLY
|--------------------------------------------------------------------------
*/
function openSubUnitModal(modalId) {
    const modal = document.getElementById(
        modalId
    );

    if (!modal) {
        console.error(
            `Modal #${modalId} tidak ditemukan.`
        );

        return;
    }

    modal.classList.remove("hidden");
    modal.classList.add("flex");

    document.body.classList.add(
        "overflow-hidden"
    );

    modal.dispatchEvent(
        new CustomEvent("modal:opened", {
            bubbles: true,
        })
    );
}

/*
|--------------------------------------------------------------------------
| CREATE
|--------------------------------------------------------------------------
*/
function initializeCreateSubUnit() {
    const tableBody = document.getElementById(
        "createSubUnitBody"
    );

    const template = document.getElementById(
        "createSubUnitRowTemplate"
    );

    const addButton = document.getElementById(
        "addSubUnitRow"
    );

    const modal = document.getElementById(
        "createSubUnitModal"
    );

    if (!tableBody || !template) {
        return;
    }

    function rows() {
        return Array.from(
            tableBody.querySelectorAll(
                "[data-create-subunit-row]"
            )
        );
    }

    function refreshRows() {
        rows().forEach(function (row, index) {
            const number = row.querySelector(
                "[data-row-number]"
            );

            const input = row.querySelector(
                "[data-subunit-name-input]"
            );

            if (number) {
                number.textContent = index + 1;
            }

            if (input) {
                input.name =
                    `subunits[${index}][name]`;
            }
        });
    }

    function addRow(value = "") {
        const fragment =
            template.content.cloneNode(true);

        const input = fragment.querySelector(
            "[data-subunit-name-input]"
        );

        if (input) {
            input.value = value;
        }

        tableBody.appendChild(fragment);

        refreshRows();
    }

    function ensureRowExists() {
        if (rows().length === 0) {
            addRow();
        }
    }

    if (addButton) {
        addButton.addEventListener(
            "click",
            function (event) {
                event.preventDefault();

                addRow();

                const currentRows = rows();

                const lastInput =
                    currentRows[
                        currentRows.length - 1
                    ]?.querySelector(
                        "[data-subunit-name-input]"
                    );

                lastInput?.focus();
            }
        );
    }

    tableBody.addEventListener(
        "click",
        function (event) {
            const removeButton =
                event.target.closest(
                    "[data-remove-subunit-row]"
                );

            if (!removeButton) {
                return;
            }

            event.preventDefault();

            const currentRows = rows();

            if (currentRows.length === 1) {
                const input =
                    currentRows[0].querySelector(
                        "[data-subunit-name-input]"
                    );

                if (input) {
                    input.value = "";
                    input.focus();
                }

                return;
            }

            const row = removeButton.closest(
                "[data-create-subunit-row]"
            );

            row?.remove();

            refreshRows();
        }
    );

    if (modal) {
        modal.addEventListener(
            "modal:opened",
            function () {
                ensureRowExists();

                window.setTimeout(function () {
                    tableBody
                        .querySelector(
                            "[data-subunit-name-input]"
                        )
                        ?.focus();
                }, 100);
            }
        );
    }

    ensureRowExists();
}

/*
|--------------------------------------------------------------------------
| EDIT
|--------------------------------------------------------------------------
*/
function initializeEditSubUnit() {
    const form = document.getElementById(
        "editSubUnitForm"
    );

    const input = document.getElementById(
        "editSubUnitName"
    );

    if (!form || !input) {
        return;
    }

    document.addEventListener(
        "click",
        function (event) {
            const button = event.target.closest(
                '[data-modal-open="editSubUnitModal"]'
            );

            if (!button) {
                return;
            }

            const action =
                button.dataset.action;

            if (!action) {
                console.error(
                    "Action edit Sub Unit tidak ditemukan."
                );

                return;
            }

            form.action = action;
            input.value =
                button.dataset.name || "";

            window.setTimeout(function () {
                input.focus();
                input.select();
            }, 100);
        }
    );
}

/*
|--------------------------------------------------------------------------
| DELETE
|--------------------------------------------------------------------------
*/
function initializeDeleteSubUnit() {
    const form = document.getElementById(
        "deleteSubUnitForm"
    );

    const nameElement = document.getElementById(
        "deleteSubUnitName"
    );

    if (!form || !nameElement) {
        return;
    }

    document.addEventListener(
        "click",
        function (event) {
            const button = event.target.closest(
                '[data-modal-open="deleteSubUnitModal"]'
            );

            if (!button) {
                return;
            }

            const action =
                button.dataset.action;

            if (!action) {
                console.error(
                    "Action delete Sub Unit tidak ditemukan."
                );

                return;
            }

            form.action = action;

            nameElement.textContent =
                button.dataset.name || "-";
        }
    );
}

/*
|--------------------------------------------------------------------------
| BULK DELETE
|--------------------------------------------------------------------------
*/
function initializeBulkDeleteSubUnit() {
    const selectAll = document.getElementById(
        "selectAllSubUnit"
    );

    const deleteButton = document.getElementById(
        "btnDeleteSelectedSubUnit"
    );

    const idsContainer = document.getElementById(
        "bulkSubUnitIds"
    );

    const modalCount = document.getElementById(
        "bulkDeleteCount"
    );

    const bulkAction = document.getElementById(
        "subUnitBulkAction"
    );

    const selectedCount = document.getElementById(
        "selectedSubUnitCount"
    );

    const cancelButton = document.getElementById(
        "btnCancelSubUnitSelection"
    );

    const checkboxes = Array.from(
        document.querySelectorAll(
            ".subunit-checkbox"
        )
    );

    if (!deleteButton) {
        return;
    }

    function getSelectedItems() {
        return checkboxes.filter(
            function (checkbox) {
                return checkbox.checked;
            }
        );
    }

    function refreshSelection() {
        const selected =
            getSelectedItems();

        const total =
            selected.length;

        deleteButton.disabled =
            total === 0;

        if (bulkAction) {
            bulkAction.classList.toggle(
                "hidden",
                total === 0
            );
        }

        if (selectedCount) {
            selectedCount.textContent =
                total;
        }

        if (selectAll) {
            selectAll.checked =
                checkboxes.length > 0 &&
                total === checkboxes.length;

            selectAll.indeterminate =
                total > 0 &&
                total < checkboxes.length;
        }
    }

    function clearSelection() {
        checkboxes.forEach(
            function (checkbox) {
                checkbox.checked = false;
            }
        );

        if (selectAll) {
            selectAll.checked = false;
            selectAll.indeterminate = false;
        }

        refreshSelection();
    }

    if (selectAll) {
        selectAll.addEventListener(
            "change",
            function () {
                checkboxes.forEach(
                    function (checkbox) {
                        checkbox.checked =
                            selectAll.checked;
                    }
                );

                refreshSelection();
            }
        );
    }

    checkboxes.forEach(function (checkbox) {
        checkbox.addEventListener(
            "change",
            refreshSelection
        );
    });

    if (cancelButton) {
        cancelButton.addEventListener(
            "click",
            clearSelection
        );
    }

    deleteButton.addEventListener(
        "click",
        function (event) {
            event.preventDefault();
            event.stopPropagation();

            const selected =
                getSelectedItems();

            if (selected.length === 0) {
                return;
            }

            if (!idsContainer) {
                console.error(
                    "#bulkSubUnitIds tidak ditemukan."
                );

                return;
            }

            idsContainer.innerHTML = "";

            selected.forEach(
                function (checkbox) {
                    const input =
                        document.createElement(
                            "input"
                        );

                    input.type = "hidden";
                    input.name = "selected[]";
                    input.value =
                        checkbox.value;

                    idsContainer.appendChild(
                        input
                    );
                }
            );

            if (modalCount) {
                modalCount.textContent =
                    selected.length;
            }

            openSubUnitModal(
                "bulkDeleteSubUnitModal"
            );
        }
    );

    refreshSelection();
}

/*
|--------------------------------------------------------------------------
| IMPORT
|--------------------------------------------------------------------------
*/
function initializeImportSubUnit() {
    const fileInput = document.getElementById(
        "subUnitImportFile"
    );

    const fileName = document.getElementById(
        "subUnitImportFileName"
    );

    const importButton = document.getElementById(
        "btnImportSubUnit"
    );

    const importForm = document.getElementById(
        "importSubUnitForm"
    );

    if (
        !fileInput ||
        !fileName ||
        !importButton ||
        !importForm
    ) {
        return;
    }

    importButton.disabled = true;

    fileInput.addEventListener(
        "change",
        function () {
            const file =
                fileInput.files?.[0];

            if (!file) {
                fileName.textContent =
                    "Pilih File";

                importButton.disabled = true;

                return;
            }

            const extension =
                file.name
                    .split(".")
                    .pop()
                    .toLowerCase();

            const allowedExtensions = [
                "xlsx",
                "xls",
                "csv",
            ];

            if (
                !allowedExtensions.includes(
                    extension
                )
            ) {
                window.alert(
                    "File harus berformat XLSX, XLS, atau CSV."
                );

                fileInput.value = "";

                fileName.textContent =
                    "Pilih File";

                importButton.disabled = true;

                return;
            }

            fileName.textContent =
                file.name;

            importButton.disabled = false;
        }
    );

    importForm.addEventListener(
        "submit",
        function (event) {
            if (
                !fileInput.files ||
                fileInput.files.length === 0
            ) {
                event.preventDefault();

                importButton.disabled = true;

                window.alert(
                    "Pilih file Excel terlebih dahulu."
                );

                return;
            }

            importButton.disabled = true;

            importButton.innerHTML = `
                <i class="fa-solid fa-spinner fa-spin"></i>
                Mengimport...
            `;
        }
    );
}