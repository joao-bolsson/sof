/**
 *
 * @author João Bolsson (joaovictorbolsson@gmail.com)
 * @since 2017, 29 May.
 */

$(function () {
    var element_gera = document.getElementById('gera');

    if (element_gera !== null) {
        var gera = $('#gera');
        gera.iCheck({
            checkboxClass: 'icheckbox_minimal-blue',
            radioClass: 'iradio_minimal-blue'
        });
        gera.iCheck('disable');
    }

    var element_ngera = document.getElementById('ngera');
    if (element_ngera !== null) {
        var ngera = $('#ngera');
        ngera.iCheck({
            checkboxClass: 'icheckbox_minimal-blue',
            radioClass: 'iradio_minimal-blue'
        });
        ngera.iCheck('disable');
    }

    for (var i = 1; i <= 6; i++) {
        var element = document.getElementById('tipoLic' + i);
        if (element !== null) {
            var tipo = $('#tipoLic' + i);
            tipo.iCheck({
                checkboxClass: 'icheckbox_minimal-blue',
                radioClass: 'iradio_minimal-blue'
            });
            tipo.on('ifChecked', function () {
                changeTipoLic(this);
            });
        }
    }
});


