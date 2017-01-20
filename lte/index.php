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

$count = $obj_Busca->getCountSolic();
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
                            <p id="userLogadop"><?= $_SESSION["nome"] ?></p>
                        </div>
                    </div>
                    <!-- sidebar menu: : style can be found in sidebar.less -->
                    <ul class="sidebar-menu">
                        <?php if ($permissao->saldos): ?>
                            <li>
                                <a href="javascript:mostraSolicAdiant();">
                                    <i class="fa fa-credit-card"></i> <span>Solic Adiantamento</span>
                                    <?php if ($count->solic_adi > 0): ?>
                                        <span class="pull-right-container">
                                            <small class="label pull-right bg-blue"><?= $count->solic_adi; ?></small>
                                        </span>
                                    <?php endif; ?>
                                </a>
                            </li>
                        <?php endif; ?>
                        <?php if ($permissao->pedidos): ?>
                            <li>
                                <a href="javascript:mostraSolicAltPed();">
                                    <i class="fa fa-cog"></i> <span>Solic Alt Pedidos</span>
                                    <?php if ($count->solic_alt > 0): ?>
                                        <span class="pull-right-container">
                                            <small class="label pull-right bg-red"><?= $count->solic_alt; ?></small>
                                        </span>
                                    <?php endif; ?>
                                </a>
                            </li>
                            <li>
                                <a href="javascript:mostraPed();">
                                    <i class="fa fa-tags"></i> <span>Pedidos</span>
                                    <?php if ($count->solic_ped > 0): ?>
                                        <span class="pull-right-container">
                                            <small class="label pull-right bg-blue"><?= $count->solic_ped; ?></small>
                                        </span>
                                    <?php endif; ?>
                                </a>
                            </li>
                        <?php endif; ?>
                        <?php if ($_SESSION['login'] == 'joao' || $_SESSION['login'] == 'iara'): ?>
                            <li>
                                <a href="javascript:abreModal('#altUser');">
                                    <i class="fa fa-user"></i> <span>Alterar Usuário</span>
                                </a>
                            </li>
                            <li>
                                <a href="javascript:abreModal('#cadUser');">
                                    <i class="fa fa-user-plus"></i> <span>Adicionar Usuário</span>
                                </a>
                            </li>
                        <?php endif; ?>
                        <?php if ($_SESSION['login'] == 'joao'): ?>
                            <li>
                                <a href="javascript:resetSystem();">
                                    <i class="fa fa-exclamation-circle"></i> <span>Resetar</span>
                                </a>
                            </li>
                            <li>
                                <a href="javascript:listProblemas();">
                                    <i class="fa fa-warning"></i> <span>Problemas</span>
                                </a>
                            </li>
                        <?php endif; ?>
                        <li>
                            <a href="javascript:abreModal('#problema');">
                                <i class="fa fa-comments"></i> <span>Relatar Problema</span>
                            </a>
                        </li>
                        <?php if ($permissao->saldos): ?>
                            <li>
                                <a href="javascript:abreModal('#freeSaldos');">
                                    <i class="fa fa-send"></i> <span>Liberar Saldo</span>
                                </a>
                            </li>
                            <li>
                                <a href="javascript:listLancamentos();">
                                    <i class="fa fa-dollar"></i> <span>Liberações Orçamentárias</span>
                                </a>
                            </li>
                            <li>
                                <a href="javascript:abreModal('#transferencia');">
                                    <i class="fa fa-arrows-h"></i> <span>Transferências</span>
                                </a>
                            </li>
                        <?php endif; ?>
                        <?php if ($permissao->pedidos): ?>
                            <li>
                                <a href="javascript:abreModal('#importItens');">
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
                                    <li><a href="javascript:abreModal('#relPedidos');"><i class="fa fa-circle-o"></i> Pedidos</a></li>
                                    <li><a href="javascript:listRelatorios();"><i class="fa fa-circle-o"></i> Lista de Pedidos</a></li>
                                </ul>
                            </li>
                        <?php endif; ?>
                        <?php if ($permissao->recepcao): ?>
                            <li>
                                <a href="javascript:listProcessos('admin');">
                                    <i class="fa fa-archive"></i> <span>Processos</span>
                                </a>
                            </li>
                            <li>
                                <a href="javascript:abreModal('#newTypeProcess');">
                                    <i class="fa fa-plus"></i> <span>Novo Tipo</span>
                                </a>
                            </li>
                            <li>
                                <a href="javascript:abreModal('#relatorio');">
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
                    <?php if ($permissao->recepcao): ?>
                        <div class="row">
                            <div class="col-xs-12">
                                <div class="box">
                                    <div class="box-header">
                                        <h3 class="box-title">Processos</h3>
                                        <div class="box-tools pull-right">
                                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                                            </button>
                                        </div>
                                    </div><!-- /.box-header -->
                                    <div class="box-body">
                                        <a href="javascript:addProcesso(' ', 0);"><i class="fa fa-plus"></i>Adicionar Processo</a>
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
                                    </div><!-- ./box-boxy -->
                                </div><!-- ./box -->
                            </div> <!-- ./col-xs-12 -->
                        </div><!-- ./row -->
                    <?php endif; ?>
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
                                                            <input id="stabertos" type="radio" name="stadi" class="minimal" value="2" checked/>
                                                            Abertos
                                                        </label>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-group">
                                                        <label>
                                                            <input id="staprovados" type="radio" name="stadi" class="minimal" value="1"/>
                                                            Aprovados
                                                        </label>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-group">
                                                        <label>
                                                            <input id="streprovado" type="radio" name="stadi" class="minimal" value="0"/>
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
                    <?php if ($permissao->pedidos): ?>
                        <div id="rowAltPed" class="row" style="display: none;">
                            <div class="col-xs-12">
                                <div class="box">
                                    <div class="box-header">
                                        <h3 class="box-title">Solicitações de Alteração de Pedido</h3>
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
                                                            <input id="stAltAbertos" type="radio" name="stAlt" class="minimal" value="2" checked/>
                                                            Abertos
                                                        </label>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-group">
                                                        <label>
                                                            <input id="stAltAprovados" type="radio" name="stAlt" class="minimal" value="1"/>
                                                            Aprovados
                                                        </label>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-group">
                                                        <label>
                                                            <input id="stAltReprovado" type="radio" name="stAlt" class="minimal" value="0"/>
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
                                                    <th>Fornecedor</th>
                                                </tr>
                                            </thead>
                                            <tbody id="conteudoSolicitacoes"></tbody>
                                        </table>
                                    </div><!-- /.box-body -->
                                </div><!-- /.box -->
                            </div><!-- /.col -->
                        </div><!-- /.row -->
                        <div id="rowDetPedido" class="row" style="display: none;">
                            <div class="col-xs-12">
                                <div class="box">
                                    <div class="box-header">
                                        <h3 class="box-title">Detalhes do Pedido: <span id="detPedId"></span>&nbsp;SETOR: <span id="nomeSetorDet"></span>&nbsp;SALDO DO SOLICITANTE <span id="text_saldo_total">R$ 0.000</span></h3>
                                        <div class="box-tools pull-right">
                                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
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
                                                <?= $obj_Busca->getStatus(4) ?>
                                            </table>
                                            <div class="form-group">
                                                <label>Comentário</label>
                                                <textarea class="form-control" rows="2" id="comentario" name="comentario"></textarea>
                                            </div>
                                            <small class="label bg-gray">Essa opção só irá ser considerada se o pedido for marcado como Reprovado</small>
                                            <div class="form-group">
                                                <label>
                                                    <input id="checkExcluir" type="checkbox" class="minimal" name="excluir">
                                                    Excluir pedido
                                                </label>
                                            </div>
                                        </div><!-- ./box-body -->
                                        <div class="box-footer">
                                            <div class="btn-group" style="width: 100%;">
                                                <button id="btnLimpa" class="btn btn-default" type="button" style="width: 49%;" onclick="limpaTela();"><i class="fa fa-undo"></i>&nbsp;Limpar / Esconder</button>
                                                <button class="btn btn-primary" type="submit" style="width: 50%;"><i class="fa fa-check"></i>&nbsp;Salvar Alterações</button>
                                            </div>
                                        </div>
                                    </form>
                                </div><!-- ./box -->
                            </div> <!-- ./col-xs-12 -->
                        </div> <!-- ./row -->
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
            <?php if ($permissao->recepcao): ?>
                <div class="modal fade" id="relatorio" role="dialog">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title">Gerar Relatório</h4>
                            </div>
                            <form action="javascript:print();" method="post">
                                <div class="modal-body">
                                    <div class="form-group">
                                        <label>Tipo</label>
                                        <select id="type" class="form-control" name="type" required>
                                            <option value="0">Todos</option>
                                            <?= $obj_Busca->getTiposProcessos() ?>
                                        </select>
                                    </div>
                                    <div class="modal-footer">
                                        <button class="btn btn-primary" type="submit" style="width: 100%;"><i class="fa fa-print"></i>&nbsp;Gerar</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div aria-hidden="true" class="modal fade" id="listProcessos" role="dialog" tabindex="-1">
                    <div class="modal-dialog" style="width: 60%;">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
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
                <div class="modal fade" id="newTypeProcess" role="dialog">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
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
                                        <button class="btn btn-primary" type="submit" style="width: 100%;"><i class="fa fa-refresh"></i>&nbsp;Cadastrar</button>
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
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title">Processo</h4>
                            </div>
                            <form id="formProcesso" action="javascript:updateProcesso();" method="post">
                                <input id="id_processo" type="hidden" value="0"/>
                                <div class="modal-body">
                                    <table class="table">
                                        <tbody>
                                            <tr>
                                                <td>
                                                    <div class="form-group">
                                                        <label>Processo</label>
                                                        <input class="form-control" id="num_processo" name="num_processo" type="text" required>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-group">
                                                        <label>Tipo</label>
                                                        <select id="tipo" class="form-control" name="tipo" required>
                                                            <?= $obj_Busca->getTiposProcessos() ?>
                                                        </select>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <div class="form-group">
                                                        <label>Estante</label>
                                                        <input class="form-control" id="estante" name="estante" type="text" required>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-group">
                                                        <label>Prateleira</label>
                                                        <input class="form-control" id="prateleira" name="prateleira" type="text" required>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <div class="form-group">
                                                        <label>Entrada (dd/mm/yyyy)</label>
                                                        <input class="form-control date" id="entrada" name="entrada" type="text" required maxlength="10">
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-group">
                                                        <label>Saída (dd/mm/yyyy)</label>
                                                        <input class="form-control date" id="saida" name="saida" type="text" maxlength="10">
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <div class="form-group">
                                                        <label>Responsável</label>
                                                        <input class="form-control" id="responsavel" name="responsavel" type="text">
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-group">
                                                        <label>Retorno (dd/mm/yyyy)</label>
                                                        <input class="form-control date" id="retorno" name="retorno" type="text" maxlength="10">
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="2">
                                                    <div class="form-group">
                                                        <label>Observação</label>
                                                        <textarea class="form-control" id="obs" name="obs" rows="2" required></textarea>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="modal-footer">
                                    <button class="btn btn-primary" type="submit" style="width: 100%;"><i class="fa fa-send"></i>&nbsp;Enviar</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            <div id="viewCompl" class="modal fade" tabindex="-1" role="dialog" data-backdrop="static">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title">Informação</h4>
                        </div>
                        <div id="complementoItem" class="modal-body"></div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary" data-dismiss="modal">Fechar</button>
                        </div>
                    </div><!-- /.modal-content -->
                </div><!-- /.modal-dialog -->
            </div><!-- /.modal -->
            <?php if ($_SESSION['login'] == 'joao' || $_SESSION['login'] == 'iara'): ?>
                <div aria-hidden="true" class="modal fade" id="altUser" role="dialog" tabindex="-1">
                    <div class="modal-dialog" style="width: 40%;">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title" >Alterar Usuário</h4>
                            </div>
                            <form action="../php/geral.php" method="POST">
                                <input type="hidden" name="form" value="altUser"/>
                                <input type="hidden" name="admin" value="1"/>
                                <div class="modal-body">
                                    <div class="form-group">
                                        <label>Usuário</label>
                                        <select id="userA" class="form-control" name="user" required>
                                            <?= $obj_Busca->getUsers(); ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button class="btn btn-primary" type="submit" style="width: 100%;"><i class="fa fa-refresh"></i>&nbsp;Trocar</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            <?php if ($_SESSION['login'] == 'joao' || $_SESSION['login'] == 'iara') : ?>
                <div aria-hidden="true" class="modal fade" id="cadUser" role="dialog" tabindex="-1">
                    <div class="modal-dialog" style="width: 40%;">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title" >Adicionar Usuário</h4>
                            </div>
                            <form action="../php/geral.php" method="POST">
                                <input type="hidden" name="form" value="addUser"/>
                                <input type="hidden" name="admin" value="1"/>
                                <div class="modal-body">
                                    <div class="form-group">
                                        <label for="nomeU">Nome</label>
                                        <input type="text" class="form-control" id="nomeU" name="nome" placeholder="Nome" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="loginU">Login</label>
                                        <input type="text" class="form-control" id="loginU" name="login" placeholder="Login" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="emailU">E-mail</label>
                                        <input type="email" class="form-control" id="emailU" name="email" placeholder="E-mail" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Setor</label>
                                        <select id="userA" class="form-control" name="setor" required>
                                            <?= $obj_Busca->getOptionsSetores(); ?>
                                        </select>
                                    </div>
                                    <?= $obj_Busca->getCheckPermissoes(); ?>
                                </div>
                                <div class="modal-footer">
                                    <button class="btn btn-primary" type="submit" style="width: 100%;"><i class="fa fa-send"></i>&nbsp;Cadastrar</button>
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
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title" >Problemas Relatados</h4>
                            </div>
                            <div class="modal-body">
                                <table id="tableListProblemas" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Setor</th>
                                            <th>Assunto</th>
                                            <th>Descrição</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbodyListProblemas"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            <div class="modal fade" id="problema" role="dialog">
                <div class="modal-dialog" style="width: 40%;">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" >Relatar Problema</h4>
                        </div>
                        <form action="../php/geral.php" method="post">
                            <input type="hidden" name="users" value="1">
                            <input type="hidden" name="form" value="problema">
                            <input type="hidden" name="pag" value="admin/adminsolicitacoes.php">
                            <div class="modal-body">
                                <div class="form-group">
                                    <label>Assunto</label>
                                    <input type="text" class="form-control" placeholder="Assunto" name="assunto" required>
                                </div>
                                <div class="form-group">
                                    <label>Descrição</label>
                                    <textarea class="form-control" rows="3" placeholder="Tente descrever o que aconteceu, o que aparece e o que deveria aparecer. Sinta-se a vontade ;)" name="descr" required></textarea>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button class="btn btn-primary" type="submit" style="width: 100%;"><i class="fa fa-send"></i>&nbsp;Enviar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <?php if ($permissao->pedidos): ?>
                <div aria-hidden="true" class="modal fade" id="cadEmpenho" role="dialog" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title">Cadastrar Empenho</h4>
                            </div>
                            <form action="javascript:enviaEmpenho();" method="POST">
                                <div class="modal-body">
                                    <div class="form-group">
                                        <label>Pedido</label>
                                        <input class="form-control" id="id_pedido_emp" name="id_pedido_emp" type="number" required disabled>
                                    </div>
                                    <div class="form-group">
                                        <label>Empenho</label>
                                        <input class="form-control" id="empenho" name="empenho" required />
                                    </div>
                                    <div class="form-group">
                                        <label>Data</label>
                                        <input class="form-control date" id="dataEmp" name="dataEmp" required />
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button class="btn btn-primary" type="submit" style="width: 100%;"><i class="fa fa-send"></i>Cadastrar</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div aria-hidden="true" class="modal fade" id="cadFontes" role="dialog" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title">Cadastrar Fontes</h4>
                            </div>
                            <form action="javascript:enviaFontes();" method="POST">
                                <div class="modal-body">
                                    <div class="form-group">
                                        <label>Pedido</label>
                                        <input class="form-control" id="id_pedido_fonte" name="id_pedido_fonte" type="number" required disabled>
                                    </div>
                                    <div class="form-group">
                                        <label>Fonte de Recurso</label>
                                        <input class="form-control" id="fonte" name="fonte" required />
                                    </div>
                                    <div class="form-group">
                                        <label>PTRES</label>
                                        <input class="form-control" id="ptres" name="ptres" required />
                                    </div>
                                    <div class="form-group">
                                        <label>Plano Interno</label>
                                        <input class="form-control" id="plano" name="plano" required />
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button class="btn btn-primary" type="submit" style="width: 100%;"><i class="fa fa-send"></i>&nbsp;Cadastrar</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div aria-hidden="true" class="modal fade" id="listRelatorios" role="dialog" tabindex="-1">
                    <div class="modal-dialog" style="width: 80%;">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title">Relatórios</h4>
                            </div>
                            <div class="modal-body">
                                <table style="width: 100%;">
                                    <?= $obj_Busca->getRadiosStatusRel(); ?>
                                </table>
                                <p id="relTotRow" style="display: none;"></p>
                                <table id="tableListRelatorios" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Pedido</th>
                                            <th>RefMes</th>
                                            <th>Data de Envio</th>
                                            <th>Prioridade</th>
                                            <th>Status</th>
                                            <th>SIAFI</th>
                                            <th>Valor</th>
                                            <th>Opções</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbodyListRelatorios"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div aria-hidden="true" class="modal fade" id="relPedidos" role="dialog" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title">Relatório de Pedidos</h4>
                            </div>
                            <form action="../admin/printRelatorio.php" method="post" target="_blank">
                                <input type="hidden" name="tipo" value="pedidos" />
                                <input type="hidden" name="relatorio" value="1" />
                                <div class="modal-body">
                                    <div class="form-group">
                                        <label>Setor</label>
                                        <select class="form-control" name="setor" required>
                                            <option value="0">Todos</option>
                                            <?= $obj_Busca->getOptionsSetores(); ?>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Prioridade</label>
                                        <select class="form-control" name="prioridade" required>
                                            <option value="0">Todas</option>
                                            <?= $obj_Busca->getOptionsPrioridades(); ?>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Status</label>
                                        <select class="form-control" name="status" required>
                                            <option value="0">Todos</option>
                                            <?= $obj_Busca->getOptionsStatus(); ?>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Data Início</label>
                                        <div class="input-group">
                                            <div class="input-group-addon">
                                                <i class="fa fa-calendar"></i>
                                            </div>
                                            <input type="text" class="form-control date" name="dataI" required data-inputmask="'alias': 'dd/mm/yyyy'" data-mask>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label>Data Fim</label>
                                        <div class="input-group">
                                            <div class="input-group-addon">
                                                <i class="fa fa-calendar"></i>
                                            </div>
                                            <input type="text" class="form-control date" name="dataF" required data-inputmask="'alias': 'dd/mm/yyyy'" data-mask>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button class="btn btn-primary" type="submit" style="width: 100%;"><i class="fa fa-refresh"></i>&nbsp;Gerar</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="modal fade" id="importItens" role="dialog">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title">Importar Itens</h4>
                            </div>
                            <form enctype="multipart/form-data" action="../php/geral.php" method="post">
                                <input type="hidden" name="admin" value="1">
                                <input type="hidden" name="form" value="importItens">
                                <div class="modal-body">
                                    <div class="form-group">
                                        <label for="file">Arquivo</label>
                                        <input type="file" id="file" name="file" required>

                                        <p class="help-block">Tamanho máximo: 2 MB</p>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button class="btn btn-primary" type="submit" style="width: 100%;"><i class="fa fa-cloud-upload"></i>&nbsp;Enviar</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="modal fade" id="infoItem" role="dialog">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title">Informações do Item</h4>
                            </div>
                            <form id="formEditItem" action="javascript:submitEditItem();" method="post">
                                <input id="idItem" type="hidden" name="idItem" value="0"/>
                                <div class="modal-body">
                                    <table class="table">
                                        <tr>
                                            <td colspan="3">
                                                <div class="form-group">
                                                    <label>Complemento do Item</label>
                                                    <textarea class="form-control" id="compItem" name="complemento" required rows="5"></textarea>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="form-group">
                                                    <label>Valor Unitário</label>
                                                    <input class="form-control" id="vlUnitario" name="vl_unitario" type="number" step="0.001" required>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-group">
                                                    <label>Quantidade Contrato</label>
                                                    <input class="form-control" id="qtContrato" name="qt_contrato" type="number" required>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-group">
                                                    <label>Valor Contrato</label>
                                                    <input class="form-control" id="vlContrato" name="vl_contrato" type="number" step="0.001" required>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="form-group">
                                                    <label>Quantidade Utilizada</label>
                                                    <input class="form-control" id="qtUtilizada" name="qt_utilizada" type="number" required>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-group">
                                                    <label>Valor Utilizado</label>
                                                    <input class="form-control" id="vlUtilizado" name="vl_utilizado" type="number" step="0.001" required>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-group">
                                                    <label>Quantidade Saldo</label>
                                                    <input class="form-control" id="qtSaldo" name="qt_saldo" type="number" required>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="3">
                                                <div class="form-group">
                                                    <label>Valor Saldo</label>
                                                    <input class="form-control" id="vlSaldo" name="vl_saldo" type="number" step="0.001" required>
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="modal-footer">
                                    <button class="btn btn-primary" type="submit" style="width: 100%;"><i class="fa fa-refresh"></i>&nbsp;Atualizar</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            <?php if ($permissao->saldos): ?>
                <div aria-hidden="true" class="modal fade" id="transferencia" role="dialog" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title">Nova Transferência</h4>
                            </div>
                            <form id="formTransferencia" action="javascript:transfereSaldo();" method="post">
                                <div class="modal-body">
                                    <div class="form-group">
                                        <label>Setor Origem</label>
                                        <select id="setorOri" class="form-control" name="setorOri" required onchange="getSaldoOri();">
                                            <?= $obj_Busca->getOptionsSetores(); ?>
                                        </select>
                                        <p id="saldoDispOri" style="font-weight: bold;"></p>
                                    </div>
                                    <div class="form-group">
                                        <label>Setor Destino</label>
                                        <select id="setorDest" class="form-control" name="setorDest" required>
                                            <?= $obj_Busca->getOptionsSetores(); ?>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Valor</label>
                                        <input class="form-control" id="valorTransf" name="valor" type="number" step="0.001" required min="0.001">
                                    </div>
                                    <div class="form-group">
                                        <label>Justificativa</label>
                                        <textarea class="form-control" id="justTransf" name="obs" rows="3" placeholder="Motivo da transferência" required></textarea>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button class="btn btn-primary" type="submit" style="width: 100%;"><i class="fa fa-refresh"></i>&nbsp;Liberar</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div aria-hidden="true" class="modal fade" id="freeSaldos" role="dialog" tabindex="-1">
                    <div class="modal-dialog" style="width: 40%;">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title" >Liberar Saldo</h4>
                            </div>
                            <div class="modal-body">
                                <form action="javascript:liberaSaldo();" method="post">
                                    <div class="modal-inner">
                                        <div class="form-group">
                                            <label>Setor</label>
                                            <select id="setor" class="form-control" name="setor" required>
                                                <?= $obj_Busca->getOptionsSetores(); ?>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Valor</label>
                                            <input id="valorFree" type="number" class="form-control" placeholder="Valor" name="valor" step="0.001" required min="0.001">
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button class="btn btn-primary" type="submit" style="width: 100%;"><i class="fa fa-refresh"></i>&nbsp;Liberar</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <div aria-hidden="true" class="modal fade" id="listLancamentos" role="dialog" tabindex="-1">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title" >Liberarações Orçamentárias</h4>
                            </div>
                            <div class="modal-body">
                                <div class="form-group form-group-label">
                                    <label>Setor</label>
                                    <select id="selectSetor" class="form-control" onchange="changeSetor();">
                                        <option value="-1">Nenhum</option>
                                        <option value="0">Todos</option>
                                        <?= $obj_Busca->getOptionsSetores(); ?>
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
                                            <th>Setor</th>
                                            <th>Data</th>
                                            <th>Valor</th>
                                            <th>Categoria</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbodyListLancamentos"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
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
        <!-- InputMask -->
        <script src="plugins/input-mask/jquery.inputmask.js"></script>
        <script src="plugins/input-mask/jquery.inputmask.date.extensions.js"></script>
        <script src="plugins/input-mask/jquery.inputmask.extensions.js"></script>
        <!-- page script -->
        <script type="text/javascript" src="../iniLTE.min.js"></script>
    </body>
</html>
