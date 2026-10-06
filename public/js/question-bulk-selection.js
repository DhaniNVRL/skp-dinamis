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

                if (!editForm || !formId || !body || selected.length === 0) return;

                editForm.action = editButton.dataset.action || "";
                formId.value = editButton.dataset.formId || "";
                body.replaceChildren();

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

                    row.append(numberCell, headerCell, noCell, nameCell);
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
