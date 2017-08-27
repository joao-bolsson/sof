<?php

ini_set('display_errors', true);
error_reporting(E_ALL);

session_start();

include_once '../class/Geral.class.php';

Geral::scanDataBase();
Geral::verifySectors();
