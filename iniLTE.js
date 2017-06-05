$(function () {

    $(document).ajaxStart(function () {
        Pace.restart();
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

function listLancamentos(id_setor) {
    $('#listLancamentos').modal('show');
    if (id_setor != null) {
        changeSetor(id_setor);
    }
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

// até aqui, ok
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
