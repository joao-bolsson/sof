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
<link rel=\"stylesheet\" type=\"text/css\" href=\"../relatorios.css\"/>
<head>
  <title>SOFHUSM | Relatório</title>
</head>";

$html_header = "
<body>
  <p style=\"text-align: center;\">
    <img src=\"../sof_files/header_setor_2.png\"/>
  </p>
  <hr/>";
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