(function () {
    "use strict";

    const initialize = () => {
        document.querySelectorAll("[data-question-bulk-container]").forEach((container) => {
            const selectAll = container.querySelector("[data-question-select-all]");
            const checkboxes = Array.from(container.querySelectorAll("[data-question-bulk-checkbox]"));
            const action = container.querySelector("[data-question-bulk-action]");
            const count = container.querySelector("[data-question-selected-count]");
            const form = container.querySelector("[data-question-bulk-form]");

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
