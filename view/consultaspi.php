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
    <title>Setor de Orçamento e Finanças – HUSM</title>
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
<input id="div_ajax" type="hidden">
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
                <h1 class="heading module wow bounceIn animated"><img src="../sof_files/logo_blue.png"
                                                                      alt="Setor de Orçamento e Finanças – HUSM"/></h1>
                <div class="text-header module wow bounceIn animated">
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
                                <p class="card-heading">Solicitação de Pagamento de Taxa de Inscrição</p>
                            </div>
                        </div><!--  ./card-header -->
                        <form action="printSolicTax.php" method="post">
                            <input type="hidden" name="form" value="solicPagTaxa">
                            <div class="card-inner">
                                <h2 class="content-sub-heading">Dados pessoais</h2>
                                <div class="form-group form-group-label">
                                    <label class="floating-label" for="nome">Solicitante</label>
                                    <input class="form-control" id="nome" name="nome" type="text" required>
                                </div>
                                <div class="form-group form-group-label">
                                    <label class="floating-label" for="cpf">CPF</label>
                                    <input class="form-control" id="cpf" name="cpf" type="text" required>
                                </div>
                                <div class="form-group form-group-label">
                                    <label class="floating-label" for="matricula">Matrícula SIAPE</label>
                                    <input class="form-control" id="matricula" name="matricula" type="text">
                                </div>
                                <div class="form-group form-group-label">
                                    <label class="floating-label" for="email">E-mail</label>
                                    <input class="form-control" id="email" name="email" type="email" required>
                                </div>
                                <div class="form-group form-group-label">
                                    <label class="floating-label" for="telefone">Telefone</label>
                                    <input class="form-control" id="telefone" name="telefone" type="text">
                                </div>
                                <div class="form-group form-group-label">
                                    <label class="floating-label" for="banco">Banco</label>
                                    <input class="form-control" id="banco" name="banco" type="text">
                                </div>
                                <div class="form-group form-group-label">
                                    <label class="floating-label" for="agencia">Agência</label>
                                    <input class="form-control" id="agencia" name="agencia" type="text">
                                </div>
                                <div class="form-group form-group-label">
                                    <label class="floating-label" for="conta">Conta</label>
                                    <input class="form-control" id="conta" name="conta" type="text">
                                </div>
                                <h2 class="content-sub-heading">Dados do Evento</h2>
                                <div class="form-group form-group-label">
                                    <label class="floating-label" for="evento">Nome do Evento</label>
                                    <input class="form-control" id="evento" name="evento" type="text" required>
                                </div>
                                <div class="form-group">
                                    <input class="datepicker-adv form-control" id="dataI" type="text" name="dataI"
                                           placeholder="Data Início" required>
                                </div>
                                <div class="form-group">
                                    <input class="datepicker-adv form-control" id="dataF" type="text" name="dataF"
                                           placeholder="Data Fim" required>
                                </div>
                                <div class="form-group form-group-label">
                                    <label class="floating-label" for="local">Local</label>
                                    <input class="form-control" id="local" name="local" type="text" required>
                                </div>
                                <div class="form-group form-group-label">
                                    <label class="floating-label" for="valor">Valor</label>
                                    <input class="form-control" id="valor" name="valor" type="text" required>
                                </div>
                            </div><!-- ./card-inner -->
                            <div class="card-action">
                                <div class="card-action-btn">
                                    <button class="btn btn-brand waves-attach" type="submit" style="width: 100%;"><span
                                                class="icon">print</span>&nbsp;Imprimir
                                    </button>
                                </div>
                            </div>
                        </form>
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
<script src="../plugins/jQuery/jquery.mask.min.js"></script>
<script src="../material/js/base.min.js"></script>

<!-- js for doc -->
<script src="../material/js/project.min.js"></script>

<script type="text/javascript" src="../ini.min.js"></script>
<script>
    $(function () {
        $('.datepicker-adv').datepicker({
            selectMonths: false,
            selectYears: true
        });
        $('#cpf').mask('000.000.000-00', {reverse: true});
        $('#telefone').mask('(00) 0000-0000');
        $('#valor').mask('000.000.000.000.000,00', {reverse: true});
    });
</script>
</body>
</html>
