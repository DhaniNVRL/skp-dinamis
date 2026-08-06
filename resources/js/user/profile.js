document.addEventListener(
    "DOMContentLoaded",
    function () {
        initializeProfileAlerts();
        initializeProfileUnitDropdown();
    }
);

function initializeProfileAlerts() {
    document.addEventListener(
        "click",
        function (event) {
            const button = event.target.closest(
                "[data-alert-close]"
            );

            if (!button) {
                return;
            }

            const alert = button.closest(
                "[data-alert]"
            );

            if (alert) {
                alert.remove();
            }
        }
    );
}

function initializeProfileUnitDropdown() {
    const page = document.getElementById(
        "respondentProfileEditPage"
    );

    if (!page) {
        return;
    }

    const groupSelect = document.getElementById(
        "group_id"
    );

    const unitSelect = document.getElementById(
        "unit_id"
    );

    const groupLabel = document.getElementById(
        "groupLabel"
    );

    const unitLabel = document.getElementById(
        "unitLabel"
    );

    const loadingMessage = document.getElementById(
        "unitLoadingMessage"
    );

    const errorMessage = document.getElementById(
        "unitErrorMessage"
    );

    const saveButton = document.getElementById(
        "saveProfileButton"
    );

    if (!groupSelect || !unitSelect) {
        return;
    }

    const baseUrl = page.dataset.unitsUrl;

    groupSelect.addEventListener(
        "change",
        function () {
            loadUnits(groupSelect.value);
        }
    );

    async function loadUnits(groupId) {
        resetUnitSelect();
        hideError();

        if (!groupId) {
            unitSelect.disabled = true;
            return;
        }

        setLoading(true);

        try {
            const response = await fetch(
                `${baseUrl}/${groupId}/units`,
                {
                    method: "GET",

                    headers: {
                        Accept: "application/json",

                        "X-Requested-With":
                            "XMLHttpRequest",
                    },
                }
            );

            const result = await response.json();

            if (!response.ok || !result.success) {
                throw new Error(
                    result.message ||
                    "Gagal mengambil data unit."
                );
            }

            const units =
                result.data?.units ?? [];

            const labels =
                result.data?.labels ?? {};

            updateLabels(labels);
            renderUnits(units);
        } catch (error) {
            console.error(error);

            resetUnitSelect(
                "Gagal memuat data unit"
            );

            showError(
                error.message ||
                "Gagal memuat data unit."
            );
        } finally {
            setLoading(false);
        }
    }

    function resetUnitSelect(
        label = "Pilih unit atau jabatan"
    ) {
        unitSelect.innerHTML = "";

        const option =
            document.createElement("option");

        option.value = "";
        option.textContent = label;

        unitSelect.appendChild(option);
    }

    function renderUnits(units) {
        resetUnitSelect();

        if (units.length === 0) {
            resetUnitSelect(
                "Tidak ada unit pada bidang kerja ini"
            );

            unitSelect.disabled = true;

            showError(
                "Belum ada unit pada bidang kerja yang dipilih."
            );

            return;
        }

        units.forEach(function (unit) {
            const option =
                document.createElement("option");

            option.value = unit.id;
            option.textContent = unit.name;

            unitSelect.appendChild(option);
        });

        unitSelect.disabled = false;
    }

    function updateLabels(labels) {
        if (groupLabel && labels.group) {
            groupLabel.textContent =
                labels.group;
        }

        if (unitLabel && labels.unit) {
            unitLabel.textContent =
                labels.unit;
        }
    }

    function setLoading(isLoading) {
        if (isLoading) {
            resetUnitSelect("Memuat unit...");
        }

        unitSelect.disabled = isLoading;

        if (saveButton) {
            saveButton.disabled = isLoading;
        }

        if (loadingMessage) {
            loadingMessage.classList.toggle(
                "hidden",
                !isLoading
            );
        }
    }

    function showError(message) {
        if (!errorMessage) {
            return;
        }

        errorMessage.textContent = message;
        errorMessage.classList.remove("hidden");
    }

    function hideError() {
        if (!errorMessage) {
            return;
        }

        errorMessage.textContent = "";
        errorMessage.classList.add("hidden");
    }
}