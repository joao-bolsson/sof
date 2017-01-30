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
$obj_Busca = new BuscaLTE();

$admin = filter_input(INPUT_POST, "admin");
$users = filter_input(INPUT_POST, "users");

if (!is_null($admin) && isset($_SESSION['id_setor']) && ($_SESSION['id_setor'] == 2 || $_SESSION['id_setor'] == 12)) {
    $form = '';

    $filter = filter_input(INPUT_POST, 'form');
    if (!is_null($filter)) {
        $form = $filter;
    }

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
} else if (!is_null($users)&& isset($_SESSION['id_setor']) && $_SESSION['id_setor'] != 0) {
    $form = '';

    $filter = filter_input(INPUT_POST, 'form');
    if (!is_null($filter)) {
        $form = $filter;
    }

    switch ($form) {
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
