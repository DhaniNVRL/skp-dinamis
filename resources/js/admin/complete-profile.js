document.addEventListener(
    "modal:opened",
    (e)=>{

        if(e.detail.id !== "createCprofileModal")
            return;


        const table =
            document.getElementById(
                "cprofileCreateTable"
            );


        if(
            table &&
            table.querySelectorAll("tbody tr").length === 0
        ){

            DynamicTable.addRow(
                "cprofileCreateTable"
            );

        }

    }
);



document.addEventListener("click", function (e) {

    const button = e.target.closest(
        '[data-modal-open="editCprofileModal"]'
    );

    if (!button) return;


    const id = button.dataset.id;
    const pgroup = button.dataset.pgroup;
    const punit = button.dataset.punit;
    const action = button.dataset.action;


    const idInput =
        document.getElementById("edit_cprofile_id");

    const groupInput =
        document.getElementById("edit_cprofile_pgroup");

    const unitInput =
        document.getElementById("edit_cprofile_punit");

    const form =
        document.getElementById("editCprofileForm");


    if (
        !idInput ||
        !groupInput ||
        !unitInput ||
        !form
    ) {
        console.error(
            "Element modal edit Complete Profile tidak ditemukan."
        );

        return;
    }


    idInput.value = id ?? "";
    groupInput.value = pgroup ?? "";
    unitInput.value = punit ?? "";

    form.action = action ?? "";

});

document.addEventListener("click", function(e){
    const button = e.target.closest(
        '[data-modal-open="deleteCprofileModal"]'
    );


    if(!button)
        return;


    console.log("DELETE CP:", button.dataset.id);


    document.getElementById(
        "deleteCprofileName"
    ).textContent =
        button.dataset.name;


    document.getElementById(
        "deleteForm"
    ).action =
        `/cprofile/${button.dataset.id}`;

});

