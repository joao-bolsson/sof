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
$obj_Busca = Busca::getInstance();

$admin = filter_input(INPUT_POST, "admin");
$users = filter_input(INPUT_POST, "users");

$form = '';

$filter = filter_input(INPUT_POST, 'form');
if (!is_null($filter)) {
    $form = $filter;
}

if (!is_null($admin) && isset($_SESSION['id_setor']) && ($_SESSION['id_setor'] == 2 || $_SESSION['id_setor'] == 12)) {

    switch ($form) {

        case 'verProcessos':
            $id_pedido = filter_input(INPUT_POST, 'id_pedido');
            echo $obj_Busca->getProcessosPedido($id_pedido);
            break;

        case 'listProblemas':
            echo $obj_Busca->getProblemas();
            break;

        case 'getSaldoOri':
            $id_setor = filter_input(INPUT_POST, 'setorOri');
            echo $obj_Busca->getSaldo($id_setor);
            break;

        case 'refreshSaldo':
            echo $obj_Busca->getSaldo($_SESSION['id_setor']);
            break;

        case 'infoItem':
            $id_item = filter_input(INPUT_POST, 'id_item');
            echo $obj_Busca->getInfoItem($id_item);
            break;

        case 'permissoes':
            echo json_encode($obj_Busca->getPermissoes($_SESSION["id"]));
            break;

        case 'relatorioProcessos':
            $_SESSION["relatorioProcessos"] = 1;
            $_SESSION["relatorioTipo"] = filter_input(INPUT_POST, 'tipo');
            break;

        case 'addProcesso':
            $id_processo = filter_input(INPUT_POST, 'id_processo');
            echo $obj_Busca->getInfoProcesso($id_processo);
            break;

        case 'editarNoticia':
            $id = filter_input(INPUT_POST, 'id');
            echo $obj_Busca->getPublicacaoEditar($id);
            break;

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

        case 'populaLicitacao':
            $pedido = filter_input(INPUT_POST, 'id_pedido');
            echo $obj_Busca->getLicitacao($pedido);
            break;

        case 'getNomeSetor':
            $id_setor = filter_input(INPUT_POST, 'id_setor');
            echo $obj_Busca->getSetorNome($id_setor);
            break;

        case 'refreshTotSaldos':
            $id_setor = filter_input(INPUT_POST, 'id_setor');
            echo $obj_Busca->getTotalInOutSaldos($id_setor);
            break;

        case 'number_format' :
            $value = filter_input(INPUT_POST, 'value');
            $valor = number_format($value, 3, ',', '.');
            echo $valor;
            break;

        case 'verEmpenho':
            $id_pedido = filter_input(INPUT_POST, 'id_pedido');
            echo $obj_Busca->verEmpenho($id_pedido);
            break;

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

        case 'pesquisar':
            $busca = filter_input(INPUT_POST, 'busca');
            echo $obj_Busca->pesquisar($busca);
            break;

        case 'ver_noticia':
            $_SESSION["id_noticia"] = filter_input(INPUT_POST, 'id');
            $_SESSION["pag"] = filter_input(INPUT_POST, 'tabela');
            $_SESSION["slide"] = filter_input(INPUT_POST, 'slide');
            break;

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
