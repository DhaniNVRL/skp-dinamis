class DynamicTable {

    constructor() {

        document.addEventListener(
            "click",
            (e) => {


                const addButton =
                    e.target.closest("[data-add-row]");


                if (addButton) {

                    this.addRow(
                        addButton.dataset.addRow
                    );

                }



                const removeButton =
                    e.target.closest("[data-remove-row]");


                if (removeButton) {


                    const row =
                        removeButton.closest("tr");


                    const table =
                        row.closest("[data-dynamic-table]");


                    if (!table) return;



                    const rows =
                        table.querySelectorAll(
                            "tbody tr"
                        );



                    if(rows.length <= 1){

                        this.showAlert(
                            "Minimal harus ada 1 data user."
                        );

                        return;

                    }



                    row.remove();


                    this.updateNumber(table);


                }


            }
        );

    }


    addRow(tableId) {


        const table =
            document.getElementById(tableId);


        if (!table) {

            console.error(
                "Table tidak ditemukan:",
                tableId
            );

            return;

        }


        const templateId =
            table.dataset.template;


        const template =
            document.getElementById(templateId);


        if (!template) {

            console.error(
                "Template tidak ditemukan:",
                templateId
            );

            return;

        }


        const row =
            template.content.cloneNode(true);


        table
            .querySelector("tbody")
            .appendChild(row);


        this.updateNumber(table);

    }



    updateNumber(table) {


        table
        .querySelectorAll("tbody tr")
        .forEach((row,index)=>{


            const number =
                row.querySelector("[data-row-number]");


            if(number){

                number.textContent =
                    index + 1;

            }


        });


    }

    showAlert(message){

        alert(message);

    }

}


window.DynamicTable = new DynamicTable();