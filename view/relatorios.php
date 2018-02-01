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
<?php include_once 'navbar-hidden.php' ?>
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
                <?php include_once 'navbar.php' ?>
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
