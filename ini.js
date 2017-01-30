$(function () {
    $('.modal').on('hidden.bs.modal', function () {
        $(this).removeClass('fv-modal-stack');
        $('body').data('fv_open_modals', $('body').data('fv_open_modals') - 1);
    });

    $('.modal').on('shown.bs.modal', function () {
        // keep track of the number of open modals
        if (typeof ($('body').data('fv_open_modals')) == 'undefined') {
            $('body').data('fv_open_modals', 0);
        }
        // if the z-index of this modal has been set, ignore.
        if ($(this).hasClass('fv-modal-stack')) {
            return;
        }
        $(this).addClass('fv-modal-stack');
        $('body').data('fv_open_modals', $('body').data('fv_open_modals') + 1);
        $(this).css('z-index', 1040 + (10 * $('body').data('fv_open_modals')));
        $('.modal-backdrop').not('.fv-modal-stack')
                .css('z-index', 1039 + (10 * $('body').data('fv_open_modals')));
        $('.modal-backdrop').not('fv-modal-stack')
                .addClass('fv-modal-stack');
    });
});

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

function resetSenha() {
    document.getElementById("loaderResetSenha").style.display = 'inline-block';
    var email = document.getElementById('userReset').value;
    $.post('../php/geral.php', {
        form: 'resetSenha',
        email: email
    }, function (resposta) {
        if (resposta) {
            alert("Sua senha foi resetada e enviada para o seu e-mail.");
        } else {
            alert("Ocorreu um erro no servidor. Contate o administrador.");
        }
        $('#esqueceuSenha').modal('hide');
        document.getElementById('formReset').reset();
        document.getElementById("loaderResetSenha").style.display = 'none';
    });
}

function altInfoUser() {
    document.getElementById("loader").style.display = 'inline-block';
    var nome = document.getElementById('nameUser').value;
    var email = document.getElementById('emailUser').value;
    var senhaAtual = document.getElementById('senhaAtualUser').value;
    var novaSenha = document.getElementById('senhaUser').value;
    $.post('../php/geral.php', {
        users: 1,
        form: 'infoUser',
        nome: nome,
        email: email,
        novaSenha: novaSenha,
        senhaAtual: senhaAtual
    }, function (resposta) {
        if (resposta) {
            $('#myInfos').modal('hide');
            document.getElementById('altInfo').reset();
            avisoSnack('Suas informações foram salvas com sucesso!', 'body');
            document.getElementById('nameUser').value = nome;
            document.getElementById('emailUser').value = email;
            document.getElementById('userLogado').innerHTML = nome;
        } else {
            alert("Ocorreu um erro no servidor. Contate o administrador.");
            location.reload();
        }
        document.getElementById("loader").style.display = 'none';
    });
}

function carregaPostsPag(tabela) {
    document.getElementById('loader');
    $.post('../php/busca.php', {
        admin: 1,
        form: 'carregaPostsPag',
        tabela: tabela
    }, function (resposta) {
        $('#tableNoticiasEditar').DataTable().destroy();
        document.getElementById('contNoticiasEditar').innerHTML = resposta;
        iniDataTable('#tableNoticiasEditar');
    });
}

function login() {
    var user = document.getElementById("user").value;
    var senha = document.getElementById("senha").value;
    document.getElementById("loader").style.display = 'inline-block';
    $.post('../php/login.php', {
        login: user,
        senha: senha
    }, function (resposta) {
        if (resposta == "false") {
            document.getElementById("formLogin").reset();
            document.getElementById("aviso").style.display = 'flex';
        } else if (resposta == "desativado") {
            alert("Estamos realizando uma manutenção no momento. Tente fazer o login novamente dentro de 10min ;)");
        } else {
            window.location.href = '../';
        }
        document.getElementById("loader").style.display = 'none';
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

function delArquivo(caminho) {
    del = confirm("Este arquivo será excluído PERMANENTEMENTE do sistema! Estando impossibilitada a sua recuperação.");
    if (del) {
        $.post('../php/geral.php', {
            admin: 1,
            form: 'delArquivo',
            caminhoDel: caminho
        }, function (resposta) {
            alert(resposta);
            window.location.href = "index.php";
        });
    }
}

function editaNoticia(id, tabela, data) {
    document.getElementById("funcao").value = "editar";
    document.getElementById("id_noticia").value = id;
    document.getElementById("tabela").value = tabela;
    document.getElementById("data").value = data;

    $.post('../php/busca.php', {
        admin: 1,
        form: 'editarNoticia',
        id: id
    }, function (resposta) {
        $('#txtnoticia').froalaEditor('destroy');
        document.getElementById("txtnoticia").value = "";
        $('#txtnoticia').froalaEditor({
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
        $('#txtnoticia').froalaEditor('html.insert', resposta, true);
        tabela = "op" + document.getElementById("tabela").value;
        document.getElementById(tabela).selected = true;
        $('#listNoticias').modal('hide');
    });
}

function recarregaForm() {
    document.getElementById("funcao").value = "novanoticia";
    document.getElementById("id_noticia").value = 0;
    document.getElementById("tabela").value = 0;
}

function excluirNoticia(id) {
    var confirma = confirm("Essa notícia será desativada do sistema. Deseja continuar?");
    if (confirma) {
        $.post('../php/geral.php', {
            admin: 1,
            form: 'excluirNoticia',
            id: id
        }, function (resposta) {
            if (!resposta) {
                window.location.href = "../";
            } else {
                carregaPostsPag(resposta);
            }
        });
    }
}