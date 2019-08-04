<?php
/**
 * @author João Bolsson (joaovictorbolsson@gmail.com).
 * @since 2019, 01 Jun.
 */
ini_set('display_errors', true);
error_reporting(E_ALL);

session_start();

if (isset($_SESSION['database']) && $_SESSION['database'] != 'main') {
    header("Location: ../");
}

spl_autoload_register(function (string $class_name) {
    include_once '../class/' . $class_name . '.class.php';
});
$permission = BuscaLTE::getPermissoes($_SESSION["id"]);
if (!isset($_SESSION["id_setor"]) || $_SESSION["id_setor"] != 2) {
    header("Location: ../");
}
$is_admin = ($_SESSION['id'] == 1 || $_SESSION['id'] == 11);
require_once '../defines.php';

$index = "aihs.php";

if ($permission->pedidos || $permission->saldos || $permission->noticias || $permission->recepcao) {
    $index = "index.php";
}

?>
<!DOCTYPE html>
<html lang="pt">
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
                    <a href="<?= $index ?>" class="navbar-brand"><b>SOF</b>HUSM</a>
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse"
                            data-target="#navbar-collapse">
                        <i class="fa fa-bars"></i>
                    </button>
                </div>

                <div class="collapse navbar-collapse pull-left">
                    <ul class="nav navbar-nav">
                        <li><a href="javascript:mostra('rowFaturamento');"><i class="fa fa-file-text"></i>
                                Faturamento</a></li>
                        <li><a href="javascript:mostra('rowReceitas');"><i class="fa fa-file-text"></i>
                                Receita Recebida</a></li>
                        <li><a href="javascript:mostra('rowFatAprov');"><i class="fa fa-file-text"></i>
                                Faturamento Aprovado</a></li>
                    </ul>
                </div>

                <?php include_once 'navbar-user.php' ?>
            </div>
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
                    <li><a href="aihs.php"><i class="fa fa-dashboard"></i>Faturamento</a></li>
                </ol>
            </section>

            <!-- Main content -->
            <section class="content">
                <div id="rowFaturamento" class="row">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Faturamento</h3>
                            <div class="box-tools pull-right">
                                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i
                                            class="fa fa-minus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="box-body">
                            <div class="margin">
                                <button class="btn btn-primary" type="button" onclick="abreModal('#cadAIHS');"><i
                                            class="fa fa-sign-in"></i>&nbsp;Adicionar
                                </button>
                            </div>
                            <table id="tableAIHS" class="table table-bordered table-striped">
                                <thead>
                                <tr>
                                    <th>Tipo</th>
                                    <th>Grupo</th>
                                    <th>Descrição</th>
                                    <th>Qtd</th>
                                    <th>Valor</th>
                                    <th>Mês</th>
                                    <th>Lançamento</th>
                                    <th>Opções</th>
                                </tr>
                                </thead>
                                <tbody id="tboodyAIHS"></tbody>
                            </table>
                        </div>
                        <div id="overlayLoadAIHS" class="overlay" style="display: none;">
                            <i class="fa fa-refresh fa-spin"></i>
                        </div>
                    </div>
                </div>
                <div id="rowReceitas" class="row" style="display: none;">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Receitas Recebidas</h3>
                            <div class="box-tools pull-right">
                                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i
                                            class="fa fa-minus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="box-body">
                            <div class="margin">
                                <button class="btn btn-primary" type="button" onclick="abreModal('#cadReceita');"><i
                                            class="fa fa-sign-in"></i>&nbsp;Adicionar
                                </button>
                                <button class="btn btn-primary" type="button" onclick="abreModal('#relReceitas');"><i
                                            class="fa fa-file-text"></i>&nbsp;Relatório
                                </button>
                            </div>
                            <table id="tableReceitas" class="table table-bordered table-striped">
                                <thead>
                                <tr>
                                    <th>Tipo</th>
                                    <th>Mês</th>
                                    <th>Recebimento</th>
                                    <th>Valor</th>
                                    <th>PF</th>
                                    <th>Observações</th>
                                    <th>Opções</th>
                                </tr>
                                </thead>
                                <tbody id="tboodyReceitas"></tbody>
                            </table>
                        </div>
                        <div id="overlayLoadReceitas" class="overlay" style="display: none;">
                            <i class="fa fa-refresh fa-spin"></i>
                        </div>
                    </div>
                </div>
                <div id="rowFatAprov" class="row" style="display: none;">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Faturamento Aprovado</h3>
                            <div class="box-tools pull-right">
                                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i
                                            class="fa fa-minus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="box-body">
                            <div class="margin">
                                <button class="btn btn-primary" type="button" onclick="abreModal('#cadFatAprov');"><i
                                            class="fa fa-sign-in"></i>&nbsp;Adicionar
                                </button>
                            </div>
                            <table id="tableFatAprov" class="table table-bordered table-striped">
                                <thead>
                                <tr>
                                    <th>Lançamento</th>
                                    <th>Competência</th>
                                    <th>Produção</th>
                                    <th>Financiamento</th>
                                    <th>Complexidade</th>
                                    <th>Valor</th>
                                    <th>Opções</th>
                                </tr>
                                </thead>
                                <tbody id="tboodyFat"></tbody>
                            </table>
                        </div>
                        <div id="overlayLoadFat" class="overlay" style="display: none;">
                            <i class="fa fa-refresh fa-spin"></i>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
    <div aria-hidden="true" class="modal fade" id="cadReceita" role="dialog" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Receita Recebida</h4>
                </div>
                <form id="formCadReceita">
                    <input type="hidden" name="users" value="1"/>
                    <input type="hidden" name="form" value="cadReceita"/>
                    <input type="hidden" name="id" value="0"/>
                    <div class="modal-body">
                        <div class="margin">
                            <button class="btn btn-primary" type="button" onclick="abreModal('#cadTipoReceitaRec')"><i
                                        class="fa fa-plus"></i> Tipo
                            </button>
                        </div>
                        <div class="form-group">
                            <label>Tipo</label>
                            <select class="form-control" name="tipo" required>
                                <?php
                                $query = Query::getInstance()->exe("SELECT id, nome FROM aihs_receita_tipo;");
                                while ($obj = $query->fetch_object()) {
                                    echo "<option value=\"{$obj->id}\">{$obj->nome}</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Competência</label>
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
                            <label>Recebimento</label>
                            <input class="form-control date" name="data" type="text" required>
                        </div>
                        <div class="form-group">
                            <label>Valor</label>
                            <input class="form-control" type="number" min="0" step="0.01" name="valor" required/>
                        </div>
                        <div class="form-group">
                            <label>PF</label>
                            <input class="form-control" name="pf" maxlength="15" required/>
                        </div>
                        <div class="form-group">
                            <label>Observações</label>
                            <textarea class="form-control" rows="3" name="obs"></textarea>
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
    <div class="modal fade" id="cadTipoReceitaRec" role="dialog">
        <div class="modal-dialog" style="width: 40%;">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Cadastrar Tipo Receita Recebida</h4>
                </div>
                <form action="../php/geral.php" method="post">
                    <input type="hidden" name="users" value="1">
                    <input type="hidden" name="form" value="cadTipoReceitaRec">
                    <div class="modal-body">
                        <small class="label bg-gray"><i class="fa fa-exclamation-circle "></i> A página será
                            recarregada em seguida
                        </small>
                        <div class="form-group">
                            <label>Tipo</label>
                            <input class="form-control" name="tipo" maxlength="30"
                                   placeholder="Digite o tipo. Máx. 30 caracteres" required/>
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
    <div aria-hidden="true" class="modal fade" id="relReceitas" role="dialog" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Relatório de Receitas</h4>
                </div>
                <form action="../admin/printRelatorio.php" method="post" target="_blank">
                    <input type="hidden" name="relatorio" value="1"/>
                    <input type="hidden" name="tipo" value="receitas"/>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Competência</label>
                            <select class="form-control" name="competencia" required>
                                <?php
                                $query = Query::getInstance()->exe("SELECT id, sigla_mes FROM mes LIMIT 12;");
                                while ($obj = $query->fetch_object()) {
                                    echo "<option value=\"{$obj->id}\">{$obj->sigla_mes}</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Mês de Recebimento</label>
                            <select class="form-control" name="mes" required>
                                <?php
                                $query = Query::getInstance()->exe("SELECT id, sigla_mes FROM mes LIMIT 12;");
                                while ($obj = $query->fetch_object()) {
                                    echo "<option value=\"{$obj->id}\">{$obj->sigla_mes}</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary" type="submit" style="width: 100%;"><i
                                    class="fa fa-send"></i>&nbsp;Gerar Relatório
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div aria-hidden="true" class="modal fade" id="cadAIHS" role="dialog" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Formulário Apresentadas</h4>
                </div>
                <form id="formCadAIHS">
                    <input type="hidden" name="users" value="1"/>
                    <input type="hidden" name="form" value="cadAIHS"/>
                    <input type="hidden" name="id" value="0"/>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Tipo</label>
                            <select id="tipo" class="form-control" name="tipo" onchange="changeAIHSType();" required>
                                <?php
                                $query = Query::getInstance()->exe("SELECT id, nome FROM aihs_tipos;");
                                while ($obj = $query->fetch_object()) {
                                    echo "<option value=\"{$obj->id}\">{$obj->nome}</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Data de Lançamento</label>
                            <input class="form-control date" name="data" type="text" required>
                        </div>
                        <div class="form-group">
                            <label>Competência</label>
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
                            <label>Quantidade</label>
                            <input class="form-control" type="number" min="0" step="1" name="qtd" required/>
                        </div>
                        <div class="form-group">
                            <label>Valor</label>
                            <input class="form-control" type="number" min="0" step="0.01" name="valor" required/>
                        </div>
                        <div class="form-group">
                            <label>Grupo</label>
                            <input id="grupo" class="form-control" name="grupo" disabled/>
                        </div>
                        <div class="form-group">
                            <label>Descrição (sub-grupo)</label>
                            <input id="descr" class="form-control" name="descricao" disabled/>
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
    <div aria-hidden="true" class="modal fade" id="cadFatAprov" role="dialog" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Faturamento Aprovado</h4>
                </div>
                <form id="formCadFatAprov">
                    <input type="hidden" name="users" value="1"/>
                    <input type="hidden" name="form" value="cadFatAprov"/>
                    <input type="hidden" name="id" value="0"/>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Data de Lançamento</label>
                            <input class="form-control date" name="data" type="text" required>
                        </div>
                        <div class="form-group">
                            <label>Competência</label>
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
                            <label>Produção</label>
                            <select class="form-control" name="producao" required>
                                <?php
                                $query = Query::getInstance()->exe("SELECT id, nome FROM faturamento_producao;");
                                while ($obj = $query->fetch_object()) {
                                    echo "<option value=\"{$obj->id}\">{$obj->nome}</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Financiamento</label>
                            <select class="form-control" name="financiamento" required>
                                <?php
                                $query = Query::getInstance()->exe("SELECT id, nome FROM faturamento_financiamento;");
                                while ($obj = $query->fetch_object()) {
                                    echo "<option value=\"{$obj->id}\">{$obj->nome}</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Compexidade</label>
                            <select class="form-control" name="complexidade" required>
                                <?php
                                $query = Query::getInstance()->exe("SELECT id, nome FROM faturamento_complexidade;");
                                while ($obj = $query->fetch_object()) {
                                    echo "<option value=\"{$obj->id}\">{$obj->nome}</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Valor</label>
                            <input class="form-control" type="number" min="0" step="0.01" name="valor" required/>
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
    <div aria-hidden="true" class="modal fade" id="cadContratualizacao" role="dialog" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Contratualização</h4>
                </div>
                <form id="formCadContratualizacao">
                    <input type="hidden" name="users" value="1"/>
                    <input type="hidden" name="form" value="cadContratualizacao"/>
                    <input type="hidden" name="id" value="0"/>
                    <input type="hidden" name="idFaturamento" value="0"/>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Número do Contrato</label>
                            <input class="form-control" name="contr" type="text" required>
                        </div>
                        <h4>Vigência</h4>
                        <div class="form-group">
                            <label>Início</label>
                            <input class="form-control date" name="vigencia_ini" type="text" required>
                        </div>
                        <div class="form-group">
                            <label>Fim</label>
                            <input class="form-control date" name="vigencia_fim" type="text" required>
                        </div>
                        <h4>Aditivo</h4>
                        <div class="form-group">
                            <label>Início</label>
                            <input class="form-control date" name="aditivo_ini" type="text" required>
                        </div>
                        <div class="form-group">
                            <label>Fim</label>
                            <input class="form-control date" name="aditivo_fim" type="text" required>
                        </div>
                        <div class="form-group">
                            <label>Pré-Fixado</label>
                            <select class="form-control" name="prefix" required>
                                <?php
                                $query = Query::getInstance()->exe("SELECT id, nome FROM contratualizacao_prefix;");
                                while ($obj = $query->fetch_object()) {
                                    echo "<option value=\"{$obj->id}\">{$obj->nome}</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Valor</label>
                            <input class="form-control" type="number" min="0" step="0.01" name="valorContr" required/>
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
<!-- FastClick -->
<script src="plugins/fastclick/fastclick.js"></script>
<!-- AdminLTE App -->
<script src="dist/js/app.min.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="dist/js/demo.js"></script>
<!-- PACE -->
<script src="plugins/pace/pace.min.js"></script>

<!-- InputMask -->
<script src="plugins/input-mask/jquery.inputmask.js"></script>
<script src="plugins/input-mask/jquery.inputmask.date.extensions.js"></script>
<script src="plugins/input-mask/jquery.inputmask.extensions.js"></script>

<!-- page script -->
<script type="text/javascript" src="js/util_lte.min.js"></script>
<script type="text/javascript" src="../iniLTE.min.js"></script>
<script type="text/javascript" src="js/aihs.min.js"></script>
</body>
</html>
