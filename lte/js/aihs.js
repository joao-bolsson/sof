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

function changeAIHSType() {
    var tipo = document.getElementById("tipo").value;

    var disabled = !(tipo >= 5 && tipo <= 9);

    document.getElementById("grupo").disabled = disabled;
    document.getElementById("grupo").required = !disabled;
    document.getElementById("descr").disabled = disabled;
    document.getElementById("descr").required = !disabled;
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

