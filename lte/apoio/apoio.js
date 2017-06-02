/**
 *
 * @author João Bolsson (joaovictorbolsson@gmail.com)
 * @since 2017, 02 Jun.
 */

function enviaForn(id_pedido) {
    $('button').blur();
    if (!confirm("O status do pedido " + id_pedido + " será alterado para 'Enviado ao Fornecedor'. \n\nDeseja Continuar?")) {
        return;
    }
    dropTableSolic(id_pedido);
    $.post('../php/geral.php', {
        admin: 1,
        form: 'enviaForn',
        id_pedido: id_pedido
    }).done(function () {
        iniDataTable('#tableSolicitacoes');
        document.getElementById('overlayLoad').style.display = 'none';
        avisoSnack('Pedido enviado ao Fornecedor');
    });
}