<?php
/**
 *  Interface usada pelo Setor de Orçamento para editar um item.
 *
 * @author João Bolsson (joaovictorbolsson@gmail.com).
 * @since 2017, 03 Feb.
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

if (!isset($_SESSION['editmode'])) {
    $_SESSION['editmode'] = true;
}
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
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse"
                            data-target="#navbar-collapse">
                        <i class="fa fa-bars"></i>
                    </button>
                </div>

                <div class="collapse navbar-collapse pull-left" id="navbar-collapse">
                    <ul class="nav navbar-nav">
                        <li>
                            <a href="javascript:listProcessos('users');">
                                <i class="fa fa-tags"></i> Processos
                            </a>
                        </li>
                        <li>
                            <a href="javascript:abreModal('#infoItem');">
                                <i class="fa fa-database"></i> Cadastrar Item
                            </a>
                        </li>
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
                    <li><a href="index.php"><i class="fa fa-dashboard"></i> Início</a></li>
                    <li class="active">Editar Itens</li>
                </ol>
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Itens do Processo: <span id="numProc">--------------------</span></h3>
                    </div>
                    <input id="searchProcesso" type="hidden">
                    <div class="box-body">
                        <table class="table table-bordered table-striped" id="tableProcessos">
                            <thead>
                            <tr>
                                <th></th>
                                <th>Fornecedor</th>
                                <th>Cod Reduzido</th>
                                <th>Complemento</th>
                                <th style="display: none;"></th>
                                <th>Vl Unitário</th>
                                <th>Qt Saldo</th>
                                <th>Qt Utilizado</th>
                                <th>Vl Saldo</th>
                                <th>Vl Utilizado</th>
                                <th>Qt Contrato</th>
                            </tr>
                            </thead>
                            <tbody id="conteudoProcesso"></tbody>
                        </table>
                    </div><!-- ./box-body -->
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
    <div class="modal fade" id="infoItem" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Informações do Item</h4>
                </div>
                <span id="editmode" style="display: none;"></span>
                <form id="formEditRegItem" action="../php/geral.php" method="POST">
                    <input id="id" type="hidden" name="id" value="0"/>
                    <input type="hidden" name="form" value="formEditRegItem"/>
                    <input type="hidden" name="admin" value="1"/>
                    <div class="modal-body">
                        <?php include_once 'formItem.html' ?>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary" type="submit" style="width: 100%;"><i class="fa fa-refresh"></i>&nbsp;Atualizar / Cadastrar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div aria-hidden="true" class="modal fade" id="listProcessos" role="dialog" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Processos Atendidos pelo SOF</h4>
                </div>
                <div class="modal-body">
                    <table id="tableListProcessos" class="table table-bordered table-striped">
                        <thead>
                        <tr>
                            <th>Número do Processo</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody id="tbodyListProcessos"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div id="viewCompl" class="modal fade" tabindex="-1" role="dialog" data-backdrop="static">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Informação</h4>
                </div>
                <div id="complementoItem" class="modal-body"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-dismiss="modal">Fechar</button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
    <div class="modal fade" id="myInfos" role="dialog">
        <div class="modal-dialog" style="width: 40%;">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Informações do Usuário</h4>
                </div>
                <form id="altInfo" action="javascript:altInfoUser();" method="post">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Nome</label>
                            <input class="form-control" id="nameUser" name="nameUser" type="text"
                                   value="<?= $_SESSION['nome'] ?>" required>
                        </div>
                        <div class="form-group">
                            <label>E-mail</label>
                            <input class="form-control" id="emailUser" name="emailUser" type="email"
                                   value="<?= $_SESSION['email'] ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Senha Atual</label>
                            <input class="form-control" id="senhaAtualUser" name="senhaAtualUser" type="password"
                                   required>
                        </div>
                        <div class="form-group">
                            <label>Nova Senha</label>
                            <input class="form-control" id="senhaUser" name="senhaUser" type="password" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary" type="submit" style="width: 100%;"><i class="fa fa-refresh"></i>&nbsp;Atualizar
                        </button>
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

