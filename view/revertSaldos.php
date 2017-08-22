<?php
ini_set('display_errors', true);
error_reporting(E_ALL);

session_start();

include_once '../class/Util.class.php';

$query = Query::getInstance()->exe("SELECT id_setor FROM saldo_setor WHERE id_setor != 2 AND saldo > 0;");

if ($query->num_rows > 0) {
    while ($obj = $query->fetch_object()) {
        $flag = Util::revertMoney($obj->id_setor);
        if ($flag) {
            echo "Sucesso, setor: " . $obj->id_setor . "<br>";
        } else {
            echo "Falha, setor: " . $obj->id_setor . "<br>";
        }
    }
}

