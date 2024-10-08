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

    $('#selectSetorRelFonte').change(function () {
        changeSelectedSector();
    });

    $('#selectSetorRelSIAFI').change(function () {
        changeSelectedSectorSIAFI();
    });

    $('#relLibOrc').on('shown.bs.modal', function () {
        $('.select2').select2();
        $(".date").inputmask("dd/mm/yyyy", {"placeholder": "dd/mm/yyyy"});
    });

    var relPedidos = document.getElementById('relPedidos');
    if (relPedidos !== null) {
        $('#relPedidos').on('shown.bs.modal', function () {
            changeSelectedSector();
            $('.select2').select2();
            $(".date").inputmask("dd/mm/yyyy", {"placeholder": "dd/mm/yyyy"});
        });
    }

    var relSIAFI = document.getElementById('relSIAFI');
    if (relSIAFI !== null) {
        $('#relSIAFI').on('shown.bs.modal', function () {
            changeSelectedSectorSIAFI();
            $('.select2').select2();
            $(".date").inputmask("dd/mm/yyyy", {"placeholder": "dd/mm/yyyy"});
        });
    }

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

    var id_e = 'checkPedRel';
    var input = document.getElementById(id_e);
    if (input !== null) {
        var checkReq = $('#' + id_e);
        checkReq.on('ifCreated', function () {
            checkReq.iCheck('destroy');
        });
        checkReq.iCheck({
            checkboxClass: 'icheckbox_flat-blue',
            radioClass: 'iradio_flat-blue'
        });
        checkReq.on('ifChecked', function () {
            selectAll(true);
        });
        checkReq.on('ifUnchecked', function () {
            selectAll(false);
        });
    }

    var checkSiafi = document.getElementById('checkSaifi');
    if (checkSiafi !== null) {
        $('#checkSaifi').iCheck({
            checkboxClass: 'icheckbox_minimal-blue',
            radioClass: 'iradio_minimal-blue'
        });
    }

    var status = ['stNormal', 'stPreferencial', 'stUrgente', 'stEmergencial', 'stRascunho', 'stHoje'];
    for (var i = 0; i < status.length; i++) {
        var element = document.getElementById(status[i]);
        if (element !== null) {
            var checkSt = $('#' + status[i]);
            checkSt.iCheck({
                checkboxClass: 'icheckbox_minimal-blue',
                radioClass: 'iradio_minimal-blue'
            });
            if (status[i] === 'stRascunho') {
                checkSt.iCheck('check');
            }
        }
    }

    var modalListProcessos = $('#listProcessos');
    modalListProcessos.on('shown.bs.modal', function () {
        iniDataTable('#tableListProcessos');
    });

    modalListProcessos.on('hidden.bs.modal', function () {
        $('#tbodyListProcessos').html("");
        $('#tableListProcessos').DataTable().destroy();
    });

});

/**
 * Function used only in modal to requests report.
 */
function changeSelectedSector() {
    var setor = document.getElementById('selectSetorRelFonte').value;
    fillSourcesToSector(setor);
}

function changeSelectedSectorSIAFI() {
    var setor = document.getElementById('selectSetorRelSIAFI').value;
    fillSourcesToSectorSIAFI(setor);
}

function fillSourcesToSector(id_setor) {
    $.post('../php/busca.php', {
        users: 1,
        form: 'fillSourcesToSector',
        id_setor: id_setor
    }).done(function (resposta) {
        if (resposta) {
            $('#selectFonte').html(resposta);
        } else {
            alert('Ocorreu um erro no servidor. Contate o administrador.');
        }
    });
}

function fillSourcesToSectorSIAFI(id_setor) {
    $.post('../php/busca.php', {
        users: 1,
        form: 'fillSourcesToSector',
        id_setor: id_setor
    }).done(function (resposta) {
        if (resposta) {
            $('#selectFonteSIAFI').html(resposta);
            $('#selectFonteSIAFI').select2();
        } else {
            alert('Ocorreu um erro no servidor. Contate o administrador.');
        }
    });
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

function listProcessos(permissao) {
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

function selectAll(flag) {
    var state = 'uncheck';
    if (flag) {
        state = 'check';
    }
    $('#checkPedRel').iCheck(state);
    var element = document.getElementById('tableSolicitacoes');
    if (element == null) {
        console.log("tableSolicitacoes doesn't exists");
        return;
    }
    var rows = element.rows;
    var len = rows.length;
    for (var i = 0; i < len; i++) {
        var id = rows[i].id;
        id = id.replace("rowPedido", "checkPedRel");
        $('#' + id).iCheck(state);
    }
}

function abreModal(id_modal) {
    $(id_modal).modal();
}

function changeSetor(id_setor) {
    var el = document.getElementById('selectSetor');
    var setor = (el !== null) ? el.value : id_setor;
    $.post('../php/buscaLTE.php', {
        users: 1,
        form: 'listLancamentos',
        id_setor: setor
    }).done(function (resposta) {
        if (document.getElementById('tbodyListLancamentos').innerHTML.length > 0) {
            $('#tableListLancamentos').DataTable().destroy();
        }
        $('#tbodyListLancamentos').html(resposta);
        if (id_setor == null) {
            iniDataTable('#tableListLancamentos');
        }
    });
    if (id_setor == null) {
        refreshDataSaldo(setor);
    }
}

function loadMore() {
    $('button').blur();
    $('#loadMoreCustom').modal('hide');
    iniSolicitacoes(true);
}

function iniProcVenc() {
    var element = document.getElementById('tbodyProcVenc');
    if (element === null) {
        return;
    }
    var load = document.getElementById('overlayLoadVenc');
    if (load !== null) {
        load.style.display = 'block';
    }
    $.post('../php/buscaLTE.php', {
        admin: 1,
        form: 'tableProcVenc'
    }).done(function (resposta) {
        if (resposta.length > 0) {
            element.innerHTML += resposta;
        }
        iniDataTable('#tableProcVenc');
        if (load !== null) {
            load.style.display = 'none';
        }
    });
}

function iniSolicitacoes(flag, id_pedido) {
    $(".date").inputmask("dd/mm/yyyy", {"placeholder": "dd/mm/yyyy"});
    var element = document.getElementById('conteudoSolicitacoes');
    if (element === null) {
        return;
    }
    var load = document.getElementById('overlayLoad');
    if (load !== null) {
        load.style.display = 'block';
    }
    var limit1 = 0, limit2 = 0;
    if (flag) {
        var input = document.getElementById('limit1');
        if (input !== null) {
            limit1 = document.getElementById('limit1').value;
            limit2 = document.getElementById('limit2').value;
        }
    } else if (id_pedido != 0) {
        console.log('vai atualizar uma linha: ' + id_pedido);
    } else if (id_pedido == 0) {
        console.log('busca os últimos 100 pedidos');
    }
    if (element.innerHTML.length > 0) {
        $('#tableSolicitacoes').DataTable().destroy();
        destroyChecks();
    }
    var pedidos = getIdsPedido();
    $.post('../php/buscaLTE.php', {
        admin: 1,
        form: 'tableItensPedido',
        limit1: limit1,
        limit2: limit2,
        id_pedido: id_pedido,
        pedidos: pedidos
    }).done(function (resposta) {
        if (resposta.length > 0) {
            element.innerHTML += resposta;
            loadChecks();
        }
        iniDataTable('#tableSolicitacoes');
        if (load !== null) {
            load.style.display = 'none';
        }
        var loadDetPed = document.getElementById('overlayLoadDetPed');
        if (loadDetPed !== null) {
            loadDetPed.style.display = 'none';
        }
    });
}

function destroyChecks() {
    var elements = document.getElementsByName('checkPedRel');
    var len = elements.length;
    for (var i = 0; i < len; i++) {
        var id_pedido = elements[i].value;
        var id_e = 'checkPedRel' + id_pedido;
        var input = document.getElementById(id_e);
        if (input !== null) {
            var check = $('#' + id_e);
            check.iCheck('uncheck');
            check.iCheck('destroy');
        }
    }
}

function loadChecks() {
    var elements = document.getElementsByName('checkPedRel');
    var len = elements.length;
    for (var i = 0; i < len; i++) {
        var id_pedido = elements[i].value;
        var id_e = 'checkPedRel' + id_pedido;
        var input = document.getElementById(id_e);
        if (input !== null) {
            var check = $('#' + id_e);
            check.iCheck({
                checkboxClass: 'icheckbox_flat-blue',
                radioClass: 'iradio_flat-blue'
            });
            check.on('ifChecked', function () {
                pushOrRemove(this);
                checkImp();
            });
            check.on('ifUnchecked', function () {
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
        // push the id on the array
        if (len === 0) {
            pedidosRelCustom.push(element.value);
        } else {
            for (var i = 0; i < len; i++) {
                if (pedidosRelCustom[i] === 0) {
                    pedidosRelCustom[i] = element.value;
                    return;
                }
            }
            pedidosRelCustom.push(element.value);
        }
    } else {
        // look for id and replace with zero
        for (var j = 0; j < len; j++) {
            if (pedidosRelCustom[j] === element.value) {
                pedidosRelCustom[j] = 0;
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
            if (btn !== null) {
                btn.disabled = false;
            }
            if (btnAprov !== null) {
                btnAprov.disabled = false;
            }
            return;
        }
    }
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

function hideDivs() {
    var divs = ['rowPedidos', 'rowDetPedido', 'rowSolicAdi', 'rowAltPed', 'rowCadRP'];
    for (i = 0; i < divs.length; i++) {
        var element = document.getElementById(divs[i]);
        if (element !== null) {
            element.style.display = 'none';
        }
    }
}

function mostra(row) {
    hideDivs();
    var div = document.getElementById(row);
    if (div === null) {
        console.log("Can't show element: is null");
        return;
    }
    var display = div.style.display;
    if (display === 'block') {
        display = 'none';
    } else {
        display = 'block';
    }
    div.style.display = display;
}

function printDueDate() {
    window.open("../admin/printDueDate.php");
}

function iniDataTable(tabela) {
    var language = {
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
    };

    if (tabela === "#tableContratos") {
        $(tabela).DataTable({
            "destroy": true,
            "order": [[0, "desc"]],
            "columnDefs": [
                {"width": "20%", "targets": 0},
                {"width": "5%", "targets": 1},
                {"width": "30%", "targets": 2},
                {"width": "15%", "targets": 3}
            ],
            language: language
        });
    } else if (tabela == '#tableItensPedido') {
        $(tabela).DataTable({
            "destroy": true,
            "scrollX": true,
            "columnDefs": [
                {"width": "15%", "targets": 0},
                {"width": "15%", "targets": 5}
            ],
            "lengthMenu": [[-1], ["Todas"]],
            language: language
        });
    } else if (tabela == '#tableProcessos') {
        $(tabela).DataTable({
            "destroy": true,
            "scrollX": true,
            "columnDefs": [
                {"width": "15%", "targets": 0},
                {"width": "15%", "targets": 5}
            ],
            "lengthMenu": [5, 10, 25, 50, 100],
            language: language
        });
    } else if (tabela == '#tableSolicitacoes') {
        $(tabela).DataTable({
            "destroy": true,
            "order": [[2, "desc"]],
            "autoWidth": true,
            "columnDefs": [
                {"width": "20%", "targets": 1}
            ],
            language: language
        });
        $(tabela).on('page.dt', function () {
            selectAll(false);
        }).DataTable();
    } else if (tabela == '#tableProcVenc') {
        $(tabela).DataTable({
            "order": [[2, "asc"]],
            "autoWidth": true,
            "lengthMenu": [5, 10, 25, 50, 100],
            language: language
        });
    } else {
        $(tabela).DataTable({
            "destroy": true,
            "order": [[0, "desc"]],
            "autoWidth": true,
            language: language
        });
    }
}