<?php

ini_set('display_errors', true);
error_reporting(E_ALL);

session_start();

$solicitante = filter_input(INPUT_POST, 'nome');
$cpf = filter_input(INPUT_POST, 'cpf');
$matricula = filter_input(INPUT_POST, 'matricula');
$email = filter_input(INPUT_POST, 'email');
$telefone = filter_input(INPUT_POST, 'telefone');
$banco = filter_input(INPUT_POST, 'banco');
$agencia = filter_input(INPUT_POST, 'agencia');
$conta = filter_input(INPUT_POST, 'conta');

$evento = filter_input(INPUT_POST, 'evento');
$dataI = filter_input(INPUT_POST, 'dataI');
$dataF = filter_input(INPUT_POST, 'dataF');
$local = filter_input(INPUT_POST, 'local');
$valor = filter_input(INPUT_POST, 'valor');

include_once '../class/Util.class.php';
require_once '../defines.php';
require_once MPDF_PATH . '/vendor/autoload.php';

$html_style = "
<style type=\"text/css\">
  fieldset {
    border: 2px solid black;
    margin-right: 10%;
    margin-left: 10%;
    padding: -2% 1% auto 1%;
  }
  fieldset h5 {
    background-color: #a6a6a6;
    padding: 0.6%;
    margin-left: -1.6%;
    margin-right: -1.6%;
    margin-bottom: 0;
    border: 1px solid black;
  }
  fieldset h6 {
    font-weight: normal;
    margin-top: -0.1%;
    margin-bottom: 0;
    padding: 0.6%;
    border: 1px solid black;
    margin-left: -1.6%;
    margin-right: -1.6%;
  }
  fieldset p {
    font-family: Times New Roman, sans-serif;
    font-size: 8pt;
  }
  h5, h6 {
    font-family: Arial, sans-serif;
  }
  #data {
    margin-left: 50%;
  }
  .ass {
    font-weight: normal;
  }
  #apro {
    text-align: center;
    background-color: #a6a6a6;
    margin-top: -0.1%;
    margin-bottom: 0;
    padding: 0.6%;
    border: 1px solid black;
    margin-left: -1.6%;
    margin-right: -1.6%;
  }
  span {
    font-size: 6pt;
    font-weight: normal;
  }
</style>
<head>
  <title>SOFHUSM | Impressão de pedido</title>
</head>";
$html_header = "
<body>
  <p style=\"text-align: center;\">
    <img src=\"../sof_files/header_setor_2.png\"/>
  </p>
  <hr/>";

$date = new DateTime(Util::dateFormat($dataF));
$date->add(new DateInterval('P15D'));
$data_prox = $date->format('d/m/Y');
$html = "
  <fieldset>
    <h5>SOLICITANTE</h5>
    <h6>NOME: {$solicitante}</h6>
    <h6>CPF: {$cpf}</h6>
    <h6>MATRÍCULA SIAPE: {$matricula}</h6>
    <h6>E-MAIL: {$email}</h6>
    <h6>TELEFONE: {$telefone}</h6>
  </fieldset>

  <fieldset>
    <h5>DADOS DO PAGAMENTO</h5>
    <h6>BANCO: {$banco}</h6>
    <h6>AGÊNCIA: {$agencia}</h6>
    <h6>CONTA: {$conta}</h6>
  </fieldset>

  <fieldset>
    <h5>DADOS DO EVENTO</h5>
    <h6>EVENTO: {$evento}</h6>
    <h6>LOCAL: {$local}</h6>
    <h6>VALOR: R$ {$valor}</h6>
    <h6><b>Período de Realização do Evento: {$dataI} A {$dataF}</b></h6>
  </fieldset>

  <fieldset>
    <h5>AMPARO LEGAL:</h5>
    <p>
      Constituição Federal - CF – 1988 (Art 70)<br>
      Decreto 93.872/86 – (Art. 66)<br>
      (Decreto-lei nº 200/67, art. 93).<br>
      A Lei 8.112/90, em seu art. 46, disciplina as reposições e indenizações ao erário pela Autoridade Administrativa.
    </p>
    <h6><b>Data para prestação de contas: ATÉ {$data_prox}</b><span> (certificado + recibo ou nota fiscal em favor do HUSM - EBSERH)<span></h6>
  </fieldset>

  <h4 id=\"data\" class=\"ass\">Santa Maria, ___ de ___________________ de _____.</h4>
  <h5 class=\"ass\" style=\"text-align: center\">
  _______________________________________________<br>
  ASSINATURA E CARIMBO DO RESPONSÁVEL
  </h5>

  <fieldset>
    <h5>APROVADO PELA GERÊNCIA ADMINISTRATIVA</h5>
    <p id=\"apro\">ASSINATURA - CARIMBO - DATA</p><br><br><br>
  </fieldset>";

$html = $html_style . $html_header . $html . "</body>";
$mpdf = new mPDF();
date_default_timezone_set('America/Sao_Paulo');
//definimos o tipo de exibicao
$mpdf->SetDisplayMode('fullpage');

$data = date('j/m/Y  H:i');
//definimos oque vai conter no rodape do pdf
$mpdf->SetFooter($data . '||Página {PAGENO}/{nb}');
//e escreve todo conteudo html vindo de nossa página html em nosso arquivo
$mpdf->WriteHTML($html);
//fechamos nossa instancia ao pdf
$mpdf->Output();

exit();
