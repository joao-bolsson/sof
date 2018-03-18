<?php

/**
 *    Todas as funções que fizerem consultas e no banco para retornar à tela, serão
 *    encaminhadas para este arquivo
 *
 *    existem algumas variáveis controladoras, tal como 'admin', 'form' e 'user'
 *
 *    se a variável admin existir, então a ação foi feita por um usuário do SOF e deve ser
 *    autenticada com isset($_SESSION['id_setor']) && $_SESSION['id_setor'] == 2
 *
 *    form controla o que fazer quando este arquivo for chamado
 *
 *    user chama funções que podem ser feitas por todos os setores (inclusive o SOF)
 *
 * @author João Bolsson (joaovictorbolsson@gmail.com)
 * @since 2017, 15 Jan.
 *
 */
ini_set('display_errors', true);
error_reporting(E_ALL);

session_start();
spl_autoload_register(function (string $class_name) {
    include_once '../class/' . $class_name . '.class.php';
});

$admin = filter_input(INPUT_POST, "admin");
$users = filter_input(INPUT_POST, "users");

$form = '';

$filter = filter_input(INPUT_POST, 'form');
if (!is_null($filter)) {
    $form = $filter;
}

if (!is_null($admin) && isset($_SESSION['id_setor']) && ($_SESSION['id_setor'] == 2 || $_SESSION['id_setor'] == 12)) {

    switch ($form) {

        case 'procNaoDev':
            echo BuscaLTE::getProcNaoDev();
            break;

        case 'tableProcVenc':
            echo BuscaLTE::loadProcsVenc();
            break;

        case 'cadEmpenho':
            $id_pedido = filter_input(INPUT_POST, 'id_pedido');
            echo BuscaLTE::getEmpenho($id_pedido);
            break;

        case 'cadFontes':
            $id_pedido = filter_input(INPUT_POST, 'id_pedido');
            echo BuscaLTE::getSources($id_pedido);
            break;

        case 'getUsers':
            echo BuscaLTE::getUsers();
            break;

        case 'loadAdminTable':
            echo BuscaLTE::loadAdminTable();
            break;

        case 'refreshTableHora':
            echo BuscaLTE::refreshTableHora();
            break;

        case 'carregaPostsPag':
            $tabela = filter_input(INPUT_POST, 'tabela');
            echo BuscaLTE::getNoticiasEditar($tabela);
            break;

        case 'customRel':
            $pedidos = filter_input(INPUT_POST, 'pedidos', FILTER_DEFAULT, FILTER_FORCE_ARRAY);
            $_SESSION['pedidosRel'] = $pedidos;
            break;

        case 'listProcessos':
            echo BuscaLTE::getProcessos("recepcao");
            break;

        case 'tableRecepcao':
            echo BuscaLTE::getTabelaRecepcao();
            break;

        case 'iniTableSolicAltPed':
            $status = filter_input(INPUT_POST, 'status');
            echo BuscaLTE::getAdminSolicAltPedidos($status);
            break;

        case 'tableItensPedido':
            $limit1 = filter_input(INPUT_POST, 'limit1');
            $limit2 = filter_input(INPUT_POST, 'limit2');

            // se != 0 atualiza toda a tabela, se não, apenas a linha do pedido
            $id_pedido = filter_input(INPUT_POST, 'id_pedido');

            $array_pedidos = filter_input(INPUT_POST, 'pedidos', FILTER_DEFAULT, FILTER_FORCE_ARRAY);
            if (empty($array_pedidos)) {
                $array_pedidos = [];
            }
            $where = '';

            if ($limit1 < $limit2) {
                $where = 'AND id > ' . $limit1 . ' AND id < ' . $limit2;
            } else if ($limit1 > $limit2) {
                $where = 'AND id > ' . $limit2 . ' AND id < ' . $limit1;
            } else if ($id_pedido != 0) {
                $where = 'AND id = ' . $id_pedido;
            }

            echo BuscaLTE::getSolicitacoesAdmin($where, $array_pedidos);
            break;

        case 'tableSolicitacoesAdiantamento':
            $status = filter_input(INPUT_POST, 'status');
            echo BuscaLTE::getSolicAdiantamentos($status);
            break;

        case 'analisaPedido':
            $id_pedido = filter_input(INPUT_POST, 'id_pedido');
            echo BuscaLTE::getItensPedidoAnalise($id_pedido);
            break;
        default:
            break;
    }
} else if (!is_null($users) && isset($_SESSION['id_setor']) && $_SESSION['id_setor'] != 0) {

    switch ($form) {

        case 'showInformation':
            $column = filter_input(INPUT_POST, 'column');
            $table = filter_input(INPUT_POST, 'table');
            $id = filter_input(INPUT_POST, 'id');

            echo BuscaLTE::showInformation($table, $column, $id);
            break;
        case 'listLancamentos':
            $id_setor = filter_input(INPUT_POST, 'id_setor');
            echo BuscaLTE::getLancamentos($id_setor);
            break;

        case 'listRascunhos':
            echo BuscaLTE::getRascunhos();
            break;

        case 'listPedidos':
            $limit1 = filter_input(INPUT_POST, 'limit1');
            $limit2 = filter_input(INPUT_POST, 'limit2');

            $where = '';

            if (!is_null($limit1) && !is_null($limit2)) {
                if ($limit1 < $limit2) {
                    $where = 'AND id > ' . $limit1 . ' AND id < ' . $limit2;
                } else if ($limit1 > $limit2) {
                    $where = 'AND id > ' . $limit2 . ' AND id < ' . $limit1;
                }
            }

            $array_pedidos = filter_input(INPUT_POST, 'pedidos', FILTER_DEFAULT, FILTER_FORCE_ARRAY);
            if (empty($array_pedidos)) {
                $array_pedidos = [];
            }

            echo BuscaLTE::getMeusPedidos($where, $array_pedidos);
            break;

        case 'listProcessos':
            echo BuscaLTE::getProcessos("solicitacoes");
            break;

        case 'iniSolicAltPedSetor':
            echo BuscaLTE::getSolicAltPedidos();
            break;

        case 'listAdiantamentos':
            echo BuscaLTE::getSolicAdiSetor();
            break;

        case 'addItemPedido':
            $id_item = filter_input(INPUT_POST, 'id_item');
            $qtd = filter_input(INPUT_POST, 'qtd');

            echo BuscaLTE::addItemPedido($id_item, $qtd);
            break;

        case 'pesquisarProcesso':
            $busca = filter_input(INPUT_POST, 'busca');
            echo BuscaLTE::getConteudoProcesso($busca);
            break;

        case 'editaPedido':
            $id_pedido = filter_input(INPUT_POST, 'id_pedido');

            echo BuscaLTE::getConteudoPedido($id_pedido);
            break;

        case 'imprimirPedido':
            $id_pedido = filter_input(INPUT_POST, 'id_pedido');
            $_SESSION["imprimirPedido"] = 1;
            $_SESSION["id_ped_imp"] = $id_pedido;
            $_SESSION['pedido_rascunho'] = BuscaLTE::getRequestDraft($id_pedido);
            break;

        default:
            echo "<tr><td>2016</td><td>10</td></tr>";
            break;
    }
} else {
    exit('Página inválida');
}
