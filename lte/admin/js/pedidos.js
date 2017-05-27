/**
 *
 * @author João Bolsson (joaovictorbolsson@gmail.com)
 * @since 2017, 26 May.
 */

$(function () {
    $('#cadEmpenho').on('shown.bs.modal', function () {
        $(".date").inputmask("dd/mm/yyyy", {"placeholder": "dd/mm/yyyy"});
    });

    $('#formEnviaEmpenho').submit(function (event) {
        event.preventDefault();
        $("input[name='id_pedido']", "#formEnviaEmpenho").removeAttr('disabled');
        var id_pedido = $("input[name='id_pedido']", "#formEnviaEmpenho").val();
        var data = $(this).serialize();

        dropTableSolic(id_pedido);
        document.getElementById('overlayLoad').style.display = 'block';
        document.getElementById('overlayLoadDetPed').style.display = 'block';

        $.post('../php/geral.php', data).done(function (resposta) {
            if (resposta) {
                $('#cadEmpenho').modal('hide');
                iniSolicitacoes(false, id_pedido);
                limpaTela();
            } else {
                alert('Ocorreu um erro no servidor. Contate o administrador.');
            }
        }).always(function () {
            $("input[name='id_pedido']", "#formEnviaEmpenho").attr('disabled', '');
        });
    });
});

function cadEmpenho(id_pedido, empenho, data) {
    $('#cadEmpenho').modal();

    $("input[name='id_pedido']", "#formEnviaEmpenho").val(id_pedido);
    $("input[name='empenho']", "#formEnviaEmpenho").val(empenho);
    $("input[name='data']", "#formEnviaEmpenho").val(data);
}
