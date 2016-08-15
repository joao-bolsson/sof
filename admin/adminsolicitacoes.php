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
      <a class="btn btn-flat waves-attach waves-light" href="javascript:abreModal('#myInfos');"><span class="text-white"><span class="icon">lock_outline</span><span id="userLogado"><?php echo $_SESSION["nome"] ?></span></span></a>
    </li>
  </ul>
  <nav class="tab-nav pull-right hidden-xx">
    <ul class="nav nav-list">
      <li>
        <a class="btn btn-flat waves-attach waves-light" href="sair.php"><span class="text-white"><span class="icon">exit_to_app</span>SAIR</span></a>
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
if ($permissao->pedidos) {
	echo "
           <li>
            <a class=\"waves-attach\" href=\"javascript:abreModal('#importItens');\"><span style=\"color: white;\"><span class=\"icon\">backup</span>IMPORTAR ITENS</span></a>
          </li>
          ";
}
if ($permissao->recepcao) {
	echo "<li>
         <a class=\"waves-attach\" href=\"javascript:abreModal('#listProcessos');\"><span class=\"text-white\"><span class=\"icon\">label</span>PROCESSOS</span></a>
       </li>";
}
?>
   </ul>
 </nav>
</div><!-- ./row -->
<div class="row" style="margin-top: -5%;">
  <table class="table">
    <tbody>
      <tr>
        <?php
if ($permissao->saldos) {
	echo "
              <td>
                <a class=\"\" href=\"javascript:mostra('rowSolicAdi');\"><span class=\"icon\">add</span>Solicitações de Adiantamento</a>
              </td>
            ";
}
if ($permissao->pedidos) {
	echo "
        <td>
          <a class=\"\" href=\"javascript:mostra('rowAltPed');\"><span class=\"icon\">add</span>Solicitações de Alteração de Pedidos</a>
        </td>
  ";
}
?>
      </tr>
    </tbody>
  </table>
</div>
<?php
if ($permissao->recepcao) {
	echo "
  <div class=\"row\">
   <div class=\"card margin-top-no\">
     <div class=\"card-main\">
       <div class=\"card-header card-brand\">
         <div class=\"card-header-side pull-left\">
           <p class=\"card-heading\">Processos</p>
         </div>
       </div><!--  ./card-header -->
       <div class=\"card-inner\">
         <a class=\"\" href=\"javascript:addProcesso(' ', 0);\"><span class=\"icon\">add</span>Adicionar Processo</a>
         <table class=\"table stripe\" id=\"tableRecepcao\" style=\"width: 100%;\">
          <thead>
            <th></th>
            <th>PROCESSO</th>
            <th>TIPO</th>
            <th>ESTANTE</th>
            <th>PRATELEIRA</th>
            <th>ENTRADA EM</th>
            <th>SAIDA EM</th>
            <th>RESPONSÁVEL</th>
            <th>RETORNO EM</th>
            <th>OBS</th>
          </thead>
          <tbody id=\"conteudoRecepcao\">

          </tbody>
        </table>
      </div><!-- ./card-inner -->
    </div><!-- ./card-main -->
  </div> <!-- ./card -->
</div>
";
}
if ($permissao->saldos) {
	echo "
  <div id=\"rowSolicAdi\" class=\"row\" style=\"display: block;\">
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
if ($permissao->pedidos) {
	echo "
  <div id=\"rowAltPed\" class=\"row\" style=\"display: block;\">
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
        <input id=\"total_hidden\" type=\"hidden\" name=\"total_hidden\" value=\"0\">
        <input id=\"saldo_total\" type=\"hidden\" name=\"saldo_total\" value=\"0.000\">
        <input id=\"prioridade\" type=\"hidden\" name=\"prioridade\" value=\"0\">
        <table class=\"table\">
          {$obj_Busca->getStatus(4)}
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
<?php
if ($permissao->pedidos) {
	echo "<div class=\"modal fade\" id=\"importItens\" role=\"dialog\">
  <div class=\"modal-dialog\" style=\"width: 40%;\">
    <div class=\"modal-content\">
      <div class=\"modal-heading\">
        <a class=\"modal-close\" data-dismiss=\"modal\">×</a>
        <h2 class=\"modal-title content-sub-heading\">Importar Itens</h2>
      </div>
      <form enctype=\"multipart/form-data\" action=\"../php/geral.php\" method=\"post\">
        <input type=\"hidden\" name=\"admin\" value=\"1\">
        <input type=\"hidden\" name=\"form\" value=\"importItens\">
        <div class=\"modal-inner\">
          <div class=\"form-group form-group-label\">
            <label class=\"floating-label\" for=\"file\"><span class=\"icon\">insert_drive_file</span>&nbsp;Arquivo</label>
            <input id=\"file\" class=\"form-control\" type=\"file\" style=\"text-transform: none !important;\" name=\"file\">
          </div>
          <p class=\"help-block\">Max. 32MB</p>
          <div id=\"loaderImport\" class=\"progress-circular\" style=\"margin-left: 45%; display: none;\">
            <div class=\"progress-circular-wrapper\">
              <div class=\"progress-circular-inner\">
                <div class=\"progress-circular-left\">
                  <div class=\"progress-circular-spinner\"></div>
                </div>
                <div class=\"progress-circular-gap\"></div>
                <div class=\"progress-circular-right\">
                  <div class=\"progress-circular-spinner\"></div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class=\"modal-footer margin-bottom\">
          <button class=\"btn btn-brand waves-attach waves-light waves-effect\" type=\"submit\" style=\"width: 100%;\"><span class=\"icon\">backup</span>&nbsp;Enviar</button>
        </div>
      </form>
    </div>
  </div>
</div>";
}
if ($permissao->saldos) {
	echo "<div aria-hidden=\"true\" class=\"modal fade\" id=\"freeSaldos\" role=\"dialog\" tabindex=\"-1\">
  <div class=\"modal-dialog\">
    <div class=\"modal-content\">
      <div class=\"modal-heading\">
        <a class=\"modal-close\" data-dismiss=\"modal\">×</a>
        <h2 class=\"modal-title content-sub-heading\">Liberar Saldos</h2>
      </div>
      <div class=\"modal-inner\">
        <table class=\"table\">
          <thead>
            <tr>
              <th>Setor</th>
              <th>Valor</th>
              <th>Liberar valor mês/ano</th>
              <th></th>
            </tr>
          </thead>
          <tbody id=\"contFreeSaldos\">

          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>";
}
if ($permissao->recepcao) {
	echo "<div aria-hidden=\"true\" class=\"modal fade\" id=\"listProcessos\" role=\"dialog\" tabindex=\"-1\">
  <div class=\"modal-dialog\" style=\"width: 40%;\">
    <div class=\"modal-content\">
      <div class=\"modal-heading\">
        <a class=\"modal-close\" data-dismiss=\"modal\">×</a>
        <h2 class=\"modal-title content-sub-heading\">Processos Atendidos pelo SOF</h2>
      </div>
      <div class=\"modal-inner\">
        <table id=\"tableListProcessos\" class=\"table\" style=\"width: 100%; display: none\">
          <thead>
            <tr>
              <th>Número do Processo</th>
              <th></th>
            </tr>
          </thead>
          <tbody id=\"contentListProcessos\">
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>";
}
if ($permissao->recepcao) {
	echo "<div aria-hidden=\"true\" class=\"modal fade\" id=\"addProcesso\" role=\"dialog\" tabindex=\"-1\">
  <div class=\"modal-dialog\" style=\"width: 50%;\">
    <div class=\"modal-content\">
      <div class=\"modal-heading\">
        <a class=\"modal-close\" data-dismiss=\"modal\">×</a>
        <h2 class=\"modal-title content-sub-heading\">Processo</h2>
      </div>
      <form id=\"formProcesso\" action=\"javascript:updateProcesso();\" method=\"post\">
        <input id=\"id_processo\" type=\"hidden\" value=\"0\"></input>
        <div class=\"modal-inner\">
          <table style=\"width: 100%;\">
            <tbody>
              <tr>
                <td>
                  <div id=\"divNumProc\" class=\"form-group form-group-label\">
                    <label class=\"floating-label\" for=\"num_processo\"><span class=\"icon\">label</span>&nbsp;Processo</label>
                    <input class=\"form-control\" id=\"num_processo\" name=\"num_processo\" type=\"text\" required>
                  </div>
                </td>
                <td>
                  <div class=\"form-group form-group-label\">
                    <label class=\"floating-label\" for=\"tipo\"><span class=\"icon\">lock_outline</span>&nbsp;Tipo</label>
                    <input class=\"form-control\" id=\"tipo\" name=\"tipo\" type=\"text\" required>
                  </div>
                </td>
              </tr>
              <tr>
                <td>
                  <div class=\"form-group form-group-label\">
                    <label class=\"floating-label\" for=\"estante\"><span class=\"icon\">line_weight</span>&nbsp;Estante</label>
                    <input class=\"form-control\" id=\"estante\" name=\"estante\" type=\"text\" required>
                  </div>
                </td>
                <td>
                  <div class=\"form-group form-group-label\">
                    <label class=\"floating-label\" for=\"prateleira\"><span class=\"icon\">list</span>&nbsp;Prateleira</label>
                    <input class=\"form-control\" id=\"prateleira\" name=\"prateleira\" type=\"text\" required>
                  </div>
                </td>
              </tr>
              <tr>
                <td>
                  <div class=\"form-group form-group-label\">
                    <label class=\"floating-label\" for=\"entrada\"><span class=\"icon\">date_range</span>&nbsp;Entrada (dd/mm/yyyy)</label>
                    <input class=\"form-control\" id=\"entrada\" name=\"entrada\" type=\"text\" required maxlength=\"10\">
                  </div>
                </td>
                <td>
                  <div class=\"form-group form-group-label\">
                    <label class=\"floating-label\" for=\"saida\"><span class=\"icon\">alarm</span>&nbsp;Saída (dd/mm/yyyy)</label>
                    <input class=\"form-control\" id=\"saida\" name=\"saida\" type=\"text\" maxlength=\"10\">
                  </div>
                </td>
              </tr>
              <tr>
                <td>
                  <div class=\"form-group form-group-label\">
                    <label class=\"floating-label\" for=\"responsavel\"><span class=\"icon\">perm_identity</span>&nbsp;Responsável</label>
                    <input class=\"form-control\" id=\"responsavel\" name=\"responsavel\" type=\"text\">
                  </div>
                </td>
                <td>
                  <div class=\"form-group form-group-label\">
                    <label class=\"floating-label\" for=\"retorno\"><span class=\"icon\">date_range</span>&nbsp;Retorno (dd/mm/yyyy)</label>
                    <input class=\"form-control\" id=\"retorno\" name=\"retorno\" type=\"text\" maxlength=\"10\">
                  </div>
                </td>
              </tr>
              <tr>
                <td colspan=\"2\">
                  <div class=\"form-group form-group-label\">
                    <label class=\"floating-label\" for=\"obs\"><span class=\"icon\">announcement</span>&nbsp;Observação</label>
                    <textarea class=\"form-control textarea-autosize\" id=\"obs\" name=\"obs\" rows=\"1\" required></textarea>
                  </div>
                </td>
                <td></td>
              </tr>
            </tbody>
          </table>
          <div id=\"loading\" class=\"progress-circular\" style=\"margin-left: 45%; display: none;\">
            <div class=\"progress-circular-wrapper\">
              <div class=\"progress-circular-inner\">
                <div class=\"progress-circular-left\">
                  <div class=\"progress-circular-spinner\"></div>
                </div>
                <div class=\"progress-circular-gap\"></div>
                <div class=\"progress-circular-right\">
                  <div class=\"progress-circular-spinner\"></div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class=\"modal-footer margin-bottom\">
          <button class=\"btn btn-brand waves-attach waves-light waves-effect\" type=\"submit\" style=\"width: 100%;\"><span class=\"icon\">send</span>&nbsp;Enviar</button>
        </div>
      </form>
    </div>
  </div>
</div>";
}
?>
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
            <input class="form-control" id="nameUser" name="nameUser" type="text" value="<?php echo $_SESSION['nome'] ?>" required>
          </div>
          <div class="form-group form-group-label">
            <label class="floating-label" for="emailUser"><span class="icon">message</span>&nbsp;E-mail</label>
            <input class="form-control" id="emailUser" name="emailUser" type="email" value="<?php echo $_SESSION['email'] ?>" required>
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
<!-- js -->
<script src="../plugins/jQuery/jquery.min.js"></script>
<script src="../material/js/base.min.js"></script>

<!-- js for doc -->
<script src="../material/js/project.min.js"></script>

<script type="text/javascript" src="../plugins/dataTables/datatables.min.js"></script>

<script type="text/javascript" src="../ini.js"></script>
</body>
</html>
