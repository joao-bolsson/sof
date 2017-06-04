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
    });

    var modalPedidos = $('#listPedidos');

    modalPedidos.on('show.bs.modal', function () {
        $.post('../php/buscaLTE.php', {
            users: 1,
            form: 'listPedidos'
        }).done(function (resposta) {
            $('#tbodyListPedidos').html(resposta);
        });
    });

    modalPedidos.on('shown.bs.modal', function () {
        iniDataTable('#tableListPedidos');
    });

    modalPedidos.on('hidden.bs.modal', function () {
        $('#tbodyListPedidos').html("");
        $('#tableListPedidos').DataTable().destroy();
    });

});

function listPedidos() {
    $('#listPedidos').modal();
}

/**
 * Action do formulário para carregar mais pedidos (Meus Pedidos).
 */
function loadMoreRequests() {
    document.getElementById('overlayLoad').style.display = 'block';
    var limit1 = 0, limit2 = 0;
    var input = document.getElementById('limit1');
    if (input !== null) {
        limit1 = document.getElementById('limit1').value;
        limit2 = document.getElementById('limit2').value;
    }
    var element = document.getElementById('tbodyListPedidos');
    if (element.innerHTML.length > 0) {
        $('#tableListPedidos').DataTable().destroy();
    }
    var pedidos = getIdsRequest();
    $.post('../php/buscaLTE.php', {
        users: 1,
        form: 'listPedidos',
        limit1: limit1,
        limit2: limit2,
        pedidos: pedidos
    }).done(function (resposta) {
        if (resposta.length > 0) {
            element.innerHTML += resposta;
        }
        iniDataTable('#tableListPedidos');
    });
    $('button').blur();
    $('#loadMoreCustom').modal('hide');
    document.getElementById('overlayLoad').style.display = 'none';
}

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