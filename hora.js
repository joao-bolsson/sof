/**
 * This file contains the mains functions used in lte/hora.php
 * 
 * @author João Bolsson (joaovictorbolsson@gmail.com)
 * @since 2017, 04 Mar.
 */

$(function () {
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
    $('#registerAdmin').on('shown.bs.modal', function () {
        loadAdminTable();
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

