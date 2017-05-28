/**
 *
 * @author João Bolsson (joaovictorbolsson@gmail.com)
 * @since 2017, 27 May.
 */

$(function () {
    $("#formTransferencia").submit(function (event) {
        event.preventDefault();
        console.log('vai fazer transfencia');
        var data = $(this).serialize();

        $.post('../php/geral.php', data).done(function (resposta) {
            if (resposta === 'success') {
                alert('O valor foi transferido com SUCESSO! ');
            } else {
                alert('Saldo insuficiente para realizar a transferência');
            }
        }).always(function () {
            $('#tableListLancamentos').DataTable().destroy();
            $('#transferencia').modal('hide');
            document.getElementById('formTransferencia').reset();
        });
    });
});