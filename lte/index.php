<?php
ini_set('display_erros', true);
error_reporting(E_ALL);

session_start();
if (!isset($_SESSION["id_setor"]) || $_SESSION["id_setor"] != 2) {
    header("Location: ../");
}
include_once '../class/BuscaLTE.class.php';
//instanciando classe de busca para popular o select de estados
$obj_Busca = new BuscaLTE();
$permissao = $obj_Busca->getPermissoes($_SESSION["id"]);
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
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">
        <!-- Ionicons -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">
        <!-- DataTables -->
        <link rel="stylesheet" href="plugins/datatables/dataTables.bootstrap.css">
        <!-- iCheck for checkboxes and radio inputs -->
        <link rel="stylesheet" href="plugins/iCheck/all.css">
        <!-- Theme style -->
        <link rel="stylesheet" href="dist/css/AdminLTE.min.css">
        <!-- AdminLTE Skins. Choose a skin from the css/skins
             folder instead of downloading all of them to reduce the load. -->
        <link rel="stylesheet" href="dist/css/skins/_all-skins.min.css">

        <link rel="icon" href="../favicon.ico">
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
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                    <img src="dist/img/user.png" class="user-image" alt="User Image">
                                    <span class="hidden-xs">João Bolsson</span>
                                </a>
                            </li>
                            <li>
                                <a href="#"><i class="fa fa-power-off"></i></a>
                            </li>
                        </ul>
                    </div>
                </nav>
            </header>
            <!-- Left side column. contains the logo and sidebar -->
            <aside class="main-sidebar">
                <!-- sidebar: style can be found in sidebar.less -->
                <section class="sidebar">
                    <!-- Sidebar user panel -->
                    <div class="user-panel">
                        <div class="pull-left image">
                            <img src="dist/img/user.png" class="img-circle" alt="User Image">
                        </div>
                        <div class="pull-left info">
                            <p>João Bolsson</p>
                        </div>
                    </div>
                    <!-- sidebar menu: : style can be found in sidebar.less -->
                    <ul class="sidebar-menu">
                        <li class="header">NAVEGAÇÃO</li>
                        <li>
                            <a href="javascript:mostraSolicAdiant();">
                                <i class="fa fa-credit-card"></i> <span>Solic Adiantamento</span>
                                <span class="pull-right-container">
                                    <small class="label pull-right bg-blue">17</small>
                                </span>
                            </a>
                        </li>
                        <li>
                            <a href="#">
                                <i class="fa fa-cog"></i> <span>Solic Alt Pedidos</span>
                                <span class="pull-right-container">
                                    <small class="label pull-right bg-red">3</small>
                                </span>
                            </a>
                        </li>
                        <li>
                            <a href="#">
                                <i class="fa fa-tags"></i> <span>Pedidos</span>
                                <span class="pull-right-container">
                                    <small class="label pull-right bg-blue">29</small>
                                </span>
                            </a>
                        </li>
                        <?php if ($_SESSION['login'] == 'joao' || $_SESSION['login'] == 'iara'): ?>
                            <li>
                                <a href="#">
                                    <i class="fa fa-user"></i> <span>Alterar Usuário</span>
                                </a>
                            </li>
                            <li>
                                <a href="#">
                                    <i class="fa fa-user-plus"></i> <span>Adicionar Usuário</span>
                                </a>
                            </li>
                        <?php endif; ?>
                        <?php if ($_SESSION['login'] == 'joao'): ?>
                            <li>
                                <a href="#">
                                    <i class="fa fa-exclamation-circle"></i> <span>Resetar</span>
                                </a>
                            </li>
                            <li>
                                <a href="#">
                                    <i class="fa fa-warning"></i> <span>Problemas</span>
                                </a>
                            </li>
                        <?php endif; ?>
                        <li>
                            <a href="#">
                                <i class="fa fa-comments"></i> <span>Relatar Problema</span>
                            </a>
                        </li>
                        <?php if ($permissao->saldos): ?>
                            <li>
                                <a href="#">
                                    <i class="fa fa-send"></i> <span>Liberar Saldo</span>
                                </a>
                            </li>
                            <li>
                                <a href="#">
                                    <i class="fa fa-dollar"></i> <span>Liberações Orçamentárias</span>
                                </a>
                            </li>
                            <li>
                                <a href="#">
                                    <i class="fa fa-arrows-h"></i> <span>Transferências</span>
                                </a>
                            </li>
                        <?php endif; ?>
                        <?php if ($permissao->pedidos): ?>
                            <li>
                                <a href="#">
                                    <i class="fa fa-cloud-upload"></i> <span>Importar Itens</span>
                                </a>
                            </li>
                            <li class="treeview">
                                <a href="#">
                                    <i class="fa fa-file-text"></i> <span>Relatórios</span>
                                    <span class="pull-right-container">
                                        <i class="fa fa-angle-left pull-right"></i>
                                    </span>
                                </a>
                                <ul class="treeview-menu">
                                    <li><a href="#"><i class="fa fa-circle-o"></i> Pedidos</a></li>
                                    <li><a href="#"><i class="fa fa-circle-o"></i> Lista de Pedidos</a></li>
                                </ul>
                            </li>
                        <?php endif; ?>
                        <?php if ($permissao->recepcao): ?>
                            <li>
                                <a href="#">
                                    <i class="fa fa-archive"></i> <span>Processos</span>
                                </a>
                            </li>
                            <li>
                                <a href="#">
                                    <i class="fa fa-plus"></i> <span>Novo Tipo</span>
                                </a>
                            </li>
                            <li>
                                <a href="#">
                                    <i class="fa fa-pie-chart"></i> <span>Relatório</span>
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </section>
                <!-- /.sidebar -->
            </aside>

            <!-- Content Wrapper. Contains page content -->
            <div class="content-wrapper">
                <!-- Content Header (Page header) -->
                <section class="content-header">
                    <h1>
                        Setor de Orçamento e Finanças
                        <small>Saldo: R$ 299.800.000,000</small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="#"><i class="fa fa-dashboard"></i> Início</a></li>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="box">
                                <div class="box-header">
                                    <h3 class="box-title">Pedidos</h3>
                                    <div class="box-tools pull-right">
                                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                                        </button>
                                    </div>
                                </div><!-- /.box-header -->
                                <div class="box-body">
                                    <table id="tableSolicitacoes" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>Opções</th>
                                                <th>Pedido</th>
                                                <th>Setor</th>
                                                <th>Data</th>
                                                <th>Mês</th>
                                                <th>Prioridade</th>
                                                <th>Status</th>
                                                <th>Valor</th>
                                                <th>Empenho</th>
                                            </tr>
                                        </thead>
                                        <tbody id="conteudoSolicitacoes"></tbody>
                                    </table>
                                </div><!-- /.box-body -->
                            </div><!-- /.box -->
                        </div><!-- /.col -->
                    </div><!-- /.row -->
                    <?php if ($permissao->saldos): ?>
                    <div id="rowSolicAdi" class="row" style="display: none;">
                            <div class="col-xs-12">
                                <div class="box">
                                    <div class="box-header">
                                        <h3 class="box-title">Solicitações de Adiantamento</h3>
                                        <div class="box-tools pull-right">
                                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                                            </button>
                                        </div>
                                    </div><!-- /.box-header -->
                                    <div class="box-body">
                                        <table class="table">
                                            <tr>
                                                <td>
                                                    <div class="form-group">
                                                        <label>
                                                            <input id="stabertos" type="radio" name="stadi" class="minimal" value="2" onclick="iniTableSolicAdiant();" checked>
                                                            Abertos
                                                        </label>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-group">
                                                        <label>
                                                            <input id="staprovados" type="radio" name="stadi" class="minimal" value="1" onclick="iniTableSolicAdiant();">
                                                            Aprovados
                                                        </label>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-group">
                                                        <label>
                                                            <input id="streprovado" type="radio" name="stadi" class="minimal" value="0" onclick="iniTableSolicAdiant();">
                                                            Reprovados
                                                        </label>
                                                    </div>
                                                </td>
                                            </tr>
                                        </table>
                                        <table id="tableSolicitacoesAdiantamento" class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Opções</th>
                                                    <th>Setor</th>
                                                    <th>Data Solic</th>
                                                    <th>Data Análise</th>
                                                    <th>Valor Adiantado</th>
                                                    <th>Justificativa</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody id="conteudoSolicitacoesAdiantamento"></tbody>
                                        </table>
                                    </div><!-- /.box-body -->
                                </div><!-- /.box -->
                            </div><!-- /.col -->
                        </div><!-- /.row -->
                    <?php endif; ?>
                </section><!-- /.content -->
            </div><!-- /.content-wrapper -->
            <footer class="main-footer">
                <div class="pull-right hidden-xs">
                    <b>Version</b> 2.0.0
                </div>
                <strong>Copyright © 2016-2017 <a href="https://github.com/joao-bolsson">João Bolsson</a>.</strong> All rights
                reserved.
            </footer>
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
        <!-- FastClick -->
        <script src="plugins/fastclick/fastclick.js"></script>
        <!-- AdminLTE App -->
        <script src="dist/js/app.min.js"></script>
        <!-- AdminLTE for demo purposes -->
        <script src="dist/js/demo.js"></script>
        <!-- iCheck 1.0.1 -->
        <script src="plugins/iCheck/icheck.min.js"></script>

        <!-- Page script -->
        <script>
                                                                $(function () {

                                                                    $('input[type="checkbox"].minimal, input[type="radio"].minimal').iCheck({
                                                                        checkboxClass: 'icheckbox_minimal-blue',
                                                                        radioClass: 'iradio_minimal-blue'
                                                                    });
                                                                });
        </script>

        <!-- page script -->
        <script type="text/javascript" src="../iniLTE.js"></script>
    </body>
</html>
