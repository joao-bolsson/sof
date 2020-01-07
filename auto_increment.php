<?php

include_once 'class/Query.class.php';

$tables = ["pedido_contrato", "pedido_empenho", "pedido_fonte", "pedido_grupo", "pedido_id_fonte", "pedido_log_status", "pedido_plano"];

Query::getInstance()->exe("SET foreign_key_checks = 0;");
$query = Query::getInstance()->exe("SELECT id FROM pedido");

$i = 1;

while ($obj = $query->fetch_object()) {
    foreach ($tables as $table) {
        Query::getInstance()->exe("UPDATE " . $table . " SET id_pedido = " . $i . " WHERE id_pedido = " . $obj->id);
        $i++;
    }
}

Query::getInstance()->exe("ALTER TABLE pedido AUTO_INCREMENT = " . $i);

Query::getInstance()->exe("SET foreign_key_checks = 1;");

echo "Terminou!";
