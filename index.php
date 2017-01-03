<?php

/**
 * 	Arquivo principal do sistema.
 *
 * 	@author João Bolsson
 */
ini_set('display_erros', true);
error_reporting(E_ALL);

session_start();

//// ========= testa o envio de e-mails para vários usuários ========= //
//include_once 'class/Util.class.php';
//include_once 'class/Geral.class.php';
////instanciando classe de busca para popular o select de estados
//$obj_Util = new Util();
//$obj_Geral = new Geral();
//
//for ($i = 1; $i <= 17; $i++) {
//
//    $senha = $obj_Util->criaSenha();
//
//    $email = "joaovictorbolsson@gmail.com";
//    $reset = $obj_Geral->resetSenha($email, $senha);
//
//    if ($reset == "Sucesso") {
//        $from = $obj_Util->mail->Username;
//        $nome_from = "Setor de Orçamento e Finanças do HUSM";
//        $nome_from = utf8_decode($nome_from);
//        $assunto = "Reset Senha";
//        $altBody = "Sua senha resetada";
//        $body = "Sua nova senha:<strong>{$senha}</strong>";
//        $body .= utf8_decode("
//			<br>
//			<br> Não responda à esse e-mail.
//			<br>
//			<br>Caso tenha problemas, contate orcamentofinancashusm@gmail.com
//			<br>
//			<br>Atenciosamente,
//			<br>equipe do SOF.
//			");
//
//        $obj_Util->preparaEmail($from, $nome_from, $email, "Usuário", $assunto, $altBody, $body);
//
//        //send the message, check for errors
//        if ($obj_Util->mail->send()) {
//            echo true;
//        } else {
//            echo false;
//        }
//    } else {
//        echo false;
//    }
//}
include_once 'class/Busca.class.php';
//instanciando classe de busca para popular o select de estados
$obj_Busca = new Busca();

$_SESSION['slide1'] = $obj_Busca->getSlide(1);
$_SESSION['slide2'] = $obj_Busca->getSlide(2);

if (isset($_SESSION['id_setor']) && $_SESSION['id_setor'] == 12) {
	header('Location: view/apoio.php');
} else if (isset($_SESSION["admin"])) {
//redireciona para a página do admin
	header('Location: admin/');
} else if (isset($_SESSION["id_setor"]) && $_SESSION["id_setor"] != 0) {
	header("Location: view/solicitacoes.php");
} else {
	header("Location: view/");
}
?>
