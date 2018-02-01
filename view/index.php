<?php
session_start();

ini_set('display_errors', true);
error_reporting(E_ALL);

if (!isset($_SESSION['slide1']) || !isset($_SESSION['slide2'])) {
    header("Location: ../");
}

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
    <title>Setor de Orçamento e Finanças – HUSM</title>

    <!-- css -->
    <link href="../material/css/base.min.css" rel="stylesheet">

    <!-- css for doc -->
    <link href="../material/css/project.min.css" rel="stylesheet">

    <link rel="stylesheet" type="text/css" href="../sof_files/estilo.min.css">
    <link rel="stylesheet" type="text/css" href="../sof_files/style.min.css">
    <link href="../plugins/animate/animate.min.css" rel="stylesheet">
    <!-- favicon -->
    <link rel="icon" href="../favicon.ico">

</head>
<body class="page-brand">
<header class="header header-transparent header-waterfall">
    <ul class="nav nav-list pull-left">
        <li id="limenu">
            <a data-toggle="menu" href="#doc_menu">
                <span class="icon icon-lg">menu</span><span class="text-white">MENU</span>
            </a>
        </li>
        <li>
            <a class="btn btn-flat waves-attach waves-light" href="javascript:abreModal('#modalPesquisar');">
                <span class="text-white"><span class="icon icon-lg">search</span>PESQUISAR</span>
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
                            <a class="waves-attach" href="<?= $controller->hrefSolic() ?>">SOLICITAÇÕES DE EMPENHO</a>
                        </li>
                        <li>
                            <a class="waves-attach" href="consultaspi.php">SOLICITAÇÕES GERAIS</a>
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
                <h1 class="heading module wow slideInRight animated"><img src="../sof_files/logo_blue.png"
                                                                          alt="Setor de Orçamento e Finanças – HUSM"/>
                </h1>
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
                <?php include_once 'navbar.php' ?>
                <div>
                    <div class="card-wrap col-lg-4 col-sm-6 pull-left margin-bottom">
                        <div class="card">
                            <div class="card-main">
                                <div class="card-header card-brand">
                                    <div class="card-header-side pull-left">
                                        <p class="card-heading">O SETOR</p>
                                    </div>
                                </div><!--  ./card-header -->
                                <div class="card-inner" style="padding-left: 30px; padding-right: 30px;">
                                    <p style="text-align: justify; text-indent: 2em;">O Setor de Orçamento e Finanças -
                                        SOF subordina-se à Superintendência do Hospital Universitário de Santa Maria -
                                        HUSM
                                        por meio da Gerência Administrativa - GA, estando diretamente ligado à Divisão
                                        Administrativa Financeira – DAF.
                                        Suas atividades tiveram início em 01 de janeiro de 2015, após a Empresa
                                        Brasileira de Serviços Hospitalares – EBSERH assumir a gestão do HUSM.</p>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-main">
                                <div class="card-header card-brand">
                                    <div class="card-header-side pull-left">
                                        <p class="card-heading">MISSÃO</p>
                                    </div>
                                </div><!--  ./card-header -->
                                <div class="card-inner" style="padding-left: 30px; padding-right: 30px;">
                                    <p style="text-align: justify; text-indent: 2em;">
                                        Realizar a gestão orçamentária e financeira do HUSM, executando com
                                        responsabilidade, economicidade, celeridade, qualidade
                                        e resolutividade os três estágios da despesa pública -<b>empenho, liquidação e
                                            pagamento</b>- em atendimento às necessidades da
                                        instituição. </p>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-main">
                                <div class="card-header card-brand">
                                    <div class="card-header-side pull-left">
                                        <p class="card-heading">VISÃO</p>
                                    </div>
                                </div><!--  ./card-header -->
                                <div class="card-inner" style="padding-left: 30px; padding-right: 30px;">
                                    <p style="text-align: justify; text-indent: 2em;">
                                        Ser referência em planejamento, execução e controle orçamentário e financeiro,
                                        contribuindo para o desenvolvimento da qualidade em saúde,
                                        ensino, pesquisa e extensão. </p>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-main">
                                <div class="card-header card-brand">
                                    <div class="card-header-side pull-left">
                                        <p class="card-heading">VALORES</p>
                                    </div>
                                </div><!--  ./card-header -->
                                <div class="card-inner" style="padding-left: 30px; padding-right: 30px;">
                                    <p style="text-align: justify; text-indent: 2em;">
                                        Valorização da Equipe de Trabalho, Transparência nas Ações, Sinergia na Execução
                                        das Tarefas, Empatia no Atendimento, Pró-atividade
                                        e Dinamismo. </p>
                                </div>
                            </div>
                        </div>
                    </div><!-- ./pull-left-->
                    <div class="card-wrap col-lg-4 col-sm-6 pull-right margin-bottom">
                        <div id="destaque">
                            <div class="card">
                                <div class="card-main">
                                    <div class="card-header card-brand">
                                        <div class="card-header-side pull-left">
                                            <p class="card-heading">ÚLTIMAS NOTÍCIAS</p>
                                        </div>
                                    </div><!--  ./card-header -->
                                    <div class="card-inner padding-no margin-no">
                                        <div class="slider">
                                            <div class="mascara">
                                                <ul>
                                                    <?php echo $_SESSION['slide1'] ?>
                                                </ul>
                                            </div>
                                            <div class="barra-progresso"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card">
                                <div class="card-main">
                                    <div class="card-header card-brand">
                                        <div class="card-header-side pull-left">
                                            <p class="card-heading">NOTÍCIAS EM DESTAQUE</p>
                                        </div>
                                    </div><!--  ./card-header -->
                                    <div class="card-inner padding-no margin-no">
                                        <div class="slider">
                                            <div class="mascara">
                                                <ul>
                                                    <?php echo $_SESSION['slide2'] ?>
                                                </ul>
                                            </div>
                                            <div class="barra-progresso"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div> <!--/ fim destaque -->
                    </div><!-- ./pull-right -->
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

<div class="modal fade" id="modalPesquisar" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-heading">
                <a class="modal-close" data-dismiss="modal">×</a>
                <h2 class="modal-title content-sub-heading"></h2>
            </div>
            <div class="form-group form-group-label">
                <label class="floating-label padding-left" for="search" style="font-size: 14pt;"><span class="icon">search</span>&nbsp;Digite
                    aqui sua procura</label>
                <input class="form-control padding-left" id="search" name="search" type="text" required
                       style="font-size: 14pt;">
            </div>
            <div class="modal-footer">
                <button class="btn waves-attach" type="button" onclick="pesquisar();" style="width: 100%;"><span
                            class="icon" style="font-weight: bold;">search</span></button>
            </div>
            <div id="conteudo" class="modal-inner">

            </div>
        </div>
    </div>
</div>

<!-- js -->
<script src="../plugins/jQuery/jquery.min.js"></script>
<script src="../material/js/base.min.js"></script>
<script src="../ini.min.js"></script>
<!-- js for doc -->
<script src="../material/js/project.min.js"></script>
</body>
</html>
