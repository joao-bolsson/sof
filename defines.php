<?php

defined('SALT') or define('SALT', '$1$j:[]bols$');
defined('VERSION') or define('VERSION', '2.4.6');
defined('COPYRIGHT') or define('COPYRIGHT', "<strong>Copyright © 2016-2019 <a href=\"https://github.com/joao-bolsson\">João Bolsson</a>.</strong>");

defined('ARRAY_STATUS') or define('ARRAY_STATUS', [NULL, 'Rascunho', 'Em Análise', 'Reprovado', 'Aprovado', 'Aguarda Orçamento', 'Aguarda SIAFI', 'Empenhado', 'Enviado ao Ordenador', 'Enviado ao SOF', 'Recebido da Unidade de Apoio']);

defined('ARRAY_PRIORIDADE') or define('ARRAY_PRIORIDADE', [NULL, 'Normal', 'Preferencial', 'Urgente', 'Emergencial', 'Rascunho', 'Hoje']);

defined('ARRAY_SETORES') or define('ARRAY_SETORES', [NULL, 'Público', 'Setor de Orçamento e Finanças', 'Farmácia de Medicamentos', 'Farmácia de Materiais', 'Almoxarifado Geral', 'Divisão de Logística', 'Traumato', 'Dispensas de Licitação', 'Nutrição', 'Divisão Administrativa Financeira', 'NVE', 'Unidade de Apoio', 'Psiquiatria', 'Radiologia', 'SGPTI', 'Protese Auditiva', 'Empresas']);

defined('ARRAY_CATEGORIA') or define('ARRAY_CATEGORIA', [NULL, 'normal', 'adiantamento', 'transferencia', 'antecipacao', 'recolhimento']);

defined('ARRAY_ITEM_FIELDS') or define('ARRAY_ITEM_FIELDS', ['id', 'id_item_processo', 'id_item_contrato', 'cod_despesa', 'descr_despesa', 'descr_tipo_doc', 'num_contrato', 'num_processo', 'descr_mod_compra', 'num_licitacao', 'dt_inicio', 'dt_fim', 'dt_geracao', 'cgc_fornecedor', 'nome_fornecedor', 'num_extrato', 'cod_estruturado', 'nome_unidade', 'cod_reduzido', 'complemento_item', 'descricao', 'id_extrato_contr', 'vl_unitario', 'qt_contrato', 'vl_contrato', 'qt_utilizado', 'vl_utilizado', 'qt_saldo', 'vl_saldo', 'id_unidade', 'ano_orcamento', 'cancelado', 'chave', 'seq_item_processo']);

defined('LIMIT_MAX') or define('LIMIT_MAX', 100);
defined('LIMIT_LOGS') or define('LIMIT_LOGS', 50);
defined('LIMIT_HOURS_REPORT') or define('LIMIT_HOURS_REPORT', 100);

defined('MPDF_PATH') or define('MPDF_PATH', __DIR__);
defined('TEMP_FOLDER') or define('TEMP_FOLDER', __DIR__ . '/temp/');

defined('BTN_DEFAULT') or define('BTN_DEFAULT', 'btn btn-default');
defined('BTN_DANGER') or define('BTN_DANGER', 'btn btn-danger');
defined('MAX_UPLOAD_SIZE') or define('MAX_UPLOAD_SIZE', 3);

defined('ARRAY_DATABASES') or define('ARRAY_DATABASES', ['main', 'sof_2018', 'sof_2017']);

// faturamento AIHS
defined('ARRAY_PRODUCAO') or define('ARRAY_PRODUCAO', [NULL, 'SIA', 'SIH']);
defined('ARRAY_FINANCIAMENTO') or define('ARRAY_FINANCIAMENTO', [NULL, 'FAEC', 'MAC', 'NAO SE APLICA']);
defined('ARRAY_COMPLEXIDADE') or define('ARRAY_COMPLEXIDADE', [NULL, 'MC', 'AC']);
