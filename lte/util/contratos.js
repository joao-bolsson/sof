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


    // for (var k = 1; k <= 4; k++) {
    //     var perm = document.getElementById('perm' + k);
    //     if (perm !== null) {
    //         $('.minimal').iCheck({
    //             checkboxClass: 'icheckbox_minimal-blue',
    //             radioClass: 'iradio_minimal-blue'
    //         });
    //     }
    // }
});