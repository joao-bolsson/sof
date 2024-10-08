$(function () {

    $(document).ajaxStart(function () {
        Pace.restart();
    });

    tableItens = '';
    var ta = document.getElementById('divTableItens');
    if (ta !== null) {
        tableItens = ta.innerHTML;
    }

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
            iniProcVenc();
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
