document.addEventListener("click", function (e) {

    const button = e.target.closest(
        '[data-modal-open="editActivityModal"]'
    );

    if (!button) return;


    const id = button.dataset.id;
    const name = button.dataset.name;
    const description = button.dataset.description;
    const action = button.dataset.action;


    const idInput =
        document.getElementById("edit_activity_id");

    const nameInput =
        document.getElementById("edit_activity_name");

    const descriptionInput =
        document.getElementById("edit_activity_description");

    const form =
        document.getElementById("editActivityForm");


    if (
        !idInput ||
        !nameInput ||
        !descriptionInput ||
        !form
    ) {
        console.error(
            "Element modal edit activity tidak ditemukan."
        );

        return;
    }


    idInput.value = id ?? "";
    nameInput.value = name ?? "";
    descriptionInput.value = description ?? "";

    form.action = action ?? "";

});

document.addEventListener("click", function(e) {

    const button = e.target.closest(
        '[data-modal-open="deleteModal"]'
    );

    if (!button)
        return;


    console.log("DELETE DATA:", button.dataset);


    document.getElementById("deleteUserName").textContent =
        button.dataset.name;


    document.getElementById("deleteForm").action =
        `/activities/${button.dataset.id}`;

});

const ActivityPage = {

    init() {
        this.initBulkSelect();
        this.bindEvents();
    },


    bindEvents() {

        const btn =
            document.getElementById("btnDeleteSelected");


        if (!btn) {
            return;
        }


        btn.addEventListener("click", () => {


            const checked =
                document.querySelectorAll(".bulk-checkbox:checked");


            if (!checked.length) {

                alert("Pilih minimal satu activity.");

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

    }

};

document.addEventListener("DOMContentLoaded", () => {

    autoFilterTable({
        table: "#activityTable",
        searchInput: "#searchInput"
    });

    ActivityPage.init();

});

document.addEventListener("modal:opened", (e) => {

    if (e.detail.id !== "bulkDeleteModal") {
        return;
    }


    const checked =
        document.querySelectorAll(".bulk-checkbox:checked");


    document.getElementById("bulkDeleteCount").textContent =
        checked.length;


    const list =
        document.getElementById("bulkDeleteUserList");


    list.innerHTML = "";


    checked.forEach(cb => {


        const li =
            document.createElement("li");


        li.textContent =
            cb.dataset.label;


        list.appendChild(li);


    });


});

document.addEventListener(
    "modal:opened",
    (e)=>{

        if(e.detail.id !== "createActivityModal")
            return;


        const table =
            document.getElementById(
                "activityCreateTable"
            );


        if(
            table &&
            table.querySelectorAll("tbody tr").length === 0
        ){

            DynamicTable.addRow(
                "activityCreateTable"
            );

        }

    }
);
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
            "activityCreateTable"
        );


    }
);