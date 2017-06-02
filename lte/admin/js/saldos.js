/**
 *
 * @author João Bolsson (joaovictorbolsson@gmail.com)
 * @since 2017, 27 May.
 */

$(function () {

    var transfModal = $("#transferencia");
    transfModal.on('shown.bs.modal', function () {
        getSaldoOri();
    });

    transfModal.on('hidden.bs.modal', function () {
        document.getElementById('formTransferencia').reset();
    });

    $("#formTransferencia").submit(function (event) {
        event.preventDefault();
        var data = $(this).serialize();

        $.post('../php/geral.php', data).done(function (resposta) {
            if (resposta === 'success') {
                alert('O valor foi transferido com SUCESSO! ');
            } else {
                alert('Saldo insuficiente para realizar a transferência');
            }
        }).always(function () {
            $('#tableListLancamentos').DataTable().destroy();
            $('#transferencia').modal('hide');
            document.getElementById('formTransferencia').reset();
        });
    });

    $("#formLiberaSaldo").submit(function (event) {
        event.preventDefault();
        var data = $(this).serializeArray();

        var valor = 0;
        // valor must have 3 decimals
        for (var i = 0; i < data.length; i++) {
            if (data[i].name === 'valor') {
                data[i].value = parseFloat(data[i].value).toFixed(3);
                valor = data[i].value;
            }
        }

        $.post('../php/geral.php', data).done(function (resposta) {
            if (resposta) {
                alert('O valor de R$ ' + valor + ' foi acrescentado ao saldo do setor com SUCESSO.');
                location.reload();
            } else {
                alert('Ocorreu um erro no servidor. Contate o administrador.');
                window.location.href = 'sair.php';
            }
        });

    });

});

function putNumberFormat(value) {
    $.post('../php/busca.php', {
        users: 1,
        form: 'number_format',
        value: value
    }).done(function (resposta) {
        $('#saldoDispOri').html('Saldo disponível: R$ ' + resposta);
    });
}

function getSaldoOri() {
    var select = document.getElementById('transfOri');
    var setorOri = select.options[select.selectedIndex].value;
    $.post('../php/busca.php', {
        admin: 1,
        form: 'getSaldoOri',
        setorOri: setorOri
    }).done(function (resposta) {
        putNumberFormat(resposta);
        $("input[name='valor']", "#formTransferencia").attr('max', resposta);
    });
}

function refreshDataSaldo(id_setor) {
    $.post('../php/busca.php', {
        users: 1,
        form: 'refreshTotSaldos',
        id_setor: id_setor
    }).done(function (resposta) {
        var obj = jQuery.parseJSON(resposta);
        $('#totIn').html(obj.entrada);
        $('#totOut').html(obj.saida);
    });
}
