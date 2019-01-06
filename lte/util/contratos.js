/**
 *
 * @author João Bolsson (joaovictorbolsson@gmail.com)
 * @since 2018, 12 Jun.
 */

function reloadTableContr(group) {
    $('#overlayLoad').css("display", "block");
    $('#tableContratos').DataTable().destroy();
    $.post('../php/busca.php', {
        admin: 1,
        form: 'fillTableProc',
        group: group
    }).done(function (resposta) {
        $('#conteudoContrato').html(resposta);
        iniDataTable('#tableContratos');
        $('#overlayLoad').css("display", "none");
    });
}

$(function () {
    $('#selectGroupTable').select2();

    iniDataTable('#tableContratos');

    $('#selectGroupTable').change(function () {
        reloadTableContr(this.value);
    });

    $('#selectGroupTable').change();

    $('#cadContrato').on('shown.bs.modal', function () {
        $(".date").inputmask("dd/mm/yyyy", {"placeholder": "dd/mm/yyyy"});
    });

    $('#cadContrato').on('hidden.bs.modal', function () {
        $('#formContr').trigger("reset");
        $("#formContr input[name=id]").val(0);
    });

    $('#formContr').submit(function (event) {
        event.preventDefault();

        var data = $(this).serialize();
        $.post('../php/geral.php', data).done(function (resposta) {
            var msg = "Ocorreu um erro no servidor. Contate o administrador.";
            if (resposta) {
                msg = "Contrato cadastrado!";
            }
            avisoSnack(msg);
            $('#selectGroupTable').change();
        }).always(function () {
            $("#cadContrato").modal('hide');
        });

        $.post('../php/busca.php', {
            admin: 1,
            form: 'getAllContracts'
        }).done(function (resposta) {
            document.getElementById('selectContrMens').innerHTML = resposta;
        });
    });

    $('#cadEmpresa').on('shown.bs.modal', function () {
        $("#formEmpresa input[name=cnpj]").inputmask("99.999.999/9999-99", {"placeholder": "99.999.999/9999-99"});
        $.post('../php/busca.php', {
            admin: 1,
            form: 'fillContratos'
        }).done(function (resposta) {
            document.getElementById('selectContr').innerHTML = resposta;
        }).always(function () {
            $('.select2').select2();
        });
    });

    $('#cadEmpresa').on('hidden.bs.modal', function () {
        $('#formEmpresa').trigger("reset");
        $('.minimal').iCheck('destroy');
        $('.select2').select2('destroy');
    });

    $('#formEmpresa').submit(function (event) {
        event.preventDefault();

        var data = $(this).serialize();
        $.post('../php/geral.php', data).done(function (resposta) {
            var msg = "Ocorreu um erro no servidor. Contate o administrador.";
            if (resposta) {
                msg = "Empresa cadastrada!";
            }
            avisoSnack(msg);
            $('#selectGroupTable').change();
        }).always(function () {
            $("#cadEmpresa").modal('hide');
        });
    });

    $('#cadMensalidade').on('shown.bs.modal', function () {
        $('.minimal').iCheck({
            checkboxClass: 'icheckbox_minimal-blue',
            radioClass: 'iradio_minimal-blue'
        });

        var id_contr = document.getElementById('selectContrMens').value;

        $.post('../php/busca.php', {
            admin: 1,
            form: 'selectGroupMens',
            id_contr: id_contr
        }).done(function (resposta) {
            document.getElementById('selectGroupMens').innerHTML = resposta;
        });

        var now = new Date();

        $("#formMensalidade select[name=mes]").val(now.getMonth() + 1);

        var checkReajuste = $("#formMensalidade input[name=checkReajuste]");
        var inputReajuste = $("#formMensalidade input[name=valorReajuste]");

        checkReajuste.on('ifChecked', function () {
            inputReajuste.prop('disabled', false);
            inputReajuste.prop('required', true);
        });

        checkReajuste.on('ifUnchecked', function () {
            inputReajuste.prop('disabled', true);
            inputReajuste.prop('required', false);
        });
    });

    $('#cadMensalidade').on('hidden.bs.modal', function () {
        $('#formMensalidade').trigger("reset");
        $('.minimal').iCheck('destroy');

        $("#formMensalidade input[name=checkReajuste]").prop('disabled', true);
        $("#formMensalidade input[name=checkReajuste]").prop('required', false);

        if ($('#mensalidades').is(':visible')) {
            $('#mensalidades').modal('hide');
        }
    });

    $('#formMensalidade').submit(function (event) {
        event.preventDefault();

        var data = $(this).serialize();

        /**
         * Workaround to gets the disabled select value.
         * @type {string}
         */
        data += "&contrato=" + $("#formMensalidade select[name=contrato]").val();

        $.post('../php/geral.php', data).done(function (resposta) {
            var msg = "Saldo do contrato insuficiente.";
            if (resposta) {
                msg = "Mensalidade cadastrada!";
            }
            avisoSnack(msg);

            var group = $('#selectGroupTable').val();

            reloadTableContr(group);
        }).always(function () {
            $("#cadMensalidade").modal('hide');
        });
    });

});

function showMensalidades(contrato) {
    $.post('../php/busca.php', {
        admin: 1,
        form: 'showMensalidades',
        id_contr: contrato
    }).done(function (resposta) {
        document.getElementById('conteudoMensalidades').innerHTML = resposta;
    }).always(function () {
        $('#mensalidades').modal('show');
    });
}

function editMens(id_contr, id_mes, id_ano) {
    $.post('../php/busca.php', {
        admin: 1,
        form: 'editMens',
        id_contr: id_contr,
        id_mes: id_mes,
        id_ano: id_ano
    }).done(function (resposta) {
        var obj = jQuery.parseJSON(resposta);

        $("#formMensalidade select[name=contrato]").val(id_contr);

        /**
         * Need to show the modal now because the operations in shown.bs.modal.
         */
        $('#cadMensalidade').modal('show');

        $("#formMensalidade select[name=grupo]").val(obj.id_grupo);
        $("#formMensalidade select[name=ano]").val(id_ano);
        $("#formMensalidade select[name=mes]").val(id_mes);
        $("#formMensalidade input[name=valor]").val(obj.valor);

        if (obj.reajuste > 0) {
            $("#formMensalidade input[name=checkReajuste]").attr('selected', 'selected');
            $("#formMensalidade input[name=valorReajuste]").val(obj.reajuste);
        }

        if (obj.nota) {
            $("#formMensalidade input[name=nota]").iCheck('check');
        }

        if (obj.aguardaOrcamento) {
            $("#formMensalidade input[name=checkAgOrc]").iCheck('check');
        }

        if (obj.paga) {
            $("#formMensalidade input[name=checkPaga]").iCheck('check');
        }
    });
}

function editContract(contrato) {
    $.post('../php/busca.php', {
        admin: 1,
        form: 'editContract',
        id: contrato
    }).done(function (resposta) {
        var obj = jQuery.parseJSON(resposta);
        $("#formContr input[name=id]").val(contrato);
        $("#formContr input[name=numero]").val(obj.numero);
        $("#formContr input[name=dt_inicio]").val(obj.dt_inicio);
        $("#formContr input[name=dt_fim]").val(obj.dt_fim);
        $("#formContr input[name=teto]").val(obj.teto);
        $("#formContr input[name=mensalidade]").val(obj.mensalidade);

        $('#cadContrato').modal('show');
    });
}

function addMensalidade(contrato, mensalidade) {
    $("#formMensalidade select[name=contrato]").val(contrato);
    $("#formMensalidade input[name=valor]").val(mensalidade);
    $('#cadMensalidade').modal('show');
}

function printContract(contrato) {
    window.open("printContrato.php?id=" + contrato);
}