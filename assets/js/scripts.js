// Empty JS for your own code to be here


//Populando o select de placas
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

//adicionando o valor do borao do serial ao campo serial
$("#serialButton").on('click', function () {
    var serialField = $("#serial");
    var serial = $(this).text();
    serialField.val(serial.trim());

    $("#serialNumber").hide();

});


//definindo via codigo a action do form da pagina de congidurarção das ONUs
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
        acao = '/pnexdtc/find';
    }
    else {
        if (onu_name == '' || serial_number == '' || chassi == '0' || olt == '0' || checkeds.length === 0 || pId.length === 0) {
            alert('Alguns campos estao em branco ou não ha porta selecionada!!');
            return;
        }
        acao = '/pnexdtc/save';
    }
    $("#onuForm").attr('action', acao);
    $("#modal-container-486491").modal();
    $("#onuForm").submit();
});


//passando para os selects os valores vindo da consulta no banco
if ($("#chassiNumber").text() != '') {

    var chassiValue = $("#chassi").val($("#chassiNumber").text().trim());
    var mValue = JSON.parse($("#chassi").val());
    var oltNumber = $("#mOltNumber").text().trim();
    console.log(mValue + " " + chassiValue);
    console.log($("#chassiNumber").text().trim());

    $('#olt').html($('<option>', {
        //value: mValue['1'],
        //text: mValue['1']
        value: oltNumber,
        text: oltNumber
    }));
}

// verificando campos nao selecionados
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
    console.log("buscando mac!!");
    $("#modal-container-486491").modal();

    $("#formOnu").submit();

});

//verificando numero de serie digitado
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

    if (serial.length < 11) {
        $("#changeAlert").html(html);
        s.focus();
        s.css('color', 'red');
        return;
    }

    $("#modal-container-486491").modal();

    $('#changeOnu').submit();
    //return c; //you can just return c because it will be true or false
});


$("#activeOnu [type=button]").click(function () {

    var onuName = $("#onuName");
    var html = '';
    var acao;


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


    if ($(this).hasClass('selectButton')) {

        acao = '/pnexdtc/portas';
    }
    else {

        acao = '/pnexdtc/active';
    }
    $("#activeOnu").attr('action', acao);

    $("#activeOnu").submit();
});


$(".checkOnu").on('change', function () {

    $("#onuHtmData").html('');

});

//enviando dados via ajax para ativação e desativação de portas
//$("input[type='checkbox']").on('click', function () {
$(".activateCheckbox").on('click', function () {

    var result = '';
    $("#modal-container-486491").modal();
    $("#changeAlert").html('');

    var id = $(this).val();
    var url = '/pnexdtc/active';

    $("input[type='checkbox']").attr('disabled', true);
    $("#selectButton").attr('disabled', true);


    if ($(this).is(':checked')) {

        sendData(url, id, 'enable');
        console.log('enable port');

    }
    else {

        result = sendData(url, id, 'disable');
        console.log('disable port');

    }



});

//function em ajax para enviar dados via post
function sendData(url, id, stringAction) {
    var html = '';


    $.ajax({
        url: url,
        type: "POST",
        method: "POST",
        data: {
            'id': id,
            'action': stringAction
        },
        dataType: 'json',
        //processData: false,
        //contentType: false,
        success: function (retorno) {

            if (retorno.result === 'success') {

                html += ' <div class="alert alert-dismissable alert-success">';
                html += '                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">';
                html += '                           ×';
                html += '                        </button>';
                html += '                        <h4>';
                html += '                            Tudo certo!!!';
                html += '                        </h4> <strong>Porta habilitada / desabilitada com sucesso!</strong>';
                html += '                    </div>';

            }
            else {

                html += ' <div class="alert alert-dismissable alert-danger">';
                html += '                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">';
                html += '                           ×';
                html += '                        </button>';
                html += '                        <h4>';
                html += '                            OPS!!!';
                html += '                        </h4> <strong>' + retorno.msg + '</strong>';
                html += '                    </div>'
                $("#onuHtmData").html('');

            }

            $("#modal-container-486491").modal('hide');
            $("#changeAlert").html(html);
            $("input[type='checkbox']").removeAttr('disabled');
            $("#selectButton").removeAttr('disabled');

            return retorno.result;
        }

    });
}

