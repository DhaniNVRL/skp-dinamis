function format(command){
    document.execCommand(command, false, null);
}

function formatBlock(value){
    document.execCommand("formatBlock", false, value);
}

function insertTable(){

    let rows = prompt("Jumlah baris?");
    let cols = prompt("Jumlah kolom?");

    if(!rows || !cols) return;

    let table = "<table>";

    for(let i=0; i<rows; i++){

        table += "<tr>";

        for(let j=0; j<cols; j++){
            table += "<td>Isi</td>";
        }

        table += "</tr>";
    }

    table += "</table><br>";

    document.execCommand('insertHTML', false, table);
}