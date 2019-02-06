/**
 *
 * @author João Bolsson (joaovictorbolsson@gmail.com)
 * @since 2017, 30 May.
 */

$(function () {
    //Date picker
    $('#data').datepicker({
        autoclose: true,
        format: 'yyyy-mm-dd'
    });

    var minimal = $('.minimal');

    minimal.iCheck({
        checkboxClass: 'icheckbox_flat-blue',
        radioClass: 'iradio_flat-blue'
    });

    minimal.on('ifChecked', function () {
        loadPosts(this);
    });

    $('#txtnoticia').froalaEditor({
        language: 'pt_br',
        charCounterCount: false,
        heightMin: 100,
        heightMax: 400,
        // Set the image upload URL.
        imageUploadURL: '../admin/upload_image.php',
        // Set the file upload URL.
        fileUploadURL: '../admin/upload_file.php',
        // Set the image upload URL.
        imageManagerLoadURL: '../admin/load_images.php',
        // Set the image delete URL.
        imageManagerDeleteURL: '../admin/delete_image.php',
    });
});

function loadPosts(element) {
    if (element === null) {
        return;
    }
    var id = element.id.replace('pag', '');
    carregaPostsPag(id);
}

function carregaPostsPag(tabela) {
    document.getElementById('overlayLoad').style.display = 'block';
    $.post('../php/buscaLTE.php', {
        admin: 1,
        form: 'carregaPostsPag',
        tabela: tabela
    }, function (resposta) {
        $('#contNoticiasEditar').html(resposta);
        document.getElementById('overlayLoad').style.display = 'none';
    });
}

function editaNoticia(id, tabela) {
    document.getElementById('overlayLoadBox').style.display = 'block';
    document.getElementById("funcao").value = "editar";
    document.getElementById("id_noticia").value = id;
    document.getElementById("tabela").value = tabela;

    $.post('../php/busca.php', {
        admin: 1,
        form: 'editarNoticia',
        id: id
    }, function (resposta) {
        var txtNoticia = $('#txtnoticia');
        txtNoticia.froalaEditor('destroy');
        document.getElementById("txtnoticia").value = "";
        txtNoticia.froalaEditor({
            language: 'pt_br',
            charCounterCount: false,
            heightMin: 100,
            heightMax: 400,
            // Set the image upload URL.
            imageUploadURL: 'upload_image.php',
            // Set the file upload URL.
            fileUploadURL: 'upload_file.php',
            // Set the image upload URL.
            imageManagerLoadURL: 'load_images.php',
            // Set the image delete URL.
            imageManagerDeleteURL: 'delete_image.php',
        });
        txtNoticia.froalaEditor('html.insert', resposta, true);
        tabela = 'op' + document.getElementById('tabela').value;
        document.getElementById(tabela).selected = true;
        $('#listNoticias').modal('hide');
        document.getElementById('overlayLoadBox').style.display = 'none';
    });
}

function excluirNoticia(id) {
    var confirma = confirm("Essa notícia será desativada do sistema. Deseja continuar?");
    if (confirma) {
        $.post('../php/geral.php', {
            admin: 1,
            form: 'excluirNoticia',
            id: id
        }, function (resposta) {
            location.reload();
        });
    }
}

function limpaTela() {
    Pace.restart();
    $('button').blur();
}

function recarregaForm() {
    document.getElementById("funcao").value = "novanoticia";
    document.getElementById("id_noticia").value = 0;
    document.getElementById("tabela").value = 0;
}