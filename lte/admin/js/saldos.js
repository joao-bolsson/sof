/**
 *
 * @author João Bolsson (joaovictorbolsson@gmail.com)
 * @since 2017, 27 May.
 */

$(function () {
    $("#formTransferencia").submit(function (event) {
        event.preventDefault();
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

    $("#formLiberaSaldo").submit(function (event) {
        event.preventDefault();
        var data = $(this).serializeArray();

        var valor = 0;
        // valor must have 3 decimals
        for (var i = 0; i < data.length; i++) {
            if (data[i].name === 'valor') {
                data[i].value = parseFloat(data[i].value).toFixed(3);
                valor = data[i].value;
            }
        }

        $.post('../php/geral.php', data).done(function (resposta) {
            if (resposta) {
                alert('O valor de R$ ' + valor + ' foi acrescentado ao saldo do setor com SUCESSO.');
                location.reload();
            } else {
                alert('Ocorreu um erro no servidor. Contate o administrador.');
                window.location.href = 'sair.php';
            }
        });

    });

});
