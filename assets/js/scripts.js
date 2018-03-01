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
    var serial = $("#serialNumber").text();
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
            html += '                    Tecnologia - Porta '+p;
            html += '                </label>';
            html += '            </div>';
            html += '           <div class="col-sm-6">';
            html += '                <div class="input-group">';
            html += '                    <div class="input-group-prepend">';
            html += '                        <div class="input-group-text">';
            html += '                            <input type="checkbox" name="porta[]" value="UTP"';
            html += '                                   aria-label="Checkbox for following text input">';
            html += '                        </div>';
            html += '                    </div>';
            html += '                   <input type="text" class="form-control radioValue"aria-label="Text input with radio button"';
            html += '                           placeholder="UTP">';
            html += '                </div>';
            html += '            </div>';
            html += '           <div class="col-sm-6">';
            html += '                <div class="input-group">';
            html += '                    <div class="input-group-prepend">';
            html += '                        <div class="input-group-text">';
            html += '                            <input type="checkbox" name="porta[]" value="HPNA"';
            html += '                                   aria-label="Checkbox for following text input">';
            html += '                        </div>';
            html += '                    </div>';
            html += '                    <input type="text" class="form-control" aria-label="Text input with radio button"';
            html += '                           placeholder="HPNA">';
            html += '                </div>';
            html += '            </div>';

            p++;
        }
        $("#qtd_portas").html(html);

    }


});


$("#onuForm [type=button]").click(function(){

    var onu_name = $("#name").val();
    var serial_number = $("#serial").val();
    var chassi = var olts = JSON.parse($("#chassi").val());
    var olt = $("#olt");
    var selectionPorts = $("#selectionPorts").val();


    if(onu_name == '' || serial_number == '' || chassi[0] == '0' || olt == '0' || selectionPorts == '0'){

        alert(Alguns campos estao em branco);
        return;
    }


    var acao;
    if( $(this).hasClass('findMac') ){
        acao = '/dtc/find';
    }else{
        acao = '/dtc/save';
    }
    $("#onuForm").attr('action', acao);
    $("#onuForm").submit();
});