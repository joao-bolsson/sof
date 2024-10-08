/**
 *
 * @author João Bolsson (joaovictorbolsson@gmail.com)
 * @since 2017, 29 May.
 */

var statusSolicAlt = ['stAltAbertos', 'stAltAprovados', 'stAltReprovado'];
$(function () {
    $("#formPedido").submit(function (event) {
        event.preventDefault();
        var data = $(this).serialize();

        var id_pedido = document.getElementById('id_pedido').value;

        if (id_pedido == 0) {
            alert("Nenhum pedido para editar");
            return false;
        }

        document.getElementById('overlayLoad').style.display = 'block';
        document.getElementById('overlayLoadDetPed').style.display = 'block';

        $.post('../php/geral.php', data).done(function (resposta) {
            console.log(resposta);
            dropTableSolic(id_pedido);
            iniSolicitacoes(false, id_pedido);
            limpaTela();
            document.getElementById('overlayLoad').style.display = 'none';
            document.getElementById('overlayLoadDetPed').style.display = 'none';
            avisoSnack('Alterações Salvas! Pedido: ' + id_pedido);
        });
    });

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

function analisarPedido(id_pedido, id_setor) {
    $('button').blur();
    // habilita radios de status
    for (var i = 2; i <= 8; i++) {
        $('#st' + i).iCheck('enable');
    }
    $('#divTableItens').html(tableItens);
    $.post('../php/buscaLTE.php', {
        admin: 1,
        form: 'analisaPedido',
        id_pedido: id_pedido
    }).done(function (resposta) {
        document.getElementById("conteudoPedido").innerHTML = resposta;
        iniDataTable('#tableItensPedido');
        $('#tableItensPedido').DataTable().columns.adjust().draw();
    });
    document.getElementById('form').value = 'gerenciaPedido';
    document.getElementById('id_setor').value = id_setor;
    $('#tableSolicitacoes tr').css('background-color', '');
    $('#rowPedido' + id_pedido).css('background-color', '#c1df9f');
    document.getElementById('rowDetPedido').style.display = 'block';
    $.post('../php/busca.php', {
        admin: 1,
        form: 'infoPedido',
        id_pedido: id_pedido,
        id_setor: id_setor
    }).done(function (retorno) {
        var obj = jQuery.parseJSON(retorno);
        //valor do pedido
        document.getElementById('total_hidden').value = obj.valor;
        //saldo
        document.getElementById('saldo_total').value = parseFloat(obj.saldo).toFixed(3);
        document.getElementById('text_saldo_total').innerHTML = "R$ " + parseFloat(obj.saldo).toFixed(3);
        //prioridade
        document.getElementById('prioridade').value = obj.prioridade;
        //status
        $('#st' + obj.status).iCheck('check');
        if (obj.status == 2) {
            // pedido em analise deve desabilitar certas opcoes de status
            for (var i = 5; i <= 10; i++) {
                $('#st' + i).iCheck('disable');
            }
        } else if (obj.status == 7) {
            for (var i = 2; i <= 5; i++) {
                $('#st' + i).iCheck('disable');
            }
        } else if (obj.status == 5) {
            for (var i = 2; i <= 4; i++) {
                $('#st' + i).iCheck('disable');
            }
        }
        $('#st3').iCheck('enable');
        //obs
        document.getElementById('obs').value = obj.obs;
    });
    document.getElementById("id_pedido").value = id_pedido;
    document.getElementById("detPedId").innerHTML = id_pedido;
    document.getElementById('tableItensPedido').style.display = 'block';
    getNomeSetor(id_setor);
}

function getNomeSetor(id_setor) {
    $.post('../php/busca.php', {
        users: 1,
        form: 'getNomeSetor',
        id_setor: id_setor
    }).done(function (resposta) {
        document.getElementById("nomeSetorDet").innerHTML = resposta;
    });
}

function getStatus(id_pedido, id_setor) {
    limpaTela();
    for (var i = 2; i <= 8; i++) {
        $('#st' + i).iCheck('enable');
    }
    document.getElementById('tableItensPedido').style.display = 'none';
    document.getElementById('rowDetPedido').style.display = 'block';
    $('#rowPedido' + id_pedido).css('background-color', '#c1df9f');
    document.getElementById('form').value = 'altStatus';
    document.getElementById('id_setor').value = id_setor;
    document.getElementById('id_pedido').value = id_pedido;
    $.post('../php/busca.php', {
        admin: 1,
        form: 'infoPedido',
        id_pedido: id_pedido,
        id_setor: id_setor
    }).done(function (retorno) {
        var obj = jQuery.parseJSON(retorno);
        document.getElementById('text_saldo_total').innerHTML = "R$ " + parseFloat(obj.saldo).toFixed(3);
        //obs
        document.getElementById('obs').value = obj.obs;
        //status
        $('#st' + obj.status).iCheck('check');
        if (obj.status >= 5) {
            for (var i = 2; i < 5; i++) {
                $('#st' + i).iCheck('disable');
            }
        } else if (obj.status == 2) {
            // pedido em analise deve desabilitar certas opcoes de status
            for (var i = 5; i <= 10; i++) {
                $('#st' + i).iCheck('disable');
            }
        }
        $('#st3').iCheck('enable');
    });
    $('#detPedId').html(id_pedido);
    getNomeSetor(id_setor);
}

function limpaTela() {
    Pace.restart();
    $('button').blur();
    $('#divTableItens').html(tableItens);
    $('#tableSolicitacoes tr').css('background-color', '');
    $('#text_saldo_total').html('R$ 0.000');
    for (var i = 2; i <= 8; i++) {
        $('#st' + i).iCheck('disable');
    }
    $('#checkExcluir').iCheck('uncheck');
    document.getElementById('formPedido').reset();
    document.getElementById('rowDetPedido').style.display = "none";
}

// cancelar um item
function cancelaItem(id_item) {
    var cancelado = document.getElementById("item_cancelado" + id_item).value;
    // SE O ITEM JÁ ESTÁ CANCELADO, ENTÃO DESCANCELAR
    if (cancelado == "1") {
        $('#icon-cancela-item' + id_item).removeClass('fa-check').addClass('fa-close');
        $("#icon-cancela-item" + id_item).removeClass('text-green').addClass('text-red');
        document.getElementById("item_cancelado" + id_item).value = 0;
        document.getElementById("row_item" + id_item).style.backgroundColor = "";
    } else {
        // AVISA O QUE ACONTECERÁ SE O ITEM FOR REMOVIDO
        var cancel = confirm("O item de id = " + id_item + " será desativado no Banco de Dados ao final da análise do pedido atual, e portanto, os setores não poderão mais solicitá-lo. Deseja prosseguir?");
        if (cancel) {
            $('#icon-cancela-item' + id_item).removeClass('fa-close').addClass('fa-check');
            $("#icon-cancela-item" + id_item).removeClass('text-red').addClass('text-green');
            document.getElementById("item_cancelado" + id_item).value = 1;
            document.getElementById("row_item" + id_item).style.backgroundColor = "#ffe6e6";
        }
    }
}