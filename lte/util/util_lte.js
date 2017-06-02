/**
 *
 * @author João Bolsson (joaovictorbolsson@gmail.com)
 * @since 2017, 30 May.
 */

$(function () {
    var modal = $('.modal');
    var body = $('body');
    var modal_backdrop = $('.modal-backdrop');

    modal.on('hidden.bs.modal', function () {
        $(this).removeClass('fv-modal-stack');
        body.data('fv_open_modals', body.data('fv_open_modals') - 1);
    });

    modal.on('shown.bs.modal', function () {
        // keep track of the number of open modals
        if (typeof (body.data('fv_open_modals')) == 'undefined') {
            body.data('fv_open_modals', 0);
        }
        // if the z-index of this modal has been set, ignore.
        if ($(this).hasClass('fv-modal-stack')) {
            return;
        }
        $(this).addClass('fv-modal-stack');
        body.data('fv_open_modals', body.data('fv_open_modals') + 1);
        $(this).css('z-index', 1040 + (10 * body.data('fv_open_modals')));
        modal_backdrop.not('.fv-modal-stack')
            .css('z-index', 1039 + (10 * body.data('fv_open_modals')));
        modal_backdrop.not('fv-modal-stack')
            .addClass('fv-modal-stack');
    });

    $('#myInfos').on('hidden.bs.modal', function () {
        document.getElementById('altInfo').reset();
    });

    $('#altInfo').submit(function (event) {
        event.preventDefault();

        var data = $(this).serialize();

        var nome = $("input[name='nome']", "#altInfo").val();
        $.post('../php/geral.php', data).done(function (callback) {
            if (callback) {
                $('#myInfos').modal('hide');
                document.getElementById('altInfo').reset();
                alert('Suas informações foram salvas com sucesso!');
                var userLogado = document.getElementById('userLogado');
                if (userLogado !== null) {
                    userLogado.innerHTML = nome;
                }

                var userLogadoP = document.getElementById('userLogadop');
                if (userLogadoP !== null) {
                    userLogadoP.innerHTML = nome;
                }
            } else {
                alert("Ocorreu um erro no servidor. Contate o administrador.");
                location.reload();
            }
        });
    });
});

function abreModal(id_modal) {
    $(id_modal).modal();
}

function loadChecks() {
    var elements = document.getElementsByName('checkPedRel');
    var len = elements.length;
    for (var i = 0; i < len; i++) {
        var id_pedido = elements[i].value;
        var id_e = 'checkPedRel' + id_pedido;
        var input = document.getElementById(id_e);
        if (input !== null) {
            $('#' + id_e).iCheck('destroy');
            $('#' + id_e).iCheck({
                checkboxClass: 'icheckbox_flat-blue',
                radioClass: 'iradio_flat-blue'
            });
            $('#' + id_e).on('ifChecked', function () {
                pushOrRemove(this);
                checkImp();
            });
            $('#' + id_e).on('ifUnchecked', function () {
                pushOrRemove(this);
                checkImp();
            });
        }
    }
}

var pedidosRelCustom = [];

function pushOrRemove(element) {
    var len = pedidosRelCustom.length;
    if (element.checked) {
        console.log('push: ' + element.value);
        // push the id on the array
        if (len === 0) {
            console.log('array vazio');
            pedidosRelCustom.push(element.value);
        } else {
            console.log('array nao vazio -> procura por 0 e substitui');
            for (var i = 0; i < len; i++) {
                if (pedidosRelCustom[i] === 0) {
                    console.log('achou zero, vai substituir');
                    pedidosRelCustom[i] = element.value;
                    return;
                }
            }
            console.log('nao achou zero, faz o push');
            pedidosRelCustom.push(element.value);
        }
    } else {
        console.log('remove: ' + element.value);
        // procura o id e substitui por zero
        for (var i = 0; i < len; i++) {
            if (pedidosRelCustom[i] === element.value) {
                console.log('achou, substitui por zero');
                pedidosRelCustom[i] = 0;
                return;
            }
        }
    }
}

function printChecks() {
    $('#btnPrintCheck').blur();
    var len = pedidosRelCustom.length;
    var pedidos = [];
    for (var i = 0; i < len; i++) {
        if (pedidosRelCustom[i] !== 0) {
            pedidos.push(pedidosRelCustom[i]);
        } else {
            console.log('zero');
        }
    }
    if (pedidos.length < 1) {
        console.log('nenhum pedido selecionado');
        return;
    }
    $.post('../php/buscaLTE.php', {
        admin: 1,
        form: 'customRel',
        pedidos: pedidos
    }).done(function () {
        window.open("../admin/printRelatorio.php");
    });
}

function aprovGerencia() {
    $('#btnAprovGeren').blur();
    var len = pedidosRelCustom.length;
    var pedidos = [];
    var str = '[';
    for (var i = 0; i < len; i++) {
        if (pedidosRelCustom[i] !== 0) {
            pedidos.push(pedidosRelCustom[i]);
            str += '' + pedidosRelCustom[i];
            if (i !== len - 1) {
                str += ', ';
            }
        } else {
            console.log('zero');
        }
    }
    str += ']';
    if (pedidos.length < 1) {
        console.log('nenhum pedido selecionado');
        return;
    }

    if (confirm('Os pedidos ' + str + ' serão sinalizados como Aprovado pela Gerência. Essa açao é irreversível. Prosseguir?')) {
        $.post('../php/geral.php', {
            admin: 1,
            form: 'aprovaGeren',
            pedidos: pedidos
        }).done(function (resposta) {
            console.log(resposta);
            iniSolicitacoes(false, 0);
        });
    }
    pedidosRelCustom = [];
    checkImp();
}

function checkImp() {
    var len = pedidosRelCustom.length;
    var btn = document.getElementById('btnPrintCheck');
    var btnAprov = document.getElementById('btnAprovGeren');
    if (btn === null && btnAprov === null) {
        return;
    }
    for (var i = 0; i < len; i++) {
        if (pedidosRelCustom[i] !== 0) {
            console.log('tem algum selecionado');
            if (btn !== null) {
                btn.disabled = false;
            }
            if (btnAprov !== null) {
                btnAprov.disabled = false;
            }
            return;
        }
    }
    console.log('nenhum selecionado');
    if (btn !== null) {
        btn.disabled = true;
    }
    if (btnAprov !== null) {
        btnAprov.disabled = true;
    }
}

function dropTableSolic(id_pedido) {
    document.getElementById('overlayLoad').style.display = 'block';
    var element = document.getElementById('conteudoSolicitacoes');
    if (element.innerHTML.length > 0) {
        $('#tableSolicitacoes').DataTable().destroy();
    }
    if (id_pedido !== null) {
        console.log('is not null: ' + id_pedido);
        var row = document.getElementById('rowPedido' + id_pedido);
        if (row.parentNode) {
            row.parentNode.removeChild(row);
            console.log('removeu rowPedido' + id_pedido);
        } else {
            console.log('nao removeu');
        }
    } else {
        console.log('is null: clear table');
        document.getElementById('conteudoSolicitacoes').innerHTML = '';
    }
}

function getIdsPedido() {
    var element = document.getElementById('conteudoSolicitacoes');
    var pedidos = [];
    if (element === null) {
        console.log('conteudoSolicitacoes nao existe');
        return pedidos;
    } else if (element.innerHTML.length == 0) {
        console.log('tabela vazia');
        return pedidos;
    }
    var rows = element.rows;
    var len = rows.length;
    console.log('rows: ' + len);
    for (var i = 0; i < len; i++) {
        var id = rows[i].id;
        id = id.replace('rowPedido', '');
        pedidos.push(id);
    }
    return pedidos;
}

function avisoSnack(aviso) {
    // Get the snackbar DIV
    var x = document.getElementById('snackbar');
    if (x === null) {
        return;
    }
    x.innerHTML = aviso;

    // Add the "show" class to DIV
    x.className = 'show';

    // After 3 seconds, remove the show class from DIV
    setTimeout(function () {
        x.className = x.className.replace('show', '');
    }, 3000);
}

function iniDataTable(tabela) {
    if (tabela == '#tableItensPedido' || tabela == '#tableProcessos') {
        $(tabela).DataTable({
            "destroy": true,
            "paging": true,
            "lengthChange": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "scrollX": true,
            "columnDefs": [
                {"width": "15%", "targets": 0},
                {"width": "15%", "targets": 5}
            ],
            "lengthMenu": [5, 10, 25, 50, 100],
            language: {
                "decimal": "",
                "emptyTable": "Nenhum dado na tabela",
                "info": "_MAX_ resultados encontrados",
                "infoEmpty": "",
                "infoFiltered": "",
                "infoPostFix": "",
                "thousands": ",",
                "lengthMenu": "Monstrando _MENU_ entradas",
                "loadingRecords": "Carregando...",
                "processing": "Processando...",
                "search": "Pesquisar:",
                "zeroRecords": "Nenhum resultado encontrado",
                "paginate": {
                    "first": "Primeiro",
                    "last": "Último",
                    "next": "Próximo",
                    "previous": "Anterior"
                },
                "aria": {
                    "sortAscending": ": activate to sort column ascending",
                    "sortDescending": ": activate to sort column descending"
                }
            }
        });
    } else if (tabela == '#tableSolicitacoes') {
        $(tabela).DataTable({
            "destroy": true,
            "paging": true,
            "lengthChange": true,
            "searching": true,
            "ordering": true,
            "order": [[2, "desc"]],
            "info": true,
            "autoWidth": true,
            "columnDefs": [
                {"width": "15%", "targets": 1}
            ],
            language: {
                "decimal": "",
                "emptyTable": "Nenhum dado na tabela",
                "info": "_MAX_ resultados encontrados",
                "infoEmpty": "",
                "infoFiltered": "",
                "infoPostFix": "",
                "thousands": ",",
                "lengthMenu": "Monstrando _MENU_ entradas",
                "loadingRecords": "Carregando...",
                "processing": "Processando...",
                "search": "Pesquisar:",
                "zeroRecords": "Nenhum resultado encontrado",
                "paginate": {
                    "first": "Primeiro",
                    "last": "Último",
                    "next": "Próximo",
                    "previous": "Anterior"
                },
                "aria": {
                    "sortAscending": ": activate to sort column ascending",
                    "sortDescending": ": activate to sort column descending"
                }
            }
        });
        $(tabela).on('page.dt', function () {
            selectAll(false);
        }).DataTable();
    } else {
        $(tabela).DataTable({
            "destroy": true,
            "paging": true,
            "lengthChange": true,
            "searching": true,
            "ordering": true,
            "order": [[0, "desc"]],
            "info": true,
            "autoWidth": true,
            language: {
                "decimal": "",
                "emptyTable": "Nenhum dado na tabela",
                "info": "_MAX_ resultados encontrados",
                "infoEmpty": "",
                "infoFiltered": "",
                "infoPostFix": "",
                "thousands": ",",
                "lengthMenu": "Monstrando _MENU_ entradas",
                "loadingRecords": "Carregando...",
                "processing": "Processando...",
                "search": "Pesquisar:",
                "zeroRecords": "Nenhum resultado encontrado",
                "paginate": {
                    "first": "Primeiro",
                    "last": "Último",
                    "next": "Próximo",
                    "previous": "Anterior"
                },
                "aria": {
                    "sortAscending": ": activate to sort column ascending",
                    "sortDescending": ": activate to sort column descending"
                }
            }
        });
    }
}