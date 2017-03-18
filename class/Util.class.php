<?php

/**
 * 	Classe com funções úteis
 *
 * 	@author João Bolsson
 * 	@since 2016, 05 Sep
 */
ini_set('display_errors', true);
error_reporting(E_ALL);
require_once 'phpmailer/PHPMailerAutoload.php';

spl_autoload_register(function (string $class_name) {
    include_once $class_name . '.class.php';
});

final class Util {

    public $mail;
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
    private static $INSTANCE;

    public static function getInstance(): Util {
        if (empty(self::$INSTANCE)) {
            self::$INSTANCE = new Util();
        }
        return self::$INSTANCE;
    }

    private function __construct() {
        $this->mail = new PHPMailer();
        $this->mail->isSMTP();
        $this->mail->SMTPDebug = 0;
        $this->mail->Host = 'smtp.gmail.com';
        $this->mail->Port = '587';
        //Set the encryption system to use - ssl (deprecated) or tls
        $this->mail->SMTPSecure = 'tls';
        //Whether to use SMTP authentication
        $this->mail->SMTPAuth = true;
//        $this->mail->Charset = 'utf8_decode()';
        $this->mail->Username = "sofhusm@gmail.com";
        //Password to use for SMTP authentication
        $this->mail->Password = "joaovictor201610816@[]";
        $this->mail->IsHTML(true);
        $this->mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );
    }

    /**
     * @param int $id_usuario User's id
     * @param string $periodo Date range like 03/03/2017 - 05/04/2017
     * @return float Hours report of the user in $periodo (may be NULL)
     */
    public function getTotHoras(int $id_usuario, string $periodo): float {
        $array = explode(' - ', $periodo);
        $dataI = $this->dateFormat($array[0]);
        $dataF = $this->dateFormat($array[1]);

        $query = Query::getInstance()->exe("SELECT sum(horas) AS total FROM usuario_hora WHERE (entrada BETWEEN '" . $dataI . "' AND '" . $dataF . "') AND id_usuario = " . $id_usuario . ' LIMIT 1');
        $obj = $query->fetch_object();
        $obj->total = ($obj->total == NULL) ? 0 : $obj->total;
        return $obj->total;
    }

    /**
     * @param int $id_usuario User's id
     * @return bool Any 'saida' of user is NULL - true, else false.
     */
    public function isCurrentLoggedIn(int $id_usuario): bool {
        $query = Query::getInstance()->exe('SELECT saida FROM usuario_hora WHERE id_usuario = ' . $id_usuario . ' AND saida IS NULL;');

        return ($query->num_rows > 0);
    }

    /**
     * @param int $id_last Register's id.
     * @return float Hours between in and out of register $id_last
     */
    public function getTimeDiffHora(int $id_last): float {
        $query = Query::getInstance()->exe("SELECT TIMESTAMPDIFF(SECOND, entrada, saida) as seconds FROM usuario_hora WHERE id = " . $id_last);

        $obj = $query->fetch_object();
        $horas = 0;
        if ($obj->seconds == NULL) {
            exit('TIMEDIFF is NULL');
        } else {
            $horas = $obj->seconds / 3600;
        }

        return $horas;
    }

    /**
     * Gets the user's name from id.
     * @param int $id_user User's id.
     * @return string User's name.
     */
    public function getUserName(int $id_user): string {
        $query = Query::getInstance()->exe("SELECT usuario.nome FROM usuario WHERE usuario.id = " . $id_user);
        $obj = $query->fetch_object();

        return $obj->nome;
    }

    public function readFile(string $tmp_name): array {
        $array = [];
        $i = 0;
        if (($handle = fopen($tmp_name, "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, "	")) !== FALSE) {
                if ($data[$this->id_item_processo] != "ID_ITEM_PROCESSO") {
                    for ($c = 0; $c < count($data); $c++) {
                        $data[$c] = str_replace("\"", "'", $data[$c]);
                        $data[$c] = Query::getInstance()->real_escape_string($data[$c]);
                    }
                    $array[$i] = $data;
                    $i++;
                } else if ($data[$this->seq_item_processo] != "SEQ_ITEM_PROCESSO") {
                    echo "Arquivo inválido. Não contém a coluna SEQ_ITEM_PROCESSO no lugar correto.";
                    return $array;
                }
            }
            fclose($handle);
        }

        return $array;
    }

    /**
     * Function that prepare data to import to table itens.
     * @param array $data Array with data to import.
     * @return array
     */
    public function prepareImport(array $data): array {
        $array_sql = array();
        $insert = "INSERT IGNORE INTO itens VALUES";
        $values = "";
        $row = 1;
        $i = $pos = 0;
        $len = count($data);
        for ($a = 0; $a < $len; $a++) {
            $row++;
            // chave = num_processo#cod_reduzido#seq_item_processo
            $chave = $data[$a][$this->num_processo] . '#' . $data[$a][$this->cod_reduzido] . '#' . $data[$a][$this->seq_item_processo];
            $values .= "\n(NULL, " . $data[$a][$this->id_item_processo] . ", " . $data[$a][$this->id_item_contrato] . ", \"" . $data[$a][$this->cod_despesa] . "\", \"" . $data[$a][$this->descr_despesa] . "\", \"" . $data[$a][$this->descr_tipo_doc] . "\", \"" . $data[$a][$this->num_contrato] . "\", \"" . $data[$a][$this->num_processo] . "\", \"" . $data[$a][$this->descr_mod_compra] . "\", \"" . $data[$a][$this->num_licitacao] . "\", \"" . $data[$a][$this->dt_inicio] . "\", \"" . $data[$a][$this->dt_fim] . "\", \"" . $data[$a][$this->dt_geracao] . "\", \"" . $data[$a][$this->cgc_fornecedor] . "\", \"" . $data[$a][$this->nome_fornecedor] . "\", \"" . $data[$a][$this->num_extrato] . "\", \"" . $data[$a][$this->cod_estruturado] . "\", \"" . $data[$a][$this->nome_unidade] . "\", \"" . $data[$a][$this->cod_reduzido] . "\", \"" . $data[$a][$this->complemento_item] . "\", \"" . $data[$a][$this->descricao] . "\", \"" . $data[$a][$this->id_extrato_contr] . "\", \"" . $data[$a][$this->vl_unitario] . "\", " . $data[$a][$this->qt_contrato] . ", \"" . $data[$a][$this->vl_contrato] . "\", " . $data[$a][$this->qt_utilizado] . ", \"" . $data[$a][$this->vl_utilizado] . "\", " . $data[$a][$this->qt_saldo] . ", \"" . $data[$a][$this->vl_saldo] . "\", \"" . $data[$a][$this->id_unidade] . "\", \"" . $data[$a][$this->ano_orcamento] . "\", 0, \"" . $chave . "\", \"" . $data[$a][$this->seq_item_processo] . "\"), ";
            if ($row == 70) {
                $pos = strrpos($values, ", ");
                $values[$pos] = ";";
                $array_sql[$i] = $insert . $values;
                $values = "";
                $i++;
                $row = 1;
            }
        }
        if ($row < 70) {
            $pos = strrpos($values, ", ");
            $values[$pos] = ";";
            $array_sql[$i] = $insert . $values;
        }
        return $array_sql;
    }

    /**
     * Function to send an e-mail
     *
     * @param string $from
     * @param string $nome_from
     * @param string $para
     * @param string $nome_para
     * @param string $assunto
     * @param string $altBody
     * @param string $body
     * @return bool
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

    /**
     * Função que exibe os arquivos no modal do admin, usada diretamente no index
     *
     * @access public
     * @return string
     */
    public function getArquivos(): string {
        //declarando retorno
        $retorno = "";
        $pasta = '../uploads/';
        $diretorio = dir($pasta);

        while ($arquivo = $diretorio->read()) {
            $tipo = pathinfo($pasta . $arquivo);
            $label = "label";
            if ($tipo["extension"] == "jpg" || $tipo["extension"] == "png" || $tipo["extension"] == "jpeg") {
                $tipo = "Imagem";
                $label .= " label-brand";
            } else {
                $tipo = "Documento";
            }
            if ($arquivo != "." && $arquivo != ".." && $tipo != "Imagem") {
                //mostra apenas os documentos pdf e doc
                $retorno .= "
                    <tr>
                        <td><span class=\"" . $label . "\" style=\"font-size: 11pt !important; font-weight: bold;\">" . $tipo . "</span></td>
                        <td><a href=\"" . $pasta . $arquivo . "\">" . $arquivo . "</a></td>
                        <td><button class=\"btn btn-flat waves-attach waves-effect\" onclick=\"delArquivo('" . $pasta . $arquivo . "');\"><span class=\"icon\">delete</span><span style=\"font-weight:bold;\">Excluir</span></button></td>
                    </tr>";
            }
        }
        $diretorio->close();
        return $retorno;
    }

    /**
     * Função que exibe os arquivos no modal do admin, usada diretamente no index
     *
     * @access public
     * @return string
     */
    public function getArquivosLTE(): string {
        $pasta = '../uploads/';
        $diretorio = dir($pasta);

        $table = new Table('', '', [], false);
        while ($arquivo = $diretorio->read()) {
            $tipo = pathinfo($pasta . $arquivo);

            $label = ($tipo["extension"] == "jpg" || $tipo["extension"] == "png" || $tipo["extension"] == "jpeg") ? 'blue' : 'gray';
            $tipo_doc = ($tipo["extension"] == "jpg" || $tipo["extension"] == "png" || $tipo["extension"] == "jpeg") ? 'Imagem' : 'Documento';
            if ($arquivo != "." && $arquivo != "..") {
                $row = new Row();
                $row->addColumn(new Column(new Small('label bg-' . $label, $tipo_doc)));
                $row->addColumn(new Column("<a href=\"" . $pasta . $arquivo . "\" target=\"_blank\">" . $arquivo . "</a>"));

                $table->addRow($row);
            }
        }
        $diretorio->close();
        return $table;
    }

    /**
     * Função para adicionar novos inputs para adicionar arquivos
     *
     * @access public
     * @return string
     */
    public function setInputsArquivo(int $qtd): string {
        $qtd++;
        return "
            <div id=\"file-" . $qtd . "\" class=\"tile\">
                <div class=\"tile-side pull-left\">
                    <div class=\"avatar avatar-sm avatar-brand\">
                            <span class=\"icon\">backup</span>
                    </div>
                </div>
                <div class=\"tile-action tile-action-show\">
                    <ul class=\"nav nav-list margin-no pull-right\">
                        <li>
                            <a class=\"text-black-sec waves-attach\" href=\"javascript:dropTile('file-" . $qtd . "');\"><span class=\"icon\">delete</span></a>
                        </li>
                    </ul>
                </div>
                <div class=\"tile-inner\">
                    <input id=\"arq-" . $qtd . "\" class=\"btn btn-default btn-file\" type=\"file\" name=\"file-" . $qtd . "\" style=\"text-transform: none !important;\">
                </div>
            </div>";
    }

}
