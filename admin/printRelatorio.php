<?php

ini_set('display_erros', true);
error_reporting(E_ALL);

session_start();

include_once '../class/PrintMod.class.php';
//instanciando classe de busca para popular o select de estados
$obj_Print = new PrintMod();

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
$input = filter_input(INPUT_POST, 'relatorio');

if (isset($_SESSION['pedidosRel'])) {

    $html = $html_style . $html_header;
    $html .= $obj_Print->getRelPed($_SESSION['pedidosRel']);
    unset($_SESSION['pedidosRel']);
    $html .= "</body>";

    //definimos o tipo de exibicao
    $mpdf->SetDisplayMode('fullpage');
    $data = date('j/m/Y  H:i');
    //definimos oque vai conter no rodape do pdf
    $mpdf->SetFooter("{$data}||Página {PAGENO}/{nb}");
    //e escreve todo conteudo html vindo de nossa página html em nosso arquivo
    $mpdf->WriteHTML($html);
    //fechamos nossa instancia ao pdf
    $mpdf->Output();
    //pausamos a tela para exibir oque foi feito
    exit();
} else if (!empty($input) && $_SESSION["id_setor"] == 2) {
    $html = $html_style . $html_header;

    $tipo = filter_input(INPUT_POST, 'tipo');
    switch ($tipo) {
        case 'pedidos':
            $id_setor = filter_input(INPUT_POST, 'setor');
            $prioridade = filter_input(INPUT_POST, 'prioridade');
            $status = filter_input(INPUT_POST, 'status', FILTER_DEFAULT, FILTER_FORCE_ARRAY);
            $dataI = filter_input(INPUT_POST, 'dataI');
            $dataF = filter_input(INPUT_POST, 'dataF');
            $html .= $obj_Print->getRelatorioPedidos($id_setor, $prioridade, $status, $dataI, $dataF);
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
    $mpdf->SetFooter("{$data}||Página {PAGENO}/{nb}");
    //e escreve todo conteudo html vindo de nossa página html em nosso arquivo
    $mpdf->WriteHTML($html);
    //fechamos nossa instancia ao pdf
    $mpdf->Output();
    //pausamos a tela para exibir oque foi feito
    exit();
} else {
    exit('Página inválida. É preciso mandar imprimir novamente.');
}
