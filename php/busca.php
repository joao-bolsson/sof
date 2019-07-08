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
 * @author João Bolsson
 * @since Version 1.0
 *
 */
ini_set('display_errors', true);
error_reporting(E_ALL);

session_start();
include_once '../class/Busca.class.php';
include_once '../class/Util.class.php';

$admin = filter_input(INPUT_POST, "admin");
$users = filter_input(INPUT_POST, "users");

$form = '';

$filter = filter_input(INPUT_POST, 'form');
if (!is_null($filter)) {
    $form = $filter;
}

if (!is_null($admin) && isset($_SESSION['id_setor']) && ($_SESSION['id_setor'] == 2 || $_SESSION['id_setor'] == 12)) {

    switch ($form) {

        case 'selectGroupMens':
            $id = filter_input(INPUT_POST, 'id_contr');
            echo Busca::getAlGroupsByContract($id);
            break;

        case 'getAllContracts':
            echo Busca::getAllContracts();
            break;

        case 'editMens':
            $id = filter_input(INPUT_POST, 'id');

            echo json_encode(Busca::getEditMens($id));
            break;

        case 'showMensalidades':
            $id = filter_input(INPUT_POST, 'id_contr');
            echo Busca::fillTableMens($id);
            break;

        case 'editContract':
            $id = filter_input(INPUT_POST, 'id');
            echo json_encode(Busca::editContract($id));
            break;

        case 'fillTableProc':
            $grupo = filter_input(INPUT_POST, 'group');
            echo Busca::fillTableProc($grupo);
            break;

        case 'fillContratos':
            echo Busca::fillContracts();
            break;

        case 'editLog':
            $id = filter_input(INPUT_POST, 'id');
            echo Busca::getInfoLog($id);
            break;

        case 'consultaHora':
            echo Busca::getInfoTime();
            break;

        case 'listProblemas':
            echo Busca::getProblemas();
            break;

        case 'getSaldoOri':
            $id_setor = filter_input(INPUT_POST, 'setorOri');
            echo Busca::getSaldo($id_setor);
            break;

        case 'infoItem':
            $id_item = filter_input(INPUT_POST, 'id_item');
            echo json_encode(new Item($id_item));
            break;

        case 'permissoes':
            echo json_encode(Busca::getPermissoes($_SESSION["id"]));
            break;

        case 'relatorioProcessos':
            $_SESSION["relatorioProcessos"] = 1;
            $_SESSION["relatorioTipo"] = filter_input(INPUT_POST, 'tipo');
            break;

        case 'addProcesso':
            $id_processo = filter_input(INPUT_POST, 'id_processo');
            echo Busca::getInfoProcesso($id_processo);
            break;

        case 'editarNoticia':
            $id = filter_input(INPUT_POST, 'id');
            echo Busca::getPublicacaoEditar($id);
            break;

        case 'infoPedido':
            $id_pedido = filter_input(INPUT_POST, 'id_pedido');
            $id_setor = filter_input(INPUT_POST, 'id_setor');
            echo Busca::getInfoPedidoAnalise($id_pedido, $id_setor);
            break;

        case 'fillTransfSource':
            $id_setor = filter_input(INPUT_POST, 'id');
            echo Busca::getSources($id_setor);
            break;

        default:
            break;
    }
} else if (!is_null($users) && isset($_SESSION['id_setor']) && $_SESSION['id_setor'] != 0) {

    switch ($form){

        case 'editReceita':
            $id = filter_input(INPUT_POST, 'id');
            echo json_encode(Busca::getReceita($id));
            break;

        case 'editAIHS':
            $id = filter_input(INPUT_POST, 'id');
            echo json_encode(Busca::getAIHS($id));
            break;

        case 'fillSourcesToSector':
            $id_setor = filter_input(INPUT_POST, 'id_setor');
            echo Busca::getSourcesForSector($id_setor);
            break;

        case 'fillSaldo':
            echo number_format(Busca::getSaldo($_SESSION['id_setor']), 3, ',', '.');
            break;

        case 'getSaldo':
            echo Busca::getSaldo($_SESSION['id_setor']);
            break;

        case 'populaPlano':
            $id_pedido = filter_input(INPUT_POST, 'id_pedido');
            if (!empty($id_pedido)) {
                echo Busca::getInfoPlano($id_pedido);
            }
            break;

        case 'populaContrato':
            $id_pedido = filter_input(INPUT_POST, 'id_pedido');
            if (empty($id_pedido)) {
                break;
            }
            echo Busca::getInfoContrato($id_pedido);
            break;

        case 'populaGrupo':
            $id_pedido = filter_input(INPUT_POST, 'id_pedido');
            echo Busca::getGrupo($id_pedido);
            break;

        case 'populaLicitacao':
            $pedido = filter_input(INPUT_POST, 'id_pedido');
            echo Busca::getLicitacao($pedido);
            break;

        case 'getNomeSetor':
            $id_setor = filter_input(INPUT_POST, 'id_setor');
            echo Busca::getSetorNome($id_setor);
            break;

        case 'refreshTotSaldos':
            $id_setor = filter_input(INPUT_POST, 'id_setor');
            echo Busca::getTotalInOutSaldos($id_setor);
            break;

        case 'number_format' :
            $value = filter_input(INPUT_POST, 'value');
            $valor = number_format($value, 3, ',', '.');
            echo $valor;
            break;

        case 'verEmpenho':
            $id_pedido = filter_input(INPUT_POST, 'id_pedido');
            echo Busca::verEmpenho($id_pedido);
            break;

        case 'populaRascunho':
            $id_pedido = filter_input(INPUT_POST, 'id_pedido');
            $id_setor = $_SESSION['id_setor'];

            echo Busca::getPopulaRascunho($id_pedido, $id_setor);
            break;

        default:
            echo "<tr><td>2016</td><td>10</td></tr>";
            break;
    }
} else {

    switch ($form) {

        case 'pesquisar':
            $busca = filter_input(INPUT_POST, 'busca');
            echo Busca::pesquisar($busca);
            break;

        case 'ver_noticia':
            $_SESSION["id_noticia"] = filter_input(INPUT_POST, 'id');
            $_SESSION["pag"] = filter_input(INPUT_POST, 'tabela');
            $_SESSION["slide"] = filter_input(INPUT_POST, 'slide');
            break;

        case 'addInputsArquivo':
            $qtd = filter_input(INPUT_POST, 'qtd');
            echo Util::setInputsArquivo($qtd);
            break;
        default:
            // remove all session variables
            session_unset();
            // destroy the session
            session_destroy();
            break;
    }
}
