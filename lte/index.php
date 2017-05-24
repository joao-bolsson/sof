<?php
/**
 *  Interface usada pelo Setor de Orçamento.
 *
 * @author João Bolsson (joaovictorbolsson@gmail.com).
 * @since 2017, 15 Jan.
 */
ini_set('display_errors', true);
error_reporting(E_ALL);

session_start();
if (!isset($_SESSION["id_setor"]) || $_SESSION["id_setor"] != 2) {
    header("Location: ../");
}
spl_autoload_register(function (string $class_name) {
    include_once '../class/' . $class_name . '.class.php';
});
require_once '../defines.php';
$permissao = BuscaLTE::getPermissoes($_SESSION["id"]);

$count = BuscaLTE::getCountSolic();

if (isset($_SESSION['editmode'])) {
    unset($_SESSION['editmode']);
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Setor de Orçamento e Finanças - HUSM</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <!-- Bootstrap 3.3.6 -->
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="plugins/font-awesome-4.7.0/css/font-awesome.min.css">
    <!-- Ionicons -->
    <link rel="stylesheet" href="plugins/ionicons/css/ionicons.min.css">
    <!-- DataTables -->
    <link rel="stylesheet" href="plugins/datatables/dataTables.bootstrap.css">
    <!-- Select2 -->
    <link rel="stylesheet" href="plugins/select2/select2.min.css">
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
<body class="hold-transition skin-blue sidebar-mini" onload="iniAdminSolicitacoes();">
<div class="wrapper">

    <header class="main-header">
        <!-- Logo -->
        <a href="#" class="logo">
            <!-- mini logo for sidebar mini 50x50 pixels -->
            <span class="logo-mini"><b>S</b>OF</span>
            <!-- logo for regular state and mobile devices -->
            <span class="logo-lg"><b>SOF</b>HUSM</span>
        </a>
        <!-- Header Navbar: style can be found in header.less -->
        <nav class="navbar navbar-static-top">
            <!-- Sidebar toggle button-->
            <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </a>

            <div class="navbar-custom-menu">
                <ul class="nav navbar-nav">
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
        </nav>
    </header>
    <?php include_once "aside-menu.php"; ?>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                <?= $_SESSION['nome_setor']; ?>
                <small>Saldo:
                    R$ <?= number_format(Busca::getSaldo($_SESSION['id_setor']), 3, ',', '.'); ?></small>
            </h1>
            <ol class="breadcrumb">
                <li><a href="#"><i class="fa fa-dashboard"></i> Início</a></li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <?php
            if ($permissao->recepcao) {
                include_once "body_recepcao.php";
            }

            if ($permissao->saldos) {
                include_once "body_saldos.php";
            }

            if ($permissao->pedidos) {
                include_once "body_pedidos.php";
            }
            ?>
        </section><!-- /.content -->
    </div><!-- /.content-wrapper -->

    <div id="snackbar">Some text some message..</div>

    <footer class="main-footer">
        <div class="pull-right hidden-xs">
            <b>Version</b> <?= VERSION ?>
        </div>
        <?= COPYRIGHT ?>
    </footer>
    <?php
    if ($permissao->pedidos) {
        include_once "modals-pedidos.php";
    }

    if ($permissao->saldos) {
        include_once "modals-saldos.php";
    }

    if ($permissao->recepcao) {
        include_once 'modals-reception.php';
    }

    if ($_SESSION['login'] == 'joao' || $_SESSION['login'] == 'iara') {
        include_once "admin_modais.php";
    }

    // no need special permissions
    include_once "modals-geral.php";
    ?>
</div><!-- ./wrapper -->

<!-- jQuery 2.2.3 -->
<script src="plugins/jQuery/jquery-2.2.3.min.js"></script>
<!-- Bootstrap 3.3.6 -->
<script src="bootstrap/js/bootstrap.min.js"></script>
<!-- DataTables -->
<script src="plugins/datatables/jquery.dataTables.min.js"></script>
<script src="plugins/datatables/dataTables.bootstrap.min.js"></script>
<!-- SlimScroll -->
<script src="plugins/slimScroll/jquery.slimscroll.min.js"></script>
<!-- Select2 -->
<script src="plugins/select2/select2.full.min.js"></script>
<!-- FastClick -->
<script src="plugins/fastclick/fastclick.js"></script>
<!-- AdminLTE App -->
<script src="dist/js/app.min.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="dist/js/demo.js"></script>
<!-- iCheck 1.0.1 -->
<script src="plugins/iCheck/icheck.min.js"></script>
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
