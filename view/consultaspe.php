<?php
session_start();

ini_set('display_erros', true);
error_reporting(E_ALL);

spl_autoload_register(function (string $class_name) {
    include_once '../class/' . $class_name . '.class.php';
});

$controller = new Controller();
//instanciando classe de busca para popular o select de estados
$obj_Busca = new Busca();
?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="UTF-8">
        <meta content="IE=edge" http-equiv="X-UA-Compatible">
        <meta content="initial-scale=1.0, maximum-scale=1.0, user-scalable=no, width=device-width" name="viewport">
        <title>Público Externo | Setor de Orçamento e Finanças – HUSM</title>
        <link href="../plugins/animate/animate.min.css" rel="stylesheet">
        <!-- css -->
        <link href="../material/css/base.min.css" rel="stylesheet">

        <!-- css for doc -->
        <link href="../material/css/project.min.css" rel="stylesheet">
        <link href="../sof_files/estilo.min.css" rel="stylesheet">
        <!-- favicon -->
        <link rel="icon" href="../favicon.ico">
    </head>
    <body class="page-brand">
        <header class="header header-transparent header-waterfall ">
            <ul class="nav nav-list pull-left">
                <li id="limenu">
                    <a data-toggle="menu" href="#doc_menu">
                        <span class="icon icon-lg">menu</span><span class="text-white">MENU</span>
                    </a>
                </li>
            </ul>
            <nav class="tab-nav pull-right ">
                <ul class="nav nav-list">
                    <?= $controller->buttonsRight() ?>
                </ul>
            </nav>
        </header>
        <nav aria-hidden="true" class="menu" id="doc_menu" tabindex="-1">
            <div class="menu-scroll">
                <div class="menu-content">
                    <h1 class="menu-logo"><img src="../sof_files/logo_blue.png" alt="Setor de Orçamento e Finanças – HUSM" /></h1>
                    <ul class="nav">
                        <li>
                            <a class="waves-attach" href="index.php"><span class="text-black"><span class="icon">home</span>INÍCIO</span></a>
                        </li>
                        <li>
                            <a class="collaosed waves-attach" data-toggle="collapse" href="#osetor"><span class="text-black"><span class="icon">account_balance</span>O SETOR</a>
                            <ul class="menu-collapse collapse" id="osetor">
                                <li>
                                    <a class="waves-attach" href="sof.php">SOF</a>
                                </li>
                                <li>
                                    <a class="waves-attach" href="recepcao.php">Recepção</a>
                                </li>
                                <li>
                                    <a class="waves-attach" href="unidades.php">Unidades</a>
                                </li>
                            </ul>
                        </li>
                        <li>
                            <a class="collapsed waves-attach" data-toggle="collapse" href="#servicossof"><span class="text-black"><span class="icon">payment</span>SERVIÇOS DO SOF</a>
                            <ul class="menu-collapse collapse" id="servicossof">
                                <li>
                                    <a class="waves-attach" href="<?= $controller->hrefSolic() ?>">SOLICITAÇÕES DE EMPENHO</a>
                                </li>
                                <li>
                                    <a class="waves-attach" href="consultaspi.php">SOLICITAÇÕES GERAIS</a>
                                </li>
                            </ul>
                        </li>
                        <li>
                            <a class="collapsed waves-attach" data-toggle="collapse" href="#mconsultas"><span class="text-black"><span class="icon">build</span>CONSULTAS</a>
                            <ul class="menu-collapse collapse" id="mconsultas">

                            </ul>
                        </li>
                        <li>
                            <a class="waves-attach waves-light" href="relatorios.php"><span class="text-black"><span class="icon">folder</span>RELATÓRIOS</span></a>
                        </li>
                        <li>
                            <a class="collapsed waves-attach" data-toggle="collapse" href="#mlinks"><span class="text-black"><span class="icon">near_me</span>LINKS ÚTEIS</a>
                            <ul class="menu-collapse collapse" id="mlinks">
                                <li>
                                    <a class="waves-attach" href="linksexternos.php">LINKS EXTERNOS</a>
                                </li>
                                <li>
                                    <a class="waves-attach" href="linksinternos.php">LINKS INTERNOS</a>
                                </li>
                                <li>
                                    <a class="waves-attach" href="tutoriais.php">POPs E TUTORIAIS</a>
                                </li>
                            </ul>
                        </li>
                        <li>
                            <a class="waves-attach waves-light" href="noticia.php"><span class="text-black"><span class="icon">event</span>NOTÍCIAS</span></a>
                        </li>
                        <li>
                            <a class="collapsed waves-attach" data-toggle="collapse" href="#mencontros"><span class="text-black"><span class="icon">place</span>ENCONTROS</a>
                            <ul class="menu-collapse collapse" id="mencontros">
                                <li>
                                    <a class="waves-attach" href="boaspraticas.php">BOAS PRÁTICAS</a>
                                </li>
                                <li>
                                    <a class="waves-attach" href="comemoracoes.php">COMEMORAÇÕES</a>
                                </li>
                                <li>
                                    <a class="waves-attach" href="dinamicas.php">DINÂMICAS DE GRUPO</a>
                                </li>
                            </ul>
                        </li>
                        <li>
                            <a class="waves-attach waves-light"  href="faleconosco.php"><span class="text-black"><span class="icon">chat</span>CONTATO</span></a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
        <main class="content">
            <div class="content-heading">
                <div class="container">
                    <div class="row">
                        <h1 class="heading module wow rotateInUpRight animated"><img src="../sof_files/logo_blue.png" alt="Setor de Orçamento e Finanças – HUSM" /></h1>
                        <div class="text-header module wow rotateInUpLeft animated">
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
                                <li class="active">
                                    <a class="waves-attach" href="index.php"><span class="text-white"><span class="icon">home</span>INÍCIO</span></a>
                                </li>
                                <li>
                                    <div class="dropdown dropdown-inline">
                                        <a class="waves-attach" data-toggle="dropdown"><span class="text-white"><span class="icon">store_mall_directory</span>O SETOR</span><span class="icon margin-left-sm">keyboard_arrow_down</span></a>
                                        <ul class="dropdown-menu nav">
                                            <li>
                                                <a class="waves-attach" href="sof.php">SOF</a>
                                            </li>
                                            <li>
                                                <a class="waves-attach" href="recepcao.php">Recepção</a>
                                            </li>
                                            <li>
                                                <a class="waves-attach" href="unidades.php">Unidades</a>
                                            </li>
                                        </ul>
                                    </div>
                                </li>
                                <li>
                                    <div class="dropdown dropdown-inline">
                                        <a class="waves-attach" data-toggle="dropdown"><span class="text-white"><span class="icon">payments</span>SERVIÇOS SOF</span><span class="icon margin-left-sm">keyboard_arrow_down</span></a>
                                        <ul class="dropdown-menu nav">
                                            <li>
                                                <a class="waves-attach" href="<?= $controller->hrefSolic() ?>">Solicitações de Empenho</a>
                                            </li>
                                            <li>
                                                <a class="waves-attach" href="consultaspi.php">Solicitações Gerais</a>
                                            </li>
                                        </ul>
                                    </div>
                                </li>
                                <li>
                                    <div class="dropdown dropdown-inline">
                                        <a class="waves-attach" data-toggle="dropdown"><span class="text-white"><span class="icon">build</span>CONSULTAS</span><span class="icon margin-left-sm">keyboard_arrow_down</span></a>
                                        <ul class="dropdown-menu nav">
                                            <li>
                                                <a class="waves-attach" href="consultaspe.php">Público Externo</a>
                                            </li>
                                        </ul>
                                    </div>
                                </li>
                                <li>
                                    <a class="waves-attach waves-light" href="relatorios.php"><span class="text-white"><span class="icon">folder</span>RELATÓRIOS</span></a>
                                </li>
                                <li>
                                    <div class="dropdown dropdown-inline">
                                        <a class="waves-attach" data-toggle="dropdown"><span class="text-white"><span class="icon">near_me</span>LINKS ÚTEIS</span><span class="icon margin-left-sm">keyboard_arrow_down</span></a>
                                        <ul class="dropdown-menu nav">
                                            <li>
                                                <a class="waves-attach" href="linksexternos.php">Links Externos</a>
                                            </li>
                                            <li>
                                                <a class="waves-attach" href="linksinternos.php">Links Internos</a>
                                            </li>
                                            <li>
                                                <a class="waves-attach" href="tutoriais.php">POPs e Tutoriais</a>
                                            </li>
                                        </ul>
                                    </div>
                                </li>
                                <li>
                                    <a class="waves-attach waves-light" href="noticia.php"><span class="text-white"><span class="icon">event</span>NOTÍCIAS</span></a>
                                </li>
                                <li>
                                    <div class="dropdown dropdown-inline">
                                        <a class="waves-attach" data-toggle="dropdown"><span class="text-white"><span class="icon">place</span>ENCONTROS</span><span class="icon margin-left-sm">keyboard_arrow_down</span></a>
                                        <ul class="dropdown-menu nav">
                                            <li>
                                                <a class="waves-attach" href="boaspraticas.php">Boas Práticas</a>
                                            </li>
                                            <li>
                                                <a class="waves-attach" href="comemoracoes.php">Comemorações</a>
                                            </li>
                                            <li>
                                                <a class="waves-attach" href="dinamicas.php">Dinâmicas de grupos</a>
                                            </li>
                                        </ul>
                                    </div>
                                </li>
                                <li>
                                    <a class="waves-attach waves-light"  href="faleconosco.php"><span class="text-white"><span class="icon">chat</span>CONTATO</span></a>
                                </li>
                            </ul>
                        </nav>
                </div><!-- ./row -->
                <div class="row">
                    <div class="card margin-top-no">
                        <div class="card-main">
                            <div class="card-header card-brand">
                                <div class="card-header-side pull-left">
                                    <p class="card-heading">CONSULTAS AO PORTAL DA TRANSPARÊNCIA PÚBLICA</p>
                                </div>
                            </div><!--  ./card-header -->
                            <div class="card-inner" style="text-indent: 1.5em; text-align: justify; font-size: 12pt;">
                                <p>Por meio do <a href="http://www.portaltransparencia.gov.br/" target="_blank">Portal da Transparência Pública</a> é possível consultar a emissão de empenhos, notas fiscais pendentes de pagamentos, bem como os pagamentos realizados.</p>
                                <p>Antes da realização da consulta, sugerimos a leitura das instruções abaixo:</p>
                                <p><strong>Fase da Emissão do Empenho – Existe Orçamento Disponível</strong></p>

                                <p>&nbsp;</p>
                                <p><strong>Item 1 – NE</strong>: é o documento que o hospital envia ao fornecedor e que respalda o pedido do material/serviço. Para consultar esse documento no portal, é necessário digitar o ano de sua emissão, as iniciais de <strong>Nota de Empenho</strong>, ou seja, <strong>NE</strong>, seguidas do número do documento composto por seis dígitos. Exemplo: <strong>2015NE803674</strong>. Tudo isso sem espaços. Há que se complementar a consulta informando o número da UG que emitiu o empenho, bem como o número da GESTÃO.</p>
                                <p>As informações de UG e Gestão podem variar da seguinte forma, de acordo com a origem do Empenho:</p>
                                <p>UG 153610 – GESTÃO 15238: HOSPITAL UNIVERSITÁRIO DE SANTA MARIA – HUSM/UFSM</p>
                                <p>UG 155125 – GESTÃO 26443: HOSPITAL UNIVERSITÁRIO DE SANTA MARIA – HUSM/EBSERH</p>
                                <p><strong>Fase da Liquidação da Despesa – Aguarda Recurso Financeiro</strong></p>
                                <p>&nbsp;</p>
                                <p><strong>Item 2 – NS</strong>: é o documento gerado quando da liquidação de notas fiscais no sistema, ou seja, quando a nota fiscal é deduzida do valor respaldado pelo Empenho. Significa que a nota fiscal já foi apropriada ao empenho e aguarda que o governo disponibilize recursos financeiros para que o pagamento seja realizado. A forma mais fácil de consultar esse documento é por meio da consulta ao Empenho (item 1) destinado ao pagamento da nota fiscal que se deseja consultar. Após clicar-se sobre o número do empenho desejado, todas as NSs registradas para a referida <strong>Nota de Empenho (NE)</strong> serão exibidas citando o número das notas fiscais que serão pagas no painel <strong>Dados Detalhados</strong>.</p>
                                <p>&nbsp;</p>
                                <p><strong>Fase do Pagamento da Despesa – Existe Recurso Financeiro Disponível</strong></p>
                                <p>&nbsp;</p>
                                <p><strong>Item 3 – OB:</strong> é o documento que emite a ordem de pagamento. De acordo com a disponibilidade de recursos financeiros, assim que a OB é emitida, em até dois dias úteis o valor é disponibilizado na conta bancária do fornecedor. A forma mais fácil de consultar esse documento é por meio da consulta ao Empenho (item 1) e, logo, clicando-se no número das OBs emitidas a fim de identificar se o pagamento refere-se às notas fiscais citadas nas NSs listadas no Empenho.<strong> Quando não há informação de OB, significa que a NS aguarda liberação de recursos financeiros para que o pagamento seja efetivado</strong>.</p>
                                <p>&nbsp;</p>
                                <p><strong>Item 4</strong> – Retenções (DF, DR, GP) são orientadas pela IN 1234/2012. Essas retenções são deduzidas do valor total da nota fiscal, sendo que o valor líquido é liberado ao fornecedor por meio da OB (item 3). Para maiores esclarecimentos quanto às retenções, o fornecedor deve consultar seu próprio suporte contábil.</p>
                                <p>&nbsp;</p>

                                <p><strong>Vídeo demonstrativo:</strong><br>
                                <div style="align: center; margin-left: 23%;">
                                    <iframe src="https://www.youtube.com/embed/T9Ez5Ivn0Us?rel=0" width="560" height="315" frameborder="0" allowfullscreen="allowfullscreen"></iframe></p>
                                </div>
                            </div>
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
    <div class="modal fade" id="esqueceuSenha" role="dialog">
        <div class="modal-dialog" style="width: 40%;">
            <div class="modal-content">
                <div class="modal-heading">
                    <a class="modal-close" data-dismiss="modal">×</a>
                    <h2 class="modal-title content-sub-heading">Esqueceu a Senha?</h2>
                </div>
                <form id="formReset" action="javascript:resetSenha();" method="post">
                    <div id="innerLogin" class="modal-inner">
                        <div class="form-group form-group-label">
                            <label class="floating-label" for="userReset"><span class="icon">perm_identity</span>&nbsp;E-mail</label>
                            <input class="form-control" id="userReset" name="login" type="text" required>
                        </div>
                        <div id="loaderResetSenha" class="progress-circular" style="margin-left: 45%; display: none;">
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
                        <button class="btn btn-brand waves-attach waves-light waves-effect" type="submit" style="width: 100%;"><span class="icon">autorenew</span>&nbsp;Resetar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="login" role="dialog">
        <div class="modal-dialog" style="width: 40%;">
            <div class="modal-content">
                <div class="modal-heading">
                    <a class="modal-close" data-dismiss="modal">×</a>
                    <h2 class="modal-title content-sub-heading">Login</h2>
                </div>
                <form id="formLogin" action="javascript:login();" method="post">
                    <div id="innerLogin" class="modal-inner">
                        <div id="aviso" class="tile tile-orange margin-bottom margin-top-no" style="display: none;">
                            <div class="tile-side pull-left">
                                <div class="avatar avatar-sm avatar-orange">
                                    <span class="icon icon-lg text-white">error_outline</span>
                                </div>
                            </div>
                            <div class="tile-inner">
                                <span class="text-overflow text-white" style="font-weight: bold; font-size: 10pt;">Login ou Senha incorretos </span>
                            </div>
                        </div>
                        <div class="form-group form-group-label">
                            <label class="floating-label" for="user"><span class="icon">perm_identity</span>&nbsp;Usuário</label>
                            <input class="form-control" id="user" name="login" type="text" required>
                        </div>
                        <div class="form-group form-group-label">
                            <label class="floating-label" for="senha"><span class="icon">lock_outline</span>&nbsp;Senha</label>
                            <input class="form-control" id="senha" name="senha" type="password" required>
                        </div>
                        <a class="" href="javascript:abreModal('#esqueceuSenha');">Esqueceu a senha?</a>
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
                        <button class="btn btn-brand waves-attach waves-light waves-effect" type="submit" style="width: 100%;"><span class="icon">send</span>&nbsp;Entrar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div aria-hidden="true" class="modal modal-va-middle fade" id="construindo" role="dialog" tabindex="-1">
        <div class="modal-dialog modal-xs">
            <div class="modal-content">
                <div class="modal-heading">
                    <a class="modal-close" data-dismiss="modal">×</a>
                </div>
                <div class="modal-inner">
                    <h2 class="content-sub-heading">Página em Contrução...</h2>
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
    <script src="../ini.min.js"></script>

</body>
</html>
