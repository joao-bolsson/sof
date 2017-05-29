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

    var status = ['stabertos', 'staprovados', 'streprovado'];
    for (var i = 0; i < status.length; i++) {
        var element = document.getElementById(status[i]);
        if (element !== null) {
            $('#' + status[i]).iCheck({
                checkboxClass: 'icheckbox_minimal-blue',
                radioClass: 'iradio_minimal-blue'
            });
            $('#' + status[i]).on('ifChecked', function (event) {
                iniTableSolicAdiant();
            });
        }
    }

    for (var i = 2; i <= 10; i++) {
        // radios dos detalhes do pedido
        var element = document.getElementById('st' + i);
        if (element !== null) {
            $('#st' + i).iCheck({
                checkboxClass: 'icheckbox_minimal-blue',
                radioClass: 'iradio_minimal-blue'
            });
        }
    }


    status = ['stAltAbertos', 'stAltAprovados', 'stAltReprovado'];
    for (var i = 0; i < status.length; i++) {
        var element = document.getElementById(status[i]);
        if (element !== null) {
            $('#' + status[i]).iCheck({
                checkboxClass: 'icheckbox_minimal-blue',
                radioClass: 'iradio_minimal-blue'
            });
            $('#' + status[i]).on('ifChecked', function (event) {
                iniTableSolicAltPed();
            });
        }
    }

    status = ['stNormal', 'stPreferencial', 'stUrgente', 'stEmergencial', 'stRascunho'];
    for (var i = 0; i < status.length; i++) {
        var element = document.getElementById(status[i]);
        if (element !== null) {
            $('#' + status[i]).iCheck({
                checkboxClass: 'icheckbox_minimal-blue',
                radioClass: 'iradio_minimal-blue'
            });
            if (i === status.length - 1) {
                $('#' + status[i]).iCheck('check');
            }
        }
    }

    for (var i = 1; i <= 6; i++) {
        var element = document.getElementById('tipoLic' + i);
        if (element !== null) {
            $('#tipoLic' + i).iCheck({
                checkboxClass: 'icheckbox_minimal-blue',
                radioClass: 'iradio_minimal-blue'
            });
            $('#tipoLic' + i).on('ifChecked', function () {
                changeTipoLic(this);
            });
        }
    }

    var element = document.getElementById('gera');
    if (element !== null) {
        $('#gera').iCheck({
            checkboxClass: 'icheckbox_minimal-blue',
            radioClass: 'iradio_minimal-blue'
        });
        $('#gera').iCheck('disable');
    }
    var element = document.getElementById('ngera');
    if (element !== null) {
        $('#ngera').iCheck({
            checkboxClass: 'icheckbox_minimal-blue',
            radioClass: 'iradio_minimal-blue'
        });
        $('#ngera').iCheck('disable');
    }

    element = document.getElementById('checkSaifi');
    if (element !== null) {
        $('#checkSaifi').iCheck({
            checkboxClass: 'icheckbox_minimal-blue',
            radioClass: 'iradio_minimal-blue'
        });
    }

// numero de permissoes
    var perm_count = 4;
    for (var k = 1; k <= perm_count; k++) {
        var perm = document.getElementById('perm' + k);
        if (perm !== null) {
            $('#perm' + k).iCheck({
                checkboxClass: 'icheckbox_minimal-blue',
                radioClass: 'iradio_minimal-blue'
            });
        }
    }

    var excluir = document.getElementById('checkExcluir');
    if (excluir !== null) {
        $('#checkExcluir').iCheck({
            checkboxClass: 'icheckbox_minimal-blue',
            radioClass: 'iradio_minimal-blue'
        });
    }

    // TODO: mover infoItem para outro js
    $('#infoItem').on('shown.bs.modal', function () {
        $(".date").inputmask("dd/mm/yyyy", {"placeholder": "dd/mm/yyyy"});
    });

    $('#manageUsers').on('shown.bs.modal', function () {
        $.post('../php/buscaLTE.php', {
            admin: 1,
            form: 'getUsers'
        }, function (resposta) {
            document.getElementById('usersToDisable').innerHTML = resposta;
        });
    });

    $('#infoItem').on('hidden.bs.modal', function () {
        document.getElementById('id').value = 0;
        document.getElementById('formEditRegItem').reset();
    });

    $('.modal').on('hidden.bs.modal', function () {
        $(this).removeClass('fv-modal-stack');
        $('body').data('fv_open_modals', $('body').data('fv_open_modals') - 1);
    });
    $('.modal').on('shown.bs.modal', function () {
// keep track of the number of open modals
        if (typeof ($('body').data('fv_open_modals')) == 'undefined') {
            $('body').data('fv_open_modals', 0);
        }
// if the z-index of this modal has been set, ignore.
        if ($(this).hasClass('fv-modal-stack')) {
            return;
        }
        $(this).addClass('fv-modal-stack');
        $('body').data('fv_open_modals', $('body').data('fv_open_modals') + 1);
        $(this).css('z-index', 1040 + (10 * $('body').data('fv_open_modals')));
        $('.modal-backdrop').not('.fv-modal-stack')
            .css('z-index', 1039 + (10 * $('body').data('fv_open_modals')));
        $('.modal-backdrop').not('fv-modal-stack')
            .addClass('fv-modal-stack');
    });
    var checkPedContr = document.getElementById("checkPedContr");
    if (checkPedContr !== null) {
        document.getElementById("checkPedContr").onclick = function () {
            checkPedContr(this);
        };
    }

    $('#transferencia').on('shown.bs.modal', function () {
        var id_dest = document.getElementById('transfDest').value;
        fillTransfSource(id_dest);
    });

    $('#transferencia').on('shown.bs.modal', function () {
        $('.select2').select2();
    });

    $('#listProcessos').on('shown.bs.modal', function () {
        if (!$.fn.DataTable.isDataTable('#tableListProcessos')) {
            iniDataTable('#tableListProcessos');
        }
    });

    $('#listRascunhos').on('shown.bs.modal', function () {
        if (!$.fn.DataTable.isDataTable('#tableListRascunhos')) {
            iniDataTable('#tableListRascunhos');
        }
    });

    $('#listPedidos').on('shown.bs.modal', function () {
        if (!$.fn.DataTable.isDataTable('#tableListPedidos')) {
            iniDataTable('#tableListPedidos');
        }
    });

    $('#listSolicAltPedidos').on('shown.bs.modal', function () {
        if (!$.fn.DataTable.isDataTable('#tableSolicAltPedido')) {
            iniDataTable('#tableSolicAltPedido');
        }
    });

    $('#listLancamentos').on('shown.bs.modal', function () {
        if (!$.fn.DataTable.isDataTable('#tableListLancamentos')) {
            iniDataTable('#tableListLancamentos');
        }
    });

    $('#listAdiantamentos').on('shown.bs.modal', function () {
        if (!$.fn.DataTable.isDataTable('#tableListAdiantamentos')) {
            iniDataTable('#tableListAdiantamentos');
        }
    });

    var id_e = 'checkPedRel';
    var input = document.getElementById(id_e);
    if (input !== null) {
        $('#' + id_e).on('ifCreated', function () {
            $('#' + id_e).iCheck('destroy');
        });
        $('#' + id_e).iCheck({
            checkboxClass: 'icheckbox_flat-blue',
            radioClass: 'iradio_flat-blue'
        });
        $('#' + id_e).on('ifChecked', function () {
            selectAll(true);
        });
        $('#' + id_e).on('ifUnchecked', function () {
            selectAll(false);
        });
    }

});

function loadPosts(element) {
    if (element === null) {
        return;
    }
    var id = element.id.replace('pag', '');
    carregaPostsPag(id);
}

function carregaPostsPag(tabela) {
    document.getElementById('overlayLoad').style.display = 'block';
    $.post('../php/buscaLTE.php', {
        admin: 1,
        form: 'carregaPostsPag',
        tabela: tabela
    }, function (resposta) {
        $('#contNoticiasEditar').html(resposta);
        document.getElementById('overlayLoad').style.display = 'none';
    });
}

function editaNoticia(id, tabela) {
    document.getElementById('overlayLoadBox').style.display = 'block';
    document.getElementById("funcao").value = "editar";
    document.getElementById("id_noticia").value = id;
    document.getElementById("tabela").value = tabela;

    $.post('../php/busca.php', {
        admin: 1,
        form: 'editarNoticia',
        id: id
    }, function (resposta) {
        $('#txtnoticia').froalaEditor('destroy');
        document.getElementById("txtnoticia").value = "";
        $('#txtnoticia').froalaEditor({
            language: 'pt_br',
            charCounterCount: false,
            heightMin: 100,
            heightMax: 400,
            // Set the image upload URL.
            imageUploadURL: 'upload_image.php',
            // Set the file upload URL.
            fileUploadURL: 'upload_file.php',
            // Set the image upload URL.
            imageManagerLoadURL: 'load_images.php',
            // Set the image delete URL.
            imageManagerDeleteURL: 'delete_image.php',
        });
        $('#txtnoticia').froalaEditor('html.insert', resposta, true);
        tabela = 'op' + document.getElementById('tabela').value;
        document.getElementById(tabela).selected = true;
        $('#listNoticias').modal('hide');
        document.getElementById('overlayLoadBox').style.display = 'none';
    });
}

function excluirNoticia(id) {
    var confirma = confirm("Essa notícia será desativada do sistema. Deseja continuar?");
    if (confirma) {
        $.post('../php/geral.php', {
            admin: 1,
            form: 'excluirNoticia',
            id: id
        }, function (resposta) {
            location.reload();
        });
    }
}

function relListUsers() {
    window.open("../admin/printRelatorio.php?relatorio=1&tipo=users");
}

function fillTransfSource(id_dest) {
    $.post('../php/busca.php', {
        admin: 1,
        form: 'fillTransfSource',
        id: id_dest
    }, function (resposta) {
        $('#transfSource').html(resposta);
        $("#transfSource").select2();
    });
}

function changeTransfDest(element) {
    fillTransfSource(element.value);
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
    var display = document.getElementById(row).style.display;
    if (display === 'block') {
        display = 'none';
    } else {
        display = 'block';
    }
    document.getElementById(row).style.display = display;
}

function mostraPed() {
    $('button').blur();
    hideDivs();
    document.getElementById('rowPedidos').style.display = 'block';
    iniSolicitacoes(false, 0);
}
function mostraSolicAdiant() {
    mostra('rowSolicAdi');
    iniTableSolicAdiant();
}

function mostraSolicAltPed() {
    mostra('rowAltPed');
    iniTableSolicAltPed();
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

function abreModal(id_modal) {
    $(id_modal).modal();
}

function resetSystem() {
    confirm = confirm('Resetar o sistema para o estado original?');
    if (confirm) {
        $.post('../php/geral.php', {
            admin: 1,
            form: 'resetSystem'
        }).done(function () {
            location.reload();
        });
    }
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
function changeTipoContr(element) {
    var val = element.value;
    document.getElementById('siafi').required = (val == 3);
}

function maybeRequiredTipoContr(flag) {
    var x = document.getElementsByName("tipoCont");
    for (var i = 0; i < x.length; i++) {
        if (x[i].type === "radio") {
            x[i].required = flag;
        }
    }
}

function checkPedContr(element) {
// se for um pedido de contrato, deve escolher uma opcao
    maybeRequiredTipoContr(element.checked);
}

function altInfoUser() {
    var nome = document.getElementById('nameUser').value;
    var email = document.getElementById('emailUser').value;
    var senhaAtual = document.getElementById('senhaAtualUser').value;
    var novaSenha = document.getElementById('senhaUser').value;
    $.post('../php/geral.php', {
        users: 1,
        form: 'infoUser',
        nome: nome,
        email: email,
        novaSenha: novaSenha,
        senhaAtual: senhaAtual
    }).done(function (resposta) {
        if (resposta) {
            $('#myInfos').modal('hide');
            document.getElementById('altInfo').reset();
            alert('Suas informações foram salvas com sucesso!');
            document.getElementById('nameUser').value = nome;
            document.getElementById('emailUser').value = email;
            document.getElementById('userLogado').innerHTML = nome;
            document.getElementById('userLogadop').innerHTML = nome;
        } else {
            alert("Ocorreu um erro no servidor. Contate o administrador.");
            location.reload();
        }
    });
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
        if (permissao.saldos == 1) {
            getSaldoOri();
        }
    });
}

function putNumberFormat(value) {
    $.post('../php/busca.php', {
        users: 1,
        form: 'number_format',
        value: value
    }).done(function (resposta) {
        document.getElementById('saldoDispOri').innerHTML = 'Saldo disponível: R$ ' + resposta;
    });
}

function getSaldoOri() {
    var select = document.getElementById('setorOri');
    if (select === null) {
        return;
    }
    var setorOri = select.options[select.selectedIndex].value;
    $.post('../php/busca.php', {
        admin: 1,
        form: 'getSaldoOri',
        setorOri: setorOri
    }).done(function (resposta) {
        putNumberFormat(resposta);
        $('#valorTransf').attr('max', resposta);
    });
}

var pedidosRelCustom = [];

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

function loadMore() {
    $('button').blur();
    $('#loadMoreCustom').modal('hide');
    iniSolicitacoes(true);
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

function iniSolicitacoes(flag, id_pedido) {
    $(".date").inputmask("dd/mm/yyyy", {"placeholder": "dd/mm/yyyy"});
    var element = document.getElementById('conteudoSolicitacoes');
    if (element === null) {
        return;
    }
    document.getElementById('overlayLoad').style.display = 'block';
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
        document.getElementById('overlayLoad').style.display = 'none';
        document.getElementById('overlayLoadDetPed').style.display = 'none';
    });
}

function enviaForn(id_pedido) {
    $('button').blur();
    if (!confirm("O status do pedido " + id_pedido + " será alterado para 'Enviado ao Fornecedor'. \n\nDeseja Continuar?")) {
        return;
    }
    dropTableSolic(id_pedido);
    $.post('../php/geral.php', {
        admin: 1,
        form: 'enviaForn',
        id_pedido: id_pedido
    }).done(function () {
        iniDataTable('#tableSolicitacoes');
        document.getElementById('overlayLoad').style.display = 'none';
        avisoSnack('Pedido enviado ao Fornecedor');
    });
}

function formEnvia() {
    var id_pedido = document.getElementById("id_pedido_alt").value;
    var justificativa = document.getElementById("justificativa_alt_ped").value;
    $.post('../php/geral.php', {
        users: 1,
        form: 'alt_pedido',
        id_pedido: id_pedido,
        justificativa: justificativa
    }).done(function (resposta) {
        // Quando terminada a requisição
        alert(resposta);
        $('#alt_pedido').modal('hide');
    });
}

function solicAltPed(id_pedido) {
    document.getElementById('id_pedido_alt').value = id_pedido;
    abreModal('#alt_pedido');
    $('#div-lb-high').addClass('control-highlight');
}

function refreshSaldo() {
    $.post('../php/busca.php', {
        admin: 1,
        form: 'refreshSaldo'
    }).done(function (resposta) {
        document.getElementById('labelSaldoSOF').innerHTML = 'Saldo disponível: R$ ' + resposta;
    });
}

function listLancamentos(id_setor) {
    $('#listLancamentos').modal('show');
    if (id_setor != null) {
        changeSetor(id_setor);
    }
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

function undoFreeMoney(id_lancamento) {
    console.log("Undo free money: " + id_lancamento);
    $.post('../php/geral.php', {
        admin: 1,
        form: 'undoFreeMoney',
        id_lancamento: id_lancamento
    }).done(function () {
        location.reload();
    });
}

function refreshDataSaldo(id_setor) {
    $.post('../php/busca.php', {
        users: 1,
        form: 'refreshTotSaldos',
        id_setor: id_setor
    }).done(function (resposta) {
        var obj = jQuery.parseJSON(resposta);
        $('#totIn').html(obj.entrada);
        $('#totOut').html(obj.saida);
    });
}

function listSolicAltPedidos() {
    if (!$.fn.DataTable.isDataTable('#tableListProcessos')) {
        $.post('../php/buscaLTE.php', {
            users: 1,
            form: 'iniSolicAltPedSetor'
        }).done(function (resposta) {
            $('#tbodySolicAltPedido').html(resposta);
        });
    }
    $('#listSolicAltPedidos').modal();
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

function listAdiantamentos() {
    if (!$.fn.DataTable.isDataTable('#tableListAdiantamentos')) {
        $.post('../php/buscaLTE.php', {
            users: 1,
            form: 'listAdiantamentos'
        }).done(function (resposta) {
            $('#tbodyListAdiantamentos').html(resposta);
        });
    }
    $('#listAdiantamentos').modal('show');
}

function changeTipoLic(element) {
    var selected = element.value;
    if (selected == 3 || selected == 4 || selected == 2) { // Adesao, Compra Compartilhada ou Inexibilidade
        maybeDisableFields(false);
    } else {
        maybeDisableFields(true);
    }
    if (selected == 6) { // RP
        document.getElementById('infoLic').required = false;
    } else {
        document.getElementById('infoLic').required = true;
    }
    maybeRequiredTipoContr(true);
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

function verProcessos(pedido) {
    $.post('../php/busca.php', {
        admin: 1,
        form: 'verProcessos',
        id_pedido: pedido
    }).done(function (resposta) {
        $('#tbodyListProcessos').html(resposta);
        abreModal('#listProcessos');
    });
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

function listPedidos() {
    if (document.getElementById('tbodyListPedidos').innerHTML.length === 0) {
        $.post('../php/buscaLTE.php', {
            users: 1,
            form: 'listPedidos'
        }).done(function (resposta) {
            $('#tbodyListPedidos').html(resposta);
            $('#listPedidos').modal('show');
        });
    } else {
        $('#listPedidos').modal('show');
    }
}

function listRascunhos() {
    if (document.getElementById('tbodyListRascunhos').innerHTML.length === 0) {
        $.post('../php/buscaLTE.php', {
            users: 1,
            form: 'listRascunhos'
        }).done(function (resposta) {
            $('#tbodyListRascunhos').html(resposta);
        });
    }
    $('#listRascunhos').modal('show');
}

function iniPagSolicitacoes() {
    iniDataTable('#tableProcessos');
    $(".select2").select2();
    $('#checkPedContr').iCheck({
        checkboxClass: 'icheckbox_minimal-blue',
        radioClass: 'iradio_minimal-blue'
    });
    for (var i = 1; i <= 3; i++) {
        $('#tipoCont' + i).iCheck({
            checkboxClass: 'icheckbox_minimal-blue',
            radioClass: 'iradio_minimal-blue'
        });
        $('#tipoCont' + i).on('ifChecked', function () {
            changeTipoContr(this);
        });
    }
}

function iniTableSolicAdiant() {
    var status = ['stabertos', 'staprovados', 'streprovado'];
    var st = null;
    for (var i = 0; i < status.length; i++) {
        var element = document.getElementById(status[i]);
        if (element.checked) {
            st = element.value;
        }
    }
    $.post('../php/buscaLTE.php', {
        admin: 1,
        form: 'tableSolicitacoesAdiantamento',
        status: st
    }).done(function (resposta) {
        if ($.fn.DataTable.isDataTable('#tableSolicitacoesAdiantamento')) {
            $('#tableSolicitacoesAdiantamento').DataTable().destroy();
        }
        document.getElementById('conteudoSolicitacoesAdiantamento').innerHTML = resposta;
        iniDataTable('#tableSolicitacoesAdiantamento');
    });
}

function iniTableSolicAltPed() {
    var table = 'tableSolicAltPedido';
    var status = ['stAltAbertos', 'stAltAprovados', 'stAltReprovado'];
    var st = null;
    for (var i = 0; i < status.length; i++) {
        var element = document.getElementById(status[i]);
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

function analisaAdi(id, acao) {
    $('a').blur();
    var aprova;
    if (acao) {
        aprova = confirm("O setor receberá o valor adiantado e terá esse mesmo valor descontado no próximo mês, estando sujeito à ficar com saldo negativo.\n\nDeseja continuar?");
    } else {
        aprova = confirm("A solicitação de adiantamento será reprovada e o saldo do setor não será alterado.\n\nDeseja continuar?");
    }
    if (aprova) {
        $.post('../php/geral.php', {
            admin: 1,
            form: 'aprovaAdi',
            id: id,
            acao: acao
        }).done(function () {
            $('#tableListLancamentos').DataTable().destroy();
            iniTableSolicAdiant();
        });
    }
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
    $('#gera').iCheck('uncheck');
    document.getElementById('gera').required = false;
    $('#gera').iCheck('disable');
    $('#ngera').iCheck('uncheck');
    document.getElementById('ngera').required = false;
    $('#ngera').iCheck('disable');
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

function sleep(milliseconds) {
    var start = new Date().getTime();
    for (var i = 0; i < 1e7; i++) {
        if ((new Date().getTime() - start) > milliseconds) {
            break;
        }
    }
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
