<?php
/**
 *
 * @author João Bolsson (joaovictorbolsson@gmail.com).
 * @since 2018, 11 Mar.
 */
ini_set('display_errors', true);
error_reporting(E_ALL);

session_start();
spl_autoload_register(function (string $class_name) {
    include_once '../../class/' . $class_name . '.class.php';
});
if (!isset($_SESSION["id_setor"]) || $_SESSION["id_setor"] != 17) {
    header("Location: ../../");
}
$is_admin = ($_SESSION['id'] == 1 || $_SESSION['id'] == 11);
require_once '../../defines.php';
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
    <link rel="stylesheet" href="../bootstrap/css/bootstrap.min.css">
    <!-- DataTables -->
    <link rel="stylesheet" href="../plugins/datatables/dataTables.bootstrap.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="../plugins/font-awesome-4.7.0/css/font-awesome.min.css">
    <!-- Ionicons -->
    <link rel="stylesheet" href="../plugins/ionicons/css/ionicons.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="../dist/css/AdminLTE.min.css">
    <!-- AdminLTE Skins. Choose a skin from the css/skins
         folder instead of downloading all of them to reduce the load. -->
    <link rel="stylesheet" href="../dist/css/skins/_all-skins.min.css">
    <!-- Custom snackbar style -->
    <link rel="stylesheet" href="../dist/css/snackbar.min.css">
    <!-- Pace style -->
    <link rel="stylesheet" href="../plugins/pace/pace.min.css">

    <link rel="icon" href="../../favicon.ico">

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
                </div>

                <div class="navbar-custom-menu">
                    <ul class="nav navbar-nav">
                        <?php
                        if (isset($_SESSION['database']) && $_SESSION['database'] == 'main') { ?>
                            <li class="dropdown user user-menu">
                                <a href="javascript:abreModal('#myInfos');" class="dropdown-toggle">
                                    <img src="../dist/img/user.png" class="user-image" alt="User Image">
                                    <span id="userLogado" class="hidden-xs"><?= $_SESSION["nome"] ?></span>
                                </a>
                            </li>
                        <?php } ?>
                        <li>
                            <a href="../../admin/sair.php"><i class="fa fa-power-off"></i></a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>
    <div class="content-wrapper">
        <div class="container">
            <section class="content-header">
                <h1>
                    <?= $_SESSION['nome_setor']; ?>
                </h1>
                <ol class="breadcrumb">
                    <li><a href="index.php"><i class="fa fa-dashboard"></i> Início</a></li>
                </ol>
            </section>

            <section class="content">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Arquivos</h3>
                    </div>
                    <div class="box-body">
                        <?php
                        $pasta = '../../comprovantes/';
                        $diretorio = dir($pasta);

                        $table = new Table('files', 'table table-bordered table-striped', ['Tipo', 'Nome'], true);
                        while ($arquivo = $diretorio->read()) {
                            $tipo = pathinfo($pasta . $arquivo);

                            $label = 'blue';
                            $tipo_doc = 'Documento';
                            if ($arquivo != "." && $arquivo != "..") {
                                $cnpj = str_replace([".", "-", "/"], "", $_SESSION['login']);
                                $file_name = str_replace([".xps", '-', '.'], "", $arquivo);
                                if ($cnpj == $file_name) {
                                    $row = new Row();
                                    $row->addComponent(new Column(new Small('label bg-' . $label, $tipo_doc)));
                                    $row->addComponent(new Column("<a href=\"" . $pasta . $arquivo . "\" target=\"_blank\">" . $arquivo . "</a>"));

                                    $table->addComponent($row);
                                }
                            }
                        }
                        $diretorio->close();
                        echo $table;
                        ?>
                    </div>
                </div>
            </section>
        </div>
    </div>
    <div id="snackbar"></div>
    <footer class="main-footer">
        <div class="container">
            <div class="pull-right hidden-xs">
                <b>Version</b> <?= VERSION ?>
            </div>
            <?= COPYRIGHT ?>
        </div>
    </footer>
    <?php
    include_once "../util/modals-util.php";
    ?>
</div>
<!-- jQuery 2.2.3 -->
<script src="../plugins/jQuery/jquery-2.2.3.min.js"></script>
<!-- DataTables -->
<script src="../plugins/datatables/jquery.dataTables.min.js"></script>
<script src="../plugins/datatables/dataTables.bootstrap.min.js"></script>
<!-- Bootstrap 3.3.6 -->
<script src="../bootstrap/js/bootstrap.min.js"></script>
<!-- date-range-picker -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.11.2/moment.min.js"></script>
<script src="../plugins/daterangepicker/daterangepicker.js"></script>
<!-- SlimScroll -->
<script src="../plugins/slimScroll/jquery.slimscroll.min.js"></script>
<!-- FastClick -->
<script src="../plugins/fastclick/fastclick.js"></script>
<!-- AdminLTE App -->
<script src="../dist/js/app.min.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="../dist/js/demo.js"></script>
<!-- PACE -->
<script src="../plugins/pace/pace.min.js"></script>
<!-- DataTables -->
<script src="../plugins/datatables/jquery.dataTables.min.js"></script>
<script src="../plugins/datatables/dataTables.bootstrap.min.js"></script>
<!-- page script -->
<script type="text/javascript" src="../util/util_lte.min.js"></script>
<script>
    var language = {
        "decimal": "",
        "emptyTable": "Nenhum dado na tabela",
        "info": "_MAX_ resultados encontrados",
        "infoEmpty": "",
        "infoFiltered": "",
        "infoPostFix": "",
        "thousands": ",",
        "lengthMenu": "Monstrando _MENU_ entradas",
        "loadingRecords": "Carregando...",
        "processing": "Processando...",
        "search": "Pesquisar:",
        "zeroRecords": "Nenhum resultado encontrado",
        "paginate": {
            "first": "Primeiro",
            "last": "Último",
            "next": "Próximo",
            "previous": "Anterior"
        },
        "aria": {
            "sortAscending": ": activate to sort column ascending",
            "sortDescending": ": activate to sort column descending"
        }
    };

    $('#files').DataTable({
        "destroy": true,
        "scrollX": true,
        language: language
    });
</script>
</body>
</html>





