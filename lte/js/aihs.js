/**
 *
 * @author João Bolsson (joaovictorbolsson@gmail.com)
 * @since 2019, 01 Jun.
 */

$(function () {
    $("#cadAIHS").on('shown.bs.modal', function () {
        $(".date").inputmask("dd/mm/yyyy", {"placeholder": "dd/mm/yyyy"});
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
function addAIHS() {
    document.getElementById('overlayLoad').style.display = 'block';
    console.log("Addin AIHS");
}

