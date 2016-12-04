<?php
ini_set('display_erros', true);
error_reporting(E_ALL);

session_start();
if (!isset($_SESSION["id_setor"]) || $_SESSION["id_setor"] != 2) {
    header("Location: ../");
}
include_once '../class/Busca.class.php';
//instanciando classe de busca para popular o select de estados
$obj_Busca = new Busca();
$permissao = $obj_Busca->getPermissoes($_SESSION["id"]);
?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="UTF-8">
        <meta content="IE=edge" http-equiv="X-UA-Compatible">
        <meta content="initial-scale=1.0, maximum-scale=1.0, user-scalable=no, width=device-width" name="viewport">
        <title>Setor de Orçamento e Finanças – HUSM</title>
        <!-- css -->
        <link href="../material/css/base.min.css" rel="stylesheet">

        <!-- css for doc -->
        <link href="../material/css/project.css" rel="stylesheet">
        <link href="../sof_files/estilo.min.css" rel="stylesheet">

        <link rel="stylesheet" type="text/css" href="../plugins/dataTables/datatables.min.css"/>

        <!-- favicon -->
        <link rel="icon" href="../favicon.ico">
    </head>

    <body class="page-brand" onload="iniAdminSolicitacoes();">
        <header class="header header-transparent header-waterfall affix">
            <ul class="nav nav-list pull-left">
                <?php if ($permissao->noticias): ?>
                    <li>
                        <a class="btn btn-flat waves-attach waves-light" href="index.php">
                            <span class="text-white"><span class="icon icon-lg">undo</span>VOLTAR</span>
                        </a>
                    </li>
                <?php endif; ?>
                <li>
                    <a class="btn btn-flat waves-attach waves-light" href="javascript:abreModal('#myInfos');"><span class="text-white"><span class="icon">lock_outline</span><span id="userLogado"><?= $_SESSION["nome"] ?></span></span></a>
                </li>
            </ul>
            <nav class="tab-nav pull-right hidden-xx">
                <ul class="nav nav-list">
                    <?php if ($_SESSION['login'] == 'joao'): ?>
                        <li>
                            <a class="btn btn-flat waves-attach waves-light" href="javascript:resetSystem();"><span class="text-white"><span class="icon">error</span>RESETAR</span></a>
                        </li>
                        <li>
                            <a class="btn btn-flat waves-attach waves-light" href="javascript:listProblemas();"><span class="text-white"><span class="icon">warning</span>PROBLEMAS</span></a>
                        </li>
                    <?php endif; ?>
                    <li>
                        <a class="btn btn-flat waves-attach waves-light" href="javascript:abreModal('#problema');"><span class="text-white"><span class="icon">feedback</span>RELATAR PROBLEMA</span></a>
                    </li>
                    <li>
                        <a class="btn btn-flat waves-attach waves-light" href="sair.php"><span class="text-white"><span class="icon">exit_to_app</span>SAIR</span></a>
                    </li>
                </ul>
            </nav>
        </header>
        <main class="content">
            <div class="content-heading">
                <div class="container">
                    <div class="row">
                        <h1 class="heading"><img src="../sof_files/logo_blue.png" alt="Setor de Orçamento e Finanças – HUSM" /></h1>
                        <div class="text-header">
                            <p>Setor de Orçamento e Finanças</p>
                            <span>Hospital Universitário de Santa Maria</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="container">
                <div class="row">
                    <section class="content-inner margin-top-no">
                        <nav class="tab-nav ui-tab-tab">
                            <ul class="nav nav-list">
                                <li>
                                    <a class="waves-attach" href="../view/"><span style="color: white;"><span class="icon">home</span>INÍCIO</span></a>
                                </li>
                                <li class="active">
                                    <a class="waves-attach" href="adminsolicitacoes.php"><span style="color: white;"><span class="icon">payment</span>SOLICITAÇÕES</span></a>
                                </li>
                                <?php if ($permissao->saldos): ?>
                                    <li>
                                        <a class="waves-attach" href="javascript:abreModal('#freeSaldos');"><span style="color: white;"><span class="icon">near_me</span>LIBERAR SALDOS</span></a>
                                    </li>
                                    <li>
                                        <a class="waves-attach" href="javascript:listLancamentos(0);"><span style="color: white;"><span class="icon">attach_money</span>LANÇAMENTOS DE SALDOS</span></a>
                                    </li>
                                    <li>
                                        <a class="waves-attach" href="javascript:abreModal('#transferencia');"><span style="color: white;"><span class="icon">swap_horiz</span>TRANSFERÊNCIAS</span></a>
                                    </li>
                                <?php endif; ?>
                                <?php if ($permissao->pedidos): ?>
                                    <li>
                                        <a class="waves-attach" href="javascript:abreModal('#importItens');"><span style="color: white;"><span class="icon">backup</span>IMPORTAR ITENS</span></a>
                                    </li>
                                    <li>
                                        <div class="dropdown dropdown-inline">
                                            <a class="waves-attach" data-toggle="dropdown"><span class="text-white"><span class="icon">print</span>RELATÓRIOS</span><span class="icon margin-left-sm">keyboard_arrow_down</span></a>
                                            <ul class="dropdown-menu nav">
                                                <li>
                                                    <a class="waves-attach" href="javascript:abreModal('#relPedidos');">PEDIDOS</a>
                                                </li>
                                                <li>
                                                    <a class="waves-attach" href="javascript:listRelatorios();">LISTA DE PEDIDOS</a>
                                                </li>
                                            </ul>
                                        </div>
                                    </li>
                                <?php endif; ?>
                                <?php if ($permissao->recepcao): ?>
                                    <li>
                                        <a class="waves-attach" href="javascript:listProcessos('admin');"><span class="text-white"><span class="icon">label</span>PROCESSOS</span></a>
                                    </li>
                                    <li>
                                        <a class="waves-attach" href="javascript:abreModal('#newTypeProcess');"><span class="text-white"><span class="icon">add</span>NOVO TIPO</span></a>
                                    </li>
                                    <li>
                                        <a class="waves-attach" href="javascript:abreModal('#relatorio');"><span class="text-white"><span class="icon">print</span>RELATÓRIO</span></a>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </nav>
                    </section>
                </div><!-- ./row -->
                <div class="row" style="margin-top: -5%;">
                    <table class="table">
                        <tbody>
                            <tr>
                                <?php if ($permissao->saldos): ?>
                                    <td>
                                        <a href="javascript:mostraSolicAdiant();"><span id="iconSolicAdi" class="icon">keyboard_arrow_down</span>Solicitações de Adiantamento</a>
                                    </td>
                                <?php endif; ?>
                                <?php if ($permissao->pedidos): ?>
                                    <td>
                                        <a href="javascript:mostraSolicAltPed();"><span id="iconSolicAlt" class="icon">keyboard_arrow_down</span>Solicitações de Alteração de Pedidos</a>
                                    </td>
                                    <td>
                                        <a href="javascript:mostraPed();"><span id="iconPed" class="icon">keyboard_arrow_down</span>Pedidos</a>
                                    </td>
                                <?php endif; ?>
                                <?php if (true): ?>
                                    <td id="labelSaldoSOF" style="font-weight: bold;">Saldo Disponível: R$ <?= number_format($obj_Busca->getSaldo($_SESSION['id_setor']), 3, ',', '.'); ?></td>
                                <?php endif ?>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <?php if ($permissao->recepcao): ?>
                    <div class="row">
                        <div class="card margin-top-no">
                            <div class="card-main">
                                <div class="card-header card-brand">
                                    <div class="card-header-side pull-left">
                                        <p class="card-heading">Processos</p>
                                    </div>
                                </div><!--  ./card-header -->
                                <div class="card-inner">
                                    <a href="javascript:addProcesso(' ', 0);"><span class="icon">add</span>Adicionar Processo</a>
                                    <table class="table stripe" id="tableRecepcao" style="width: 100%;">
                                        <thead>
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
                                        </thead>
                                        <tbody id="conteudoRecepcao">

                                        </tbody>
                                    </table>
                                </div><!-- ./card-inner -->
                            </div><!-- ./card-main -->
                        </div> <!-- ./card -->
                    </div>
                <?php endif; ?>
                <?php if ($permissao->saldos): ?>
                    <div id="rowSolicAdi" class="row" style="display: none;">
                        <div class="card margin-top-no">
                            <div class="card-main">
                                <div class="card-header card-brand">
                                    <div class="card-header-side pull-left">
                                        <p class="card-heading">Solicitações de Adiantamento</p>
                                    </div>
                                </div><!--  ./card-header -->
                                <div class="card-inner">
                                    <table class="table">
                                        <tr>
                                            <td>
                                                <div class="radiobtn radiobtn-adv">
                                                    <label for="stabertos">
                                                        <input type="radio" id="stabertos" name="stadi" class="access-hide" onclick="iniTableSolicAdiant();" checked value="2">Abertos
                                                        <span class="radiobtn-circle"></span><span class="radiobtn-circle-check"></span>
                                                    </label>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="radiobtn radiobtn-adv">
                                                    <label for="staprovados">
                                                        <input type="radio" id="staprovados" name="stadi" class="access-hide" onclick="iniTableSolicAdiant();" value="1">Aprovados
                                                        <span class="radiobtn-circle"></span><span class="radiobtn-circle-check"></span>
                                                    </label>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="radiobtn radiobtn-adv">
                                                    <label for="streprovado">
                                                        <input type="radio" id="streprovado" name="stadi" class="access-hide" onclick="iniTableSolicAdiant();" value="0">Reprovados
                                                        <span class="radiobtn-circle"></span><span class="radiobtn-circle-check"></span>
                                                    </label>
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                    <table class="table stripe" id="tableSolicitacoesAdiantamento" style="width: 100%;">
                                        <thead>
                                        <th></th>
                                        <th>SETOR</th>
                                        <th>DATA_SOLICITACAO</th>
                                        <th>DATA_ANALISE</th>
                                        <th>VALOR_ADIANTADO</th>
                                        <th>JUSTIFICATIVA</th>
                                        <th>STATUS</th>
                                        </thead>
                                        <tbody id="conteudoSolicitacoesAdiantamento">

                                        </tbody>
                                    </table>
                                </div><!-- ./card-inner -->
                            </div><!-- ./card-main -->
                        </div> <!-- ./card -->
                    </div>
                <?php endif; ?>
                <?php if ($permissao->pedidos): ?>
                    <div id="rowAltPed" class="row" style="display: none;">
                        <div class="card margin-top-no">
                            <div class="card-main">
                                <div class="card-header card-brand">
                                    <div class="card-header-side pull-left">
                                        <p class="card-heading">Solicitações de Alteração de Pedidos</p>
                                    </div>
                                </div><!--  ./card-header -->
                                <div class="card-inner">
                                    <table class="table">
                                        <tr>
                                            <td>
                                                <div class="radiobtn radiobtn-adv">
                                                    <label for="stAltAbertos">
                                                        <input type="radio" id="stAltAbertos" name="stAlt" class="access-hide" onclick="iniTableSolicAltPed();" value="2" checked>Abertos
                                                        <span class="radiobtn-circle"></span><span class="radiobtn-circle-check"></span>
                                                    </label>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="radiobtn radiobtn-adv">
                                                    <label for="stAltAprovados">
                                                        <input type="radio" id="stAltAprovados" name="stAlt" class="access-hide" onclick="iniTableSolicAltPed();" value="1" >Aprovados
                                                        <span class="radiobtn-circle"></span><span class="radiobtn-circle-check"></span>
                                                    </label>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="radiobtn radiobtn-adv">
                                                    <label for="stAltReprovado">
                                                        <input type="radio" id="stAltReprovado" name="stAlt" class="access-hide" onclick="iniTableSolicAltPed();" value="0">Reprovados
                                                        <span class="radiobtn-circle"></span><span class="radiobtn-circle-check"></span>
                                                    </label>
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                    <table id="tableSolicAltPedido" class="table" style="width: 100%;">
                                        <thead>
                                        <th></th>
                                        <th>NUM_PEDIDO</th>
                                        <th>SETOR</th>
                                        <th>DATA_SOLICITACAO</th>
                                        <th>DATA_ANALISE</th>
                                        <th>JUSTIFICATIVA</th>
                                        <th>STATUS</th>
                                        </thead>
                                        <tbody id="contSolicAltPedido"></tbody>
                                    </table>
                                </div><!-- ./card-inner -->
                            </div><!-- ./card-main -->
                        </div> <!-- ./card -->
                    </div> <!-- ./row -->

                    <!-- TABELA COM OS PEDIDOS ENVIADOS AO SOF -->

                    <div id="rowPedidos" class="row">
                        <div id="card" class="card margin-top-no">
                            <div class="card-main">
                                <div class="card-header card-brand">
                                    <div class="card-header-side pull-left">
                                        <p class="card-heading">Pedidos</p>
                                    </div>
                                </div><!--  ./card-header -->
                                <div class="card-inner">
                                    <table class="table stripe" id="tableSolicitacoes" style="width: 100%;">
                                        <thead>
                                        <th></th>
                                        <th>#PEDIDO</th>
                                        <th>SETOR</th>
                                        <th>DATA_PEDIDO</th>
                                        <th>REF_MES</th>
                                        <th>PRIORIDADE</th>
                                        <th>STATUS</th>
                                        <th>VALOR</th>
                                        <th>EMPENHO</th>
                                        </thead>
                                        <tbody id="conteudoSolicitacoes"></tbody>
                                    </table>
                                </div><!-- ./card-inner -->
                            </div><!-- ./card-main -->
                        </div> <!-- ./card -->
                    </div> <!-- ./row -->
                    <div id="rowDetPedido" class="row" style="display: none;">
                        <div class="card margin-top-no">
                            <div class="card-main">
                                <div class="card-header card-brand">
                                    <div class="card-header-side pull-left">
                                        <p class="card-heading">Detalhes do Pedido</p>
                                    </div>
                                    <div class="card-header-side pull-right" style="margin-left: 55%; display: none;">
                                        <p class="card-heading">SALDO DO SOLICITANTE <span id="text_saldo_total">R$ 0.000</span></p>
                                    </div>
                                </div><!--  ./card-header -->
                                <form id="formPedido" action="../php/geral.php" method="POST">
                                    <input type="hidden" name="admin" value="1"></input>
                                    <input id="form" type="hidden" name="form" value="" required></input>
                                    <input id="id_pedido" type="hidden" name="id_pedido" value="0"></input>
                                    <input id="id_setor" type="hidden" name="id_setor" value="0"></input>
                                    <div class="card-inner">
                                        <table class="table stripe" id="tableItensPedido">
                                            <thead>
                                            <th>Ferramentas</th>
                                            <th>COD_REDUZIDO</th>
                                            <th>COD_DESPESA</th>
                                            <th>DESCRICAO_DESPESA</th>
                                            <th>NUM_CONTRATO</th>
                                            <th>NUM_PROCESSO</th>
                                            <th>DESCR_MOD_COMPRA</th>
                                            <th>NUM_LICITACAO</th>
                                            <th>DT_INICIO</th>
                                            <th>DT_FIM</th>
                                            <th>DT_GERACAO</th>
                                            <th>CGC_FORNECEDOR</th>
                                            <th>NOME_FORNECEDOR</th>
                                            <th>NUM_EXTRATO</th>
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
                                            </thead>
                                            <tbody id="conteudoPedido"></tbody>
                                        </table>
                                        <div id="divObs" class="form-group form-group-label">
                                            <label class="floating-label" for="obs"><span class="icon">announcement</span>&nbsp;Observações</label>
                                            <textarea class="form-control textarea-autosize" id="obs" name="obs" disabled></textarea>
                                        </div>
                                        <input id="total_hidden" type="hidden" name="total_hidden" value="0">
                                        <input id="saldo_total" type="hidden" name="saldo_total" value="0.000">
                                        <input id="prioridade" type="hidden" name="prioridade" value="0">
                                        <table class="table">
                                            <?= $obj_Busca->getStatus(4) ?>
                                        </table>
                                        <div class="form-group form-group-label">
                                            <label class="floating-label" for="comentario"><span class="icon">announcement</span>&nbsp;Comentário</label>
                                            <textarea class="form-control textarea-autosize" id="comentario" name="comentario" rows="1"></textarea>
                                        </div>
                                    </div><!-- ./card-inner -->
                                    <div class="card-action">
                                        <div class="card-action-btn">
                                            <button id="btnLimpa" class="btn btn-default waves-attach" type="button" style="width: 49%;" onclick="limpaTela();"><span class="icon">undo</span>&nbsp;Limpar / Esconder</button>
                                            <button class="btn btn-brand waves-attach" type="submit" style="width: 50%;"><span class="icon">check</span>&nbsp;Salvar Alterações</button>
                                        </div>
                                    </div>
                                </form>
                            </div><!-- ./card-main -->
                        </div> <!-- ./card -->
                    </div> <!-- ./row -->
                <?php endif; ?>
            </div> <!-- ./container -->
        </main>
        <footer class="footer">
            <div class="col-md-4 col-sm-6">
                <div class="container" style="text-align: center; margin-left: 100px;">
                    <p>
                        Hospital Universitário de Santa Maria<br>
                        HUSM - UFSM - EBSERH<br>
                        Endereço: Av. Roraima, 1000, Prédio 22<br>
                        Bairro Camobi, Santa Maria - RS<br>
                        CEP 97105-900
                    </p>
                </div>
            </div>
            <div class="col-md-4 col-sm-6">
                <div class="conteiner">
                    <div id="info-img" class="margin-bottom" style="text-align: center;">
                        <a href="http://www.husm.ufsm.br/"><img src="../sof_files/logo husm.png" rel="" title=""></a>
                        <a href="http://www.ebserh.gov.br/"><img src="../sof_files/logo ebserh.png" rel="" title=""></a>
                        <a href="http://site.ufsm.br/"><img src="../sof_files/logo ufsm.png" rel="" title=""></a>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-sm-6">
                <div class="container" style="text-align: center; margin-right: 100px;">
                    <p>
                        Telefone: (55) 3213 - 1610<br>
                        E-mail: orcamentofinancashusm@gmail.com<br>
                        Horário de Atend.: 07h às 12h e 13h às 17h
                    </p>
                </div>
            </div>
        </footer>
        <div aria-hidden="true" class="modal fade" id="listProcessos" role="dialog" tabindex="-1">
            <div class="modal-dialog" style="width: 40%;">
                <div class="modal-content">
                    <div class="modal-heading">
                        <a class="modal-close" data-dismiss="modal">×</a>
                        <h2 class="modal-title content-sub-heading">Processos do Pedido</h2>
                    </div>
                    <div class="modal-inner">
                        <table id="tableListProcessos" class="table" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th>Número do Processo</th>
                                    <th>Data Fim</th>
                                </tr>
                            </thead>
                            <tbody id="tbodyListProcessos"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div aria-hidden="true" class="modal fade" id="listRelatorios" role="dialog" tabindex="-1">
            <div class="modal-dialog" style="width: 80%;">
                <div class="modal-content">
                    <div class="modal-heading">
                        <a class="modal-close" data-dismiss="modal">×</a>
                        <h2 class="modal-title content-sub-heading">Relatórios</h2>
                    </div>
                    <div class="modal-inner">
                        <table style="width: 100%;">
                            <?= $obj_Busca->getRadiosStatusRel(); ?>
                        </table>
                        <p id="relTotRow" style="display: none;"></p>
                        <table id="tableListRelatorios" class="table" style="width: 100%;">
                            <thead>
                            <th>Pedido</th>
                            <th>RefMes</th>
                            <th>Data de Envio</th>
                            <th>Prioridade</th>
                            <th>Status</th>
                            <th>SIAFI</th>
                            <th>Valor</th>
                            <th>Opções</th>
                            </thead>
                            <tbody id="tbodyListRelatorios"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div aria-hidden="true" class="modal fade" id="listProblemas" role="dialog" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-heading">
                        <a class="modal-close" data-dismiss="modal">×</a>
                        <h2 class="modal-title content-sub-heading">Problemas Relatados</h2>
                    </div>
                    <div class="modal-inner">
                        <table id="tableListProblemas" class="table" style="width: 100%;">
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
        <div class="modal fade" id="problema" role="dialog">
            <div class="modal-dialog" style="width: 40%;">
                <div class="modal-content">
                    <div class="modal-heading">
                        <a class="modal-close" data-dismiss="modal">×</a>
                        <h2 class="modal-title content-sub-heading">Relatar um problema</h2>
                    </div>
                    <form action="../php/geral.php" method="post">
                        <input type="hidden" name="users" value="1">
                        <input type="hidden" name="form" value="problema">
                        <input type="hidden" name="pag" value="admin/adminsolicitacoes.php">
                        <div class="modal-inner">
                            <span class="label">Tente descrever o que aconteceu, o que aparece e o que deveria aparecer. Sinta-se a vontade ;)</span>
                            <div class="form-group form-group-label">
                                <label class="floating-label" for="assunto"><span class="icon">perm_identity</span>&nbsp;Assunto</label>
                                <input class="form-control" id="assunto" name="assunto" type="text" required>
                            </div>
                            <div class="form-group form-group-label">
                                <label class="floating-label" for="descr_problema"><span class="icon">announcement</span>&nbsp;Descrição</label>
                                <textarea class="form-control textarea-autosize" id="descr_problema" name="descr" rows="1" required></textarea>
                            </div>
                        </div>
                        <div class="modal-footer margin-bottom">
                            <button class="btn btn-brand waves-attach waves-light waves-effect" type="submit" style="width: 100%;"><span class="icon">send</span>&nbsp;Enviar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <?php if ($permissao->pedidos): ?>
            <div aria-hidden="true" class="modal fade" id="relPedidos" role="dialog" tabindex="-1">
                <div class="modal-dialog" style="width: 40%;">
                    <div class="modal-content">
                        <div class="modal-heading">
                            <a class="modal-close" data-dismiss="modal">×</a>
                            <h2 class="modal-title content-sub-heading">Relatório de Pedidos</h2>
                        </div>
                        <div class="modal-inner">
                            <form action="printRelatorio.php" method="post" target="_blank">
                                <input type="hidden" name="tipo" value="pedidos" />
                                <input type="hidden" name="relatorio" value="1" />
                                <div class="modal-inner">
                                    <div class="form-group form-group-label">
                                        <label class="floating-label" for="setorRelPed"><span class="icon">perm_identity</span>&nbsp;Setor</label>
                                        <select id="setorRelPed" class="form-control" name="setor" required>
                                            <option value="0">Todos</option>
                                            <?= $obj_Busca->getOptionsSetores(); ?>
                                        </select>
                                    </div>
                                    <div class="form-group form-group-label">
                                        <label class="floating-label" for="prioridadeRelPed"><span class="icon">perm_identity</span>&nbsp;Prioridade</label>
                                        <select id="prioridadeRelPed" class="form-control" name="prioridade" required>
                                            <option value="0">Todas</option>
                                            <?= $obj_Busca->getOptionsPrioridades(); ?>
                                        </select>
                                    </div>
                                    <div class="form-group form-group-label">
                                        <label class="floating-label" for="stRelPed"><span class="icon">perm_identity</span>&nbsp;Status</label>
                                        <select id="stRelPed" class="form-control" name="status" required>
                                            <option value="0">Todos</option>
                                            <?= $obj_Busca->getOptionsStatus(); ?>
                                        </select>
                                    </div>
                                    <div class="form-group form-group-label">
                                        <label class="floating-label" for="dataI"><span class="icon">date_range</span>&nbsp;Data Início</label>
                                        <input class="form-control date" id="dataI" name="dataI" type="text" required>
                                    </div>
                                    <div class="form-group form-group-label">
                                        <label class="floating-label" for="dataF"><span class="icon">date_range</span>&nbsp;Data Fim</label>
                                        <input class="form-control date" id="dataF" name="dataF" type="text">
                                    </div>
                                </div>
                                <div class="modal-footer margin-bottom">
                                    <button class="btn btn-brand waves-attach waves-light waves-effect" type="submit" style="width: 100%;"><span class="icon">autorenew</span>&nbsp;Gerar</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="infoItem" role="dialog">
                <div class="modal-dialog" style="width: 50%;">
                    <div class="modal-content">
                        <div class="modal-heading">
                            <a class="modal-close" data-dismiss="modal">×</a>
                            <h2 class="modal-title content-sub-heading">Informações do Item</h2>
                        </div>
                        <form id="formEditItem" action="javascript:submitEditItem();" method="post">
                            <input id="idItem" type="hidden" name="idItem" value="0"/>
                            <div class="modal-inner">
                                <table width="100%;">
                                    <tr>
                                        <td colspan="3">
                                            <div class="form-group form-group-label">
                                                <label class="floating-label" for="compItem"><span class="icon">announcement</span>&nbsp;Complemento do Item</label>
                                                <textarea class="form-control textarea-autosize" id="compItem" name="complemento" required rows="5"></textarea>
                                            </div>
                                        </td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="form-group form-group-label">
                                                <label class="floating-label" for="vlUnitario"><span class="icon">label</span>&nbsp;Valor Unitário</label>
                                                <input class="form-control" id="vlUnitario" name="vl_unitario" type="number" step="0.001" required>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-group form-group-label">
                                                <label class="floating-label" for="qtContrato"><span class="icon">label</span>&nbsp;Quantidade Contrato</label>
                                                <input class="form-control" id="qtContrato" name="qt_contrato" type="number" required>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-group form-group-label">
                                                <label class="floating-label" for="vlContrato"><span class="icon">label</span>&nbsp;Valor Contrato</label>
                                                <input class="form-control" id="vlContrato" name="vl_contrato" type="number" step="0.001" required>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="form-group form-group-label">
                                                <label class="floating-label" for="qtUtilizada"><span class="icon">label</span>&nbsp;Quantidade Utilizada</label>
                                                <input class="form-control" id="qtUtilizada" name="qt_utilizada" type="number" required>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-group form-group-label">
                                                <label class="floating-label" for="vlUtilizado"><span class="icon">label</span>&nbsp;Valor Utilizado</label>
                                                <input class="form-control" id="vlUtilizado" name="vl_utilizado" type="number" step="0.001" required>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-group form-group-label">
                                                <label class="floating-label" for="qtSaldo"><span class="icon">label</span>&nbsp;Quantidade Saldo</label>
                                                <input class="form-control" id="qtSaldo" name="qt_saldo" type="number" required>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="3">
                                            <div class="form-group form-group-label">
                                                <label class="floating-label" for="vlSaldo"><span class="icon">label</span>&nbsp;Valor Saldo</label>
                                                <input class="form-control" id="vlSaldo" name="vl_saldo" type="number" step="0.001" required>
                                            </div>
                                        </td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                </table>
                            </div>
                            <div class="modal-footer margin-bottom">
                                <button class="btn btn-brand waves-attach waves-light waves-effect" type="submit" style="width: 100%;"><span class="icon">autorenew</span>&nbsp;Atualizar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div aria-hidden="true" class="modal fade" id="cadEmpenho" role="dialog" tabindex="-1">
                <div class="modal-dialog" style="width: 40%;">
                    <div class="modal-content">
                        <div class="modal-heading">
                            <a class="modal-close" data-dismiss="modal">×</a>
                            <h2 class="modal-title content-sub-heading">Cadastrar Empenho</h2>
                        </div>
                        <form action="javascript:enviaEmpenho();" method="POST">
                            <div class="modal-inner">
                                <div id="div-lb-high" class="form-group form-group-label">
                                    <label class="floating-label" for="id_pedido_emp"><span class="icon">label</span>&nbsp;Pedido</label>
                                    <input class="form-control" id="id_pedido_emp" name="id_pedido_emp" type="number" required disabled>
                                </div>
                                <div class="form-group form-group-label">
                                    <label class="floating-label" for="empenho"><span class="icon">announcement</span>&nbsp;Empenho</label>
                                    <input class="form-control" id="empenho" name="empenho" required />
                                </div>
                                <div class="form-group form-group-label">
                                    <label class="floating-label" for="dataEmp"><span class="icon">date_range</span>&nbsp;Data</label>
                                    <input class="form-control date" id="dataEmp" name="dataEmp" required />
                                </div>
                            </div>
                            <div class="modal-footer margin-bottom">
                                <button class="btn btn-brand waves-attach waves-light waves-effect" type="submit" style="width: 100%;"><span class="icon">send</span>&nbsp;Cadastrar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div aria-hidden="true" class="modal fade" id="cadFontes" role="dialog" tabindex="-1">
                <div class="modal-dialog" style="width: 40%;">
                    <div class="modal-content">
                        <div class="modal-heading">
                            <a class="modal-close" data-dismiss="modal">×</a>
                            <h2 class="modal-title content-sub-heading">Cadastrar Fontes</h2>
                        </div>
                        <form action="javascript:enviaFontes();" method="POST">
                            <div class="modal-inner">
                                <div id="div-lbl-high" class="form-group form-group-label">
                                    <label class="floating-label" for="id_pedido_fonte"><span class="icon">label</span>&nbsp;Pedido</label>
                                    <input class="form-control" id="id_pedido_fonte" name="id_pedido_fonte" type="number" required disabled>
                                </div>
                                <div class="form-group form-group-label">
                                    <label class="floating-label" for="fonte"><span class="icon">label</span>&nbsp;Fonte de Recurso</label>
                                    <input class="form-control" id="fonte" name="fonte" required />
                                </div>
                                <div class="form-group form-group-label">
                                    <label class="floating-label" for="ptres"><span class="icon">label</span>&nbsp;PTRES</label>
                                    <input class="form-control" id="ptres" name="ptres" required />
                                </div>
                                <div class="form-group form-group-label">
                                    <label class="floating-label" for="plano"><span class="icon">label</span>&nbsp;Plano Interno</label>
                                    <input class="form-control" id="plano" name="plano" required />
                                </div>
                            </div>
                            <div class="modal-footer margin-bottom">
                                <button class="btn btn-brand waves-attach waves-light waves-effect" type="submit" style="width: 100%;"><span class="icon">send</span>&nbsp;Cadastrar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="importItens" role="dialog">
                <div class="modal-dialog" style="width: 40%;">
                    <div class="modal-content">
                        <div class="modal-heading">
                            <a class="modal-close" data-dismiss="modal">×</a>
                            <h2 class="modal-title content-sub-heading">Importar Itens</h2>
                        </div>
                        <form enctype="multipart/form-data" action="../php/geral.php" method="post">
                            <input type="hidden" name="admin" value="1">
                            <input type="hidden" name="form" value="importItens">
                            <div class="modal-inner">
                                <div class="form-group form-group-label">
                                    <label class="floating-label" for="file"><span class="icon">insert_drive_file</span>&nbsp;Arquivo</label>
                                    <input id="file" class="form-control" type="file" style="text-transform: none !important;" name="file">
                                </div>
                                <p class="help-block">Tamanho máximo: 3 MB</p>
                                <div id="loaderImport" class="progress-circular" style="margin-left: 45%; display: none;">
                                    <div class="progress-circular-wrapper">
                                        <div class="progress-circular-inner">
                                            <div class="progress-circular-left">
                                                <div class="progress-circular-spinner"></div>
                                            </div>
                                            <div class="progress-circular-gap"></div>
                                            <div class="progress-circular-right">
                                                <div class="progress-circular-spinner"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer margin-bottom">
                                <button class="btn btn-brand waves-attach waves-light waves-effect" type="submit" style="width: 100%;"><span class="icon">backup</span>&nbsp;Enviar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        <?php if ($permissao->saldos): ?>
            <div aria-hidden="true" class="modal fade" id="transferencia" role="dialog" tabindex="-1">
                <div class="modal-dialog" style="width: 40%;">
                    <div class="modal-content">
                        <div class="modal-heading">
                            <a class="modal-close" data-dismiss="modal">×</a>
                            <h2 class="modal-title content-sub-heading">Nova Transferência</h2>
                        </div>
                        <div class="modal-inner">
                            <form id="formTransferencia" action="javascript:transfereSaldo();" method="post">
                                <div class="modal-inner">
                                    <div class="form-group form-group-label">
                                        <label class="floating-label" for="setorOri"><span class="icon">perm_identity</span>&nbsp;Setor Origem</label>
                                        <select id="setorOri" class="form-control" name="setorOri" required onchange="getSaldoOri();">
                                            <?= $obj_Busca->getOptionsSetores(); ?>
                                        </select>
                                        <p id="saldoDispOri" style="font-weight: bold;"></p>
                                    </div>
                                    <div class="form-group form-group-label">
                                        <label class="floating-label" for="setorDest"><span class="icon">perm_identity</span>&nbsp;Setor Destino</label>
                                        <select id="setorDest" class="form-control" name="setorDest" required>
                                            <?= $obj_Busca->getOptionsSetores(); ?>
                                        </select>
                                    </div>
                                    <div class="form-group form-group-label">
                                        <label class="floating-label" for="valorTransf"><span class="icon">attach_money</span>&nbsp;Valor</label>
                                        <input class="form-control" id="valorTransf" name="valor" type="number" step="0.001"required min="0.000" max="0.000">
                                    </div>
                                    <div class="form-group form-group-label">
                                        <label class="floating-label" for="justTransf"><span class="icon">announcement</span>&nbsp;Justificativa</label>
                                        <textarea class="form-control textarea-autosize" id="justTransf" name="obs" rows="1" required></textarea>
                                    </div>
                                    <div id="loadingTransf" class="progress-circular" style="margin-left: 45%; display: none;">
                                        <div class="progress-circular-wrapper">
                                            <div class="progress-circular-inner">
                                                <div class="progress-circular-left">
                                                    <div class="progress-circular-spinner"></div>
                                                </div>
                                                <div class="progress-circular-gap"></div>
                                                <div class="progress-circular-right">
                                                    <div class="progress-circular-spinner"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer margin-bottom">
                                    <button class="btn btn-brand waves-attach waves-light waves-effect" type="submit" style="width: 100%;"><span class="icon">autorenew</span>&nbsp;Liberar</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div aria-hidden="true" class="modal fade" id="freeSaldos" role="dialog" tabindex="-1">
                <div class="modal-dialog" style="width: 40%;">
                    <div class="modal-content">
                        <div class="modal-heading">
                            <a class="modal-close" data-dismiss="modal">×</a>
                            <h2 class="modal-title content-sub-heading">Liberar Saldo</h2>
                        </div>
                        <div class="modal-inner">
                            <form action="javascript:liberaSaldo();" method="post">
                                <div class="modal-inner">
                                    <div class="form-group form-group-label">
                                        <label class="floating-label" for="setor"><span class="icon">perm_identity</span>&nbsp;Setor</label>
                                        <select id="setor" class="form-control" name="setor" required>
                                            <?= $obj_Busca->getOptionsSetores(); ?>
                                        </select>
                                    </div>
                                    <div class="form-group form-group-label">
                                        <label class="floating-label" for="valorFree"><span class="icon">attach_money</span>&nbsp;Valor</label>
                                        <input class="form-control" id="valorFree" name="valor" type="number" step="0.001"required min="0.000">
                                    </div>
                                    <div id="loadingFree" class="progress-circular" style="margin-left: 45%; display: none;">
                                        <div class="progress-circular-wrapper">
                                            <div class="progress-circular-inner">
                                                <div class="progress-circular-left">
                                                    <div class="progress-circular-spinner"></div>
                                                </div>
                                                <div class="progress-circular-gap"></div>
                                                <div class="progress-circular-right">
                                                    <div class="progress-circular-spinner"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer margin-bottom">
                                    <button class="btn btn-brand waves-attach waves-light waves-effect" type="submit" style="width: 100%;"><span class="icon">autorenew</span>&nbsp;Liberar</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div aria-hidden="true" class="modal fade" id="listLancamentos" role="dialog" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-heading">
                            <a class="modal-close" data-dismiss="modal">×</a>
                            <h2 class="modal-title content-sub-heading">Lançamentos de Saldos</h2>
                        </div>
                        <div class="modal-inner">
                            <table id="tableListLancamentos" class="table" style="width: 100%;">
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
        <?php if ($permissao->recepcao): ?>
            <div aria-hidden="true" class="modal fade" id="listProcessos" role="dialog" tabindex="-1">
                <div class="modal-dialog" style="width: 40%;">
                    <div class="modal-content">
                        <div class="modal-heading">
                            <a class="modal-close" data-dismiss="modal">×</a>
                            <h2 class="modal-title content-sub-heading">Processos Atendidos pelo SOF</h2>
                        </div>
                        <div class="modal-inner">
                            <table id="tableListProcessos" class="table" style="width: 100%;">
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
            <div aria-hidden="true" class="modal fade" id="addProcesso" role="dialog" tabindex="-1">
                <div class="modal-dialog" style="width: 50%;">
                    <div class="modal-content">
                        <div class="modal-heading">
                            <a class="modal-close" data-dismiss="modal">×</a>
                            <h2 class="modal-title content-sub-heading">Processo</h2>
                        </div>
                        <form id="formProcesso" action="javascript:updateProcesso();" method="post">
                            <input id="id_processo" type="hidden" value="0"></input>
                            <div class="modal-inner">
                                <table style="width: 100%;">
                                    <tbody>
                                        <tr>
                                            <td>
                                                <div id="divNumProc" class="form-group form-group-label">
                                                    <label class="floating-label" for="num_processo"><span class="icon">label</span>&nbsp;Processo</label>
                                                    <input class="form-control" id="num_processo" name="num_processo" type="text" required>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-group form-group-label">
                                                    <label class="floating-label" for="tipo"><span class="icon">lock_outline</span>&nbsp;Tipo</label>
                                                    <select id="tipo" class="form-control" name="tipo" required>
                                                        <?= $obj_Busca->getTiposProcessos() ?>
                                                    </select>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="form-group form-group-label">
                                                    <label class="floating-label" for="estante"><span class="icon">line_weight</span>&nbsp;Estante</label>
                                                    <input class="form-control" id="estante" name="estante" type="text" required>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-group form-group-label">
                                                    <label class="floating-label" for="prateleira"><span class="icon">list</span>&nbsp;Prateleira</label>
                                                    <input class="form-control" id="prateleira" name="prateleira" type="text" required>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="form-group form-group-label">
                                                    <label class="floating-label" for="entrada"><span class="icon">date_range</span>&nbsp;Entrada (dd/mm/yyyy)</label>
                                                    <input class="form-control" id="entrada" name="entrada" type="text" required maxlength="10">
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-group form-group-label">
                                                    <label class="floating-label" for="saida"><span class="icon">alarm</span>&nbsp;Saída (dd/mm/yyyy)</label>
                                                    <input class="form-control" id="saida" name="saida" type="text" maxlength="10">
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="form-group form-group-label">
                                                    <label class="floating-label" for="responsavel"><span class="icon">perm_identity</span>&nbsp;Responsável</label>
                                                    <input class="form-control" id="responsavel" name="responsavel" type="text">
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-group form-group-label">
                                                    <label class="floating-label" for="retorno"><span class="icon">date_range</span>&nbsp;Retorno (dd/mm/yyyy)</label>
                                                    <input class="form-control" id="retorno" name="retorno" type="text" maxlength="10">
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="2">
                                                <div class="form-group form-group-label">
                                                    <label class="floating-label" for="obs"><span class="icon">announcement</span>&nbsp;Observação</label>
                                                    <textarea class="form-control textarea-autosize" id="obs" name="obs" rows="1" required></textarea>
                                                </div>
                                            </td>
                                            <td></td>
                                        </tr>
                                    </tbody>
                                </table>
                                <div id="loading" class="progress-circular" style="margin-left: 45%; display: none;">
                                    <div class="progress-circular-wrapper">
                                        <div class="progress-circular-inner">
                                            <div class="progress-circular-left">
                                                <div class="progress-circular-spinner"></div>
                                            </div>
                                            <div class="progress-circular-gap"></div>
                                            <div class="progress-circular-right">
                                                <div class="progress-circular-spinner"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer margin-bottom">
                                <button class="btn btn-brand waves-attach waves-light waves-effect" type="submit" style="width: 100%;"><span class="icon">send</span>&nbsp;Enviar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="newTypeProcess" role="dialog">
                <div class="modal-dialog" style="width: 40%;">
                    <div class="modal-content">
                        <div class="modal-heading">
                            <a class="modal-close" data-dismiss="modal">×</a>
                            <h2 class="modal-title content-sub-heading">Novo Tipo de Processo</h2>
                        </div>
                        <form action="../php/geral.php" method="post">
                            <input type="hidden" name="admin" value="1"></input>
                            <input type="hidden" name="form" value="newTypeProcess"></input>
                            <div class="modal-inner">
                                <div class="form-group form-group-label">
                                    <label class="floating-label" for="newType"><span class="icon">perm_identity</span>&nbsp;Nome</label>
                                    <input class="form-control" id="newType" name="newType" type="text" required>
                                </div>
                                <div class="modal-footer margin-bottom">
                                    <button class="btn btn-brand waves-attach waves-light waves-effect" type="submit" style="width: 100%;"><span class="icon">autorenew</span>&nbsp;Cadastrar</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="relatorio" role="dialog">
                <div class="modal-dialog" style="width: 40%;">
                    <div class="modal-content">
                        <div class="modal-heading">
                            <a class="modal-close" data-dismiss="modal">×</a>
                            <h2 class="modal-title content-sub-heading">Gerar Relatório</h2>
                        </div>
                        <form action="javascript:print();" method="post">
                            <div class="modal-inner">
                                <div class="form-group form-group-label">
                                    <label class="floating-label" for="type"><span class="icon">label</span>&nbsp;Tipo</label>
                                    <select id="type" class="form-control" name="type" required>
                                        <option value="0">Todos</option>
                                        <?= $obj_Busca->getTiposProcessos() ?>
                                    </select>
                                </div>
                                <div class="modal-footer margin-bottom">
                                    <button class="btn btn-brand waves-attach waves-light waves-effect" type="submit" style="width: 100%;"><span class="icon">print</span>&nbsp;Gerar</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        <div class="modal fade" id="myInfos" role="dialog">
            <div class="modal-dialog" style="width: 40%;">
                <div class="modal-content">
                    <div class="modal-heading">
                        <a class="modal-close" data-dismiss="modal">×</a>
                        <h2 class="modal-title content-sub-heading">Informações do Usuário</h2>
                    </div>
                    <form id="altInfo" action="javascript:altInfoUser();" method="post">
                        <div class="modal-inner">
                            <div class="form-group form-group-label">
                                <label class="floating-label" for="nameUser"><span class="icon">perm_identity</span>&nbsp;Nome</label>
                                <input class="form-control" id="nameUser" name="nameUser" type="text" value="<?= $_SESSION['nome'] ?>" required>
                            </div>
                            <div class="form-group form-group-label">
                                <label class="floating-label" for="emailUser"><span class="icon">message</span>&nbsp;E-mail</label>
                                <input class="form-control" id="emailUser" name="emailUser" type="email" value="<?= $_SESSION['email'] ?>" required>
                            </div>
                            <div class="form-group form-group-label">
                                <label class="floating-label" for="senhaAtualUser"><span class="icon">lock_outline</span>&nbsp;Senha Atual</label>
                                <input class="form-control" id="senhaAtualUser" name="senhaAtualUser" type="password" required>
                            </div>
                            <div class="form-group form-group-label">
                                <label class="floating-label" for="senhaUser"><span class="icon">lock_outline</span>&nbsp;Nova Senha</label>
                                <input class="form-control" id="senhaUser" name="senhaUser" type="password" required>
                            </div>
                            <div id="loader" class="progress-circular" style="margin-left: 45%; display: none;">
                                <div class="progress-circular-wrapper">
                                    <div class="progress-circular-inner">
                                        <div class="progress-circular-left">
                                            <div class="progress-circular-spinner"></div>
                                        </div>
                                        <div class="progress-circular-gap"></div>
                                        <div class="progress-circular-right">
                                            <div class="progress-circular-spinner"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer margin-bottom">
                            <button class="btn btn-brand waves-attach waves-light waves-effect" type="submit" style="width: 100%;"><span class="icon">autorenew</span>&nbsp;Atualizar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div aria-hidden="true" class="modal modal-va-middle fade" id="viewCompl" role="dialog" tabindex="-1">
            <div class="modal-dialog modal-xs">
                <div class="modal-content">
                    <div class="modal-heading">
                        <a class="modal-close" data-dismiss="modal">×</a>
                    </div>
                    <div class="modal-inner" id="complementoItem">
                    </div>
                    <div class="modal-footer margin-bottom">
                        <p class="text-right"><a class="btn waves-attach waves-effect" data-dismiss="modal">OK</a></p>
                    </div>
                </div>
            </div>
        </div>
        <!-- js -->
        <script src="../plugins/jQuery/jquery.min.js"></script>
        <script src="../plugins/jQuery/jquery.mask.min.js"></script>
        <script src="../material/js/base.min.js"></script>

        <!-- js for doc -->
        <script src="../material/js/project.min.js"></script>

        <script type="text/javascript" src="../plugins/dataTables/datatables.min.js"></script>

        <script type="text/javascript" src="../ini.min.js"></script>
    </body>
</html>
