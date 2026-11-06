(function () {
    "use strict";

    const initialize = () => {
        document.querySelectorAll("[data-question-bulk-container]").forEach((container) => {
            const selectAll = container.querySelector("[data-question-select-all]");
            const checkboxes = Array.from(container.querySelectorAll("[data-question-bulk-checkbox]"));
            const action = container.querySelector("[data-question-bulk-action]");
            const count = container.querySelector("[data-question-selected-count]");
            const form = container.querySelector("[data-question-bulk-form]");
            const editButton = container.querySelector("[data-question-bulk-edit]");

            if (!selectAll || checkboxes.length === 0 || !form) return;

            const refresh = () => {
                const selected = checkboxes.filter((checkbox) => checkbox.checked).length;
                selectAll.checked = selected === checkboxes.length;
                selectAll.indeterminate = selected > 0 && selected < checkboxes.length;
                action?.classList.toggle("hidden", selected === 0);
                action?.classList.toggle("flex", selected > 0);
                if (count) count.textContent = String(selected);
            };

            selectAll.addEventListener("change", () => {
                checkboxes.forEach((checkbox) => checkbox.checked = selectAll.checked);
                refresh();
            });
            checkboxes.forEach((checkbox) => checkbox.addEventListener("change", refresh));

            editButton?.addEventListener("click", () => {
                const selected = checkboxes.filter((checkbox) => checkbox.checked);
                const editForm = document.getElementById("bulkEditQuestionForm");
                const formId = document.getElementById("bulkEditQuestionFormId");
                const body = document.getElementById("bulkEditQuestionBody");
                const typeColumn = document.querySelector("[data-bulk-question-type-column]");
                const labelColumn = document.querySelector("[data-bulk-question-label-column]");

                if (!editForm || !formId || !body || selected.length === 0) return;

                editForm.action = editButton.dataset.action || "";
                formId.value = editButton.dataset.formId || "";
                body.replaceChildren();

                const formTypeId = Number(editButton.dataset.formTypeId || 0);
                const customerFormTypes = [2, 3, 15, 16, 17, 19];
                const engagementFormTypes = [4, 5, 18];
                const isCustomerForm = customerFormTypes.includes(formTypeId);
                const isEngagementForm = engagementFormTypes.includes(formTypeId);
                const supportsTypeEdit = isCustomerForm || isEngagementForm;

                typeColumn?.classList.toggle("hidden", !supportsTypeEdit);
                labelColumn?.classList.toggle("hidden", !supportsTypeEdit);

                const optionTemplateId = isCustomerForm
                    ? (formTypeId === 2
                        ? "customerAssessment15TypeOptions"
                        : formTypeId === 15
                            ? "customerAssessment15WithoutZeroTypeOptions"
                            : formTypeId === 16
                                ? "customerAssessment17WithoutZeroTypeOptions"
                                : "customerAssessment17TypeOptions")
                    : (formTypeId === 4
                        ? "engagementAssessment15TypeOptions"
                        : "engagementAssessment17TypeOptions");

                const createTypeSelect = (index, checkbox) => {
                    const select = document.createElement("select");
                    select.name = `questions[${index}][questiontype_id]`;
                    select.required = true;
                    select.className = "w-full rounded-lg border border-gray-300 px-3 py-2 text-sm";

                    const template = document.getElementById(optionTemplateId);
                    if (template?.content) {
                        select.appendChild(template.content.cloneNode(true));
                    }

                    select.value = checkbox.dataset.questionTypeId || "";
                    return select;
                };

                const appendLabelField = (container, label, name, value, colorClass) => {
                    const fieldLabel = document.createElement("label");
                    fieldLabel.className = `block text-xs font-medium ${colorClass}`;
                    fieldLabel.textContent = label;

                    const input = document.createElement("input");
                    input.type = "text";
                    input.name = name;
                    input.value = value;
                    input.maxLength = 100;
                    input.className = "mt-1 w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-700";
                    fieldLabel.appendChild(input);
                    container.appendChild(fieldLabel);
                };

                selected.forEach((checkbox, index) => {
                    const row = document.createElement("tr");
                    row.className = "align-top";

                    const numberCell = document.createElement("td");
                    numberCell.className = "px-4 py-3 text-sm font-semibold text-gray-500";
                    numberCell.textContent = String(index + 1);

                    const headerCell = document.createElement("td");
                    headerCell.className = "px-4 py-3";
                    const headerInput = document.createElement("input");
                    headerInput.type = "text";
                    headerInput.name = `questions[${index}][no_header]`;
                    headerInput.value = checkbox.dataset.questionHeader || "";
                    headerInput.maxLength = 20;
                    headerInput.className = "w-full rounded-lg border border-gray-300 px-3 py-2 text-sm";
                    headerCell.appendChild(headerInput);

                    const noCell = document.createElement("td");
                    noCell.className = "px-4 py-3";
                    const idInput = document.createElement("input");
                    idInput.type = "hidden";
                    idInput.name = `questions[${index}][id]`;
                    idInput.value = checkbox.dataset.questionId || checkbox.value;
                    const noInput = document.createElement("input");
                    noInput.type = "text";
                    noInput.name = `questions[${index}][no]`;
                    noInput.value = checkbox.dataset.questionNo || "";
                    noInput.maxLength = 20;
                    noInput.required = true;
                    noInput.className = "w-full rounded-lg border border-gray-300 px-3 py-2 text-sm";
                    noCell.append(idInput, noInput);

                    const nameCell = document.createElement("td");
                    nameCell.className = "px-4 py-3";
                    const nameInput = document.createElement("textarea");
                    nameInput.name = `questions[${index}][name]`;
                    nameInput.value = checkbox.dataset.questionName || "";
                    nameInput.maxLength = 1000;
                    nameInput.required = true;
                    nameInput.rows = 3;
                    nameInput.className = "w-full resize-y rounded-lg border border-gray-300 px-3 py-2 text-sm";
                    nameCell.appendChild(nameInput);

                    if (supportsTypeEdit) {
                        const typeCell = document.createElement("td");
                        typeCell.className = "px-4 py-3";
                        const typeSelect = createTypeSelect(index, checkbox);
                        typeCell.appendChild(typeSelect);

                        const labelCell = document.createElement("td");
                        labelCell.className = "px-4 py-3";

                        const renderLabels = () => {
                            labelCell.replaceChildren();
                            const selectedType = Number(typeSelect.value || 0);

                            if (isCustomerForm && [2, 3, 4, 7, 8].includes(selectedType)) {
                                const grid = document.createElement("div");
                                grid.className = "grid min-w-72 grid-cols-1 gap-2";
                                appendLabelField(grid, "Label pertama", `questions[${index}][importance_label]`, checkbox.dataset.importanceLabel || "Kepentingan", "text-blue-800");
                                appendLabelField(grid, "Label kedua", `questions[${index}][performance_label]`, checkbox.dataset.performanceLabel || "Kinerja", "text-emerald-800");
                                labelCell.appendChild(grid);
                                return;
                            }

                            if (isEngagementForm && selectedType === 2) {
                                appendLabelField(labelCell, "Keterangan pilihan", `questions[${index}][engagement_label]`, checkbox.dataset.engagementLabel || "Penilaian Keterikatan", "text-blue-800");
                                return;
                            }

                            const empty = document.createElement("span");
                            empty.className = "text-sm text-gray-400";
                            empty.textContent = "Tidak menggunakan label penilaian";
                            labelCell.appendChild(empty);
                        };

                        typeSelect.addEventListener("change", renderLabels);
                        renderLabels();
                        row.append(numberCell, headerCell, noCell, nameCell, typeCell, labelCell);
                    } else {
                        row.append(numberCell, headerCell, noCell, nameCell);
                    }

                    body.appendChild(row);
                });
            });

            form.addEventListener("submit", (event) => {
                const selected = checkboxes.filter((checkbox) => checkbox.checked).length;
                if (selected === 0 || !window.confirm(`Hapus ${selected} pertanyaan yang dipilih?`)) {
                    event.preventDefault();
                }
            });

            refresh();
        });
    };

    if (document.readyState === "loading") {
        document.addEventListener("DOMContentLoaded", initialize, { once: true });
    } else {
        initialize();
    }
})();
