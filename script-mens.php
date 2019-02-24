<?php

include_once 'class/Query.class.php';

$query = Query::getInstance()->exe("SELECT * FROM mensalidade");

$values = "";

while ($obj = $query->fetch_object()) {
    $values .= "(NULL, $obj->id_contr, $obj->id_mes, $obj->id_ano, $obj->id_grupo, '$obj->valor', $obj->nota, '$obj->reajuste', $obj->aguardaOrcamento, $obj->paga),";
}

$pos = strrpos($values, ",");
$values[$pos] = ";";

Query::getInstance()->exe("INSERT INTO mens VALUES " . $values);
Query::getInstance()->exe("DROP TABLE mensalidade");
Query::getInstance()->exe("ALTER TABLE mens RENAME mensalidade");

echo "Terminou!";

/**
 * D
 */