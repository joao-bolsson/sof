<?php

ini_set('display_errors', true);
error_reporting(E_ALL);

session_start();

spl_autoload_register(function (string $class_name) {
    include_once '../class/' . $class_name . '.class.php';
});

if (!Busca::isActive()) {
    echo "desativado";
} else {
    $obj_Login = Login::getInstance();

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
