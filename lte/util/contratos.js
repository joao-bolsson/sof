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

    $('#cadEmpresa').on('shown.bs.modal', function () {
        $('.minimal').iCheck({
            checkboxClass: 'icheckbox_minimal-blue',
            radioClass: 'iradio_minimal-blue'
        });
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