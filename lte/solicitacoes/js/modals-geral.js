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

    var modalRascunhos = $('#listRascunhos');

    modalRascunhos.on('show.bs.modal', function () {
        $.post('../php/buscaLTE.php', {
            users: 1,
            form: 'listRascunhos'
        }).done(function (resposta) {
            $('#tbodyListRascunhos').html(resposta);
        });
    });

    modalRascunhos.on('shown.bs.modal', function () {
        iniDataTable('#tableListRascunhos');
    });

    modalRascunhos.on('hidden.bs.modal', function () {
        $('#tbodyListRascunhos').html("");
        $('#tableListRascunhos').DataTable().destroy();
    })
});

function deletePedido(id_pedido) {
    var confirma = confirm('Todos os registros referentes à esse pedido serão excluído do sistema para economizar espaço ;) Deseja prosseguir?');
    if (!confirma) {
        return;
    }
    $.post('../php/geral.php', {
        users: 1,
        form: 'deletePedido',
        id_pedido: id_pedido
    }).done(function (resposta) {
        if (resposta != "true") {
            alert(resposta);
        } else {
            avisoSnack('Pedido deletado com sucesso !');
            $('#tableListRascunhos').DataTable().destroy();
            $('#tbodyListRascunhos').html('');
        }
    });
    $('button').blur();
    $('#listRascunhos').modal('hide');
}

function listAdiantamentos() {
    $('#listAdiantamentos').modal();
}

function listSolicAltPedidos() {
    $('#listSolicAltPedidos').modal();
}

function listRascunhos() {
    $('#listRascunhos').modal();
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