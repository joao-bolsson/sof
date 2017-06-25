/**
 * This file contains the mains functions used in lte/hora.php
 *
 * @author João Bolsson (joaovictorbolsson@gmail.com)
 * @since 2017, 04 Mar.
 */

$(function () {

    $("#atestado").on('shown.bs.modal', function () {
        $(".date").inputmask("dd/mm/yyyy", {"placeholder": "dd/mm/yyyy"});
    });

    $('#formAtestado').submit(function (event) {
        event.preventDefault();

        var data = $(this).serialize();

        $.post('../php/geral.php', data).done(function (resposta) {
            if (resposta) {
                alert("Atestado cadastrado com sucesso.");
            } else {
                alert("Ocorreu um erro no servidor. Contate o administrador.")
            }
        }).always(function () {
            $("#atestado").modal('hide');
            document.getElementById("formAtestado").reset();
        });
    });

    $('#relRegister').on('shown.bs.modal', function () {
        $('#reservation').daterangepicker({
            locale: {
                format: 'DD/MM/YYYY',
                applyLabel: 'Aplicar',
                cancelLabel: 'Cancelar'
            },
            language: 'pt-BR'
        });
    });
    var modalAdmin = $('#registerAdmin');
    modalAdmin.on('shown.bs.modal', function () {
        loadAdminTable();
    });
    modalAdmin.on('hidden.bs.modal', function () {
        $('#tableRegisters').DataTable().destroy();
        $('#tbodyAdminTool').html('');
    });
});

/**
 * Function called to edit a log.
 */
function editLog(id) {
    $.post('../php/busca.php', {
        admin: 1,
        form: 'editLog',
        id: id
    }, function (resposta) {
        document.getElementById('idLog').value = id;
        var obj = jQuery.parseJSON(resposta);
        document.getElementById('nomeEdit').value = obj.nome;
        document.getElementById('entradaEdit').value = obj.entrada;
        document.getElementById('saidaEdit').value = obj.saida;
        $('#editLog').modal();
    });
}

function iniDataTable(tabela) {
    $(tabela).DataTable({
        "destroy": true,
        "paging": true,
        "lengthChange": true,
        "searching": true,
        "ordering": true,
        "order": [[1, "asc"]],
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

/**
 * Load table of admin tool
 */
function loadAdminTable() {
    document.getElementById('overlayLoadAdmin').style.display = 'block';
    $.post('../php/buscaLTE.php', {
        admin: 1,
        form: 'loadAdminTable'
    }, function (resposta) {
        $('#tbodyAdminTool').html(resposta);
        iniDataTable('#tableRegisters');
        document.getElementById('overlayLoadAdmin').style.display = 'none';
    });
}

/**
 * Function that refreshs the page.
 */
function refreshPage() {
    document.getElementById('overlayLoad').style.display = 'block';
    $.post('../php/busca.php', {
        admin: 1,
        form: 'consultaHora'
    }, function (resposta) {
        if (resposta == 1) {
            document.getElementById('btnIn').disabled = false;
            document.getElementById('btnOut').disabled = true;
        } else {
            document.getElementById('btnIn').disabled = true;
            document.getElementById('btnOut').disabled = false;
        }
        console.log('refreshing this page: ' + resposta);
        refreshTable();
    });
}

/**
 * Refres the table with logs.
 */
function refreshTable() {
    $.post('../php/buscaLTE.php', {
        admin: 1,
        form: 'refreshTableHora'
    }, function (resposta) {
        console.log('refreshing the table');
        $('#tboodyHora').html(resposta);
        document.getElementById('overlayLoad').style.display = 'none';
    });
}

/**
 * Function that register a log in or out
 */
function pointRegister(in_out) {
    document.getElementById('overlayLoad').style.display = 'block';
    console.log(in_out);
    $.post('../php/geral.php', {
        admin: 1,
        form: 'pointRegister',
        log: in_out
    }, function (resposta) {
        console.log(resposta);
        if (resposta == '') {
            refreshPage();
        } else {
            alert(resposta);
        }
    });
}

