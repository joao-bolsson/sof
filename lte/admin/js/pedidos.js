/**
 *
 * @author João Bolsson (joaovictorbolsson@gmail.com)
 * @since 2017, 26 May.
 */

$(function () {
    $('#cadEmpenho').on('shown.bs.modal', function () {
        $(".date").inputmask("dd/mm/yyyy", {"placeholder": "dd/mm/yyyy"});
    });

    $('#cadEmpenho').on('hidden.bs.modal', function () {
        $("input[name='id_pedido']", "#formEnviaEmpenho").attr('disabled', '');
        document.getElementById("formEnviaEmpenho").reset();
    });

    $('#cadFontes').on('hidden.bs.modal', function () {
        $("input[name='id_pedido']", "#formEnviaFontes").attr('disabled', '');
        document.getElementById("formEnviaFontes").reset();
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
            document.getElementById("formEnviaEmpenho").reset();
        });
    });

    $('#formEnviaFontes').submit(function (event) {
        event.preventDefault();
        $("input[name='id_pedido']", "#formEnviaFontes").removeAttr('disabled');
        var id_pedido = $("input[name='id_pedido']", "#formEnviaFontes").val();
        var data = $(this).serialize();

        dropTableSolic(id_pedido);
        document.getElementById('overlayLoad').style.display = 'block';
        document.getElementById('overlayLoadDetPed').style.display = 'block';

        $.post('../php/geral.php', data).done(function (resposta) {
            if (resposta) {
                $('#cadFontes').modal('hide');
                iniSolicitacoes(false, id_pedido);
                limpaTela();
            } else {
                alert('Ocorreu um erro no servidor. Contate o administrador.');
            }
        }).always(function () {
            $("input[name='id_pedido']", "#formEnviaFontes").attr('disabled', '');
            document.getElementById("formEnviaFontes").reset();
        });
    });
});

// button onclick
function cadEmpenho(id_pedido, empenho, data) {
    $('#cadEmpenho').modal();

    $("input[name='id_pedido']", "#formEnviaEmpenho").val(id_pedido);
    $("input[name='empenho']", "#formEnviaEmpenho").val(empenho);
    $("input[name='data']", "#formEnviaEmpenho").val(data);
}

// button onclick
function cadFontes(id_pedido) {
    $('#cadFontes').modal();
    $("input[name='id_pedido']", "#formEnviaFontes").val(id_pedido);
}
