<?php

include_once 'class/Busca.class.php';

$obj_Busca = new Busca();

defined('SALT') or define('SALT', '$1$j:[]bols$');
defined('VERSION') or define('VERSION', '2.1.4');

defined('ARRAY_STATUS') or define('ARRAY_STATUS', $obj_Busca->getArrayDefines('status'));
defined('ARRAY_PRIORIDADE') or define('ARRAY_PRIORIDADE', $obj_Busca->getArrayDefines('prioridade'));
defined('ARRAY_SETORES') or define('ARRAY_SETORES', $obj_Busca->getArrayDefines('setores'));

defined('LIMIT_MAX') or define('LIMIT_MAX', 100);
