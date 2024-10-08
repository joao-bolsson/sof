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
        });
    });

    $("#relFaturamento").on('shown.bs.modal', function () {
        $(".date").inputmask("dd/mm/yyyy", {"placeholder": "dd/mm/yyyy"});

        $.post('../php/busca.php', {
            users: 1,
            form: 'getOptContratos'
        }).done(function (resposta) {
            $("#relFaturamento select[name=contrato]").html(resposta);
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

    $('#formCadFatAprov').submit(function (event) {
        event.preventDefault();

        var data = $(this).serialize();

        $.post('../php/geral.php', data).done(function (resposta) {
            alert(resposta);
        }).always(function () {
            $("#cadFatAprov").modal('hide');
            loadTableFatAprov();
        });
    });

    $('#formCadContratualizacao').submit(function (event) {
        event.preventDefault();

        var data = $(this).serialize();

        $.post('../php/geral.php', data).done(function (resposta) {
            alert(resposta);
        }).always(function () {
            $("#cadContratualizacao").modal('hide');
            loadTableFatAprov();
        });
    });

    $("#cadFatAprov").on('shown.bs.modal', function () {
        $("#formCadFatAprov .date").inputmask("dd/mm/yyyy", {"placeholder": "dd/mm/yyyy"});
    });

    $("#cadFatAprov").on('hidden.bs.modal', function () {
        $('#formCadFatAprov').trigger("reset");
    });

    $("#cadContratualizacao").on('shown.bs.modal', function () {
        $("#formCadContratualizacao .date").inputmask("dd/mm/yyyy", {"placeholder": "dd/mm/yyyy"});
    });

    $("#cadContratualizacao").on('hidden.bs.modal', function () {
        $('#formCadContratualizacao').trigger("reset");
        $("#formCadContratualizacao input[name=id]").val(0);

        $('#listContratualizacoes').modal('hide');
    });

    $('#formCadFixos').submit(function (event) {
        event.preventDefault();

        var data = $(this).serialize();

        $.post('../php/geral.php', data).done(function (resposta) {
            alert(resposta);
        }).always(function () {
            $("#cadFixos").modal('hide');
        });
    });

    $("#cadFixos").on('hidden.bs.modal', function () {
        $('#formCadFixos').trigger("reset");
        $("#formCadFixos input[name=idContr]").val(0);
    });

    loadTableAIHS();
    loadTableReceitas();
    loadTableFatAprov();
});

function showContrs(id) {
    $.post('../php/buscaLTE.php', {
        users: 1,
        form: 'listContratualizacoes',
        id: id
    }).done(function (resposta) {
        if (document.getElementById('tbodyContr').innerHTML.length > 0) {
            $('#tableContr').DataTable().destroy();
        }
        $('#tbodyContr').html(resposta);
        iniDataTable('#tableContr');

        $('#listContratualizacoes').modal('show');
    });
}

function editFatAprov(id) {
    // TODO
    $.post('../php/busca.php', {
        users: 1,
        form: 'editFatAprov',
        id: id
    }).done(function (resposta) {
        var obj = jQuery.parseJSON(resposta);

        $("#formCadFatAprov input[name=data]").val(obj.data);
        $("#formCadFatAprov select[name=mes]").val(obj.competencia);
        $("#formCadFatAprov select[name=producao]").val(obj.producao);
        $("#formCadFatAprov select[name=financiamento]").val(obj.financiamento);
        $("#formCadFatAprov select[name=complexidade]").val(obj.complexidade);
        $("#formCadFatAprov input[name=valor]").val(obj.valor);

        $('#cadFatAprov').modal('show');
    });
}

function showValores(id) {
    // show Valores fixos
    $.post('../php/buscaLTE.php', {
        users: 1,
        form: 'showValoresFixos',
        id: id
    }).done(function (resposta) {
        if (document.getElementById('tbodyContr').innerHTML.length > 0) {
            $('#tableContr').DataTable().destroy();
        }
        $('#tbodyContr').html(resposta);
        iniDataTable('#tableContr');

        $('#listContratualizacoes').modal('show');
    });
}

function addContratualizacao(id) {
    $('#cadContratualizacao').modal('show');
}

function addValFixos(id) {
    $("#formCadFixos input[name=idContr]").val(id);
    $('#cadFixos').modal('show');
}

function addValVariaveis(id) {
    $("#formCadFatAprov input[name=idContr]").val(id);
    $('#cadFatAprov').modal('show');
}

function editContratualizacao(id) {
    $.post('../php/busca.php', {
        users: 1,
        form: 'editContratualizacao',
        id: id
    }).done(function (resposta) {
        var obj = jQuery.parseJSON(resposta);

        $("#formCadContratualizacao input[name=id]").val(id);
        $("#formCadContratualizacao input[name=contr]").val(obj.numero_contr);
        $("#formCadContratualizacao input[name=vigencia_ini]").val(obj.vigenc_ini);
        $("#formCadContratualizacao input[name=vigencia_fim]").val(obj.vigenc_fim);
        $("#formCadContratualizacao input[name=aditivo_ini]").val(obj.aditivo_ini);
        $("#formCadContratualizacao input[name=aditivo_fim]").val(obj.aditivo_fim);

        $('#cadContratualizacao').modal('show');
    });
}

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

function loadTableFatAprov() {
    var element = document.getElementById('tboodyFat');
    if (element === null) {
        return;
    }
    var load = document.getElementById('overlayLoadFat');
    if (load !== null) {
        load.style.display = 'block';
    }
    $.post('../php/buscaLTE.php', {
        users: 1,
        form: 'loadTableFatAprov'
    }).done(function (resposta) {
        if ($.fn.DataTable.isDataTable('#tableFatAprov')) {
            $('#tableFatAprov').DataTable().destroy();
        }
        if (resposta.length > 0) {
            element.innerHTML = resposta;
        }
        iniDataTable('#tableFatAprov');

        if (load !== null) {
            load.style.display = 'none';
        }
    });
}

function removeContratualizacao(id) {
    if (confirm("Esta operação não pode ser desfeita. Deseja remover esse item?")) {
        $.post('../php/geral.php', {
            users: 1,
            form: 'removeContratualizacao',
            id: id
        }).done(function () {
            $('#listContratualizacoes').modal('hide');
            loadTableFatAprov();
        });
    }
}

function removeFatAprov(id) {
    if (confirm("Esta operação não pode ser desfeita. Deseja remover esse item?")) {
        $.post('../php/geral.php', {
            users: 1,
            form: 'removeFatAprov',
            id: id
        }).done(function () {
            loadTableFatAprov();
        });
    }
}

function removeAIHS(id) {
    if (confirm("Esta operação não pode ser desfeita. Deseja remover esse item?")) {
        $.post('../php/geral.php', {
            users: 1,
            form: 'removeAIHS',
            id: id
        }).done(function () {
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

function hideOthers() {
    var divs = ['rowFaturamento', 'rowReceitas', 'rowFatAprov'];
    for (i = 0; i < divs.length; i++) {
        var element = document.getElementById(divs[i]);
        if (element !== null) {
            element.style.display = 'none';
        }
    }
}

function mostra(row) {
    hideOthers();
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

