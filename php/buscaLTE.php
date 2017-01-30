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

if (isset($_POST["admin"]) && isset($_SESSION['id_setor']) && ($_SESSION['id_setor'] == 2 || $_SESSION['id_setor'] == 12)) {
    $form = $_POST["form"];

    switch ($form) {

        case 'customRel':
            $pedidos = filter_input(INPUT_POST, 'pedidos', FILTER_DEFAULT, FILTER_FORCE_ARRAY);
            $_SESSION['pedidosRel'] = $pedidos;
            break;

        // relatórios nova versão
        case 'listRelatorios':
            echo $obj_Busca->getRelatorio($_POST['status']);
            break;
        // comment.

        case 'listProcessos':
            echo $obj_Busca->getProcessos("recepcao");
            break;

        // retornar a tabela com os processos para edição na recepção
        case 'tableRecepcao':
            echo $obj_Busca->getTabelaRecepcao();
            break;

        // retornar a tabela com as solicitações de alteração de pedidos

        case 'iniTableSolicAltPed':
            $status = $_POST["status"];
            echo $obj_Busca->getAdminSolicAltPedidos($status);
            break;

        // comentário

        case 'tableItensPedido':
            echo $obj_Busca->getSolicitacoesAdmin();
            break;

        // comentário

        case 'tableSolicitacoesAdiantamento':
            $status = $_POST["status"];
            echo $obj_Busca->getSolicAdiantamentos($status);
            break;

        // busca a tabela com os itens do pedido analisado

        case 'analisaPedido':
            $id_pedido = $_POST["id_pedido"];

            echo $obj_Busca->getItensPedidoAnalise($id_pedido);
            break;
        default:
            break;
    }
} else if (isset($_POST["users"]) && isset($_SESSION['id_setor']) && $_SESSION['id_setor'] != 0) {
    $form = $_POST["form"];

    switch ($form) {
        // comment.
        case 'listRascunhos':
            echo $obj_Busca->getRascunhos($_SESSION['id_setor']);
            break;

        // comment.

        case 'listPedidos':
            echo $obj_Busca->getMeusPedidos($_SESSION['id_setor']);
            break;
        // comment.

        case 'listProcessos':
            echo $obj_Busca->getProcessos("solicitacoes");
            break;
        // comment.

        case 'iniSolicAltPedSetor':
            echo $obj_Busca->getSolicAltPedidos($_SESSION['id_setor']);
            break;
        // retorna a tabela de solicitações de adiantamento de um setor

        case 'listAdiantamentos':
            echo $obj_Busca->getSolicAdiSetor($_SESSION['id_setor']);
            break;

        // adiciona um item ao pedido

        case 'addItemPedido':
            $id_item = $_POST["id_item"];
            $qtd = $_POST["qtd"];

            echo $obj_Busca->addItemPedido($id_item, $qtd);
            break;

        // pesquisar processo

        case 'pesquisarProcesso':
            $busca = $_POST["busca"];
            echo $obj_Busca->getConteudoProcesso($busca);
            break;

        // edita pedido
        case 'editaPedido':
            $id_pedido = filter_input(INPUT_POST, 'id_pedido');

            echo $obj_Busca->getConteudoPedido($id_pedido);
            break;

        // imprime pedido
        case 'imprimirPedido':
            $_SESSION["imprimirPedido"] = 1;
            $_SESSION["id_ped_imp"] = $_POST["id_pedido"];
            $_SESSION['pedido_rascunho'] = $obj_Busca->getRequestDraft($_POST['id_pedido']);
            break;

        default:
            echo "<tr><td>2016</td><td>10</td></tr>";
            break;
    }
} else {
    exit('Página inválida');
}
