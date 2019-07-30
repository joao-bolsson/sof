<?php

include_once '../class/Query.class.php';

Query::getInstance()->exe("delete from aihs_receita_tipo where id > 100;");
Query::getInstance()->exe("alter table aihs_receita_tipo auto_increment = 101;");

$query = Query::getInstance()->exe("select nome from aihs_receita_tipo;");
while ($obj = $query->fetch_object()) {
    Query::getInstance()->exe("INSERT INTO aihs_receita_tipo VALUES(NULL, '" . $obj->nome . "');");
}

$query = Query::getInstance()->exe("SELECT id, tipo FROM aihs_receita;");
while ($obj = $query->fetch_object()) {
    $tipo = $obj->tipo + 90;
    Query::getInstance()->exe("UPDATE aihs_receita SET tipo = " . $tipo . " WHERE id = " . $obj->id);
}

// seta os ids certos
$query = Query::getInstance()->exe("select id from aihs_receita_tipo where id < 100;");
while ($obj = $query->fetch_object()) {
    $id = $obj->id - 10;
    Query::getInstance()->exe("UPDATE aihs_receita_tipo SET id = " . $id . " WHERE id = " . $obj->id);
}

// seta os ids certos para as receitas
$query = Query::getInstance()->exe("SELECT id, tipo FROM aihs_receita;");
while ($obj = $query->fetch_object()) {
    $tipo = $obj->tipo - 100;
    Query::getInstance()->exe("UPDATE aihs_receita SET tipo = " . $tipo . " WHERE id = " . $obj->id);
}

Query::getInstance()->exe("DELETE FROM aihs_receita_tipo WHERE id > 100");
Query::getInstance()->exe("ALTER TABLE aihs_receita_tipo AUTO_INCREMENT = 23");



