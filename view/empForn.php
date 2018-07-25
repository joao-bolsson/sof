<?php
/**
 * @author João Bolsson (joaovictorbolsson@gmail.com)
 * @since 2018, 25 Jul.
 */

ini_set('display_errors', true);
error_reporting(E_ALL);

session_start();

spl_autoload_register(function (string $class_name) {
    include_once '../class/' . $class_name . '.class.php';
});

$dataI = Util::dateFormat(filter_input(INPUT_POST, 'dataI'));
$dataF = Util::dateFormat(filter_input(INPUT_POST, 'dataF'));

// Definimos o nome do arquivo que será exportado
$arquivo = 'planilha.xls';

$table = new Table('', '', ['Pedido', 'Empenho', 'Data de Empenho', 'Fornecedor'], true);

$query = Query::getInstance()->exe("SELECT pedido_empenho.id_pedido, pedido_empenho.empenho, DATE_FORMAT(pedido_empenho.data, '%d/%m/%Y') AS data, (SELECT itens.nome_fornecedor FROM itens, itens_pedido WHERE itens.id = itens_pedido.id_item AND itens_pedido.id_pedido = pedido_empenho.id_pedido LIMIT 1) AS fornecedor FROM pedido_empenho WHERE pedido_empenho.data BETWEEN '" . $dataI . "' AND '" . $dataF . "';");

while ($obj = $query->fetch_object()) {
    $row = new Row();
    $row->addComponent(new Column($obj->id_pedido));
    $row->addComponent(new Column($obj->empenho));
    $row->addComponent(new Column($obj->data));
    $row->addComponent(new Column($obj->fornecedor));

    $table->addComponent($row);
}

$html = '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
$html .= $table->__toString();

// Configurações header para forçar o download
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT");
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache");
header("Content-type: application/x-msexcel");
header("Content-Disposition: attachment; filename=\"{$arquivo}\"");
header("Content-Description: PHP Generated Data");
// Envia o conteúdo do arquivo
echo $html;
exit;