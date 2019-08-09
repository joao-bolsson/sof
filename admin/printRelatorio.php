<?php

ini_set('display_errors', true);
error_reporting(E_ALL);

session_start();

include_once '../class/BuscaLTE.class.php';
include_once '../class/PrintMod.class.php';
include_once '../class/report/ReportSIAFIPart.class.php';
include_once '../class/report/ReportSIAFI.class.php';
require_once '../defines.php';

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

            case 'faturamento':
                $contrato = filter_input(INPUT_POST, 'contrato');

                $html .= PrintMod::getRelatorioFaturamento($contrato);
                break;

            case 'receitas':
                $competencia = filter_input(INPUT_POST, 'competencia');
                $mes = filter_input(INPUT_POST, 'mes');

                $html .= PrintMod::getRelatorioReceitas($competencia, $mes);
                break;

            case 'siafi':
                $id_setor = filter_input(INPUT_POST, 'setor');
                $fonte = filter_input(INPUT_POST, 'fonte', FILTER_DEFAULT, FILTER_FORCE_ARRAY);

                $num_processo = filter_input(INPUT_POST, 'num_processo', FILTER_DEFAULT, FILTER_FORCE_ARRAY);
                $dataI = filter_input(INPUT_POST, 'dataI');
                $dataF = filter_input(INPUT_POST, 'dataF');

                $report = new ReportSIAFI($id_setor, $fonte, $num_processo, $dataI, $dataF);
                $html .= $report;
                break;

            case 'pedidos':
                $id_setor = filter_input(INPUT_POST, 'setor');
                $prioridade = filter_input(INPUT_POST, 'prioridade', FILTER_DEFAULT, FILTER_FORCE_ARRAY);

                if (in_array(0, $prioridade)) {
                    $prioridade = [];
                    $count = count(ARRAY_PRIORIDADE);

                    $index = 0;
                    for ($i = 1; $i < $count; $i++) {
                        if (ARRAY_PRIORIDADE[$i] != 'Rascunho') {
                            $prioridade[$index++] = $i;
                        }
                    }
                }

                $status = filter_input(INPUT_POST, 'status', FILTER_DEFAULT, FILTER_FORCE_ARRAY);
                $dataI = filter_input(INPUT_POST, 'dataI');
                $dataF = filter_input(INPUT_POST, 'dataF');
                $checkSaifi = !empty(filter_input(INPUT_POST, 'checkSaifi'));
                $fonte = filter_input(INPUT_POST, 'fonte');
                $html .= PrintMod::getRelatorioPedidos($id_setor, $prioridade, $status, $dataI, $dataF, $checkSaifi, $fonte);
                break;

            case 'liberacoes':
                $id_setor = filter_input(INPUT_POST, 'setor');
                $categoria = filter_input(INPUT_POST, 'categoria', FILTER_DEFAULT, FILTER_FORCE_ARRAY);
                $dataI = filter_input(INPUT_POST, 'dataI');
                $dataF = filter_input(INPUT_POST, 'dataF');
                $html .= PrintMod::getRelatorioLib($id_setor, $categoria, $dataI, $dataF);
                break;

            case 'hora':
                $id_usuario = filter_input(INPUT_POST, 'user');
                $periodo = filter_input(INPUT_POST, 'periodo');

                $html .= PrintMod::relatorioHora($id_usuario, $periodo);
                break;
            default:
                exit('Nenhum dado foi recebido para gerar relatório.');
                break;
        }
    } else if ($type === INPUT_GET) {
        $tipo = filter_input($type, 'tipo');
        switch ($tipo) {
            case 'users':
                $html .= PrintMod::getRelUsers();
                break;

            default:
                exit('Nenhum dado foi recebido para gerar relatório.');
                break;
        }
    }
} else if (isset($_SESSION['pedidosRel']) && empty($input)) {
    $html .= PrintMod::getRelPed($_SESSION['pedidosRel']);
    unset($_SESSION['pedidosRel']);
} else {
    exit('Página inválida. É preciso mandar imprimir novamente.');
}

$html .= "</body>";
require_once MPDF_PATH . '/vendor/autoload.php';
try {
    $mpdf = new \Mpdf\Mpdf(['tempDir' => TEMP_FOLDER]);
    //definimos o tipo de exibicao
    $mpdf->SetDisplayMode('fullpage');
    $data = date('j/m/Y  H:i');
    //definimos oque vai conter no rodape do pdf
    $mpdf->SetFooter("{$data}||Página {PAGENO}/{nb}");
    //e escreve o conteudo html vindo de nossa página html em nosso arquivo
    $mpdf->WriteHTML($html);
    //fechamos nossa instancia ao pdf
    $mpdf->Output();
} catch (\Mpdf\MpdfException $ex) {
    Logger::error("Exception on printRel: " . $ex);
}

//pausamos a tela para exibir oque foi feito
exit();
