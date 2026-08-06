document.addEventListener("DOMContentLoaded", function () {
    initializeShowQuestion();
});

function initializeShowQuestion() {
    const page = document.getElementById(
        "showQuestionPage"
    );

    if (!page) {
        console.warn(
            "showQuestionPage tidak ditemukan."
        );

        return;
    }

    /*
    |--------------------------------------------------------------------------
    | EVENT DELEGATION
    |--------------------------------------------------------------------------
    */
    page.addEventListener(
        "change",
        function (event) {
            const target = event.target;

            /*
             * Kuesioner Umum:
             * radio atau checkbox dengan has_child.
             */
            if (
                target.matches(
                    "[data-general-option-input]"
                )
            ) {
                handleGeneralOptionChange(
                    target
                );
            }

            /*
             * Customer Assessment:
             * hanya radio KINERJA yang mengatur alasan.
             */
            if (
                target.matches(
                    'input[type="radio"][name^="performance_"]'
                )
            ) {
                const question = target.closest(
                    "[data-customer-question]"
                );

                if (question) {
                    updateCustomerReasonPanel(
                        question
                    );
                }
            }

            /*
             * Checkbox pilihan alasan.
             */
            if (
                target.matches(
                    "[data-reason-checkbox]"
                )
            ) {
                updateCustomerReasonChild(
                    target
                );
            }
        }
    );

    /*
     * Sinkronisasi kondisi awal.
     */
    synchronizeInitialState(page);
}

/*
|--------------------------------------------------------------------------
| INITIAL STATE
|--------------------------------------------------------------------------
*/
function synchronizeInitialState(page) {
    page
        .querySelectorAll(
            "[data-customer-question]"
        )
        .forEach(function (question) {
            updateCustomerReasonPanel(
                question
            );
        });

    page
        .querySelectorAll(
            "[data-reason-checkbox]"
        )
        .forEach(function (checkbox) {
            updateCustomerReasonChild(
                checkbox
            );
        });

    page
        .querySelectorAll(
            "[data-general-question]"
        )
        .forEach(function (question) {
            const questionType =
                question.dataset.generalQuestionType;

            if (questionType === "radio") {
                const checkedRadio =
                    question.querySelector(
                        'input[type="radio"][data-general-option-input]:checked'
                    );

                updateGeneralRadioQuestion(
                    question,
                    checkedRadio
                );
            }

            if (questionType === "checkbox") {
                question
                    .querySelectorAll(
                        'input[type="checkbox"][data-general-option-input]'
                    )
                    .forEach(function (checkbox) {
                        updateGeneralCheckboxOption(
                            checkbox
                        );
                    });
            }
        });
}

/*
|--------------------------------------------------------------------------
| CUSTOMER ASSESSMENT REASON
|--------------------------------------------------------------------------
*/
function updateCustomerReasonPanel(question) {
    const panel = question.querySelector(
        "[data-customer-reason-panel]"
    );

    if (!panel) {
        return;
    }

    /*
     * Hanya membaca nilai Kinerja.
     * Nilai Kepentingan tidak diproses.
     */
    const selectedPerformance =
        question.querySelector(
            'input[type="radio"][name^="performance_"]:checked'
        );

    const selectedScore =
        selectedPerformance
            ? Number(
                selectedPerformance.value
            )
            : null;

    const maximumScore = Number(
        question.dataset.reasonMaximumScore
    );

    /*
     * 1-5: maximumScore = 3.
     * 1-7: maximumScore = 4.
     * Nilai 0 tidak memenuhi selectedScore >= 1.
     */
    const shouldShow =
        selectedScore !== null &&
        Number.isFinite(selectedScore) &&
        selectedScore >= 1 &&
        selectedScore <= maximumScore;

    panel.classList.toggle(
        "hidden",
        !shouldShow
    );

    panel.setAttribute(
        "aria-hidden",
        shouldShow ? "false" : "true"
    );

    /*
     * Aktifkan textarea alasan atau checkbox alasan
     * ketika panel tampil.
     */
    panel
        .querySelectorAll(
            "[data-customer-reason-input]"
        )
        .forEach(function (input) {
            input.disabled = !shouldShow;

            if (!shouldShow) {
                clearInput(input);
            }
        });

    /*
     * Jika panel disembunyikan, reset semua child.
     */
    if (!shouldShow) {
        resetCustomerReasonChildren(
            panel
        );
    }
}

/*
|--------------------------------------------------------------------------
| CUSTOMER REASON CHILD
|--------------------------------------------------------------------------
*/
function updateCustomerReasonChild(checkbox) {
    const option = checkbox.closest(
        "[data-reason-option]"
    );

    if (!option) {
        return;
    }

    const child = option.querySelector(
        "[data-reason-child]"
    );

    if (!child) {
        return;
    }

    const shouldShow =
        checkbox.checked &&
        !checkbox.disabled;

    child.classList.toggle(
        "hidden",
        !shouldShow
    );

    child.setAttribute(
        "aria-hidden",
        shouldShow ? "false" : "true"
    );

    child
        .querySelectorAll(
            "[data-customer-reason-child-input]"
        )
        .forEach(function (input) {
            input.disabled = !shouldShow;

            if (!shouldShow) {
                clearInput(input);
            }
        });
}

function resetCustomerReasonChildren(panel) {
    panel
        .querySelectorAll(
            "[data-reason-child]"
        )
        .forEach(function (child) {
            child.classList.add(
                "hidden"
            );

            child.setAttribute(
                "aria-hidden",
                "true"
            );

            child
                .querySelectorAll(
                    "[data-customer-reason-child-input]"
                )
                .forEach(function (input) {
                    input.disabled = true;
                    clearInput(input);
                });
        });
}

/*
|--------------------------------------------------------------------------
| GENERAL QUESTIONNAIRE
|--------------------------------------------------------------------------
*/
function handleGeneralOptionChange(input) {
    const question = input.closest(
        "[data-general-question]"
    );

    if (!question) {
        return;
    }

    const questionType =
        question.dataset.generalQuestionType;

    if (questionType === "radio") {
        updateGeneralRadioQuestion(
            question,
            input
        );

        return;
    }

    if (questionType === "checkbox") {
        updateGeneralCheckboxOption(
            input
        );
    }
}

function updateGeneralRadioQuestion(
    question,
    selectedInput
) {
    question
        .querySelectorAll(
            "[data-general-option]"
        )
        .forEach(function (option) {
            const child = option.querySelector(
                "[data-general-child]"
            );

            if (!child) {
                return;
            }

            setGeneralChildVisibility(
                child,
                false
            );
        });

    if (
        !selectedInput ||
        selectedInput.dataset.hasChild !== "1"
    ) {
        return;
    }

    const option = selectedInput.closest(
        "[data-general-option]"
    );

    const child = option?.querySelector(
        "[data-general-child]"
    );

    if (child) {
        setGeneralChildVisibility(
            child,
            true
        );
    }
}

function updateGeneralCheckboxOption(input) {
    const option = input.closest(
        "[data-general-option]"
    );

    const child = option?.querySelector(
        "[data-general-child]"
    );

    if (!child) {
        return;
    }

    const shouldShow =
        input.checked &&
        input.dataset.hasChild === "1";

    setGeneralChildVisibility(
        child,
        shouldShow
    );
}

function setGeneralChildVisibility(
    child,
    shouldShow
) {
    child.classList.toggle(
        "hidden",
        !shouldShow
    );

    child.setAttribute(
        "aria-hidden",
        shouldShow ? "false" : "true"
    );

    child
        .querySelectorAll(
            "[data-general-child-input]"
        )
        .forEach(function (input) {
            input.disabled = !shouldShow;

            if (!shouldShow) {
                clearInput(input);
            }
        });
}

/*
|--------------------------------------------------------------------------
| HELPER
|--------------------------------------------------------------------------
*/
function clearInput(input) {
    if (
        input.type === "checkbox" ||
        input.type === "radio"
    ) {
        input.checked = false;

        return;
    }

    if (
        input.tagName === "SELECT"
    ) {
        input.selectedIndex = 0;

        return;
    }

    input.value = "";
}