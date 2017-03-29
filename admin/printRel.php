<?php

ini_set('display_errors', true);
error_reporting(E_ALL);

session_start();

if (isset($_SESSION["relatorioProcessos"]) && $_SESSION["relatorioProcessos"] && $_SESSION["id_setor"] != 0) {

    include_once '../class/PrintMod.class.php';
    require_once '../defines.php';
    require_once MPDF_PATH . '/vendor/autoload.php';

    $tipo = $_SESSION["relatorioTipo"];

    $html_style = "<link rel=\"stylesheet\" type=\"text/css\" href=\"../relatorios.css\"/>";

    $html_header = "
    <body>
        <p style=\"text-align: center;\">
            <img src=\"../sof_files/header_setor_2.png\"/>
        </p>
    <hr/>";

    $html_itens = "
    <fieldset>
        <h5>RELATÓRIO DE PROCESSOS</h5>
    </fieldset><br>";

    $html_itens .= PrintMod::getRelatorioProcessos($tipo);

    $html = $html_style . $html_header . $html_itens . "</body>";

    $mpdf = new mPDF();
    date_default_timezone_set('America/Sao_Paulo');
    //definimos o tipo de exibicao
    $mpdf->SetDisplayMode('fullpage');
    $data = date('j/m/Y  H:i');
    //definimos oque vai conter no rodape do pdf
    $mpdf->SetFooter("{$data}||Pagina {PAGENO}/{nb}");
    //e escreve o conteudo html vindo de nossa página html em nosso arquivo
    $mpdf->WriteHTML($html);
    //fechamos nossa instancia ao pdf
    $mpdf->Output();
    //pausamos a tela para exibir oque foi feito
    exit();
}
