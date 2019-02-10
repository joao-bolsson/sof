/**
 *
 * @author João Bolsson (joaovictorbolsson@gmail.com)
 * @since 2017, 27 May.
 */
var statusSaldos = ['stabertos', 'staprovados', 'streprovado'];
$(function () {

    var transfModal = $("#transferencia");
    transfModal.on('shown.bs.modal', function () {
        getSaldoOri();
        var id_dest = document.getElementById('transfDest').value;
        fillTransfSource(id_dest);
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

        $.post('../php/geral.php', data).done(function () {
            alert('O valor de R$ ' + valor + ' foi acrescentado ao saldo do setor com SUCESSO.');
            location.reload();
        });

    });

    for (var i = 0; i < statusSaldos.length; i++) {
        var element = document.getElementById(statusSaldos[i]);
        if (element !== null) {
            var checkEl = $('#' + statusSaldos[i]);
            checkEl.iCheck({
                checkboxClass: 'icheckbox_minimal-blue',
                radioClass: 'iradio_minimal-blue'
            });
            checkEl.on('ifChecked', function () {
                iniTableSolicAdiant();
            });
        }
    }

});

function fillTransfSource(id_dest) {
    $.post('../php/busca.php', {
        admin: 1,
        form: 'fillTransfSource',
        id: id_dest
    }, function (resposta) {
        var selectSource = $('#transfSource');
        selectSource.html(resposta);
        $(".select2", "#transferencia").select2({
            dropdownParent: $("#transferencia")
        });
    });
}

function changeTransfDest(element) {
    fillTransfSource(element.value);
}

function mostraSolicAdiant() {
    mostra('rowSolicAdi');
    iniTableSolicAdiant();
}

function iniTableSolicAdiant() {
    var st = null;
    for (var i = 0; i < statusSaldos.length; i++) {
        var element = document.getElementById(statusSaldos[i]);
        if (element.checked) {
            st = element.value;
        }
    }
    $.post('../php/buscaLTE.php', {
        admin: 1,
        form: 'tableSolicitacoesAdiantamento',
        status: st
    }).done(function (resposta) {
        if ($.fn.DataTable.isDataTable('#tableSolicitacoesAdiantamento')) {
            $('#tableSolicitacoesAdiantamento').DataTable().destroy();
        }
        document.getElementById('conteudoSolicitacoesAdiantamento').innerHTML = resposta;
        iniDataTable('#tableSolicitacoesAdiantamento');
    });
}

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

function undoFreeMoney(id_lancamento) {
    $.post('../php/geral.php', {
        admin: 1,
        form: 'undoFreeMoney',
        id_lancamento: id_lancamento
    }).done(function () {
        location.reload();
    });
}

function analisaAdi(id, acao) {
    $('a').blur();
    var aprova;
    if (acao) {
        aprova = confirm("O setor receberá o valor adiantado e terá esse mesmo valor descontado no próximo mês, estando sujeito à ficar com saldo negativo.\n\nDeseja continuar?");
    } else {
        aprova = confirm("A solicitação de adiantamento será reprovada e o saldo do setor não será alterado.\n\nDeseja continuar?");
    }
    if (aprova) {
        $.post('../php/geral.php', {
            admin: 1,
            form: 'aprovaAdi',
            id: id,
            acao: acao
        }).done(function () {
            $('#tableListLancamentos').DataTable().destroy();
            iniTableSolicAdiant();
        });
    }
}
