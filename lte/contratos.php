<?php
/**
 *  Interface usada pelo Setor de Orçamento para editar um item.
 *
 * @author João Bolsson (joaovictorbolsson@gmail.com).
 * @since 2018, 12 Jun.
 */
ini_set('display_errors', true);
error_reporting(E_ALL);

session_start();
spl_autoload_register(function (string $class_name) {
    include_once '../class/' . $class_name . '.class.php';
});
$permissao = BuscaLTE::getPermissoes($_SESSION["id"]);
if (!isset($_SESSION["id_setor"]) || $_SESSION["id_setor"] != 2 || !$permissao->pedidos) {
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
<body class="hold-transition skin-blue layout-top-nav">
<div class="wrapper">

    <header class="main-header">
        <nav class="navbar navbar-static-top">
            <div class="container">
                <div class="navbar-header">
                    <a href="index.php" class="navbar-brand"><b>SOF</b>HUSM</a>
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse"
                            data-target="#navbar-collapse">
                        <i class="fa fa-bars"></i>
                    </button>
                </div>

                <div class="collapse navbar-collapse pull-left" id="navbar-collapse">
                    <ul class="nav navbar-nav">
                        <li>
                            <a href="#">
                                <i class="fa fa-book"></i> Cadastrar Contrato
                            </a>
                        </li>
                        <li>
                            <a href="#">
                                <i class="fa fa-bank"></i> Cadastrar Empresa
                            </a>
                        </li>
                        <li>
                            <a href="#">
                                <i class="fa fa-money"></i> Mensalidade
                            </a>
                        </li>
                    </ul>
                </div>

                <?php include_once 'navbar-user.php' ?>
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
                    <li><a href="index.php"><i class="fa fa-dashboard"></i> Início</a></li>
                    <li class="active">Contratos</li>
                </ol>
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Contratos</h3>
                    </div>
                    <input id="searchProcesso" type="hidden">
                    <div class="box-body">
                        <table class="table table-bordered table-striped" id="tableContratos">
                            <thead>
                            <tr>
                                <th>Opções</th>
                                <th>Contrato</th>
                                <th>Empresa</th>
                                <th>Vigência</th>
                                <th>Mensalidade</th>
                                <th>Saldo Disponível</th>
                            </tr>
                            </thead>
                            <tbody id="conteudoContrato"></tbody>
                        </table>
                    </div><!-- ./box-body -->
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
    <?php
    include_once "util/modals-util.php";
    ?>
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
<!-- iCheck 1.0.1 -->
<script src="plugins/iCheck/icheck.min.js"></script>
<!-- FastClick -->
<script src="plugins/fastclick/fastclick.js"></script>
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
<script type="text/javascript" src="util/util_lte.min.js"></script>
<script type="text/javascript" src="../iniLTE.min.js"></script>
<script type="text/javascript" src="util/editMode.js"></script>
</body>
</html>

