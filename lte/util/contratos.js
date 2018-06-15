/**
 *
 * @author João Bolsson (joaovictorbolsson@gmail.com)
 * @since 2018, 12 Jun.
 */

$(function () {
    $('#cadContrato').on('shown.bs.modal', function () {
        $(".date").inputmask("dd/mm/yyyy", {"placeholder": "dd/mm/yyyy"});
    });

    $('#cadContrato').on('hidden.bs.modal', function () {
        $('#formContr').trigger("reset");
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
        }).always(function () {
            $("#cadContrato").modal('hide');
        });
    });

    $('#cadEmpresa').on('shown.bs.modal', function () {
        $('.minimal').iCheck({
            checkboxClass: 'icheckbox_minimal-blue',
            radioClass: 'iradio_minimal-blue'
        });
        $('.select2').select2();
    });

    $('#cadEmpresa').on('hidden.bs.modal', function () {
        $('#formEmpresa').trigger("reset");
        $('.minimal').iCheck('destroy');
    });

    $('#cadMensalidade').on('shown.bs.modal', function () {
        $('.minimal').iCheck({
            checkboxClass: 'icheckbox_minimal-blue',
            radioClass: 'iradio_minimal-blue'
        });
    });

    $('#cadMensalidade').on('hidden.bs.modal', function () {
        $('#formMensalidade').trigger("reset");
        $('.minimal').iCheck('destroy');
    });

});