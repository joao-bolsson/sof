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
<html lang="pt">
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

            <?php include_once 'navbar-user.php' ?>
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
            <?php if ($permissao->saldos): ?>
                <div id="rowSolicAdi" class="row" style="display: none;">
                    <div class="col-xs-12">
                        <div class="box">
                            <div class="box-header">
                                <h3 class="box-title">Solicitações de Adiantamento</h3>
                                <div class="box-tools pull-right">
                                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i
                                                class="fa fa-minus"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="box-body">
                                <table class="table">
                                    <tr>
                                        <td>
                                            <div class="form-group">
                                                <label>
                                                    <input id="stabertos" type="radio" name="stadi" class="minimal"
                                                           value="2" checked/>
                                                    Abertos
                                                </label>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-group">
                                                <label>
                                                    <input id="staprovados" type="radio" name="stadi" class="minimal"
                                                           value="1"/>
                                                    Aprovados
                                                </label>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-group">
                                                <label>
                                                    <input id="streprovado" type="radio" name="stadi" class="minimal"
                                                           value="0"/>
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
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12">
                        <div class="box box-warning box-solid">
                            <div class="box-header">
                                <h3 class="box-title">Pedidos em Vencimento</h3>
                                <div class="box-tools pull-right">
                                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i
                                                class="fa fa-minus"></i>
                                    </button>
                                </div>
                            </div><!-- /.box-header -->
                            <div class="box-body">
                                <div class="margin">
                                    <button class="btn btn-primary" type="button" onclick="printDueDate()"><i
                                                class="fa fa-print"></i>Imprimir Todos
                                    </button>
                                </div>
                                <table id="tableProcVenc" class="table table-bordered table-striped">
                                    <thead>
                                    <tr>
                                        <th>Pedido</th>
                                        <th>Setor</th>
                                        <th>Processo</th>
                                        <th>Fornecedor</th>
                                        <th>Data Fim</th>
                                    </tr>
                                    </thead>
                                    <tbody id="tbodyProcVenc"></tbody>
                                </table>
                            </div>
                            <div id="overlayLoadVenc" class="overlay" style="display: none;">
                                <i class="fa fa-refresh fa-spin"></i>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($permissao->recepcao): ?>
                <div class="row">
                    <div class="col-xs-12">
                        <div class="box box-primary">
                            <div class="box-header">
                                <h3 class="box-title">Processos</h3>
                                <div class="box-tools pull-right">
                                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i
                                                class="fa fa-minus"></i>
                                    </button>
                                </div>
                            </div><!-- /.box-header -->
                            <div class="box-body">
                                <div class="margin">
                                    <button class="btn btn-primary" type="button" onclick="addProcesso(' ', 0)"><i
                                                class="fa fa-plus"></i>&nbsp;Adicionar Processo
                                    </button>
                                </div>
                                <table class="table stripe" id="tableRecepcao" style="width: 100%;">
                                    <thead>
                                    <tr>
                                        <th></th>
                                        <th>PROCESSO</th>
                                        <th>TIPO</th>
                                        <th>ESTANTE</th>
                                        <th>PRATELEIRA</th>
                                        <th>ENTRADA EM</th>
                                        <th>SAIDA EM</th>
                                        <th>RESPONSÁVEL</th>
                                        <th>RETORNO EM</th>
                                        <th>OBS</th>
                                    </tr>
                                    </thead>
                                    <tbody id="conteudoRecepcao"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($permissao->pedidos): ?>
                <div id="rowAltPed" class="row" style="display: none;">
                    <div class="col-xs-12">
                        <div class="box">
                            <div class="box-header">
                                <h3 class="box-title">Solicitações de Alteração de Pedido</h3>
                                <div class="box-tools pull-right">
                                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i
                                                class="fa fa-minus"></i>
                                    </button>
                                </div>
                            </div><!-- /.box-header -->
                            <div class="box-body">
                                <table class="table">
                                    <tr>
                                        <td>
                                            <div class="form-group">
                                                <label>
                                                    <input id="stAltAbertos" type="radio" name="stAlt" class="minimal"
                                                           value="2" checked/>
                                                    Abertos
                                                </label>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-group">
                                                <label>
                                                    <input id="stAltAprovados" type="radio" name="stAlt" class="minimal"
                                                           value="1"/>
                                                    Aprovados
                                                </label>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-group">
                                                <label>
                                                    <input id="stAltReprovado" type="radio" name="stAlt" class="minimal"
                                                           value="0"/>
                                                    Reprovados
                                                </label>
                                            </div>
                                        </td>
                                    </tr>
                                </table>
                                <table id="tableSolicAltPedido" class="table table-bordered table-striped">
                                    <thead>
                                    <tr>
                                        <th>Opções</th>
                                        <th>Pedido</th>
                                        <th>Setor</th>
                                        <th>Data Solic</th>
                                        <th>Data Análise</th>
                                        <th>Justificativa</th>
                                        <th>Status</th>
                                    </tr>
                                    </thead>
                                    <tbody id="contSolicAltPedido"></tbody>
                                </table>
                            </div><!-- /.box-body -->
                        </div><!-- /.box -->
                    </div><!-- /.col -->
                </div><!-- /.row -->
                <div id="rowPedidos" class="row">
                    <div class="col-xs-12">
                        <div class="box box-primary">
                            <div class="box-header">
                                <h3 class="box-title">Pedidos</h3>
                                <div class="box-tools pull-right">
                                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i
                                                class="fa fa-minus"></i>
                                    </button>
                                </div>
                            </div><!-- /.box-header -->
                            <div class="box-body">
                                <div class="margin">
                                    <button id="btnPrintCheck" class="btn btn-primary" type="button"
                                            onclick="printChecks()" disabled><i class="fa fa-print"></i>&nbsp;Imprimir
                                    </button>
                                    <button id="btnAprovGeren" class="btn btn-primary" type="button"
                                            onclick="aprovGerencia()" disabled><i class="fa fa-check-circle"></i>&nbsp;Aprovado
                                        pela Gerência
                                    </button>
                                    <button class="btn btn-primary" type="button"
                                            onclick="abreModal('#loadMoreCustom');" data-toggle="tooltip"
                                            title="Carregar mais pedidos"><i class="fa fa-cloud-download"></i>&nbsp;Carregar
                                    </button>
                                </div>
                                <table id="tableSolicitacoes" class="table table-bordered table-striped">
                                    <thead>
                                    <tr>
                                        <th>
                                            <div class=form-group>
                                                <input type="checkbox" name="checkPedRel" id="checkPedRel" value="1">
                                            </div>
                                        </th>
                                        <th>Opções</th>
                                        <th>Pedido</th>
                                        <th>Setor</th>
                                        <th>Data</th>
                                        <th>Prioridade</th>
                                        <th>Status</th>
                                        <th>Valor</th>
                                        <th>Empenho</th>
                                        <th>Fornecedor</th>
                                    </tr>
                                    </thead>
                                    <tbody id="conteudoSolicitacoes"></tbody>
                                </table>
                            </div>
                            <div id="overlayLoad" class="overlay" style="display: none;">
                                <i class="fa fa-refresh fa-spin"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="rowDetPedido" class="row" style="display: none;">
                    <div class="col-xs-12">
                        <div class="box">
                            <div class="box-header">
                                <h3 class="box-title">Detalhes do Pedido: <span id="detPedId"></span>&nbsp;SETOR: <span
                                            id="nomeSetorDet"></span>&nbsp;SALDO DO SOLICITANTE <span
                                            id="text_saldo_total">R$ 0.000</span></h3>
                                <div class="box-tools pull-right">
                                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i
                                                class="fa fa-minus"></i>
                                    </button>
                                </div>
                            </div><!-- /.box-header -->
                            <form id="formPedido" action="../php/geral.php" method="POST">
                                <input type="hidden" name="admin" value="1"/>
                                <input id="form" type="hidden" name="form" value="" required/>
                                <input id="id_pedido" type="hidden" name="id_pedido" value="0"/>
                                <input id="id_setor" type="hidden" name="id_setor" value="0"/>
                                <div class="box-body">
                                    <div id="divTableItens">
                                        <table id="tableItensPedido" class="table table-bordered table-striped">
                                            <thead>
                                            <tr>
                                                <th>Ferramentas</th>
                                                <th>COD_DESPESA</th>
                                                <th>DESCRICAO_DESPESA</th>
                                                <th>NUM_EXTRATO</th>
                                                <th>NUM_CONTRATO</th>
                                                <th>NUM_PROCESSO</th>
                                                <th>DESCR_MOD_COMPRA</th>
                                                <th>NUM_LICITACAO</th>
                                                <th>DT_INICIO</th>
                                                <th>DT_FIM</th>
                                                <th>CGC_FORNECEDOR</th>
                                                <th>NOME_FORNECEDOR</th>
                                                <th>COD_REDUZIDO</th>
                                                <th>SEQ_ITEM_PROCESSO</th>
                                                <th>DESCRICAO</th>
                                                <th>VL_UNITARIO</th>
                                                <th>QT_CONTRATO</th>
                                                <th>VL_CONTRATO</th>
                                                <th>QT_UTILIZADO</th>
                                                <th>VL_UTILIZADO</th>
                                                <th>QT_SALDO</th>
                                                <th>VL_SALDO</th>
                                                <th>QT_SOLICITADA</th>
                                                <th>VALOR</th>
                                                <th></th>
                                            </tr>
                                            </thead>
                                            <tbody id="conteudoPedido"></tbody>
                                        </table>
                                    </div>
                                    <div class="form-group">
                                        <label>Observações</label>
                                        <textarea class="form-control" id="obs" name="obs" rows="2" disabled></textarea>
                                    </div>
                                    <input id="total_hidden" type="hidden" name="total_hidden" value="0">
                                    <input id="saldo_total" type="hidden" name="saldo_total" value="0.000">
                                    <input id="prioridade" type="hidden" name="prioridade" value="0">
                                    <table class="table table-bordered table-striped">
                                        <?= BuscaLTE::getStatus(4) ?>
                                    </table>
                                    <div class="form-group">
                                        <label>Comentário</label>
                                        <textarea class="form-control" rows="2" id="comentario"
                                                  name="comentario"></textarea>
                                    </div>
                                    <small class="label bg-gray">Essa opção só irá ser considerada se o pedido for
                                        marcado como Reprovado
                                    </small>
                                    <div class="form-group">
                                        <label>
                                            <input id="checkExcluir" type="checkbox" class="minimal" name="excluir">
                                            Excluir pedido
                                        </label>
                                    </div>
                                </div><!-- ./box-body -->
                                <div class="box-footer">
                                    <div class="btn-group" style="width: 100%;">
                                        <button id="btnLimpa" class="btn btn-default" type="button" style="width: 49%;"
                                                onclick="limpaTela();"><i class="fa fa-undo"></i>&nbsp;Limpar / Esconder
                                        </button>
                                        <button class="btn btn-primary" type="submit" style="width: 50%;"><i
                                                    class="fa fa-check"></i>&nbsp;Salvar Alterações
                                        </button>
                                    </div>
                                </div>
                            </form>
                            <div id="overlayLoadDetPed" class="overlay" style="display: none;">
                                <i class="fa fa-refresh fa-spin"></i>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </section>
    </div>

    <div id="snackbar">Some text some message..</div>

    <footer class="main-footer">
        <div class="pull-right hidden-xs">
            <b>Version</b> <?= VERSION ?>
        </div>
        <?= COPYRIGHT ?>
    </footer>
    <?php if ($permissao->pedidos): ?>
        <div aria-hidden="true" class="modal fade" id="cadEmpenho" role="dialog" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                    aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Cadastrar Empenho</h4>
                    </div>
                    <form id="formEnviaEmpenho">
                        <input type="hidden" name="form" value="enviaEmpenho">
                        <input type="hidden" name="admin" value="1">
                        <div class="modal-body">
                            <div class="form-group">
                                <label>Pedido</label>
                                <input class="form-control" name="id_pedido" type="number"
                                       required disabled/>
                            </div>
                            <div class="form-group">
                                <label>Empenho</label>
                                <input class="form-control" name="empenho" required/>
                            </div>
                            <div class="form-group">
                                <label>Data</label>
                                <input class="form-control date" name="data" required/>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-primary" type="submit" style="width: 100%;"><i
                                        class="fa fa-send"></i>Cadastrar / Editar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div aria-hidden="true" class="modal fade" id="cadFontes" role="dialog" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                    aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Cadastrar Fontes</h4>
                    </div>
                    <form id="formEnviaFontes">
                        <input type="hidden" name="admin" value="1">
                        <input type="hidden" name="form" value="enviaFontes">
                        <div class="modal-body">
                            <div class="form-group">
                                <label>Pedido</label>
                                <input class="form-control" name="id_pedido" type="number"
                                       required disabled>
                            </div>
                            <div class="form-group">
                                <label>Fonte de Recurso</label>
                                <input class="form-control" name="fonte" required/>
                            </div>
                            <div class="form-group">
                                <label>PTRES</label>
                                <input class="form-control" name="ptres" required/>
                            </div>
                            <div class="form-group">
                                <label>Plano Interno</label>
                                <input class="form-control" name="plano" required/>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-primary" type="submit" style="width: 100%;"><i
                                        class="fa fa-send"></i>&nbsp;Cadastrar / Editar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <?php include_once __DIR__ . "/util/modal-relLibOrc.php" ?>
        <?php include_once __DIR__ . "/util/modal-relPedidos.php" ?>
        <div aria-hidden="true" class="modal fade" id="relSIAFI" role="dialog" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                    aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Relatório SIAFI</h4>
                    </div>
                    <form action="../admin/printRelatorio.php" method="post" target="_blank">
                        <input type="hidden" name="tipo" value="siafi"/>
                        <input type="hidden" name="relatorio" value="1"/>
                        <div class="modal-body">
                            <div class="form-group">
                                <label>Setor</label>
                                <select id="selectSetorRelSIAFI" class="form-control" name="setor" required>
                                    <?= BuscaLTE::getOptionsSetores(); ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Fonte</label>
                                <select id="selectFonteSIAFI" class="form-control select2" name="fonte[]"
                                        multiple="multiple"
                                        data-placeholder="Selecione" required>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Processo</label>
                                <select class="form-control select2" multiple="multiple" data-placeholder="Selecione"
                                        name="num_processo[]" required>
                                    <option value="Todos">Todos</option>
                                    <?= BuscaLTE::getOptionsProcessos(); ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Data Início</label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    <input type="text" class="form-control date" name="dataI" required
                                           data-inputmask="'alias': 'dd/mm/yyyy'" data-mask>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Data Fim</label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    <input type="text" class="form-control date" name="dataF" required
                                           data-inputmask="'alias': 'dd/mm/yyyy'" data-mask>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-primary" type="submit" style="width: 100%;"><i
                                        class="fa fa-refresh"></i>&nbsp;Gerar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div aria-hidden="true" class="modal fade" id="relEmpForn" role="dialog" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                    aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Empenho/Fornecedor</h4>
                    </div>
                    <form action="../view/empForn.php" method="post">
                        <div class="modal-body">
                            <div class="form-group">
                                <label>Data Início</label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    <input type="text" class="form-control date" name="dataI" required
                                           data-inputmask="'alias': 'dd/mm/yyyy'" data-mask>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Data Fim</label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    <input type="text" class="form-control date" name="dataF" required
                                           data-inputmask="'alias': 'dd/mm/yyyy'" data-mask>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-primary" type="submit" style="width: 100%;"><i
                                        class="fa fa-refresh"></i>&nbsp;Gerar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="modal fade" id="importItens" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span>&times;</span>
                        </button>
                        <h4 class="modal-title">Importar Itens</h4>
                    </div>
                    <form enctype="multipart/form-data" action="../php/geral.php" method="post">
                        <input type="hidden" name="admin" value="1">
                        <input type="hidden" name="form" value="importItens">
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="file">Arquivo</label>
                                <input type="file" id="file" name="file" required>

                                <p class="help-block">Tamanho máximo: <?= MAX_UPLOAD_SIZE ?> MB</p>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-primary" type="submit" style="width: 100%;"><i
                                        class="fa fa-cloud-upload"></i>&nbsp;Enviar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="modal fade" id="infoItem" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                    aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Informações do Item</h4>
                    </div>
                    <?php include_once __DIR__ . "/util/formEditRegItem.php"; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <?php if ($permissao->saldos): ?>
        <div aria-hidden="true" class="modal fade" id="transferencia" role="dialog" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                    aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Nova Transferência</h4>
                    </div>
                    <form id="formTransferencia">
                        <input type="hidden" name="admin" value="1">
                        <input type="hidden" name="form" value="transfereSaldo">
                        <div class="modal-body">
                            <div class="margin">
                                <button class="btn btn-primary" type="button" onclick="abreModal('#regJustify')"><i
                                            class="fa fa-plus"></i> Justificativa
                                </button>
                            </div>
                            <div class="form-group">
                                <label>Setor Origem</label>
                                <select class="form-control" id="transfOri" name="ori" required>
                                    <?= BuscaLTE::getOptionsSetores([2]); ?>
                                </select>
                                <p id="saldoDispOri" style="font-weight: bold;"></p>
                            </div>
                            <div class="form-group">
                                <label>Setor Destino</label>
                                <select id="transfDest" class="form-control" name="dest" required
                                        onchange="changeTransfDest(this)">
                                    <?= BuscaLTE::getOptionsSetores([], [2]); ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Valor</label>
                                <input class="form-control" name="valor" type="number" step="0.001"
                                       required min="0.001">
                            </div>
                            <div class="form-group">
                                <label>Justificativa</label>
                                <select class="form-control select2" name="just"
                                        data-placeholder="Selecione o motivo da transferência" required>
                                    <?= BuscaLTE::getJustifies(); ?>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-primary" type="submit" style="width: 100%;"><i
                                        class="fa fa-refresh"></i>&nbsp;Liberar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div aria-hidden="true" class="modal fade" id="cadFontesTransf" role="dialog" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                    aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Cadastrar Fontes</h4>
                    </div>
                    <form action="../php/geral.php" method="POST">
                        <input type="hidden" name="admin" value="1"/>
                        <input type="hidden" name="form" value="cadFontesTransf"/>
                        <div class="modal-body">
                            <small class="label bg-gray"><i class="fa fa-exclamation-circle "></i> A página será
                                recarregada em seguida
                            </small>
                            <div class="form-group">
                                <label>Setor</label>
                                <select id="fonteSetores" class="form-control" name="setores[]" multiple="multiple"
                                        data-placeholder="Selecione" required>
                                    <?= BuscaLTE::getOptionsSetores([], [2]); ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Fonte de Recurso</label>
                                <input class="form-control" name="fonte" required/>
                            </div>
                            <div class="form-group">
                                <label>PTRES</label>
                                <input class="form-control" name="ptres" required/>
                            </div>
                            <div class="form-group">
                                <label>Plano Interno</label>
                                <input class="form-control" name="plano" required/>
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
        <div class="modal fade" id="regJustify" role="dialog">
            <div class="modal-dialog" style="width: 40%;">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                    aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Cadastrar Justificativa</h4>
                    </div>
                    <form action="../php/geral.php" method="post">
                        <input type="hidden" name="admin" value="1">
                        <input type="hidden" name="form" value="regJustify">
                        <div class="modal-body">
                            <small class="label bg-gray"><i class="fa fa-exclamation-circle "></i> A página será
                                recarregada em seguida
                            </small>
                            <div class="form-group">
                                <label>Justificativa</label>
                                <textarea class="form-control" rows="2" name="justificativa" maxlength="40"
                                          placeholder="Digite a justificativa. Máx. 40 caracteres" required></textarea>
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
        <div aria-hidden="true" class="modal fade" id="freeSaldos" role="dialog" tabindex="-1">
            <div class="modal-dialog" style="width: 40%;">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                    aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Liberar Saldo</h4>
                    </div>
                    <div class="modal-body">
                        <form id="formLiberaSaldo">
                            <input type="hidden" name="admin" value="1">
                            <input type="hidden" name="form" value="liberaSaldo">
                            <div class="modal-inner">
                                <div class="form-group">
                                    <label>Setor</label>
                                    <select class="form-control" name="id_setor" required>
                                        <?= BuscaLTE::getOptionsSetores([2]); ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Valor</label>
                                    <input type="number" class="form-control" placeholder="Valor"
                                           name="valor" step="0.001" required min="0.001">
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button class="btn btn-primary" type="submit" style="width: 100%;"><i
                                            class="fa fa-refresh"></i>&nbsp;Liberar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div aria-hidden="true" class="modal fade" id="listLancamentos" role="dialog" tabindex="-1">
            <div class="modal-dialog" style="width: 90%;">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                    aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Liberações Orçamentárias</h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group form-group-label">
                            <label>Setor</label>
                            <select id="selectSetor" class="form-control" onchange="changeSetor();">
                                <option value="-1">Nenhum</option>
                                <option value="0">Todos</option>
                                <?= BuscaLTE::getOptionsSetores(); ?>
                            </select>
                        </div>
                        <table class="table table-bordered table-striped">
                            <tbody id="rowDataSaldo">
                            <tr>
                                <td>Dados do setor selecionado</td>
                                <td id="totOut">Total de Saídas: R$ 0.000</td>
                                <td id="totIn">Total de Entradas: R$ 0.000</td>
                            </tr>
                            </tbody>
                        </table>
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
                </div>
            </div>
        </div>
    <?php endif; ?>

    <?php if ($permissao->recepcao): ?>
        <div class="modal fade" id="procNaoDev" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                    aria-hidden="true">&times;</span>
                        </button>
                        <h4 class="modal-title">Processos Não Devolvidos</h4>
                    </div>
                    <div class="modal-body" id="tbodyProcNaoDev">Aguarde...</div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="relatorio" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                    aria-hidden="true">&times;</span>
                        </button>
                        <h4 class="modal-title">Gerar Relatório</h4>
                    </div>
                    <form id="formRelatorioRecepcao">
                        <input type="hidden" name="admin" value="1">
                        <input type="hidden" name="form" value="relatorioProcessos">
                        <div class="modal-body">
                            <div class="form-group">
                                <label>Tipo</label>
                                <select class="form-control" name="tipo" required>
                                    <option value="0">Todos</option>
                                    <?= BuscaLTE::getTiposProcessos() ?>
                                </select>
                            </div>
                            <div class="modal-footer">
                                <button class="btn btn-primary" type="submit" style="width: 100%;"><i
                                            class="fa fa-print"></i>&nbsp;Gerar
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="modal fade" id="newTypeProcess" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                    aria-hidden="true">&times;</span>
                        </button>
                        <h4 class="modal-title">Novo Tipo de Processo</h4>
                    </div>
                    <form action="../php/geral.php" method="post">
                        <input type="hidden" name="admin" value="1"/>
                        <input type="hidden" name="form" value="newTypeProcess"/>
                        <div class="modal-body">
                            <div class="form-group">
                                <label>Nome</label>
                                <input class="form-control" id="newType" name="newType" type="text" required>
                            </div>
                            <div class="modal-footer">
                                <button class="btn btn-primary" type="submit" style="width: 100%;"><i
                                            class="fa fa-refresh"></i>&nbsp;Cadastrar
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div aria-hidden="true" class="modal fade" id="addProcesso" role="dialog" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                    aria-hidden="true">&times;</span>
                        </button>
                        <h4 class="modal-title">Processo</h4>
                    </div>
                    <form id="formProcesso">
                        <input id="id_processo" name="id_processo" type="hidden" value="0"/>
                        <input type="hidden" name="form" value="recepcao">
                        <input type="hidden" name="admin" value="1">
                        <div class="modal-body">
                            <table class="table">
                                <tbody>
                                <tr>
                                    <td>
                                        <div class="form-group">
                                            <label>Processo</label>
                                            <input class="form-control" name="num_processo" type="text"
                                                   required>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-group">
                                            <label>Tipo</label>
                                            <select class="form-control" name="tipo" required>
                                                <?= BuscaLTE::getTiposProcessos() ?>
                                            </select>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="form-group">
                                            <label>Estante</label>
                                            <input class="form-control" name="estante" type="text" required>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-group">
                                            <label>Prateleira</label>
                                            <input class="form-control" name="prateleira" type="text" required>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="form-group">
                                            <label>Entrada (dd/mm/yyyy)</label>
                                            <input class="form-control date" name="entrada" type="text" required
                                                   maxlength="10">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-group">
                                            <label>Saída (dd/mm/yyyy)</label>
                                            <input class="form-control date" name="saida" type="text" maxlength="10">
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="form-group">
                                            <label>Responsável</label>
                                            <input class="form-control" name="responsavel" type="text">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-group">
                                            <label>Retorno (dd/mm/yyyy)</label>
                                            <input class="form-control date" name="retorno" type="text"
                                                   maxlength="10">
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="form-group">
                                            <label>Observação</label>
                                            <textarea class="form-control" name="obs" rows="2"></textarea>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-group">
                                            <label>Vigência (dd/mm/yyyy)</label>
                                            <input class="form-control date" name="vigencia" type="text"
                                                   maxlength="10">
                                        </div>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-primary" type="submit" style="width: 100%;"><i
                                        class="fa fa-send"></i>&nbsp;Enviar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <?php if ($_SESSION['login'] == 'joao' || $_SESSION['login'] == 'iara'): ?>
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
                                <select class="form-control" name="user" required>
                                    <?= BuscaLTE::getUsers(); ?>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-primary" type="submit" style="width: 100%;"><i
                                        class="fa fa-refresh"></i>&nbsp;Trocar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div aria-hidden="true" class="modal fade" id="cadUser" role="dialog" tabindex="-1">
            <div class="modal-dialog" style="width: 40%;">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                    aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Adicionar Usuário</h4>
                    </div>
                    <form action="../php/geral.php" method="POST">
                        <input type="hidden" name="form" value="addUser"/>
                        <div class="modal-body">
                            <div class="form-group">
                                <label>Nome</label>
                                <input type="text" class="form-control" name="nome" placeholder="Nome" maxlength="40"
                                       required>
                            </div>
                            <div class="form-group">
                                <label>Login</label>
                                <input type="text" class="form-control" name="login" placeholder="Login" maxlength="30"
                                       required>
                            </div>
                            <div class="form-group">
                                <label>E-mail</label>
                                <input type="email" class="form-control" name="email" placeholder="E-mail"
                                       maxlength="40" required>
                            </div>
                            <div class="form-group">
                                <label>Setor</label>
                                <select class="form-control" name="setor" required>
                                    <?= BuscaLTE::getOptionsSetores(); ?>
                                </select>
                            </div>
                            <?= BuscaLTE::getCheckPermissoes(); ?>
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
        <div aria-hidden="true" class="modal fade" id="manageUsers" role="dialog" tabindex="-1">
            <div class="modal-dialog" style="width: 40%;">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                    aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Desativar Usuário</h4>
                    </div>
                    <form id="formDesativarUsuario" action="../php/geral.php" method="POST">
                        <input type="hidden" name="form" value="desativaUser"/>
                        <input type="hidden" name="admin" value="1"/>
                        <div class="modal-body">
                            <div class="form-group">
                                <label>Usuário</label>
                                <select id="usersToDisable" class="form-control" name="user" required>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-danger" type="submit" style="width: 100%;"><i
                                        class="fa fa-close"></i>&nbsp;Desativar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <?php endif; ?>
    <?php if ($_SESSION['login'] == 'joao'): ?>
        <div aria-hidden="true" class="modal fade" id="listProblemas" role="dialog" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                    aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Problemas Relatados</h4>
                    </div>
                    <div class="modal-body">
                        <table id="tableListProblemas" class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th>Setor</th>
                                <th>Assunto</th>
                                <th>Opções</th>
                            </tr>
                            </thead>
                            <tbody id="tbodyListProblemas"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <div aria-hidden="true" class="modal fade" id="loadMoreCustom" role="dialog" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Carregar Pedidos</h4>
                </div>
                <form action="javascript:loadMore();" method="POST">
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
    <div class="modal fade" id="problema" role="dialog">
        <div class="modal-dialog" style="width: 40%;">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Relatar Problema</h4>
                </div>
                <form action="../php/geral.php" method="post">
                    <input type="hidden" name="users" value="1">
                    <input type="hidden" name="form" value="problema">
                    <input type="hidden" name="pag" value="lte/">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Assunto</label>
                            <input type="text" class="form-control" placeholder="Assunto" name="assunto" required>
                        </div>
                        <div class="form-group">
                            <label>Descrição</label>
                            <textarea class="form-control" rows="3"
                                      placeholder="Tente descrever o que aconteceu, o que aparece e o que deveria aparecer. Sinta-se a vontade ;)"
                                      name="descr" required></textarea>
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
    <?php
    // no need special permissions
    include_once "util/modals-util.php";
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

<?php if ($_SESSION['login'] == 'joao' || $_SESSION['login'] == 'iara'): ?>
    <script type="text/javascript" src="js/admin.min.js"></script>
<?php endif; ?>

<?php if ($permissao->pedidos): ?>
    <script type="text/javascript" src="js/modals-pedidos.min.js"></script>
    <script type="text/javascript" src="js/body-pedidos.min.js"></script>
    <script type="text/javascript" src="js/editMode.min.js"></script>
<?php endif; ?>

<?php if ($permissao->saldos): ?>
    <script type="text/javascript" src="js/saldos.min.js"></script>
<?php endif; ?>

<?php if ($permissao->recepcao): ?>
    <script type="text/javascript" src="js/recepcao.min.js"></script>
<?php endif; ?>

<!-- page script -->
<script type="text/javascript" src="js/util_lte.min.js"></script>
<script type="text/javascript" src="../iniLTE.min.js"></script>
</body>
</html>
