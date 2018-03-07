// Empty JS for your own code to be here

$("#chassi").on("change", function () {

    //console.log(JSON.parse($(this).val()));
    var olts = JSON.parse($(this).val())
    var html = "";
    //html += "<option>Selecione a placa</option>";

    for (i = 1; i <= olts[1]; i++) {
        html += "<option value=" + i + ">" + i + "</option>";
    }
    if (olts === 0) html += "<option value='0'>Selecione um chassi</option>";

    $("#olt").html(html);

});


$("#serialButton").on('click', function () {
    var serialField = $("#serial");
    var serial = $(this).text();
    serialField.val(serial.trim());

    $("#serialNumber").hide();

});
var portas = 1;
$("#selectionPorts").on('change', function () {

    var valorPorta = $(this).val();

    //alert(valorPorta+" "+portas);

    if (valorPorta != 0) {
        var html = "";
        var p = 1;
        for (var i = 0; i < valorPorta; i++) {
            html += '     <div class="col-md-12">';
            html += '                <label for="tecnologia">';
            html += '                    Tecnologia - Porta ' + p;
            html += '                </label>';
            html += '            </div>';
            html += '           <div class="col-sm-6">';
            html += '                <div class="input-group">';
            html += '                    <div class="input-group-prepend">';
            html += '                        <div class="input-group-text">';
            html += '                            <input type="checkbox" name="porta[]" value="UTP" aria-label="Checkbox for following text input">';
            html += '                        </div>';
            html += '                    </div>';
            html += '                   <input type="text" class="form-control radioValue"aria-label="Text input with radio button" placeholder="UTP">';
            html += '                </div>';
            html += '            </div>';
            html += '           <div class="col-sm-6">';
            html += '                <div class="input-group">';
            html += '                    <div class="input-group-prepend">';
            html += '                        <div class="input-group-text">';
            html += '                           <input type="checkbox" name="porta[]" value="HPNA" aria-label="Checkbox for following text input">';
            html += '                        </div>';
            html += '                    </div>';
            html += '                    <input type="text" class="form-control" aria-label="Text input with radio button" placeholder="HPNA">';
            html += '                </div>';
            html += '            </div>';

            p++;
        }
        $("#qtd_portas").html(html);

    }


});


$("#onuForm [type=button]").click(function () {

    var onu_name = $("#name").val();
    var serial_number = $("#serial").val();
    var chassi = JSON.parse($("#chassi").val());
    var olt = $("#olt");
    var checkeds = new Array();
    $("input[name='porta[]']:checked").each(function () {

        checkeds.push($(this).val());
    });
    console.log(chassi);
    console.log(checkeds);

    var acao;
    if ($(this).hasClass('findMac')) {

        if (chassi == '0' || olt == '0') {
            alert('Selecione o CHASSI e PLACA por favor!');
            return;
        }
        acao = '/dtc/find';
    }
    else {
        if (onu_name == '' || serial_number == '' || chassi == '0' || olt == '0' || checkeds.length === 0) {
            alert('Alguns campos estao em branco ou não ha porta selecionada!!');
            return;
        }
        acao = '/dtc/save';
    }
    $("#onuForm").attr('action', acao);

    $("#onuForm").submit();
});


if ($("#chassiNumber").text() != '') {

    var chassiValue = $("#chassi").val($("#chassiNumber").text().trim());
    var mValue = JSON.parse($("#chassi").val());
    console.log(mValue + " " + chassiValue);
    console.log($("#chassiNumber").text().trim());

    $('#olt').html($('<option>', {
        value: mValue['1'],
        text: mValue['1']
    }));
}
//console.log($("#chassiNumber").text());
//console.log($("#oltNumber").text());