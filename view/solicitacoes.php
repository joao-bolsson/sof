<?php
session_start();
ini_set('display_erros', true);
error_reporting(E_ALL);

require_once '../defines.php';
if (!isset($_SESSION["id"])) {
	header("Location: ../");
}
include_once '../class/Busca.class.php';
//instanciando classe de busca para popular o select de estados
$obj_Busca = new Busca();
$obj_Saldo = $obj_Busca->getSaldoSetor($_SESSION["id_setor"]);

$saldo_total = $obj_Saldo->saldo;
$saldo_suplementado = $obj_Saldo->saldo_suplementado;
$saldo_aditivado = $obj_Saldo->saldo_aditivado;

$saldo_mes_anterior = $obj_Busca->getSaldoMesAnterior($_SESSION["id_setor"]);
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta content="IE=edge" http-equiv="X-UA-Compatible">
    <meta content="initial-scale=1.0, maximum-scale=1.0, user-scalable=no, width=device-width" name="viewport">
    <title>Setor de Orçamento e Finanças – HUSM</title>
    <link href="../plugins/animate/animate.min.css" rel="stylesheet">
    <!-- css -->
    <link href="../material/css/base.min.css" rel="stylesheet">

    <!-- css for doc -->
    <link href="../material/css/project.min.css" rel="stylesheet">
    <link href="../sof_files/estilo.min.css" rel="stylesheet">

    <link rel="stylesheet" type="text/css" href="../plugins/dataTables/datatables.min.css"/>

    <!-- favicon -->
    <link rel="icon" href="../favicon.ico">
    <head>
        <body class="page-brand" onload="iniPagSolicitacoes();">
            <header class="header header-transparent header-waterfall affix">
                <nav class="tab-nav pull-left hidden-xx">
                    <ul class="nav nav-list">
                        <li>
                            <a class="btn btn-flat waves-attach waves-light" href="javascript:abreModal('#myInfos');"><span class="text-white"><span class="icon">lock_outline</span><span id="userLogado"><?php echo $_SESSION["nome"] ?></span></span></a>
                        </li>
                    </ul>
                </nav>
                <nav class="tab-nav pull-right hidden-xx">
                    <ul class="nav nav-list">
                        <li>
                            <a class="btn btn-flat waves-attach waves-light" href="../admin/sair.php"><span class="text-white"><span class="icon">exit_to_app</span>SAIR</span></a>
                        </li>
                    </ul>
                </nav>
            </header>
            <main class="content">
                <div class="content-heading">
                    <div class="container">
                        <div class="row">
                            <h1 class="heading module wow slideInRight animated"><img src="../sof_files/logo_blue.png" alt="Setor de Orçamento e Finanças – HUSM" /></h1>
                            <div class="text-header module wow slideInLeft animated">
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
                                        <a class="waves-attach" href="javascript:abreModal('#listRascunhos');"><span class="text-white"><span class="icon">drafts</span>RASCUNHOS</span></a>
                                    </li>
                                    <li>
                                        <a class="waves-attach" href="javascript:abreModal('#listMeusPedidos');"><span class="text-white"><span class="icon">description</span>MEUS PEDIDOS</span></a>
                                    </li>
                                    <li>
                                        <a class="waves-attach" href="javascript:abreModal('#saldos');"><span class="text-white"><span class="icon">attach_money</span>SALDOS</span></a>
                                    </li>
                                    <li>
                                        <a class="waves-attach" href="javascript:abreModal('#listMeusAdiantamentos');"><span class="text-white"><span class="icon">money_off</span>MEUS ADIANTAMENTOS</span></a>
                                    </li>
                                    <li>
                                        <a class="waves-attach" href="javascript:abreModal('#listSolicAltPedidos');"><span class="text-white"><span class="icon">autorenew</span>SOLIC ALTERAÇÕES PEDIDOS</span></a>
                                    </li>
                                    <li>
                                        <a class="waves-attach" href="javascript:abreModal('#listProcessos');"><span class="text-white"><span class="icon">label</span>PROCESSOS</span></a>
                                    </li>
                                </ul>
                            </nav>
                        </div><!-- ./row -->
                        <div class="row">
                            <div id="card" class="card margin-top-no">
                                <div class="card-main">
                                    <div class="card-header card-brand">
                                        <div class="card-header-side pull-left">
                                            <p class="card-heading">Solicitações</p>
                                        </div>
                                    </div><!--  ./card-header -->
                                    <div id="groupNumProc" class="form-group form-group-label">
                                        <label class="floating-label padding-left" for="searchProcesso" style="font-size: 14pt;"><span class="icon">search</span>&nbsp;Digite o número do processo</label>
                                        <input class="form-control padding-left" id="searchProcesso" name="search" type="text" style="font-size: 14pt;">
                                    </div>
                                    <button class="btn waves-attach" type="button" onclick="pesquisarProcesso(0);" style="width: 100%;"><span class="icon" style="font-weight: bold;">search</span></button>
                                    <div class="card-inner">
                                        <table class="table" id="tableProcessos">
                                            <thead>
                                                <th></th>
                                                <th>NOME_FORNECEDOR</th>
                                                <th>COD_REDUZIDO</th>
                                                <th>QT_SOLICITADA</th>
                                                <th>COMPLEMENTO_ITEM</th>
                                                <th style="display: none;"></th>
                                                <th>VL_UNITARIO</th>
                                                <th>QT_SALDO</th>
                                                <th>QT_UTILIZADO</th>
                                                <th>VL_SALDO</th>
                                                <th>VL_UTILIZADO</th>
                                                <th>QT_CONTRATO</th>
                                            </thead>
                                            <tbody id="conteudoProcesso">

                                            </tbody>
                                        </table>
                                    </div><!-- ./card-inner -->
                                </div><!-- ./card-main -->
                            </div> <!-- ./card -->
                        </div>
                        <div class="row">
                            <div class="card margin-top-no">
                                <div class="card-main">
                                    <div class="card-header card-brand">
                                        <div class="card-header-side pull-left">
                                            <p class="card-heading">Pedido</p>
                                        </div>
                                        <div class="card-header-side pull-right" style="margin-left: 70%;">
                                            <p class="card-heading">SALDO <span id="text_saldo_total">R$ <?php echo $saldo_total ?><span></p>
                                        </div>
                                    </div><!--  ./card-header -->
                                    <form action="../php/geral.php" method="POST">
                                        <input type="hidden" name="users" value="1"></input>
                                        <input type="hidden" name="form" value="pedido"></input>
                                        <input id="pedido" type="hidden" name="pedido" value="0"></input>
                                        <div class="card-inner">
                                            <table class="table">
                                                <thead>
                                                    <th></th>
                                                    <th>COD_REDUZIDO</th>
                                                    <th>COMPLEMENTO_ITEM</th>
                                                    <th>VL_UNITARIO</th>
                                                    <th>NOME_FORNECEDOR</th>
                                                    <th>NUM_LICITACAO</th>
                                                    <th>QT_SOLICITADA</th>
                                                    <th>VALOR</th>
                                                </thead>
                                                <tbody id="conteudoPedido">

                                                </tbody>
                                            </table>
                                            <table class="table">
                                                <tr>
                                                    <td>
                                                        <div class="form-group form-group-label control-highlight">
                                                            <label class="floating-label padding-left" for="total" style="font-size: 14pt;"><span class="icon">attach_money</span>&nbsp;Total</label>
                                                            <input class="form-control padding-left" id="total" name="total" style="font-size: 14pt;" type="text" disabled value="R$ 0">
                                                        </div>
                                                    </td>
                                                </tr>
                                                <input id="total_hidden" type="hidden" name="total_hidden" value="0">
                                                <input id="saldo_total" type="hidden" name="saldo_total" value="<?php echo $saldo_total ?>">
                                            </table>
                                            <div class="form-group">
                                                <table class="table">
                                                    <tr>
                                                        <?php echo $obj_Busca->getPrioridades(); ?>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div><!-- ./card-inner -->
                                        <div class="card-action">
                                            <div class="card-action-btn">
                                                <button class="btn btn-brand waves-attach" type="submit" style="width: 100%;"><span class="icon">check</span>&nbsp;Enviar Pedido / Salvar Rascunho</button>
                                            </div>
                                        </div>
                                    </form>
                                </div><!-- ./card-main -->
                            </div> <!-- ./card -->
                        </div>
                    </section>
                </div>
            </div>
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
                                <input class="form-control" id="nameUser" name="nameUser" type="text" value="<?php echo $_SESSION['nome'] ?>" required>
                            </div>
                            <div class="form-group form-group-label">
                                <label class="floating-label" for="emailUser"><span class="icon">message</span>&nbsp;E-mail</label>
                                <input class="form-control" id="emailUser" name="emailUser" type="email" value="<?php echo $_SESSION['email'] ?>" required>
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
        <div aria-hidden="true" class="modal fade" id="listMeusAdiantamentos" role="dialog" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-heading">
                        <a class="modal-close" data-dismiss="modal">×</a>
                        <h2 class="modal-title content-sub-heading">Solicitações de Adiantamentos</h2>
                    </div>
                    <div class="modal-inner">
                        <table class="table" id="tableSolicAdi" style="width: 100%;">
                            <thead style="width: 100%;">
                                <th>DATA_SOLICITACAO</th>
                                <th>DATA_ANALISE</th>
                                <th>MES_SUBTRAIDO</th>
                                <th>VALOR_ADIANTADO</th>
                                <th>JUSTIFICATIVA</th>
                                <th>STATUS</th>
                            </thead>
                            <tbody id="contSolicAdi">

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div aria-hidden="true" class="modal fade" id="listMeusPedidos" role="dialog" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-heading">
                        <a class="modal-close" data-dismiss="modal">×</a>
                        <h2 class="modal-title content-sub-heading">Meus Pedidos</h2>
                    </div>
                    <div id="noticias-editar" class="modal-inner">
                        <table id="tableMeusPedidos" class="table" style="width: 100%;">
                            <thead>
                                <th>RefMes</th>
                                <th>Data de Envio</th>
                                <th>Prioridade</th>
                                <th>Status</th>
                                <th>Valor</th>
                                <th>Opções</th>
                            </thead>
                            <tbody>
                                <?php echo $obj_Busca->getMeusPedidos($_SESSION["id_setor"]); ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
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
                            <tbody>
                                <?php echo $obj_Busca->getProcessos("solicitacoes"); ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div aria-hidden="true" class="modal fade" id="alt_pedido" role="dialog" tabindex="-1">
            <div class="modal-dialog" style="width: 40%;">
                <div class="modal-content">
                    <div class="modal-heading">
                        <a class="modal-close" data-dismiss="modal">×</a>
                        <h2 class="modal-title content-sub-heading">Solicitar Alteração de Pedido</h2>
                    </div>
                    <form action="javascript:formEnvia();" method="POST">
                        <div class="modal-inner">
                            <div id="div-lb-high" class="form-group form-group-label">
                                <label class="floating-label" for="id_pedido_alt"><span class="icon">label</span>&nbsp;Pedido</label>
                                <input class="form-control" id="id_pedido_alt" name="id_pedido_alt" type="number" required disabled>
                            </div>
                            <div class="form-group form-group-label">
                                <label class="floating-label" for="justificativa_alt_ped"><span class="icon">announcement</span>&nbsp;Justificativa</label>
                                <textarea class="form-control textarea-autosize" id="justificativa_alt_ped" name="justificativa_alt_ped" rows="1" required></textarea>
                            </div>
                        </div>
                        <div class="modal-footer margin-bottom">
                            <button class="btn btn-brand waves-attach waves-light waves-effect" type="submit" style="width: 100%;"><span class="icon">send</span>&nbsp;Enviar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div aria-hidden="true" class="modal fade" id="listSolicAltPedidos" role="dialog" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-heading">
                        <a class="modal-close" data-dismiss="modal">×</a>
                        <h2 class="modal-title content-sub-heading">Solicitações de Alteração de Pedidos</h2>
                    </div>
                    <div id="noticias-editar" class="modal-inner">
                        <table id="tableSolicAltPedido" class="table" style="width: 100%;">
                            <thead>
                                <th>#</th>
                                <th>DATA_SOLICITACAO</th>
                                <th>DATA_ANALISE</th>
                                <th>JUSTIFICATIVA</th>
                                <th>STATUS</th>
                            </thead>
                            <tbody>
                                <?php echo $obj_Busca->getSolicAltPedidos($_SESSION["id_setor"]); ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div aria-hidden="true" class="modal fade" id="listRascunhos" role="dialog" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-heading">
                        <a class="modal-close" data-dismiss="modal">×</a>
                        <h2 class="modal-title content-sub-heading">Rascunhos</h2>
                    </div>
                    <div id="noticias-editar" class="modal-inner">
                        <table id="tableRascunhos" class="table" style="width: 100%;">
                            <thead>
                                <th>RefMes</th>
                                <th>Última modificação</th>
                                <th>Valor</th>
                                <th>Editar</th>
                            </thead>
                            <tbody>
                                <?php echo $obj_Busca->getRascunhos($_SESSION["id_setor"]); ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div aria-hidden="true" class="modal fade" id="saldos" role="dialog" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-heading">
                        <a class="modal-close" data-dismiss="modal">×</a>
                        <h2 class="modal-title content-sub-heading">Saldos</h2>
                    </div>
                    <div id="noticias-editar" class="modal-inner">
                        <table class="table">
                            <tr>
                                <td>Saldo Restante do Mês Anterior</td>
                                <td>R$ <?php echo $saldo_mes_anterior ?></td>
                            </tr>
                            <tr>
                                <td>Saldo Suplementado</td>
                                <td>R$ <?php echo $saldo_suplementado ?></td>
                            </tr>
                            <tr>
                                <td>Saldo Aditivado</td>
                                <td>R$ <?php echo $saldo_aditivado ?></td>
                            </tr>
                            <tr>
                                <td>Saldo Disponível</td>
                                <td>R$ <?php echo $saldo_total ?></td>
                            </tr>
                        </table>
                    </div>
                    <div class="modal-footer margin-bottom">
                        <button class="btn btn-brand waves-attach waves-light waves-effect" type="button" onclick="abreModal('#adiantamento');" style="width: 100%;"><span class="icon">money_off</span>&nbsp;Solicitar Adiantamento</button>
                    </div>
                </div>
            </div>
        </div>
        <div aria-hidden="true" class="modal fade" id="adiantamento" role="dialog" tabindex="-1">
            <div class="modal-dialog" style="width: 40%;">
                <div class="modal-content">
                    <div class="modal-heading">
                        <a class="modal-close" data-dismiss="modal">×</a>
                        <h2 class="modal-title content-sub-heading">Solicitar Adiantamento</h2>
                    </div>
                    <form action="../php/geral.php" method="POST">
                        <input type="hidden" name="form" value="adiantamento" />
                        <input type="hidden" name="users" value="1" />
                        <div class="modal-inner">
                            <div class="form-group form-group-label">
                                <label class="floating-label" for="valor_adiantamento"><span class="icon">attach_money</span>&nbsp;Valor</label>
                                <input class="form-control" id="valor_adiantamento" name="valor_adiantamento" type="number" step="0.001" min="0.001" max="2000.000" required>
                            </div>
                            <div class="form-group form-group-label">
                                <label class="floating-label" for="justificativa"><span class="icon">announcement</span>&nbsp;Justificativa</label>
                                <textarea class="form-control textarea-autosize" id="justificativa" name="justificativa" rows="1" required></textarea>
                            </div>
                        </div>
                        <div class="modal-footer margin-bottom">
                            <button class="btn btn-brand waves-attach waves-light waves-effect" type="submit" style="width: 100%;"><span class="icon">send</span>&nbsp;Enviar</button>
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
        <script src="../material/js/base.min.js"></script>

        <!-- js for doc -->
        <script src="../material/js/project.min.js"></script>

        <script type="text/javascript" src="../plugins/dataTables/datatables.min.js"></script>

        <script type="text/javascript" src="../ini.min.js"></script>
    </body>
    </html>
