<?php
ini_set('display_erros', true);
error_reporting(E_ALL);

session_start();
//incluindo classe de Login
include_once '../class/Login.class.php';
//instanciando objeto de login
$obj_Login = new Login();

$login = $_POST["login"];
$senha = $_POST["senha"];

$logar = $obj_Login->login($login, $senha, $retorno = false);
//se o retorno for true então vai redirecionar para o painel de administração do sistema

if ($logar == 2) {
	$_SESSION["admin"] = true;
}
if ($logar != 0) {
	echo "true";
} else {
	echo "false";
}
?>
