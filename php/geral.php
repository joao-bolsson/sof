<?php
/**
 *	Todos os formulários de registro e funções que precisem registrar informações no banco
 *	devem ser mandadas para este arquivo
 *
 *
 *	existem algumas variáveis controladoras, tal como 'admin', 'form' e 'user'
 *
 *	se a variável admin existir, então a ação foi feita por um usuário do SOF e deve ser
 *	autenticada com isset($_SESSION["id_setor"]) && $_SESSION["id_setor"] == 2
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
include_once '../class/Geral.class.php';
include_once '../class/Busca.class.php';
include_once '../class/Util.class.php';
//instanciando a classe Geral
$obj_Geral = new Geral();
//instanciando a classe Busca
$obj_Busca = new Busca();
//instanciando a classe Busca
$obj_Util = new Util();

if (isset($_POST["admin"]) && isset($_SESSION["id_setor"]) && $_SESSION["id_setor"] == 2) {
	// variável que controla o que deve ser feito quando geral.php for chamado
	$form = $_POST["form"];

	switch ($form) {

	// comentário

	case 'recepcao':
		// array com os dados
		$dados = $_POST["dados"];
		$update = $obj_Geral->updateProcesso($dados);

		if ($update) {
			echo "true";
		} else {
			echo "false";
		}

		break;

	// comentário

	case 'importItens':
		// Tamanho máximo do arquivo (em Bytes)
		$_UP['tamanho'] = 1024 * 1024 * 1024 * 1024 * 1024 * 1024 * 1024 * 1024 * 1024 * 1024 * 1024; //
		// Array com as extensões permitidas
		$_UP['extensoes'] = array('tsv');
		// Renomeia o arquivo? (Se true, o arquivo será salvo como .jpg e um nome único)
		$_UP['renomeia'] = false;
		// Array com os tipos de erros de upload do PHP
		$_UP['erros'][0] = 'Não houve erro';
		$_UP['erros'][1] = 'O arquivo no upload é maior do que o limite do PHP';
		$_UP['erros'][2] = 'O arquivo ultrapassa o limite de tamanho especifiado no HTML';
		$_UP['erros'][3] = 'O upload do arquivo foi feito parcialmente';
		$_UP['erros'][4] = 'Não foi feito o upload do arquivo';
		//fazendo o upload
		// Verifica se houve algum erro com o upload. Se sim, exibe a mensagem do erro
		if ($_FILES["file"]["error"] != 0) {
			die("Não foi possível fazer o upload, erro:" . $_UP['erros'][$_FILES["file"]['error']]);
			exit; // Para a execução do script
		}
		// Caso script chegue a esse ponto, não houve erro com o upload e o PHP pode continuar
		// Faz a verificação da extensão do arquivo
		$file_name = $_FILES["file"]['name'];
		$tmp = explode('.', $file_name);
		$file_extension = end($tmp);
		$extensao = strtolower($file_extension);
		if (array_search($extensao, $_UP['extensoes']) === false) {
			echo "Por favor, envie arquivos com as seguintes extensões: tsv";
			exit;
		}
		// Faz a verificação do tamanho do arquivo
		if ($_UP['tamanho'] < $_FILES["file"]['size']) {
			echo "O arquivo enviado é muito grande, envie arquivos de até XMb.";
			exit;
		}
		$nome_final = $_FILES["file"]['name'];
		// O arquivo passou em todas as verificações, hora de tentar movê-lo para a pasta
		// Primeiro verifica se deve trocar o nome do arquivo
		if ($_UP['renomeia'] == true) {
			// Cria um nome baseado no UNIX TIMESTAMP atual e com extensão .jpg
			$nome_final = md5(time()) . '.tsv';
		}
		$array_sql = $obj_Util->preparaImportacao($_FILES["file"]['tmp_name']);
		unset($_FILES["file"]);
		$importa = $obj_Geral->importaItens($array_sql);
		unset($array_sql);
		if ($importa) {
			header("Location: ../admin/adminsolicitacoes.php");
		} else {
			echo "Ocorreu um erro no servidor. Contate o administrador.";
			session_unset();
			session_destroy();
			exit;
		}
		break;

	// comentário

	case 'analisaSolicAlt':
		$id_solic = $_POST["id_solic"];
		$id_pedido = $_POST["id_pedido"];
		$acao = $_POST["acao"];

		$analisa = $obj_Geral->analisaSolicAlt($id_solic, $id_pedido, $acao);
		echo $analisa;
		break;

	// comentário

	case 'gerenciaPedido':
		$saldo_setor = $_POST["saldo_total"];
		//id do pedido
		$id_pedido = $_POST["id_pedido"];
		$total_pedido = $_POST["total_hidden"];
		$id_item = $_POST["id_item"];
		// id dos itens cancelados
		$item_cancelado = $_POST["item_cancelado"];

		$qtd_solicitada = $_POST["qtd_solicitada"];
		$qt_saldo = $_POST["qt_saldo"];
		$qt_utilizado = $_POST["qt_utilizado"];
		$vl_saldo = $_POST["vl_saldo"];
		$vl_utilizado = $_POST["vl_utilizado"];
		$valor_item = $_POST["valor_item"];

		$fase = $_POST["fase"];
		$prioridade = $_POST["prioridade"];
		if ($fase == 'rascunho') {
			$prioridade = $fase;
			$fase = 'Rascunho';
		}
		$total_pedido = $_POST["total_hidden"];

		$comentario = $_POST["comentario"];

		$analisado = $obj_Geral->pedidoAnalisado($id_pedido, $fase, $prioridade, $id_item, $item_cancelado, $qtd_solicitada, $qt_saldo, $qt_utilizado, $vl_saldo, $vl_utilizado, $valor_item, $saldo_setor, $total_pedido, $comentario);

		if ($analisado) {
			header("Location: ../admin/adminsolicitacoes.php");
		} else {
			echo "Ocorreu algum erro no servidor. Contate o administrador.";
		}
		break;

	// comentário

	case 'liberaSaldo':
		$id_setor = $_POST["id_setor"];
		$mes = $_POST["mes"];
		$ano = $_POST["ano"];
		$valor = $_POST["valor"];

		$saldo_anterior = $obj_Busca->getSaldoMesAnterior($id_setor);

		$libera = $obj_Geral->liberaSaldo($id_setor, $mes, $ano, $valor, $saldo_anterior);

		if ($libera) {
			echo true;
		} else {
			echo false;
		}

		break;

	// comentário

	case 'aprovaAdi':
		$id = $_POST["id"];
		$acao = $_POST["acao"];
		$aprova = $obj_Geral->analisaAdi($id, $acao);
		if (!$aprova) {
			echo "Ocorreu um erro no servidor. Contate o administrador";
		}
		break;

	// comentário

	case 'alterSenha':
		$id_user = $_POST["id_user"];
		$senha = $_POST["senha"];
		//encritpando a senha
		$senha = crypt($senha);
		//alterando no banco
		$update = $obj_Geral->updateSenha($id_user, $senha);
		if ($update) {
			echo true;
		} else {
			echo false;
		}
		break;

	// comentário

	case 'novanoticia':
		$data = $_POST["data"];
		$postagem = $_POST["postagem"];
		$pag = $_POST["pag"];

		//verificando se o usuário está publicando ou editando notícia
		if ($_POST["funcao"] == "novanoticia") {
			$id_noticia = $obj_Geral->setPost($data, $postagem, $pag);

			if ($id_noticia != 0) {
				header("Location: ../admin/");
			} else {
				echo "Ocorreu um erro no servidor. Contate o administrador.";
			}

		} else {
			$id = $_POST["id_noticia"];

			$editar = $obj_Geral->editPost($data, $id, $postagem, $pag);
			if ($editar) {
				header("Location: ../admin/");
			} else {
				echo "Ocorreu um erro no servidor. Contate o administrador.";
			}
		}
		break;

	// comentário

	case 'delArquivo':
		$file = $_POST["caminhoDel"];
		unlink($file);
		echo "O arquivo foi excluído com sucesso! A seguir, esta página será recarregada";
		break;

	// comentário

	case 'excluirNoticia':
		$id = $_POST["id"];
		echo $obj_Geral->excluirNoticia($id);
		break;

	default:
		break;
	}
} else if (isset($_POST["users"]) && isset($_SESSION["id_setor"]) && $_SESSION["id_setor"] != 0) {
	$form = $_POST["form"];

	switch ($form) {

	// redefindo uma nova senha
	case 'novaSenha':
		$id_user = $_SESSION["id"];
		$novaSenha = $_POST["novaSenha"];
		$senhaAtual = $_POST["senhaAtual"];

		$redefini = $obj_Geral->novaSenha($id_user, $novaSenha, $senhaAtual);
		echo $redefini;
		break;

	// solicitação para alterar um pedido

	case 'alt_pedido':
		$id_pedido = $_POST["id_pedido"];
		$justificativa = $_POST["justificativa"];
		$id_setor = $_SESSION["id_setor"];

		$solicita = $obj_Geral->solicAltPedido($id_pedido, $id_setor, $justificativa);
		echo $solicita;
		break;
	case 'adiantamento':
		$valor = $_POST["valor_adiantamento"];
		$justificativa = $_POST["justificativa"];
		$id_setor = $_SESSION["id_setor"];

		$envia = $obj_Geral->solicitaAdiantamento($id_setor, $valor, $justificativa);
		if ($envia) {
			header("Location: ../view/solicitacoes.php");
		} else {
			echo "Ocorreu algum erro no servidor. Contate o administrador";
		}
		break;
	case 'pedido':
		$id_setor = $_SESSION["id_setor"];
		$id_item = $_POST["id_item"];
		$qtd_solicitada = $_POST["qtd_solicitada"];
		$qtd_disponivel = $_POST["qtd_disponivel"];
		$qtd_contrato = $_POST["qtd_contrato"];
		$qtd_utilizado = $_POST["qtd_utilizado"];
		$vl_saldo = $_POST["vl_saldo"];
		$vl_contrato = $_POST["vl_contrato"];
		$vl_utilizado = $_POST["vl_utilizado"];
		$valor = $_POST["valor"];
		$refMes = $_POST["refMes"];
		$total_pedido = $_POST["total_hidden"];
		$saldo_total = $_POST["saldo_total"];
		$prioridade = $_POST["st"];

		$pedido = $_POST["pedido"];

		$insertPedido = $obj_Geral->insertPedido($id_setor, $id_item, $qtd_solicitada, $qtd_disponivel, $qtd_contrato, $qtd_utilizado, $vl_saldo, $vl_contrato, $vl_utilizado, $valor, $refMes, $total_pedido, $saldo_total, $prioridade, $pedido);

		if ($insertPedido) {
			header("Location: ../view/solicitacoes.php");
		} else {
			echo "Ocorreu um erro no servidor. Contate o administrador.";
		}
		break;
	default:
		break;
	}
} else {
	$form = $_POST["form"];
	switch ($form) {

	// resetando senha

	case 'resetSenha':
		$email = $_POST["email"];
		// gera uma senha aleatória (sem criptografia)
		$senha = $obj_Util->criaSenha();

		$reset = $obj_Geral->resetSenha($email, $senha);

		if ($reset == "Sucesso") {
			$from = $obj_Util->mail->Username;
			$nome_from = "Setor de Orçamento e Finanças do HUSM";
			$nome_from = utf8_decode($nome_from);
			$assunto = "Reset Senha";
			$altBody = "Sua senha resetada";
			$body = "Sua nova senha:<strong>{$senha}</strong>";
			$body .= utf8_decode("
			<br>
			<br> Não responda à esse e-mail.
			<br>
			<br>Caso tenha problemas, contate orcamentofinancashusm@gmail.com
			<br>
			<br>Atenciosamente,
			<br>equipe do SOF.
			");

			$obj_Util->preparaEmail($from, $nome_from, $email, "Usuário", $assunto, $altBody, $body);

			//send the message, check for errors
			if ($obj_Util->mail->send()) {
				echo true;
			} else {
				echo false;
			}
		} else {
			echo false;
		}
		break;

	// formulário para contato

	case 'faleconosco':
		$nome_from = utf8_decode($_POST["nome"]);
		$from = $_POST["email"];
		$para = "orcamentofinancashusm@gmail.com";
		$nome_para = "Setor de Orçamento e Finanças do HUSM";
		$assunto = utf8_decode($_POST["assunto"]);
		$mensagem = utf8_decode($_POST["mensagem"]);

		$altBody = 'SOF Fale Conosco';
		$body = $mensagem;

		$obj_Util->preparaEmail($from, $nome_from, $para, $nome_para, $assunto, $altBody, $body);

		/*=========== ANEXANDO OS ARQUIVOS ========  */
		if ($_POST["qtd-arquivos"] != 0) {
			// Tamanho máximo do arquivo (em Bytes)
			$_UP['tamanho'] = 1024 * 1024 * 1024 * 1024 * 1024 * 1024 * 1024 * 1024 * 1024 * 1024 * 1024; //
			// Array com as extensões permitidas
			$_UP['extensoes'] = array('pdf', 'docx', 'odt', 'jpg', 'jpeg', 'png');
			// Renomeia o arquivo? (Se true, o arquivo será salvo como .jpg e um nome único)
			$_UP['renomeia'] = false;
			// Array com os tipos de erros de upload do PHP
			$_UP['erros'][0] = 'Não houve erro';
			$_UP['erros'][1] = 'O arquivo no upload é maior do que o limite do PHP';
			$_UP['erros'][2] = 'O arquivo ultrapassa o limite de tamanho especifiado no HTML';
			$_UP['erros'][3] = 'O upload do arquivo foi feito parcialmente';
			$_UP['erros'][4] = 'Não foi feito o upload do arquivo';
			//fazendo o upload de todos os arquivos inseridos
			for ($i = 1; $i <= $_POST["qtd-arquivos"]; $i++) {
				// Verifica se houve algum erro com o upload. Se sim, exibe a mensagem do erro
				if ($_FILES["file-$i"]["error"] != 0) {
					die("Não foi possível fazer o upload, erro:" . $_UP['erros'][$_FILES["file-$i"]['error']]);
					exit; // Para a execução do script
				}
				// Caso script chegue a esse ponto, não houve erro com o upload e o PHP pode continuar
				// Faz a verificação da extensão do arquivo
				$extensao = strtolower(end(explode('.', $_FILES["file-$i"]['name'])));
				if (array_search($extensao, $_UP['extensoes']) === false) {
					echo "Por favor, envie arquivos com as seguintes extensões: pdf, docx ou odt";
					exit;
				}
				// Faz a verificação do tamanho do arquivo
				if ($_UP['tamanho'] < $_FILES["file-$i"]['size']) {
					echo "O arquivo enviado é muito grande, envie arquivos de até XMb.";
					exit;
				}
				$nome_final = $_FILES["file-$i"]['name'];
				// O arquivo passou em todas as verificações, hora de tentar movê-lo para a pasta
				// Primeiro verifica se deve trocar o nome do arquivo
				if ($_UP['renomeia'] == true) {
					// Cria um nome baseado no UNIX TIMESTAMP atual e com extensão .jpg
					$nome_final = md5(time()) . '.pdf';
				}

				$obj_Util->mail->addAttachment($_FILES["file-$i"]['tmp_name'], $_FILES["file-$i"]['name']);
			}
		}
		//send the message, check for errors
		if (!$obj_Util->mail->send()) {
			echo "Mailer Error: " . $obj_Util->mail->ErrorInfo;
		} else {
			$_SESSION["email_sucesso"] = 1;
			header("Location: ../view/faleconosco.php");
		}
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
