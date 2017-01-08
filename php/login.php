<?php

ini_set('display_erros', true);
error_reporting(E_ALL);

session_start();
//incluindo classe de Login
include_once '../class/Login.class.php';
include_once '../class/Busca.class.php';

$obj_Busca = new Busca();
if (!$obj_Busca->isActive()) {
    echo "desativado";
} else {
    //instanciando objeto de login
    $obj_Login = new Login();

    $login = filter_input(INPUT_POST, 'login');
    $senha = filter_input(INPUT_POST, 'senha');

    if (is_null($login) || is_null($senha)) {
        echo "false";
    } else {
        $logar = $obj_Login->login($login, $senha, $retorno = false);

        if ($logar == 2) {
            $_SESSION["admin"] = true;
        }
        if ($logar != 0) {
            echo "true";
        } else {
            echo "false";
        }
    }
}
?>
