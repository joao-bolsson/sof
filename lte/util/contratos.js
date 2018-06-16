/**
 *
 * @author João Bolsson (joaovictorbolsson@gmail.com)
 * @since 2018, 12 Jun.
 */

$(function () {
    $('#selectGroupTable').select2();

    iniDataTable('#tableContratos');

    $('#selectGroupTable').change(function () {

        $('#overlayLoad').css("display", "block");
        $('#tableContratos').DataTable().destroy();
        $.post('../php/busca.php', {
            admin: 1,
            form: 'fillTableProc',
            group: this.value
        }).done(function (resposta) {
            $('#conteudoContrato').html(resposta);
            iniDataTable('#tableContratos');
            $('#overlayLoad').css("display", "none");
        });
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
    });

    $('#cadEmpresa').on('shown.bs.modal', function () {
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
        }).always(function () {
            $("#cadEmpresa").modal('hide');
        });
    });

    $('#cadMensalidade').on('shown.bs.modal', function () {
        $('.minimal').iCheck({
            checkboxClass: 'icheckbox_minimal-blue',
            radioClass: 'iradio_minimal-blue'
        });

        $.post('../php/busca.php', {
            admin: 1,
            form: 'fillContratos'
        }).done(function (resposta) {
            document.getElementById('selectContrMens').innerHTML = resposta;
        }).always(function () {
            $('.select2').select2();
        });
    });

    $('#cadMensalidade').on('hidden.bs.modal', function () {
        $('#formMensalidade').trigger("reset");
        $('.minimal').iCheck('destroy')
        $('.select2').select2('destroy');
    });

    $('#formMensalidade').submit(function (event) {
        event.preventDefault();

        var data = $(this).serialize();
        $.post('../php/geral.php', data).done(function (resposta) {
            var msg = "Ocorreu um erro no servidor. Contate o administrador.";
            if (resposta) {
                msg = "Mensalidade cadastrada!";
            }
            avisoSnack(msg);
        }).always(function () {
            $("#cadMensalidade").modal('hide');
        });
    });

});

function editContract(contrato) {
    $.post('../php/busca.php', {
        admin: 1,
        form: 'editContract',
        id: contrato
    }).done(function (resposta) {
        var obj = jQuery.parseJSON(resposta);
        $("#formContr input[name=id]").val(contrato);
        $("#formContr input[name=numero]").val(obj.numero);
        $("#formContr input[name=vigencia]").val(obj.vigencia);
        $("#formContr input[name=teto]").val(obj.teto);
        $("#formContr input[name=mensalidade]").val(obj.mensalidade);

        $('#cadContrato').modal('show');
    })
}