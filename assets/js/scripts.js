// Empty JS for your own code to be here

$("#chassi").on("change", function () {

    //console.log(JSON.parse($(this).val()));
    var olts = JSON.parse($(this).val());
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


$("#onuForm [type=button]").click(function () {

    var onu_name = $("#name").val();
    var serial_number = $("#serial").val();
    var chassi = JSON.parse($("#chassi").val());
    var olt = $("#olt");
    var checkeds = new Array();
    var pId = new Array();
    $("input[name='porta[]']:checked").each(function () {
        checkeds.push($(this).val());
    });

    $("input[name='porta_id[]']:checked").each(function () {
        pId.push($(this).val());
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
        if (onu_name == '' || serial_number == '' || chassi == '0' || olt == '0' || checkeds.length === 0 || pId.length === 0) {
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

$("#onuButton").click(function () {

    var onuName = $("#onuName");
    var html = '';

    html += '<div class="alert alert-dismissable alert-warning" >';
    html += '           <button type="button" class="close" data-dismiss="alert" aria-hidden="true">';
    html += '                 ×';
    html += '             </button>';
    html += '             <h4>';
    html += '                 Ops!';
    html += '             </h4>';
    html += '            <strong>Atenção!</strong> você precisa selecionar uma ONU';
    html += '         </div>';

    if (onuName.val() === 'selecione') {
        $("#changeAlert").html(html);
        onuName.focus();
        onuName.css('color', 'red');
        return;
    }
    $("#formOnu").submit();

});

$("#changeButton").click(function () {

    var serial = $("#serial-number").val();
    var html = '';
    var s = $("#serial-number");
    html += '<div class="alert alert-dismissable alert-warning" >';
    html += '           <button type="button" class="close" data-dismiss="alert" aria-hidden="true">';
    html += '                 ×';
    html += '             </button>';
    html += '             <h4>';
    html += '                 Ops!';
    html += '             </h4>';
    html += '            <strong>Atenção!</strong> Confirme o numero de serie digitado';
    html += '         </div>';

    //var c = confirm("Confirmar troca do serial de "+onuName+ " para "+serial);

    if (serial.length < 12) {
        $("#changeAlert").html(html);
        s.focus();
        s.css('color', 'red');
        return;
    }
    $('#changeOnu').submit();
    //return c; //you can just return c because it will be true or false
});

$("#activeOnu [type=button]").click(function () {

    var onuName = $("#onuName");
    var html = '';
    var acao;





    if ($(this).hasClass('selectButton')) {

        acao = '/dtc/find';
    }
    else {
        if (onu_name == '' || serial_number == '' || chassi == '0' || olt == '0' || checkeds.length === 0 || pId.length === 0) {
            alert('Alguns campos estao em branco ou não ha porta selecionada!!');
            return;
        }
        acao = '/dtc/save';
    }
    $("#onuForm").attr('action', acao);

    $("#onuForm").submit();
});
