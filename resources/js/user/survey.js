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
        initializeConditionalQuestions(page);
        initializeCustomerAssessment(page);
        initializeSurveyValidation(form);
        initializeServerValidation(form);
        initializeRankingAssessment(page);
        initializeSurveyorAutofill(form);
    }
);


/*
|--------------------------------------------------------------------------
| MEANINGFUL ANSWER VALIDATION
|--------------------------------------------------------------------------
|
| Field yang menggunakan:
|
| data-meaningful-answer
|
| yaitu:
|
| - Keunggulan
| - Keluhan
| - Saran
| - Child Answer
|
*/
const MEANINGLESS_ANSWERS = [
    "tidak ada",
    "tidak ada saran",
    "tidak ada keluhan",
    "tidak ada keunggulan",
    "tidak ada masukan",
    "tidak ada komentar",
    "tidak ada jawaban",

    "ga ada",
    "gak ada",
    "nggak ada",
    "ngga ada",
    "enggak ada",
    "g ada",
    "gada",

    "belum ada",
    "nihil",
    "kosong",

    "tidak tahu",
    "ga tahu",
    "gak tahu",
    "nggak tahu",
    "ngga tahu",
    "enggak tahu",
    "g tahu",
    "kurang tahu",
    "entah",

    "terserah",
    "skip",
    "lewati",
    "pass",

    "none",
    "nothing",
    "nil",

    "n a",
    "na",

    "no comment",
    "no comments",
    "no suggestion",
    "no suggestions",
    "no complaint",
    "no complaints",
    "no idea",
    "nothing to say",
];


/*
|--------------------------------------------------------------------------
| VALIDATION MESSAGES
|--------------------------------------------------------------------------
*/
const MEANINGFUL_ANSWER_MESSAGE =
    "Berikan pendapat agar masukan Anda dapat dianalisa.";

const DUPLICATE_FEEDBACK_MESSAGE =
    "Berikan masukan yang berbeda dan relevan untuk setiap Sub Unit agar jawaban dapat dianalisa.";


/*
|--------------------------------------------------------------------------
| NORMALIZE ANSWER
|--------------------------------------------------------------------------
|
| Contoh:
|
| "Sudah Baik dan Terus Dikembangkan."
|
| menjadi:
|
| "sudah baik dan terus dikembangkan"
|
*/
function normalizeMeaningfulAnswer(value) {
    return String(value ?? "")
        .trim()
        .toLocaleLowerCase("id-ID")
        .replace(/[^\p{L}\p{N}]+/gu, " ")
        .replace(/\s+/g, " ")
        .trim();
}


/*
|--------------------------------------------------------------------------
| MEANINGLESS ANSWER
|--------------------------------------------------------------------------
*/
function isMeaninglessAnswer(value) {
    const original = String(
        value ?? ""
    ).trim();

    if (original === "") {
        return true;
    }

    /*
     * Tolak jika hanya simbol /
     * tanda baca / spasi.
     */
    if (
        /^[\p{P}\p{S}\s]+$/u.test(
            original
        )
    ) {
        return true;
    }

    const normalized =
        normalizeMeaningfulAnswer(
            original
        );

    if (normalized === "") {
        return true;
    }

    return MEANINGLESS_ANSWERS.includes(
        normalized
    );
}


/*
|--------------------------------------------------------------------------
| CONDITIONAL QUESTIONS
|--------------------------------------------------------------------------
*/
function initializeConditionalQuestions(page) {
    const conditionalCards = Array.from(
        page.querySelectorAll(
            "[data-conditional-question]"
        )
    );

    if (
        conditionalCards.length === 0
    ) {
        return;
    }

    conditionalCards.forEach(
        function (card) {
            card.querySelectorAll(
                "input, textarea, select"
            ).forEach(
                function (field) {
                    field.dataset.conditionalRequired =
                        field.required
                            ? "1"
                            : "0";
                }
            );
        }
    );

    const refresh = function () {
        conditionalCards.forEach(
            function (card) {
                let rules = {
                    show_rules: [],
                    hide_rules: [],
                };

                try {
                    rules = JSON.parse(
                        card.dataset.conditionalRules
                        || "{}"
                    );
                } catch (error) {
                    rules = {
                        show_rules: [],
                        hide_rules: [],
                    };
                }

                const matches =
                    function (trigger) {
                        const parentId =
                            String(
                                trigger.parent_id
                                || ""
                            );

                        const selected =
                            page.querySelector(
                                `input[name="answers[${parentId}][value]"]:checked`
                            )
                            ||
                            page.querySelector(
                                `select[name="answers[${parentId}][value]"]`
                            );

                        return (
                            Boolean(selected)
                            &&
                            (
                                trigger.option_ids
                                || []
                            )
                                .map(String)
                                .includes(
                                    String(
                                        selected.value
                                    )
                                )
                        );
                    };

                const showRules =
                    Array.isArray(
                        rules.show_rules
                    )
                        ? rules.show_rules
                        : [];

                const hideRules =
                    Array.isArray(
                        rules.hide_rules
                    )
                        ? rules.hide_rules
                        : [];

                const visible =
                    (
                        showRules.length === 0
                        ||
                        showRules.some(matches)
                    )
                    &&
                    !hideRules.some(matches);

                card.hidden = !visible;

                card.classList.toggle(
                    "hidden",
                    !visible
                );

                card.querySelectorAll(
                    "input, textarea, select"
                ).forEach(
                    function (field) {
                        field.disabled =
                            !visible;

                        field.required =
                            visible
                            &&
                            field.dataset
                                .conditionalRequired
                                === "1";
                    }
                );

                if (!visible) {
                    clearQuestionValidation(
                        card
                    );
                }
            }
        );
    };

    page.addEventListener(
        "change",
        function (event) {
            if (
                event.target.matches(
                    'input[type="radio"], select'
                )
            ) {
                refresh();
            }
        }
    );

    refresh();
}


/*
|--------------------------------------------------------------------------
| SURVEYOR AUTOFILL
|--------------------------------------------------------------------------
*/
function initializeSurveyorAutofill(form) {
    const button =
        document.getElementById(
            "surveyorAutofillButton"
        );

    if (!button) {
        return;
    }

    button.addEventListener(
        "click",
        function () {
            const originalHtml =
                button.innerHTML;

            button.disabled = true;

            button.innerHTML =
                '<i class="fa-solid fa-spinner fa-spin"></i>' +
                "<span>Mengisi...</span>";

            fillRankingSelects(form);
            fillRadioGroups(form);
            fillRequiredCheckboxGroups(form);
            fillRegularSelects(form);
            fillDummyTextFields(form);
            fillRequiredCheckboxGroups(form);
            fillDummyTextFields(form);

            clearAllValidation(form);

            button.classList.remove(
                "bg-blue-600",
                "hover:bg-blue-700"
            );

            button.classList.add(
                "bg-emerald-600",
                "hover:bg-emerald-700"
            );

            button.innerHTML =
                '<i class="fa-solid fa-circle-check"></i>' +
                "<span>Dummy Terisi</span>";

            window.setTimeout(
                function () {
                    button.disabled =
                        false;

                    button.classList.remove(
                        "bg-emerald-600",
                        "hover:bg-emerald-700"
                    );

                    button.classList.add(
                        "bg-blue-600",
                        "hover:bg-blue-700"
                    );

                    button.innerHTML =
                        originalHtml;
                },
                1800
            );
        }
    );
}


function fillRankingSelects(form) {
    form.querySelectorAll(
        "[data-ranking-container]"
    ).forEach(
        function (container) {
            const usedValues =
                new Set();

            container.querySelectorAll(
                "[data-ranking-select]:not(:disabled)"
            ).forEach(
                function (select) {
                    const option =
                        Array.from(
                            select.options
                        ).find(
                            function (candidate) {
                                return (
                                    candidate.value !== ""
                                    &&
                                    !candidate.disabled
                                    &&
                                    !usedValues.has(
                                        candidate.value
                                    )
                                );
                            }
                        );

                    if (!option) {
                        return;
                    }

                    select.value =
                        option.value;

                    usedValues.add(
                        option.value
                    );

                    dispatchDummyEvents(
                        select
                    );
                }
            );
        }
    );
}


function fillRadioGroups(form) {
    const groups =
        new Map();

    form.querySelectorAll(
        'input[type="radio"]:not(:disabled)'
    ).forEach(
        function (radio) {
            if (
                !groups.has(
                    radio.name
                )
            ) {
                groups.set(
                    radio.name,
                    []
                );
            }

            groups
                .get(radio.name)
                .push(radio);
        }
    );

    groups.forEach(
        function (radios) {
            const numeric =
                radios.filter(
                    function (radio) {
                        return (
                            radio.value !== ""
                            &&
                            Number.isFinite(
                                Number(
                                    radio.value
                                )
                            )
                        );
                    }
                );

            const selected =
                numeric.length > 0
                    ? numeric.reduce(
                        function (
                            best,
                            radio
                        ) {
                            return Number(
                                radio.value
                            )
                            >
                            Number(
                                best.value
                            )
                                ? radio
                                : best;
                        }
                    )
                    : radios[
                        radios.length - 1
                    ];

            selected.checked =
                true;

            dispatchDummyEvents(
                selected
            );
        }
    );
}


function fillRequiredCheckboxGroups(form) {
    form.querySelectorAll(
        "[data-required-group]"
    ).forEach(
        function (group) {
            if (
                group.closest(
                    ".hidden"
                )
            ) {
                return;
            }

            const checkboxes =
                Array.from(
                    group.querySelectorAll(
                        'input[type="checkbox"]:not(:disabled)'
                    )
                );

            if (
                checkboxes.length === 0
                ||
                checkboxes.some(
                    function (item) {
                        return item.checked;
                    }
                )
            ) {
                return;
            }

            checkboxes[0].checked =
                true;

            dispatchDummyEvents(
                checkboxes[0]
            );
        }
    );
}


function fillRegularSelects(form) {
    form.querySelectorAll(
        "select:not([data-ranking-select]):not(:disabled)"
    ).forEach(
        function (select) {
            const option =
                Array.from(
                    select.options
                ).find(
                    function (candidate) {
                        return (
                            candidate.value !== ""
                            &&
                            !candidate.disabled
                        );
                    }
                );

            if (!option) {
                return;
            }

            select.value =
                option.value;

            dispatchDummyEvents(
                select
            );
        }
    );
}


function fillDummyTextFields(form) {
    const selector =
        "input:not([type='hidden'])" +
        ":not([type='radio'])" +
        ":not([type='checkbox'])" +
        ":not([type='submit'])" +
        ":not([type='button'])" +
        ":not(:disabled), " +
        "textarea:not(:disabled)";

    form.querySelectorAll(
        selector
    ).forEach(
        function (field) {
            const type =
                (
                    field.type
                    || "text"
                ).toLowerCase();

            if (
                type === "number"
                ||
                type === "range"
            ) {
                field.value =
                    field.min !== ""
                        ? field.min
                        : "1";

            } else if (
                type === "date"
            ) {
                field.value =
                    new Date()
                        .toISOString()
                        .slice(0, 10);

            } else if (
                type === "datetime-local"
            ) {
                field.value =
                    new Date()
                        .toISOString()
                        .slice(0, 16);

            } else if (
                type === "time"
            ) {
                field.value =
                    "09:00";

            } else if (
                type === "email"
            ) {
                field.value =
                    "surveyor.dummy@example.com";

            } else if (
                type === "tel"
            ) {
                field.value =
                    "081234567890";

            } else if (
                type === "url"
            ) {
                field.value =
                    "https://example.com";

            } else {
                field.value =
                    field.tagName ===
                    "TEXTAREA"
                        ? "Contoh jawaban pengisian oleh Surveyor."
                        : "Contoh Surveyor";
            }

            dispatchDummyEvents(
                field
            );
        }
    );
}


function dispatchDummyEvents(field) {
    field.dispatchEvent(
        new Event(
            "input",
            {
                bubbles: true,
            }
        )
    );

    field.dispatchEvent(
        new Event(
            "change",
            {
                bubbles: true,
            }
        )
    );
}


/*
|--------------------------------------------------------------------------
| CHILD OPTION
|--------------------------------------------------------------------------
*/
function initializeChildOptions(page) {
    page.addEventListener(
        "change",
        function (event) {
            const input =
                event.target.closest(
                    "[data-option-input]"
                );

            if (!input) {
                return;
            }

            if (
                input.type === "radio"
            ) {
                refreshRadioChildren(
                    input
                );
            }

            if (
                input.type === "checkbox"
            ) {
                refreshCheckboxChild(
                    input
                );
            }
        }
    );

    page.querySelectorAll(
        "[data-option-input]"
    ).forEach(
        function (input) {
            if (
                input.type === "radio"
            ) {
                refreshRadioChildren(
                    input
                );
            }

            if (
                input.type === "checkbox"
            ) {
                refreshCheckboxChild(
                    input
                );
            }
        }
    );
}


function refreshRadioChildren(input) {
    const group =
        input.closest(
            "[data-option-group]"
        );

    if (!group) {
        return;
    }

    group.querySelectorAll(
        '[data-option-input][type="radio"]'
    ).forEach(
        function (radio) {
            const targetId =
                radio.dataset
                    .childTarget;

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
                radio.checked
                &&
                radio.dataset
                    .hasChild
                    === "1"
            );
        }
    );
}


function refreshCheckboxChild(input) {
    const targetId =
        input.dataset
            .childTarget;

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
        input.checked
        &&
        input.dataset
            .hasChild === "1"
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

    const input =
        child.querySelector(
            "[data-child-input]"
        );

    if (!input) {
        return;
    }

    input.disabled =
        !shouldShow;

    input.required =
        shouldShow;

    if (!shouldShow) {
        input.value = "";

        clearMeaningfulFieldValidation(
            input
        );
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
    ).forEach(
        refreshCustomerReason
    );

    page.addEventListener(
        "change",
        function (event) {
            const input =
                event.target.closest(
                    "[data-performance-input]"
                );

            if (!input) {
                return;
            }

            const container =
                input.closest(
                    "[data-customer-assessment]"
                );

            refreshCustomerReason(
                container
            );

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

    const questionType =
        Number(
            container.dataset
                .questionType
        );

    if (
        ![3, 4].includes(
            questionType
        )
    ) {
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

    const reasonMaximum =
        Number(
            container.dataset
                .reasonMaximum
        );

    const performanceValue =
        checkedPerformance
            ? Number(
                checkedPerformance
                    .value
            )
            : null;

    const shouldShow =
        performanceValue !== null
        &&
        performanceValue !== 0
        &&
        performanceValue
            <= reasonMaximum;

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
            reasonInput.value =
                "";
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
        .forEach(
            function (checkbox) {
                checkbox.disabled =
                    !shouldShow;

                if (!shouldShow) {
                    checkbox.checked =
                        false;

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
            }
        );
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
            clearAllValidation(
                form
            );

            const invalidQuestions =
                [];

            /*
             * Validasi masing-masing
             * question container.
             */
            form.querySelectorAll(
                "[data-question-container]"
            ).forEach(
                function (container) {
                    const result =
                        validateQuestionContainer(
                            container
                        );

                    if (!result.valid) {
                        markQuestionInvalid(
                            container,
                            result.message,
                            result.field
                        );

                        invalidQuestions.push({
                            container:
                                container,

                            field:
                                result.field,
                        });
                    }
                }
            );


            /*
            |--------------------------------------------------------------------------
            | DUPLICATE FEEDBACK
            |--------------------------------------------------------------------------
            */
            const duplicateFields =
                findDuplicateFeedbackFields(
                    form
                );

            duplicateFields.forEach(
                function (field) {
                    const container =
                        field.closest(
                            "[data-question-container]"
                        );

                    if (!container) {
                        return;
                    }

                    markQuestionInvalid(
                        container,
                        DUPLICATE_FEEDBACK_MESSAGE,
                        field
                    );

                    invalidQuestions.push({
                        container:
                            container,

                        field:
                            field,
                    });
                }
            );


            /*
             * Semua valid.
             */
            if (
                invalidQuestions.length
                === 0
            ) {
                return;
            }


            /*
             * Ada error.
             *
             * Stop submit supaya
             * halaman tidak reload.
             */
            event.preventDefault();
            event.stopPropagation();

            const first =
                invalidQuestions[0];

            first.container
                .scrollIntoView({
                    behavior:
                        "smooth",

                    block:
                        "center",
                });

            window.setTimeout(
                function () {
                    first.field
                        ?.focus();
                },
                350
            );
        }
    );


    /*
     * Live validation.
     */
    form.addEventListener(
        "input",
        function (event) {
            refreshLiveValidation(
                event.target
            );

            if (
                event.target.matches(
                    "[data-feedback-field]"
                )
            ) {
                refreshDuplicateFeedbackValidation(
                    form
                );
            }
        }
    );


    form.addEventListener(
        "change",
        function (event) {
            refreshLiveValidation(
                event.target
            );

            if (
                event.target.matches(
                    "[data-feedback-field]"
                )
            ) {
                refreshDuplicateFeedbackValidation(
                    form
                );
            }
        }
    );
}


/*
|--------------------------------------------------------------------------
| FIND DUPLICATE FEEDBACK
|--------------------------------------------------------------------------
|
| Dibandingkan berdasarkan:
|
| question_id
| +
| field yang sama
| +
| isi yang sama
|
| Contoh:
|
| D1 + Keluhan + Sub Unit A
| D1 + Keluhan + Sub Unit B
|
| akan dibandingkan.
|
| Tetapi:
|
| D1 + Keluhan
| D1 + Saran
|
| tidak dibandingkan.
|
*/
function findDuplicateFeedbackFields(
    form
) {
    const groups =
        new Map();

    const duplicateFields =
        new Set();

    const fields =
        Array.from(
            form.querySelectorAll(
                "[data-feedback-question-id]" +
                "[data-feedback-field]" +
                ":not(:disabled)"
            )
        );

    fields.forEach(
        function (field) {
            const rawValue =
                String(
                    field.value ?? ""
                ).trim();

            /*
             * Abaikan kosong.
             */
            if (!rawValue) {
                return;
            }

            /*
             * Meaningless answer ditangani
             * oleh validator lain.
             */
            if (
                isMeaninglessAnswer(
                    rawValue
                )
            ) {
                return;
            }

            const value =
                normalizeMeaningfulAnswer(
                    rawValue
                );

            if (!value) {
                return;
            }

            const questionId =
                String(
                    field.dataset
                        .feedbackQuestionId
                    || ""
                );

            const feedbackField =
                String(
                    field.dataset
                        .feedbackField
                    || ""
                );

            /*
             * Sub Unit sengaja tidak dimasukkan
             * ke key karena kita ingin mencari
             * jawaban sama ANTAR Sub Unit.
             */
            const key =
                questionId
                +
                "::"
                +
                feedbackField
                +
                "::"
                +
                value;

            if (
                !groups.has(
                    key
                )
            ) {
                groups.set(
                    key,
                    []
                );
            }

            groups
                .get(key)
                .push(field);
        }
    );

    groups.forEach(
        function (groupFields) {
            if (
                groupFields.length
                <= 1
            ) {
                return;
            }

            groupFields.forEach(
                function (field) {
                    duplicateFields.add(
                        field
                    );
                }
            );
        }
    );

    return Array.from(
        duplicateFields
    );
}


/*
|--------------------------------------------------------------------------
| LIVE DUPLICATE VALIDATION
|--------------------------------------------------------------------------
*/
function refreshDuplicateFeedbackValidation(
    form
) {
    const duplicateFields =
        new Set(
            findDuplicateFeedbackFields(
                form
            )
        );

    const feedbackFields =
        Array.from(
            form.querySelectorAll(
                "[data-feedback-field]"
            )
        );

    feedbackFields.forEach(
        function (field) {
            /*
             * Jika masih duplicate.
             */
            if (
                duplicateFields.has(
                    field
                )
            ) {
                const container =
                    field.closest(
                        "[data-question-container]"
                    );

                if (!container) {
                    return;
                }

                markQuestionInvalid(
                    container,
                    DUPLICATE_FEEDBACK_MESSAGE,
                    field
                );

                return;
            }

            /*
             * Kalau field sudah diperbaiki,
             * jangan langsung clear seluruh
             * container apabila masih ada
             * masalah pada field lain.
             */
            clearMeaningfulFieldValidation(
                field
            );

            const container =
                field.closest(
                    "[data-question-container]"
                );

            if (!container) {
                return;
            }

            const normalResult =
                validateQuestionContainer(
                    container
                );

            const duplicateInContainer =
                feedbackFields.some(
                    function (otherField) {
                        return (
                            duplicateFields.has(
                                otherField
                            )
                            &&
                            otherField.closest(
                                "[data-question-container]"
                            )
                            === container
                        );
                    }
                );

            if (
                normalResult.valid
                &&
                !duplicateInContainer
            ) {
                clearQuestionValidation(
                    container
                );
            }
        }
    );
}


/*
|--------------------------------------------------------------------------
| SERVER VALIDATION
|--------------------------------------------------------------------------
*/
function initializeServerValidation(form) {
    let invalidQuestionIds =
        [];

    try {
        invalidQuestionIds =
            JSON.parse(
                form.dataset
                    .serverInvalidQuestionIds
                    || "[]"
            );
    } catch (_) {
        invalidQuestionIds =
            [];
    }

    invalidQuestionIds.forEach(
        function (questionId) {
            const containers =
                form.querySelectorAll(
                    `[data-question-container][data-question-id="${questionId}"]`
                );

            containers.forEach(
                function (container) {
                    markQuestionInvalid(
                        container
                    );
                }
            );
        }
    );
}


/*
|--------------------------------------------------------------------------
| VALIDATE QUESTION CONTAINER
|--------------------------------------------------------------------------
*/
function validateQuestionContainer(
    container
) {
    if (!container) {
        return {
            valid: true,
        };
    }


    /*
    |--------------------------------------------------------------------------
    | RADIO
    |--------------------------------------------------------------------------
    */
    const radioNames =
        new Set();

    container.querySelectorAll(
        'input[type="radio"][required]:not(:disabled)'
    ).forEach(
        function (radio) {
            radioNames.add(
                radio.name
            );
        }
    );

    for (
        const name
        of radioNames
    ) {
        const radios =
            Array.from(
                container.querySelectorAll(
                    'input[type="radio"]'
                )
            ).filter(
                function (radio) {
                    return (
                        radio.name ===
                            name
                        &&
                        !radio.disabled
                    );
                }
            );

        if (
            !radios.some(
                function (radio) {
                    return radio.checked;
                }
            )
        ) {
            return {
                valid: false,

                field:
                    radios[0],

                message:
                    "Pilih salah satu nilai.",
            };
        }
    }


    /*
    |--------------------------------------------------------------------------
    | CHECKBOX
    |--------------------------------------------------------------------------
    */
    const checkboxGroups =
        container.querySelectorAll(
            "[data-required-group]"
        );

    for (
        const group
        of checkboxGroups
    ) {
        if (
            group.closest(
                ".hidden"
            )
        ) {
            continue;
        }

        const checkboxes =
            Array.from(
                group.querySelectorAll(
                    'input[type="checkbox"]:not(:disabled)'
                )
            );

        if (
            checkboxes.length > 0
            &&
            !checkboxes.some(
                function (checkbox) {
                    return checkbox.checked;
                }
            )
        ) {
            return {
                valid: false,

                field:
                    checkboxes[0],

                message:
                    "Pilih minimal satu alasan.",
            };
        }
    }


    /*
    |--------------------------------------------------------------------------
    | REQUIRED FIELD
    |--------------------------------------------------------------------------
    */
    const requiredFields =
        container.querySelectorAll(
            "input[required]:not([type='radio']):not([type='checkbox']):not(:disabled), " +
            "textarea[required]:not(:disabled), " +
            "select[required]:not(:disabled)"
        );

    for (
        const field
        of requiredFields
    ) {
        if (
            !String(
                field.value ?? ""
            ).trim()
        ) {
            return {
                valid: false,

                field:
                    field,

                message:
                    "Pertanyaan ini wajib diisi.",
            };
        }

        if (
            !field.checkValidity()
        ) {
            return {
                valid: false,

                field:
                    field,

                message:
                    "Format jawaban tidak valid.",
            };
        }
    }


    /*
    |--------------------------------------------------------------------------
    | MEANINGFUL ANSWER
    |--------------------------------------------------------------------------
    */
    const meaningfulFields =
        container.querySelectorAll(
            "[data-meaningful-answer]:not(:disabled)"
        );

    for (
        const field
        of meaningfulFields
    ) {
        const value =
            String(
                field.value ?? ""
            ).trim();

        /*
         * Kosong sudah ditangani required.
         */
        if (!value) {
            continue;
        }

        if (
            isMeaninglessAnswer(
                value
            )
        ) {
            return {
                valid: false,

                field:
                    field,

                message:
                    MEANINGFUL_ANSWER_MESSAGE,
            };
        }
    }


    return {
        valid: true,
    };
}


/*
|--------------------------------------------------------------------------
| MARK QUESTION INVALID
|--------------------------------------------------------------------------
*/
function markQuestionInvalid(
    container,
    message = null,
    field = null
) {
    if (!container) {
        return;
    }

    container.classList.remove(
        "border-gray-200"
    );

    container.classList.add(
        "border-red-500",
        "ring-2",
        "ring-red-100",
        "transition-colors"
    );

    container.style.backgroundColor =
        "rgb(239 68 68 / 20%)";

    if (field) {
        markMeaningfulFieldInvalid(
            field
        );
    }

    const errorElement =
        container.querySelector(
            "[data-question-error]"
        );

    if (
        errorElement
        &&
        message
    ) {
        errorElement.textContent =
            message;

        errorElement.classList.remove(
            "hidden"
        );
    }
}


/*
|--------------------------------------------------------------------------
| MARK FIELD INVALID
|--------------------------------------------------------------------------
*/
function markMeaningfulFieldInvalid(
    field
) {
    if (!field) {
        return;
    }

    field.classList.remove(
        "border-gray-300"
    );

    field.classList.add(
        "border-red-500",
        "ring-2",
        "ring-red-100"
    );
}


/*
|--------------------------------------------------------------------------
| CLEAR FIELD VALIDATION
|--------------------------------------------------------------------------
*/
function clearMeaningfulFieldValidation(
    field
) {
    if (!field) {
        return;
    }

    field.classList.remove(
        "border-red-500",
        "ring-2",
        "ring-red-100"
    );

    field.classList.add(
        "border-gray-300"
    );
}


/*
|--------------------------------------------------------------------------
| CLEAR QUESTION VALIDATION
|--------------------------------------------------------------------------
*/
function clearQuestionValidation(
    container
) {
    if (!container) {
        return;
    }

    container.classList.remove(
        "border-red-500",
        "ring-2",
        "ring-red-100",
        "transition-colors"
    );

    container.style.removeProperty(
        "background-color"
    );

    container.classList.add(
        "border-gray-200"
    );

    container.querySelectorAll(
        "[data-meaningful-answer]"
    ).forEach(
        clearMeaningfulFieldValidation
    );

    const errorElement =
        container.querySelector(
            "[data-question-error]"
        );

    if (errorElement) {
        errorElement.classList.add(
            "hidden"
        );
    }
}


/*
|--------------------------------------------------------------------------
| CLEAR ALL VALIDATION
|--------------------------------------------------------------------------
*/
function clearAllValidation(form) {
    form.querySelectorAll(
        "[data-question-container]"
    ).forEach(
        clearQuestionValidation
    );
}


/*
|--------------------------------------------------------------------------
| LIVE VALIDATION
|--------------------------------------------------------------------------
*/
function refreshLiveValidation(field) {
    const container =
        field.closest(
            "[data-question-container]"
        );

    if (!container) {
        return;
    }

    if (
        field.matches(
            "[data-meaningful-answer]"
        )
    ) {
        if (
            !String(
                field.value ?? ""
            ).trim()
            ||
            !isMeaninglessAnswer(
                field.value
            )
        ) {
            clearMeaningfulFieldValidation(
                field
            );
        }
    }

    const result =
        validateQuestionContainer(
            container
        );

    if (result.valid) {
        /*
         * Untuk feedback, duplicate akan
         * diperiksa setelah fungsi ini.
         */
        if (
            !field.matches(
                "[data-feedback-field]"
            )
        ) {
            clearQuestionValidation(
                container
            );
        }

        return;
    }

    markQuestionInvalid(
        container,
        result.message,
        result.field
    );
}


/*
|--------------------------------------------------------------------------
| RANKING ASSESSMENT
|--------------------------------------------------------------------------
*/
function initializeRankingAssessment(page) {
    page.querySelectorAll(
        "[data-ranking-container]"
    ).forEach(
        function (container) {
            refreshRankingContainer(
                container
            );
        }
    );

    page.addEventListener(
        "change",
        function (event) {
            const select =
                event.target.closest(
                    "[data-ranking-select]"
                );

            if (!select) {
                return;
            }

            const container =
                select.closest(
                    "[data-ranking-container]"
                );

            refreshRankingChild(
                select
            );

            refreshRankingOptions(
                container
            );

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
    ).forEach(
        function (select) {
            refreshRankingChild(
                select
            );
        }
    );

    refreshRankingOptions(
        container
    );
}


/*
|--------------------------------------------------------------------------
| RANKING CHILD
|--------------------------------------------------------------------------
*/
function refreshRankingChild(select) {
    const row =
        select.closest(
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
        !childContainer
        ||
        !childInput
    ) {
        return;
    }

    const selectedOption =
        select.options[
            select.selectedIndex
        ];

    const hasChild =
        selectedOption
        &&
        selectedOption.value !== ""
        &&
        selectedOption.dataset
            .hasChild === "1";

    const childPlaceholder =
        selectedOption
            ?.dataset
            ?.answerText2
            ?.trim()
        ||
        "Tulis jawaban tambahan...";

    childContainer.classList.toggle(
        "hidden",
        !hasChild
    );

    childInput.disabled =
        !hasChild;

    childInput.required =
        hasChild;

    childInput.placeholder =
        childPlaceholder;

    if (!hasChild) {
        childInput.value = "";

        clearMeaningfulFieldValidation(
            childInput
        );
    }

    if (
        hasChild
        &&
        childLabel
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

    const selects =
        Array.from(
            container.querySelectorAll(
                "[data-ranking-select]"
            )
        );

    const selectedNonRepeatable =
        [];

    selects.forEach(
        function (select) {
            const selectedOption =
                select.options[
                    select.selectedIndex
                ];

            if (
                !selectedOption
                ||
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
        }
    );

    selects.forEach(
        function (select) {
            Array.from(
                select.options
            ).forEach(
                function (option) {
                    if (
                        option.value === ""
                    ) {
                        option.disabled =
                            false;

                        return;
                    }

                    if (
                        option.dataset
                            .hasChild === "1"
                    ) {
                        option.disabled =
                            false;

                        return;
                    }

                    const usedByOtherSelect =
                        selectedNonRepeatable
                            .some(
                                function (item) {
                                    return (
                                        item.value
                                            ===
                                            option.value
                                        &&
                                        item.select
                                            !==
                                            select
                                    );
                                }
                            );

                    option.disabled =
                        usedByOtherSelect;
                }
            );
        }
    );
}
