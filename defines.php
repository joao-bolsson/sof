<?php

defined('SALT') or define('SALT', '$1$j:[]bols$');
defined('VERSION') or define('VERSION', '2.1.11');
defined('COPYRIGHT') or define('COPYRIGHT', "<strong>Copyright © 2016-2017 <a href=\"https://github.com/joao-bolsson\">João Bolsson</a>.</strong> All rights reserved.");

defined('ARRAY_STATUS') or define('ARRAY_STATUS', [NULL, 'Rascunho', 'Em Análise', 'Reprovado', 'Aprovado', 'Aguarda Orçamento', 'Aguarda SIAFI', 'Empenhado', 'Enviado ao Ordenador', 'Enviado ao Fornecedor', 'Recebido da Unidade de Apoio']);

defined('ARRAY_PRIORIDADE') or define('ARRAY_PRIORIDADE', [NULL, 'Normal', 'Preferencial', 'Urgente', 'Emergencial', 'Rascunho']);

defined('ARRAY_SETORES') or define('ARRAY_SETORES', [NULL, 'Público', 'Setor de Orçamento e Finanças', 'Farmácia de Medicamentos', 'Farmácia de Materiais', 'Almoxarifado Geral', 'Divisão de Logística', 'Traumato', 'Dispensas de Licitação', 'Nutrição', 'Divisão Administrativa Financeira', 'NVE', 'Unidade de Apoio', 'Psiquiatria', 'Radiologia', 'SGPTI', 'Protese Auditiva']);

defined('ARRAY_CATEGORIA') or define('ARRAY_CATEGORIA', [NULL, 'normal', 'adiantamento', 'transferencia', 'antecipacao']);

defined('LIMIT_MAX') or define('LIMIT_MAX', 100);
defined('LIMIT_LOGS') or define('LIMIT_LOGS', 50);
defined('LIMIT_REQ_REPORT') or define('LIMIT_REQ_REPORT', 200);

defined('MPDF_PATH') or define('MPDF_PATH', __DIR__ . '/pdf');

defined('BTN_DEFAULT') or define('BTN_DEFAULT', 'btn btn-default');
