<?php
/**
 * 	Arquivo principal do sistema.
 *
 * 	@author João Bolsson
 */
ini_set('display_erros', true);
error_reporting(E_ALL);

session_start();

include_once 'class/Busca.class.php';
//instanciando classe de busca para popular o select de estados
$obj_Busca = new Busca();
include_once 'class/Geral.class.php';
$obj_Geral = new Geral();

setcookie('slide1', $obj_Busca->getSlide(1), 0);
setcookie('slide2', $obj_Busca->getSlide(2), 0);

if (isset($_SESSION["admin"])) {
//redireciona para a página do admin
	header('Location: admin/');
} else if (isset($_SESSION["id_setor"]) && $_SESSION["id_setor"] != 0) {
	header("Location: view/solicitacoes.php");
} else {
	header("Location: view/");
}
?>
