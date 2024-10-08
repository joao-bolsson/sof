<?php
/**
 *  Interface usada pelos demais setores cadastrados no sistema.
 *
 * @author João Bolsson (joaovictorbolsson@gmail.com).
 * @since 2017, 21 Jan.
 */
session_start();
ini_set('display_errors', true);
error_reporting(E_ALL);

if (!isset($_SESSION["id"]) || $_SESSION['id_setor'] == 12) {
    header("Location: ../");
}
spl_autoload_register(function (string $class_name) {
    include_once '../class/' . $class_name . '.class.php';
});
require_once '../defines.php';
$id_setor = $_SESSION["id_setor"];
$saldo_total = Busca::getSaldo($id_setor);
$pedidos_em_analise = BuscaLTE::getPedidosAnalise($id_setor);

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
    <!-- Font Awesome -->
    <link rel="stylesheet" href="plugins/font-awesome-4.7.0/css/font-awesome.min.css">
    <!-- Ionicons -->
    <link rel="stylesheet" href="plugins/ionicons/css/ionicons.min.css">
    <!-- Select2 -->
    <link rel="stylesheet" href="plugins/select2/select2.min.css">
    <!-- DataTables -->
    <link rel="stylesheet" href="plugins/datatables/dataTables.bootstrap.css">
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
<body class="hold-transition skin-blue sidebar-mini" onload="iniPagSolicitacoes();">
<div class="wrapper">

    <!-- Main Header -->
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

            <?php include_once 'navbar-user.php' ?>
        </nav>
    </header>
    <!-- Left side column. contains the logo and sidebar -->
    <aside class="main-sidebar">
        <section class="sidebar">
            <div class="user-panel">
                <div class="pull-left image">
                    <img src="dist/img/user.png" class="img-circle" alt="User Image">
                </div>
                <div class="pull-left info">
                    <p><?= $_SESSION["nome"] ?></p>
                </div>
            </div>

            <ul class="sidebar-menu">
                <li>
                    <a href="javascript:abreModal('#altUser');">
                        <i class="fa fa-user"></i> <span>Alterar Usuário</span>
                    </a>
                </li>
                <li class="treeview">
                    <a href="#">
                        <i class="fa fa-bar-chart"></i> <span>Relatórios</span>
                    </a>
                    <ul class="treeview-menu">
                        <li><a href="javascript:abreModal('#relPedidos');"><i class="fa fa-circle-o"></i> Pedidos</a>
                        </li>
                        <li><a href="javascript:abreModal('#relLibOrc');"><i class="fa fa-circle-o"></i> Liberações
                                Orçamentárias</a></li>
                    </ul>
                </li>
                <li>
                    <a href="javascript:listRascunhos();">
                        <i class="fa fa-pencil"></i> <span>Rascunhos</span>
                    </a>
                </li>
                <li>
                    <a href="javascript:listPedidos();">
                        <i class="fa fa-file-text"></i> <span>Meus Pedidos</span>
                    </a>
                </li>
                <li>
                    <a href="javascript:listLancamentos(<?= $_SESSION["id_setor"] ?>);">
                        <i class="fa fa-dollar"></i> <span>Saldos</span>
                    </a>
                </li>
                <li>
                    <a href="javascript:listAdiantamentos();">
                        <i class="fa fa-plus"></i> <span>Meus Adiantamentos</span>
                    </a>
                </li>
                <li>
                    <a href="javascript:listSolicAltPedidos();">
                        <i class="fa fa-refresh"></i> <span>Solic Alt Pedidos</span>
                    </a>
                </li>
                <li>
                    <a href="javascript:listProcessos('users');">
                        <i class="fa fa-tags"></i> <span>Processos</span>
                    </a>
                </li>
            </ul>
        </section>
    </aside>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                <?= $_SESSION['nome_setor']; ?>
                <small>Saldo: R$ <?= number_format(Busca::getSaldo($_SESSION['id_setor']), 3, ',', '.'); ?></small>
            </h1>
            <ol class="breadcrumb">
                <li class="active"><i class="fa fa-dashboard"></i> Solicitações de Empenho</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="row">
                <div class="col-xs-12">
                    <div class="box box-primary">
                        <div class="box-header">
                            <h3 class="box-title">Itens do Processo: <span id="numProc">--------------------</span></h3>
                            <div class="box-tools pull-right">
                                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i
                                            class="fa fa-minus"></i>
                                </button>
                            </div>
                        </div><!-- /.box-header -->
                        <input id="searchProcesso" type="hidden">
                        <div class="box-body">
                            <table class="table table-bordered table-striped" id="tableProcessos">
                                <thead>
                                <tr>
                                    <th></th>
                                    <th>Fornecedor</th>
                                    <th>Cod Reduzido</th>
                                    <th>Qt Solicitada</th>
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
                    </div><!-- ./box -->
                </div> <!-- ./col-xs-12 -->
            </div>
            <div class="row">
                <div class="col-xs-12">
                    <div class="box box-primary">
                        <div class="box-header">
                            <h3 class="box-title">Pedido | SALDO <span
                                        id="text_saldo_total">R$ <?= number_format($saldo_total, 3, ',', '.'); ?></span>
                            </h3>
                            <div class="box-tools pull-right">
                                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i
                                            class="fa fa-minus"></i>
                                </button>
                            </div>
                        </div><!-- /.box-header -->
                        <form action="../php/geral.php" method="POST">
                            <input type="hidden" name="users" value="1">
                            <input type="hidden" name="form" value="pedido">
                            <input id="pedido" type="hidden" name="pedido" value="0">
                            <div class="box-body">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                    <tr>
                                        <th></th>
                                        <th>NUM_PROCESSO</th>
                                        <th>COD_REDUZIDO</th>
                                        <th>COMPLEMENTO_ITEM</th>
                                        <th>VL_UNITARIO</th>
                                        <th>NOME_FORNECEDOR</th>
                                        <th>NUM_LICITACAO</th>
                                        <th>QT_SOLICITADA</th>
                                        <th>VALOR</th>
                                    </tr>
                                    </thead>
                                    <tbody id="conteudoPedido"></tbody>
                                </table>
                                <table class="table table-bordered table-striped">
                                    <tr>
                                        <td>
                                            <div class="form-group">
                                                <label>Total</label>
                                                <input class="form-control" id="total" name="total"
                                                       style="font-size: 14pt;" type="text" disabled value="R$ 0">
                                            </div>
                                        </td>
                                    </tr>
                                    <input id="total_hidden" type="hidden" name="total_hidden" value="0">
                                    <input id="saldo_total" type="hidden" name="saldo_total"
                                           value="<?= $saldo_total ?>">
                                </table>
                                <div class="form-group">
                                    <table class="table table-bordered table-striped">
                                        <tr>
                                            <?= BuscaLTE::getPrioridades(); ?>
                                        </tr>
                                    </table>
                                </div>
                                <div class="form-group">
                                    <label>Observações</label>
                                    <textarea class="form-control" id="obs" name="obs" rows="1" required></textarea>
                                </div>
                                <h2>Licitação</h2>
                                <table class="table table-bordered table-striped">
                                    <?= BuscaLTE::getOptionsLicitacao(4); ?>
                                </table>
                                <table class="table table-bordered table-striped">
                                    <input id="idLic" type="hidden" name="idLic" value="0">
                                    <tr>
                                        <td>
                                            <div class="form-group">
                                                <label>Número</label>
                                                <input class="form-control" id="infoLic" name="infoLic" required/>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-group">
                                                <label>UASG</label>
                                                <input class="form-control" id="uasg" name="uasg" disabled/>
                                            </div>
                                        </td>
                                        <td>
                                            <div id="divProcOri" class="form-group">
                                                <label>Processo Original</label>
                                                <input class="form-control" id="procOri" name="procOri" disabled/>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-group">
                                                <input type="radio" name="geraContrato" id="gera" class="minimal"
                                                       value="1">
                                                Gera Contrato
                                            </div>
                                            <div class="form-group">
                                                <input type="radio" name="geraContrato" id="ngera" class="minimal"
                                                       value="0">
                                                Não Gera Contrato
                                            </div>
                                        </td>
                                    </tr>
                                </table>
                                <?php
                                $select_grupo = BuscaLTE::getOptionsGrupos($_SESSION["id_setor"]);
                                if (strlen($select_grupo) > 0): ?>
                                    <h2>Grupo</h2>
                                    <div class="form-group">
                                        <label>Selecione o grupo</label>
                                        <select id="grupo" class="form-control select2" name="grupo" required>
                                            <?= $select_grupo ?>
                                        </select>
                                    </div>
                                <?php endif; ?>
                                <div class="form-group">
                                    <input class="minimal" id="checkPedContr" name="pedidoContrato" type="checkbox">
                                    Pedido de Contrato
                                </div>
                                <table class="table table-bordered table-striped">
                                    <tr>
                                        <?= BuscaLTE::getOptionsContrato(); ?>
                                    </tr>
                                </table>
                                <div class="form-group">
                                    <label>SIAFI</label>
                                    <input class="form-control" id="siafi" name="siafi" type="text">
                                </div>
                                <div class="input-group">
                        <span class="input-group-addon">
                          <input id="checkPlanoTrabalho" type="checkbox" name="checkPlanoTrabalho">
                        </span>
                                    <input id="planoTrabalho" class="form-control" type="text"
                                           placeholder="Plano de Trabalho"
                                           name="planoTrabalho" disabled>
                                </div>
                                <table class="table table-bordered table-striped">
                                    <tr>
                                        <td>
                                            <div class="form-group">
                                                <label>Processo SEI</label>
                                                <input class="form-control" id="procSei" name="procSei" type="text">
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-group">
                                                <label>Pedido SEI</label>
                                                <input class="form-control" id="pedSei" name="pedSei" type="text">
                                            </div>
                                        </td>
                                    </tr>
                                </table>
                                <!-- /input-group -->
                            </div><!-- ./card-inner -->
                            <div class="box-footer">
                                <button id="btnLimpa" class="btn btn-default" type="button" style="width: 49%;"
                                        onclick="limpaTelaSolic();"><i class="fa fa-close"></i>&nbsp;Limpar
                                </button>
                                <button class="btn btn-primary" type="submit" style="width: 50%;"><i
                                            class="fa fa-send"></i>&nbsp;Enviar Pedido / Salvar Rascunho
                                </button>
                            </div>
                        </form>
                    </div><!-- ./card-main -->
                </div> <!-- ./card -->
            </div>
        </section>
        <!-- /.content -->
    </div>

    <div id="snackbar">Some text some message..</div>
    <!-- /.content-wrapper -->

    <!-- Main Footer -->
    <footer class="main-footer">
        <div class="pull-right hidden-xs">
            <b>Version</b> <?= VERSION ?>
        </div>
        <?= COPYRIGHT ?>
    </footer>
    <?php include_once __DIR__ . "/util/modal-relLibOrc.php" ?>
    <?php include_once __DIR__ . "/util/modal-relPedidos.php" ?>
    <div aria-hidden="true" class="modal fade" id="altUser" role="dialog" tabindex="-1">
        <div class="modal-dialog" style="width: 40%;">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Alterar Usuário</h4>
                </div>
                <form action="../php/geral.php" method="POST">
                    <input type="hidden" name="form" value="altUser"/>
                    <input type="hidden" name="users" value="1"/>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Usuário</label>
                            <select id="userA" class="form-control" name="user" required>
                                <?= BuscaLTE::getUsers(true); ?>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary" type="submit" style="width: 100%;"><i class="fa fa-refresh"></i>&nbsp;Trocar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div aria-hidden="true" class="modal fade" id="listAdiantamentos" role="dialog" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Solicitações de Adiantamentos</h4>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered table-striped" id="tableListAdiantamentos">
                        <thead>
                        <tr>
                            <th>DATA_SOLICITACAO</th>
                            <th>DATA_ANALISE</th>
                            <th>VALOR_ADIANTADO</th>
                            <th>JUSTIFICATIVA</th>
                            <th>STATUS</th>
                        </tr>
                        </thead>
                        <tbody id="tbodyListAdiantamentos"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div aria-hidden="true" class="modal fade" id="adiantamento" role="dialog" tabindex="-1" data-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Solicitar Adiantamento</h4>
                </div>
                <form action="../php/geral.php" method="POST">
                    <input type="hidden" name="form" value="adiantamento"/>
                    <input type="hidden" name="users" value="1"/>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Valor</label>
                            <input class="form-control" id="valor_adiantamento" name="valor_adiantamento" type="number"
                                   step="0.001" min="0.001" required>
                        </div>
                        <div class="form-group">
                            <label>Justificativa</label>
                            <textarea class="form-control" id="justificativa" name="justificativa" rows="2"
                                      required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary" type="submit" style="width: 100%;"><i class="fa fa-send"></i>&nbsp;Enviar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div aria-hidden="true" class="modal fade" id="listLancamentos" role="dialog" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Saldos</h4>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered table-striped">
                        <tr>
                            <td>Saldo Disponível</td>
                            <td>R$ <?= number_format($saldo_total, 3, ',', '.') ?></td>
                        </tr>
                        <?= $pedidos_em_analise ?>
                    </table>
                    <small class="label bg-gray">Essa tabela vai exibir no máximo <?= LIMIT_MAX ?> linhas. Você pode
                        gerar relatório das Liberações Orçamentárias do seu Setor ;)
                    </small>
                    <table id="tableListLancamentos" class="table table-bordered table-striped">
                        <thead>
                        <tr>
                            <th></th>
                            <th>Setor</th>
                            <th>Data</th>
                            <th>Valor</th>
                            <th>Categoria</th>
                            <th>Origem / Destino</th>
                        </tr>
                        </thead>
                        <tbody id="tbodyListLancamentos"></tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary" type="button" onclick="abreModal('#adiantamento');"
                            style="width: 100%;"><i class="fa fa-dollar"></i>&nbsp;Solicitar Adiantamento
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div aria-hidden="true" class="modal fade" id="listSolicAltPedidos" role="dialog" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Solicitações de Alteração de Pedidos</h4>
                </div>
                <div class="modal-body">
                    <table id="tableSolicAltPedido" class="table table-bordered table-striped">
                        <thead>
                        <tr>
                            <th>NUM_PEDIDO</th>
                            <th>DATA_SOLICITACAO</th>
                            <th>DATA_ANALISE</th>
                            <th>JUSTIFICATIVA</th>
                            <th>STATUS</th>
                        </tr>
                        </thead>
                        <tbody id="tbodySolicAltPedido"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div aria-hidden="true" class="modal fade" id="alt_pedido" role="dialog" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Solicitar Alteração de Pedido</h4>
                </div>
                <form action="javascript:formEnvia();" method="POST">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Pedido</label>
                            <input class="form-control" id="id_pedido_alt" name="id_pedido_alt" type="number" required
                                   disabled>
                        </div>
                        <div class="form-group">
                            <label>Justificativa</label>
                            <textarea class="form-control" id="justificativa_alt_ped" name="justificativa_alt_ped"
                                      rows="2" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary" type="submit" style="width: 100%;"><i class="fa fa-send"></i>&nbsp;Enviar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div aria-hidden="true" class="modal fade" id="loadMoreCustom" role="dialog" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Carregar Pedidos</h4>
                </div>
                <form action="javascript:loadMoreRequests();" method="POST">
                    <div class="modal-body">
                        <small class="label bg-gray">Carrega todos os pedidos entre Limite 1 e Limite 2.</small>
                        <div class="form-group">
                            <label>Limite 1</label>
                            <input type="number" class="form-control" id="limit1" name="limit1" step="1" min="0"
                                   required>
                        </div>
                        <div class="form-group">
                            <label>Limite 2</label>
                            <input type="number" class="form-control" id="limit2" name="limit2" step="1" min="0"
                                   required>
                        </div>
                        <small class="label bg-gray">Por motivos de segurança, serão retornados no
                            máximo <?= LIMIT_MAX ?> resultados nesta consulta. ;)
                        </small>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary" type="submit" style="width: 100%;"><i
                                    class="fa fa-cloud-download"></i>&nbsp;Carregar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div aria-hidden="true" class="modal fade" id="listPedidos" role="dialog" tabindex="-1">
        <div class="modal-dialog" style="width: 90%">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Meus Pedidos</h4>
                </div>
                <div class="modal-body">
                    <div id="overlayLoad" class="overlay" style="display: none;">
                        <i class="fa fa-refresh fa-spin"></i>
                    </div>
                    <div class="margin">
                        <button class="btn btn-primary" type="button" onclick="abreModal('#loadMoreCustom');"
                                data-toggle="tooltip" title="Carregar mais pedidos"><i class="fa fa-cloud-download"></i>&nbsp;Carregar
                        </button>
                    </div>
                    <small class="label bg-gray">Essa tabela vai exibir no máximo <?= LIMIT_MAX ?> linhas. Você pode
                        gerar relatório dos Pedidos do seu Setor ;)
                    </small>
                    <table id="tableListPedidos" class="table table-bordered table-striped">
                        <thead>
                        <tr>
                            <th>NUM</th>
                            <th>Data de Envio</th>
                            <th>Prioridade</th>
                            <th>Status</th>
                            <th>SIAFI</th>
                            <th>Valor</th>
                            <th>Fornecedor</th>
                            <th>Opções</th>
                        </tr>
                        </thead>
                        <tbody id="tbodyListPedidos"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div aria-hidden="true" class="modal fade" id="listRascunhos" role="dialog" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Rascunhos</h4>
                </div>
                <div class="modal-body">
                    <small class="label bg-gray">Essa tabela vai exibir no máximo <?= LIMIT_MAX ?> linhas. Para exibir
                        alguma linha que não está sendo mostrada, é necessário excluir algum Rascunho. ;)
                    </small>
                    <table id="tableListRascunhos" class="table table-bordered table-striped">
                        <thead>
                        <tr>
                            <th>Num Pedido</th>
                            <th>Status</th>
                            <th>Última modificação</th>
                            <th>Valor</th>
                            <th>Opções</th>
                        </tr>
                        </thead>
                        <tbody id="tbodyListRascunhos"></tbody>
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

    <?php
    include_once "util/modals-util.php";
    ?>
</div>
<!-- ./wrapper -->

<!-- REQUIRED JS SCRIPTS -->

<!-- jQuery 2.2.3 -->
<script src="plugins/jQuery/jquery-2.2.3.min.js"></script>
<!-- Bootstrap 3.3.6 -->
<script src="bootstrap/js/bootstrap.min.js"></script>
<!-- DataTables -->
<script src="plugins/datatables/jquery.dataTables.min.js"></script>
<script src="plugins/datatables/dataTables.bootstrap.min.js"></script>
<!-- Select2 -->
<script src="plugins/select2/select2.full.min.js"></script>
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
<!-- InputMask -->
<script src="plugins/input-mask/jquery.inputmask.js"></script>
<script src="plugins/input-mask/jquery.inputmask.date.extensions.js"></script>
<script src="plugins/input-mask/jquery.inputmask.extensions.js"></script>
<!-- PACE -->
<script src="plugins/pace/pace.min.js"></script>
<!-- page scripts -->
<script type="text/javascript" src="js/util_lte.min.js"></script>
<script type="text/javascript" src="../iniLTE.min.js"></script>
<script type="text/javascript" src="js/rows.min.js"></script>
<script type="text/javascript" src="js/modals-geral.min.js"></script>
</body>
</html>


