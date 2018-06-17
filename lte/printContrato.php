<?php
/**
 * @author João Bolsson (joaovictorbolsson@gmail.com)
 * @since 2018, 16 Jun.
 */

ini_set('display_errors', true);
error_reporting(E_ALL);

session_start();

include_once '../class/PrintMod.class.php';
require_once '../defines.php';
require_once MPDF_PATH . '/vendor/autoload.php';

$contrato = filter_input(INPUT_GET, 'id');

$html_style = "
        <link rel=\"stylesheet\" type=\"text/css\" href=\"../relatorios.css\"/>
        <head>
            <title>SOFHUSM | Impressão de Contrato</title>
        </head>";
$img = "../sof_files/header_setor_2.png";

$html_header = "
        <body>
          <p style=\"text-align: center;\">
            <img src=\"" . $img . "\"/>
          </p>
          <hr/>";
$html_itens = "
        <fieldset>
            <h5>MENSALIDADES</h5>
        </fieldset><br>";
//$html_itens .= PrintMod::getContentPedido($id_pedido);

$html = $html_style . $html_header . $html_itens . "</body>";
date_default_timezone_set('America/Sao_Paulo');
try {
    $mpdf = new \Mpdf\Mpdf(['tempDir' => TEMP_FOLDER]);
    //definimos o tipo de exibicao
    $mpdf->SetDisplayMode('fullpage');
    $data = date('j/m/Y  H:i');
    //definimos oque vai conter no rodape do pdf
    $mpdf->SetFooter($data . '||Página {PAGENO}/{nb}');
    //e escreve o conteudo html vindo de nossa página html em nosso arquivo
    $mpdf->WriteHTML($html);
    //fechamos nossa instancia ao pdf
    $mpdf->Output();
} catch (\Mpdf\MpdfException $ex) {
    Logger::error("Exception on printRel: " . $ex);
}
//pausamos a tela para exibir oque foi feito
exit();