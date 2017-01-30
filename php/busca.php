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
 * 	@author João Bolsson
 * 	@since Version 1.0
 *
 */
ini_set('display_erros', true);
error_reporting(E_ALL);

session_start();
include_once '../class/Busca.class.php';
include_once '../class/Util.class.php';
//instanciando classe de busca
$obj_Busca = new Busca();

$admin = filter_input(INPUT_POST, "admin");
$users = filter_input(INPUT_POST, "users");

$form = '';

$filter = filter_input(INPUT_POST, 'form');
if (!is_null($filter)) {
    $form = $filter;
}

if (!is_null($admin) && isset($_SESSION['id_setor']) && ($_SESSION['id_setor'] == 2 || $_SESSION['id_setor'] == 12)) {

    switch ($form) {

        // retorna o total dos pedidos pelo status selecionado
        case 'refreshTot':
            $status = filter_input(INPUT_POST, 'status');
            echo $obj_Busca->getTotalByStatus($status);
            break;
        // visualiza os processos de um pedido com suas datas
        case 'verProcessos':
            $id_pedido = filter_input(INPUT_POST, 'id_pedido');
            echo $obj_Busca->getProcessosPedido($id_pedido);
            break;
        // relatórios nova versão
        case 'listRelatorios':
            $status = filter_input(INPUT_POST, 'status');
            echo $obj_Busca->getRelatorio($status);
            break;
        // gerencia de problemas relatados
        case 'listProblemas':
            echo $obj_Busca->getProblemas();
            break;

        // comment.
        case 'getSaldoOri':
            $id_setor = filter_input(INPUT_POST, 'setorOri');
            echo $obj_Busca->getSaldo($id_setor);
            break;
        // comment.
        case 'refreshSaldo':
            echo $obj_Busca->getSaldo($_SESSION['id_setor']);
            break;
        // comment.
        case 'infoItem':
            $id_item = filter_input(INPUT_POST, 'id_item');
            echo $obj_Busca->getInfoItem($id_item);
            break;
        // comment.
        case 'permissoes':
            echo json_encode($obj_Busca->getPermissoes($_SESSION["id"]));
            break;
        // comment.

        case 'relatorioProcessos':
            $_SESSION["relatorioProcessos"] = 1;
            $_SESSION["relatorioTipo"] = filter_input(INPUT_POST, 'tipo');
            break;

        // comentário
        case 'addProcesso':
            $id_processo = filter_input(INPUT_POST, 'id_processo');
            echo $obj_Busca->getInfoProcesso($id_processo);
            break;

        case 'editarNoticia':
            $id = filter_input(INPUT_POST, 'id');
            echo $obj_Busca->getPublicacaoEditar($id);
            break;

        // comentário

        case 'carregaPostsPag':
            $tabela = filter_input(INPUT_POST, 'tabela');
            echo $obj_Busca->getNoticiasEditar($tabela);
            break;

        // quando clico no pedido para fazer a análise, preenche saldo, status, etc... (não a tabela com os itens)

        case 'infoPedido':
            $id_pedido = filter_input(INPUT_POST, 'id_pedido');
            $id_setor = filter_input(INPUT_POST, 'id_setor');
            echo $obj_Busca->getInfoPedidoAnalise($id_pedido, $id_setor);
            break;

        default:
            break;
    }
} else if (!is_null($users) && isset($_SESSION['id_setor']) && $_SESSION['id_setor'] != 0) {

    switch ($form) {

        case 'fillSaldo':
            echo number_format($obj_Busca->getSaldo($_SESSION['id_setor']), 3, ',', '.');
            break;

        case 'getSaldo':
            echo $obj_Busca->getSaldo($_SESSION['id_setor']);
            break;

        case 'populaContrato':
            $id_pedido = filter_input(INPUT_POST, 'id_pedido');
            if (empty($id_pedido)) {
                break;
            }
            echo $obj_Busca->getInfoContrato($id_pedido);
            break;

        case 'populaGrupo':
            $id_pedido = filter_input(INPUT_POST, 'id_pedido');
            echo $obj_Busca->getGrupo($id_pedido);
            break;
        // retorna um objeto com as informações da licitação do pedido
        case 'populaLicitacao':
            $pedido = filter_input(INPUT_POST, 'id_pedido');
            echo $obj_Busca->getLicitacao($pedido);
            break;

        // retornar o nome do setor
        case 'getNomeSetor':
            $id_setor = filter_input(INPUT_POST, 'id_setor');
            echo $obj_Busca->getSetorNome($id_setor);
            break;
        // atualiza os valores de entradas e saídas do setor selecionado
        case 'refreshTotSaldos':
            $id_setor = filter_input(INPUT_POST, 'id_setor');
            echo $obj_Busca->getTotalInOutSaldos($id_setor);
            break;
        // return the number format as 1.000,000 (mil)
        case 'number_format' :
            $value = filter_input(INPUT_POST, 'value');
            $valor = number_format($value, 3, ',', '.');
            echo $valor;
            break;
        // comment.
        case 'verEmpenho':
            $id_pedido = filter_input(INPUT_POST, 'id_pedido');
            echo $obj_Busca->verEmpenho($id_pedido);
            break;
        // comment.
        case 'listLancamentos':
            $id_setor = filter_input(INPUT_POST, 'id_setor');
            echo $obj_Busca->getLancamentos($id_setor);
            break;

        // popula rascunho

        case 'populaRascunho':
            $id_pedido = filter_input(INPUT_POST, 'id_pedido');
            $id_setor = $_SESSION['id_setor'];

            echo $obj_Busca->getPopulaRascunho($id_pedido, $id_setor);
            break;

        default:
            echo "<tr><td>2016</td><td>10</td></tr>";
            break;
    }
} else {

    switch ($form) {

        // pesquisa de notícias

        case 'pesquisar':
            $busca = filter_input(INPUT_POST, 'busca');
            echo $obj_Busca->pesquisar($busca);
            break;

        // ver notícia

        case 'ver_noticia':
            $_SESSION["id_noticia"] = filter_input(INPUT_POST, 'id');
            $_SESSION["pag"] = filter_input(INPUT_POST, 'tabela');
            $_SESSION["slide"] = filter_input(INPUT_POST, 'slide');
            break;

        // seta inputs para adicionar arquivos

        case 'addInputsArquivo':
            $qtd = filter_input(INPUT_POST, 'qtd');
            $obj_Util = new Util();
            echo $obj_Util->setInputsArquivo($qtd);
            break;
        default:
            // remove all session variables
            session_unset();
            // destroy the session
            session_destroy();
            break;
    }
}
