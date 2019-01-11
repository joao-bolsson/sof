<?php

ini_set('display_errors', true);
error_reporting(E_ALL);

session_start();

if (isset($_SESSION['imprimirPedido']) && $_SESSION['imprimirPedido'] && $_SESSION['id_setor'] != 0) {
    $id_pedido = $_SESSION['id_ped_imp'];
    $pedido_rascunho = $_SESSION['pedido_rascunho'];

    include_once '../class/PrintMod.class.php';
    require_once '../defines.php';
    require_once MPDF_PATH . '/vendor/autoload.php';

    $id_setor = PrintMod::getSetorPedido($id_pedido);

    $html_style = "
        <link rel=\"stylesheet\" type=\"text/css\" href=\"../relatorios.css\"/>
        <head>
            <title>SOFHUSM | Impressão de pedido</title>
        </head>";
    $img = "../sof_files/header_setor_" . $id_setor . ".png";
    if (!file_exists($img)) {
        $img = "../sof_files/header_setor_2.png";
    }

    $html_header = "
        <body>
          <p style=\"text-align: center;\">
            <img src=\"" . $img . "\"/>
          </p>
          <hr/>";
    $html_header .= PrintMod::getHeader($id_pedido);
    $html_itens = "
        <fieldset>
            <h5>DESCRIÇÃO DO PEDIDO</h5>
        </fieldset><br>";
    $html_itens .= PrintMod::getContentPedido($id_pedido);

    $html_rel = "
        <fieldset>
            <h5>COMENTÁRIOS DO SOF</h5>
        </fieldset><br>";
    $html_rel .= PrintMod::getComentarios($id_pedido);
    $html = $html_style . $html_header . $html_itens . $html_rel . "</body>";
    date_default_timezone_set('America/Sao_Paulo');
    try {
        $mpdf = new \Mpdf\Mpdf(['tempDir' => TEMP_FOLDER]);
        //definimos o tipo de exibicao
        $mpdf->SetDisplayMode('fullpage');
        if ($pedido_rascunho) {
            $mpdf->watermark_font = 'DejaVuSansCondensed';
            $mpdf->showWatermarkText = true;
            $mpdf->SetWatermarkText('RASCUNHO');
        }
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
}
