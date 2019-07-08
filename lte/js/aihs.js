/**
 *
 * @author João Bolsson (joaovictorbolsson@gmail.com)
 * @since 2019, 01 Jun.
 */

$(function () {
    $("#cadAIHS").on('shown.bs.modal', function () {
        $(".date").inputmask("dd/mm/yyyy", {"placeholder": "dd/mm/yyyy"});
    });

    $("#cadAIHS").on('hidden.bs.modal', function () {
        $('#formCadAIHS').trigger("reset");
        $("#formCadAIHS input[name=id]").val(0);
        changeAIHSType();
    });

    $('#formCadAIHS').submit(function (event) {
        event.preventDefault();

        var data = $(this).serialize();

        $.post('../php/geral.php', data).done(function (resposta) {
            alert(resposta);
        }).always(function () {
            $("#cadAIHS").modal('hide');
            loadTableAIHS();
        });
    });

    $("#cadReceita").on('shown.bs.modal', function () {
        $(".date").inputmask("dd/mm/yyyy", {"placeholder": "dd/mm/yyyy"});
    });

    $("#cadReceita").on('hidden.bs.modal', function () {
        $('#formCadReceita').trigger("reset");
        $("#formCadReceita input[name=id]").val(0);
    });

    $('#formCadReceita').submit(function (event) {
        event.preventDefault();

        var data = $(this).serialize();

        $.post('../php/geral.php', data).done(function (resposta) {
            alert(resposta);
        }).always(function () {
            $("#cadReceita").modal('hide');
            loadTableReceitas();
        });
    });

    loadTableAIHS();
    loadTableReceitas();

});

function editAIHS(id) {
    $.post('../php/busca.php', {
        users: 1,
        form: 'editAIHS',
        id: id
    }).done(function (resposta) {
        var obj = jQuery.parseJSON(resposta);
        $("#formCadAIHS input[name=id]").val(id);
        $("#formCadAIHS select[name=tipo]").val(obj.tipo);
        $("#formCadAIHS input[name=data]").val(obj.data_lanc);
        $("#formCadAIHS select[name=mes]").val(obj.mes);
        $("#formCadAIHS input[name=qtd]").val(obj.qtd);
        $("#formCadAIHS input[name=valor]").val(obj.valor);
        $("#formCadAIHS input[name=grupo]").val(obj.grupo);
        $("#formCadAIHS input[name=descricao]").val(obj.descricao);

        changeAIHSType();

        $('#cadAIHS').modal('show');
    });
}

function editReceita(id) {
    $.post('../php/busca.php', {
        users: 1,
        form: 'editReceita',
        id: id
    }).done(function (resposta) {
        var obj = jQuery.parseJSON(resposta);
        $("#formCadReceita input[name=id]").val(id);
        $("#formCadReceita select[name=tipo]").val(obj.tipo);
        $("#formCadReceita select[name=mes]").val(obj.competencia);
        $("#formCadReceita input[name=data]").val(obj.recebimento);
        $("#formCadReceita input[name=valor]").val(obj.valor);
        $("#formCadReceita input[name=pf]").val(obj.pf);
        $("#formCadReceita textarea[name=obs]").val(obj.observacoes);

        $('#cadReceita').modal('show');
    });
}

function loadTableReceitas() {
    var element = document.getElementById('tboodyReceitas');
    if (element === null) {
        return;
    }
    var load = document.getElementById('overlayLoadReceitas');
    if (load !== null) {
        load.style.display = 'block';
    }
    $.post('../php/buscaLTE.php', {
        users: 1,
        form: 'loadTableReceitas'
    }).done(function (resposta) {
        if ($.fn.DataTable.isDataTable('#tableReceitas')) {
            $('#tableReceitas').DataTable().destroy();
        }
        if (resposta.length > 0) {
            element.innerHTML = resposta;
        }
        iniDataTable('#tableReceitas');

        if (load !== null) {
            load.style.display = 'none';
        }
    });
}

function loadTableAIHS() {
    var element = document.getElementById('tboodyAIHS');
    if (element === null) {
        return;
    }
    var load = document.getElementById('overlayLoadAIHS');
    if (load !== null) {
        load.style.display = 'block';
    }
    $.post('../php/buscaLTE.php', {
        users: 1,
        form: 'loadTableAIHS'
    }).done(function (resposta) {
        if ($.fn.DataTable.isDataTable('#tableAIHS')) {
            $('#tableAIHS').DataTable().destroy();
        }
        if (resposta.length > 0) {
            element.innerHTML = resposta;
        }
        iniDataTable('#tableAIHS');

        if (load !== null) {
            load.style.display = 'none';
        }
    });
}

function removeAIHS(id) {
    if (confirm("Esta operação não pode ser desfeita. Deseja remover esse item?")) {
        $.post('../php/geral.php', {
            users: 1,
            form: 'removeAIHS',
            id: id
        }).done(function () {
            console.log("done!");
            loadTableAIHS();
        });
    }
}

function removeReceita(id) {
    if (confirm("Esta operação não pode ser desfeita. Deseja remover esse item?")) {
        $.post('../php/geral.php', {
            users: 1,
            form: 'removeReceita',
            id: id
        }).done(function () {
            console.log("done!");
            loadTableReceitas();
        });
    }
}

function changeAIHSType() {
    var tipo = document.getElementById("tipo").value;

    var disabled = !(tipo >= 5 && tipo <= 9);

    document.getElementById("grupo").disabled = disabled;
    document.getElementById("grupo").required = !disabled;
    document.getElementById("descr").disabled = disabled;
    document.getElementById("descr").required = !disabled;
}

