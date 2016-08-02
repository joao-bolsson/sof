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
include_once 'Conexao.class.php';

class Util extends Conexao {
	public $mail;
	private $mysqli;
	function __construct() {
		//chama o método contrutor da classe Conexao
		parent::__construct();
		$this->mysqli = parent::getConexao();

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
	// ---------------------------------------------------------------------------
	/**
	 *	Função para formatar datas no formato dd/mm/yyyy para YYYY-MM-DD
	 *
	 *	@access private
	 *	@return string
	 */
	private function formataData($data) {
		$ano = substr($data, 6, 4);
		$mes = substr($data, 3, 2);
		$dia = substr($data, 0, 2);
		$data = $ano . "-" . $mes . "-" . $dia;
		return $data;
	}
	/**
	 *	Função utilizada para auxiliar a importação dos itens
	 *
	 *	@access public
	 *	@return array
	 */
	public function preparaImportacao($tmp_name) {
		// Abre o Arquvio no Modo r (para leitura)
		$arquivo = fopen($tmp_name, 'r');
		$values = "";
		$insert = "INSERT INTO itens VALUES";
		$array_sql = array();
		$index = $cont = 0;
		$array_sql[$index] = $insert;
		while (!feof($arquivo)) {
			$linha = fgets($arquivo, 1024);
			$dados = explode('	', $linha);
			for ($i = 0; $i < count($dados); $i++) {
				$dados[$i] = $this->mysqli->real_escape_string($dados[$i]);
			}
			if ($dados[0] != "ID_ITEM_PROCESSO") {
				$values .= "(NULL, {$dados[0]}, {$dados[1]}, '{$dados[2]}', '{$dados[3]}', '{$dados[4]}', '{$dados[5]}', '{$dados[6]}', '{$dados[7]}', '{$dados[8]}', '{$dados[9]}', '{$dados[10]}', '{$dados[11]}', '{$dados[12]}', '{$dados[13]}', '{$dados[14]}', '{$dados[15]}', '{$dados[16]}', '{$dados[17]}', '{$dados[18]}', '{$dados[19]}', '{$dados[20]}', '{$dados[21]}', {$dados[22]}, '{$dados[23]}', {$dados[24]}, '{$dados[25]}', {$dados[26]}, '{$dados[27]}', '{$dados[28]}', '{$dados[29]}', 0), ";
				$cont++;
			}
			if ($cont == 200) {
				$array_sql[$index] = $insert . $values . ";";
				$cont = 0;
				$values = "";
				$pos = strrpos($array_sql[$index], ", ");
				$array_sql[$index][$pos] = "";
				$index++;
			}
		}
		$array_sql[$index] = $insert . $values . ";";
		fclose($arquivo);
		$max = count($array_sql) - 1;
		$pos = strrpos($array_sql[$max], ", ");
		if ($pos) {
			$array_sql[$max][$pos] = "";
		}
		echo $array_sql[0];
		return $array_sql;
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