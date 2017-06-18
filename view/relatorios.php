<?php
session_start();

ini_set('display_errors', true);
error_reporting(E_ALL);

spl_autoload_register(function (string $class_name) {
    include_once '../class/' . $class_name . '.class.php';
});

$controller = Controller::getInstance();
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta content="IE=edge" http-equiv="X-UA-Compatible">
    <meta content="initial-scale=1.0, maximum-scale=1.0, user-scalable=no, width=device-width" name="viewport">
    <title>Relatórios | Setor de Orçamento e Finanças – HUSM</title>
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
            <h1 class="menu-logo"><img src="../sof_files/logo_blue.png" alt="Setor de Orçamento e Finanças – HUSM"/>
            </h1>
            <ul class="nav">
                <li>
                    <a class="waves-attach" href="index.php"><span class="text-black"><span class="icon">home</span>INÍCIO</span></a>
                </li>
                <li>
                    <a class="collaosed waves-attach" data-toggle="collapse" href="#osetor"><span
                                class="text-black"><span class="icon">account_balance</span>O SETOR</a>
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
                    <a class="collapsed waves-attach" data-toggle="collapse" href="#servicossof"><span
                                class="text-black"><span class="icon">payment</span>SERVIÇOS DO SOF</a>
                    <ul class="menu-collapse collapse" id="servicossof">
                        <li>
                            <a class="waves-attach" href="javascript:abreModal('#construindo');">SOLICITAÇÕES</a>
                        </li>
                    </ul>
                </li>
                <li>
                    <a class="collapsed waves-attach" data-toggle="collapse" href="#mconsultas"><span
                                class="text-black"><span class="icon">build</span>CONSULTAS</a>
                    <ul class="menu-collapse collapse" id="mconsultas">
                        <li>
                            <a class="waves-attach" href="consultaspe.php">PÚBLICO EXTERNO</a>
                        </li>
                        <li>
                            <a class="waves-attach" href="javascript:abreModal('#construindo');">PÚBLICO INTERNO</a>
                        </li>
                    </ul>
                </li>
                <li>
                    <a class="waves-attach waves-light" href="relatorios.php"><span class="text-black"><span
                                    class="icon">folder</span>RELATÓRIOS</span></a>
                </li>
                <li>
                    <a class="collapsed waves-attach" data-toggle="collapse" href="#mlinks"><span
                                class="text-black"><span class="icon">near_me</span>LINKS ÚTEIS</a>
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
                    <a class="collapsed waves-attach" data-toggle="collapse" href="#mencontros"><span
                                class="text-black"><span class="icon">place</span>ENCONTROS</a>
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
                    <a class="waves-attach waves-light" href="faleconosco.php"><span class="text-black"><span
                                    class="icon">chat</span>CONTATO</span></a>
                </li>
            </ul>
        </div>
    </div>
</nav>
<main class="content">
    <div class="content-heading">
        <div class="container">
            <div class="row">
                <h1 class="heading module wow rotateInDownRight animated"><img src="../sof_files/logo_blue.png"
                                                                               alt="Setor de Orçamento e Finanças – HUSM"/>
                </h1>
                <div class="text-header module wow rotateInDownLeft animated">
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
                            <a class="waves-attach" href="index.php"><span class="text-white"><span
                                            class="icon">home</span>INÍCIO</span></a>
                        </li>
                        <li>
                            <div class="dropdown dropdown-inline">
                                <a class="waves-attach" data-toggle="dropdown"><span class="text-white"><span
                                                class="icon">store_mall_directory</span>O SETOR</span><span
                                            class="icon margin-left-sm">keyboard_arrow_down</span></a>
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
                                <a class="waves-attach" data-toggle="dropdown"><span class="text-white"><span
                                                class="icon">payments</span>SERVIÇOS SOF</span><span
                                            class="icon margin-left-sm">keyboard_arrow_down</span></a>
                                <ul class="dropdown-menu nav">
                                    <li>
                                        <a class="waves-attach" href="<?= $controller->hrefSolic() ?>">Solicitações de
                                            Empenho</a>
                                    </li>
                                    <li>
                                        <a class="waves-attach" href="consultaspi.php">Solicitações Gerais</a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                        <li>
                            <div class="dropdown dropdown-inline">
                                <a class="waves-attach" data-toggle="dropdown"><span class="text-white"><span
                                                class="icon">build</span>CONSULTAS</span><span
                                            class="icon margin-left-sm">keyboard_arrow_down</span></a>
                                <ul class="dropdown-menu nav">
                                    <li>
                                        <a class="waves-attach" href="consultaspe.php">Público Externo</a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                        <li>
                            <a class="waves-attach waves-light" href="relatorios.php"><span class="text-white"><span
                                            class="icon">folder</span>RELATÓRIOS</span></a>
                        </li>
                        <li>
                            <div class="dropdown dropdown-inline">
                                <a class="waves-attach" data-toggle="dropdown"><span class="text-white"><span
                                                class="icon">near_me</span>LINKS ÚTEIS</span><span
                                            class="icon margin-left-sm">keyboard_arrow_down</span></a>
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
                            <a class="waves-attach waves-light" href="noticia.php"><span class="text-white"><span
                                            class="icon">event</span>NOTÍCIAS</span></a>
                        </li>
                        <li>
                            <div class="dropdown dropdown-inline">
                                <a class="waves-attach" data-toggle="dropdown"><span class="text-white"><span
                                                class="icon">place</span>ENCONTROS</span><span
                                            class="icon margin-left-sm">keyboard_arrow_down</span></a>
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
                            <a class="waves-attach waves-light" href="faleconosco.php"><span class="text-white"><span
                                            class="icon">chat</span>CONTATO</span></a>
                        </li>
                    </ul>
                </nav>
        </div><!-- ./row -->
        <div class="row">
            <div class="card margin-top-no">
                <div class="card-main">
                    <div class="card-header card-brand">
                        <div class="card-header-side pull-left">
                            <p class="card-heading">CADERNO DE RELATÓRIOS</p>
                        </div>
                    </div><!--  ./card-header -->
                    <div class="card-inner" style="text-indent: 1.5em; text-align: justify; font-size: 12pt;">
                        <p>A divulgação das informações aqui disponibilizadas está amparada na <a
                                    href="http://www.acessoainformacao.gov.br/assuntos/conheca-seu-direito/principais-aspectos/principais-aspectos"
                                    target="_blank">Lei de Acesso à Informação</a> regulamentada pelo <a
                                    href="http://www.acessoainformacao.gov.br/assuntos/conheca-seu-direito/a-lei-de-acesso-a-informacao/mapa-do-decreto-no-7.724"
                                    target="_blank">Decreto nº 7.724/2012</a>.</p>
                        <ul>
                            <li><a href="rel1.php">Caderno de Execução Orçamentária do HUSM</a></li>
                            <li><a href="#">Caderno de Execução Financeira do HUSM (em construção)</a></li>
                            <li><a href="#">Caderno de Indicadores do SOF (em construção)</a></li>
                        </ul>
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

<!-- js -->
<script src="../plugins/jQuery/jquery.min.js"></script>
<script src="../material/js/base.min.js"></script>

<!-- js for doc -->
<script src="../material/js/project.min.js"></script>

<script type="text/javascript" src="../ini.min.js"></script>
</body>
</html>
