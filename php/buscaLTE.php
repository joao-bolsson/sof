<?php

/**
 * 	Todas as funções que fizerem consultas e no banco para retornar à tela, serão
 * 	encaminhadas para este arquivo
 *
 * 	existem algumas variáveis controladoras, tal como 'admin', 'form' e 'user'
 *
 * 	se a variável admin existir, então a ação foi feita por um usuário do SOF e deve ser
 * 	autenticada com isset($_SESSION['id_setor']) && $_SESSION['id_setor'] == 2
 *
 * 	form controla o que fazer quando este arquivo for chamado
 *
 * 	user chama funções que podem ser feitas por todos os setores (inclusive o SOF)
 *
 * 	@author João Bolsson (joaovictorbolsson@gmail.com)
 * 	@since 2017, 15 Jan.
 *
 */
ini_set('display_erros', true);
error_reporting(E_ALL);

session_start();
include_once '../class/BuscaLTE.class.php';
include_once '../class/Util.class.php';
require_once '../defines.php';
$obj_Busca = new BuscaLTE();

$admin = filter_input(INPUT_POST, "admin");
$users = filter_input(INPUT_POST, "users");

$form = '';

$filter = filter_input(INPUT_POST, 'form');
if (!is_null($filter)) {
    $form = $filter;
}

if (!is_null($admin) && isset($_SESSION['id_setor']) && ($_SESSION['id_setor'] == 2 || $_SESSION['id_setor'] == 12)) {

    switch ($form) {

        case 'customRel':
            $pedidos = filter_input(INPUT_POST, 'pedidos', FILTER_DEFAULT, FILTER_FORCE_ARRAY);
            $_SESSION['pedidosRel'] = $pedidos;
            break;

        case 'listRelatorios':
            $status = filter_input(INPUT_POST, 'status');
            echo $obj_Busca->getRelatorio($status);
            break;

        case 'listProcessos':
            echo $obj_Busca->getProcessos("recepcao");
            break;

        case 'tableRecepcao':
            echo $obj_Busca->getTabelaRecepcao();
            break;

        case 'iniTableSolicAltPed':
            $status = filter_input(INPUT_POST, 'status');
            echo $obj_Busca->getAdminSolicAltPedidos($status);
            break;

        case 'tableItensPedido':
            echo $obj_Busca->getSolicitacoesAdmin();
            break;

        case 'tableItensPedidoSSP':
            // DB table to use
            $table = 'pedido';

            // Table's primary key
            $primaryKey = 'id';

            // Array of database columns which should be read and sent back to DataTables.
            // The `db` parameter represents the column name in the database, while the `dt`
            // parameter represents the DataTables column identifier. In this case simple
            // indexes
            $columns = array(
                array('db' => 'id', 'dt' => 'DT_RowId',
                    'formatter' => function($d, $row) {
                        return 'rowPedido' . $d;
                    }
                ),
                array('db' => 'id', 'dt' => 'selectAll'),
                array('db' => 'id', 'dt' => 'buttons',
                    'formatter' => function($d, $row) {
                        $obj_Busca = new BuscaLTE();
                        $btnAnalisar = "";
                        $btnGroup = "<div class=\"btn-group\">";
                        $status = ARRAY_STATUS[$row['status']];
                        if ($status != 'Reprovado' && $status != 'Aprovado') {
                            if ($_SESSION['id_setor'] == 12) {
                                $btnAnalisar = "<button type=\"button\" class=\"btn btn-default\" onclick=\"enviaForn(" . $d . ");\" data-toggle=\"tooltip\" title=\"Enviar ao Fornecedor\"><i class=\"fa fa-send\"></i></button>";
                            } else if ($status == 'Em Analise') {
                                $btnAnalisar = "<button type=\"button\" class=\"btn btn-default\" onclick=\"analisarPedido(" . $d . ", " . $row['id_setor'] . ");\" data-toggle=\"tooltip\" title=\"Analisar\"><i class=\"fa fa-pencil\"></i></button>";
                            } else if ($status == 'Aguarda Orcamento') {
                                $btnAnalisar = "<button type=\"button\" class=\"btn btn-default\" onclick=\"cadFontes(" . $d . ");\" data-toggle=\"tooltip\" title=\"Cadastrar Fontes\"><i class=\"fa fa-comment\"></i></button>";
                            } else if ($status == 'Aguarda SIAFI') {
                                $btnAnalisar = "<button type=\"button\" class=\"btn btn-default\" onclick=\"cadEmpenho(" . $d . ");\" data-toggle=\"tooltip\" title=\"Cadastrar Empenho\"><i class=\"fa fa-credit-card\"></i></button>";
                            } else if ($status == 'Empenhado') {
                                $btnAnalisar = "<button type=\"button\" class=\"btn btn-default\" onclick=\"enviaOrdenador(" . $d . ");\" data-toggle=\"tooltip\" title=\"Enviar ao Ordenador\"><i class=\"fa fa-send\"></i></button>";
                            } else {
                                $btnAnalisar = "<button type=\"button\" class=\"btn btn-default\" onclick=\"getStatus(" . $d . ", " . $row['id_setor'] . ");\" data-toggle=\"tooltip\" title=\"Alterar Status\"><i class=\"fa fa-wrench\"></i></button>";
                            }
                        }

                        if ($_SESSION['id_setor'] != 12 && $row['status'] > 6) {
                            $btnAnalisar .= "<button type=\"button\" class=\"btn btn-default\" onclick=\"cadEmpenho(" . $d . ", '" . $obj_Busca->verEmpenho($d) . "', '" . $obj_Busca->verDataEmpenho($d) . "');\" data-toggle=\"tooltip\" title=\"Cadastrar Empenho\"><i class=\"fa fa-credit-card\"></i></button>";
                        }

                        $btnAnalisar .= "<button type=\"button\" class=\"btn btn-default\" onclick=\"imprimir(" . $d . ");\" data-toggle=\"tooltip\" title=\"Imprimir\"><i class=\"fa fa-print\"></i></button>";

                        $btnGroup .= $btnAnalisar . "</div>";

                        return $btnGroup;
                    }),
                array('db' => 'id', 'dt' => 'id'),
                array('db' => 'data_pedido', 'dt' => 'data_pedido',
                    'formatter' => function($d, $row) {
                        return date('d/m/Y', strtotime($d));
                    }),
                array('db' => 'status', 'dt' => 'status',
                    'formatter' => function($d, $row) {
                        return ARRAY_STATUS[$d];
                    }),
                array('db' => 'prioridade', 'dt' => 'prioridade'),
                array('db' => 'aprov_gerencia', 'dt' => 'aprov_gerencia'),
                array('db' => 'valor', 'dt' => 'valor'),
                array('db' => 'id_setor', 'dt' => 'id_setor',
                    'formatter' => function($d, $row) {
                        return ARRAY_SETORES[$d];
                    }),
                array('db' => 'ref_mes', 'dt' => 'ref_mes',
                    'formatter' => function($d, $row) {
                        return ARRAY_MES[$d];
                    }),
                array('db' => 'obs', 'dt' => 'obs')
            );

            $custom_where = "status <> 1";

            // SQL server connection information
            $sql_details = array(
                'user' => 'root',
                'pass' => 'j:03984082037@[]ccufsm',
                'db' => 'sof',
                'host' => 'localhost'
            );

            require('../class/SSP.class.php');

            echo json_encode(SSP::simple($_POST, $sql_details, $table, $primaryKey, $columns, $custom_where));
            break;

        case 'tableSolicitacoesAdiantamento':
            $status = filter_input(INPUT_POST, 'status');
            echo $obj_Busca->getSolicAdiantamentos($status);
            break;

        case 'analisaPedido':
            $id_pedido = filter_input(INPUT_POST, 'id_pedido');
            echo $obj_Busca->getItensPedidoAnalise($id_pedido);
            break;
        default:
            break;
    }
} else if (!is_null($users) && isset($_SESSION['id_setor']) && $_SESSION['id_setor'] != 0) {

    switch ($form) {
        case 'listLancamentos':
            $id_setor = filter_input(INPUT_POST, 'id_setor');
            echo $obj_Busca->getLancamentos($id_setor);
            break;

        case 'listRascunhos':
            echo $obj_Busca->getRascunhos($_SESSION['id_setor']);
            break;

        case 'listPedidos':
            echo $obj_Busca->getMeusPedidos($_SESSION['id_setor']);
            break;

        case 'listProcessos':
            echo $obj_Busca->getProcessos("solicitacoes");
            break;

        case 'iniSolicAltPedSetor':
            echo $obj_Busca->getSolicAltPedidos($_SESSION['id_setor']);
            break;

        case 'listAdiantamentos':
            echo $obj_Busca->getSolicAdiSetor($_SESSION['id_setor']);
            break;

        case 'addItemPedido':
            $id_item = filter_input(INPUT_POST, 'id_item');
            $qtd = filter_input(INPUT_POST, 'qtd');

            echo $obj_Busca->addItemPedido($id_item, $qtd);
            break;

        case 'pesquisarProcesso':
            $busca = filter_input(INPUT_POST, 'busca');
            echo $obj_Busca->getConteudoProcesso($busca);
            break;

        case 'editaPedido':
            $id_pedido = filter_input(INPUT_POST, 'id_pedido');

            echo $obj_Busca->getConteudoPedido($id_pedido);
            break;

        case 'imprimirPedido':
            $id_pedido = filter_input(INPUT_POST, 'id_pedido');
            $_SESSION["imprimirPedido"] = 1;
            $_SESSION["id_ped_imp"] = $id_pedido;
            $_SESSION['pedido_rascunho'] = $obj_Busca->getRequestDraft($id_pedido);
            break;

        default:
            echo "<tr><td>2016</td><td>10</td></tr>";
            break;
    }
} else {
    exit('Página inválida');
}
