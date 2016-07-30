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
	}
	/**
	 *	Função que envia um email
	 *
	 *	@access public
	 *	@return bool
	 */
	public function preparaEmail($from, $nome_from, $para, $nome_para, $assunto, $altBody, $body) {
		$this->mail->setFrom($from, $nome_from);
		$this->mail->addAddress($para, $nome_para);
		$this->mail->addReplyTo($from, $nome_from);
		$this->mail->Subject = $assunto;
		$this->mail->AltBody = $altBody;
		$this->mail->Body = $body;
	}
	/**
	 *  Função que gera uma senha aleatória
	 *
	 *  @access public
	 *  @return string
	 */
	public function criaSenha() {
		// declarando retorno
		$retorno = "";

		// tamanho da nova senha
		$tam = 8;
		// caracteres que serão utilizados
		$min = 'abcdefghijklmnopqrstuvwxyz';
		$mai = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$num = '1234567890';
		$simb = '!@#$%*-';

		$caracteres = str_split($min . $mai . $num . $simb);
		for ($i = 0; $i < $tam; $i++) {
			$rand = mt_rand(0, count($caracteres) - 1);
			$retorno .= $caracteres[$rand];
		}
		return $retorno;
	}
}
?>