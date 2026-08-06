document.addEventListener("DOMContentLoaded", function () {
    initializeHideAndShow();
    initializeGeneralQuestionChildren();
    initializeCustomerAssessmentReasons();
    initializeRankingQuestions();
});

function initializeHideAndShow() {
    const page = document.getElementById(
        "hideShowPage"
    );

    if (!page) {
        return;
    }

    const toggleUrl =
        page.dataset.toggleUrl;

    const csrfToken =
        document.getElementById(
            "hideShowCsrfToken"
        )?.value;

    if (!toggleUrl || !csrfToken) {
        console.error(
            "URL toggle atau CSRF token Hide and Show tidak ditemukan."
        );

        return;
    }

    page.addEventListener(
        "click",
        async function (event) {
            const button = event.target.closest(
                "[data-hide-show-toggle]"
            );

            if (!button) {
                return;
            }

            event.preventDefault();

            if (button.disabled) {
                return;
            }

            const formId =
                Number(button.dataset.formId);

            const questionId =
                Number(button.dataset.questionId);

            let subunitIds = [];

            try {
                subunitIds = JSON.parse(
                    button.dataset.subunitIds || "[]"
                );
            } catch (error) {
                console.error(
                    "Data Sub Unit tidak valid.",
                    error
                );

                showHideShowNotification(
                    "Data Sub Unit tidak valid.",
                    "error"
                );

                return;
            }

            if (
                !formId ||
                !questionId ||
                subunitIds.length === 0
            ) {
                showHideShowNotification(
                    "Data pertanyaan belum lengkap.",
                    "error"
                );

                return;
            }

            const currentActive =
                button.dataset.active === "1";

            const nextActive =
                !currentActive;

            setToggleLoading(
                button,
                true
            );

            try {
                const response = await fetch(
                    toggleUrl,
                    {
                        method: "POST",
                        headers: {
                            "Content-Type":
                                "application/json",
                            "Accept":
                                "application/json",
                            "X-CSRF-TOKEN":
                                csrfToken,
                            "X-Requested-With":
                                "XMLHttpRequest",
                        },
                        body: JSON.stringify({
                            form_id: formId,
                            question_id:
                                questionId,
                            subunit_ids:
                                subunitIds,
                            is_active:
                                nextActive,
                        }),
                    }
                );

                const result =
                    await response.json();

                if (!response.ok) {
                    throw new Error(
                        getResponseError(
                            result
                        )
                    );
                }

                updateToggleState(
                    button,
                    Boolean(result.is_active)
                );

                showHideShowNotification(
                    result.message ||
                        "Status pertanyaan berhasil diperbarui.",
                    "success"
                );
            } catch (error) {
                console.error(error);

                showHideShowNotification(
                    error.message ||
                        "Status pertanyaan gagal diperbarui.",
                    "error"
                );
            } finally {
                setToggleLoading(
                    button,
                    false
                );
            }
        }
    );
}

function updateToggleState(
    button,
    isActive
) {
    const knob = button.querySelector(
        "[data-toggle-knob]"
    );

    const container =
        button.closest(
            ".flex.items-center.justify-between"
        );

    const status =
        container?.querySelector(
            "[data-toggle-status]"
        );

    button.dataset.active =
        isActive ? "1" : "0";

    button.setAttribute(
        "aria-pressed",
        isActive ? "true" : "false"
    );

    button.classList.toggle(
        "bg-green-500",
        isActive
    );

    button.classList.toggle(
        "bg-gray-300",
        !isActive
    );

    if (knob) {
        knob.classList.toggle(
            "translate-x-6",
            isActive
        );

        knob.classList.toggle(
            "translate-x-1",
            !isActive
        );
    }

    if (status) {
        status.textContent =
            isActive
                ? "Ditampilkan"
                : "Disembunyikan";

        status.classList.toggle(
            "text-green-600",
            isActive
        );

        status.classList.toggle(
            "text-red-500",
            !isActive
        );
    }
}

function setToggleLoading(
    button,
    isLoading
) {
    button.disabled = isLoading;

    button.classList.toggle(
        "cursor-wait",
        isLoading
    );

    button.classList.toggle(
        "opacity-60",
        isLoading
    );
}

function getResponseError(result) {
    if (result?.message) {
        return result.message;
    }

    if (result?.errors) {
        const messages =
            Object.values(
                result.errors
            ).flat();

        if (messages.length > 0) {
            return messages[0];
        }
    }

    return "Terjadi kesalahan saat memperbarui pertanyaan.";
}

function showHideShowNotification(
    message,
    type
) {
    const notification =
        document.getElementById(
            "hideShowNotification"
        );

    if (!notification) {
        return;
    }

    notification.textContent =
        message;

    notification.classList.remove(
        "hidden",
        "bg-green-600",
        "bg-red-600"
    );

    notification.classList.add(
        type === "success"
            ? "bg-green-600"
            : "bg-red-600"
    );

    window.clearTimeout(
        notification.hideTimer
    );

    notification.hideTimer =
        window.setTimeout(
            function () {
                notification.classList.add(
                    "hidden"
                );
            },
            3000
        );
}

document.addEventListener("change", function (event) {
    const checkbox = event.target.closest(
        "[data-reason-checkbox]"
    );

    if (!checkbox) {
        return;
    }

    const container = checkbox.closest(
        "[data-reason-option]"
    );

    const child = container?.querySelector(
        "[data-reason-child]"
    );

    if (!child) {
        return;
    }

    const textarea = child.querySelector(
        "[data-customer-reason-child-input]"
    );

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

    if (textarea) {
        textarea.disabled =
            !shouldShow;

        if (!shouldShow) {
            textarea.value = "";
        }
    }
});

document.addEventListener("DOMContentLoaded", function () {
    initializeGeneralQuestionChildren();
});

function initializeGeneralQuestionChildren() {
    const questionnaires =
        document.querySelectorAll(
            "[data-general-questionnaire]"
        );

    if (questionnaires.length === 0) {
        return;
    }

    questionnaires.forEach(function (questionnaire) {
        questionnaire.addEventListener(
            "change",
            function (event) {
                const input = event.target.closest(
                    "[data-general-option-input]"
                );

                if (!input) {
                    return;
                }

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
        );

        /*
         * Sinkronisasi kondisi awal.
         * Berguna jika browser mempertahankan checked setelah refresh.
         */
        questionnaire
            .querySelectorAll(
                "[data-general-question]"
            )
            .forEach(function (question) {
                const questionType =
                    question.dataset.generalQuestionType;

                if (questionType === "radio") {
                    const checkedInput =
                        question.querySelector(
                            'input[type="radio"][data-general-option-input]:checked'
                        );

                    updateGeneralRadioQuestion(
                        question,
                        checkedInput
                    );
                }

                if (questionType === "checkbox") {
                    question
                        .querySelectorAll(
                            'input[type="checkbox"][data-general-option-input]'
                        )
                        .forEach(
                            updateGeneralCheckboxOption
                        );
                }
            });
    });
}

function updateGeneralRadioQuestion(
    question,
    selectedInput
) {
    /*
     * Sembunyikan semua child dalam satu pertanyaan radio.
     */
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

    if (!selectedInput) {
        return;
    }

    if (
        selectedInput.dataset.hasChild !== "1"
    ) {
        return;
    }

    const selectedOption =
        selectedInput.closest(
            "[data-general-option]"
        );

    const selectedChild =
        selectedOption?.querySelector(
            "[data-general-child]"
        );

    if (selectedChild) {
        setGeneralChildVisibility(
            selectedChild,
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

    child
        .querySelectorAll(
            "[data-general-child-input]"
        )
        .forEach(function (input) {
            input.disabled = !shouldShow;

            if (!shouldShow) {
                input.value = "";
            }
        });
}

/*
|--------------------------------------------------------------------------
| CUSTOMER ASSESSMENT REASONS
|--------------------------------------------------------------------------
*/
function initializeCustomerAssessmentReasons() {
    const questions = document.querySelectorAll(
        "[data-customer-question]"
    );

    if (questions.length === 0) {
        return;
    }

    questions.forEach(function (question) {
        /*
         * Hanya mendengarkan nilai Kinerja.
         * Kepentingan tidak memengaruhi alasan.
         */
        const performanceInputs =
            question.querySelectorAll(
                'input[type="radio"][name^="performance_"]'
            );

        performanceInputs.forEach(function (input) {
            input.addEventListener(
                "change",
                function () {
                    updateCustomerReasonPanel(
                        question
                    );
                }
            );
        });

        /*
         * Sinkronisasi jika browser mempertahankan
         * pilihan radio setelah reload.
         */
        updateCustomerReasonPanel(question);
    });
}

function updateCustomerReasonPanel(question) {
    const panel = question.querySelector(
        "[data-customer-reason-panel]"
    );

    if (!panel) {
        return;
    }

    /*
     * Ambil pilihan Kinerja saja.
     */
    const selectedPerformance =
        question.querySelector(
            'input[type="radio"][name^="performance_"]:checked'
        );

    const selectedScore =
        selectedPerformance
            ? Number(selectedPerformance.value)
            : null;

    const maximumScore = Number(
        question.dataset.reasonMaximumScore
    );

    /*
     * Skala 1-5:
     * maximumScore = 3
     *
     * Skala 1-7:
     * maximumScore = 4
     *
     * Nilai 0 tidak menampilkan alasan.
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
     * Aktifkan atau nonaktifkan input alasan.
     */
    panel
        .querySelectorAll(
            "[data-customer-reason-input]"
        )
        .forEach(function (input) {
            input.disabled = !shouldShow;

            if (!shouldShow) {
                clearCustomerInput(input);
            }
        });

    if (!shouldShow) {
        resetCustomerReasonChildren(
            panel
        );
    }
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
                    "input, textarea, select"
                )
                .forEach(function (input) {
                    input.disabled = true;
                    clearCustomerInput(input);
                });
        });
}

function clearCustomerInput(input) {
    if (
        input.type === "checkbox" ||
        input.type === "radio"
    ) {
        input.checked = false;

        return;
    }

    if (input.tagName === "SELECT") {
        input.selectedIndex = 0;

        return;
    }

    input.value = "";
}

/*
|--------------------------------------------------------------------------
| RANKING QUESTIONS
|--------------------------------------------------------------------------
*/
function initializeRankingQuestions() {
    const rankingQuestions =
        document.querySelectorAll(
            "[data-ranking-question]"
        );

    if (rankingQuestions.length === 0) {
        return;
    }

    rankingQuestions.forEach(function (question) {
        const selects = question.querySelectorAll(
            "[data-ranking-select]"
        );

        selects.forEach(function (select) {
            select.addEventListener(
                "change",
                function () {
                    updateRankingChild(
                        select
                    );

                    refreshRankingOptions(
                        question
                    );
                }
            );

            /*
             * Sinkronisasi textarea awal.
             */
            updateRankingChild(select);
        });

        /*
         * Sinkronisasi opsi awal.
         */
        refreshRankingOptions(question);
    });
}

/*
|--------------------------------------------------------------------------
| RANKING CHILD TEXTAREA
|--------------------------------------------------------------------------
*/
function updateRankingChild(select) {
    const block = select.closest(
        "[data-ranking-block]"
    );

    if (!block) {
        return;
    }

    const child = block.querySelector(
        "[data-ranking-child]"
    );

    const childInput = block.querySelector(
        "[data-ranking-child-input]"
    );

    if (!child || !childInput) {
        return;
    }

    const selectedOption =
        select.options[
            select.selectedIndex
        ];

    const hasChild =
        selectedOption?.dataset
            ?.hasChild === "1";

    child.classList.toggle(
        "hidden",
        !hasChild
    );

    child.setAttribute(
        "aria-hidden",
        hasChild ? "false" : "true"
    );

    childInput.disabled = !hasChild;

    if (!hasChild) {
        childInput.value = "";
    }
}

/*
|--------------------------------------------------------------------------
| UNIQUE RANKING OPTIONS
|--------------------------------------------------------------------------
|
| has_child = 0:
| hanya dapat dipilih satu kali.
|
| has_child = 1:
| boleh dipilih berkali-kali.
|
*/
function refreshRankingOptions(question) {
    const selects = Array.from(
        question.querySelectorAll(
            "[data-ranking-select]"
        )
    );

    /*
     * Simpan opsi tanpa child yang sudah dipilih.
     */
    const selectedUniqueValues =
        selects
            .map(function (select) {
                const selectedOption =
                    select.options[
                        select.selectedIndex
                    ];

                if (
                    !selectedOption ||
                    !selectedOption.value
                ) {
                    return null;
                }

                if (
                    selectedOption.dataset
                        .hasChild === "1"
                ) {
                    /*
                     * Opsi has_child boleh dipilih ulang.
                     */
                    return null;
                }

                return selectedOption.value;
            })
            .filter(function (value) {
                return value !== null;
            });

    selects.forEach(function (select) {
        const currentValue =
            select.value;

        Array.from(
            select.options
        ).forEach(function (option) {
            /*
             * Placeholder selalu aktif.
             */
            if (!option.value) {
                option.disabled = false;

                return;
            }

            /*
             * Opsi has_child selalu boleh dipilih
             * pada ranking lain.
             */
            if (
                option.dataset.hasChild === "1"
            ) {
                option.disabled = false;

                return;
            }

            /*
             * Opsi tanpa child:
             * disable jika sudah dipilih pada
             * ranking lain.
             */
            const selectedElsewhere =
                selectedUniqueValues.includes(
                    option.value
                ) &&
                option.value !== currentValue;

            option.disabled =
                selectedElsewhere;
        });
    });
}

