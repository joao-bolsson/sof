/**
 *
 * @author João Bolsson (joaovictorbolsson@gmail.com)
 * @since 2017, 27 May.
 */

$(function () {
    $("#formRelatorioRecepcao").submit(function (event) {
        event.preventDefault();
        var data = $(this).serialize();
        $.post('../php/busca.php', data).done(function () {
            window.open("../admin/printRel.php");
        });
    });
});
