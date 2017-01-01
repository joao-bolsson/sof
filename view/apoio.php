<?php
ini_set('display_erros', true);
error_reporting(E_ALL);

session_start();
if (!isset($_SESSION['id_setor']) || $_SESSION['id_setor'] != 12) {
    header('Location: ../');
}
include_once '../class/Busca.class.php';
//instanciando classe de busca para popular o select de estados
$obj_Busca = new Busca();
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

    <body class="page-brand" onload="iniSolicitacoes();">
        <header class="header header-transparent header-waterfall affix">
            <ul class="nav nav-list pull-left">
                <li>
                    <a class="btn btn-flat waves-attach waves-light" href="javascript:abreModal('#myInfos');"><span class="text-white"><span class="icon">lock_outline</span><span id="userLogado"><?= $_SESSION["nome"] ?></span></span></a>
                </li>
            </ul>
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
                                    <a class="waves-attach" href="index.php"><span style="color: white;"><span class="icon">home</span>INÍCIO</span></a>
                                </li>
                            </ul>
                        </nav>
                </div><!-- ./row -->
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
                                    <th>Opções</th>
                                    <th>#PEDIDO</th>
                                    <th>SETOR</th>
                                    <th>DATA_PEDIDO</th>
                                    <th>REF_MES</th>
                                    <th>PRIORIDADE</th>
                                    <th>STATUS</th>
                                    <th>VALOR</th>
                                    <th>EMPENHO</th>
                                    </thead>
                                    <tbody id="conteudoSolicitacoes">

                                    </tbody>
                                </table>
                            </div><!-- ./card-inner -->
                        </div><!-- ./card-main -->
                    </div> <!-- ./card -->
                </div> <!-- ./row -->
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
    <!-- js -->
    <script src="../plugins/jQuery/jquery.min.js"></script>
    <script src="../material/js/base.min.js"></script>

    <!-- js for doc -->
    <script src="../material/js/project.min.js"></script>

    <script type="text/javascript" src="../plugins/dataTables/datatables.min.js"></script>

    <script type="text/javascript" src="../ini.min.js"></script>
</body>
</html>
