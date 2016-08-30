<?php
ini_set('display_erros', true);
error_reporting(E_ALL);

session_start();

if (isset($_SESSION["relatorioProcessos"]) && $_SESSION["relatorioProcessos"] && $_SESSION["id_setor"] != 0) {

	include_once '../class/Busca.class.php';
	$tipo = $_SESSION["relatorioTipo"];
//instanciando classe de busca para popular o select de estados
	$obj_Busca = new Busca();

//definimos uma constante com o nome da pasta
	define('MPDF_PATH', '../pdf/MPDF57/');
//incluimos o arquivo
	include MPDF_PATH . 'mpdf.php';
//definimos o timezone para pegar a hora local
	date_default_timezone_set('America/Sao_Paulo');
	// ============================================================================
	//                                     STYLE
	// ============================================================================
	$html_style = "
  <style type=\"text/css\">
   fieldset {
    border: 2px solid black;
    padding-left: 5px;
  }
  fieldset p{
    font-size: 8pt;
    font-weight: bold;
  }
  /* ================= TÍTULO DO RELATÓRIO DE ATIVIDADES =================== */
  fieldset h5{
    text-align: center;
  }
  /* ============================ TABELAS ================================== */
  table{
    border-collapse: collapse;
    border-spacing: 0;
    font-size: 7pt;
    width: 100% !important;
    background-color: #ffffff;
    border: 0;
  }
  table td, table th {
    line-height: 20px;
    padding: 1px;
    vertical-align: top;
  }
  table td {
    text-align: center;
  }
  thead {
    display: table-header-group;
  }
  /* ===================== FIELDSET COM OS PREGÕES ========================== */
  fieldset.preg table td{
    padding: 10px;
    font-weight: bold;
    font-size: 7pt;
  }
  /* =============== TABELA COM OS ITENS DO PEDIDO ========================== */
  table.prod td, table.prod th{
    text-align: left;
    font-size: 8pt;
    padding: 3px;
  }
</style>
";
	// ===================================================================================
	//                                         CABEÇALHO
	// ===================================================================================

	$html_header = "
<body>
  <p style=\"text-align: center;\">
    <img src=\"../sof_files/header_setor_2.png\"/>
  </p>
  <hr/>
  ";
	// ====================================================================================
	//                                  TABELA COM OS ITENS
	// ====================================================================================
	$html_itens = "
  <fieldset>
    <h5>RELATÓRIO DE PROCESSOS</h5>
  </fieldset><br>
  ";

	$html_itens .= $obj_Busca->getRelatorioProcessos($tipo);

	$html = $html_style . $html_header . $html_table_itens . $html_itens . "</body>";

	$mpdf = new mPDF();
//definimos o tipo de exibicao
	$mpdf->SetDisplayMode('fullpage');
	$data = date('j/m/Y  H:i');
//definimos oque vai conter no rodape do pdf
	$mpdf->SetFooter("{$data}||Pagina {PAGENO}/{nb}");
//e escreve todo conteudo html vindo de nossa página html em nosso arquivo
	$mpdf->WriteHTML($html);
//fechamos nossa instancia ao pdf
	$mpdf->Output();
//pausamos a tela para exibir oque foi feito
	exit();
}

?>