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
                <?php
                include_once 'navbar.php';
                if (isset($_SESSION["email_sucesso"])) {
                    echo "
   <div id=\"aviso\" class=\"tile tile-orange\" style=\"margin-top: -3%;\">
    <div class=\"tile-side pull-left\">
      <div class=\"avatar avatar-sm avatar-orange\">
        <span class=\"icon icon-lg text-white\">done_all</span>
      </div>
    </div>
    <div class=\"tile-action tile-action-show\">
      <ul class=\"nav nav-list margin-no pull-right\">
        <li>
          <a class=\"text-black-sec waves-attach waves-effect\" href=\"javascript:aviso();\"><span class=\"icon text-white\">close</span></a>
        </li>
      </ul>
    </div>
    <div class=\"tile-inner\">
      <span class=\"text-overflow text-white\" style=\"font-weight: bold; font-size: 12pt;\">Sua mensagem foi enviada com Sucesso !</span>
    </div>
  </div>
  ";
                    unset($_SESSION["email_sucesso"]);
                }
                ?>
                <div class="card margin-top-no">
                    <div class="card-main">
                        <div class="card-header card-brand">
                            <div class="card-header-side pull-left">
                                <p class="card-heading">Formulário para Contato</p>
                            </div>
                        </div><!--  ./card-header -->
                        <form enctype="multipart/form-data" action="../php/geral.php" method="post">
                            <input type="hidden" name="form" value="faleconosco">
                            <div class="card-inner">
                                <div class="form-group form-group-label">
                                    <label class="floating-label" for="nome">Nome Completo*</label>
                                    <input class="form-control" id="nome" name="nome" type="text" required>
                                </div>
                                <div class="form-group form-group-label">
                                    <label class="floating-label" for="email">E-mail*</label>
                                    <input class="form-control" id="email" name="email" type="email" required>
                                </div>
                                <div class="form-group form-group-label">
                                    <label class="floating-label" for="assunto">Assunto</label>
                                    <input class="form-control" id="assunto" name="assunto" type="text">
                                </div>
                                <div class="form-group form-group-label">
                                    <label class="floating-label" for="mensagem">Mensagem</label>
                                    <textarea class="form-control textarea-autosize" id="mensagem" name="mensagem"
                                              rows="1"></textarea>
                                </div>
                                <input id="qtd-arquivos" name="qtd-arquivos" type="hidden" value="0">
                                <button class="btn waves-attach" type="button" onclick="addInputsArquivo();"><span
                                            class="text-black"><span class="icon">add</span>Anexar Arquivos</span>
                                </button>
                                <div class="form-group">
                                    <table class="table margin-top-no" id="arquivos">

                                    </table>
                                    <p class="help-block">Max. 32MB</p>
                                </div>
                                <span class="label label-orange" style="font-size: 11pt;"><span
                                            class="icon">warning</span>Se quiser excluir um arquivo anexado, por favor, comece excluindo de baixo para cima</span><br><br>
                                <span class="label">* Campo Obrigatório</span>
                            </div><!-- ./card-inner -->
                            <div class="card-action">
                                <div class="card-action-btn">
                                    <button class="btn btn-brand waves-attach" type="submit" style="width: 100%;"><span
                                                class="icon">check</span>&nbsp;Enviar
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
<script src="../material/js/base.min.js"></script>

<!-- js for doc -->
<script src="../material/js/project.min.js"></script>

<script type="text/javascript" src="../ini.min.js"></script>
</body>
</html>
