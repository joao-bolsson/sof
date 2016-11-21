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
	final public function preparaImportacao($tmp_name) {
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
					// chave = num_processo#cod_reduzido
					$chave = $data[6] . '#' . $data[17];
					$values .= "\n(NULL, {$data[0]}, {$data[1]}, '{$data[2]}', '{$data[3]}', '{$data[4]}', '{$data[5]}', '{$data[6]}', '{$data[7]}', '{$data[8]}', '{$data[9]}', '{$data[10]}', '{$data[11]}', '{$data[12]}', '{$data[13]}', '{$data[14]}', '{$data[15]}', '{$data[16]}', '{$data[17]}', '{$data[18]}', '{$data[19]}', '{$data[20]}', '{$data[21]}', {$data[22]}, '{$data[23]}', {$data[24]}, '{$data[25]}', {$data[26]}, '{$data[27]}', '{$data[28]}', '{$data[29]}', 0, '{$chave}'), ";
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
	 *	Função para retornar os fornecedores que estão no arquivo (SELECT DISTINCT)
	 *
	 *	@access public
	 *	@return array
	 */
	public function getFornecedores($tmp_name): array{
		$array_fornecedores = array();
		$i = 0;
		$colFornecedor = -1;
		$distinct = "";
		if (($handle = fopen($tmp_name, "r")) !== FALSE) {
			while (($data = fgetcsv($handle, 1000, "	")) !== FALSE) {
				if ($colFornecedor == -1) {
					$count = count($data);
					for ($j = 0; $j < $count; $j++) {
						if ($data[$j] == 'NOME_FORNECEDOR') {
							$colFornecedor = $j;
							$j = $count;
						}
					}
				} else {
					if ($distinct != $data[$colFornecedor]) {
						$array_fornecedores[$i] = $this->mysqli->real_escape_string($data[$colFornecedor]);
						$distinct = $data[$colFornecedor];
						$i++;
					}
				}
			}
			fclose($handle);
		}
		return $array_fornecedores;
	}
	/**
	 *	Função que envia um email
	 *
	 *	@access public
	 *	@return bool
	 */
	final public function preparaEmail(string $from, string $nome_from, string $para, string $nome_para, string $assunto, string $altBody, string $body) {
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
	final public function criaSenha(): string{
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
	/**
	 *	Função para formatar uma data d/m/Y em Y-m-d
	 *
	 *	@access public
	 *	@param $data Data a ser formatada
	 *	@return The date in format Y-m-d
	 */
	public function dateFormat(string $data): string{
		$array_data = explode('/', $data);

		$retorno = "";
		// Y-m-d
		$retorno .= $array_data[2] . '-' . $array_data[1] . '-' . $array_data[0];
		return $retorno;
	}
}
?>