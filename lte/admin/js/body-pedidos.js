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