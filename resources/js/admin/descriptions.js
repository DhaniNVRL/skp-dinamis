document.addEventListener("DOMContentLoaded", function () {
    if (window.__descriptionEditorInitialized) {
        return;
    }

    window.__descriptionEditorInitialized = true;

    document
        .querySelectorAll("[data-editor-wrapper]")
        .forEach(function (wrapper) {
            const editor = wrapper.querySelector(
                "[data-description-editor]"
            );

            const hiddenInput = wrapper.querySelector(
                "[data-description-input]"
            );

            const formatSelect = wrapper.querySelector(
                "[data-description-format]"
            );

            const commandButtons =
                wrapper.querySelectorAll(
                    "[data-description-command]"
                );

            const tableButton = wrapper.querySelector(
                "[data-description-table]"
            );

            const tableSettings = wrapper.querySelector(
                "[data-table-settings]"
            );

            const rowInput = wrapper.querySelector(
                "[data-table-rows]"
            );

            const columnInput = wrapper.querySelector(
                "[data-table-columns]"
            );

            const borderInput = wrapper.querySelector(
                "[data-table-border]"
            );

            const applyButton = wrapper.querySelector(
                "[data-table-apply]"
            );

            const applyLabel = wrapper.querySelector(
                "[data-table-apply-label]"
            );

            const deleteButton = wrapper.querySelector(
                "[data-table-delete]"
            );

            const cancelButton = wrapper.querySelector(
                "[data-table-cancel]"
            );

            if (!editor || !hiddenInput) {
                return;
            }

            let savedRange = null;
            let activeTable = null;

            function syncContent() {
                /*
                 * Class selection tidak perlu disimpan.
                 */
                editor
                    .querySelectorAll(
                        ".description-table-selected"
                    )
                    .forEach(function (table) {
                        table.classList.remove(
                            "description-table-selected"
                        );
                    });

                hiddenInput.value =
                    editor.innerHTML.trim();

                if (activeTable) {
                    activeTable.classList.add(
                        "description-table-selected"
                    );
                }
            }

            function saveSelection() {
                const selection = window.getSelection();

                if (
                    !selection ||
                    selection.rangeCount === 0
                ) {
                    return;
                }

                const range = selection.getRangeAt(0);

                const node =
                    range.commonAncestorContainer;

                const element =
                    node.nodeType === Node.TEXT_NODE
                        ? node.parentElement
                        : node;

                if (
                    element &&
                    editor.contains(element)
                ) {
                    savedRange =
                        range.cloneRange();
                }
            }

            function restoreSelection() {
                editor.focus();

                if (!savedRange) {
                    return;
                }

                const selection = window.getSelection();

                selection.removeAllRanges();
                selection.addRange(savedRange);
            }

            function clearActiveTable() {
                editor
                    .querySelectorAll(
                        ".description-table-selected"
                    )
                    .forEach(function (table) {
                        table.classList.remove(
                            "description-table-selected"
                        );
                    });

                activeTable = null;
            }

            function selectTable(table) {
                clearActiveTable();

                activeTable = table;

                if (activeTable) {
                    activeTable.classList.add(
                        "description-table-selected"
                    );
                }
            }

            function getTableDimensions(table) {
                const rows =
                    table.querySelectorAll("tr");

                let columns = 0;

                rows.forEach(function (row) {
                    columns = Math.max(
                        columns,
                        row.children.length
                    );
                });

                return {
                    rows: rows.length,
                    columns: columns
                };
            }

            function openTableSettings(table = null) {
                activeTable = table;

                tableSettings?.classList.remove(
                    "hidden"
                );

                if (table) {
                    selectTable(table);

                    const dimensions =
                        getTableDimensions(table);

                    rowInput.value =
                        dimensions.rows || 1;

                    columnInput.value =
                        dimensions.columns || 1;

                    borderInput.value = String(
                        Number(
                            table.dataset.borderWidth
                            ?? 1
                        )
                    );

                    if (applyLabel) {
                        applyLabel.textContent =
                            "Perbarui Table";
                    }

                    deleteButton?.classList.remove(
                        "hidden"
                    );
                } else {
                    clearActiveTable();

                    rowInput.value = "2";
                    columnInput.value = "2";
                    borderInput.value = "1";

                    if (applyLabel) {
                        applyLabel.textContent =
                            "Buat Table";
                    }

                    deleteButton?.classList.add(
                        "hidden"
                    );
                }
            }

            function closeTableSettings() {
                tableSettings?.classList.add(
                    "hidden"
                );
            }

            function applyBorder(table, width) {
                const borderWidth =
                    Number(width);

                table.dataset.borderWidth =
                    String(borderWidth);

                table.style.setProperty(
                    "width",
                    "100%"
                );

                table.style.setProperty(
                    "border-collapse",
                    "collapse"
                );

                if (borderWidth === 0) {
                    table.style.setProperty(
                        "border",
                        "none",
                        "important"
                    );
                } else {
                    table.style.setProperty(
                        "border",
                        `${borderWidth}px solid #9ca3af`,
                        "important"
                    );
                }

                table
                    .querySelectorAll("td, th")
                    .forEach(function (cell) {
                        cell.style.setProperty(
                            "padding",
                            "10px"
                        );

                        if (borderWidth === 0) {
                            cell.style.setProperty(
                                "border",
                                "none",
                                "important"
                            );
                        } else {
                            cell.style.setProperty(
                                "border",
                                `${borderWidth}px solid #9ca3af`,
                                "important"
                            );
                        }
                    });
            }

            function createCell(text = "") {
                const cell =
                    document.createElement("td");

                cell.innerHTML =
                    text || "<br>";

                return cell;
            }

            function createTable(
                rowCount,
                columnCount
            ) {
                const table =
                    document.createElement("table");

                const tbody =
                    document.createElement("tbody");

                for (
                    let rowIndex = 0;
                    rowIndex < rowCount;
                    rowIndex++
                ) {
                    const row =
                        document.createElement("tr");

                    for (
                        let columnIndex = 0;
                        columnIndex < columnCount;
                        columnIndex++
                    ) {
                        row.appendChild(
                            createCell(
                                `Data ${
                                    (
                                        rowIndex *
                                        columnCount
                                    ) +
                                    columnIndex +
                                    1
                                }`
                            )
                        );
                    }

                    tbody.appendChild(row);
                }

                table.appendChild(tbody);

                return table;
            }

            function resizeTable(
                table,
                targetRows,
                targetColumns
            ) {
                let tbody =
                    table.querySelector("tbody");

                if (!tbody) {
                    tbody =
                        document.createElement("tbody");

                    const existingRows =
                        Array.from(
                            table.querySelectorAll(
                                ":scope > tr"
                            )
                        );

                    existingRows.forEach(
                        function (row) {
                            tbody.appendChild(row);
                        }
                    );

                    table.appendChild(tbody);
                }

                let rows = Array.from(
                    tbody.querySelectorAll(
                        ":scope > tr"
                    )
                );

                /*
                 * Tambah row.
                 */
                while (
                    rows.length < targetRows
                ) {
                    const row =
                        document.createElement("tr");

                    for (
                        let column = 0;
                        column < targetColumns;
                        column++
                    ) {
                        row.appendChild(
                            createCell()
                        );
                    }

                    tbody.appendChild(row);

                    rows = Array.from(
                        tbody.querySelectorAll(
                            ":scope > tr"
                        )
                    );
                }

                /*
                 * Kurangi row dari bawah.
                 */
                while (
                    rows.length > targetRows
                ) {
                    rows[
                        rows.length - 1
                    ].remove();

                    rows.pop();
                }

                /*
                 * Sesuaikan column setiap row.
                 */
                rows.forEach(function (row) {
                    let cells = Array.from(
                        row.children
                    );

                    while (
                        cells.length <
                        targetColumns
                    ) {
                        row.appendChild(
                            createCell()
                        );

                        cells = Array.from(
                            row.children
                        );
                    }

                    while (
                        cells.length >
                        targetColumns
                    ) {
                        cells[
                            cells.length - 1
                        ].remove();

                        cells.pop();
                    }
                });
            }

            function insertTableAtCursor(table) {
                restoreSelection();

                const selection = window.getSelection();

                if (
                    selection &&
                    selection.rangeCount > 0
                ) {
                    const range =
                        selection.getRangeAt(0);

                    range.deleteContents();
                    range.insertNode(table);

                    const paragraph =
                        document.createElement("p");

                    paragraph.innerHTML = "<br>";

                    table.after(paragraph);

                    range.setStart(
                        paragraph,
                        0
                    );

                    range.collapse(true);

                    selection.removeAllRanges();
                    selection.addRange(range);

                    savedRange =
                        range.cloneRange();
                } else {
                    editor.appendChild(table);

                    const paragraph =
                        document.createElement("p");

                    paragraph.innerHTML = "<br>";

                    editor.appendChild(paragraph);
                }
            }

            commandButtons.forEach(
                function (button) {
                    button.addEventListener(
                        "mousedown",
                        function (event) {
                            event.preventDefault();
                        }
                    );

                    button.addEventListener(
                        "click",
                        function () {
                            restoreSelection();

                            document.execCommand(
                                button.dataset
                                    .descriptionCommand,
                                false,
                                null
                            );

                            saveSelection();
                            syncContent();
                        }
                    );
                }
            );

            formatSelect?.addEventListener(
                "change",
                function () {
                    restoreSelection();

                    document.execCommand(
                        "formatBlock",
                        false,
                        formatSelect.value
                    );

                    saveSelection();
                    syncContent();
                }
            );

            /*
             * Tombol Table:
             * membuka create atau edit.
             */
            tableButton?.addEventListener(
                "mousedown",
                function (event) {
                    event.preventDefault();
                }
            );

            tableButton?.addEventListener(
                "click",
                function () {
                    openTableSettings(
                        activeTable
                    );
                }
            );

            /*
             * Klik cell memilih tabel untuk diedit.
             */
            editor.addEventListener(
                "click",
                function (event) {
                    const table =
                        event.target.closest(
                            "table"
                        );

                    if (table) {
                        selectTable(table);
                    } else {
                        clearActiveTable();
                    }

                    saveSelection();
                }
            );

            /*
             * Buat atau update tabel.
             */
            applyButton?.addEventListener(
                "click",
                function () {
                    const rows =
                        Number(rowInput.value);

                    const columns =
                        Number(columnInput.value);

                    const borderWidth =
                        Number(borderInput.value);

                    if (
                        !Number.isInteger(rows) ||
                        rows < 1 ||
                        rows > 20
                    ) {
                        alert(
                            "Jumlah row harus antara 1 sampai 20."
                        );

                        return;
                    }

                    if (
                        !Number.isInteger(columns) ||
                        columns < 1 ||
                        columns > 10
                    ) {
                        alert(
                            "Jumlah column harus antara 1 sampai 10."
                        );

                        return;
                    }

                    if (
                        ![0, 1, 2].includes(
                            borderWidth
                        )
                    ) {
                        alert(
                            "Ketebalan garis tidak valid."
                        );

                        return;
                    }

                    if (activeTable) {
                        resizeTable(
                            activeTable,
                            rows,
                            columns
                        );

                        applyBorder(
                            activeTable,
                            borderWidth
                        );
                    } else {
                        const table =
                            createTable(
                                rows,
                                columns
                            );

                        applyBorder(
                            table,
                            borderWidth
                        );

                        insertTableAtCursor(
                            table
                        );

                        selectTable(table);
                    }

                    syncContent();
                    closeTableSettings();
                }
            );

            /*
             * Hapus tabel aktif.
             */
            deleteButton?.addEventListener(
                "click",
                function () {
                    if (!activeTable) {
                        return;
                    }

                    if (
                        !window.confirm(
                            "Hapus tabel ini?"
                        )
                    ) {
                        return;
                    }

                    const tableToDelete =
                        activeTable;

                    activeTable = null;

                    tableToDelete.remove();

                    closeTableSettings();
                    syncContent();
                }
            );

            cancelButton?.addEventListener(
                "click",
                closeTableSettings
            );

            editor.addEventListener(
                "keyup",
                saveSelection
            );

            editor.addEventListener(
                "input",
                function () {
                    saveSelection();
                    syncContent();
                }
            );

            const form =
                wrapper.closest("form");

            form?.addEventListener(
                "submit",
                function () {
                    editor
                        .querySelectorAll(
                            ".description-table-selected"
                        )
                        .forEach(function (table) {
                            table.classList.remove(
                                "description-table-selected"
                            );
                        });

                    hiddenInput.value =
                        editor.innerHTML.trim();
                }
            );

            syncContent();
        });
});

document.addEventListener("DOMContentLoaded", function () {
    if (window.__createDescriptionInitialized) {
        return;
    }

    window.__createDescriptionInitialized = true;

    let createDescriptionTrigger = null;

    const createForm = document.getElementById(
        "createDescriptionForm"
    );

    const groupInput = document.getElementById(
        "create_description_group_id"
    );

    const formInput = document.getElementById(
        "create_description_form_id"
    );

    const editor = document.getElementById(
        "create_description_editor"
    );

    const contentInput = document.getElementById(
        "create_description_content"
    );

    /*
    |--------------------------------------------------------------------------
    | Simpan tombol pembuka
    |--------------------------------------------------------------------------
    */
    document.addEventListener(
        "click",
        function (event) {
            const button = event.target.closest(
                '[data-modal-open="createDescriptionModal"]'
            );

            if (!button) {
                return;
            }

            createDescriptionTrigger = button;

            /*
             * Isi langsung agar tidak bergantung pada urutan
             * event GlobalModal.
             */
            if (groupInput) {
                groupInput.value =
                    button.dataset.groupId || "";
            }

            if (formInput) {
                formInput.value =
                    button.dataset.formId || "";
            }

            if (editor) {
                editor.innerHTML = "";
            }

            if (contentInput) {
                contentInput.value = "";
            }
        },
        true
    );

    /*
    |--------------------------------------------------------------------------
    | Validasi sebelum submit
    |--------------------------------------------------------------------------
    */
    createForm?.addEventListener(
        "submit",
        function (event) {
            if (editor && contentInput) {
                /*
                 * Hilangkan class editor yang tidak perlu disimpan.
                 */
                editor
                    .querySelectorAll(
                        ".description-table-selected"
                    )
                    .forEach(function (table) {
                        table.classList.remove(
                            "description-table-selected"
                        );
                    });

                contentInput.value =
                    editor.innerHTML.trim();
            }

            if (!groupInput?.value) {
                event.preventDefault();

                alert(
                    "Group description tidak ditemukan."
                );

                return;
            }

            if (!formInput?.value) {
                event.preventDefault();

                alert(
                    "Form description tidak ditemukan."
                );

                return;
            }

            if (!contentInput?.value.trim()) {
                event.preventDefault();

                alert(
                    "Content description wajib diisi."
                );
            }
        }
    );

    /*
    |--------------------------------------------------------------------------
    | Reset ketika ditutup
    |--------------------------------------------------------------------------
    */
    document.addEventListener(
        "click",
        function (event) {
            const button = event.target.closest(
                '[data-modal-close="createDescriptionModal"]'
            );

            if (!button) {
                return;
            }

            createForm?.reset();

            if (editor) {
                editor.innerHTML = "";
            }

            if (contentInput) {
                contentInput.value = "";
            }

            createDescriptionTrigger = null;
        }
    );
});

document.addEventListener("DOMContentLoaded", function () {
    if (window.__editDescriptionInitialized) {
        return;
    }

    window.__editDescriptionInitialized = true;

    const editModal = document.getElementById(
        "editDescriptionModal"
    );

    const editForm = document.getElementById(
        "editDescriptionForm"
    );

    const editFormName = document.getElementById(
        "edit_description_form_name"
    );

    /*
     * ID ini harus sama persis dengan ID yang dikirim
     * pada include description-editor.
     */
    const editEditor = document.getElementById(
        "editDescriptionEditor"
    );

    const editContentInput = document.getElementById(
        "editDescriptionContent"
    );

    if (
        !editModal ||
        !editForm ||
        !editEditor ||
        !editContentInput
    ) {
        console.error(
            "Elemen modal edit description belum lengkap.",
            {
                editModal: !!editModal,
                editForm: !!editForm,
                editEditor: !!editEditor,
                editContentInput: !!editContentInput
            }
        );

        return;
    }

    /*
    |--------------------------------------------------------------------------
    | Bersihkan status table editor
    |--------------------------------------------------------------------------
    */
    function resetTableState() {
        editEditor
            .querySelectorAll(
                ".description-table-selected"
            )
            .forEach(function (table) {
                table.classList.remove(
                    "description-table-selected"
                );
            });

        const wrapper = editEditor.closest(
            "[data-editor-wrapper]"
        );

        const settings = wrapper?.querySelector(
            "[data-table-settings]"
        );

        const deleteButton = wrapper?.querySelector(
            "[data-table-delete]"
        );

        const applyLabel = wrapper?.querySelector(
            "[data-table-apply-label]"
        );

        settings?.classList.add("hidden");
        deleteButton?.classList.add("hidden");

        if (applyLabel) {
            applyLabel.textContent =
                "Buat Table";
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Isi data lama saat tombol edit diklik
    |--------------------------------------------------------------------------
    */
    document.addEventListener(
        "click",
        function (event) {
            const button = event.target.closest(
                '[data-modal-open="editDescriptionModal"]'
            );

            if (!button) {
                return;
            }

            event.preventDefault();

            const action =
                button.dataset.action || "";

            const templateId =
                button.dataset.contentTemplate || "";

            const contentTemplate =
                document.getElementById(templateId);

            const oldContent =
                contentTemplate?.innerHTML.trim()
                || "";

            if (!action) {
                console.error(
                    "Route update description tidak ditemukan."
                );

                return;
            }

            if (!contentTemplate) {
                console.error(
                    "Template content description tidak ditemukan.",
                    {
                        templateId: templateId
                    }
                );
            }

            /*
             * Isi action update.
             */
            editForm.action = action;

            /*
             * Isi nama form.
             */
            if (editFormName) {
                editFormName.textContent =
                    button.dataset.formName || "-";
            }

            /*
             * Isi data lama ke contenteditable.
             */
            editEditor.innerHTML =
                oldContent;

            /*
             * Isi juga hidden textarea.
             */
            editContentInput.value =
                oldContent;

            resetTableState();

            /*
             * Trigger input agar editor menyadari
             * bahwa content sudah berubah.
             */
            editEditor.dispatchEvent(
                new Event("input", {
                    bubbles: true
                })
            );
        },
        true
    );

    /*
    |--------------------------------------------------------------------------
    | Sinkronisasi sebelum submit
    |--------------------------------------------------------------------------
    */
    editForm.addEventListener(
        "submit",
        function (event) {
            /*
             * Jangan simpan class selection table.
             */
            editEditor
                .querySelectorAll(
                    ".description-table-selected"
                )
                .forEach(function (table) {
                    table.classList.remove(
                        "description-table-selected"
                    );
                });

            editContentInput.value =
                editEditor.innerHTML.trim();

            if (!editForm.getAttribute("action")) {
                event.preventDefault();

                alert(
                    "URL update description tidak ditemukan."
                );

                return;
            }

            if (!editContentInput.value) {
                event.preventDefault();

                alert(
                    "Content description wajib diisi."
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
                '[data-modal-close="editDescriptionModal"]'
            );

            if (!button) {
                return;
            }

            editForm.reset();
            editForm.removeAttribute("action");

            editEditor.innerHTML = "";
            editContentInput.value = "";

            if (editFormName) {
                editFormName.textContent = "";
            }

            resetTableState();
        }
    );
});

document.addEventListener("DOMContentLoaded", function () {
    if (window.__deleteDescriptionInitialized) {
        return;
    }

    window.__deleteDescriptionInitialized = true;

    const deleteForm = document.getElementById(
        "deleteDescriptionForm"
    );

    const deleteIdInput = document.getElementById(
        "delete_description_id"
    );

    const formNameElement = document.getElementById(
        "delete_description_form_name"
    );

    if (!deleteForm) {
        console.error(
            "#deleteDescriptionForm tidak ditemukan."
        );

        return;
    }

    /*
    |--------------------------------------------------------------------------
    | Isi modal delete
    |--------------------------------------------------------------------------
    */
    document.addEventListener(
        "click",
        function (event) {
            const button = event.target.closest(
                '[data-modal-open="deleteDescriptionModal"]'
            );

            if (!button) {
                return;
            }

            event.preventDefault();

            const action =
                button.dataset.action || "";

            const descriptionId =
                button.dataset.id || "";

            const formName =
                button.dataset.formName || "-";

            if (!action) {
                console.error(
                    "data-action delete description kosong.",
                    button.dataset
                );

                return;
            }

            /*
             * Arahkan DELETE ke /description/{id}.
             */
            deleteForm.setAttribute(
                "action",
                action
            );

            if (deleteIdInput) {
                deleteIdInput.value =
                    descriptionId;
            }

            if (formNameElement) {
                formNameElement.textContent =
                    formName;
            }

            console.log(
                "Delete description action:",
                deleteForm.getAttribute("action")
            );
        },
        true
    );

    /*
    |--------------------------------------------------------------------------
    | Validasi sebelum submit
    |--------------------------------------------------------------------------
    */
    deleteForm.addEventListener(
        "submit",
        function (event) {
            const action =
                deleteForm.getAttribute("action");

            if (!action) {
                event.preventDefault();

                alert(
                    "URL penghapusan description tidak ditemukan."
                );

                return;
            }

            console.log(
                "Submitting DELETE:",
                action
            );
        }
    );

    /*
    |--------------------------------------------------------------------------
    | Reset modal
    |--------------------------------------------------------------------------
    */
    document.addEventListener(
        "click",
        function (event) {
            const button = event.target.closest(
                '[data-modal-close="deleteDescriptionModal"]'
            );

            if (!button) {
                return;
            }

            deleteForm.reset();
            deleteForm.removeAttribute(
                "action"
            );

            if (deleteIdInput) {
                deleteIdInput.value = "";
            }

            if (formNameElement) {
                formNameElement.textContent = "";
            }
        }
    );
});