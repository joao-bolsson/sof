<?php

session_start();
ini_set('display_erros', true);
error_reporting(E_ALL);

if (!isset($_SESSION["id"]) || $_SESSION['id_setor'] == 12) {
    header("Location: ../");
}

header('Location: ../lte/solicitacoes.php');
