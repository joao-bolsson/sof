<?php
session_start();

ini_set('display_erros', true);
error_reporting(E_ALL);

//define se o botão de LOGIN deve ou não existir
$btnLogin = true;
//define se o botão de ADMIN deve ou não existir
$btnAdmin = false;
//define se o botão sair deve ou não existir
$btnSair = false;
$hrefSolicitacoes = "javascript:abreModal('#login');";
if (!isset($_SESSION["id_setor"]) || $_SESSION["id_setor"] == 0) {
	$_SESSION["id_setor"] = 0;
} else {
	//o botão admin deve existir
	if ($_SESSION["id_setor"] == 2) {
		$btnAdmin = true;
	}
	$hrefSolicitacoes = "../lte/solicitacoes.php";
	$btnLogin = false;
	$btnSair = true;
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
	<title>Unidades | Setor de Orçamento e Finanças – HUSM</title>
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
      <?php if ($btnAdmin): ?>
        <li class="active">
          <a class="btn btn-flat waves-attach waves-light" href="../lte/"><span class="text-white"><span class="icon">power_settings_new</span>ADMIN</span></a>
        </li>
      <?php endif;?>
      <?php if ($btnLogin): ?>
        <li class="active">
          <a class="btn btn-flat waves-attach waves-light" href="javascript:abreModal('#login');"><span class="text-white"><span class="icon">power_settings_new</span>LOGIN</span></a>
        </li>
      <?php endif;?>
      <?php if ($btnSair): ?>
        <li>
          <a class="btn btn-flat waves-attach waves-light" href="../admin/sair.php"><span class="text-white"><span class="icon">undo</span>SAIR</span></a>
        </li>
      <?php endif;?>
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
              <a class="waves-attach" href="<?=$hrefSolicitacoes?>">SOLICITAÇÕES DE EMPENHO</a>
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
        <h1 class="heading module wow zoomInUp animated"><img src="../sof_files/logo_blue.png" alt="Setor de Orçamento e Finanças – HUSM" /></h1>
        <div class="text-header module wow zoomInDown animated">
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
            <a class="waves-attach" href="<?=$hrefSolicitacoes?>">Solicitações de Empenho</a>
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
  <div class="col-lg-3 col-sm-6 pull-left">
    <div class="card margin-top-no">
      <div class="card-main">
        <div class="card-header card-brand">
          <div class="card-header-side pull-left">
            <p class="card-heading">Unidade de Programação Orçamentária e Financeira</p>
          </div>
        </div><!--  ./card-header -->
        <div class="card-inner">
          <ul class="nav">
            <li class="tile"><a class="btn btn-brand-accent btn-flat waves-attach waves-effect" data-toggle="modal" href="#progApresentacao"><span class="icon icon-lg">accessibility</span>APRESENTAÇÃO</a></li>
            <li><a class="btn btn-brand-accent btn-flat waves-attach waves-effect" data-toggle="modal" href="#servicoAnalise"><span class="icon icon-lg">find_in_page</span>SERVIÇO DE ANÁLISE</a></li>
            <li class="tile"><a class="btn btn-brand-accent btn-flat waves-attach waves-effect" data-toggle="modal" href="#sieRegPrecos"><span class="icon icon-lg">attach_money</span>SERVIÇO DE EMPENHO SIE – REGISTRO DE PREÇOS</a></li>
            <li><a class="btn btn-brand-accent btn-flat waves-attach waves-effect" data-toggle="modal" href="#sieContratos"><span class="icon icon-lg">insert_drive_file</span>SERVIÇO DE EMPENHOS SIE – CONTRATOS</a></li>
            <li class="tile"><a class="btn btn-brand-accent btn-flat waves-attach waves-effect" data-toggle="modal" href="#empenhoSIAFI"><span class="icon icon-lg">grade</span>SERVIÇO DE EMPENHOS SIAFI</a></li>
            <li><a class="btn btn-brand-accent btn-flat waves-attach waves-effect" data-toggle="modal" href="#servicoOrcamento"><span class="icon icon-lg">build</span>SERVIÇO DE ORÇAMENTO</a></li>
          </ul>
        </div>
      </div><!-- ./card-main -->
    </div> <!-- ./card -->
  </div>
  <div class="col-lg-3 col-sm-6 pull-right">
    <div class="card margin-top-no">
      <div class="card-main">
        <div class="card-header card-brand">
          <div class="card-header-side pull-left">
            <p class="card-heading">Unidade de Liquidação e Pagamento da Despesa</p>
          </div>
        </div><!--  ./card-header -->
        <div class="card-inner">
          <ul class="nav">
            <li class="tile"><a class="btn btn-brand-accent btn-flat waves-attach waves-effect" data-toggle="modal" href="#liqApresentacao"><span class="icon icon-lg">accessibility</span>APRESENTAÇÃO</a></li>
            <li><a class="btn btn-brand-accent btn-flat waves-attach waves-effect" data-toggle="modal" href="#servicoRelatorios"><span class="icon icon-lg">description</span>SERVIÇO DE RELATÓRIOS</a></li>
            <li class="tile"><a class="btn btn-brand-accent btn-flat waves-attach waves-effect" data-toggle="modal" href="#servicoLiqContratos"><span class="icon icon-lg">insert_drive_file</span>SERVIÇO DE LIQUIDAÇÃO DE NOTAS FISCAIS – CONTRATOS</a></li>
            <li><a class="btn btn-brand-accent btn-flat waves-attach waves-effect" data-toggle="modal" href="#liqRegPrecos"><span class="icon icon-lg">attach_money</span>SERVIÇO DE LIQUIDAÇÃO – REGISTRO DE PREÇOS</a></li>
            <li class="tile"><a class="btn btn-brand-accent btn-flat waves-attach waves-effect" data-toggle="modal" href="#servicoPagamentos"><span class="icon icon-lg">payment</span>SERVIÇO DE PAGAMENTOS</a></li>
            <li><a class="btn btn-brand-accent btn-flat waves-attach waves-effect" data-toggle="modal" href="#suprimentosFundo"><span class="icon icon-lg">local_shipping</span>SERVIÇO DE SUPRIMENTO DE FUNDOS</a></li>
          </ul>
        </div>
      </div><!-- ./card-main -->
    </div> <!-- ./card -->
  </div>
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
  <!--

      MODAIS UNIDADES DE PROGRAMAÇÃO ORÇAMENTÁRIA E FINANCEIRA

    -->
    <div aria-hidden="true" class="modal fade" id="progApresentacao" role="dialog" tabindex="-1">
      <div class="modal-dialog">
       <div class="modal-content">
        <div class="modal-heading">
         <a class="modal-close" data-dismiss="modal">×</a>
         <h2 class="modal-title">Unidade de Programação Orçamentária e Financeira do HUSM</h2>
       </div>
       <div class="modal-inner" style="text-ident: 1.5em; text-align: justify;">
        <p>Na medida em que o orçamento é liberado, a Unidade de Programação Orçamentária e Financeira realiza a reclassificação dos créditos a fim de atender às demandas de compras do hospital. <br><br>Então, logo após serem devidamente analisados, os pedidos de compras são lançados no Sistema Integrado para o Ensino – SIE, transformando-se em “empenhos internos”, divididos entre contratos e registros de preços, que serão emitidos no Sistema Integrado de Administração de Serviços Gerais – SIASG, e enviados ao Sistema Integrado de Administração Financeira do Governo Federal – SIAFI.</p>
        <p>Para visualizar os empenhos já emitidos, <a href="consultaspe.php">clique aqui</a>.</p>
      </div>
      <div class="modal-footer">
       <p class="text-right"><button class="btn btn-brand waves-attach waves-effect" data-dismiss="modal" type="button">FECHAR</button></p>

     </div>
   </div>
 </div>
</div>
<div aria-hidden="true" class="modal fade" id="servicoAnalise" role="dialog" tabindex="-1">
  <div class="modal-dialog">
   <div class="modal-content">
    <div class="modal-heading">
     <a class="modal-close" data-dismiss="modal">×</a>
     <h2 class="modal-title">Serviço de Análise</h2>
   </div>
   <div class="modal-inner" style="text-ident: 1.5em; text-align: justify;">
    <p>Na medida em que os pedidos de compras e solicitações de serviços chegam ao Serviço de
     Análise, inicia-se o processo de avaliação da solicitação de empenho, sendo que, nos
     pedidos referentes a materiais, são realizadas a análise da disponibilidade do item em
     <a class="btn waves-attach waves-effect" data-toggle="collapse" href="#colrestos">Restos a Pagar</a> e análise dos
     quantitativos disponíveis em <a class="btn waves-attach waves-effect" data-toggle="collapse" href="#colestoque">estoque</a>.
   </p>
   <div class="collapse collapsible-region" id="colrestos">
    <div class="card">
      <div class="card-main">
        <div class="card-inner">
          <p class="card-heading">RESTOS A PAGAR</p>
          <p>No que tange a RP, cada item do pedido é verificado a fim de encontrar, ou não, sua existência em empenhos do exercício anterior. Em caso positivo, solicita-se ao interessado na compra uma justificativa, formal, sobre a necessidade de aquisição de quantitativos excedentes às quantidades disponíveis nos empenhos de exercícios anteriores. <br><br>Essa medida se faz necessária em dois aspectos. São eles:</p>
          <p> – <strong>Gestão de RP</strong><br>
            Visa gerenciar a real necessidade da inscrição de empenhos em restos a pagar, ou seja, se for inscrito, será usado;</p>
            <p style="background: #ccc;">É importante destacar que os valores em RP que são cancelados não podem ser utilizados em novas compras no exercício corrente.</p>
            <p>– <strong>Gestão do Orçamento Corrente</strong><br>
              Visa à economicidade e ao melhor aproveitamento dos créditos disponíveis, ou seja, a execução da despesa só será realizada se realmente necessária;</p>
              <p>Contudo, diante da comprovada necessidade de novo empenhamento, solicitamos que a justificativa retorne ao nosso serviço, acompanhada de solicitação de cancelamento do valor inscrito em restos a pagar, com o objetivo de diminuir o comprometimento do HUSM na dívida pública flutuante.
              </p>
            </div>
          </div>
        </div>
      </div>
      <div class="collapse collapsible-region" id="colestoque">
        <div class="card">
          <div class="card-main">
            <div class="card-inner">
              <p class="card-heading">ESTOQUE</p>
              <p>A análise da disponibilidade em estoque é realizada comparando-se o quantitativo
                solicitado com as quantidades disponíveis no estoque e com a média consumida do item de
                forma a evitar uma compra exacerbada.
              </p>
            </div>
          </div>
        </div>
      </div>
      <p>Depois de realizada essa primeira etapa, os pedidos aprovados são encaminhados ao Serviço de Empenhos SIE para que sejam transformados em “empenhos internos” e, só então, retornem ao Serviço de Análise para serem conferidos.<br><br>
        Em um segundo momento, é realizada a conferência dos empenhos internos, recebidos do Serviço de Orçamento, com os dados do respectivo processo que respalda a compra. Essa conferência é realizada em todos os empenhos internos de compras ou serviços do HUSM, incluindo Registros de Preços, Contratos, Taxas de Inscrição, Dispensas de Licitação e Processos Administrativos.<br><br> Diante da concordância de todos os empenhos internos com seus respectivos processos, esses documentos são encaminhados para serem lançados no Sistema de Administração de Serviços Gerais do Governo Federal – SIASG e, logo, enviados ao Sistema Integrado de Administração Financeira do Governo Federal – SIAFI por meio do Serviço de Empenhos SIAFI do HUSM.</p>
        <p><a class="btn btn-flat waves-attach waves effect" href="javascript:void(0);">Fluxograma em construção…</a></p>

      </div>
      <div class="modal-footer">
       <p class="text-right"><button class="btn btn-brand waves-attach waves-effect" data-dismiss="modal" type="button">FECHAR</button></p>

     </div>
   </div>
 </div>
</div>
<div aria-hidden="true" class="modal fade" id="sieRegPrecos" role="dialog" tabindex="-1">
  <div class="modal-dialog">
   <div class="modal-content">
    <div class="modal-heading">
     <a class="modal-close" data-dismiss="modal">×</a>
     <h2 class="modal-title">Serviço de Empenho SIE | Registro de Preços</h2>
   </div>
   <div class="modal-inner" style="text-ident: 1.5em; text-align: justify;">
    <p>À medida que as solicitações de empenhos chegam ao Serviço de Empenhos – Registro de Preços, os mesmos são separados por: origem da solicitação, fornecedores e, em seguida, por ordem cronológica a fim de que, em um último momento, conforme disponibilidade orçamentária, os mesmos sejam lançados no Sistema Integrado para o Ensino – SIE, na forma de empenhos internos. <br><br>Logo, solicita-se a reclassificação orçamentária ao Serviço de Orçamento para o posterior envio dos empenhos internos ao Serviço de Empenhos SIAFI do HUSM. <br><br>É importante destacar que esses procedimentos são realizados para as solicitações de compras e serviços, bem como para Taxas de Inscrição, Dispensas de Licitação e Suprimento de Fundos.</p>
    <p><a class="btn btn-flat waves-attach waves effect" href="javascript:void(0);">Fluxograma em construção…</a></p>

  </div>
  <div class="modal-footer">
   <p class="text-right"><button class="btn btn-brand waves-attach waves-effect" data-dismiss="modal" type="button">FECHAR</button></p>

 </div>
</div>
</div>
</div>
<div aria-hidden="true" class="modal fade" id="sieContratos" role="dialog" tabindex="-1">
  <div class="modal-dialog">
   <div class="modal-content">
    <div class="modal-heading">
     <a class="modal-close" data-dismiss="modal">×</a>
     <h2 class="modal-title">Serviço de Empenho SIE | Contratos</h2>
   </div>
   <div class="modal-inner" style="text-ident: 1.5em; text-align: justify;">
    <p>À medida que as solicitações de empenhos chegam no Serviço de Empenhos – Contratos, os mesmos são separados por: origem da solicitação, fornecedores e, em seguida, por ordem cronológica a fim de que, em um último momento, conforme disponibilidade orçamentária, os mesmos sejam lançados no Sistema Integrado para o Ensino – SIE, na forma de empenhos internos. <br><br>Logo, solicita-se a reclassificação orçamentária ao Serviço de Orçamento para o posterior envio dos empenhos internos ao Serviço de Empenhos SIAFI do HUSM.</p>
    <p><a class="btn btn-flat waves-attach waves effect" href="javascript:void(0);">Fluxograma em construção…</a></p>

  </div>
  <div class="modal-footer">
   <p class="text-right"><button class="btn btn-brand waves-attach waves-effect" data-dismiss="modal" type="button">FECHAR</button></p>

 </div>
</div>
</div>
</div>
<div aria-hidden="true" class="modal fade" id="empenhoSIAFI" role="dialog" tabindex="-1">
  <div class="modal-dialog">
   <div class="modal-content">
    <div class="modal-heading">
     <a class="modal-close" data-dismiss="modal">×</a>
     <h2 class="modal-title">Serviço de Empenho SIAFI</h2>
   </div>
   <div class="modal-inner" style="text-ident: 1.5em; text-align: justify;">
    <p>A principal atribuição desse serviço é emitir empenhos no Sistema de Administração de Serviços Gerais – SIASG e enviá-los ao Sistema de Administração Financeira do Governo Federal – SIAFI.<br><br>
      Assim, na medida em que os empenhos SIE chegam ao Serviço de Empenhos SIAFI, oriundos do Serviço de Análise e devidamente protocolados, é realizada a separação desses empenhos por unidade gestora (UG 153610 ou UG 155125), modalidade de empenho e nível de prioridade estabelecido pelo solicitante da compra.<br><br>
      Depois de gerada a minuta do empenho no SIASG, a mesma é enviada ao SIAFI e o empenho é impresso e conferido. Não existindo pendências, o número SIAFI é informado no sistema SIE e a nota de empenho segue para receber as assinaturas do Gestor Financeiro e do Ordenador de Despesas.<br><br>
      É importante destacar que, após assinados, os documentos são enviados à Unidade de Apoio para que sejam enviados aos fornecedores.<br><br>
      O Serviço de Empenhos SIAFI também tem como atribuição realizar as anulações de saldos de empenhos não utilizados no exercício, bem como os cancelamentos dos saldos de empenhos inscritos em restos a pagar.</p>
      <p>Para consultar empenhos emitidos no SIAFI, <a href="consultaspe.php">clique aqui</a>.</p>
      <p><a class="btn btn-flat waves-attach waves effect" href="javascript:void(0);">Fluxograma em construção…</a></p>

    </div>
    <div class="modal-footer">
     <p class="text-right"><button class="btn btn-brand waves-attach waves-effect" data-dismiss="modal" type="button">FECHAR</button></p>

   </div>
 </div>
</div>
</div>
<div aria-hidden="true" class="modal fade" id="servicoOrcamento" role="dialog" tabindex="-1">
  <div class="modal-dialog">
   <div class="modal-content">
    <div class="modal-heading">
     <a class="modal-close" data-dismiss="modal">×</a>
     <h2 class="modal-title">Serviço de Orçamento</h2>
   </div>
   <div class="modal-inner" style="text-ident: 1.5em; text-align: justify;">
    <p>Esse serviço tem a responsabilidade de realizar a liberação do orçamento referente ao período no Sistema Integrado de Administração Financeira do Governo Federal – SIAFI, bem como de providenciar as reclassificações e transferências orçamentárias.<br><br>
      Outra atribuição do Serviço de Orçamento é abrir o crédito orçamentário no Sistema Integrado de Ensino – SIE, viabilizando a execução da despesa realizada pelo Serviço de Empenhos SIE por meio da emissão de empenhos internos.<br><br>
      Nesse contexto, a liberação orçamentária acontece por Plano Interno – PI, a fim de que os valores comprometidos nos empenhos internos possam ser identificados de acordo com o solicitante da compra e a modalidade de fornecimento. <br><br>Uma vez liberados os créditos por PIs, os já referidos empenhos recebem as informações sobre fonte de recursos para, em seguida, serem tramitados ao Serviço de Análise, onde são conferidos e enviados ao Serviço de Empenhos SIAFI.</p>
      <p><a class="btn btn-flat waves-attach waves effect" href="javascript:void(0);">Fluxograma em construção…</a></p>

    </div>
    <div class="modal-footer">
     <p class="text-right"><button class="btn btn-brand waves-attach waves-effect" data-dismiss="modal" type="button">FECHAR</button></p>

   </div>
 </div>
</div>
</div>
  <!--


  MODAIS UNIDADE DE LIQUIDAÇÃO E PAGAMENTO DA DESPESA


-->
<div aria-hidden="true" class="modal fade" id="liqApresentacao" role="dialog" tabindex="-1">
  <div class="modal-dialog">
   <div class="modal-content">
    <div class="modal-heading">
     <a class="modal-close" data-dismiss="modal">×</a>
     <h2 class="modal-title">Unidade de Liquidação e Pagamentos do HUSM</h2>
   </div>
   <div class="modal-inner" style="text-ident: 1.5em; text-align: justify;">
    <p>Assim que as notas fiscais dão entrada na Unidade de Liquidação e Pagamentos, por meio do Serviço de Recepção do Setor de Orçamento e Finanças, estas são distribuídas entre o Serviço de Liquidação – Contratos, o Serviço de Liquidação – Registros de Preços e o Serviço de Suprimento de Fundos. <br><br>Logo, os documentos passam por um processo de conferência e de apropriação no Sistema Integrado de Administração Financeira do Governo Federal – SIAFI, sendo pagos de acordo com a disponibilidade financeira.</p>
    <p>Para consultar despesas liquidadas ou pagas, <a href="consultaspe.php">clique aqui</a>.</p>
  </div>
  <div class="modal-footer">
   <p class="text-right"><button class="btn btn-brand waves-attach waves-effect" data-dismiss="modal" type="button">FECHAR</button></p>

 </div>
</div>
</div>
</div>
<div aria-hidden="true" class="modal fade" id="servicoRelatorios" role="dialog" tabindex="-1">
  <div class="modal-dialog">
   <div class="modal-content">
    <div class="modal-heading">
     <a class="modal-close" data-dismiss="modal">×</a>
     <h2 class="modal-title">Serviço de Relatórios</h2>
   </div>
   <div class="modal-inner" style="text-ident: 1.5em; text-align: justify;">
    <p>O objetivo do Serviço de Relatórios do Setor de Orçamento e Finanças é respaldar a Equipe de Governança do Hospital Universitário de Santa Maria na tomada de decisão, bem como informar ao público interno e externo acerca do comprometimento orçamentário e financeiro do HUSM. <br><br>Para isso, o serviço de relatórios do SOF elaborou o I Caderno de Execução Orçamentária do HUSM que já se encontra disponível para apreciação neste site e terá periodicidade quadrimestral. Além do referido caderno, encontram-se em desenvolvimento o I Caderno de Execução Financeira do HUSM e o Caderno de Indicadores do SOF.</p>
    <p>Para obter um exemplar dos relatórios, <a href="faleconosco.php">clique aqui</a>.</p>
    <p><a class="btn btn-flat waves-attach waves effect" href="javascript:void(0);">Fluxograma em construção…</a></p>

  </div>
  <div class="modal-footer">
   <p class="text-right"><button class="btn btn-brand waves-attach waves-effect" data-dismiss="modal" type="button">FECHAR</button></p>

 </div>
</div>
</div>
</div>
<div aria-hidden="true" class="modal fade" id="servicoLiqContratos" role="dialog" tabindex="-1">
  <div class="modal-dialog">
   <div class="modal-content">
    <div class="modal-heading">
     <a class="modal-close" data-dismiss="modal">×</a>
     <h2 class="modal-title">Serviço de Liquidação de Notas Fiscais | Contratos</h2>
   </div>
   <div class="modal-inner" style="text-ident: 1.5em; text-align: justify;">
    <p>É responsável pela conferência e liquidação de Notas Fiscais referentes a Contratos
      Administrativos, sendo que a liquidação tem por objetivo verificar o direito adquirido
      do credor em receber o seu pagamento. <br><br>Vale destacar que o Serviço de Liquidação
      de Notas Fiscais – Contratos não tem a atribuição de realizar a gestão ou fiscalização
      dos contratos. Essa responsabilidade fica a cargo do Gestor e/ou Fiscal devidamente
      designado para essa função. <br><br>O que é inerente à
      <a class="btn waves-attach waves-effect" data-toggle="collapse" href="#faseConf">fase da conferência e da liquidação</a>
      diz respeito ao confronto da nota fiscal com seu respectivo contrato, a verificação da
      anuência do gestor do contrato diante dos documentos em questão, bem como ao lançamento
      dessas informações no Sistema de Informações Gerencias – SIG EBSERH e no Sistema Integrado
      de Administração Financeira do Governo Federal – SIAFI.
    </p>
    <div class="collapse collapsible-region" id="faseConf">
      <div class="card">
        <div class="card-main">
          <div class="card-inner">
            <p class="card-heading">Conferência e Liquidação de Notas Fiscais</p>
            <p>Após as notas fiscais darem entrada no Serviço de Liquidação de Notas Fiscais de Contratos,
              estas são inseridas em planilhas de controles e passam por uma conferência embasada em itens
              de um Check- List a fim de se verificar se as notas estão aptas a serem lançadas no SIAFI.
              <br><br>Por conseguinte, é realizada a verificação do saldo do empenho e, em caso de saldo
              suficiente, dá-se prosseguimento na liquidação. No caso de insuficiência de saldo, mas com
              disponibilidade orçamentária, é solicitado o reforço necessário ao empenho para o Serviço de
              Empenhos SIE – Contratos, da Unidade de Programação Orçamentária e Financeira. <br><br>
              Uma vez o saldo orçamentário disponibilizado, são anexadas as situações do Simples Nacional e do
              Sistema de Cadastramento Unificado de Fornecedores – SICAF para que as notas sejam lançadas no
              SIAFI e sejam geradas as Notas de Sistema – NSs. <br><br>Então, as NSs são conferidas e
              encaminhadas, com as notas fiscais, para serem pagas pelo Serviço de Pagamentos do HUSM.
            </p>
          </div>
        </div>
      </div>
    </div>
    <p>Para consultar despesas liquidadas, <a href="consultaspe.php">clique aqui</a>.</p>

    <p><a class="btn btn-flat waves-attach waves effect" href="javascript:void(0);">Fluxograma em construção…</a></p>

    <p><strong>Arquivos para download</strong>:<br><br>
      <a href="../uploads/Planilha-Diferenças-Reequilíbrio-Econômico-Financeiro.xls">Planilha Diferenças Reequilíbrio Econômico Financeiro</a></p>

    </div>
    <div class="modal-footer">
     <p class="text-right"><button class="btn btn-brand waves-attach waves-effect" data-dismiss="modal" type="button">FECHAR</button></p>

   </div>
 </div>
</div>
</div>
<div aria-hidden="true" class="modal fade" id="liqRegPrecos" role="dialog" tabindex="-1">
  <div class="modal-dialog">
   <div class="modal-content">
    <div class="modal-heading">
     <a class="modal-close" data-dismiss="modal">×</a>
     <h2 class="modal-title">Serviço de Liquidação | Registro de Preços</h2>
   </div>
   <div class="modal-inner" style="text-ident: 1.5em; text-align: justify;">
    <p>Quando as notas fiscais chegam ao Serviço de Liquidação – Registros de Preços, são anexadas ao seu respectivo empenho para conferência de dados e, também, para verificação da necessidade de retenção de tributos conforme a legislação vigente. <br><br>Cabe ressaltar que a retenção de tributos é feita sobre os pagamentos efetuados às pessoas jurídicas conforme o que dispõe a IN RFB Nº 1.234/12, enquanto a conferência de dados tem como base itens de um Check- List. <br><br>A seguir, as notas fiscais são lançadas no Sistema Integrado de Administração Financeira do Governo Federal – SIAFI efetuando-se o segundo estágio da despesa orçamentária pública, prevista na Lei nº 4.320/64. <br><br>Posteriormente, são lançadas todas as notas fiscais em uma planilha de controle financeiro que tem o objetivo de controlar o montante de recursos financeiros esperados do Governo Federal, bem como de manter a ordem cronológica das notas fiscais a serem encaminhadas para o pagamento. <br><br>Para finalizar, é feita a conferência da liquidação das notas fiscais e o encaminhamento dessas notas ao Serviço de Pagamentos.</p>
    <p>Para consultar despesas já liquidadas, <a href="consultaspe.php">clique aqui</a>.</p>
    <p><a class="btn btn-flat waves-attach waves effect" href="javascript:void(0);">Fluxograma em construção…</a></p>
  </div>
  <div class="modal-footer">
   <p class="text-right"><button class="btn btn-brand waves-attach waves-effect" data-dismiss="modal" type="button">FECHAR</button></p>

 </div>
</div>
</div>
</div>
<div aria-hidden="true" class="modal fade" id="servicoPagamentos" role="dialog" tabindex="-1">
  <div class="modal-dialog">
   <div class="modal-content">
    <div class="modal-heading">
     <a class="modal-close" data-dismiss="modal">×</a>
     <h2 class="modal-title">Serviço de Pagamentos</h2>
   </div>
   <div class="modal-inner" style="text-ident: 1.5em; text-align: justify;">
    <p>O Serviço de Pagamentos recebe as notas fiscais oriundas dos Serviços de Liquidação – Registro de Preços e de Liquidação – Contratos, e realiza a verificação e o acompanhamento da disponibilidade de recursos financeiros a serem liberados pelo Governo Federal por meio de planilhas de controle financeiro e do Sistema Integrado de Administração Financeira – SIAFI. <br><br>Diante da disponibilidade de recursos, as notas fiscais são lançadas no SIAFI, a fim de serem pagas, respeitando-se a ordem cronológica do fato gerador da obrigação. Esses procedimentos geram as Ordens Bancárias – OBs que são encaminhadas ao Banco do Brasil, através de uma Relação de Ordem Bancária Externa – RE e, em situações normais, as OBs são compensadas na conta do fornecedor em dois dias úteis, a partir da data do pagamento. <br><br>O Serviço de Pagamentos também é responsável por disponibilizar aos fornecedores os comprovantes de retenções para declaração do Imposto de Renda.</p>
    <p>Para consultar pagamentos, <a href="consultaspe.php">clique aqui</a>.</p>
    <p><a class="btn btn-flat waves-attach waves effect" href="javascript:void(0);">Fluxograma em construção…</a></p>

  </div>
  <div class="modal-footer">
   <p class="text-right"><button class="btn btn-brand waves-attach waves-effect" data-dismiss="modal" type="button">FECHAR</button></p>

 </div>
</div>
</div>
</div>
<div aria-hidden="true" class="modal fade" id="suprimentosFundo" role="dialog" tabindex="-1">
  <div class="modal-dialog">
   <div class="modal-content">
    <div class="modal-heading">
     <a class="modal-close" data-dismiss="modal">×</a>
     <h2 class="modal-title">Serviço de Suprimento de Fundo</h2>
   </div>
   <div class="modal-inner" style="text-ident: 1.5em; text-align: justify;">
    <p>Visando o pronto pagamento de despesas de pequeno vulto, de caráter excepcional e com prazo certo de aplicação e comprovação de gastos, o Suprimento de Fundos é concedido a servidor devidamente cadastrado para tal. <br><br>O servidor solicitará, por escrito, a liberação do crédito para as referidas despesas que terá um prazo máximo de 90 dias para ser utilizado. <br><br>Mensalmente, o servidor deverá prestar contas do valor gasto, até a utilização total do recurso ou até o término da concessão. <br><br>A prestação de contas será conferida e as notas fiscais pagas, bem como suas retenções realizadas, através do Sistema Integrado de Administração Financeira do Governo Federal – SIAFI. <br><br>É possível que o encerramento do processo seja solicitado a qualquer momento. Então, o processo será conferido em sua totalidade, anulando-se o saldo restante, se houver e, encaminhando-se a documentação para arquivamento.<br><br>
      Para visualizar empenhos emitidos para suprimento de fundos, <a href="consultaspe.php">clique aqui</a>.</p>
      <p><a class="btn btn-flat waves-attach waves effect" href="javascript:void(0);">Fluxograma em construção…</a></p>

      <div class="card">
        <div class="card-main">
          <div class="card-inner">
            <p class="card-heading">Formulários</p>
            <div class="card-table">
              <div class="table-responsive">
               <table class="table">
                <tbody>
                 <tr>
                  <td><a class="btn btn-flat waves-attach" href="../uploads/AUTORIZACAO-EM-FOLHA-DE-PAGAMENTO.pdf" target="_blank">Autorização para Desconto em Folha</a></td>
                  <td><a class="btn btn-flat waves-attach" href="../uploads/AUTORIZACAO-EM-FOLHA-DE-PAGAMENTO.doc">Download p/ edição</a></td>
                </tr>
                <tr>
                  <td><a class="btn btn-flat waves-attach" href="../uploads/DECLARACAO-SUPRIDO-PROPONENTE-CONSUMO.pdf" target="_blank">Declaração suprido/proponente</a></td>
                  <td><a class="btn btn-flat waves-attach" href="../uploads/DECLARACAO-SUPRIDO-PROPONENTE-CONSUMO.doc">Download p/ edição</a></td>
                </tr>
                <tr>
                  <td><a class="btn btn-flat waves-attach" href="../uploads/PARECER.pdf" target="_blank">Parecer</a></td>
                  <td><a class="btn btn-flat waves-attach" href="../uploads/PARECER.doc">Download p/ edição</a></td>
                </tr>
                <tr>
                  <td><a class="btn btn-flat waves-attach" href="../uploads/RECIBO-CONSUMO.pdf" target="_blank">Recibo consumo</a></td>
                  <td><a class="btn btn-flat waves-attach" href="../uploads/RECIBO-CONSUMO.doc">Download p/ edição</a></td>
                </tr>
                <tr>
                  <td><a class="btn btn-flat waves-attach" href="../uploads/RECIBO-SERVICO.pdf" target="_blank">Recibo serviço</a></td>
                  <td><a class="btn btn-flat waves-attach" href="../uploads/RECIBO-SERVICO.doc">Download p/ edição</a></td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="modal-footer">
 <p class="text-right"><button class="btn btn-brand waves-attach waves-effect" data-dismiss="modal" type="button">FECHAR</button></p>

</div>
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

<script type="text/javascript" src="../ini.min.js"></script>
</body>
</html>
