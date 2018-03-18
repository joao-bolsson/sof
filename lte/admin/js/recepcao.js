/**
 *
 * @author João Bolsson (joaovictorbolsson@gmail.com)
 * @since 2017, 27 May.
 */

$(function () {
    var modalProcNaoDev = $('#procNaoDev');

    modalProcNaoDev.on('shown.bs.modal', function () {
        $.post('../php/buscaLTE.php', {
            admin: 1,
            form: 'procNaoDev'
        }).done(function (resposta) {
            document.getElementById('tbodyProcNaoDev').innerHTML = resposta;
        });
    });

    modalProcNaoDev.on('hidden.bs.modal', function () {
        document.getElementById('tbodyProcNaoDev').innerHTML = "Aguarde...";
    });

    $("#formRelatorioRecepcao").submit(function (event) {
        event.preventDefault();
        var data = $(this).serialize();
        $.post('../php/busca.php', data).done(function () {
            window.open("../admin/printRel.php");
        });
    });

    $("#formProcesso").submit(function (event) {
        event.preventDefault();
        var data = $(this).serialize();

        $.post('../php/geral.php', data).done(function (resposta) {
            if (resposta === "true") {
                document.getElementById('formProcesso').reset();
                document.getElementById('id_processo').value = 0;
                alert('Sucesso!');
            } else {
                alert('Ocorreu um erro no servidor. Contate o administrador');
                window.location.href = "sair.php";
            }
        }).always(function () {
            iniRecepcao();
            iniListProcessos();
            $('#addProcesso').modal('hide');
            document.getElementById("formProcesso").reset();
        });
    });

    $('#addProcesso').on('shown.bs.modal', function () {
        $(".date").inputmask("dd/mm/yyyy", {"placeholder": "dd/mm/yyyy"});
    });
});

function iniRecepcao() {
    var element = document.getElementById('conteudoRecepcao');
    if (element === null) {
        return;
    }
    $.post('../php/buscaLTE.php', {
        admin: 1,
        form: 'tableRecepcao'
    }).done(function (resposta) {
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
        form: 'listProcessos'
    }).done(function (resposta) {
        if (document.getElementById('tbodyListProcessos').innerHTML.length > 0) {
            $('#tableListProcessos').DataTable().destroy();
        }
        document.getElementById('tbodyListProcessos').innerHTML = resposta;
        iniDataTable('#tableListProcessos');
    });
}

function addProcesso(numProcesso, id) {
    $("input[name='id_processo']", "#formProcesso").val(id);
    if (id === 0) {
        $("input[name='num_processo']", "#formProcesso").val(numProcesso);
    } else {
        $.post('../php/busca.php', {
            admin: 1,
            form: 'addProcesso',
            id_processo: id
        }).done(function (resposta) {
            var obj = jQuery.parseJSON(resposta);

            $("input[name='num_processo']", "#formProcesso").val(obj.num_processo);
            $("input[name='tipo']", "#formProcesso").val(obj.tipo);
            $("input[name='estante']", "#formProcesso").val(obj.estante);
            $("input[name='prateleira']", "#formProcesso").val(obj.prateleira);
            $("input[name='entrada']", "#formProcesso").val(obj.entrada);
            $("input[name='saida']", "#formProcesso").val(obj.saida);
            $("input[name='responsavel']", "#formProcesso").val(obj.responsavel);
            $("input[name='retorno']", "#formProcesso").val(obj.retorno);
            $("textarea[name='obs']", "#formProcesso").val(obj.obs);
            $("input[name='vigencia']", "#formProcesso").val(obj.vigencia);
        });
    }
    $('#addProcesso').modal();
}
