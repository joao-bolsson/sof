<?php
ini_set('display_erros', true);
error_reporting(E_ALL);

session_start();
include_once '../class/Geral.class.php';
include_once '../class/Busca.class.php';
//instanciando a classe Geral
$obj_Geral = new Geral();
//instanciando a classe Busca
$obj_Busca = new Busca();
if ($_POST["form"] == "alterSenha") {
	$id_user = $_POST["id_user"];
	$senha = $_POST["senha"];
	//encritpando a senha
	$senha = crypt($senha);
	//alterando no banco
	$update = $obj_Geral->updateSenha($id_user, $senha);
	if ($update) {
		return true;
	} else {
		return false;
	}
}
if ($_POST["form"] == "novanoticia") {
	$data = $_POST["data"];
	$postagem = $_POST["postagem"];
	$pag = $_POST["pag"];
	$array_visu = array();

	$qtdSetores = $obj_Busca->getQtdSetores();

	for ($i = 0; $i < $qtdSetores; $i++) {
		$var = "visu" . ($i + 1);
		if (isset($_POST[$var])) {
			$array_visu[$i] = $var;
		} else {
			$array_visu[$i] = 0;
		}
	}

	//verificando se o usuário está publicando ou editando notícia
	if ($_POST["funcao"] == "novanoticia") {
		$id_noticia = $obj_Geral->setPost($data, $postagem, $pag, $array_visu);
		if ($id_noticia != 0) {
			header("Location: ../admin/");
		} else {
			echo "Ocorreu um erro no servidor. Contate o administrador.";
		}
	} else {
		//a data não será alterada
		$id = $_POST["id_noticia"];
		$tabela = $_POST["tabela"];
		$editar = $obj_Geral->editPost($data, $id, $tabela, $pag, $postagem, $array_visu);
		if ($editar) {
			header("Location: ../admin/");
		} else {
			echo "Ocorreu um erro no servidor. Contate o administrador.";
		}
	}

}
if (isset($_GET["caminhoDel"])) {
	$file = $_GET["caminhoDel"];
	unlink($file);
	header("Location: ../admin/");
}
if (isset($_GET["excluirNoticia"])) {
	$id = $_GET["id"];
	$tabela = $_GET["tabela"];
	if ($obj_Geral->excluirNoticia($id, $tabela)) {
		header("Location: ../admin/");
	} else {
		echo "Ocorreu um erro no servidor. Contate o administrador";
	}
}
if (isset($_POST["form"]) && $_POST["form"] == "faleconosco") {
	$nome = utf8_decode($_POST["nome"]);
	$email = $_POST["email"];
	$assunto = utf8_decode($_POST["assunto"]);
	$mensagem = utf8_decode($_POST["mensagem"]);
	/**
	 * This example shows settings to use when sending via Google's Gmail servers.
	 */
	//SMTP needs accurate times, and the PHP time zone MUST be set
	//This should be done in your php.ini, but this is how to do it if you don't have access to that
	date_default_timezone_set('Etc/UTC');
	require '../class/phpmailer/PHPMailerAutoload.php';
	//Create a new PHPMailer instance
	$mail = new PHPMailer;
	//Tell PHPMailer to use SMTP
	$mail->isSMTP();
	//Enable SMTP debugging
	// 0 = off (for production use)
	// 1 = client messages
	// 2 = client and server messages
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
	$mail->Username = "joaovictorbolsson@gmail.com";
	//Password to use for SMTP authentication
	$mail->Password = "vitinho1386";
	//Set who the message is to be sent from
	$mail->setFrom($email, $nome);
	//Set an alternative reply-to address
	//$mail->addReplyTo('replyto@example.com', 'First Last');
	//Set who the message is to be sent to
	$mail->addAddress('joaovictorbolsson@hotmail.com', 'João Víctor Bolsson Marques');
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
		$_UP['tamanho'] = 1024 * 1024 * 1024 * 1024 * 1024 * 2; //
		// Array com as extensões permitidas
		$_UP['extensoes'] = array('pdf', 'docx', 'odt');
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

			$mail->addAttachment($_FILES["file-$i"]['tmp_name']);
		}
	}
	//send the message, check for errors
	if (!$mail->send()) {
		echo "Mailer Error: " . $mail->ErrorInfo;
	} else {
		$_SESSION["email_sucesso"] = 1;
		header("Location: ../view/faleconosco.php");
	}
}
?>
