<?php
/**
 * @author João Bolsson (joaovictorbolsson@gmail.com)
 * @since 2018, Apr 18.
 */
ini_set('display_errors', true);
error_reporting(E_ALL);

session_start();

include_once '../class/BuscaLTE.class.php';
include_once '../class/PrintMod.class.php';

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

$tbody = BuscaLTE::loadProcsVenc();

$html .= "<table>
                                   <thead>
                                    <tr>
                                        <th>Pedido</th>
                                        <th>Processo</th>
                                        <th>Data Fim</th>
                                    </tr>
                                    </thead>
                                    <tbody>" . $tbody . "</tbody>
                                </table>";

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
