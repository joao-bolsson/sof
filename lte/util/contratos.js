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
});