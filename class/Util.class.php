<?php
/**
 *	Classe com funções úteis
 *
 *	@author João Bolsson
 *	@since 2016, 05 Sep
 */

ini_set('display_erros', true);
error_reporting(E_ALL);
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
	 *	Função utilizada para auxiliar a importação dos itens
	 *
	 *	@access public
	 *	@return array
	 */
	public function preparaImportacao($tmp_name) {
		$insert = "INSERT INTO itens VALUES";
		$values = "";
		$row = 1;
		$array_sql = array();
		$i = $pos = 0;
		if (($handle = fopen($tmp_name, "r")) !== FALSE) {
			while (($data = fgetcsv($handle, 1000, "	")) !== FALSE) {
				if ($data[0] != "ID_ITEM_PROCESSO") {
					$row++;
					for ($c = 0; $c < count($data); $c++) {
						$data[$c] = $this->mysqli->real_escape_string($data[$c]);
					}
					$values .= "\n(NULL, {$data[0]}, {$data[1]}, '{$data[2]}', '{$data[3]}', '{$data[4]}', '{$data[5]}', '{$data[6]}', '{$data[7]}', '{$data[8]}', '{$data[9]}', '{$data[10]}', '{$data[11]}', '{$data[12]}', '{$data[13]}', '{$data[14]}', '{$data[15]}', '{$data[16]}', '{$data[17]}', '{$data[18]}', '{$data[19]}', '{$data[20]}', '{$data[21]}', {$data[22]}, '{$data[23]}', {$data[24]}, '{$data[25]}', {$data[26]}, '{$data[27]}', '{$data[28]}', '{$data[29]}', 0), ";
					if ($row == 70) {
						$pos = strrpos($values, ", ");
						$values[$pos] = ";";
						$array_sql[$i] = $insert . $values;
						$values = "";
						$i++;
						$row = 1;
					}
				}
			}
			fclose($handle);
			if ($row < 70) {
				$pos = strrpos($values, ", ");
				$values[$pos] = ";";
				$array_sql[$i] = $insert . $values;
			}
		}
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