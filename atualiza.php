<?php

ini_set('display_erros', true);
error_reporting(E_ALL);

session_start();

include_once 'class/Busca.class.php';
//instanciando classe de busca para popular o select de estados
$obj_Busca = new Busca();


$obj_Busca->testValores();
