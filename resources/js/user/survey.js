document.addEventListener(
    "DOMContentLoaded",
    function () {
        const page = document.getElementById(
            "surveyPage"
        );

        const form = document.getElementById(
            "surveyAnswerForm"
        );

        if (!page || !form) {
            return;
        }

        initializeChildOptions(page);
        initializeCustomerAssessment(page);
        initializeSurveyValidation(form);
        initializeRankingAssessment(page);
    }
);

/*
|--------------------------------------------------------------------------
| CHILD OPTION
|--------------------------------------------------------------------------
*/
function initializeChildOptions(page) {
    page.addEventListener(
        "change",
        function (event) {
            const input = event.target.closest(
                "[data-option-input]"
            );

            if (!input) {
                return;
            }

            if (input.type === "radio") {
                refreshRadioChildren(input);
            }

            if (input.type === "checkbox") {
                refreshCheckboxChild(input);
            }
        }
    );

    page.querySelectorAll(
        "[data-option-input]"
    ).forEach(function (input) {
        if (input.type === "radio") {
            refreshRadioChildren(input);
        }

        if (input.type === "checkbox") {
            refreshCheckboxChild(input);
        }
    });
}

function refreshRadioChildren(input) {
    const group = input.closest(
        "[data-option-group]"
    );

    if (!group) {
        return;
    }

    group.querySelectorAll(
        '[data-option-input][type="radio"]'
    ).forEach(function (radio) {
        const targetId =
            radio.dataset.childTarget;

        if (!targetId) {
            return;
        }

        const child =
            document.getElementById(
                targetId
            );

        if (!child) {
            return;
        }

        toggleChildContainer(
            child,
            radio.checked &&
            radio.dataset.hasChild === "1"
        );
    });
}

function refreshCheckboxChild(input) {
    const targetId =
        input.dataset.childTarget;

    if (!targetId) {
        return;
    }

    const child =
        document.getElementById(targetId);

    if (!child) {
        return;
    }

    toggleChildContainer(
        child,
        input.checked &&
        input.dataset.hasChild === "1"
    );
}

function toggleChildContainer(
    child,
    shouldShow
) {
    child.classList.toggle(
        "hidden",
        !shouldShow
    );

    const input = child.querySelector(
        "[data-child-input]"
    );

    if (!input) {
        return;
    }

    input.disabled = !shouldShow;
    input.required = shouldShow;

    if (!shouldShow) {
        input.value = "";
    }
}

/*
|--------------------------------------------------------------------------
| CUSTOMER ASSESSMENT
|--------------------------------------------------------------------------
*/
function initializeCustomerAssessment(
    page
) {
    page.querySelectorAll(
        "[data-customer-assessment]"
    ).forEach(refreshCustomerReason);

    page.addEventListener(
        "change",
        function (event) {
            const input = event.target.closest(
                "[data-performance-input]"
            );

            if (!input) {
                return;
            }

            const container = input.closest(
                "[data-customer-assessment]"
            );

            refreshCustomerReason(container);

            if (
                validateQuestionContainer(
                    container
                ).valid
            ) {
                clearQuestionValidation(
                    container
                );
            }
        }
    );
}

function refreshCustomerReason(
    container
) {
    if (!container) {
        return;
    }

    const questionType = Number(
        container.dataset.questionType
    );

    if (![3, 4].includes(questionType)) {
        return;
    }

    const checkedPerformance =
        container.querySelector(
            "[data-performance-input]:checked"
        );

    const reasonContainer =
        container.querySelector(
            "[data-performance-reason]"
        );

    if (!reasonContainer) {
        return;
    }

    const reasonMaximum = Number(
        container.dataset.reasonMaximum
    );

    const performanceValue =
        checkedPerformance
            ? Number(
                checkedPerformance.value
            )
            : null;

    const shouldShow =
        performanceValue !== null &&
        performanceValue !== 0 &&
        performanceValue <= reasonMaximum;

    reasonContainer.classList.toggle(
        "hidden",
        !shouldShow
    );

    const reasonInput =
        reasonContainer.querySelector(
            "[data-performance-reason-input]"
        );

    if (reasonInput) {
        reasonInput.disabled =
            !shouldShow;

        reasonInput.required =
            shouldShow;

        if (!shouldShow) {
            reasonInput.value = "";
        }
    }

    const checkboxGroup =
        reasonContainer.querySelector(
            "[data-reason-checkbox-group]"
        );

    if (!checkboxGroup) {
        return;
    }

    if (shouldShow) {
        checkboxGroup.setAttribute(
            "data-required-group",
            ""
        );
    } else {
        checkboxGroup.removeAttribute(
            "data-required-group"
        );
    }

    checkboxGroup
        .querySelectorAll(
            'input[type="checkbox"]'
        )
        .forEach(function (checkbox) {
            checkbox.disabled =
                !shouldShow;

            if (!shouldShow) {
                checkbox.checked = false;

                const targetId =
                    checkbox.dataset
                        .childTarget;

                if (!targetId) {
                    return;
                }

                const child =
                    document.getElementById(
                        targetId
                    );

                if (child) {
                    toggleChildContainer(
                        child,
                        false
                    );
                }
            }
        });
}

/*
|--------------------------------------------------------------------------
| GLOBAL VALIDATION
|--------------------------------------------------------------------------
*/
function initializeSurveyValidation(
    form
) {
    form.addEventListener(
        "submit",
        function (event) {
            clearAllValidation(form);

            const invalidQuestions = [];

            form.querySelectorAll(
                "[data-question-container]"
            ).forEach(function (container) {
                const result =
                    validateQuestionContainer(
                        container
                    );

                if (!result.valid) {
                    markQuestionInvalid(
                        container,
                        result.message
                    );

                    invalidQuestions.push({
                        container:
                            container,

                        field:
                            result.field,
                    });
                }
            });

            if (
                invalidQuestions.length === 0
            ) {
                return;
            }

            event.preventDefault();
            event.stopPropagation();

            const first =
                invalidQuestions[0];

            first.container.scrollIntoView({
                behavior: "smooth",
                block: "center",
            });

            window.setTimeout(
                function () {
                    first.field?.focus();
                },
                350
            );
        }
    );

    form.addEventListener(
        "input",
        function (event) {
            refreshLiveValidation(
                event.target
            );
        }
    );

    form.addEventListener(
        "change",
        function (event) {
            refreshLiveValidation(
                event.target
            );
        }
    );
}

function validateQuestionContainer(
    container
) {
    if (!container) {
        return {
            valid: true,
        };
    }

    const radioNames = new Set();

    container.querySelectorAll(
        'input[type="radio"][required]:not(:disabled)'
    ).forEach(function (radio) {
        radioNames.add(radio.name);
    });

    for (const name of radioNames) {
        const radios = Array.from(
            container.querySelectorAll(
                'input[type="radio"]'
            )
        ).filter(function (radio) {
            return (
                radio.name === name &&
                !radio.disabled
            );
        });

        if (
            !radios.some(
                function (radio) {
                    return radio.checked;
                }
            )
        ) {
            return {
                valid: false,
                field: radios[0],
                message:
                    "Pilih salah satu nilai.",
            };
        }
    }

    const checkboxGroups =
        container.querySelectorAll(
            "[data-required-group]"
        );

    for (const group of checkboxGroups) {
        if (
            group.closest(".hidden")
        ) {
            continue;
        }

        const checkboxes = Array.from(
            group.querySelectorAll(
                'input[type="checkbox"]:not(:disabled)'
            )
        );

        if (
            checkboxes.length > 0 &&
            !checkboxes.some(
                function (checkbox) {
                    return checkbox.checked;
                }
            )
        ) {
            return {
                valid: false,
                field: checkboxes[0],
                message:
                    "Pilih minimal satu alasan.",
            };
        }
    }

    const requiredFields =
        container.querySelectorAll(
            "input[required]:not([type='radio']):not([type='checkbox']):not(:disabled), " +
            "textarea[required]:not(:disabled), " +
            "select[required]:not(:disabled)"
        );

    for (const field of requiredFields) {
        if (!field.value.trim()) {
            return {
                valid: false,
                field: field,
                message:
                    "Pertanyaan ini wajib diisi.",
            };
        }

        if (!field.checkValidity()) {
            return {
                valid: false,
                field: field,
                message:
                    "Format jawaban tidak valid.",
            };
        }
    }

    return {
        valid: true,
    };
}

function markQuestionInvalid(
    container,
    message
) {
    container.classList.remove(
        "border-gray-200"
    );

    container.classList.add(
        "border-red-500",
        "ring-2",
        "ring-red-100"
    );

    const error =
        container.querySelector(
            "[data-question-error]"
        );

    if (error) {
        error.textContent = message;
        error.classList.remove("hidden");
    }
}

function clearQuestionValidation(
    container
) {
    container.classList.remove(
        "border-red-500",
        "ring-2",
        "ring-red-100"
    );

    container.classList.add(
        "border-gray-200"
    );

    container
        .querySelector(
            "[data-question-error]"
        )
        ?.classList.add("hidden");
}

function clearAllValidation(form) {
    form.querySelectorAll(
        "[data-question-container]"
    ).forEach(
        clearQuestionValidation
    );
}

function refreshLiveValidation(field) {
    const container = field.closest(
        "[data-question-container]"
    );

    if (!container) {
        return;
    }

    if (
        validateQuestionContainer(
            container
        ).valid
    ) {
        clearQuestionValidation(
            container
        );
    }
}

/*
|--------------------------------------------------------------------------
| RANKING ASSESSMENT
|--------------------------------------------------------------------------
*/
function initializeRankingAssessment(page) {
    page.querySelectorAll(
        "[data-ranking-container]"
    ).forEach(function (container) {
        refreshRankingContainer(
            container
        );
    });

    page.addEventListener(
        "change",
        function (event) {
            const select = event.target.closest(
                "[data-ranking-select]"
            );

            if (!select) {
                return;
            }

            const container = select.closest(
                "[data-ranking-container]"
            );

            refreshRankingChild(select);
            refreshRankingOptions(container);

            if (
                validateQuestionContainer(
                    container
                ).valid
            ) {
                clearQuestionValidation(
                    container
                );
            }
        }
    );
}

function refreshRankingContainer(
    container
) {
    if (!container) {
        return;
    }

    container.querySelectorAll(
        "[data-ranking-select]"
    ).forEach(function (select) {
        refreshRankingChild(select);
    });

    refreshRankingOptions(container);
}

/*
|--------------------------------------------------------------------------
| RANKING CHILD
|--------------------------------------------------------------------------
*/
function refreshRankingChild(select) {
    const row = select.closest(
        "[data-ranking-row]"
    );

    if (!row) {
        return;
    }

    const childContainer =
        row.querySelector(
            "[data-ranking-child]"
        );

    const childInput =
        row.querySelector(
            "[data-ranking-child-input]"
        );

    const childLabel =
        row.querySelector(
            "[data-ranking-child-label]"
        );

    if (
        !childContainer ||
        !childInput
    ) {
        return;
    }

    const selectedOption =
        select.options[
            select.selectedIndex
        ];

    const hasChild =
        selectedOption &&
        selectedOption.value !== "" &&
        selectedOption.dataset
            .hasChild === "1";

    const childPlaceholder =
        selectedOption?.dataset
            ?.answerText2?.trim() ||
        "Tulis jawaban tambahan...";

    childContainer.classList.toggle(
        "hidden",
        !hasChild
    );

    childInput.disabled = !hasChild;
    childInput.required = hasChild;
    childInput.placeholder =
        childPlaceholder;

    if (!hasChild) {
        childInput.value = "";
    }

    if (
        hasChild &&
        childLabel &&
        selectedOption.textContent
    ) {
        childLabel.textContent =
            childPlaceholder;
    }
}

/*
|--------------------------------------------------------------------------
| PREVENT DUPLICATE RANKING
|--------------------------------------------------------------------------
*/
function refreshRankingOptions(
    container
) {
    if (!container) {
        return;
    }

    const selects = Array.from(
        container.querySelectorAll(
            "[data-ranking-select]"
        )
    );

    /*
     * Kumpulkan pilihan yang tidak
     * memiliki has_child.
     */
    const selectedNonRepeatable = [];

    selects.forEach(function (select) {
        const selectedOption =
            select.options[
                select.selectedIndex
            ];

        if (
            !selectedOption ||
            selectedOption.value === ""
        ) {
            return;
        }

        if (
            selectedOption.dataset
                .hasChild !== "1"
        ) {
            selectedNonRepeatable.push({
                value:
                    selectedOption.value,

                select:
                    select,
            });
        }
    });

    /*
     * Disable pilihan pada dropdown lain.
     */
    selects.forEach(function (select) {
        Array.from(
            select.options
        ).forEach(function (option) {
            if (option.value === "") {
                option.disabled = false;
                return;
            }

            /*
             * Option has_child boleh
             * dipilih berkali-kali.
             */
            if (
                option.dataset
                    .hasChild === "1"
            ) {
                option.disabled = false;
                return;
            }

            const usedByOtherSelect =
                selectedNonRepeatable.some(
                    function (item) {
                        return (
                            item.value ===
                                option.value &&
                            item.select !==
                                select
                        );
                    }
                );

            option.disabled =
                usedByOtherSelect;
        });
    });

    
}
