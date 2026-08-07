(function () {
    "use strict";

    const setValue = (id, value) => {
        const element = document.getElementById(id);
        if (element) element.value = value ?? "";
        return element;
    };

    const templateByFormType = (formTypeId) => {
        const templates = {
            1: "generalQuestionnaireTypeOptions",
            2: "customerAssessment15TypeOptions",
            3: "customerAssessment17TypeOptions",
            4: "engagementAssessment15TypeOptions",
            5: "engagementAssessment17TypeOptions",
            6: "ranking13TypeOptions",
            7: "ranking15TypeOptions",
            8: "strengthComplaintSuggestionTypeOptions",
            9: "complaintSuggestionTypeOptions",
            10: "suggestionTypeOptions",
            11: "competitorTypeOptions",
        };

        return document.getElementById(templates[Number(formTypeId)] || "");
    };

    const fillQuestionTypes = (formTypeId, selectedId) => {
        const select = document.getElementById("edit_question_type");
        if (!select) return;

        select.replaceChildren(new Option("Pilih tipe", ""));
        const template = templateByFormType(formTypeId);
        if (template) select.appendChild(template.content.cloneNode(true));

        select.value = String(selectedId ?? "");
        select.dataset.originalValue = String(selectedId ?? "");

        const help = document.getElementById("edit_question_type_help");
        if (help) {
            help.textContent = select.options[select.selectedIndex]?.dataset.description || "";
        }
    };

    document.addEventListener("click", (event) => {
        const formButton = event.target.closest('[data-modal-open="editFormModal"]');
        if (formButton) {
            const form = document.getElementById("editFormForm");
            if (form) form.action = formButton.dataset.action || "";
            setValue("edit_form_id", formButton.dataset.id);
            setValue("edit_form_group_id", formButton.dataset.groupId);
            setValue("edit_form_no_urut", formButton.dataset.noUrut);
            setValue("edit_form_name", formButton.dataset.name);
            const type = setValue("edit_form_type", formButton.dataset.formtypeId);
            if (type) type.dataset.originalValue = formButton.dataset.formtypeId || "";
        }

        const questionButton = event.target.closest('[data-modal-open="editQuestionModal"]');
        if (questionButton) {
            const form = document.getElementById("editQuestionForm");
            if (form) form.action = questionButton.dataset.action || "";
            setValue("edit_question_id", questionButton.dataset.id);
            setValue("edit_question_group_id", questionButton.dataset.groupId);
            setValue("edit_question_form_id", questionButton.dataset.formId);
            setValue("edit_question_formtype_id", questionButton.dataset.formTypeId);
            setValue("edit_question_header", questionButton.dataset.header);
            setValue("edit_question_no", questionButton.dataset.no);
            setValue("edit_question_name", questionButton.dataset.name);
            fillQuestionTypes(questionButton.dataset.formTypeId, questionButton.dataset.questionTypeId);
        }

        const optionButton = event.target.closest('[data-modal-open="editOptionModal"]');
        if (optionButton) {
            const form = document.getElementById("editOptionForm");
            if (form) form.action = optionButton.dataset.action || "";
            setValue("edit_option_id", optionButton.dataset.id);
            setValue("edit_option_question_id", optionButton.dataset.questionId);
            setValue("edit_option_no", optionButton.dataset.no);
            setValue("edit_option_answer_text", optionButton.dataset.answerText);

            const hasChild = optionButton.dataset.hasChild === "1";
            const checkbox = document.getElementById("edit_option_has_child_checkbox");
            if (checkbox) checkbox.checked = hasChild;
            setValue("edit_option_has_child", hasChild ? "1" : "0");

            const child = setValue("edit_option_answer_text2", optionButton.dataset.answerText2);
            if (child) {
                child.disabled = !hasChild;
                child.required = hasChild;
                child.classList.toggle("bg-gray-100", !hasChild);
                child.classList.toggle("bg-white", hasChild);
            }
        }
    }, true);
})();
