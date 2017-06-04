/**
 *
 * @author João Bolsson (joaovictorbolsson@gmail.com)
 * @since 2017, 31 May.
 */

$(function () {
    var modalAltPed = $("#listSolicAltPedidos");

    modalAltPed.on('show.bs.modal', function () {
        $.post('../php/buscaLTE.php', {
            users: 1,
            form: 'iniSolicAltPedSetor'
        }).done(function (resposta) {
            $('#tbodySolicAltPedido').html(resposta);
        });
    });

    modalAltPed.on('shown.bs.modal', function () {
        iniDataTable('#tableSolicAltPedido');
    });

    modalAltPed.on('hidden.bs.modal', function () {
        $('#tbodySolicAltPedido').html("");
        $('#tableSolicAltPedido').DataTable().destroy();
    });

    var modalAdi = $('#listAdiantamentos');

    modalAdi.on('show.bs.modal', function () {
        $.post('../php/buscaLTE.php', {
            users: 1,
            form: 'listAdiantamentos'
        }).done(function (resposta) {
            $('#tbodyListAdiantamentos').html(resposta);
        });
    });

    modalAdi.on('shown.bs.modal', function () {
        iniDataTable('#tableListAdiantamentos');
    });

    modalAdi.on('hidden.bs.modal', function () {
        $('#tbodyListAdiantamentos').html("");
        $('#tableListAdiantamentos').DataTable().destroy();
    });
});

function listAdiantamentos() {
    $('#listAdiantamentos').modal();
}

function listSolicAltPedidos() {
    $('#listSolicAltPedidos').modal();
}

function listRascunhos() {
    if (document.getElementById('tbodyListRascunhos').innerHTML.length === 0) {
        $.post('../php/buscaLTE.php', {
            users: 1,
            form: 'listRascunhos'
        }).done(function (resposta) {
            $('#tbodyListRascunhos').html(resposta);
        });
    }
    $('#listRascunhos').modal('show');
}

/**
 * Form action.
 */
function formEnvia() {
    var id_pedido = document.getElementById("id_pedido_alt").value;
    var justificativa = document.getElementById("justificativa_alt_ped").value;
    $.post('../php/geral.php', {
        users: 1,
        form: 'alt_pedido',
        id_pedido: id_pedido,
        justificativa: justificativa
    }).done(function (resposta) {
        alert(resposta);
        $('#alt_pedido').modal('hide');
    });
}

function solicAltPed(id_pedido) {
    document.getElementById('id_pedido_alt').value = id_pedido;
    abreModal('#alt_pedido');
}