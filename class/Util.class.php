<?php
/**
 *	Classe com funções úteis
 *
 *	@author João Bolsson
 *	@since Version 1.0
 */

ini_set('display_erros', true);
error_reporting(E_ALL);
date_default_timezone_set('Etc/UTC');
require 'phpmailer/PHPMailerAutoload.php';

class Util {
	public $mail;
	function __construct() {

		$this->mail = new PHPMailer;
		$this->mail->isSMTP();
		$this->mail->SMTPDebug = 0;
		$this->mail->Host = 'smtp.gmail.com';
		$this->mail->Port = 587;
//Set the encryption system to use - ssl (deprecated) or tls
		$this->mail->SMTPSecure = 'tls';
//Whether to use SMTP authentication
		$this->mail->SMTPAuth = true;
		$this->mail->Username = "sofhusm@gmail.com";
//Password to use for SMTP authentication
		$this->mail->Password = "joaovictor201610816@[]";
		$mail->addAddress('orcamentofinancashusm@gmail.com', 'Setor de Orçamento e Finanças do HUSM');
	}
	/**
	 *	Função que envia um email
	 *
	 *	@access public
	 *	@return bool
	 */
	public function preparaEmail($email, $nome, $assunto, $altBody, $body) {
		//Set who the message is to be sent from
		$this->mail->setFrom($email, $nome);
		//Set an alternative reply-to address
		$this->mail->addReplyTo($email, $nome);
		//Set the subject line
		$this->mail->Subject = $assunto;
		//Replace the plain text body with one created manually
		$this->mail->AltBody = $altBody;
		$this->mail->Body = $body;
	}
}
?>