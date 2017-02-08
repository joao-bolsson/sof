<?php

require_once 'class/Busca.class.php';
$obj_Busca = new Busca();

defined('SALT') or define('SALT', '$1$j:[]bols$');
defined('VERSION') or define('VERSION', '2.1.4');

defined('ARRAY_STATUS') or define('ARRAY_STATUS', $obj_Busca->getArrayStatus());
defined('ARRAY_SETORES') or define('ARRAY_SETORES', $obj_Busca->getArraySetores());
defined('ARRAY_MES') or define('ARRAY_MES', $obj_Busca->getArrayMes());
