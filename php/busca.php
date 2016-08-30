<?php
/**
 *	Todas as funções que fizerem consultas e no banco para retornar à tela, serão
 *	encaminhadas para este arquivo
 *
 *	existem algumas variáveis controladoras, tal como 'admin', 'form' e 'user'
 *
 *	se a variável admin existir, então a ação foi feita por um usuário do SOF e deve ser
 *	autenticada com isset($_SESSION['id_setor']) && $_SESSION['id_setor'] == 2
 *
 *	form controla o que fazer quando este arquivo for chamado
 *
 *	user chama funções que podem ser feitas por todos os setores (inclusive o SOF)
 *
 *	@author João Bolsson
 *	@since Version 1.0
 *
 */
ini_set('display_erros', true);
error_reporting(E_ALL);

session_start();
include_once '../class/Busca.class.php';
//instanciando classe de busca
$obj_Busca = new Busca();

if (isset($_POST["admin"]) && isset($_SESSION['id_setor']) && $_SESSION['id_setor'] == 2) {
	$form = $_POST["form"];

	switch ($form) {

	// comment.
	case 'listLancamentos':
		echo $obj_Busca->getLancamentos($_SESSION['id_setor']);
		break;
	// comment.

	case 'relatorioProcessos':
		$_SESSION["relatorioProcessos"] = 1;
		$_SESSION["relatorioTipo"] = $_POST["tipo"];
		break;

	// comment.

	case 'listProcessos':
		echo $obj_Busca->getProcessos("recepcao");
		break;

	// comentário
	case 'addProcesso':
		$id_processo = $_POST["id_processo"];
		echo $obj_Busca->getInfoProcesso($id_processo);
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

	// comentário

	case 'editarNoticia':
		$id = $_POST["id"];
		echo $obj_Busca->getPublicacaoEditar($id);
		break;

	// comentário

	case 'carregaPostsPag':
		$tabela = $_POST["tabela"];
		echo $obj_Busca->getNoticiasEditar($tabela);
		break;

	// quando clico no pedido para fazer a análise, preenche saldo, status, etc... (não a tabela com os itens)

	case 'infoPedido':
		$id_pedido = $_POST["id_pedido"];
		$id_setor = $_POST['id_setor'];
		echo $obj_Busca->getInfoPedidoAnalise($id_pedido, $id_setor);
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
	case 'verEmpenho':
		$id_pedido = $_POST['id_pedido'];
		echo $obj_Busca->verEmpenho($id_pedido);
		break;
	// comment.
	case 'listLancamentos':
		echo $obj_Busca->getLancamentos($_SESSION['id_setor']);
		break;

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

	// popula rascunho

	case 'populaRascunho':
		$id_pedido = $_POST["id_pedido"];
		$id_setor = $_SESSION['id_setor'];

		echo $obj_Busca->getPopulaRascunho($id_pedido, $id_setor);
		break;

	// edita pedido

	case 'editaPedido':
		$id_pedido = $_POST["id_pedido"];

		echo $obj_Busca->getConteudoPedido($id_pedido);
		break;

	// imprime pedido
	case 'imprimirPedido':
		$_SESSION["imprimirPedido"] = 1;
		$_SESSION["id_ped_imp"] = $_POST["id_pedido"];
		$_SESSION["id_pedido_setor"] = $obj_Busca->getSetorPedido($_POST["id_pedido"]);
		$_SESSION['pedido_rascunho'] = $obj_Busca->getRequestDraft($_POST['id_pedido']);
		break;

	default:
		break;
	}
} else {
	$form = $_POST["form"];

	switch ($form) {

	// pesquisa de notícias

	case 'pesquisar':
		$busca = $_POST["busca"];
		echo $obj_Busca->pesquisar($busca);
		break;

	// ver notícia

	case 'ver_noticia':
		$_SESSION["id_noticia"] = $_POST["id"];
		$_SESSION["pag"] = $_POST["tabela"];
		$_SESSION["slide"] = $_POST["slide"];
		break;

	// seta inputs para adicionar arquivos

	case 'addInputsArquivo':
		$qtd = $_POST["qtd"];
		echo $obj_Busca->setInputsArquivo($qtd);
		break;
	default:
		// remove all session variables
		session_unset();
		// destroy the session
		session_destroy();
		break;
	}
}
?>
