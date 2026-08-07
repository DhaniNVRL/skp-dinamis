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
   ROLE CHANGE
========================= */
document.addEventListener(
    "change",
    (e)=>{


        if(
            !e.target.classList.contains(
                "role-select"
            )
        ){
            return;
        }


        const row =
            e.target.closest("tr");


        const activityColumn =
            row.querySelector(
                ".activity-column"
            );


        const activitySelect =
            row.querySelector(
                ".activity-select"
            );


        const roleName =
            e.target
                .options[
                    e.target.selectedIndex
                ]
                .text
                .toLowerCase();



        if(roleName === "admin"){


            activityColumn.classList.add(
                "hidden"
            );


            activitySelect.value = "";


            activitySelect.removeAttribute(
                "required"
            );


            clearError(
                activitySelect
            );


        }else{


            activityColumn.classList.remove(
                "hidden"
            );


            activitySelect.setAttribute(
                "required",
                true
            );


        }


    }
);


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
        this.initBulkSelect();
        this.bindEvents();
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
