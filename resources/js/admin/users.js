let activeModalButton = null;
document.addEventListener("DOMContentLoaded", () => {
    autoFilterTable({
        table: "#userTable",
        searchInput: "#searchInput",
        filters: {
            activity: "#activityFilter",
            role: "#roleFilter"
        }
    });
    UserPage.init();
});

/* =========================
   HANDLE BUTTON MODAL GLOBAL
========================= */
document.addEventListener(
    "mousedown",
    (e)=>{

        const button =
            e.target.closest("[data-modal-open]");


        if(!button)
            return;


        activeModalButton = button;

    }
);

document.addEventListener(
    "modal:opened",
    (e)=>{
        if(e.detail.id !== "createUserModal")
            return;
        const table =
            document.getElementById(
                "userCreateTable"
            );
        if(
            table &&
            table.querySelectorAll("tbody tr").length === 0
        ){
            DynamicTable.addRow(
                "userCreateTable"
            );
        }
    }
);


/* =========================
   ROLE & PROFILE CASCADE
========================= */
function filterProfileOptions(row, resetGroup = false, resetUnit = false) {
    const activity = row.querySelector('.activity-select');
    const activityNotApplicable = row.querySelector('.activity-not-applicable');
    const group = row.querySelector('.group-select');
    const unit = row.querySelector('.unit-select');
    if (!activity || !group || !unit) return;

    if (resetGroup) group.value = '';
    [...group.options].forEach((option, index) => {
        if (index === 0) return;
        const visible = String(option.dataset.activityId) === String(activity.value);
        option.hidden = !visible;
        option.disabled = !visible;
    });
    if (group.selectedOptions[0]?.disabled) group.value = '';

    if (resetUnit) unit.value = '';
    [...unit.options].forEach((option, index) => {
        if (index === 0) return;
        const visible = String(option.dataset.groupId) === String(group.value);
        option.hidden = !visible;
        option.disabled = !visible;
    });
    if (unit.selectedOptions[0]?.disabled) unit.value = '';
}

function syncUserRow(row, reset = false) {
    const role = row.querySelector('.role-select');
    const activityColumn = row.querySelector('.activity-column');
    const groupColumn = row.querySelector('.group-column');
    const unitColumn = row.querySelector('.unit-column');
    const activity = row.querySelector('.activity-select');
    const activityNotApplicable = row.querySelector('.activity-not-applicable');
    const group = row.querySelector('.group-select');
    const unit = row.querySelector('.unit-select');
    const groupNotApplicable = row.querySelector('.group-not-applicable');
    const unitNotApplicable = row.querySelector('.unit-not-applicable');
    if (!role || !activity || !group || !unit) return;

    const roleId = Number(role.value);
    const profileRole = roleId === 2 || roleId === 4;
    const showProfileFields = !roleId || profileRole;
    const activityRequired = roleId === 3 || roleId === 4;
    activityColumn?.classList.remove('hidden');
    activity.classList.toggle('hidden', roleId === 1);
    activityNotApplicable?.classList.toggle('hidden', roleId !== 1);
    groupColumn?.classList.remove('hidden');
    unitColumn?.classList.remove('hidden');
    group.classList.toggle('hidden', !showProfileFields);
    unit.classList.toggle('hidden', !showProfileFields);
    groupNotApplicable?.classList.toggle('hidden', showProfileFields);
    unitNotApplicable?.classList.toggle('hidden', showProfileFields);
    activity.required = activityRequired;

    if (reset) {
        if (roleId === 1) activity.value = '';
        if (!profileRole) {
            group.value = '';
            unit.value = '';
        }
    }
    filterProfileOptions(row, reset, reset);
}

document.addEventListener('change', (event) => {
    const row = event.target.closest('tr') || event.target.closest('form');
    if (!row) return;
    if (event.target.classList.contains('role-select')) syncUserRow(row, true);
    if (event.target.classList.contains('activity-select')) filterProfileOptions(row, true, true);
    if (event.target.classList.contains('group-select')) filterProfileOptions(row, false, true);
});
/* =========================
   VALIDATION INPUT
========================= */
document.addEventListener(
    "input",
    (e)=>{


        if(
            e.target.matches(
                "input[required], select[required]"
            )
        ){

            validateField(
                e.target
            );

        }


    }
);



document.addEventListener(
    "change",
    (e)=>{


        if(
            e.target.matches(
                "select[required]"
            )
        ){

            validateField(
                e.target
            );

        }


    }
);


/* =========================
   FORM VALIDATION
========================= */
document.addEventListener(
    "submit",
    (e)=>{


        const form =
            e.target;


        const fields =
            form.querySelectorAll(
                "input[required], select[required]"
            );


        let invalid = false;



        fields.forEach(
            field=>{


                if(
                    !validateField(field)
                ){

                    invalid = true;

                }


            }
        );



        if(invalid){

            e.preventDefault();

        }


    }
);


function validateField(field){


    if(
        field.value.trim() === ""
    ){


        field.classList.add(
            "border-red-500"
        );


        field.classList.remove(
            "border-gray-300"
        );


        return false;


    }


    clearError(field);


    return true;


}



function clearError(field){


    field.classList.remove(
        "border-red-500"
    );


    field.classList.add(
        "border-gray-300"
    );


}


/* =========================
   ADD ROW TABLE
========================= */

document.addEventListener(
    "click",
    (e)=>{


        const button =
            e.target.closest(
                "[data-add-row]"
            );


        if(!button)
            return;



        DynamicTable.addRow(
            "userCreateTable"
        );


    }
);


/* =========================
   EDIT USER MODAL
========================= */

document.addEventListener("click", function (e) {

    const button = e.target.closest(
        '[data-modal-open="editUserModal"]'
    );

    if (!button) return;


    const id = button.dataset.id;
    const username = button.dataset.username;
    const role = button.dataset.role;
    const activity = button.dataset.activity;
    const action = button.dataset.action;


    const idInput =
        document.getElementById("edit_user_id");

    const usernameInput =
        document.getElementById("edit_username");

    const roleInput =
        document.getElementById("edit_role");

    const activityInput =
        document.getElementById("edit_activity");

    const passwordInput =
        document.getElementById("edit_password");

    const form =
        document.getElementById("editUserForm");


    if (
        !idInput ||
        !usernameInput ||
        !roleInput ||
        !activityInput ||
        !form
    ) {
        console.error(
            "Element modal edit user tidak ditemukan."
        );

        return;
    }


    idInput.value = id ?? "";
    usernameInput.value = username ?? "";
    roleInput.value = String(role ?? "");
    activityInput.value = String(activity ?? "");

    roleInput.dispatchEvent(new Event("change", { bubbles: true }));


    // Password selalu dikosongkan saat modal edit dibuka
    if (passwordInput) {
        passwordInput.value = "";
    }


    form.action = action ?? "";

});


/* =========================
   DELETE USER MODAL
========================= */

document.addEventListener(
    "modal:opened",
    (e)=>{


        if(e.detail.id !== "deleteUserModal")
            return;

        if(!activeModalButton)
            return;


        const button = activeModalButton;


        console.log("DELETE:", button.dataset.id);


        const userName = document.getElementById(
            "deleteUserName"
        );

        const deleteForm = document.getElementById(
            "deleteUserForm"
        );

        if (!userName || !deleteForm || !button.dataset.action) {
            console.error(
                "Konfigurasi modal hapus user tidak lengkap."
            );

            return;
        }

        userName.textContent = button.dataset.name;
        deleteForm.action = button.dataset.action;


    }
);

document.addEventListener("modal:opened", (event) => {
    if (event.detail.id !== "deleteAnswersModal" || !activeModalButton) {
        return;
    }

    const form = document.getElementById("deleteAnswersForm");
    const name = document.getElementById("deleteAnswersUserName");

    if (!form || !name || !activeModalButton.dataset.action) {
        console.error("Konfigurasi modal hapus jawaban tidak lengkap.");
        return;
    }

    name.textContent = activeModalButton.dataset.name || "user ini";
    form.action = activeModalButton.dataset.action;
});

document.addEventListener("modal:opened", (event) => {
    if (event.detail.id !== "clearProfileAssignmentModal" || !activeModalButton) {
        return;
    }

    const form = document.getElementById("clearProfileAssignmentForm");
    const name = document.getElementById("clearProfileAssignmentName");

    if (form) {
        form.action = activeModalButton.dataset.action || "#";
    }

    if (name) {
        name.textContent = activeModalButton.dataset.name || "user ini";
    }
});
document.addEventListener("modal:opened", (event) => {
    if (event.detail.id !== "resetProfileModal" || !activeModalButton) {
        return;
    }

    const form = document.getElementById("resetProfileForm");
    const name = document.getElementById("resetProfileUserName");

    if (!form || !name || !activeModalButton.dataset.action) {
        console.error("Konfigurasi modal reset profile tidak lengkap.");
        return;
    }

    name.textContent = activeModalButton.dataset.name || "user ini";
    form.action = activeModalButton.dataset.action;
});

document.addEventListener("modal:opened", (event) => {
    if (event.detail.id !== "reopenSurveyModal" || !activeModalButton) {
        return;
    }

    const form = document.getElementById("reopenSurveyForm");
    const name = document.getElementById("reopenSurveyUserName");

    if (!form || !name || !activeModalButton.dataset.action) {
        console.error("Konfigurasi modal buka kembali survey tidak lengkap.");
        return;
    }

    name.textContent = activeModalButton.dataset.name || "user ini";
    form.action = activeModalButton.dataset.action;
});

/* =========================
   Bulk Selected
========================= */
const UserPage = {

    init() {
        this.initImportFile();
        this.initBulkSelect();
        this.bindEvents();
    },

    initImportFile() {
        const input = document.getElementById("userImportFile");
        const label = document.getElementById("userImportFileLabel");
        const submit = document.getElementById("userImportSubmit");

        if (!input || !label || !submit) return;

        const syncImportState = () => {
            const file = input.files?.[0] ?? null;
            label.textContent = file?.name || "Pilih File";
            label.title = file?.name || "";
            submit.disabled = !file;
        };

        input.addEventListener("change", syncImportState);
        input.form?.addEventListener("reset", () => {
            window.setTimeout(syncImportState, 0);
        });

        syncImportState();
    },

    bindEvents() {

        const btn = document.getElementById("btnDeleteSelected");

        if (!btn) {
            return;
        }

        btn.addEventListener("click", () => {

            const ids = BulkSelect.getSelectedIds();

            if (!ids.length) {
                alert("Pilih minimal satu user.");
                return;
            }

            Modal.open("bulkDeleteModal");

        });

    },

    initBulkSelect() {
        BulkSelect.init({
        selectAll: '.bulk-select-all',
        checkbox: '.bulk-checkbox'
    });

    },

    getSelectedIds() {
        // Return array id user
    },

    deleteSelected() {
        // Ajax delete
    },

    resetProfile() {
        // Ajax reset profile
    }

};

document.addEventListener("modal:opened", (e) => {

    if (e.detail.id !== "bulkDeleteModal" || !document.getElementById("userTable")) {
        return;
    }

    const checked = document.querySelectorAll(".bulk-checkbox:checked");

    document.getElementById("bulkDeleteCount").textContent = checked.length;

    const list = document.getElementById("bulkDeleteUserList");
    list.innerHTML = "";

    const form = document.getElementById("bulkDeleteForm");

    // Hapus input lama
    form.querySelectorAll("input[name='ids[]']").forEach(input => input.remove());

    checked.forEach(cb => {

        // Tambahkan username ke list
        const li = document.createElement("li");
        li.textContent = cb.dataset.username;
        list.appendChild(li);

        // Tambahkan hidden input
        const input = document.createElement("input");
        input.type = "hidden";
        input.name = "ids[]";
        input.value = cb.value;

        form.appendChild(input);

    });

});
