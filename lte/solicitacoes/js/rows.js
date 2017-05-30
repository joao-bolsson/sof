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


