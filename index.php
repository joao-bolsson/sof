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
$obj_Busca = Busca::getInstance();

if (!isset($_SESSION['slide1'])) {
    $_SESSION['slide1'] = $obj_Busca->getSlide(1);
}

if (!isset($_SESSION['slide2'])) {
    $_SESSION['slide2'] = $obj_Busca->getSlide(2);
}

if (isset($_SESSION['id_setor']) && $_SESSION['id_setor'] == 12) {
    header('Location: lte/apoio.php');
} else if (isset($_SESSION["admin"])) {
    $permissao = $obj_Busca->getPermissoes($_SESSION["id"]);
    if ($permissao->pedidos || $permissao->saldos || $permissao->noticias || $permissao->recepcao) {
        //redireciona para a página do admin
        header('Location: lte/');
    } else {
        header('Location: lte/hora.php');
    }
} else if (isset($_SESSION["id_setor"]) && $_SESSION["id_setor"] != 0) {
    header("Location: lte/solicitacoes.php");
} else {
    header("Location: view/");
}
