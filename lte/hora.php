<?php
/**
 *  Interface usada pelo Setor de Orçamento para gerenciar postagens no site.
 *
 *  @author João Bolsson (joaovictorbolsson@gmail.com).
 *  @since 2017, 26 Feb.
 */
ini_set('display_erros', true);
error_reporting(E_ALL);

session_start();
spl_autoload_register(function (string $class_name) {
    include_once '../class/' . $class_name . '.class.php';
});
$obj_Busca = BuscaLTE::getInstance();
$permissao = $obj_Busca->getPermissoes($_SESSION["id"]);
if (!isset($_SESSION["id_setor"]) || $_SESSION["id_setor"] != 2) {
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
                                <li class="active">Ponto Eletrônico</li>
                            <?php else : ?>
                                <li><a href="hora.php"><i class="fa fa-dashboard"></i> Ponto Eletrônico</a></li>
                            <?php endif; ?>
                        </ol>
                    </section>

                    <!-- Main content -->
                    <section class="content">
                        <div class="box box-primary">
                            <div class="box-header with-border">
                                <h3 class="box-title">Ponto Eletrônico</h3>
                            </div>
                            <div class="box-body">
                                <div class="margin">
                                    <button class="btn btn-primary" type="button" onclick="pointRegister()"><i class="fa fa-sign-in"></i>&nbsp;Entrada</button>
                                    <button class="btn btn-danger" type="button" onclick="pointRegister()"><i class="fa fa-sign-out"></i>&nbsp;Saída</button>
                                </div>
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Nome</th>
                                            <th>Entrada/Saída</th>
                                            <th>Hora</th>
                                            <th>Data</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div><!-- ./box-body -->
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
                    <strong>Copyright © 2016-2017 <a href="https://github.com/joao-bolsson">João Bolsson</a>.</strong> All rights
                    reserved.
                </div>
                <!-- /.container -->
            </footer>
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

        <!-- page script -->
        <script type="text/javascript" src="../iniLTE.min.js"></script>
    </body>
</html>

