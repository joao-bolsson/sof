/**
 *
 * @author João Bolsson (joaovictorbolsson@gmail.com)
 * @since 2017, 26 May.
 */

$(function () {
    $('#manageUsers').on('shown.bs.modal', function () {
        $.post('../php/buscaLTE.php', {
            admin: 1,
            form: 'getUsers'
        }, function (resposta) {
            document.getElementById('usersToDisable').innerHTML = resposta;
        });
    });

    // checkbox permissions to add new user
    for (var k = 1; k <= 5; k++) {
        var perm = document.getElementById('perm' + k);
        if (perm !== null) {
            $('#perm' + k).iCheck({
                checkboxClass: 'icheckbox_minimal-blue',
                radioClass: 'iradio_minimal-blue'
            });
        }
    }

    $('#listProblemas').on('shown.bs.modal', function () {
        if (!$.fn.DataTable.isDataTable('#tableListProblemas')) {
            iniDataTable('#tableListProblemas');
        }
    });

    $("#formDesativarUsuario").submit(function () {
        var data = $(this).serialize();

        document.getElementById('overlayLoad').style.display = 'block';
        document.getElementById('overlayLoadDetPed').style.display = 'block';

        $.ajax({
            type: "POST",
            url: "../php/geral.php",
            data: data,
            success: function () {
                $("#manageUsers").modal('hide');
                document.getElementById('overlayLoad').style.display = 'none';
                document.getElementById('overlayLoadDetPed').style.display = 'none';
                avisoSnack("Usuário desativado");
                document.getElementById('usersToDisable').innerHTML = '';
            }
        }).done(function (r) {
            if (r === 'fail') {
                alert('Erro ao desativar usuário. Contate o administrador.');
            }
        });

        return false;
    });
});

function listProblemas() {
    if ($.fn.DataTable.isDataTable('#tableListProblemas')) {
        $('#tableListProblemas').DataTable().destroy();
    }
    $.post('../php/busca.php', {
        admin: 1,
        form: 'listProblemas'
    }).done(function (resposta) {
        $('#tbodyListProblemas').html(resposta);
    });
    $('#listProblemas').modal();
}
