/**
 *
 * @author João Bolsson (joaovictorbolsson@gmail.com)
 * @since 2017, 29 May.
 */

$(function () {
    var checkPedContrato = document.getElementById("checkPedContr");
    if (checkPedContrato !== null) {
        document.getElementById("checkPedContr").onclick = function () {
            checkPedContr(this);
        };
    }

    var checkPlanTrab = $('#checkPlanoTrabalho');

    checkPlanTrab.on('ifChecked', function () {
        checkPlan(true);
    });

    checkPlanTrab.on('ifUnchecked', function () {
        checkPlan(false);
    });


    var element_gera = document.getElementById('gera');

    if (element_gera !== null) {
        var gera = $('#gera');
        gera.iCheck({
            checkboxClass: 'icheckbox_minimal-blue',
            radioClass: 'iradio_minimal-blue'
        });
        gera.iCheck('disable');
    }

    var element_ngera = document.getElementById('ngera');
    if (element_ngera !== null) {
        var ngera = $('#ngera');
        ngera.iCheck({
            checkboxClass: 'icheckbox_minimal-blue',
            radioClass: 'iradio_minimal-blue'
        });
        ngera.iCheck('disable');
    }

    for (var i = 1; i <= 6; i++) {
        var element = document.getElementById('tipoLic' + i);
        if (element !== null) {
            var tipo = $('#tipoLic' + i);
            tipo.iCheck({
                checkboxClass: 'icheckbox_minimal-blue',
                radioClass: 'iradio_minimal-blue'
            });
            tipo.on('ifChecked', function () {
                changeTipoLic(this);
            });
        }
    }
});

function checkPlan(status) {
    document.getElementById('planoTrabalho').value = "";
    document.getElementById('planoTrabalho').disabled = !status;
    document.getElementById('planoTrabalho').required = !status;
}

function changeTipoLic(element) {
    var selected = element.value;
    if (selected == 3 || selected == 4 || selected == 2) { // Adesao, Compra Compartilhada ou Inexibilidade
        maybeDisableFields(false);
    } else {
        maybeDisableFields(true);
    }

    document.getElementById('infoLic').required = selected != 6;
    maybeRequiredTipoContr(true);
}

function maybeRequiredTipoContr(flag) {
    var x = document.getElementsByName("tipoCont");
    for (var i = 0; i < x.length; i++) {
        if (x[i].type === "radio") {
            x[i].required = flag;
        }
    }
}

function maybeDisableFields(flag) {
    document.getElementById('uasg').disabled = flag;
    document.getElementById('procOri').disabled = flag;
    var status = 'enable';
    if (flag) {
        status = 'disable';
    }
    $('#gera').iCheck(status);
    $('#ngera').iCheck(status);
    // required

    document.getElementById('uasg').required = !flag;
    document.getElementById('procOri').required = !flag;
    document.getElementById('gera').required = !flag;
    document.getElementById('ngera').required = !flag;
}

function checkPedContr(element) {
    // se for um pedido de contrato, deve escolher uma opcao
    maybeRequiredTipoContr(element.checked);
}

function iniPagSolicitacoes() {
    iniDataTable('#tableProcessos');
    $(".select2").select2();
    $('#checkPedContr').iCheck({
        checkboxClass: 'icheckbox_minimal-blue',
        radioClass: 'iradio_minimal-blue'
    });

    $('#checkPlanoTrabalho').iCheck({
        checkboxClass: 'icheckbox_minimal-blue',
        radioClass: 'iradio_minimal-blue'
    });

    for (var i = 1; i <= 3; i++) {
        var tipoCont = $('#tipoCont' + i);
        tipoCont.iCheck({
            checkboxClass: 'icheckbox_minimal-blue',
            radioClass: 'iradio_minimal-blue'
        });
        tipoCont.on('ifChecked', function () {
            changeTipoContr(this);
        });
    }
}

function changeTipoContr(element) {
    var val = element.value;
    document.getElementById('siafi').required = (val == 3);
}

function fillSaldo() {
    $.post('../php/busca.php', {
        users: 1,
        form: 'fillSaldo'
    }).done(function (resposta) {
        $('#text_saldo_total').html('R$ ' + resposta);
    });
    $.post('../php/busca.php', {
        users: 1,
        form: 'getSaldo'
    }).done(function (resposta) {
        document.getElementById('saldo_total').value = resposta;
    });
}

function limpaTelaSolic() {
    $('#checkPlanoTrabalho').iCheck('uncheck');
    checkPlan(false);
    fillSaldo();
    document.getElementById('pedido').value = 0;
    document.getElementById('conteudoPedido').innerHTML = '';
    document.getElementById('total').value = 'R$ 0';
    document.getElementById('total_hidden').value = 0;
    $('#stRascunho').iCheck('check');
    document.getElementById('obs').value = '';
    // licitação
    for (var i = 1; i <= 6; i++) {
        $('#tipoLic' + i).iCheck('uncheck');
    }
    document.getElementById('infoLic').value = '';
    document.getElementById('infoLic').required = true;
    document.getElementById('uasg').value = '';
    document.getElementById('uasg').required = false;
    document.getElementById('uasg').disabled = true;
    document.getElementById('procOri').value = '';
    document.getElementById('procOri').required = false;
    document.getElementById('procOri').disabled = true;
    var gera = $('#gera');
    var ngera = $('#ngera');

    gera.iCheck('uncheck');
    document.getElementById('gera').required = false;
    gera.iCheck('disable');
    ngera.iCheck('uncheck');
    document.getElementById('ngera').required = false;
    ngera.iCheck('disable');
    $('#checkPedContr').iCheck('uncheck');
    // opções de contrato
    for (var i = 1; i <= 3; i++) {
        document.getElementById('tipoCont' + i).required = false;
        $('#tipoCont' + i).iCheck('uncheck');
    }
    document.getElementById('siafi').value = '';
    $('button').blur();
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

function editaPedido(id_pedido) {
    limpaTelaSolic();
    $.post('../php/busca.php', {
        users: 1,
        form: 'populaRascunho',
        id_pedido: id_pedido
    }).done(function (retorno) {
        var obj = jQuery.parseJSON(retorno);
        //valor do pedido
        document.getElementById('total_hidden').value = obj.valor;
        document.getElementById('total').value = "R$ " + obj.valor;
        //saldo
        document.getElementById('saldo_total').value = parseFloat(obj.saldo - obj.valor).toFixed(3);
        $('#text_saldo_total').html('R$ ' + parseFloat(obj.saldo - obj.valor).toFixed(3));
        // obs
        document.getElementById('obs').value = obj.obs;

        document.getElementById('procSei').value = obj.procSei;
        document.getElementById('pedSei').value = obj.pedSei;
    });
    document.getElementById('pedido').value = id_pedido;
    $('#listRascunhos').modal('hide');
    $.post('../php/buscaLTE.php', {
        users: 1,
        form: 'editaPedido',
        id_pedido: id_pedido
    }).done(function (resposta) {
        $('#conteudoPedido').html(resposta);
    });
    populaLicitacao(id_pedido);
    populaGrupo(id_pedido);
    populaContrato(id_pedido);
    populaPlano(id_pedido);
}

function populaPlano(id_pedido) {
    $.post('../php/busca.php', {
        users: 1,
        form: 'populaPlano',
        id_pedido: id_pedido
    }).done(function (resposta) {
        if (resposta.length > 0) {
            $('#checkPlanoTrabalho').iCheck('check');
            checkPlan(true);
            document.getElementById('planoTrabalho').value = resposta;
        } else {
            $('#checkPlanoTrabalho').iCheck('uncheck');
            checkPlan(false);
        }
    });
}

function populaContrato(id_pedido) {
    $.post('../php/busca.php', {
        users: 1,
        form: 'populaContrato',
        id_pedido: id_pedido
    }).done(function (resposta) {
        if (resposta !== false) {
            var obj = jQuery.parseJSON(resposta);
            document.getElementById('siafi').value = obj.siafi;
            if (obj.id_tipo > 0) {
                $('#tipoCont' + obj.id_tipo).iCheck('check');
            }
            if (obj.pedido_contrato == 1) {
                $('#checkPedContr').iCheck('check');
            }
        }
    });
}

function populaGrupo(id_pedido) {
    $.post('../php/busca.php', {
        users: 1,
        form: 'populaGrupo',
        id_pedido: id_pedido
    }).done(function (resposta) {
        if (resposta) {
            $('#grupo').val(resposta).trigger('change');
        }
    });
}

function populaLicitacao(id_pedido) {
    $.post('../php/busca.php', {
        users: 1,
        form: 'populaLicitacao',
        id_pedido: id_pedido
    }).done(function (resposta) {
        if (resposta) {
            var obj = jQuery.parseJSON(resposta);
            document.getElementById('idLic').value = obj.id;
            document.getElementById('infoLic').value = obj.numero;
            $('#tipoLic' + obj.tipo).iCheck('check');
            if (obj.tipo == 3 || obj.tipo == 4 || obj.tipo == 2) {
                document.getElementById('uasg').value = obj.uasg;
                document.getElementById('procOri').value = obj.processo_original;
            }
            if (obj.gera_contrato == 1) {
                $('#gera').iCheck('check');
            } else {
                $('#ngera').iCheck('check');
            }
            maybeDisableFields(!(obj.tipo == 3 || obj.tipo == 4 || obj.tipo == 2));
            var element = document.getElementById('tipoLic' + obj.tipo);
            changeTipoLic(element);
        }
    });
}


