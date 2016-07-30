<?php
session_start();

ini_set('display_erros', true);
error_reporting(E_ALL);

require_once '../defines.php';
//define se o botão de LOGIN deve ou não existir
$btnLogin = true;
//define se o botão de ADMIN deve ou não existir
$btnAdmin = false;
//define se o botão sair deve ou não existir
$btnSair = false;
$hrefSolicitacoes = HREF_MODAL_LOGIN;
if (!isset($_SESSION["id_setor"]) || $_SESSION["id_setor"] == 0) {
	$_SESSION["id_setor"] = 0;
} else {
	//o botão admin deve existir
	if ($_SESSION["id_setor"] == 2) {
		$btnAdmin = true;
	}
	$hrefSolicitacoes = HREF_SOLICITACOES;
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
    <nav class="tab-nav pull-right hidden-xx">
     <ul class="nav nav-list">
      <?php
if ($btnAdmin) {
	echo "
       <li class=\"active\">
        <a class=\"btn btn-flat waves-attach waves-light\" href=\"../admin/\"><span class=\"text-white\"><span class=\"icon\">power_settings_new</span>ADMIN</span></a>
      </li>
      ";
}
if ($btnLogin) {
	echo "
     <li class=\"active\">
      <a class=\"btn btn-flat waves-attach waves-light\" href=" . HREF_MODAL_LOGIN . "><span class=\"text-white\"><span class=\"icon\">power_settings_new</span>LOGIN</span></a>
    </li>
    ";
}
if ($btnSair) {
	echo "
   <li>
    <a class=\"btn btn-flat waves-attach waves-light\" href=\"../admin/sair.php\"><span class=\"text-white\"><span class=\"icon\">undo</span>SAIR</span></a>
  </li>
  ";
}
?>
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
              <a class="waves-attach" href="<?php echo HREF_SOF ?>">SOF</a>
            </li>
            <li>
              <a class="waves-attach" href="<?php echo HREF_RECEPCAO ?>">Recepção</a>
            </li>
            <li>
              <a class="waves-attach" href="<?php echo HREF_UNIDADES ?>">Unidades</a>
            </li>
          </ul>
        </li>
        <li>
          <a class="collapsed waves-attach" data-toggle="collapse" href="#servicossof"><span class="text-black"><span class="icon">payment</span>SERVIÇOS DO SOF</a>
          <ul class="menu-collapse collapse" id="servicossof">
            <li>
              <a class="waves-attach" href="<?php echo $hrefSolicitacoes ?>">SOLICITAÇÕES</a>
            </li>
          </ul>
        </li>
        <li>
          <a class="collapsed waves-attach" data-toggle="collapse" href="#mconsultas"><span class="text-black"><span class="icon">build</span>CONSULTAS</a>
          <ul class="menu-collapse collapse" id="mconsultas">
            <li>
              <a class="waves-attach" href="<?php echo HREF_CONSULTAS_PE ?>">PÚBLICO EXTERNO</a>
            </li>
            <li>
              <a class="waves-attach" href="<?php echo HREF_MODAL_CONSTRUINDO ?>">PÚBLICO INTERNO</a>
            </li>
          </ul>
        </li>
        <li>
          <a class="waves-attach waves-light" href="<?php echo HREF_RELATORIOS ?>"><span class="text-black"><span class="icon">folder</span>RELATÓRIOS</span></a>
        </li>
        <li>
          <a class="collapsed waves-attach" data-toggle="collapse" href="#mlinks"><span class="text-black"><span class="icon">near_me</span>LINKS ÚTEIS</a>
          <ul class="menu-collapse collapse" id="mlinks">
            <li>
              <a class="waves-attach" href="<?php echo LINKS_EXTERNOS ?>">LINKS EXTERNOS</a>
            </li>
            <li>
              <a class="waves-attach" href="<?php echo LINKS_INTERNOS ?>">LINKS INTERNOS</a>
            </li>
            <li>
              <a class="waves-attach" href="<?php echo HREF_TUTORIAIS ?>">POPs E TUTORIAIS</a>
            </li>
          </ul>
        </li>
        <li>
          <a class="waves-attach waves-light" href="<?php echo HREF_NOTICIAS ?>"><span class="text-black"><span class="icon">event</span>NOTÍCIAS</span></a>
        </li>
        <li>
          <a class="collapsed waves-attach" data-toggle="collapse" href="#mencontros"><span class="text-black"><span class="icon">place</span>ENCONTROS</a>
          <ul class="menu-collapse collapse" id="mencontros">
            <li>
              <a class="waves-attach" href="<?php echo HREF_BOAS_PRATICAS ?>">BOAS PRÁTICAS</a>
            </li>
            <li>
              <a class="waves-attach" href="<?php echo HREF_COMEMORACOES ?>">COMEMORAÇÕES</a>
            </li>
            <li>
              <a class="waves-attach" href="<?php echo HREF_DINAMICAS ?>">DINÂMICAS DE GRUPO</a>
            </li>
          </ul>
        </li>
        <li>
          <a class="waves-attach waves-light"  href="<?php echo HREF_FALE_CONOSCO ?>"><span class="text-black"><span class="icon">chat</span>CONTATO</span></a>
        </li>
      </ul>
    </div>
  </div>
</nav>
<main class="content">
  <div class="content-heading">
    <div class="container">
      <div class="row">
        <h1 class="heading module wow bounceIn animated"><img src="../sof_files/logo_blue.png" alt="Setor de Orçamento e Finanças – HUSM" /></h1>
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
            <a class="waves-attach" href="<?php echo HREF_SOF ?>">SOF</a>
          </li>
          <li>
            <a class="waves-attach" href="<?php echo HREF_RECEPCAO ?>">Recepção</a>
          </li>
          <li>
            <a class="waves-attach" href="<?php echo HREF_UNIDADES ?>">Unidades</a>
          </li>
        </ul>
      </div>
    </li>
    <li>
      <div class="dropdown dropdown-inline">
        <a class="waves-attach" data-toggle="dropdown"><span class="text-white"><span class="icon">payments</span>SERVIÇOS SOF</span><span class="icon margin-left-sm">keyboard_arrow_down</span></a>
        <ul class="dropdown-menu nav">
          <li>
            <a class="waves-attach" href="<?php echo $hrefSolicitacoes ?>">Solicitações</a>
          </li>
        </ul>
      </div>
    </li>
    <li>
      <div class="dropdown dropdown-inline">
        <a class="waves-attach" data-toggle="dropdown"><span class="text-white"><span class="icon">build</span>CONSULTAS</span><span class="icon margin-left-sm">keyboard_arrow_down</span></a>
        <ul class="dropdown-menu nav">
          <li>
            <a class="waves-attach" href="<?php echo HREF_CONSULTAS_PE ?>">Público Externo</a>
          </li>
          <li>
            <a class="waves-attach" href="<?php echo HREF_MODAL_CONSTRUINDO ?>">Público Interno</a>
          </li>
        </ul>
      </div>
    </li>
    <li>
     <a class="waves-attach waves-light" href="<?php echo HREF_RELATORIOS ?>"><span class="text-white"><span class="icon">folder</span>RELATÓRIOS</span></a>
   </li>
   <li>
    <div class="dropdown dropdown-inline">
      <a class="waves-attach" data-toggle="dropdown"><span class="text-white"><span class="icon">near_me</span>LINKS ÚTEIS</span><span class="icon margin-left-sm">keyboard_arrow_down</span></a>
      <ul class="dropdown-menu nav">
        <li>
          <a class="waves-attach" href="<?php echo HREF_LINKS_EXTERNOS ?>">Links Externos</a>
        </li>
        <li>
          <a class="waves-attach" href="<?php echo HREF_LINKS_INTERNOS ?>">Links Internos</a>
        </li>
        <li>
          <a class="waves-attach" href="<?php echo HREF_TUTORIAIS ?>">POPs e Tutoriais</a>
        </li>
      </ul>
    </div>
  </li>
  <li>
   <a class="waves-attach waves-light" href="<?php echo HREF_NOTICIAS ?>"><span class="text-white"><span class="icon">event</span>NOTÍCIAS</span></a>
 </li>
 <li>
  <div class="dropdown dropdown-inline">
    <a class="waves-attach" data-toggle="dropdown"><span class="text-white"><span class="icon">place</span>ENCONTROS</span><span class="icon margin-left-sm">keyboard_arrow_down</span></a>
    <ul class="dropdown-menu nav">
      <li>
        <a class="waves-attach" href="<?php echo HREF_BOAS_PRATICAS ?>">Boas Práticas</a>
      </li>
      <li>
        <a class="waves-attach" href="<?php echo HREF_COMEMORACOES ?>">Comemorações</a>
      </li>
      <li>
        <a class="waves-attach" href="<?php echo HREF_DINAMICAS ?>">Dinâmicas de grupos</a>
      </li>
    </ul>
  </div>
</li>
<li>
 <a class="waves-attach waves-light"  href="<?php echo HREF_FALE_CONOSCO ?>"><span class="text-white"><span class="icon">chat</span>CONTATO</span></a>
</li>
</ul>
</nav>
</div><!-- ./row -->
<div class="row">
  <?php
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
        <textarea class="form-control textarea-autosize" id="mensagem" name="mensagem" rows="1"></textarea>
      </div>
      <input id="qtd-arquivos" name="qtd-arquivos" type="hidden" value="0">
      <button class="btn waves-attach" type="button" onclick="addInputsArquivo();"><span class="text-black"><span class="icon">add</span>Anexar Arquivos</span></button>
      <div class="form-group">
       <table class="table margin-top-no" id="arquivos">

       </table>
       <p class="help-block">Max. 32MB</p>
     </div>
     <span class="label label-orange" style="font-size: 11pt;"><span class="icon">warning</span>Se quiser excluir um arquivo anexado, por favor, comece excluindo de baixo para cima</span><br><br>
     <span class="label">* Campo Obrigatório</span>
   </div><!-- ./card-inner -->
   <div class="card-action">
    <div class="card-action-btn">
     <button class="btn btn-brand waves-attach" type="submit" style="width: 100%;"><span class="icon">check</span>&nbsp;Enviar</button>
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

<script type="text/javascript" src="../ini.min.js"></script>
</body>
</html>
