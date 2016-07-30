<?php
ini_set('display_erros', true);
error_reporting(E_ALL);

session_start();
if (!isset($_SESSION["id_setor"]) || $_SESSION["id_setor"] != 2) {
	header("Location: ../");
}
include_once '../class/Busca.class.php';
//instanciando classe de busca para popular o select de estados
$obj_Busca = new Busca();
$permissao = $obj_Busca->getPermissoes($_SESSION["id"]);
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
</head>

<body class="page-brand" onload="iniAdminSolicitacoes();">
  <input id="div_ajax" type="hidden">
  <header class="header header-transparent header-waterfall affix">
    <ul class="nav nav-list pull-left">
      <?php
if ($permissao->noticias) {
	echo "
       <li>
        <a class=\"btn btn-flat waves-attach waves-light\" href=\"index.php\">
          <span class=\"text-white\"><span class=\"icon icon-lg\">undo</span>VOLTAR</span>
        </a>
      </li>
      ";
}
?>
    <li>
     <a class="btn btn-flat waves-attach waves-light" href="javascript:abreModal('#redefinirSenha');"><span class="text-white"><span class="icon">lock_outline</span>REDEFINIR SENHA</span></a>
   </li>
 </ul>
 <nav class="tab-nav pull-right hidden-xx">
  <ul class="nav nav-list">
    <li>
      <a class="btn btn-flat waves-attach waves-light" href="sair.php"><span class="text-white"><span class="icon">power_settings_new</span>SAIR</span></a>
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
          <a class="waves-attach" href="../view/"><span style="color: white;"><span class="icon">home</span>INÍCIO</span></a>
        </li>
        <li class="active">
          <a class="waves-attach" href="adminsolicitacoes.php"><span style="color: white;"><span class="icon">payment</span>SOLICITAÇÕES</span></a>
        </li>
        <?php
if ($permissao->saldos) {
	echo "
         <li>
          <a class=\"waves-attach\" href=\"javascript:abreModal('#freeSaldos');\"><span style=\"color: white;\"><span class=\"icon\">payment</span>LIBERAR SALDOS</span></a>
        </li>
        ";
}
?>
    </ul>
  </nav>
</div><!-- ./row -->
<?php
if ($permissao->saldos) {
	echo "
  <div class=\"row\">
   <div class=\"card margin-top-no\">
     <div class=\"card-main\">
       <div class=\"card-header card-brand\">
         <div class=\"card-header-side pull-left\">
           <p class=\"card-heading\">Solicitações de Adiantamento</p>
         </div>
       </div><!--  ./card-header -->
       <div class=\"card-inner\">
        <table class=\"table\">
          <tr>
            <td>
              <div class=\"radiobtn radiobtn-adv\">
                <label for=\"stabertos\">
                  <input type=\"radio\" id=\"stabertos\" name=\"stadi\" class=\"access-hide\" onclick=\"iniTableSolicAdiant(2);\" checked>Abertos
                  <span class=\"radiobtn-circle\"></span><span class=\"radiobtn-circle-check\"></span>
                </label>
              </div>
            </td>
            <td>
              <div class=\"radiobtn radiobtn-adv\">
                <label for=\"staprovados\">
                  <input type=\"radio\" id=\"staprovados\" name=\"stadi\" class=\"access-hide\" onclick=\"iniTableSolicAdiant(1);\">Aprovados
                  <span class=\"radiobtn-circle\"></span><span class=\"radiobtn-circle-check\"></span>
                </label>
              </div>
            </td>
            <td>
              <div class=\"radiobtn radiobtn-adv\">
                <label for=\"streprovado\">
                  <input type=\"radio\" id=\"streprovado\" name=\"stadi\" class=\"access-hide\" onclick=\"iniTableSolicAdiant(0);\">Reprovados
                  <span class=\"radiobtn-circle\"></span><span class=\"radiobtn-circle-check\"></span>
                </label>
              </div>
            </td>
          </tr>
        </table>
        <table class=\"table stripe\" id=\"tableSolicitacoesAdiantamento\" style=\"width: 100%;\">
          <thead>
            <th></th>
            <th>SETOR</th>
            <th>DATA_SOLICITACAO</th>
            <th>DATA_ANALISE</th>
            <th>MES_SUBTRAIDO</th>
            <th>VALOR_ADIANTADO</th>
            <th>JUSTIFICATIVA</th>
            <th>STATUS</th>
          </thead>
          <tbody id=\"conteudoSolicitacoesAdiantamento\">

          </tbody>
        </table>
      </div><!-- ./card-inner -->
    </div><!-- ./card-main -->
  </div> <!-- ./card -->
</div>
";
}
?>
<?php
if ($permissao->pedidos) {
	echo "
  <div class=\"row\">
   <div class=\"card margin-top-no\">
     <div class=\"card-main\">
       <div class=\"card-header card-brand\">
         <div class=\"card-header-side pull-left\">
           <p class=\"card-heading\">Solicitações de Alteração de Pedidos</p>
         </div>
       </div><!--  ./card-header -->
       <div class=\"card-inner\">
        <table class=\"table\">
          <tr>
            <td>
              <div class=\"radiobtn radiobtn-adv\">
                <label for=\"stAltAbertos\">
                  <input type=\"radio\" id=\"stAltAbertos\" name=\"stAlt\" class=\"access-hide\" onclick=\"iniTableSolicAltPed(2);\" checked>Abertos
                  <span class=\"radiobtn-circle\"></span><span class=\"radiobtn-circle-check\"></span>
                </label>
              </div>
            </td>
            <td>
              <div class=\"radiobtn radiobtn-adv\">
                <label for=\"stAltAprovados\">
                  <input type=\"radio\" id=\"stAltAprovados\" name=\"stAlt\" class=\"access-hide\" onclick=\"iniTableSolicAltPed(1);\">Aprovados
                  <span class=\"radiobtn-circle\"></span><span class=\"radiobtn-circle-check\"></span>
                </label>
              </div>
            </td>
            <td>
              <div class=\"radiobtn radiobtn-adv\">
                <label for=\"stAltReprovado\">
                  <input type=\"radio\" id=\"stAltReprovado\" name=\"stAlt\" class=\"access-hide\" onclick=\"iniTableSolicAltPed(0);\">Reprovados
                  <span class=\"radiobtn-circle\"></span><span class=\"radiobtn-circle-check\"></span>
                </label>
              </div>
            </td>
          </tr>
        </table>
        <table id=\"tableSolicAltPedido\" class=\"table\" style=\"width: 100%;\">
          <thead>
            <th></th>
            <th>#PEDIDO</th>
            <th>SETOR</th>
            <th>DATA_SOLICITACAO</th>
            <th>DATA_ANALISE</th>
            <th>JUSTIFICATIVA</th>
            <th>STATUS</th>
          </thead>
          <tbody id=\"contSolicAltPedido\">

          </tbody>
        </table>
      </div><!-- ./card-inner -->
    </div><!-- ./card-main -->
  </div> <!-- ./card -->
</div> <!-- ./row -->

<!-- TABELA COM OS PEDIDOS ENVIADOS AO SOF -->

<div class=\"row\">
 <div id=\"card\" class=\"card margin-top-no\">
   <div class=\"card-main\">
     <div class=\"card-header card-brand\">
       <div class=\"card-header-side pull-left\">
         <p class=\"card-heading\">Pedidos</p>
       </div>
     </div><!--  ./card-header -->
     <div class=\"card-inner\">
      <table class=\"table stripe\" id=\"tableSolicitacoes\" style=\"width: 100%;\">
        <thead>
          <th></th>
          <th>#PEDIDO</th>
          <th>SETOR</th>
          <th>DATA_PEDIDO</th>
          <th>REF_MES</th>
          <th>PRIORIDADE</th>
          <th>STATUS</th>
          <th>VALOR</th>
        </thead>
        <tbody id=\"conteudoSolicitacoes\">

        </tbody>
      </table>
    </div><!-- ./card-inner -->
  </div><!-- ./card-main -->
</div> <!-- ./card -->
</div> <!-- ./row -->

<!-- DETALHES DO PEDIDO -->

<div class=\"row\">
  <div class=\"card margin-top-no\">
   <div class=\"card-main\">
     <div class=\"card-header card-brand\">
       <div class=\"card-header-side pull-left\">
         <p class=\"card-heading\">Detalhes do Pedido</p>
       </div>
       <div class=\"card-header-side pull-right\" style=\"margin-left: 70%;\">
         <p class=\"card-heading\">SALDO <span id=\"text_saldo_total\">R$ 0.000<span></p>
       </div>
     </div><!--  ./card-header -->
     <form action=\"../php/geral.php\" method=\"POST\">
      <input type=\"hidden\" name=\"admin\" value=\"1\"></input>
      <input type=\"hidden\" name=\"form\" value=\"gerenciaPedido\"></input>
      <input id=\"id_pedido\" type=\"hidden\" name=\"id_pedido\" value=\"0\"></input>
      <input id=\"id_setor\" type=\"hidden\" name=\"id_setor\" value=\"0\"></input>
      <div class=\"card-inner\">
        <table class=\"table stripe\" id=\"tableItensPedido\">
          <thead>
            <th></th>
            <th>COD_REDUZIDO</th>
            <th>COD_DESPESA</th>
            <th>DESCRICAO_DESPESA</th>
            <th>NUM_CONTRATO</th>
            <th>NUM_PROCESSO</th>
            <th>DESCR_MOD_COMPRA</th>
            <th>NUM_LICITACAO</th>
            <th>DT_INICIO</th>
            <th>DT_FIM</th>
            <th>DT_GERACAO</th>
            <th>CGC_FORNECEDOR</th>
            <th>NOME_FORNECEDOR</th>
            <th>NUM_EXTRATO</th>
            <th>COD_ESTRUTURADO</th>
            <th>NOME_UNIDADE</th>
            <th>COMPLEMENTO_ITEM</th>
            <th>DESCRICAO</th>
            <th>VL_UNITARIO</th>
            <th>QT_CONTRATO</th>
            <th>VL_CONTRATO</th>
            <th>QT_UTILIZADO</th>
            <th>VL_UTILIZADO</th>
            <th>QT_SALDO</th>
            <th>VL_SALDO</th>
            <th>QT_SOLICITADA</th>
            <th>VALOR</th>
            <th></th>
          </thead>
          <tbody id=\"conteudoPedido\">

          </tbody>
        </table>
        <table class=\"table\">
          <tr>
            <td>
              <div class=\"form-group form-group-label control-highlight\">
                <label class=\"floating-label padding-left\" for=\"refMes\" style=\"font-size: 14pt;\"><span class=\"icon\">comment</span>&nbsp;Referente ao mês de</label>
                <input class=\"form-control padding-left\" id=\"refMes\" name=\"refMes\" style=\"font-size: 14pt;\" type=\"text\" disabled value=\"--------------------\" required>
              </div>
            </td>
            <td>
              <div class=\"form-group form-group-label control-highlight\">
                <label class=\"floating-label padding-left\" for=\"total\" style=\"font-size: 14pt;\"><span class=\"icon\">attach_money</span>&nbsp;Total</label>
                <input class=\"form-control padding-left\" id=\"total\" name=\"total\" style=\"font-size: 14pt;\" type=\"text\" disabled value=\"R$ 0\" required>
              </div>
            </td>
          </tr>
          <input id=\"total_hidden\" type=\"hidden\" name=\"total_hidden\" value=\"0\">
          <input id=\"saldo_total\" type=\"hidden\" name=\"saldo_total\" value=\"0.000\">
        </table>
        <table class=\"table\">
          <tr>
            <td>
              <div class=\"radiobtn radiobtn-adv\">
                <label for=\"stnormal\">
                  <input type=\"radio\" name=\"prioridade\" required=\"true\" id=\"stnormal\" class=\"access-hide\" value=\"normal\">Normal
                  <span class=\"radiobtn-circle\"></span><span class=\"radiobtn-circle-check\"></span>
                </label>
              </div>
            </td>
            <td>
              <div class=\"radiobtn radiobtn-adv\">
                <label for=\"stpreferencial\">
                  <input type=\"radio\" name=\"prioridade\" required=\"true\" id=\"stpreferencial\" class=\"access-hide\" value=\"preferencial\">Preferencial
                  <span class=\"radiobtn-circle\"></span><span class=\"radiobtn-circle-check\"></span>
                </label>
              </div>
            </td>
            <td>
              <div class=\"radiobtn radiobtn-adv\">
                <label for=\"sturgente\">
                  <input type=\"radio\" name=\"prioridade\" required=\"true\" id=\"sturgente\" class=\"access-hide\" value=\"urgente\" >Urgente
                  <span class=\"radiobtn-circle\"></span><span class=\"radiobtn-circle-check\"></span>
                </label>
              </div>
            </td>
            <td>
              <div class=\"radiobtn radiobtn-adv\">
                <label for=\"stemergencial\">
                  <input type=\"radio\" name=\"prioridade\" required=\"true\" id=\"stemergencial\" class=\"access-hide\" value=\"emergencial\" >Emergencial
                  <span class=\"radiobtn-circle\"></span><span class=\"radiobtn-circle-check\"></span>
                </label>
              </div>
            </td>
          </tr>
        </table>
        <table class=\"table\">
          <tr>
            <td>
              <div class=\"radiobtn radiobtn-adv\">
                <label for=\"stRascunho\">
                  <input type=\"radio\" name=\"fase\" required id=\"stRascunho\" class=\"access-hide\" value=\"rascunho\">Retornado
                  <span class=\"radiobtn-circle\"></span><span class=\"radiobtn-circle-check\"></span>
                </label>
              </div>
            </td>
            <td>
              <div class=\"radiobtn radiobtn-adv\">
                <label for=\"stEmAnalise\">
                  <input type=\"radio\" name=\"fase\" required id=\"stEmAnalise\" class=\"access-hide\" value=\"Em Analise\">Em Análise
                  <span class=\"radiobtn-circle\"></span><span class=\"radiobtn-circle-check\"></span>
                </label>
              </div>
            </td>
            <td>
              <div class=\"radiobtn radiobtn-adv\">
                <label for=\"stAprovado\">
                  <input type=\"radio\" name=\"fase\" required id=\"stAprovado\" class=\"access-hide\" value=\"Aprovado\">Aprovado
                  <span class=\"radiobtn-circle\"></span><span class=\"radiobtn-circle-check\"></span>
                </label>
              </div>
            </td>
            <td>
              <div class=\"radiobtn radiobtn-adv\">
                <label for=\"stReprovado\">
                  <input type=\"radio\" name=\"fase\" required id=\"stReprovado\" class=\"access-hide\" value=\"Reprovado\">Reprovado
                  <span class=\"radiobtn-circle\"></span><span class=\"radiobtn-circle-check\"></span>
                </label>
              </div>
            </td>
          </tr>
          <tr>
            <td>
              <div class=\"radiobtn radiobtn-adv\">
                <label for=\"stAguardaOrcamento\">
                  <input type=\"radio\" name=\"fase\" required id=\"stAguardaOrcamento\" class=\"access-hide\" value=\"Aguarda Orcamento\">Aguarda Orçamento
                  <span class=\"radiobtn-circle\"></span><span class=\"radiobtn-circle-check\"></span>
                </label>
              </div>
            </td>
            <td>
              <div class=\"radiobtn radiobtn-adv\">
                <label for=\"stEmpenhado\">
                  <input type=\"radio\" name=\"fase\" required id=\"stEmpenhado\" class=\"access-hide\" value=\"Empenhado\">Empenhado
                  <span class=\"radiobtn-circle\"></span><span class=\"radiobtn-circle-check\"></span>
                </label>
              </div>
            </td>
            <td>
              <div class=\"radiobtn radiobtn-adv\">
                <label for=\"stEnviadoAoOrdenador\">
                  <input type=\"radio\" name=\"fase\" required id=\"stEnviadoaoOrdenador\" class=\"access-hide\" value=\"Enviado ao Ordenador\">Enviado Ao Ordenador
                  <span class=\"radiobtn-circle\"></span><span class=\"radiobtn-circle-check\"></span>
                </label>
              </div>
            </td>
            <td>
              <div class=\"radiobtn radiobtn-adv\">
                <label for=\"stRecebidodaUnidadedeAprovacao\">
                  <input type=\"radio\" name=\"fase\" required id=\"stRecebidodaUnidadedeAprovacao\" class=\"access-hide\" value=\"Recebido da Unidade de Aprovacao\">Recebido da Unidade de Aprovação
                  <span class=\"radiobtn-circle\"></span><span class=\"radiobtn-circle-check\"></span>
                </label>
              </div>
            </td>
          </tr>
        </table>
        <div class=\"form-group form-group-label\">
          <label class=\"floating-label\" for=\"comentario\"><span class=\"icon\">announcement</span>&nbsp;Comentário</label>
          <textarea class=\"form-control textarea-autosize\" id=\"comentario\" name=\"comentario\" rows=\"1\" required></textarea>
        </div>
      </div><!-- ./card-inner -->
      <div class=\"card-action\">
        <div class=\"card-action-btn\">
          <button class=\"btn btn-brand waves-attach\" type=\"submit\" style=\"width: 100%;\"><span class=\"icon\">check</span>&nbsp;Salvar Alterações</button>
        </div>
      </div>
    </form>
  </div><!-- ./card-main -->
</div> <!-- ./card -->
</div> <!-- ./row -->
";
}
?>
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
<div class="modal fade" id="redefinirSenha" role="dialog">
  <div class="modal-dialog" style="width: 40%;">
    <div class="modal-content">
      <div class="modal-heading">
        <a class="modal-close" data-dismiss="modal">×</a>
        <h2 class="modal-title content-sub-heading">Redefinir Senha</h2>
      </div>
      <form id="alterSenha" action="javascript:novaSenha();" method="post">
        <div class="modal-inner">
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

<div aria-hidden="true" class="modal fade" id="freeSaldos" role="dialog" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-heading">
        <a class="modal-close" data-dismiss="modal">×</a>
        <h2 class="modal-title content-sub-heading">Liberar Saldos</h2>
      </div>
      <div class="modal-inner">
        <table class="table">
          <thead>
            <tr>
              <th>Setor</th>
              <th>Valor</th>
              <th>Liberar valor mês/ano</th>
              <th></th>
            </tr>
          </thead>
          <tbody id="contFreeSaldos">

          </tbody>
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
