$(function () {

    $(document).ajaxStart(function () {
        Pace.restart();
    });

    $("#formPedido").submit(function () {
        var data = $(this).serialize();

        var id_pedido = document.getElementById('id_pedido').value;

        if (id_pedido == 0) {
            alert("Nenhum pedido para editar");
            return false;
        }

        document.getElementById('overlayLoad').style.display = 'block';
        document.getElementById('overlayLoadDetPed').style.display = 'block';
        $.ajax({
            type: "POST",
            url: "../php/geral.php",
            data: data,
            success: function () {
                dropTableSolic(id_pedido);
                iniSolicitacoes(false, id_pedido);
                limpaTela();
                document.getElementById('overlayLoad').style.display = 'none';
                document.getElementById('overlayLoadDetPed').style.display = 'none';
                avisoSnack('Alterações Salvas! Pedido: ' + id_pedido);
            }
        });

        return false;
    });

    $("#formEditRegItem").submit(function () {
        var data = $(this).serialize();

        $.ajax({
            type: "POST",
            url: "../php/geral.php",
            data: data,
            success: function () {
                $('#infoItem').modal('hide');
                var element = document.getElementById('editmode');
                if (element !== null) {
                    var proc = document.getElementById('numProc').innerHTML;
                    pesquisarProcesso(proc);
                } else {
                    limpaTela();
                    iniSolicitacoes(false, 0);
                }
            }
        }).done(function (resposta) {
            if (resposta == 'fail') {
                alert('Conflito: a nova quantidade do contrato ou a nova quantidade utilizada são menores que a quantidade utilizada pelos pedidos desse item. A edição não foi concluída');
            }
        });

        return false;
    });

    tableItens = '';
    var ta = document.getElementById('divTableItens');
    if (ta !== null) {
        tableItens = ta.innerHTML;
    }

    var modalInfoItem = $('#infoItem');
    modalInfoItem.on('shown.bs.modal', function () {
        $(".date").inputmask("dd/mm/yyyy", {"placeholder": "dd/mm/yyyy"});
    });

    modalInfoItem.on('hidden.bs.modal', function () {
        document.getElementById('id').value = 0;
        document.getElementById('formEditRegItem').reset();
    });

    $('#listProcessos').on('shown.bs.modal', function () {
        if (!$.fn.DataTable.isDataTable('#tableListProcessos')) {
            iniDataTable('#tableListProcessos');
        }
    });

    $('#listLancamentos').on('shown.bs.modal', function () {
        if (!$.fn.DataTable.isDataTable('#tableListLancamentos')) {
            iniDataTable('#tableListLancamentos');
        }
    });

});

function relListUsers() {
    window.open("../admin/printRelatorio.php?relatorio=1&tipo=users");
}

function mostraPed() {
    $('button').blur();
    hideDivs();
    document.getElementById('rowPedidos').style.display = 'block';
    iniSolicitacoes(false, 0);
}

function mostraSolicAltPed() {
    mostra('rowAltPed');
    iniTableSolicAltPed();
}

function iniAdminSolicitacoes() {
    $.post('../php/busca.php', {
        admin: 1,
        form: 'permissoes'
    }).done(function (resposta) {
        var permissao = jQuery.parseJSON(resposta);
        if (permissao.recepcao == 1) {
            iniRecepcao();
        }
        if (permissao.pedidos == 1) {
            iniSolicitacoes(false, 0);
        }
    });
}

function loadMore() {
    $('button').blur();
    $('#loadMoreCustom').modal('hide');
    iniSolicitacoes(true);
}

function listLancamentos(id_setor) {
    $('#listLancamentos').modal('show');
    if (id_setor != null) {
        changeSetor(id_setor);
    }
}

function listProcessos(permissao) {
    if (document.getElementById('tbodyListProcessos').innerHTML.length > 0) {
        $('#tableListProcessos').DataTable().destroy();
    }
    if (permissao == 'users') {
        $.post('../php/buscaLTE.php', {
            users: 1,
            form: 'listProcessos'
        }).done(function (resposta) {
            $('#tbodyListProcessos').html(resposta);
        });
    } else if (permissao == 'admin') {
        $.post('../php/buscaLTE.php', {
            admin: 1,
            form: 'listProcessos'
        }).done(function (resposta) {
            $('#tbodyListProcessos').html(resposta);
        });
    }
    $('#listProcessos').modal('show');
}

function getIdsRequest() {
    var element = document.getElementById('tbodyListPedidos');
    var pedidos = [];
    if (element === null) {
        console.log('tbodyListPedidos nao existe');
        return pedidos;
    } else if (element.innerHTML.length == 0) {
        console.log('tabela vazia');
        return pedidos;
    }
    var rows = element.rows;
    var len = rows.length;
    for (var i = 0; i < len; i++) {
        var id = rows[i].id;
        id = id.replace('ped', '');
        pedidos.push(id);
    }
    return pedidos;
}

function checkItemPedido(id_item, vl_unitario, qt_saldo) {
    var qtd_item = document.getElementById('qtd' + id_item).value;
    var itens = document.getElementsByClassName('classItens');
    for (var i = 0; i < itens.length; i++) {
        if (itens[i].value == id_item) {
            avisoSnack('Esse item já está contido no pedido. Verifique!');
            return;
        }
    }
    if (qtd_item <= 0) {
        document.getElementById("qtd" + id_item).style.border = "0.12em solid red";
        document.getElementById("qtd" + id_item).focus();
    } else {
        //limpando os campos
        document.getElementById("qtd" + id_item).style.border = "none";
        document.getElementById("qtd" + id_item).value = "";
        //verifica se a qtd solicitada está disponível
        if (qtd_item > qt_saldo) {
            avisoSnack('QUANTIDADE INDISPONÍVEL !');
        } else {
            var valor = parseFloat(qtd_item * vl_unitario).toFixed(3);
            var total_pedido = document.getElementById('total_hidden').value;
            total_pedido += valor;
            saldo_total = parseFloat(document.getElementById("saldo_total").value);
            if (valor > saldo_total) {
                avisoSnack('SALDO INSUFICIENTE !');
            } else {
                addItemPedido(id_item, qtd_item, vl_unitario);
            }
        }
    }
}

function addItemPedido(id_item, qtd, vl_unitario) {
    //valor do pedido
    var valor = qtd * vl_unitario;
    var t = document.getElementById('total_hidden').value;
    var total = parseFloat(t) + parseFloat(valor);
    document.getElementById('total_hidden').value = parseFloat(total).toFixed(3);
    var tot_str = parseFloat(total).toFixed(3);
    $.post('../php/busca.php', {
        users: 1,
        form: 'number_format',
        value: tot_str
    }).done(function (resposta) {
        document.getElementById('total').value = "R$ " + resposta;
    });
    //saldo
    var s = document.getElementById('saldo_total').value;
    var saldo_total = parseFloat(s) - parseFloat(valor);
    document.getElementById('saldo_total').value = parseFloat(saldo_total).toFixed(3);
    $('#text_saldo_total').html('R$ ' + parseFloat(saldo_total).toFixed(3));
    $.post('../php/buscaLTE.php', {
        users: 1,
        form: 'addItemPedido',
        id_item: id_item,
        qtd: qtd
    }).done(function (resposta) {
        var conteudoPedido = document.getElementById('conteudoPedido').innerHTML;
        $('#conteudoPedido').html(conteudoPedido + resposta);
        avisoSnack('Item Inserido ao Pedido !');
    });
}

function removeTableRow(id_item, valor) {
    //valor do pedido
    var t = document.getElementById('total_hidden').value;
    var total = parseFloat(t) - parseFloat(valor);
    document.getElementById('total_hidden').value = parseFloat(total).toFixed(3);
    document.getElementById('total').value = "R$ " + parseFloat(total).toFixed(3);
    //saldo
    var s = document.getElementById('saldo_total').value;
    var saldo_total = parseFloat(s) + parseFloat(valor);
    document.getElementById('saldo_total').value = parseFloat(saldo_total).toFixed(3);
    document.getElementById('text_saldo_total').innerHTML = "R$ " + parseFloat(saldo_total).toFixed(3);
    var row = document.getElementById("row" + id_item);
    if (row.parentNode) {
        row.parentNode.removeChild(row);
    }
    avisoSnack('Item Removido do Pedido !');
}

function showInformation(table, column, id) {
    $.post('../php/buscaLTE.php', {
        users: 1,
        form: 'showInformation',
        table: table,
        column: column,
        id: id
    }).done(function (resposta) {
        viewCompl(resposta);
    });
}

function viewCompl(texto) {
    $('#complementoItem').html(texto);
    $('#viewCompl').modal();
}

function pesquisarProcesso(busca) {
    document.getElementById('overlayLoad').style.display = 'block';
    console.log(busca);
    $('#listProcessos').modal('hide');
    $.post('../php/buscaLTE.php', {
        users: 1,
        form: 'pesquisarProcesso',
        busca: busca
    }).done(function (resposta) {
        $('#tableProcessos').DataTable().destroy();
        $('#conteudoProcesso').html(resposta);
        iniDataTable('#tableProcessos');
        document.getElementById('numProc').innerHTML = busca;
        document.getElementById('overlayLoad').style.display = 'none';
        avisoSnack('Busca Realizada com Sucesso !');
    });
}

function btnPesquisa() {
    if (document.getElementById("pesquisa").style.display == "" ||
        document.getElementById("pesquisa").style.display == "none") {
        $("#pesquisa").slideDown(200);
    } else {
        $("#pesquisa").slideUp(200);
    }
}

function imprimir(id_pedido) {
    $('button').blur();
    if (id_pedido == 0) {
        id_pedido = document.getElementById("pedido").value;
    }
    $.post('../php/buscaLTE.php', {
        users: 1,
        form: 'imprimirPedido',
        id_pedido: id_pedido
    }).done(function () {
        window.open("../view/printPedido.php");
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

function editaItem(id_item) {
    console.log('Edit: ' + id_item);
    $('button').blur();
    $('#infoItem').modal();
    document.getElementById('id').value = id_item;
    $.post('../php/busca.php', {
        admin: 1,
        form: 'infoItem',
        id_item: id_item
    }).done(function (retorno) {
        var obj = jQuery.parseJSON(retorno);
        document.getElementById('complemento_item').value = obj.complemento_item;
        document.getElementById('vl_unitario').value = obj.vl_unitario;
        document.getElementById('qt_contrato').value = obj.qt_contrato;
        document.getElementById('vl_contrato').value = obj.vl_contrato;
        document.getElementById('qt_utilizado').value = obj.qt_utilizado;
        document.getElementById('vl_utilizado').value = obj.vl_utilizado;
        document.getElementById('qt_saldo').value = obj.qt_saldo;
        document.getElementById('vl_saldo').value = obj.vl_saldo;
        document.getElementById('cod_despesa').value = obj.cod_despesa;
        document.getElementById('cod_reduzido').value = obj.cod_reduzido;
        document.getElementById('dt_fim').value = obj.dt_fim;
        document.getElementById('descr_despesa').value = obj.descr_despesa;
        document.getElementById('seq_item_processo').value = obj.seq_item_processo;

        document.getElementById('id_item_processo').value = obj.id_item_processo;
        document.getElementById('id_item_contrato').value = obj.id_item_contrato;
        document.getElementById('descr_tipo_doc').value = obj.descr_tipo_doc;
        document.getElementById('num_contrato').value = obj.num_contrato;
        document.getElementById('num_processo').value = obj.num_processo;
        document.getElementById('descr_mod_compra').value = obj.descr_mod_compra;
        document.getElementById('num_licitacao').value = obj.num_licitacao;
        document.getElementById('dt_inicio').value = obj.dt_inicio;
        document.getElementById('dt_fim').value = obj.dt_fim;
        document.getElementById('dt_geracao').value = obj.dt_geracao;
        document.getElementById('cgc_fornecedor').value = obj.cgc_fornecedor;
        document.getElementById('nome_fornecedor').value = obj.nome_fornecedor;
        document.getElementById('nome_unidade').value = obj.nome_unidade;
        document.getElementById('cod_estruturado').value = obj.cod_estruturado;
        document.getElementById('num_extrato').value = obj.num_extrato;
        document.getElementById('descricao').value = obj.descricao;
        document.getElementById('id_extrato_contr').value = obj.id_extrato_contr;
        document.getElementById('id_unidade').value = obj.id_unidade;
        document.getElementById('ano_orcamento').value = obj.ano_orcamento;
    });
}
