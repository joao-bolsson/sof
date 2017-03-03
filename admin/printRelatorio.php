<?php

ini_set('display_erros', true);
error_reporting(E_ALL);

session_start();

include_once '../class/PrintMod.class.php';
require_once '../defines.php';
require_once MPDF_PATH . '/vendor/autoload.php';

$obj_Print = PrintMod::getInstance();

$mpdf = new mPDF();
date_default_timezone_set('America/Sao_Paulo');

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
$html = $html_style . $html_header;

// request type
$type = NULL;

$input = filter_input(INPUT_POST, 'relatorio');

if (!empty($input)) {
    $type = INPUT_POST;
} else {
    // try via GET
    $input = filter_input(INPUT_GET, 'relatorio');
    if (!empty($input)) {
        $type = INPUT_GET;
    }
}

if (!is_null($type)) {
    if ($type === INPUT_POST) {
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

            case 'liberacoes':
                $id_setor = filter_input(INPUT_POST, 'setor');
                $categoria = filter_input(INPUT_POST, 'categoria', FILTER_DEFAULT, FILTER_FORCE_ARRAY);
                $dataI = filter_input(INPUT_POST, 'dataI');
                $dataF = filter_input(INPUT_POST, 'dataF');
                $html .= $obj_Print->getRelatorioLib($id_setor, $categoria, $dataI, $dataF);
                break;
            default:
                exit('Nenhum dado foi recebido para gerar relatório.');
                break;
        }
    } else if ($type === INPUT_GET) {
        $tipo = filter_input($type, 'tipo');
        switch ($tipo) {
            case 'users':
                $html .= $obj_Print->getRelUsers();
                break;

            default:
                exit('Nenhum dado foi recebido para gerar relatório.');
                break;
        }
    }
} else if (isset($_SESSION['pedidosRel']) && empty($input)) {
    $html .= $obj_Print->getRelPed($_SESSION['pedidosRel']);
    unset($_SESSION['pedidosRel']);
} else {
    exit('Página inválida. É preciso mandar imprimir novamente.');
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
