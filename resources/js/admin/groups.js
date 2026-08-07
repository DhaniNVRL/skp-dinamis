$('.tab-button').on('click', function () {

    const tab = $(this).data('tab');

    const url = new URL(window.location.href);
    url.searchParams.set('tab', tab);
    window.history.replaceState({}, '', url.toString());

    $('.tab-button')
        .removeClass('border-blue-600 text-blue-600')
        .addClass('border-transparent text-gray-500');

    $(this)
        .removeClass('border-transparent text-gray-500')
        .addClass('border-blue-600 text-blue-600');

    $('#tab-group').addClass('hidden');
    $('#tab-profile').addClass('hidden');

    $('#tab-' + tab).removeClass('hidden');

});

$(function () {
    const requestedTab = new URLSearchParams(window.location.search).get('tab');
    const button = requestedTab
        ? $('.tab-button[data-tab]').filter(function () {
            return this.dataset.tab === requestedTab;
        })
        : $();

    if (button.length) {
        button.trigger('click');
    }
});

document.addEventListener("click", function (e) {

    const button = e.target.closest(
        '[data-modal-open="editGroupModal"]'
    );

    if (!button) return;

    const id = button.dataset.id;
    const name = button.dataset.name;
    const action = button.dataset.action;

    const idInput =
        document.getElementById("edit_group_id");

    const nameInput =
        document.getElementById("edit_group_name");

    const form =
        document.getElementById("editGroupForm");


    if (!idInput || !nameInput || !form) {
        console.error(
            "Element modal edit group tidak ditemukan."
        );
        return;
    }


    idInput.value = id ?? "";
    nameInput.value = name ?? "";

    form.action = action ?? "";

});

document.addEventListener("click", function(e) {
    const button = e.target.closest(
        '[data-modal-open="deleteGroupModal"]'
    );
    if (!button)
        return;
    console.log("DELETE GROUP:", button.dataset);
    document.getElementById(
        "deleteGroupName"
    ).textContent =
        button.dataset.name;
    document.getElementById(
        "deleteGroupForm"
    ).action =
        `/groups/${button.dataset.id}`;
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
        table: "#GroupsTable",
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

        if(e.detail.id !== "createGroupModal")
            return;
        const table =
            document.getElementById(
                "groupCreateTable"
            );


        if(
            table &&
            table.querySelectorAll("tbody tr").length === 0
        ){

            DynamicTable.addRow(
                "groupCreateTable"
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
            "groupCreateTable"
        );


    }
);

function initializeGroupImport() {
    const fileInput =
        document.getElementById(
            "groupImportFile"
        );

    const fileName =
        document.getElementById(
            "groupImportFileName"
        );

    const importButton =
        document.getElementById(
            "btnImportGroup"
        );

    const importForm =
        document.getElementById(
            "importGroupForm"
        );

    if (
        !fileInput ||
        !fileName ||
        !importButton ||
        !importForm
    ) {
        return;
    }

    importButton.disabled = true;

    fileInput.addEventListener(
        "change",
        function () {
            const file =
                fileInput.files?.[0];

            if (!file) {
                fileName.textContent =
                    "Pilih File";

                importButton.disabled = true;

                return;
            }

            const extension =
                file.name
                    .split(".")
                    .pop()
                    .toLowerCase();

            const allowedExtensions = [
                "xlsx",
                "xls",
            ];

            if (
                !allowedExtensions.includes(
                    extension
                )
            ) {
                window.alert(
                    "File harus berformat XLSX atau XLS."
                );

                fileInput.value = "";

                fileName.textContent =
                    "Pilih File";

                importButton.disabled = true;

                return;
            }

            fileName.textContent =
                file.name;

            importButton.disabled = false;
        }
    );

    importForm.addEventListener(
        "submit",
        function (event) {
            if (!fileInput.files?.length) {
                event.preventDefault();

                importButton.disabled = true;

                window.alert(
                    "Pilih file Excel terlebih dahulu."
                );

                return;
            }

            importButton.disabled = true;

            importButton.innerHTML = `
                <i class="fa-solid fa-spinner fa-spin"></i>
                Mengimport...
            `;
        }
    );
}

document.addEventListener(
    "DOMContentLoaded",
    function () {
        initializeGroupImport();
    }
);
