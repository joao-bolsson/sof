<?php
/**
 *  Interface usada exclusivamente pela Unidade de Apoio
 *
 *  @author João Bolsson (joaovictorbolsson@gmail.com).
 *  @since 2017, 28 Jan.
 */
ini_set('display_erros', true);
error_reporting(E_ALL);

session_start();
if (!isset($_SESSION['id_setor']) || $_SESSION['id_setor'] != 12) {
    header('Location: ../');
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

        <link rel="icon" href="../favicon.ico">

        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
    </head>
    <!-- ADD THE CLASS layout-top-nav TO REMOVE THE SIDEBAR. -->
    <body class="hold-transition skin-blue layout-top-nav" onload="iniSolicitacoes(false, 0);">
        <div class="wrapper">

            <header class="main-header">
                <nav class="navbar navbar-static-top">
                    <div class="container">
                        <div class="navbar-header">
                            <a href="apoio.php" class="navbar-brand"><b>SOF</b>HUSM</a>
                            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse">
                                <i class="fa fa-bars"></i>
                            </button>
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
                            <li><a href="#"><i class="fa fa-dashboard"></i> Início</a></li>
                        </ol>
                    </section>

                    <!-- Main content -->
                    <section class="content">
                        <div class="box box-default">
                            <div class="box-header with-border">
                                <h3 class="box-title">Pedidos</h3>
                            </div>
                            <div class="box-body">
                                <div class="form-group">
                                    <button id="btnPrintCheck" class="btn btn-primary" type="button" onclick="printChecks()" disabled><i class="fa fa-print"></i>&nbsp;Imprimir</button>
                                    <button class="btn btn-primary" type="button" onclick="abreModal('#loadMoreCustom');" data-toggle="tooltip" title="Carregar mais pedidos"><i class="fa fa-cloud-download"></i>&nbsp;Carregar</button>
                                </div>
                                <table class="table table-bordered table-striped" id="tableSolicitacoes">
                                    <thead>
                                        <tr>
                                            <th>
                                                <div class=form-group>
                                                    <input type="checkbox" name="checkPedRel" id="checkPedRel" value="1">
                                                </div>
                                            </th>
                                            <th>Opções</th>
                                            <th>Num Pedido</th>
                                            <th>Setor</th>
                                            <th>Data Pedido</th>
                                            <th>Prioridade</th>
                                            <th>Status</th>
                                            <th>Valor</th>
                                            <th>Empenho</th>
                                            <th>Fornecedor</th>
                                        </tr>
                                    </thead>
                                    <tbody id="conteudoSolicitacoes"></tbody>
                                </table>
                            </div><!-- /.box-body -->
                            <div id="overlayLoad" class="overlay" style="display: none;">
                                <i class="fa fa-refresh fa-spin"></i>
                            </div>
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
            <div aria-hidden="true" class="modal fade" id="loadMoreCustom" role="dialog" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title">Carregar Pedidos</h4>
                        </div>
                        <form action="javascript:loadMore();" method="POST">
                            <div class="modal-body">
                                <small class="label bg-gray">Carrega todos os pedidos entre Limite 1 e Limite 2. A consulta trás todos os pedidos entre tais limites, <br>independente do conteúdo que já estiver na tabela.</small>
                                <div class="form-group">
                                    <label>Limite 1</label>
                                    <input type="number" class="form-control" id="limit1" name="limit1" step="1" min="0" required>
                                </div>
                                <div class="form-group">
                                    <label>Limite 2</label>
                                    <input type="number" class="form-control" id="limit2" name="limit2" step="1" min="0" required>
                                </div>
                                <small class="label bg-gray">Por motivos de segurança, serão retornados no máximo <?= LIMIT_MAX ?> resultados nesta consulta. ;)</small>
                            </div>
                            <div class="modal-footer">
                                <button class="btn btn-primary" type="submit" style="width: 100%;"><i class="fa fa-cloud-download"></i>&nbsp;Carregar</button>
                            </div>
                        </form>
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
        <!-- SlimScroll -->
        <script src="plugins/slimScroll/jquery.slimscroll.min.js"></script>
        <!-- FastClick -->
        <script src="plugins/fastclick/fastclick.js"></script>
        <!-- iCheck 1.0.1 -->
        <script src="plugins/iCheck/icheck.min.js"></script>
        <!-- AdminLTE App -->
        <script src="dist/js/app.min.js"></script>
        <!-- AdminLTE for demo purposes -->
        <script src="dist/js/demo.js"></script>
        <!-- InputMask -->
        <script src="plugins/input-mask/jquery.inputmask.js"></script>
        <script src="plugins/input-mask/jquery.inputmask.date.extensions.js"></script>
        <script src="plugins/input-mask/jquery.inputmask.extensions.js"></script>
        <!-- PACE -->
        <script src="plugins/pace/pace.min.js"></script>
        <!-- page script -->
        <script type="text/javascript" src="../iniLTE.min.js"></script>
    </body>
</html>
