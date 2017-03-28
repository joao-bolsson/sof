<?php
/**
 *  Interface usada pelo Setor de Orçamento para gerenciar postagens no site.
 *
 *  @author João Bolsson (joaovictorbolsson@gmail.com).
 *  @since 2017, 26 Feb.
 */
ini_set('display_errors', true);
error_reporting(E_ALL);

session_start();
spl_autoload_register(function (string $class_name) {
    include_once '../class/' . $class_name . '.class.php';
});
$obj_Util = Util::getInstance();
$permissao = BuscaLTE::getPermissoes($_SESSION["id"]);
if (!isset($_SESSION["id_setor"]) || $_SESSION["id_setor"] != 2 || !$permissao->noticias) {
    header("Location: ../");
}
require_once '../defines.php';
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>Setor de Orçamento e Finanças – HUSM</title>
        <!-- Tell the browser to be responsive to screen width -->
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
        <!-- Bootstrap 3.3.6 -->
        <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
        <!-- bootstrap datepicker -->
        <link rel="stylesheet" href="plugins/datepicker/datepicker3.css">
        <!-- DataTables -->
        <link rel="stylesheet" href="plugins/datatables/dataTables.bootstrap.css">
        <!-- Font Awesome -->
        <link rel="stylesheet" href="plugins/font-awesome-4.7.0/css/font-awesome.min.css">
        <!-- Ionicons -->
        <link rel="stylesheet" href="plugins/ionicons/css/ionicons.min.css">
        <!-- iCheck for checkboxes and radio inputs -->
        <link rel="stylesheet" href="plugins/iCheck/all.css">
        <!-- Theme style -->
        <link rel="stylesheet" href="dist/css/AdminLTE.min.css">
        <!-- AdminLTE Skins. Choose a skin from the css/skins
             folder instead of downloading all of them to reduce the load. -->
        <link rel="stylesheet" href="dist/css/skins/_all-skins.min.css">
        <!-- Custom snackbar style -->
        <link rel="stylesheet" href="dist/css/snackbar.min.css">
        <!-- Pace style -->
        <link rel="stylesheet" href="plugins/pace/pace.min.css">

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

        <link rel="icon" href="../favicon.ico">

        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
    </head>
    <!-- ADD THE CLASS layout-top-nav TO REMOVE THE SIDEBAR. -->
    <body class="hold-transition skin-blue layout-top-nav">
        <div class="wrapper">

            <header class="main-header">
                <nav class="navbar navbar-static-top">
                    <div class="container">
                        <div class="navbar-header">
                            <a href="index.php" class="navbar-brand"><b>SOF</b>HUSM</a>
                            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse">
                                <i class="fa fa-bars"></i>
                            </button>
                        </div>

                        <div class="collapse navbar-collapse pull-left" id="navbar-collapse">
                            <ul class="nav navbar-nav">
                                <li><a href="javascript:abreModal('#listArquivos');"><i class="fa fa-folder-open"></i> Lista de Arquivos</a></li>
                                <li><a href="javascript:abreModal('#listNoticias');"><i class="fa fa-newspaper-o"></i> Editar Notícia</a></li>
                            </ul>
                        </div>

                        <!-- Navbar Right Menu -->
                        <div class="navbar-custom-menu">
                            <ul class="nav navbar-nav">
                                <!-- User Account Menu -->
                                <!-- User Account: style can be found in dropdown.less -->
                                <li class="dropdown user user-menu">
                                    <a href="javascript:abreModal('#myInfos');" class="dropdown-toggle">
                                        <img src="dist/img/user.png" class="user-image" alt="User Image">
                                        <span id="userLogado" class="hidden-xs"><?= $_SESSION["nome"] ?></span>
                                    </a>
                                </li>
                                <li>
                                    <a href="../admin/sair.php"><i class="fa fa-power-off"></i></a>
                                </li>
                            </ul>
                        </div>
                        <!-- /.navbar-custom-menu -->
                    </div>
                    <!-- /.container-fluid -->
                </nav>
            </header>
            <!-- Full Width Column -->
            <div class="content-wrapper">
                <div class="container">
                    <!-- Content Header (Page header) -->
                    <section class="content-header">
                        <h1>
                            <?= $_SESSION['nome_setor']; ?>
                        </h1>
                        <ol class="breadcrumb">
                            <?php if ($permissao->pedidos || $permissao->recepcao || $permissao->saldos): ?>
                                <li><a href="index.php"><i class="fa fa-dashboard"></i> Início</a></li>
                                <li class="active">Publicações</li>
                            <?php else : ?>
                                <li><a href="posts.php"><i class="fa fa-dashboard"></i> Publicações</a></li>
                            <?php endif; ?>
                        </ol>
                    </section>

                    <!-- Main content -->
                    <section class="content">
                        <div class="box box-primary">
                            <div id="overlayLoadBox" class="overlay" style="display: none;">
                                <i class="fa fa-refresh fa-spin"></i>
                            </div>
                            <div class="box-header with-border">
                                <h3 class="box-title">Adicionar Notícia</h3>
                            </div>
                            <form enctype="multipart/form-data" action="../php/geral.php" id="ajax_form" method="post" onreset="recarregaForm();">
                                <input type="hidden" name="admin" value="1"/>
                                <input type="hidden" name="form" value="novanoticia">
                                <div class="box-body">
                                    <div class="alert alert-info alert-dismissible">
                                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                        <h4><i class="icon fa fa-info"></i> Informação!</h4>
                                        Exemplo de Link para um arquivo que está em "Lista de Arquivos": ../uploads/exemplo.pdf
                                    </div>
                                    <div class="form-group">
                                        <textarea id="txtnoticia" name="postagem" required>

                                        </textarea>
                                    </div>
                                    <input type="hidden" id="funcao" name="funcao" value="novanoticia"/>
                                    <input type="hidden" id="id_noticia" name="id_noticia" value="0"/>
                                    <input type="hidden" id="tabela" name="tabela" value="0"/>
                                    <div class="form-group">
                                        <label>Postar em:</label>
                                        <select id="pag" class="form-control" name="pag" required>
                                            <?= BuscaLTE::getPostarEm(); ?>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Data de Publicação:</label>
                                        <div class="input-group date">
                                            <div class="input-group-addon">
                                                <i class="fa fa-calendar"></i>
                                            </div>
                                            <input type="text" class="form-control pull-right" id="data" name="data" required>
                                        </div><!-- /.input group -->
                                    </div>
                                </div><!-- ./box-body -->
                                <div class="box-footer">
                                    <div class="btn-group" style="width: 100%;">
                                        <button id="btnLimpa" class="btn btn-default" type="reset" style="width: 49%;" onclick="limpaTela();"><i class="fa fa-undo"></i>&nbsp;Limpar</button>
                                        <button class="btn btn-primary" type="submit" style="width: 50%;"><i class="fa fa-send"></i> Publicar</button>
                                    </div>
                                </div>
                            </form>
                            <!-- /.box-body -->
                        </div>
                        <!-- /.box -->
                    </section>
                    <!-- /.content -->
                </div>
                <!-- /.container -->
            </div>
            <!-- /.content-wrapper -->
            <div id="snackbar"></div>
            <footer class="main-footer">
                <div class="container">
                    <div class="pull-right hidden-xs">
                        <b>Version</b> <?= VERSION ?>
                    </div>
                    <?= COPYRIGHT ?>
                </div>
                <!-- /.container -->
            </footer>
            <div class="modal fade" id="listArquivos" role="dialog">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title">Lista de Arquivos</h4>
                        </div>
                        <div class="modal-body">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Tipo</th>
                                        <th>Nome</th>
                                    </tr>
                                </thead>
                                <tbody><?= $obj_Util->getArquivosLTE(); ?></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="listNoticias" role="dialog">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title">Editar Notícia</h4>
                        </div>
                        <div class="modal-body">
                            <div id="overlayLoad" class="overlay" style="display: none;">
                                <i class="fa fa-refresh fa-spin"></i>
                            </div>
                            <table class="table">
                                <tr>
                                    <?= BuscaLTE::getTabsNoticiasLTE(); ?>
                                </tr>
                            </table>
                            <table id="tableNoticiasEditar" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Título</th>
                                        <th>Data de Publicação</th>
                                        <th>Opções</th>
                                    </tr>
                                </thead>
                                <tbody id="contNoticiasEditar"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="myInfos" role="dialog">
                <div class="modal-dialog" style="width: 40%;">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" >Informações do Usuário</h4>
                        </div>
                        <form id="altInfo" action="javascript:altInfoUser();" method="post">
                            <div class="modal-body">
                                <div class="form-group">
                                    <label>Nome</label>
                                    <input class="form-control" id="nameUser" name="nameUser" type="text" value="<?= $_SESSION['nome'] ?>" required>
                                </div>
                                <div class="form-group">
                                    <label>E-mail</label>
                                    <input class="form-control" id="emailUser" name="emailUser" type="email" value="<?= $_SESSION['email'] ?>" required>
                                </div>
                                <div class="form-group">
                                    <label>Senha Atual</label>
                                    <input class="form-control" id="senhaAtualUser" name="senhaAtualUser" type="password" required>
                                </div>
                                <div class="form-group">
                                    <label>Nova Senha</label>
                                    <input class="form-control" id="senhaUser" name="senhaUser" type="password" required>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button class="btn btn-primary" type="submit" style="width: 100%;"><i class="fa fa-refresh"></i>&nbsp;Atualizar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- ./wrapper -->

        <!-- jQuery 2.2.3 -->
        <script src="plugins/jQuery/jquery-2.2.3.min.js"></script>
        <!-- DataTables -->
        <script src="plugins/datatables/jquery.dataTables.min.js"></script>
        <script src="plugins/datatables/dataTables.bootstrap.min.js"></script>
        <!-- Bootstrap 3.3.6 -->
        <script src="bootstrap/js/bootstrap.min.js"></script>
        <!-- bootstrap datepicker -->
        <script src="plugins/datepicker/bootstrap-datepicker.js"></script>
        <!-- SlimScroll -->
        <script src="plugins/slimScroll/jquery.slimscroll.min.js"></script>
        <!-- iCheck 1.0.1 -->
        <script src="plugins/iCheck/icheck.min.js"></script>
        <!-- FastClick -->
        <script src="plugins/fastclick/fastclick.js"></script>
        <!-- AdminLTE App -->
        <script src="dist/js/app.min.js"></script>
        <!-- AdminLTE for demo purposes -->
        <script src="dist/js/demo.js"></script>
        <!-- PACE -->
        <script src="plugins/pace/pace.min.js"></script>

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
                                                //Date picker
                                                $('#data').datepicker({
                                                    autoclose: true,
                                                    format: 'yyyy-mm-dd'
                                                });

                                                $('.minimal').iCheck({
                                                    checkboxClass: 'icheckbox_flat-blue',
                                                    radioClass: 'iradio_flat-blue'
                                                });

                                                $('.minimal').on('ifChecked', function () {
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
        </script>

        <!-- page script -->
        <script type="text/javascript" src="../iniLTE.min.js"></script>
    </body>
</html>

