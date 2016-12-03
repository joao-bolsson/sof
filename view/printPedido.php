<?php

ini_set('display_erros', true);
error_reporting(E_ALL);

session_start();

if (isset($_SESSION["imprimirPedido"]) && $_SESSION["imprimirPedido"] && $_SESSION["id_setor"] != 0) {
    $id_setor = $_SESSION["id_setor"];
    $id_pedido = $_SESSION["id_ped_imp"];
    $pedido_rascunho = $_SESSION['pedido_rascunho'];
    include_once '../class/Busca.class.php';
//instanciando classe de busca para popular o select de estados
    $obj_Busca = new Busca();

//definimos uma constante com o nome da pasta
    define('MPDF_PATH', '../pdf/MPDF57/');
//incluimos o arquivo
    include MPDF_PATH . 'mpdf.php';
//definimos o timezone para pegar a hora local
    date_default_timezone_set('America/Sao_Paulo');
    $html_style = "
<link rel=\"stylesheet\" type=\"text/css\" href=\"../relatorios.css\"/>
<head>
  <title>SOFHUSM | Impressão de pedido</title>
</head>
";
    $html_header = "
<body>
  <p style=\"text-align: center;\">
    <img src=\"../sof_files/header_setor_{$id_setor}.png\"/>
  </p>
  <hr/>
  ";
    $html_header .= $obj_Busca->getHeader($id_pedido);
    $html_itens = "
  <fieldset>
    <h5>DESCRIÇÃO DO PEDIDO</h5>
  </fieldset><br>
  ";
    $html_itens .= $obj_Busca->getContentPedido($id_pedido);

    $html_rel = "
  <fieldset>
    <h5>COMENTÁRIOS DO SOF</h5>
  </fieldset><br>
  ";
    $html_rel .= $obj_Busca->getComentarios($id_pedido);
    $html = $html_style . $html_header . $html_table_itens . $html_itens . $html_rel . "</body>";
    $mpdf = new mPDF();
//definimos o tipo de exibicao
    $mpdf->SetDisplayMode('fullpage');
    if ($pedido_rascunho) {
        $mpdf->useOnlyCoreFonts = true;
        $mpdf->watermark_font = 'DejaVuSansCondensed';
        $mpdf->showWatermarkText = true;
        $mpdf->SetWatermarkText('RASCUNHO');
    }
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