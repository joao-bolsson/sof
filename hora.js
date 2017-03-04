/**
 * This file contains the mains functions used in lte/hora.php
 * 
 * @author João Bolsson (joaovictorbolsson@gmail.com)
 * @since 2017, 04 Mar.
 */

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
        console.log('refreshing this page');
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

