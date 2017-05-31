$(function () {
    var modal = $('.modal');

    var body = $('body');

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

        var modal_backdrop = $('.modal-backdrop');
        $(this).addClass('fv-modal-stack');
        body.data('fv_open_modals', body.data('fv_open_modals') + 1);
        $(this).css('z-index', 1040 + (10 * body.data('fv_open_modals')));
        modal_backdrop.not('.fv-modal-stack')
            .css('z-index', 1039 + (10 * body.data('fv_open_modals')));
        modal_backdrop.not('fv-modal-stack').addClass('fv-modal-stack');
    });
});

/**
 * Essa função existe no util.min.js, para a versão LTE do sistema.
 *
 * Ela precisa ficar aqui para a versão antiga do site (público).
 *
 * @param id_modal
 */
function abreModal(id_modal) {
    $(id_modal).modal();
}

function avisoSnack(aviso, corpo) {
    $(corpo).snackbar({
        content: aviso,
        alive: 1500,
        show: function () {
            snackbarText++;
        }
    });
}

function aviso() {
    var tile = document.getElementById("aviso");
    if (tile.parentNode) {
        tile.parentNode.removeChild(tile);
    }
    document.getElementById("card").style.marginTop = "-3%";
}

function ver_noticia(id, tabela, slide) {
    $.post('../php/busca.php', {
        form: 'ver_noticia',
        id: id,
        tabela: tabela,
        slide: slide
    }, function (resposta) {
        window.location.href = 'ver_noticia.php';
    });
}

function addInputsArquivo() {
    qtd = document.getElementById('qtd-arquivos').value;
    $.post('../php/busca.php', {
        form: 'addInputsArquivo',
        qtd: qtd
    }, function (resposta) {
        // Quando terminada a requisição
        tabela_arq = document.getElementById('arquivos').innerHTML;
        document.getElementById('arquivos').innerHTML = tabela_arq + "" + resposta;
        document.getElementById('qtd-arquivos').value = parseInt(qtd) + 1;
    });
}

function pesquisar() {
    $('button').blur();
    busca = document.getElementById("search").value;
    if (busca.length < 1) {
        alert('Digite alguma coisa para pesquisar! ;)');
        return;
    }
    $.post('../php/busca.php', {
        form: 'pesquisar',
        busca: busca
    }, function (resposta) {
        document.getElementById("conteudo").innerHTML = resposta;
    });
}

//removendo um input de arquivo para adicionar notícias
function dropTile(id) {
    var tile = document.getElementById(id);
    if (tile.parentNode) {
        tile.parentNode.removeChild(tile);
    }
    qtd = document.getElementById('qtd-arquivos').value;
    document.getElementById('qtd-arquivos').value = parseInt(qtd) - 1;
}
