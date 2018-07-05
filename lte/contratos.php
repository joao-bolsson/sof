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
    <!-- Select2 -->
    <link rel="stylesheet" href="plugins/select2/select2.min.css">
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
                            <a href="javascript:abreModal('#cadContrato');">
                                <i class="fa fa-book"></i> Cadastrar Contrato
                            </a>
                        </li>
                        <li>
                            <a href="javascript:abreModal('#cadEmpresa');">
                                <i class="fa fa-bank"></i> Cadastrar Empresa
                            </a>
                        </li>
                        <li>
                            <a href="javascript:abreModal('#cadMensalidade');">
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
                    <div class="box-body">
                        <div class="margin">
                            <div class="form-group">
                                <label>Grupo</label>
                                <select id="selectGroupTable" class="form-control select2" required>
                                    <?php
                                    $query = Query::getInstance()->exe("SELECT * FROM grupo;");

                                    while ($obj = $query->fetch_object()) {
                                        echo "<option value=\"{$obj->id}\">{$obj->nome}</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
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

    <div aria-hidden="true" class="modal fade" id="cadEmpresa" role="dialog" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Cadastrar Empresa</h4>
                </div>
                <form id="formEmpresa" method="POST">
                    <input type="hidden" name="admin" value="1">
                    <input type="hidden" name="form" value="formEmpresa">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Nome</label>
                            <input type="text" class="form-control" name="nome" required>
                        </div>
                        <div class="form-group">
                            <label>CNPJ</label>
                            <input type="text" class="form-control" name="cnpj" data-inputmask="'alias': '99.999.999/9999-99'" data-mask required>
                        </div>
                        <div class="form-group">
                            <label>Contratos</label>
                            <select id="selectContr" class="form-control select2" name="contratos[]" multiple required>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Grupos</label>
                            <select class="form-control select2" name="grupos[]" multiple required>
                                <?php
                                $query = Query::getInstance()->exe("SELECT * FROM grupo;");

                                while ($obj = $query->fetch_object()) {
                                    echo "<option value=\"{$obj->id}\">{$obj->nome}</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary" type="submit" style="width: 100%;"><i
                                    class="fa fa-send"></i>&nbsp;Cadastrar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div aria-hidden="true" class="modal fade" id="cadContrato" role="dialog" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Cadastrar Contrato</h4>
                </div>
                <form id="formContr" method="POST">
                    <input type="hidden" name="admin" value="1">
                    <input type="hidden" name="form" value="formContr">
                    <input type="hidden" name="id" value="0">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Número</label>
                            <input type="text" class="form-control" name="numero" required>
                        </div>
                        <div class="form-group">
                            <label>Vigência</label>
                            <div class="input-group">
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </div>
                                <input type="text" class="form-control date" name="vigencia" required
                                       data-inputmask="'alias': 'dd/mm/yyyy'" data-mask>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Teto</label>
                            <div class="input-group">
                                <div class="input-group-addon">
                                    <i class="fa fa-dollar"></i>
                                </div>
                                <input type="number" class="form-control" name="teto" step="0.01" min="0.0" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Mensalidade</label>
                            <div class="input-group">
                                <div class="input-group-addon">
                                    <i class="fa fa-money"></i>
                                </div>
                                <input type="number" class="form-control" name="mensalidade" step="0.01" min="0.0"
                                       required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary" type="submit" style="width: 100%;"><i
                                    class="fa fa-send"></i>&nbsp;Cadastrar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div aria-hidden="true" class="modal fade" id="cadMensalidade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Mensalidade</h4>
                </div>
                <form id="formMensalidade" method="POST">
                    <input type="hidden" name="admin" value="1">
                    <input type="hidden" name="form" value="formMensalidade">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Contrato</label>
                            <select id="selectContrMens" class="form-control select2" name="contrato" required>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Ano</label>
                            <select class="form-control" name="ano" required>
                                <?php
                                $query = Query::getInstance()->exe("SELECT * FROM ano ORDER BY ano DESC;");
                                while ($obj = $query->fetch_object()) {
                                    echo "<option value=\"{$obj->id}\">{$obj->ano}</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Mês</label>
                            <select class="form-control" name="mes" required>
                                <?php
                                $query = Query::getInstance()->exe("SELECT id, sigla_mes FROM mes LIMIT 12;");
                                while ($obj = $query->fetch_object()) {
                                    echo "<option value=\"{$obj->id}\">{$obj->sigla_mes}</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Valor</label>
                            <div class="input-group">
                                <div class="input-group-addon">
                                    <i class="fa fa-dollar"></i>
                                </div>
                                <input type="number" class="form-control" name="valor" step="0.01" min="0.0" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>
                                <input type="checkbox" name="nota" class="minimal"/> Com Nota
                            </label>
                        </div>
                        <div class="form-group">
                            <label>
                                <input type="checkbox" name="checkReajuste" class="minimal"/> Reajuste
                            </label>
                        </div>
                        <div class="form-group">
                            <label>Reajuste</label>
                            <div class="input-group">
                                <div class="input-group-addon">
                                    <i class="fa fa-dollar"></i>
                                </div>
                                <input type="number" class="form-control" name="valorReajuste" step="0.01" min="0.0" disabled>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary" type="submit" style="width: 100%;"><i
                                    class="fa fa-send"></i>&nbsp;Cadastrar
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
<!-- iCheck 1.0.1 -->
<script src="plugins/iCheck/icheck.min.js"></script>
<!-- FastClick -->
<script src="plugins/fastclick/fastclick.js"></script>
<!-- Select2 -->
<script src="plugins/select2/select2.full.min.js"></script>
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
<script type="text/javascript" src="util/contratos.min.js"></script>
</body>
</html>

