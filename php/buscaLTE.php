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
spl_autoload_register(function (string $class_name) {
    include_once '../class/' . $class_name . '.class.php';
});
$obj_Busca = BuscaLTE::getInstance();

$admin = filter_input(INPUT_POST, "admin");
$users = filter_input(INPUT_POST, "users");

$form = '';

$filter = filter_input(INPUT_POST, 'form');
if (!is_null($filter)) {
    $form = $filter;
}

if (!is_null($admin) && isset($_SESSION['id_setor']) && ($_SESSION['id_setor'] == 2 || $_SESSION['id_setor'] == 12)) {

    switch ($form) {
        
        case 'loadAdminTable':
            echo $obj_Busca->loadAdminTable();
            break;

        case 'refreshTableHora':
            echo $obj_Busca->refreshTableHora();
            break;

        case 'carregaPostsPag':
            $tabela = filter_input(INPUT_POST, 'tabela');
            echo $obj_Busca->getNoticiasEditar($tabela);
            break;

        case 'customRel':
            $pedidos = filter_input(INPUT_POST, 'pedidos', FILTER_DEFAULT, FILTER_FORCE_ARRAY);
            $_SESSION['pedidosRel'] = $pedidos;
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

            echo $obj_Busca->getSolicitacoesAdmin($where, $array_pedidos);
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
            echo $obj_Busca->getRascunhos();
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
                } else if ($id_pedido != 0) {
                    $where = 'AND id = ' . $id_pedido;
                }
            }

            $array_pedidos = filter_input(INPUT_POST, 'pedidos', FILTER_DEFAULT, FILTER_FORCE_ARRAY);
            if (empty($array_pedidos)) {
                $array_pedidos = [];
            }

            echo $obj_Busca->getMeusPedidos($where, $array_pedidos);
            break;

        case 'listProcessos':
            echo $obj_Busca->getProcessos("solicitacoes");
            break;

        case 'iniSolicAltPedSetor':
            echo $obj_Busca->getSolicAltPedidos();
            break;

        case 'listAdiantamentos':
            echo $obj_Busca->getSolicAdiSetor();
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
