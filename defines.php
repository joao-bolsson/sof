<?php

include_once 'class/Busca.class.php';

defined('SALT') or define('SALT', '$1$j:[]bols$');
defined('VERSION') or define('VERSION', '2.1.10');
defined('COPYRIGHT') or define('COPYRIGHT', "<strong>Copyright © 2016-2017 <a href=\"https://github.com/joao-bolsson\">João Bolsson</a>.</strong> All rights reserved.");

defined('ARRAY_STATUS') or define('ARRAY_STATUS', Busca::getArrayDefines('status'));
defined('ARRAY_PRIORIDADE') or define('ARRAY_PRIORIDADE', Busca::getArrayDefines('prioridade'));
defined('ARRAY_SETORES') or define('ARRAY_SETORES', Busca::getArrayDefines('setores'));
defined('ARRAY_CATEGORIA') or define('ARRAY_CATEGORIA', Busca::getArrayDefines('saldo_categoria'));

defined('LIMIT_MAX') or define('LIMIT_MAX', 100);
defined('LIMIT_LOGS') or define('LIMIT_LOGS', 50);
defined('LIMIT_REQ_REPORT') or define('LIMIT_REQ_REPORT', 200);

defined('MPDF_PATH') or define('MPDF_PATH', '../pdf');

defined('BTN_DEFAULT') or define('BTN_DEFAULT', 'btn btn-default');
