<?php
ini_set('display_erros', true);
error_reporting(E_ALL);

session_start();
if (!isset($_SESSION["id_setor"]) || $_SESSION["id_setor"] != 2) {
	header("Location: ../");
}
include_once '../class/Busca.class.php';
//instanciando classe de busca para popular o select de estados
$obj_Busca = new Busca();

$permissao = $obj_Busca->getPermissoes($_SESSION["id"]);
if (!$permissao->noticias) {
	header("Location: adminsolicitacoes.php");
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta content="IE=edge" http-equiv="X-UA-Compatible">
    <meta content="initial-scale=1.0, maximum-scale=1.0, user-scalable=no, width=device-width" name="viewport">
    <title>Setor de Orçamento e Finanças – HUSM</title>
    <link href="../sof_files/estilo.min.css" rel="stylesheet">
    <!-- css -->
    <link href="../material/css/base.min.css" rel="stylesheet">

    <!-- css for doc -->
    <link href="../material/css/project.min.css" rel="stylesheet">

    <!-- Include Font Awesome. -->
    <link href="../plugins/font-awesome-4.5.0/css/font-awesome.min.css" rel="stylesheet" type="text/css" />

    <!-- Include Editor style. -->
    <link href="../plugins/froala/css/froala_editor.min.css" rel="stylesheet" type="text/css" />
    <link href="../plugins/froala/css/froala_style.min.css" rel="stylesheet" type="text/css" />
    <link href="../plugins/animate/animate.min.css" rel="stylesheet">
    <!-- Include Code Mirror style -->
    <link rel="stylesheet" href="../plugins/codemirror/codemirror.min.css">

    <!-- Include Editor Plugins style. -->
    <link rel="stylesheet" href="../plugins/froala/css/plugins/char_counter.css">
    <link rel="stylesheet" href="../plugins/froala/css/plugins/code_view.css">
    <link rel="stylesheet" href="../plugins/froala/css/plugins/colors.css">
    <link rel="stylesheet" href="../plugins/froala/css/plugins/emoticons.css">
    <link rel="stylesheet" href="../plugins/froala/css/plugins/file.css">
    <link rel="stylesheet" href="../plugins/froala/css/plugins/fullscreen.css">
    <link rel="stylesheet" href="../plugins/froala/css/plugins/image.css">
    <link rel="stylesheet" href="../plugins/froala/css/plugins/image_manager.css">
    <link rel="stylesheet" href="../plugins/froala/css/plugins/line_breaker.css">
    <link rel="stylesheet" href="../plugins/froala/css/plugins/quick_insert.css">
    <link rel="stylesheet" href="../plugins/froala/css/plugins/table.css">
    <link rel="stylesheet" href="../plugins/froala/css/plugins/video.css">

    <link rel="stylesheet" type="text/css" href="../plugins/dataTables/datatables.min.css"/>
    <!-- favicon -->
    <link rel="icon" href="../favicon.ico">
</head>

<body class="page-brand">
    <header class="header header-transparent header-waterfall affix">
        <nav class="tab-nav pull-left hidden-xx">
            <ul class="nav nav-list">
                <li>
                  <a class="btn btn-flat waves-attach waves-light" href="javascript:abreModal('#myInfos');"><span class="text-white"><span class="icon">lock_outline</span><span id="userLogado"><?=$_SESSION["nome"]?></span></span></a>
              </li>
          </ul>
      </nav>
      <nav class="tab-nav pull-right hidden-xx">
        <ul class="nav nav-list">
            <li>
                <a class="btn btn-flat waves-attach waves-light" href="sair.php"><span class="text-white"><span class="icon">exit_to_app</span>SAIR</span></a>
            </li>
        </ul>
    </nav>
</header>
<main class="content">
    <div class="content-heading">
        <div class="container">
            <div class="row">
                <h1 class="heading module wow slideInRight animated"><img src="../sof_files/logo_blue.png" alt="Setor de Orçamento e Finanças – HUSM" /></h1>
                <div class="text-header module wow slideInLeft animated">
                    <p>Setor de Orçamento e Finanças</p>
                    <span>Hospital Universitário de Santa Maria</span>
                </div>
            </div>
        </div>
    </div>
    <div class="container">
        <div class="row">
            <section class="content-inner margin-top-no">
                <nav class="tab-nav ui-tab-tab">
                    <ul class="nav nav-list">
                        <li>
                            <a class="waves-attach" href="../view/"><span style="color: white;"><span class="icon">home</span>INÍCIO</span></a>
                        </li>
                        <li>
                            <a class="waves-attach" href="javascript:abreModal('#listArquivos');"><span style="color: white;"><span class="icon">insert_drive_file</span>LISTA DE ARQUIVOS</span></a>
                        </li>
                        <li>
                            <a class="waves-attach" href="javascript:abreModal('#listNoticias');"><span style="color: white;"><span class="icon">note_add</span>EDITAR NOTÍCIA</span></a>
                        </li>
                        <li>
                            <a class="waves-attach" href="adminsolicitacoes.php"><span style="color: white;"><span class="icon">payment</span>SOLICITAÇÕES</span></a>
                        </li>
                    </ul>
                </nav>
            </div><!-- ./row -->
            <div class="row">
                <div id="aviso" class="tile tile-orange margin-bottom margin-top-no">
                    <div class="tile-side pull-left">
                        <div class="avatar avatar-sm avatar-orange">
                            <span class="icon icon-lg text-white">error_outline</span>
                        </div>
                    </div>
                    <div class="tile-action tile-action-show">
                        <ul class="nav nav-list margin-no pull-right">
                            <li>
                                <a class="text-black-sec waves-attach waves-effect" href="javascript:aviso();"><span class="icon text-white">close</span></a>
                            </li>
                        </ul>
                    </div>
                    <div class="tile-inner">
                        <span class="text-overflow text-white" style="font-weight: bold; font-size: 12pt;">Exemplo de Link para um arquivo que está em "Lista de Arquivos": ../uploads/exemplo.pdf </span>
                    </div>
                </div>
                <div id="card" class="card margin-top-no">
                    <div class="card-main">
                        <div class="card-header card-brand">
                            <div class="card-header-side pull-left">
                                <p class="card-heading">Adicionar Notícia</p>
                            </div>
                        </div><!--  ./card-header -->
                        <form enctype="multipart/form-data" action="../php/geral.php" id="ajax_form" method="post" onreset="recarregaForm();">
                            <input type="hidden" name="admin" value="1"/>
                            <input type="hidden" name="form" value="novanoticia">
                            <div class="card-inner">
                                <textarea id="txtnoticia" name="postagem" required>

                                </textarea>
                                <input type="hidden" id="funcao" name="funcao" value="novanoticia"/>
                                <input type="hidden" id="id_noticia" name="id_noticia" value="0"/>
                                <input type="hidden" id="tabela" name="tabela" value="0"/>
                                <div>
                                    <h2 class="content-sub-heading">Postar em: </h2>
                                    <select id="pag" class="form-control" name="pag" required>
                                        <?=$obj_Busca->getPostarEm();?>
                                    </select>
                                </div>
                                <h2 class="content-sub-heading">Data de Publicação: </h2>
                                <div class="form-group">
                                    <input class="datepicker-adv form-control" id="data" type="text" name="data" placeholder="Escolha a data de publicação" required>
                                </div>
                            </div><!-- ./card-inner -->
                            <div class="card-action">
                                <div class="card-action-btn">
                                    <button class="btn btn-default waves-attach" type="reset" style="width: 50%;"><span class="icon">close</span>&nbsp;Limpar</button>
                                    <button class="btn btn-brand waves-attach" type="submit" style="width: 49%;"><span class="icon">check</span>&nbsp;Enviar</button>
                                </div>
                            </div>
                        </form>
                    </div><!-- ./card-main -->
                </div> <!-- ./card -->
            </div>
        </section>
    </div>
</div>
</main>
<footer class="footer">
    <div class="col-md-4 col-sm-6">
        <div class="container" style="text-align: center; margin-left: 100px;">
            <p>
                Hospital Universitário de Santa Maria<br>
                HUSM - UFSM - EBSERH<br>
                Endereço: Av. Roraima, 1000, Prédio 22<br>
                Bairro Camobi, Santa Maria - RS<br>
                CEP 97105-900
            </p>
        </div>
    </div>
    <div class="col-md-4 col-sm-6">
        <div class="conteiner">
            <div id="info-img" class="margin-bottom" style="text-align: center;">
                <a href="http://www.husm.ufsm.br/"><img src="../sof_files/logo husm.png" rel="" title=""></a>
                <a href="http://www.ebserh.gov.br/"><img src="../sof_files/logo ebserh.png" rel="" title=""></a>
                <a href="http://site.ufsm.br/"><img src="../sof_files/logo ufsm.png" rel="" title=""></a>
            </div>
        </div>
    </div>
    <div class="col-md-4 col-sm-6">
        <div class="container" style="text-align: center; margin-right: 100px;">
            <p>
                Telefone: (55) 3213 - 1610<br>
                E-mail: orcamentofinancashusm@gmail.com<br>
                Horário de Atend.: 07h às 12h e 13h às 17h
            </p>
        </div>
    </div>
</footer>
<div class="modal fade" id="listArquivos" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-heading">
                <a class="modal-close" data-dismiss="modal">×</a>
                <h2 class="modal-title content-sub-heading">Lista de Arquivos</h2>
            </div>
            <table class="table">
                <thead>
                    <th>Tipo</th>
                    <th>Nome</th>
                    <th>Opções</th>
                </thead>
                <tbody>
                    <?=$obj_Busca->getArquivos("");?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="modal fade" id="listNoticias" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-heading">
                <a class="modal-close" data-dismiss="modal">×</a>
                <h2 class="modal-title content-sub-heading">Editar Notícia</h2>
            </div>
            <div class="modal-inner">
                <table class="table">
                    <tr>
                        <?=$obj_Busca->getTabsNoticias();?>
                    </tr>
                </table>
                <table id="tableNoticiasEditar" class="table" style="width: 100%;">
                    <thead>
                        <tr>
                            <th>Título</th>
                            <th>Data de Publicação</th>
                            <th>Opções</th>
                        </tr>
                    </thead>
                    <tbody id="contNoticiasEditar">

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="myInfos" role="dialog">
    <div class="modal-dialog" style="width: 40%;">
        <div class="modal-content">
            <div class="modal-heading">
                <a class="modal-close" data-dismiss="modal">×</a>
                <h2 class="modal-title content-sub-heading">Informações do Usuário</h2>
            </div>
            <form id="altInfo" action="javascript:altInfoUser();" method="post">
                <div class="modal-inner">
                    <div class="form-group form-group-label">
                        <label class="floating-label" for="nameUser"><span class="icon">perm_identity</span>&nbsp;Nome</label>
                        <input class="form-control" id="nameUser" name="nameUser" type="text" value="<?=$_SESSION['nome']?>" required>
                    </div>
                    <div class="form-group form-group-label">
                        <label class="floating-label" for="emailUser"><span class="icon">message</span>&nbsp;E-mail</label>
                        <input class="form-control" id="emailUser" name="emailUser" type="email" value="<?=$_SESSION['email']?>" required>
                    </div>
                    <div class="form-group form-group-label">
                        <label class="floating-label" for="senhaAtualUser"><span class="icon">lock_outline</span>&nbsp;Senha Atual</label>
                        <input class="form-control" id="senhaAtualUser" name="senhaAtualUser" type="password" required>
                    </div>
                    <div class="form-group form-group-label">
                        <label class="floating-label" for="senhaUser"><span class="icon">lock_outline</span>&nbsp;Nova Senha</label>
                        <input class="form-control" id="senhaUser" name="senhaUser" type="password" required>
                    </div>
                    <div id="loader" class="progress-circular" style="margin-left: 45%; display: none;">
                        <div class="progress-circular-wrapper">
                            <div class="progress-circular-inner">
                                <div class="progress-circular-left">
                                    <div class="progress-circular-spinner"></div>
                                </div>
                                <div class="progress-circular-gap"></div>
                                <div class="progress-circular-right">
                                    <div class="progress-circular-spinner"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer margin-bottom">
                  <button class="btn btn-brand waves-attach waves-light waves-effect" type="submit" style="width: 100%;"><span class="icon">autorenew</span>&nbsp;Atualizar</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- js -->
<script src="../plugins/jQuery/jquery.min.js"></script>
<script src="../material/js/base.min.js"></script>

<!-- js for doc -->
<script src="../material/js/project.min.js"></script>

<!-- Include JS files. -->
<script type="text/javascript" src="../plugins/froala/js/froala_editor.min.js"></script>

<!-- Include Code Mirror. -->
<script type="text/javascript" src="../plugins/codemirror/codemirror.min.js"></script>
<script type="text/javascript" src="../plugins/codemirror/xml.min.js"></script>

<!-- Include Plugins. -->
<script type="text/javascript" src="../plugins/froala/js/plugins/align.min.js"></script>
<script type="text/javascript" src="../plugins/froala/js/plugins/char_counter.min.js"></script>
<script type="text/javascript" src="../plugins/froala/js/plugins/code_beautifier.min.js"></script>
<script type="text/javascript" src="../plugins/froala/js/plugins/code_view.min.js"></script>
<script type="text/javascript" src="../plugins/froala/js/plugins/colors.min.js"></script>
<script type="text/javascript" src="../plugins/froala/js/plugins/emoticons.min.js"></script>
<script type="text/javascript" src="../plugins/froala/js/plugins/entities.min.js"></script>
<script type="text/javascript" src="../plugins/froala/js/plugins/file.min.js"></script>
<script type="text/javascript" src="../plugins/froala/js/plugins/font_family.min.js"></script>
<script type="text/javascript" src="../plugins/froala/js/plugins/font_size.min.js"></script>
<script type="text/javascript" src="../plugins/froala/js/plugins/fullscreen.min.js"></script>
<script type="text/javascript" src="../plugins/froala/js/plugins/image.min.js"></script>
<script type="text/javascript" src="../plugins/froala/js/plugins/image_manager.min.js"></script>
<script type="text/javascript" src="../plugins/froala/js/plugins/inline_style.min.js"></script>
<script type="text/javascript" src="../plugins/froala/js/plugins/line_breaker.min.js"></script>
<script type="text/javascript" src="../plugins/froala/js/plugins/link.min.js"></script>
<script type="text/javascript" src="../plugins/froala/js/plugins/lists.min.js"></script>
<script type="text/javascript" src="../plugins/froala/js/plugins/paragraph_format.min.js"></script>
<script type="text/javascript" src="../plugins/froala/js/plugins/paragraph_style.min.js"></script>
<script type="text/javascript" src="../plugins/froala/js/plugins/quick_insert.min.js"></script>
<script type="text/javascript" src="../plugins/froala/js/plugins/quote.min.js"></script>
<script type="text/javascript" src="../plugins/froala/js/plugins/table.min.js"></script>
<script type="text/javascript" src="../plugins/froala/js/plugins/save.min.js"></script>
<script type="text/javascript" src="../plugins/froala/js/plugins/url.min.js"></script>
<script type="text/javascript" src="../plugins/froala/js/plugins/video.min.js"></script>

<!-- Include Language file if we want to use it. -->
<script type="text/javascript" src="../plugins/froala/js/languages/pt_br.min.js"></script>

<!-- Initialize the editor. -->
<script>
    $(function () {
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
        $('#data').datepicker({
            format: "yyyy-mm-dd",
            selectMonths: false,
            selectYears: 1
        });
    });
</script>

<script type="text/javascript" src="../plugins/dataTables/datatables.min.js"></script>

<script type="text/javascript" src="../ini.min.js"></script>
</body>
</html>
