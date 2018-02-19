// Empty JS for your own code to be here

$("#chassi").on("change", function () {
    var olts = $(this).val().split('|');
    var html = "";
    html += "<option>Selecione a placa</option>";

    for (i = 1; i <= olts[1]; i++) {
        html += "<option value=" + i + ">" + i + "</option>";
    }
    $("#olt").html(html);

});



