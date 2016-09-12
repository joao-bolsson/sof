<?php
ini_set('display_erros', true);
error_reporting(E_ALL);

session_start();

include_once '../class/Busca.class.php';
//instanciando classe de busca para popular o select de estados
$obj_Busca = new Busca();

//definimos uma constante com o nome da pasta
define('MPDF_PATH', '../pdf/MPDF57/');
//incluimos o arquivo
include MPDF_PATH . 'mpdf.php';
//definimos o timezone para pegar a hora local
date_default_timezone_set('America/Sao_Paulo');

$mpdf = new mPDF();

$html_style = "
  <style type=\"text/css\">
   fieldset {
    border: 2px solid black;
    padding-left: 5px;
  }
  fieldset p{
    font-size: 8pt;
    font-weight: normal;
  }
  fieldset h5{
    text-align: center;
  }
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
  fieldset.preg table td{
    padding: 10px;
    font-weight: bold;
    font-size: 7pt;
  }
  table.prod td, table.prod th{
    text-align: left;
    font-size: 8pt;
    padding: 3px;
  }
</style>
<head>
  <title>SOFHUSM | Relatório</title>
</head>
";

$html_header = "
<body>
  <p style=\"text-align: center;\">
    <img src=\"../sof_files/header_setor_2.png\"/>
  </p>
  <hr/>
  ";
if (isset($_POST["relatorio"]) && $_SESSION["id_setor"] == 2) {
	$html = $html_style . $html_header;

	$tipo = $_POST['tipo'];
	switch ($tipo) {
	case 'pedidos':
		$id_setor = $_POST['setor'];
		$prioridade = $_POST['prioridade'];
		$status = $_POST['status'];
		$mes = $_POST['mes'];
		$html .= $obj_Busca->getRelatorioPedidos($id_setor, $prioridade, $status, $mes);
		break;

	default:
		// remove all session variables
		session_unset();
		// destroy the session
		session_destroy();
		break;
	}

	$html .= "</body>";

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