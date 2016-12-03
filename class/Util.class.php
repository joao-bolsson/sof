<?php

/**
 * 	Classe com funções úteis
 *
 * 	@author João Bolsson
 * 	@since 2016, 05 Sep
 */
ini_set('display_erros', true);
error_reporting(E_ALL);
require 'phpmailer/PHPMailerAutoload.php';
include_once 'Conexao.class.php';

class Util extends Conexao {

    public $mail;
    private $mysqli;
    private $id_item_processo = 0;
    private $id_item_contrato = 1;
    private $cod_despesa = 2;
    private $descr_despesa = 3;
    private $descr_tipo_doc = 4;
    private $num_contrato = 5;
    private $num_processo = 6;
    private $descr_mod_compra = 7;
    private $num_licitacao = 8;
    private $dt_inicio = 9;
    private $dt_fim = 10;
    private $dt_geracao = 11;
    private $cgc_fornecedor = 12;
    private $nome_fornecedor = 13;
    private $num_extrato = 14;
    private $cod_estruturado = 15;
    private $nome_unidade = 16;
    private $cod_reduzido = 17;
    private $complemento_item = 18;
    private $descricao = 19;
    private $id_extrato_contr = 20;
    private $vl_unitario = 21;
    private $qt_contrato = 22;
    private $vl_contrato = 23;
    private $qt_utilizado = 24;
    private $vl_utilizado = 25;
    private $qt_saldo = 26;
    private $vl_saldo = 27;
    private $id_unidade = 28;
    private $ano_orcamento = 29;
    private $seq_item_processo = 32;

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
     * 	Função utilizada para auxiliar a importação dos itens
     *
     * 	@access public
     * 	@return array
     */
    public function preparaImportacao($tmp_name): array {
        $insert = "INSERT IGNORE INTO itens VALUES";
        $values = "";
        $row = 1;
        $array_sql = array();
        $i = $pos = 0;
        if (($handle = fopen($tmp_name, "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, "	")) !== FALSE) {
                if ($data[$this->id_item_processo] != "ID_ITEM_PROCESSO") {
                    $row++;
                    for ($c = 0; $c < count($data); $c++) {
                        $data[$c] = str_replace("\"", "'", $data[$c]);
                        $data[$c] = $this->mysqli->real_escape_string($data[$c]);
                    }
                    // chave = num_processo#cod_reduzido
                    $chave = $data[$this->num_processo] . '#' . $data[$this->cod_reduzido];
                    $values .= "\n(NULL, " . $data[$this->id_item_processo] . ", " . $data[$this->id_item_contrato] . ", \"" . $data[$this->cod_despesa] . "\", \"" . $data[$this->descr_despesa] . "\", \"" . $data[$this->descr_tipo_doc] . "\", \"" . $data[$this->num_contrato] . "\", \"" . $data[$this->num_processo] . "\", \"" . $data[$this->descr_mod_compra] . "\", \"" . $data[$this->num_licitacao] . "\", \"" . $data[$this->dt_inicio] . "\", \"" . $data[$this->dt_fim] . "\", \"" . $data[$this->dt_geracao] . "\", \"" . $data[$this->cgc_fornecedor] . "\", \"" . $data[$this->nome_fornecedor] . "\", \"" . $data[$this->num_extrato] . "\", \"" . $data[$this->cod_estruturado] . "\", \"" . $data[$this->nome_unidade] . "\", \"" . $data[$this->cod_reduzido] . "\", \"" . $data[$this->complemento_item] . "\", \"" . $data[$this->descricao] . "\", \"" . $data[$this->id_extrato_contr] . "\", \"" . $data[$this->vl_unitario] . "\", " . $data[$this->qt_contrato] . ", \"" . $data[$this->vl_contrato] . "\", " . $data[$this->qt_utilizado] . ", \"" . $data[$this->vl_utilizado] . "\", " . $data[$this->qt_saldo] . ", \"" . $data[$this->vl_saldo] . "\", \"" . $data[$this->id_unidade] . "\", \"" . $data[$this->ano_orcamento] . "\", 0, \"" . $chave . "\", \"" . $data[$this->seq_item_processo] . "\"), ";
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
     * 	Função vai retornar um array com a chave do item e o seq_item_processo para atualizar.
     * 	função temporária.
     *
     * 	@access public
     * 	@return array
     */
    public function atualizaSeqItem($tmp_name): array {
        $array_sql = array();
        $i = 0;
        if (($handle = fopen($tmp_name, "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, "	")) !== FALSE) {
                if ($data[$this->id_item_processo] != "ID_ITEM_PROCESSO") {
                    // chave = num_processo#cod_reduzido
                    $chave = $data[$this->num_processo] . '#' . $data[$this->cod_reduzido];
                    $array_sql[$i] = "UPDATE itens SET seq_item_processo = '" . $data[$this->seq_item_processo] . "' WHERE chave = '" . $chave . "';";
                    $i++;
                }
            }
            fclose($handle);
        }
        return $array_sql;
    }

    /**
     * 	Função que envia um email
     *
     * 	@access public
     * 	@return bool
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
    final public function criaSenha(): string {
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
     * 	Função para formatar uma data d/m/Y em Y-m-d
     *
     * 	@access public
     * 	@param $data Data a ser formatada
     * 	@return The date in format Y-m-d
     */
    public function dateFormat(string $data): string {
        $array_data = explode('/', $data);

        $retorno = "";
        // Y-m-d
        $retorno .= $array_data[2] . '-' . $array_data[1] . '-' . $array_data[0];
        return $retorno;
    }

}

?>