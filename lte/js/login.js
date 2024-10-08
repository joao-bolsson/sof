/**
 *
 * @author João Bolsson (joaovictorbolsson@gmail.com)
 * @since 2017, 28 May.
 */

$(function () {

    $("#cadUser").on('shown.bs.modal', function () {
        $("#login").inputmask('99.999.999/9999-99');
    });

    $("#formLogin").submit(function (event) {
        document.getElementById("loader").style.display = 'block';
        event.preventDefault();

        var data = $(this).serialize();

        $.post('../php/login.php', data).done(function (resposta) {
            if (resposta == "false") {
                showInfo("Usuário ou senha inválidos");
                $("#groupUser").addClass("has-error");
                $("#groupPass").addClass("has-error");
            } else if (resposta == "desativado") {
                showInfo("Estamos realizando uma manutenção no momento. Tente fazer o login novamente dentro de 10min ;)");
            } else {
                window.location.href = '../';
            }
        }).always(function () {
            document.getElementById("loader").style.display = 'none';
        });
    });

    $("#formReset").submit(function (event) {
        event.preventDefault();
        document.getElementById('loaderFormReset').style.display = 'block';

        var data = $(this).serialize();

        $.post('../php/geral.php', data).done(function (resposta) {
            if (resposta) {
                showInfo("Sua senha foi resetada e enviada para o seu e-mail.");
            } else {
                showInfo("Ocorreu um erro no servidor. Contate o administrador.");
            }
        }).always(function () {
            document.getElementById('formReset').reset();
            document.getElementById('loaderFormReset').style.display = 'none';
            $('#esqueceuSenha').modal('hide');
        });
    });
});

function showInfo(text) {
    $("#textInfo").html(text);
    $("#info").modal();
}
