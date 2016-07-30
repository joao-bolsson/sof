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
//instanciando a classe Geral
$obj_Geral = new Geral();
//instanciando a classe Busca
$obj_Busca = new Busca();

if (isset($_POST["admin"]) && isset($_SESSION["id_setor"]) && $_SESSION["id_setor"] == 2) {
	// variável que controla o que deve ser feito quando geral.php for chamado
	$form = $_POST["form"];

	switch ($form) {

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

	// formulário para contato

	case 'faleconosco':
		$nome = utf8_decode($_POST["nome"]);
		$email = $_POST["email"];
		$assunto = utf8_decode($_POST["assunto"]);
		$mensagem = utf8_decode($_POST["mensagem"]);

		date_default_timezone_set('Etc/UTC');
		require '../class/phpmailer/PHPMailerAutoload.php';
		//Create a new PHPMailer instance
		$mail = new PHPMailer;
		//Tell PHPMailer to use SMTP
		$mail->isSMTP();
		$mail->SMTPDebug = 0;
		//Ask for HTML-friendly debug output
		//$mail->Debugoutput = 'html';
		//Set the hostname of the mail server
		$mail->Host = 'smtp.gmail.com';
		// use
		// $mail->Host = gethostbyname('smtp.gmail.com');
		// if your network does not support SMTP over IPv6
		//Set the SMTP port number - 587 for authenticated TLS, a.k.a. RFC4409 SMTP submission
		$mail->Port = 587;
		//Set the encryption system to use - ssl (deprecated) or tls
		$mail->SMTPSecure = 'tls';
		//Whether to use SMTP authentication
		$mail->SMTPAuth = true;
		//Username to use for SMTP authentication - use full email address for gmail
		$mail->Username = "sofhusm@gmail.com";
		//Password to use for SMTP authentication
		$mail->Password = "joaovictor201610816@[]";
		//Set who the message is to be sent from
		$mail->setFrom($email, $nome);
		//Set an alternative reply-to address
		$mail->addReplyTo($email, $nome);
		//Set who the message is to be sent to
		$mail->addAddress('orcamentofinancashusm@gmail.com', 'Setor de Orçamento e Finanças do HUSM');
		//Set the subject line
		$mail->Subject = $assunto;
		//Read an HTML message body from an external file, convert referenced images to embedded,
		//convert HTML into a basic plain-text alternative body
		$mail->msgHTML(file_get_contents('contents.html'), dirname(__FILE__));
		//Replace the plain text body with one created manually
		$mail->AltBody = 'SOF Fale Conosco';
		$mail->Body = $mensagem;

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

				$mail->addAttachment($_FILES["file-$i"]['tmp_name'], $_FILES["file-$i"]['name']);
			}
		}
		//send the message, check for errors
		if (!$mail->send()) {
			echo "Mailer Error: " . $mail->ErrorInfo;
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
