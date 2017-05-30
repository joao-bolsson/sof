/**
 *
 * @author João Bolsson (joaovictorbolsson@gmail.com)
 * @since 2017, 30 May.
 */

$(function () {
    var modal = $('.modal');
    var body = $('body');
    var modal_backdrop = $('.modal-backdrop');

    modal.on('hidden.bs.modal', function () {
        $(this).removeClass('fv-modal-stack');
        body.data('fv_open_modals', body.data('fv_open_modals') - 1);
    });

    modal.on('shown.bs.modal', function () {
        // keep track of the number of open modals
        if (typeof (body.data('fv_open_modals')) == 'undefined') {
            body.data('fv_open_modals', 0);
        }
        // if the z-index of this modal has been set, ignore.
        if ($(this).hasClass('fv-modal-stack')) {
            return;
        }
        $(this).addClass('fv-modal-stack');
        body.data('fv_open_modals', body.data('fv_open_modals') + 1);
        $(this).css('z-index', 1040 + (10 * body.data('fv_open_modals')));
        modal_backdrop.not('.fv-modal-stack')
            .css('z-index', 1039 + (10 * body.data('fv_open_modals')));
        modal_backdrop.not('fv-modal-stack')
            .addClass('fv-modal-stack');
    });

    $('#myInfos').on('hidden.bs.modal', function () {
        document.getElementById('altInfo').reset();
    });

    $('#altInfo').submit(function (event) {
        event.preventDefault();

        var data = $(this).serialize();

        var nome = $("input[name='nome']", "#altInfo").val();
        $.post('../php/geral.php', data).done(function (callback) {
            if (callback) {
                $('#myInfos').modal('hide');
                document.getElementById('altInfo').reset();
                alert('Suas informações foram salvas com sucesso!');
                var userLogado = document.getElementById('userLogado');
                if (userLogado !== null) {
                    userLogado.innerHTML = nome;
                }

                var userLogadoP = document.getElementById('userLogadop');
                if (userLogadoP !== null) {
                    userLogadoP.innerHTML = nome;
                }
            } else {
                alert("Ocorreu um erro no servidor. Contate o administrador.");
                location.reload();
            }
        });
    });
});
