$(function () {

    tableItens = '';
    var ta = document.getElementById('divTableItens');
    if (ta !== null) {
        tableItens = ta.innerHTML;
    }

    var str = location.pathname;
    if (str.endsWith("view/solicitacoes.php")) {
        $(".select2").select2();
    }
    if (str.endsWith("adminsolicitacoes.php")) {
        $('.date').mask('00/00/0000');
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
        var element = document.getElementById('relStatus' + i);
        if (element !== null) {
            $('#relStatus' + i).iCheck({
                checkboxClass: 'icheckbox_minimal-blue',
                radioClass: 'iradio_minimal-blue'
            });
            $('#relStatus' + i).on('ifChecked', function () {
                changeReport(this);
            });
        }

// radios dos detalhes do pedido
        element = document.getElementById('st' + i);
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

// numero de permissoes
    var perm_count = 4;
    for (var i = 1; i <= perm_count; i++) {
        var element = document.getElementById('perm' + i);
        if (element !== null) {
            $('#perm' + i).iCheck({
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

    $('#listProblemas').on('shown.bs.modal', function () {
        if (!$.fn.DataTable.isDataTable('#tableListProblemas')) {
            iniDataTable('#tableListProblemas');
        }
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

    $('#relPedidos').on('shown.bs.modal', function () {
        $(".date").inputmask("dd/mm/yyyy", {"placeholder": "dd/mm/yyyy"});
    });

    $('#cadEmpenho').on('shown.bs.modal', function () {
        $(".date").inputmask("dd/mm/yyyy", {"placeholder": "dd/mm/yyyy"});
    });

    $('#addProcesso').on('shown.bs.modal', function () {
        $(".date").inputmask("dd/mm/yyyy", {"placeholder": "dd/mm/yyyy"});
    });

    $('#listProcessos').on('shown.bs.modal', function () {
        if (!$.fn.DataTable.isDataTable('#tableListProcessos')) {
            iniDataTable('#tableListProcessos');
        }
    });

});
function cadEmpenho(id_pedido, empenho, data) {
    document.getElementById('id_pedido_emp').value = '';
    document.getElementById('empenho').value = '';
    document.getElementById('dataEmp').value = '';
    $('#cadEmpenho').modal();
    document.getElementById('id_pedido_emp').value = id_pedido;
    if (empenho.length > 0) {
        document.getElementById('empenho').value = empenho;
    }
    if (data.length > 0) {
        document.getElementById('dataEmp').value = data;
    }
}

function cadFontes(id_pedido) {
    $('#cadFontes').modal();
    document.getElementById('id_pedido_fonte').value = id_pedido;
}

function enviaEmpenho() {
    var id_pedido = document.getElementById('id_pedido_emp').value;
    var empenho = document.getElementById('empenho').value;
    var data = document.getElementById('dataEmp').value;
    $.post('../php/geral.php', {
        admin: 1,
        form: 'enviaEmpenho',
        id_pedido: id_pedido,
        empenho: empenho,
        data: data
    }, function (resposta) {
        if (resposta) {
            $('#cadEmpenho').modal('hide');
            iniSolicitacoes();
            limpaTela();
        } else {
            alert('Ocorreu um erro no servidor. Contate o administrador.');
        }
    });
}

function enviaOrdenador(id_pedido) {
    if (confirm("Mudar o status do pedido para \"Enviado ao Ordenador\"?")) {
        $.post('../php/geral.php', {
            admin: 1,
            form: 'enviaOrdenador',
            id_pedido: id_pedido
        }, function (resposta) {
            if (resposta) {
                iniSolicitacoes();
            } else {
                alert('Ocorreu um erro no servidor. Contate o administrador.');
            }
        });
    }
}

function enviaFontes() {
    var id_pedido = document.getElementById('id_pedido_fonte').value;
    var fonte = document.getElementById('fonte').value;
    var ptres = document.getElementById('ptres').value;
    var plano = document.getElementById('plano').value;
    $.post('../php/geral.php', {
        admin: 1,
        form: 'enviaFontes',
        id_pedido: id_pedido,
        fonte: fonte,
        ptres: ptres,
        plano: plano
    }, function (resposta) {
        if (resposta) {
            $('#cadFontes').modal('hide');
            iniSolicitacoes();
            limpaTela();
        } else {
            alert('Ocorreu um erro no servidor. Contate o administrador.');
        }
    });
}

function verEmpenho(id_pedido) {
    $.post('../php/busca.php', {
        users: 1,
        form: 'verEmpenho',
        id_pedido: id_pedido,
    }, function (resposta) {
        if (resposta === 'EMPENHO SIAFI PENDENTE') {
            viewCompl(resposta);
        } else {
            viewCompl('Empenho: ' + resposta);
        }
    });
}

function hideDivs() {
    var divs = ['rowPedidos', 'rowDetPedido', 'rowSolicAdi', 'rowAltPed'];
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
    iniSolicitacoes();
}
function mostraSolicAdiant() {
    mostra('rowSolicAdi');
    iniTableSolicAdiant();
}

function mostraSolicAltPed() {
    mostra('rowAltPed');
    iniTableSolicAltPed();
}
// solicitação de alteração de pedidos
function analisaSolicAlt(id_solic, id_pedido, acao) {
    $.post('../php/geral.php', {
        admin: 1,
        form: 'analisaSolicAlt',
        id_solic: id_solic,
        id_pedido: id_pedido,
        acao: acao
    }, function (resposta) {
        if (resposta == false) {
            alert(resposta);
            window.location.href = 'index.php';
        } else {
            iniSolicitacoes();
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
        }).done(function (resposta) {
            location.reload();
        });
    }
}

function avisoSnack(aviso, corpo) {
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
        return;
    } else if (tabela == '#tableSolicitacoes') {
        $(tabela).DataTable({
            "destroy": true,
            "paging": true,
            "lengthChange": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": true,
            "columnDefs": [
                {"width": "15%", "targets": 0}
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
        return;
    }
    $(tabela).DataTable({
        "destroy": true,
        "paging": true,
        "lengthChange": true,
        "searching": true,
        "ordering": true,
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

function transfereSaldo() {
    $('#formTransferencia .btn').blur();
    var ori = document.getElementById('setorOri').value;
    var dest = document.getElementById('setorDest').value;
    var valor = document.getElementById('valorTransf').value;
    var just = document.getElementById('justTransf').value;
    $.post('../php/geral.php', {
        admin: 1,
        form: 'transfereSaldo',
        ori: ori,
        dest: dest,
        valor: valor,
        just: just
    }, function (resposta) {
        if (resposta) {
            alert('O valor de R$ ' + valor + ' foi transferido com SUCESSO! ');
            $('#tableListLancamentos').DataTable().destroy();
            $('#transferencia').modal('hide');
            document.getElementById('formTransferencia').reset();
        } else {
            alert('Saldo insuficiente para realizar a transferência');
        }
    });
}

function resetSenha() {
    document.getElementById("loaderResetSenha").style.display = 'inline-block';
    var email = document.getElementById('userReset').value;
    $.post('../php/geral.php', {
        form: 'resetSenha',
        email: email
    }, function (resposta) {
        if (resposta) {
            alert("Sua senha foi resetada e enviada para o seu e-mail.");
        } else {
            alert("Ocorreu um erro no servidor. Contate o administrador.");
        }
        $('#esqueceuSenha').modal('hide');
        document.getElementById('formReset').reset();
        document.getElementById("loaderResetSenha").style.display = 'none';
    });
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
    }, function (resposta) {
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

function carregaPostsPag(tabela) {
    document.getElementById('loader');
    $.post('../php/busca.php', {
        admin: 1,
        form: 'carregaPostsPag',
        tabela: tabela
    }, function (resposta) {
        $('#tableNoticiasEditar').DataTable().destroy();
        document.getElementById('contNoticiasEditar').innerHTML = resposta;
        iniDataTable('#tableNoticiasEditar');
    });
}

function iniAdminSolicitacoes() {
    $.post('../php/busca.php', {
        admin: 1,
        form: 'permissoes',
    }, function (resposta) {
        var permissao = jQuery.parseJSON(resposta);
        if (permissao.recepcao) {
            iniRecepcao();
        }
        if (permissao.pedidos) {
            iniSolicitacoes();
        }
        if (permissao.saldos) {
            getSaldoOri();
        }
    });
}

function iniRecepcao() {
    var element = document.getElementById('conteudoRecepcao');
    if (element === null) {
        return;
    }
    $.post('../php/buscaLTE.php', {
        admin: 1,
        form: 'tableRecepcao',
    }, function (resposta) {
        if (element.innerHTML.length > 0) {
            $('#tableRecepcao').DataTable().destroy();
        }
        element.innerHTML = resposta;
        iniDataTable('#tableRecepcao');
    });
}

function iniListProcessos() {
    $.post('../php/buscaLTE.php', {
        admin: 1,
        form: 'listProcessos',
    }, function (resposta) {
        if (document.getElementById('tbodyListProcessos').innerHTML.length > 0) {
            $('#tableListProcessos').DataTable().destroy();
        }
        document.getElementById('tbodyListProcessos').innerHTML = resposta;
        iniDataTable('#tableListProcessos');
    });
}

function addProcesso(numProcesso, id) {
    document.getElementById("id_processo").value = id;
    if (id === 0) {
        document.getElementById('num_processo').value = numProcesso;
    } else {
        $.post('../php/busca.php', {
            admin: 1,
            form: 'addProcesso',
            id_processo: id
        }, function (resposta) {
            var obj = jQuery.parseJSON(resposta);
            document.getElementById("num_processo").value = obj.num_processo;
            document.getElementById("tipo").value = obj.tipo;
            document.getElementById("estante").value = obj.estante;
            document.getElementById("prateleira").value = obj.prateleira;
            document.getElementById("entrada").value = obj.entrada;
            document.getElementById("saida").value = obj.saida;
            document.getElementById("responsavel").value = obj.responsavel;
            document.getElementById("retorno").value = obj.retorno;
            document.getElementById("obs").value = obj.obs;
        });
    }
    $('#addProcesso').modal();
}

function updateProcesso() {
    var campos = ["id_processo", "num_processo", "tipo", "estante", "prateleira", "entrada", "saida", "responsavel", "retorno", "obs"];
    var dados = [];
    for (var i = 0; i < campos.length; i++) {
        dados[i] = document.getElementById(campos[i]).value;
    }
    $.post('../php/geral.php', {
        admin: 1,
        form: 'recepcao',
        dados: dados
    }, function (resposta) {
        if (resposta === "true") {
            document.getElementById('formProcesso').reset();
            document.getElementById('id_processo').value = 0;
            alert('Sucesso!');
        } else {
            alert('Ocorreu um erro no servidor. Contate o administrador');
            window.location.href = "sair.php";
        }
    });
    iniRecepcao();
    iniListProcessos();
    $('#addProcesso').modal('hide');
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
    }, function (resposta) {
        putNumberFormat(resposta);
        $('#valorTransf').attr('max', resposta);
    });
}

function iniSolicitacoes() {
    var element = document.getElementById('conteudoSolicitacoes');
    if (element === null) {
        return;
    }
    $.post('../php/buscaLTE.php', {
        admin: 1,
        form: 'tableItensPedido'
    }, function (resposta) {
        if (element.innerHTML.length > 0) {
            $('#tableSolicitacoes').DataTable().destroy();
        }
        document.getElementById('conteudoSolicitacoes').innerHTML = resposta;
        iniDataTable('#tableSolicitacoes');
    });
}

function enviaForn(id_pedido) {
    $('a').blur();
    if (!confirm("O status do pedido " + id_pedido + " será alterado para 'Enviado ao Fornecedor'. \n\nDeseja Continuar?")) {
        return;
    }
    $.post('../php/geral.php', {
        admin: 1,
        form: 'enviaForn',
        id_pedido: id_pedido
    }, function (resposta) {
        iniSolicitacoes();
        avisoSnack('Pedido enviado ao Fornecedor', 'body');
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
    }, function (resposta) {
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

function liberaSaldo() {
    var setor = document.getElementById('setor').value;
    var valor = document.getElementById('valorFree').value;
    valor = parseFloat(valor).toFixed(3);
    $.post('../php/geral.php', {
        admin: 1,
        form: 'liberaSaldo',
        id_setor: setor,
        valor: valor
    }, function (resposta) {
        if (resposta) {
            alert('O valor de R$ ' + valor + ' foi acrescentado ao saldo do setor com SUCESSO.');
            location.reload();
        } else {
            alert('Ocorreu um erro no servidor. Contate o administrador.');
            window.location.href = 'sair.php';
        }
    });
}

function refreshSaldo() {
    $.post('../php/busca.php', {
        admin: 1,
        form: 'refreshSaldo'
    }, function (resposta) {
        document.getElementById('labelSaldoSOF').innerHTML = 'Saldo disponível: R$ ' + resposta;
    });
}
var userss = false;
$('#listLancamentos').on('shown.bs.modal', function (event) {
    if (!userss) {
        return;
    }
    if (!$.fn.DataTable.isDataTable('#tableListLancamentos')) {
        iniDataTable('#tableListLancamentos');
    }
});
function listLancamentos(id_setor) {
    $('#listLancamentos').modal('show');
    if (id_setor !== null) {
        changeSetor(id_setor);
    }
}

function changeSetor(id_setor) {
    var setor;
    if (id_setor != null) {
        setor = id_setor;
        userss = true;
    } else {
        setor = document.getElementById('selectSetor').value;
        userss = false;
    }
    $.post('../php/busca.php', {
        users: 1,
        form: 'listLancamentos',
        id_setor: setor
    }).done(function (resposta) {
        $('#tableListLancamentos').DataTable().destroy();
        $('#tbodyListLancamentos').html(resposta);
        if (id_setor == null) {
            iniDataTable('#tableListLancamentos');
        }
    });
    if (id_setor == null) {
        refreshDataSaldo(setor);
    }
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

$('#listSolicAltPedidos').on('shown.bs.modal', function (event) {
    if (!$.fn.DataTable.isDataTable('#tableSolicAltPedido')) {
        iniDataTable('#tableSolicAltPedido');
    }
});
function listSolicAltPedidos() {
    $('#listSolicAltPedidos').modal();
    if (!$.fn.DataTable.isDataTable('#tableListProcessos')) {
        $.post('../php/busca.php', {
            users: 1,
            form: 'iniSolicAltPedSetor'
        }, function (resposta) {
            document.getElementById('tbodySolicAltPedido').innerHTML = resposta;
        });
    }
}

function listProblemas() {
    if ($.fn.DataTable.isDataTable('#tableListProblemas')) {
        $('#tableListProblemas').DataTable().destroy();
    }
    $.post('../php/busca.php', {
        admin: 1,
        form: 'listProblemas'
    }).done(function (resposta) {
        $('#tbodyListProblemas').html(resposta);
    });
    $('#listProblemas').modal();
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

$('#listAdiantamentos').on('shown.bs.modal', function (event) {
    if (!$.fn.DataTable.isDataTable('#tableListAdiantamentos')) {
        iniDataTable('#tableListAdiantamentos');
    }
});
function listAdiantamentos() {
    $('#listAdiantamentos').modal('show');
    if (!$.fn.DataTable.isDataTable('#tableListProcessos')) {
        $.post('../php/busca.php', {
            users: 1,
            form: 'listAdiantamentos'
        }, function (resposta) {
            document.getElementById('tbodyListAdiantamentos').innerHTML = resposta;
        });
    }
}

function listRelatorios() {
    $('#listRelatorios').modal('show');
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

function changeReport(element) {
    var st = element.value;
    $.post('../php/buscaLTE.php', {
        admin: 1,
        form: 'listRelatorios',
        status: st
    }).done(function (resposta) {
        if (document.getElementById('tbodyListRelatorios').innerHTML.length > 0) {
            $('#tableListRelatorios').DataTable().destroy();
        }
        $('#tbodyListRelatorios').html(resposta);
        iniDataTable('#tableListRelatorios');
    });
    refreshTot(st);
}

function refreshTot(status) {
    $.post('../php/busca.php', {
        admin: 1,
        form: 'refreshTot',
        status: status
    }).done(function (resposta) {
        $('#relTotRow').html(resposta);
        document.getElementById('relTotRow').style.display = 'block';
    });
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

$('#listPedidos').on('shown.bs.modal', function (event) {
    if (!$.fn.DataTable.isDataTable('#tableListPedidos')) {
        iniDataTable('#tableListPedidos');
    }
});
function listPedidos() {
    $('#listPedidos').modal('show');
    if (!$.fn.DataTable.isDataTable('#tableListPedidos')) {
        $.post('../php/busca.php', {
            users: 1,
            form: 'listPedidos'
        }, function (resposta) {
            document.getElementById('tbodyListPedidos').innerHTML = resposta;
        });
    }
}
$('#listRascunhos').on('shown.bs.modal', function (event) {
    if (!$.fn.DataTable.isDataTable('#tableListRascunhos')) {
        iniDataTable('#tableListRascunhos');
    }
});
function listRascunhos() {
    $('#listRascunhos').modal('show');
    if (!$.fn.DataTable.isDataTable('#tableListRascunhos')) {
        $.post('../php/busca.php', {
            users: 1,
            form: 'listRascunhos'
        }, function (resposta) {
            document.getElementById('tbodyListRascunhos').innerHTML = resposta;
        });
    }
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
    }, function (resposta) {
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
    }, function (resposta) {
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
        }, function (resposta) {
            $('#tableListLancamentos').DataTable().destroy();
            iniTableSolicAdiant();
        });
    }
}

// FUNÇÃO DISPARADA QUANDO O ITEM É CLICADO P/ ADC ---------------------------------
function checkItemPedido(id_item, vl_unitario, qt_saldo) {
    var qtd_item = document.getElementById('qtd' + id_item).value;
    var itens = document.getElementsByClassName('classItens');
    for (var i = 0; i < itens.length; i++) {
        if (itens[i].value == id_item) {
            alert('Esse item já está contido no pedido. Verifique!');
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
            avisoSnack('QUANTIDADE INDISPONÍVEL !', 'body');
        } else {
            var valor = parseFloat(qtd_item * vl_unitario).toFixed(3);
            var total_pedido = document.getElementById('total_hidden').value;
            total_pedido += valor;
            saldo_total = parseFloat(document.getElementById("saldo_total").value);
            if (valor > saldo_total) {
                avisoSnack('SALDO INSUFICIENTE !', 'body');
            } else {
                addItemPedido(id_item, qtd_item, vl_unitario);
            }
        }
    }
}

// ADICIONA ITEM AO PEDIDO ---------------------------------------------------------
function addItemPedido(id_item, qtd, vl_unitario) {

//valor do pedido
    var valor = qtd * vl_unitario;
    t = document.getElementById('total_hidden').value;
    total = parseFloat(t) + parseFloat(valor);
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
    s = document.getElementById('saldo_total').value;
    saldo_total = parseFloat(s) - parseFloat(valor);
    document.getElementById('saldo_total').value = parseFloat(saldo_total).toFixed(3);
    document.getElementById('text_saldo_total').innerHTML = "R$ " + parseFloat(saldo_total).toFixed(3);
    $.post('../php/buscaLTE.php', {
        users: 1,
        form: 'addItemPedido',
        id_item: id_item,
        qtd: qtd
    }, function (resposta) {
        conteudoPedido = document.getElementById("conteudoPedido").innerHTML;
        document.getElementById("conteudoPedido").innerHTML = conteudoPedido + resposta;
        avisoSnack('Item Inserido ao Pedido !', 'body');
    });
}

function removeTableRow(id_item, valor) {
//valor do pedido
    t = document.getElementById('total_hidden').value;
    total = parseFloat(t) - parseFloat(valor);
    document.getElementById('total_hidden').value = parseFloat(total).toFixed(3);
    document.getElementById('total').value = "R$ " + parseFloat(total).toFixed(3);
    //saldo
    s = document.getElementById('saldo_total').value;
    saldo_total = parseFloat(s) + parseFloat(valor);
    document.getElementById('saldo_total').value = parseFloat(saldo_total).toFixed(3);
    document.getElementById('text_saldo_total').innerHTML = "R$ " + parseFloat(saldo_total).toFixed(3);
    var row = document.getElementById("row" + id_item);
    if (row.parentNode) {
        row.parentNode.removeChild(row);
    }
    avisoSnack('Item Removido do Pedido !', 'body');
}
// VISUALIZAR O COMPLEMENTO DO ITEM ------------------------------------------------
function viewCompl(texto) {
    document.getElementById("complementoItem").innerHTML = texto;
    $('#viewCompl').modal();
}
// PESQUISA POR UM PROCESSO CONTENDO ITENS -----------------------------------------
function pesquisarProcesso(busca) {
    $('#listProcessos').modal('hide');
    $.post('../php/buscaLTE.php', {
        users: 1,
        form: 'pesquisarProcesso',
        busca: busca
    }, function (resposta) {
        $('#tableProcessos').DataTable().destroy();
        $('#conteudoProcesso').html(resposta);
        iniDataTable('#tableProcessos');
        document.getElementById('numProc').innerHTML = "Processo: " + busca;
        avisoSnack('Busca Realizada com Sucesso !', 'body');
    });
}

function fillSaldo() {
    $.post('../php/busca.php', {
        users: 1,
        form: 'fillSaldo'
    }, function (resposta) {
        document.getElementById('text_saldo_total').innerHTML = 'R$ ' + resposta;
    });
    $.post('../php/busca.php', {
        users: 1,
        form: 'getSaldo'
    }, function (resposta) {
        document.getElementById('saldo_total').value = resposta;
    });
}

function limpaTelaSolic() {
    fillSaldo();
    document.getElementById('pedido').value = 0;
    document.getElementById('conteudoPedido').innerHTML = '';
    document.getElementById('total').value = 'R$ 0';
    document.getElementById('total_hidden').value = 0;
    document.getElementById('stRascunho').checked = true;
    $('#divObs').removeClass('control-highlight');
    document.getElementById('obs').value = '';
    // licitação
    for (var i = 1; i <= 6; i++) {
        document.getElementById('tipoLic' + i).checked = false;
    }
    $('#divNum').removeClass('control-highlight');
    document.getElementById('infoLic').value = '';
    document.getElementById('infoLic').required = true;
    $('#divProcOri').removeClass('control-highlight');
    document.getElementById('procOri').value = '';
    document.getElementById('procOri').required = false;
    document.getElementById('procOri').disabled = true;
    document.getElementById('gera').checked = false;
    document.getElementById('gera').required = false;
    document.getElementById('gera').disabled = true;
    document.getElementById('ngera').checked = false;
    document.getElementById('ngera').required = false;
    document.getElementById('ngera').disabled = true;
    document.getElementById('checkPedContr').checked = false;
    // opções de contrato
    for (var i = 1; i <= 3; i++) {
        document.getElementById('tipoCont' + i).required = false;
        document.getElementById('tipoCont' + i).checked = false;
    }
    $('#divSiafi').removeClass('control-highlight');
    document.getElementById('siafi').value = '';
}

function editaPedido(id_pedido) {
    limpaTelaSolic();
    $.post('../php/busca.php', {
        users: 1,
        form: 'populaRascunho',
        id_pedido: id_pedido
    }, function (retorno) {
        var obj = jQuery.parseJSON(retorno);
        //valor do pedido
        document.getElementById('total_hidden').value = obj.valor;
        document.getElementById('total').value = "R$ " + obj.valor;
        //saldo
        document.getElementById('saldo_total').value = parseFloat(obj.saldo - obj.valor).toFixed(3);
        document.getElementById('text_saldo_total').innerHTML = "R$ " + parseFloat(obj.saldo - obj.valor).toFixed(3);
        // obs
        $('#divObs').addClass('control-highlight');
        document.getElementById('obs').value = obj.obs;
    });
    document.getElementById("pedido").value = id_pedido;
    $('#listRascunhos').modal('hide');
    $.post('../php/busca.php', {
        users: 1,
        form: 'editaPedido',
        id_pedido: id_pedido
    }, function (resposta) {
        document.getElementById("conteudoPedido").innerHTML = resposta;
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
    }, function (resposta) {
        if (resposta !== false) {
            var obj = jQuery.parseJSON(resposta);
            $('#divSiafi').addClass('control-highlight');
            document.getElementById('siafi').value = obj.siafi;
            if (obj.id_tipo > 0) {
                document.getElementById('tipoCont' + obj.id_tipo).checked = true;
            }
            if (obj.pedido_contrato == 1) {
                document.getElementById('checkPedContr').checked = true;
            }
        }
    });
}

function populaGrupo(id_pedido) {
    $.post('../php/busca.php', {
        users: 1,
        form: 'populaGrupo',
        id_pedido: id_pedido
    }, function (resposta) {
        if (resposta) {
            $("#grupo").val(resposta).trigger("change");
        }
    });
}

function populaLicitacao(id_pedido) {
    $.post('../php/busca.php', {
        users: 1,
        form: 'populaLicitacao',
        id_pedido: id_pedido
    }, function (resposta) {
        if (resposta) {
            var obj = jQuery.parseJSON(resposta);
            document.getElementById('idLic').value = obj.id;
            $('#divNum').addClass('control-highlight');
            document.getElementById('infoLic').value = obj.numero;
            document.getElementById('tipoLic' + obj.tipo).checked = true;
            if (obj.tipo == 3 || obj.tipo == 4 || obj.tipo == 2) {
                $('#divUasg').addClass('control-highlight');
                $('#divProcOri').addClass('control-highlight');
                document.getElementById('uasg').value = obj.uasg;
                document.getElementById('procOri').value = obj.processo_original;
            }
            maybeDisableFields(!(obj.tipo == 3 || obj.tipo == 4 || obj.tipo == 2));
            var element = document.getElementById('tipoLic' + obj.tipo);
            changeTipoLic(element);
        }
    });
}

function login() {
    var user = document.getElementById("user").value;
    var senha = document.getElementById("senha").value;
    document.getElementById("loader").style.display = 'inline-block';
    $.post('../php/login.php', {
        login: user,
        senha: senha
    }, function (resposta) {
        if (resposta == "false") {
            document.getElementById("formLogin").reset();
            document.getElementById("aviso").style.display = 'flex';
        } else if (resposta == "desativado") {
            alert("Estamos realizando uma manutenção no momento. Tente fazer o login novamente dentro de 10min ;)");
        } else {
            window.location.href = '../';
        }
        document.getElementById("loader").style.display = 'none';
    });
}

function aviso() {
    var tile = document.getElementById("aviso");
    if (tile.parentNode) {
        tile.parentNode.removeChild(tile);
    }
    document.getElementById("card").style.marginTop = "-3%";
}

function btnPesquisa() {
    if (document.getElementById("pesquisa").style.display == "" ||
            document.getElementById("pesquisa").style.display == "none") {
        $("#pesquisa").slideDown(200);
    } else {
        $("#pesquisa").slideUp(200);
    }
}

function ver_noticia(id, tabela, slide) {
    $.post('../php/busca.php', {
        form: 'ver_noticia',
        id: id,
        tabela: tabela,
        slide: slide
    }, function (resposta) {
        window.location.href = 'ver_noticia.php';
    });
}

function addInputsArquivo() {
    qtd = document.getElementById('qtd-arquivos').value;
    $.post('../php/busca.php', {
        form: 'addInputsArquivo',
        qtd: qtd
    }, function (resposta) {
        // Quando terminada a requisição
        tabela_arq = document.getElementById('arquivos').innerHTML;
        document.getElementById('arquivos').innerHTML = tabela_arq + "" + resposta;
        document.getElementById('qtd-arquivos').value = parseInt(qtd) + 1;
    });
}

function pesquisar() {
    $('button').blur();
    busca = document.getElementById("search").value;
    if (busca.length < 1) {
        alert('Digite alguma coisa para pesquisar! ;)');
        return;
    }
    $.post('../php/busca.php', {
        form: 'pesquisar',
        busca: busca
    }, function (resposta) {
        document.getElementById("conteudo").innerHTML = resposta;
    });
}

function imprimir(id_pedido) {
    $('button').blur();
    if (id_pedido == 0) {
        id_pedido = document.getElementById("pedido").value;
    }
    $.post('../php/busca.php', {
        users: 1,
        form: 'imprimirPedido',
        id_pedido: id_pedido
    }, function (resposta) {
        if (resposta === 2) {
            window.open("../admin/printPedido.php");
        } else {
            window.open("../view/printPedido.php");
        }
    });
}
// função para recepção para gerar relatórios
function print() {
    var tipo = document.getElementById('type').value;
    $.post('../php/busca.php', {
        admin: 1,
        form: 'relatorioProcessos',
        tipo: tipo
    }, function () {
        window.open("../admin/printRel.php");
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
    }, function (resposta) {
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
    }, function (retorno) {
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
    }, function (resposta) {
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
    if (!confirma)
        return;
    $.post('../php/geral.php', {
        users: 1,
        form: 'deletePedido',
        id_pedido: id_pedido
    }, function (resposta) {
        if (resposta != "true") {
            alert(resposta);
        } else {
            avisoSnack('Pedido deletado com sucesso !', 'body');
        }
        $('#tableListRascunhos').DataTable().destroy();
    });
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
    }, function (retorno) {
        var obj = jQuery.parseJSON(retorno);
        document.getElementById('text_saldo_total').innerHTML = "R$ " + parseFloat(obj.saldo).toFixed(3);
        //obs
        $('#divObs').addClass('control-highlight');
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
    avisoSnack('Carregamento concluído!', 'body');
}

function limpaTela() {
    $('button').blur();
    $('#divTableItens').html(tableItens);
    $('#tableSolicitacoes tr').css('background-color', '');
    $('#text_saldo_total').html('R$ 0.000');
    for (var i = 2; i <= 8; i++) {
        $('#st' + i).iCheck('disable');
    }
    $('#checkExcluir').iCheck('uncheck');
    document.getElementById('formPedido').reset();
    $('#btnLimpa').blur();
    document.getElementById('rowDetPedido').style.display = "none";
    avisoSnack('Tela Limpa', 'body');
}
// cancelar um item
function cancelaItem(id_item) {
    var icone = document.getElementById("icon-cancela-item" + id_item);
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
    $('a').blur();
    $('#infoItem').modal();
    document.getElementById('idItem').value = id_item;
    $.post('../php/busca.php', {
        admin: 1,
        form: 'infoItem',
        id_item: id_item
    }, function (retorno) {
        var obj = jQuery.parseJSON(retorno);
        document.getElementById('compItem').value = obj.complemento_item;
        document.getElementById('vlUnitario').value = obj.vl_unitario;
        document.getElementById('qtContrato').value = obj.qt_contrato;
        document.getElementById('vlContrato').value = obj.vl_contrato;
        document.getElementById('qtUtilizada').value = obj.qt_utilizado;
        document.getElementById('vlUtilizado').value = obj.vl_utilizado;
        document.getElementById('qtSaldo').value = obj.qt_saldo;
        document.getElementById('vlSaldo').value = obj.vl_saldo;
    });
}

function submitEditItem() {
    var fields = ['idItem', 'compItem', 'vlUnitario', 'qtContrato', 'vlContrato', 'qtUtilizada', 'vlUtilizado', 'qtSaldo', 'vlSaldo'];
    var dados = [];
    for (var i = 0; i < fields.length; i++) {
        dados[i] = document.getElementById(fields[i]).value;
    }
    $.post('../php/geral.php', {
        admin: 1,
        form: 'editItem',
        fields: fields,
        dados: dados
    }, function (resposta) {
        if (resposta) {
            alert('Informações salvas com sucesso!');
        } else {
            alert('Ocorreu um erro no servidor. Verifique suas informações e teste novamente, ou contate o administrador.');
        }
    });
    $('#infoItem').modal('hide');
    limpaTela();
    iniSolicitacoes();
}
//removendo um input de arquivo para adicionar notícias
function dropTile(id) {
    var tile = document.getElementById(id);
    if (tile.parentNode) {
        tile.parentNode.removeChild(tile);
    }
    qtd = document.getElementById('qtd-arquivos').value;
    document.getElementById('qtd-arquivos').value = parseInt(qtd) - 1;
}

function delArquivo(caminho) {
    del = confirm("Este arquivo será excluído PERMANENTEMENTE do sistema! Estando impossibilitada a sua recuperação.");
    if (del) {
        $.post('../php/geral.php', {
            admin: 1,
            form: 'delArquivo',
            caminhoDel: caminho
        }, function (resposta) {
            alert(resposta);
            window.location.href = "index.php";
        });
    }
}

function editaNoticia(id, tabela, data) {
    document.getElementById("funcao").value = "editar";
    document.getElementById("id_noticia").value = id;
    document.getElementById("tabela").value = tabela;
    document.getElementById("data").value = data;
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
        tabela = "op" + document.getElementById("tabela").value;
        document.getElementById(tabela).selected = true;
        $('#listNoticias').modal('hide');
    });
}

function recarregaForm() {
    document.getElementById("funcao").value = "novanoticia";
    document.getElementById("id_noticia").value = 0;
    document.getElementById("tabela").value = 0;
}

function excluirNoticia(id) {
    var confirma = confirm("Essa notícia será desativada do sistema. Deseja continuar?");
    if (confirma) {
        $.post('../php/geral.php', {
            admin: 1,
            form: 'excluirNoticia',
            id: id
        }, function (resposta) {
            if (!resposta) {
                window.location.href = "../";
            } else {
                carregaPostsPag(resposta);
            }
        });
    }
}