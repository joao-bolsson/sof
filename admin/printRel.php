<?php

ini_set('display_erros', true);
error_reporting(E_ALL);

session_start();

if (isset($_SESSION["relatorioProcessos"]) && $_SESSION["relatorioProcessos"] && $_SESSION["id_setor"] != 0) {

    include_once '../class/PrintMod.class.php';
    $tipo = $_SESSION["relatorioTipo"];
    $obj_Print = new PrintMod();

    //definimos uma constante com o nome da pasta
    define('MPDF_PATH', '../pdf/MPDF57/');
    //incluimos o arquivo
    include MPDF_PATH . 'mpdf.php';
    //definimos o timezone para pegar a hora local
    date_default_timezone_set('America/Sao_Paulo');
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

    $html_itens .= $obj_Print->getRelatorioProcessos($tipo);

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