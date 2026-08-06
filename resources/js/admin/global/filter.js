window.autoFilterTable = function ({
    table,
    searchInput = null,
    filters = {}
}) {

    const tableElement = document.querySelector(table);

    if (!tableElement) return;

    const rows = tableElement.querySelectorAll("tbody tr");

    const search = searchInput
        ? document.querySelector(searchInput)
        : null;

    function filterTable() {

        const keyword = search
            ? search.value.trim().toLowerCase()
            : "";

        rows.forEach(row => {

            let visible = true;

            // Search
            if (keyword) {

                const searchText =
                    (row.dataset.search || "").toLowerCase();

                visible = searchText.includes(keyword);

            }

            // Filter lainnya
            if (visible) {

                for (const [key, selector] of Object.entries(filters)) {

                    const element =
                        document.querySelector(selector);

                    if (!element || !element.value)
                        continue;

                    const rowValue =
                        (row.dataset[key] || "").toLowerCase();

                    if (rowValue !== element.value.toLowerCase()) {

                        visible = false;
                        break;

                    }

                }

            }

            row.style.display =
                visible ? "" : "none";

        });

    }

    search?.addEventListener("input", filterTable);

    Object.values(filters).forEach(selector => {

        document
            .querySelector(selector)
            ?.addEventListener("change", filterTable);

    });

    filterTable();

};