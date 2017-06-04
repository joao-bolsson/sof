/**
 *
 * @author João Bolsson (joaovictorbolsson@gmail.com)
 * @since 2017, 29 May.
 */

var statusSolicAlt = ['stAltAbertos', 'stAltAprovados', 'stAltReprovado'];
$(function () {
    var excluir = document.getElementById('checkExcluir');
    if (excluir !== null) {
        $('#checkExcluir').iCheck({
            checkboxClass: 'icheckbox_minimal-blue',
            radioClass: 'iradio_minimal-blue'
        });
    }

    for (var i = 0; i < statusSolicAlt.length; i++) {
        var element = document.getElementById(statusSolicAlt[i]);
        if (element !== null) {
            var checkSt = $('#' + statusSolicAlt[i]);
            checkSt.iCheck({
                checkboxClass: 'icheckbox_minimal-blue',
                radioClass: 'iradio_minimal-blue'
            });
            checkSt.on('ifChecked', function () {
                iniTableSolicAltPed();
            });
        }
    }

    for (var j = 2; j <= 10; j++) {
        // radios dos detalhes do pedido
        var checkStDetPed = document.getElementById('st' + j);
        if (checkStDetPed !== null) {
            $('#st' + j).iCheck({
                checkboxClass: 'icheckbox_minimal-blue',
                radioClass: 'iradio_minimal-blue'
            });
        }
    }
});

function enviaOrdenador(id_pedido) {
    if (confirm("Mudar o status do pedido para \"Enviado ao Ordenador\"?")) {
        dropTableSolic(id_pedido);
        $.post('../php/geral.php', {
            admin: 1,
            form: 'enviaOrdenador',
            id_pedido: id_pedido
        }).done(function (resposta) {
            if (resposta) {
                iniSolicitacoes(false, id_pedido);
            } else {
                alert('Ocorreu um erro no servidor. Contate o administrador.');
            }
        });
    }
}

function analisaSolicAlt(id_solic, id_pedido, acao) {
    $.post('../php/geral.php', {
        admin: 1,
        form: 'analisaSolicAlt',
        id_solic: id_solic,
        id_pedido: id_pedido,
        acao: acao
    }).done(function (resposta) {
        if (resposta == false) {
            alert(resposta);
            window.location.href = 'index.php';
        } else {
            iniSolicitacoes(false, 0);
            iniTableSolicAltPed();
        }
    });
}

function iniTableSolicAltPed() {
    var table = 'tableSolicAltPedido';
    var st = null;
    for (var i = 0; i < statusSolicAlt.length; i++) {
        var element = document.getElementById(statusSolicAlt[i]);
        if (element.checked) {
            st = element.value;
        }
    }
    $.post('../php/buscaLTE.php', {
        admin: 1,
        form: 'iniTableSolicAltPed',
        status: st
    }).done(function (resposta) {
        if ($.fn.DataTable.isDataTable('#' + table)) {
            $('#' + table).DataTable().destroy();
        }
        document.getElementById('contSolicAltPedido').innerHTML = resposta;
        iniDataTable('#' + table);
    });
}