<?php

/**
 *  Classe com as funções de busca utilizadas principalmente pelo arquivo php/busca.php
 *  qualquer função que RETORNE dados do banco, devem ser feitas nesta classe
 * 
 *  Usada para a nova aparência da parte administrativa do SOF.
 *
 *  @author João Bolsson (joaovictorbolsson@gmail.com).
 *  @since 2017, 15 Jan.
 */
ini_set('display_erros', true);
error_reporting(E_ALL);

spl_autoload_register(function (string $class_name) {
    include_once $class_name . '.class.php';
});

require_once '../defines.php';

final class BuscaLTE {

    private $mysqli;
    private static $INSTANCE;

    public static function getInstance(): BuscaLTE {
        if (empty(self::$INSTANCE)) {
            self::$INSTANCE = new BuscaLTE();
        }
        return self::$INSTANCE;
    }

    private function __construct() {
        // empty
    }

    private function openConnection() {
        if (is_null($this->mysqli)) {
            $this->mysqli = Conexao::getInstance()->getConexao();
        }
    }

    /**
     * Gets an array with information of the last register of user.
     * @param int $status If 1 - last register is a log in, else if a log out.
     * @param int $id_user Id of the user to format last register.
     * @return arrayArray with 'date' and 'time' of last register of the user.
     */
    private function formatTimeLast(int $status, int $id_user): array {
        $column = ($status == 1) ? 'entrada' : 'saida';
        $this->openConnection();

        $id_last = Busca::getInstance()->getLastRegister($id_user);
        $query = $this->mysqli->query("SELECT DATE_FORMAT(" . $column . ", '%d/%m/%Y') AS date, DATE_FORMAT(" . $column . ", '%H:%i:%s') AS time FROM usuario_hora WHERE id = " . $id_last) or exit('Erro ao buscar data e hora do último registro: ' . $this->mysqli->error);
        $this->mysqli = NULL;

        $array = $query->fetch_array();
        return $array;
    }

    public function refreshTableHora(): string {
        $this->openConnection();
        $query = $this->mysqli->query('SELECT DISTINCT usuario_hora.id_usuario, usuario.nome FROM usuario_hora, usuario WHERE usuario_hora.id_usuario = usuario.id ORDER BY usuario.nome ASC') or exit('Erro ao atualizar tabela: ' . $this->mysqli->error);

        $table = new Table('', '', [], false);

        while ($obj = $query->fetch_object()) {
            $info = !Busca::getInstance()->getInfoTime($obj->id_usuario);
            $status = ($info == 1) ? 'Entrada' : 'Saída';
            $color = ($info == 1) ? 'green' : 'red';

            $row = new Row();
            $row->addColumn(new Column($obj->nome));
            $row->addColumn(new Column(new Small('label bg-' . $color, $status)));
            $array = $this->formatTimeLast($info, $obj->id_usuario);
            $row->addColumn(new Column($array['time']));
            $row->addColumn(new Column($array['date']));

            $table->addRow($row);
        }

        $this->mysqli = NULL;
        return $table;
    }

    /**
     * 	Função que retorna as 'tabs' com as ṕáginas das notícias para editar.
     *
     * 	@return string
     */
    public function getTabsNoticiasLTE(): string {
        $this->openConnection();
        $query = $this->mysqli->query("SELECT id, tabela, nome FROM paginas_post") or exit("Erro ao buscar as abas de notícias para edição.");
        $this->mysqli = NULL;
        $row = new Row();
        while ($pag = $query->fetch_object()) {
            $radio = "<div class=\"form-group\" style=\"display: inline-block;\">
                    <label>
                        <input id=\"pag" . $pag->id . "\" type=\"radio\" name=\"pag\" class=\"minimal\" onclick=\"carregaPostsPag(" . $pag->id . ")\"/>
                        " . $pag->nome . "
                    </label>
                </div>";
            $row->addColumn(new Column($radio));
        }
        return $row;
    }

    /**
     * 	Função para retornar a tabela de notícias de uma página para edição
     *
     * 	@return string
     */
    public function getNoticiasEditar(int $tabela): string {
        $this->openConnection();
        $query = $this->mysqli->query("SELECT id, tabela, titulo, DATE_FORMAT(data, '%d/%m/%Y') AS data FROM postagens WHERE ativa = 1 AND tabela = " . $tabela . ' ORDER BY data ASC') or exit('Erro ao buscar as notícias para editar');
        $this->mysqli = NULL;
        $table = new Table('', '', [], false);
        while ($postagem = $query->fetch_object()) {
            $btn_group = "<div class=\"btn-group\">";
            $btn_group .= new Button('', BTN_DEFAULT . ' btn-sm', "editaNoticia(" . $postagem->id . ", " . $postagem->tabela . ")", "data-toggle=\"tooltip\"", 'Editar', 'pencil');

            $btn_group .= new Button('', BTN_DEFAULT . ' btn-sm', "excluirNoticia(" . $postagem->id . ")", "data-toggle=\"tooltip\"", 'Excluir', 'trash');
            $btn_group .= '</div>';

            $row = new Row();

            $row->addColumn(new Column($postagem->data));
            $row->addColumn(new Column($postagem->titulo));
            $row->addColumn(new Column($btn_group));

            $table->addRow($row);
        }
        return $table;
    }

    /**
     * Função para escrever as opções para "Postar em " do painel administrativo
     *
     * @return string
     */
    public function getPostarEm(): string {
        $retorno = "";
        $this->openConnection();
        $query = $this->mysqli->query("SELECT id, nome FROM paginas_post;") or exit("Erro ao buscar as páginas para postagem.");
        $this->mysqli = NULL;
        while ($pagina = $query->fetch_object()) {
            $retorno .= "<option id=\"op" . $pagina->id . "\" value=\"" . $pagina->id . "\">" . $pagina->nome . "</option>";
        }
        return $retorno;
    }

    /**
     * Retorna a quantidade de solicitações de adiantamento, alt pedidos e de pedidos em análise.
     * @return type Objeto com as quantidades de solicitações.
     */
    public function getCountSolic() {
        $this->openConnection();
        $query = $this->mysqli->query("SELECT count(saldos_adiantados.id) AS solic_adi, (SELECT count(solic_alt_pedido.id) FROM solic_alt_pedido WHERE solic_alt_pedido.status = 2) AS solic_alt, (SELECT count(pedido.id) FROM pedido WHERE pedido.status = 2) AS solic_ped FROM saldos_adiantados WHERE saldos_adiantados.status = 2;") or exit("Erro ao buscar número de solicitações.");
        $this->mysqli = NULL;
        $obj = $query->fetch_object();
        return $obj;
    }

    /**
     * @return string A row with contract options.
     */
    public function getOptionsContrato(): string {
        $this->openConnection();
        $query = $this->mysqli->query('SELECT id, nome FROM contrato_tipo') or exit('Erro ao buscar opções de contrato');
        $this->mysqli = NULL;
        $row = new Row();
        while ($obj = $query->fetch_object()) {
            $input = "<div class=\"form-group\">
                        <input type=\"radio\" name=\"tipoCont\" id=\"tipoCont" . $obj->id . "\" class=\"minimal\" value=\"" . $obj->id . "\">&nbsp;" . $obj->nome . "</div>";
            $row->addColumn(new Column($input));
        }

        return $row;
    }

    /**
     * @return string Lista de usuários cadastrados.
     */
    public function getUsers(bool $users = false, int $id_setor = 0): string {
        $this->openConnection();
        $where = ($id_setor != 0) ? 'WHERE id_setor = ' . $id_setor : '';

        if ($users) {
            $where = "WHERE login = 'uapublico'";
        }
        $query = $this->mysqli->query("SELECT id, nome, id_setor FROM usuario " . $where . " ORDER BY nome ASC;") or exit("Erro ao buscar usuários.");
        $this->mysqli = NULL;

        $retorno = "";
        while ($user = $query->fetch_object()) {
            $retorno .= "<option value=\"" . $user->id . "\">" . $user->nome . " (" . ARRAY_SETORES[$user->id_setor] . ")</option>";
        }
        return $retorno;
    }

    /**
     * 
     * @param int $id_setor Id do setor para retornar os grupos.
     * @return string Options dentro de um <select> com os grupos de um setor.
     */
    public function getOptionsGrupos(int $id_setor): string {
        $this->openConnection();
        $query = $this->mysqli->query("SELECT id, nome FROM setores_grupos WHERE id_setor = " . $id_setor) or exit("Erro ao buscar grupos.");
        $this->mysqli = NULL;
        $retorno = "";
        if ($query->num_rows >= 1) {
            $retorno = "";
            while ($obj = $query->fetch_object()) {
                $obj->nome = utf8_encode($obj->nome);
                $retorno .= "<option value=\"" . $obj->id . "\">" . $obj->nome . "</option>";
            }
        }
        return $retorno;
    }

    /**
     * Função para construir as linhas de uma tabela com as opções de licitação para fazer um pedido.
     * @param int $cont Número de radios que devem aparecer em cada linha.
     * @return string Radios buttons com opções de licitação para um pedido.
     */
    public function getOptionsLicitacao(int $cont): string {
        $this->openConnection();
        $query = $this->mysqli->query("SELECT id, nome FROM licitacao_tipo;") or exit("Erro ao buscar opções de licitação.");
        $this->mysqli = NULL;
        $retorno = "<tr>";
        $i = 0;
        while ($obj = $query->fetch_object()) {
            if ($i == $cont) {
                $i = 0;
                $retorno .= "</tr><tr>";
            }
            $retorno .= "
                <td>
                    <div class=\"form-group\">
                        <input type=\"radio\" name=\"tipoLic\" id=\"tipoLic" . $obj->id . "\" class=\"minimal\" value=\"" . $obj->id . "\" required > " . $obj->nome . "
                    </div>
                </td>";
            $i++;
        }

        return $retorno;
    }

    /**
     * Função para trazer informação do fornecedor de um pedido.
     * @param int $id_pedido Id do pedido.
     * @return string Fornecedor do pedido
     */
    public function getFornecedor(int $id_pedido): string {
        $this->openConnection();
        $query = $this->mysqli->query("SELECT itens.nome_fornecedor FROM itens, itens_pedido WHERE itens_pedido.id_item = itens.id AND itens_pedido.id_pedido = " . $id_pedido . " LIMIT 1;") or exit("Erro ao buscar fornecedor do pedido");
        $this->mysqli = NULL;
        if ($query->num_rows < 1) {
            return '';
        }

        $obj = $query->fetch_object();
        return $obj->nome_fornecedor;
    }

    /**
     * 	Função que retorna os pedidos em análise e o total deles
     *
     * 	@param $id_setor id do setor
     * 	@return string
     */
    public function getPedidosAnalise(int $id_setor): string {
        $retorno = "";
        $this->openConnection();
        $query = $this->mysqli->query("SELECT sum(valor) AS soma FROM pedido WHERE id_setor = " . $id_setor . " AND status = 2;") or exit("Erro ao buscar informações dos pedidos em análise.");
        $this->mysqli = NULL;
        if ($query->num_rows > 0) {
            $obj = $query->fetch_object();
            $soma = number_format($obj->soma, 3, ',', '.');
            if ($soma > 0) {
                $retorno = "
                <tr>
                    <td colspan=\"2\">Você tem " . $query->num_rows . " pedido(s) em análise no total de R$ " . $soma . "</td>
                </tr>";
            }
        }
        return $retorno;
    }

    /**
     * 	Função que retornar o empenho de um pedido
     *
     * 	@param $id_pedido Id do pedido.
     * 	@return string
     */
    public function verEmpenho(int $id_pedido): string {
        $retorno = '';
        $this->openConnection();
        $query = $this->mysqli->query("SELECT empenho FROM pedido_empenho WHERE id_pedido = " . $id_pedido) or exit("Erro so ver empenho.");
        $this->mysqli = NULL;
        if ($query->num_rows < 1) {
            $retorno = 'EMPENHO SIAFI PENDENTE';
        } else {
            $obj = $query->fetch_object();
            $retorno = $obj->empenho;
        }
        return $retorno;
    }

    /**
     * 	Função que retorna as options com os setores cadastrados no sistema
     *
     * 	@return string
     */
    public function getOptionsSetores(): string {
        $retorno = "";
        $count = count(ARRAY_SETORES);

        for ($i = 2; $i < $count; $i++) {
            $retorno .= "<option value=\"" . $i . "\">" . ARRAY_SETORES[$i] . "</option>";
        }
        return $retorno;
    }

    /**
     * 	Função que retornar as options com as prioridades existentes no sistemas para os pedidos
     *
     * 	@return string
     */
    public function getOptionsPrioridades(): string {
        $retorno = "";
        $count = count(ARRAY_PRIORIDADE);

        for ($i = 1; $i < $count - 1; $i++) {
            $retorno .= "<option value=\"" . $i . "\">" . ARRAY_PRIORIDADE[$i] . "</option>";
        }
        return $retorno;
    }

    public function getOptionsCategoria(): string {
        $retorno = "";
        $count = count(ARRAY_CATEGORIA);

        for ($i = 1; $i < $count; $i++) {
            $retorno .= "<option value=\"" . $i . "\">" . ARRAY_CATEGORIA[$i] . "</option>";
        }
        return $retorno;
    }

    /**
     * 	Função que retorna as options com os status de pedidos
     *
     * 	@return string
     */
    public function getOptionsStatus(): string {
        $retorno = "";
        $count = count(ARRAY_STATUS);
        for ($i = 2; $i < $count; $i++) {
            $retorno .= "<option value=\"" . $i . "\">" . ARRAY_STATUS[$i] . "</option>";
        }
        return $retorno;
    }

    /**
     * 	Função que retorna as options para tipo de processos para o cadastro de novos processos.
     *
     * 	@return string
     */
    public function getTiposProcessos(): string {
        $retorno = "";
        $this->openConnection();
        $query = $this->mysqli->query("SELECT id, nome FROM processos_tipo;") or exit("Erro ao buscar os tipos de processo.");
        $this->mysqli = NULL;
        while ($tipo = $query->fetch_object()) {
            $retorno .= "<option value=\"" . $tipo->id . "\">" . $tipo->nome . "</option>";
        }
        return $retorno;
    }

    /**
     * 	Função que retornar se um pedido é ou não rascunho.
     *
     * 	@param $id_pedido id do pedido.
     * 	@return Se o pedido é um rascunho - true, senão false.
     */
    public function getRequestDraft(int $id_pedido): bool {
        $this->openConnection();
        $query = $this->mysqli->query("SELECT prioridade FROM pedido WHERE id = " . $id_pedido) or exit("Erro ao buscar prioridade do pedido.");
        $this->mysqli = NULL;
        $obj = $query->fetch_object();
        return ARRAY_PRIORIDADE[$obj->prioridade] == 'Rascunho';
    }

    /**
     * 	Função que constroi os radioBtn da análise dos pedidos.
     *
     * 	@param $cont Número de radioBtn por linha.
     * 	@return string
     */
    public function getStatus(int $cont): string {
        $retorno = "<tr>";
        $i = 0;
        $count = count(ARRAY_STATUS);

        for ($j = 2; $j < $count; $j++) {
            if ($i == $cont) {
                $i = 0;
                $retorno .= "</tr><tr>";
            }
            $retorno .= "
                <td>
                    <div class=\"form-group\">
                        <label>
                            <input id=\"st" . $j . "\" type=\"radio\" name=\"fase\" class=\"minimal\" value=\"" . $j . "\"/>" . ARRAY_STATUS[$j] . "</label>
                    </div>
                </td>";
            $i++;
        }
        $retorno .= "</tr>";
        return $retorno;
    }

    /**
     * 	Função que retornar os radioBtn das prioridades dos pedidos.
     *
     * 	@return Opções de prioridades para os pedidos.
     */
    public function getPrioridades(): string {
        $count = count(ARRAY_PRIORIDADE);

        $row = new Row();
        for ($i = 1; $i < $count; $i++) {
            $form_group = "<div class=\"form-group\">
                        <input type=\"radio\" name=\"st\" id=\"st" . ARRAY_PRIORIDADE[$i] . "\" class=\"minimal\" value=\"" . $i . "\"> " . ARRAY_PRIORIDADE[$i] . "
                    </div>";

            $row->addColumn(new Column($form_group));
        }
        return $row;
    }

    /**
     *   Função utilizada para retornar a tabela dos processos da recepção.
     *
     *   @return string
     */
    public function getTabelaRecepcao(): string {
        $this->openConnection();
        $query = $this->mysqli->query("SELECT processos.id, processos.num_processo, processos_tipo.nome as tipo, processos.estante, processos.prateleira, processos.entrada, processos.saida, processos.responsavel, processos.retorno, processos.obs FROM processos, processos_tipo WHERE processos.tipo = processos_tipo.id ORDER BY id ASC;") or exit("Erro ao formar a tabela da recepção.");
        $this->mysqli = NULL;
        $table = new Table('', '', [], false);
        while ($processo = $query->fetch_object()) {
            $processo->obs = str_replace("\"", "\'", $processo->obs);

            $row = new Row();
            $div = new Div('btn-group');
            $div->addComponent(new Button('', BTN_DEFAULT, "addProcesso('', " . $processo->id . ")", "data-toggle=\"tooltip\"", 'Editar', 'pencil'));
            $row->addColumn(new Column($div));
            $row->addColumn(new Column($processo->num_processo));
            $row->addColumn(new Column($processo->tipo));
            $row->addColumn(new Column($processo->estante));
            $row->addColumn(new Column($processo->prateleira));
            $row->addColumn(new Column($processo->entrada));
            $row->addColumn(new Column($processo->saida));
            $row->addColumn(new Column($processo->responsavel));
            $row->addColumn(new Column($processo->retorno));
            $row->addColumn(new Column(new Button('', BTN_DEFAULT, "viewCompl('" . $processo->obs . "')", "data-toggle=\"tooltip\"", 'Ver Observação', 'eye')));
            $table->addRow($row);
        }
        return $table;
    }

    /**
     * Retorna as permissões para cadastro de usuários.
     */
    public function getCheckPermissoes(): string {
        $this->openConnection();
        $query = $this->mysqli->query("SELECT column_name AS nome FROM information_schema.columns WHERE table_name = 'usuario_permissoes' AND column_name <> 'id_usuario';") or exit("Erro ao buscar permissões.");
        $this->mysqli = NULL;
        $retorno = "";
        $i = 1;
        while ($obj = $query->fetch_object()) {
            $nome = ucfirst($obj->nome);
            $retorno .= "
                <div class=\"form-group\" style=\"display: inline-block;\">
                    <label>
                        <input id=\"perm" . $i . "\" type=\"checkbox\" name=\"" . $obj->nome . "\" class=\"minimal\"/>
                        " . $nome . "
                    </label>
                </div>";
            $i++;
        }
        return $retorno;
    }

    /**
     * 	Função que retorna as permissões do usuário.
     *
     * 	@return JSON com as permissões do usuário no sistema.
     */
    public function getPermissoes(int $id_user) {
        $this->openConnection();
        $query = $this->mysqli->query("SELECT noticias, saldos, pedidos, recepcao FROM usuario_permissoes WHERE id_usuario = " . $id_user) or exit("Erro ao buscar permissões do usuário.");
        $this->mysqli = NULL;
        $obj_permissoes = $query->fetch_object();
        return $obj_permissoes;
    }

    private static final function buildButtonsSolicAltPedAdmin(int $id, int $id_pedido): string {
        $group = "<div class=\"btn-group\">";
        $btn_aprovar = new Button('', BTN_DEFAULT, "analisaSolicAlt(" . $id . ", " . $id_pedido . ", 1)", "data-toggle=\"tooltip\"", 'Aprovar', 'check');
        $btn_reprovar = new Button('', BTN_DEFAULT, "analisaSolicAlt(" . $id . ", " . $id_pedido . ", 0)", "data-toggle=\"tooltip\"", 'Reprovar', 'trash');

        $group .= $btn_aprovar . $btn_reprovar . '</div>';
        return $group;
    }

    /**
     * 	Função que retorna a tabela com as solicitações de alteração de pedidos	para o SOF analisar
     *
     * 	@return string
     */
    public function getAdminSolicAltPedidos(int $st): string {
        $this->openConnection();
        $query = $this->mysqli->query("SELECT solic_alt_pedido.id, solic_alt_pedido.id_pedido, setores.nome, DATE_FORMAT(solic_alt_pedido.data_solicitacao, '%d/%m/%Y') AS data_solicitacao, DATE_FORMAT(solic_alt_pedido.data_analise, '%d/%m/%Y') AS data_analise, solic_alt_pedido.justificativa, solic_alt_pedido.status FROM solic_alt_pedido, setores WHERE solic_alt_pedido.id_setor = setores.id AND solic_alt_pedido.status = " . $st . " ORDER BY solic_alt_pedido.id DESC LIMIT 200") or exit("Erro ao buscar as solicitações de alteração de pedidos enviados ao SOF.");
        $this->mysqli = NULL;

        $array_status = ['Reprovado', 'Aprovado', 'Aberto'];
        $array_lb = ['red', 'green', 'orange'];

        $status = $array_status[$st];
        $label = 'bg-' . $array_lb[$st];

        $table = new Table('', '', array(), false);
        while ($solic = $query->fetch_object()) {
            $btn_group = ($st == 2) ? self::buildButtonsSolicAltPedAdmin($solic->id, $solic->id_pedido) : '';
            $solic->justificativa = str_replace("\"", "\'", $solic->justificativa);

            $row = new Row();
            $row->addColumn(new Column($btn_group));
            $row->addColumn(new Column($solic->id_pedido));
            $row->addColumn(new Column($solic->nome));
            $row->addColumn(new Column($solic->data_solicitacao));
            $row->addColumn(new Column(($st == 2) ? '--------------' : $solic->data_analise));
            $row->addColumn(new Column(new Button('', 'btn btn-sm btn-primary', "viewCompl('" . $solic->justificativa . "')", "data-toggle=\"tooltip\"", 'Ver Justificativa', 'eye')));
            $row->addColumn(new Column(new Small('label pull-right ' . $label, $status)));
            $table->addRow($row);
        }
        return $table;
    }

    /**
     * 	Função que retorna as solicitações de adiantamentos de saldos enviadas ao SOF para análise.
     *
     * 	@param $st Status
     * 	@return string
     */
    public function getSolicAdiantamentos(int $st): string {
        $this->openConnection();
        $query = $this->mysqli->query("SELECT saldos_adiantados.id, setores.nome, DATE_FORMAT(saldos_adiantados.data_solicitacao, '%d/%m/%Y') AS data_solicitacao, DATE_FORMAT(saldos_adiantados.data_analise, '%d/%m/%Y') AS data_analise, saldos_adiantados.valor_adiantado, saldos_adiantados.justificativa FROM saldos_adiantados, setores WHERE saldos_adiantados.id_setor = setores.id AND saldos_adiantados.status = " . $st . " ORDER BY saldos_adiantados.data_solicitacao DESC LIMIT 200") or exit("Erro ao buscar solicitações de adiantamento.");
        $this->mysqli = NULL;
        $array_status = ['Reprovado', 'Aprovado', 'Aberto'];
        $array_lb = ['red', 'green', 'orange'];

        $status = $array_status[$st];
        $label = $array_lb[$st];

        $table = new Table('', '', [], false);
        if ($query) {
            while ($solic = $query->fetch_object()) {
                $btn_group = ($st == 2) ? self::buildButtonsSolicAdi($solic->id) : '';
                $solic->justificativa = str_replace("\"", "\'", $solic->justificativa);
                $solic->valor_adiantado = number_format($solic->valor_adiantado, 3, ',', '.');

                $row = new Row();
                $row->addColumn(new Column($btn_group));
                $row->addColumn(new Column($solic->nome));
                $row->addColumn(new Column($solic->data_solicitacao));
                $row->addColumn(new Column(($st == 2) ? '---------------' : $solic->data_analise));
                $row->addColumn(new Column($solic->valor_adiantado));
                $row->addColumn(new Column(new Button('', 'btn btn-sm btn-primary', "viewCompl('" . $solic->justificativa . "')", "data-toggle=\"tooltip\"", 'Ver Justificativa', 'eye')));
                $row->addColumn(new Column(new Small('label pull-right bg-' . $label, $status)));

                $table->addRow($row);
            }
        }
        return $table;
    }

    private static final function buildButtonsSolicAdi(int $id): string {
        $div = new Div('btn-group');

        $div->addComponent(new Button('', BTN_DEFAULT, "analisaAdi(" . $id . ", 1)", "data-toggle=\"tooltip\"", 'Aprovar', 'check'));
        $div->addComponent(new Button('', BTN_DEFAULT, "analisaAdi(" . $id . ", 0)", "data-toggle=\"tooltip\"", 'Reprovar', 'trash'));

        return $div;
    }

    /**
     * @param int $id_pedido Id do pedido
     * @return string Data do cadastro do empenho
     */
    private function verDataEmpenho(int $id_pedido): string {
        $this->openConnection();
        $query = $this->mysqli->query("SELECT DATE_FORMAT(data, '%d/%m/%Y') AS data FROM pedido_empenho WHERE id_pedido = " . $id_pedido . " LIMIT 1;");
        $this->mysqli = NULL;

        if ($query->num_rows < 1) {
            return '';
        }
        $obj = $query->fetch_object();
        return $obj->data;
    }

    /**
     * Constroi os botoes para a análise.
     * @param int $id Id do pedido.
     * @param int $status Status atual do pedido.
     * @param int $id_setor Id do setor que fez o pedido.
     */
    private function buildButtons(int $id, int $status, int $id_setor): string {
        $component = new Div('btn-group');

        if ($status != 3 && $status != 4) {
            if ($_SESSION['id_setor'] == 12) {
                $component->addComponent(new Button('', BTN_DEFAULT, "enviaForn(" . $id . ")", "data-toggle=\"tooltip\"", 'Enviar ao Fornecedor', 'send'));
            } else if ($status == 2) {
                $component->addComponent(new Button('', BTN_DEFAULT, "analisarPedido(" . $id . ", " . $id_setor . ")", "data-toggle=\"tooltip\"", 'Analisar', 'pencil'));
            } else if ($status == 5) {
                $component->addComponent(new Button('', BTN_DEFAULT, "cadFontes(" . $id . ")", "data-toggle=\"tooltip\"", 'Cadastrar Fontes', 'comment'));
            } else if ($status == 6) {
                $component->addComponent(new Button('', BTN_DEFAULT, "cadEmpenho(" . $id . ")", "data-toggle=\"tooltip\"", 'Cadastrar Empenho', 'credit-card'));
            } else if ($status == 7) {
                $component->addComponent(new Button('', BTN_DEFAULT, "enviaOrdenador(" . $id . ")", "data-toggle=\"tooltip\"", 'Enviar ao Ordenador', 'send'));
            } else {
                $component->addComponent(new Button('', BTN_DEFAULT, "getStatus(" . $id . ", " . $id_setor . ")", "data-toggle=\"tooltip\"", 'Alterar Status', 'wrench'));
            }
        }

        if ($this->verEmpenho($id) != 'EMPENHO SIAFI PENDENTE' && $_SESSION['id_setor'] != 12 && $status > 6) {
            $component->addComponent(new Button('', BTN_DEFAULT, "cadEmpenho(" . $id . ", '" . $this->verEmpenho($id) . "', '" . $this->verDataEmpenho($id) . "')", "data-toggle=\"tooltip\"", 'Cadastrar Empenho', 'credit-card'));
        }

        $component->addComponent(new Button('', BTN_DEFAULT, "imprimir(" . $id . ")", "data-toggle=\"tooltip\"", 'Imprimir', 'print'));
        return $component;
    }

    /**
     * Função para retornar as solicitações para o SOF.
     *
     * @return string
     *
     */
    public function getSolicitacoesAdmin(string $where = '', array $pedidos = []): string {
        $this->openConnection();
        $limit = 'LIMIT ' . LIMIT_MAX;
        $query = $this->mysqli->query("SELECT id, id_setor, DATE_FORMAT(data_pedido, '%d/%m/%Y') AS data_pedido, prioridade, status, valor, aprov_gerencia FROM pedido WHERE status <> 3 AND alteracao = 0 " . $where . " ORDER BY id DESC " . $limit) or exit("Erro ao buscar os pedidos que foram mandados ao SOF.");
        $this->mysqli = NULL;

        $table = new Table('', '', array(), false);
        while ($pedido = $query->fetch_object()) {
            // determina se o pedido vai ser adicionado na tabela
            $flag = false;

            if (!in_array($pedido->id, $pedidos)) {
                if ($_SESSION['id_setor'] == 12) {
                    if ($pedido->status == 8) {
                        $flag = true;
                    }
                } else {
                    $flag = true;
                }
            }

            if ($flag) {
                $btnVerEmpenho = $this->verEmpenho($pedido->id);
                if ($btnVerEmpenho == 'EMPENHO SIAFI PENDENTE') {
                    $btnVerEmpenho = '';
                }
                $pedido->valor = number_format($pedido->valor, 3, ',', '.');
                $aprovGerencia = ($pedido->aprov_gerencia) ? new Small('label pull-right bg-gray', 'A', "data-toggle=\"tooltip\"", 'Aprovado pela Gerência') : '';

                $check_all = "
                <div class=\"form-group\">
                    <input type=\"checkbox\" name=\"checkPedRel\" id=\"checkPedRel" . $pedido->id . "\" value=\"" . $pedido->id . "\">
                </div>
                " . $aprovGerencia . "";

                $buttons = self::buildButtons($pedido->id, $pedido->status, $pedido->id_setor);

                $row = new Row('rowPedido' . $pedido->id);

                $row->addColumn(new Column($check_all));
                $row->addColumn(new Column($buttons));
                $row->addColumn(new Column($pedido->id));
                $row->addColumn(new Column(ARRAY_SETORES[$pedido->id_setor]));
                $row->addColumn(new Column($pedido->data_pedido));
                $row->addColumn(new Column(ARRAY_PRIORIDADE[$pedido->prioridade]));
                $row->addColumn(new Column(ARRAY_STATUS[$pedido->status]));
                $row->addColumn(new Column("R$ " . $pedido->valor));
                $row->addColumn(new Column($btnVerEmpenho));
                $row->addColumn(new Column($this->getFornecedor($pedido->id)));

                $table->addRow($row);
            }
        }
        return $table;
    }

    private static final function buildButtonsRequestAnalysis(int $id, string $compl): string {
        $btn_group = "
            <div class=\"btn-group\">
                <button type=\"button\" class=\"btn btn-default\" onclick=\"cancelaItem(" . $id . ");\" title=\"Item Cancelado\"><i id=\"icon-cancela-item" . $id . "\" class=\"text-red fa fa-close\"></i>
                </button>
                <button type=\"button\" class=\"btn btn-default\" onclick=\"editaItem(" . $id . ");\" title=\"Editar\"><i class=\"fa fa-pencil\"></i>
                </button>
                <button type=\"button\" class=\"btn btn-default\" onclick=\"viewCompl('" . $compl . "');\"  title=\"Ver Complemento do Item\"><i class=\"fa fa-file-text\"></i>
                </button>
            </div>";

        return $btn_group;
    }

    /**
     * Função para trazer as informações de um pedido a ser analisado.
     *
     * @return string
     */
    public function getItensPedidoAnalise(int $id_pedido): string {
        $this->openConnection();
        $query = $this->mysqli->query('SELECT itens.qt_contrato, itens.id AS id_itens, itens_pedido.qtd AS qtd_solicitada, itens_pedido.valor, itens.nome_fornecedor, itens.num_licitacao, itens.dt_inicio, itens.dt_fim, itens.cod_reduzido, itens.complemento_item, itens.vl_unitario, itens.qt_saldo, itens.cod_despesa, itens.descr_despesa, itens.num_contrato, itens.num_processo, itens.descr_mod_compra, itens.num_licitacao, itens.cgc_fornecedor, itens.num_extrato, itens.descricao, itens.qt_contrato, itens.vl_contrato, itens.qt_utilizado, itens.vl_utilizado, itens.qt_saldo, itens.vl_saldo, itens.seq_item_processo FROM itens_pedido, itens WHERE itens_pedido.id_pedido = ' . $id_pedido . ' AND itens_pedido.id_item = itens.id ORDER BY itens.seq_item_processo ASC') or exit('Erro ao buscar os itens do pedido para análise');
        $this->mysqli = NULL;

        $table = new Table('', '', [], false);
        while ($item = $query->fetch_object()) {
            $item->complemento_item = str_replace("\"", "\'", $item->complemento_item);

            $btn_group = self::buildButtonsRequestAnalysis($item->id_itens, $item->complemento_item);

            $inputs = "
                <input type=\"hidden\" name=\"id_item[]\" value=\"" . $item->id_itens . "\">
                <input id=\"item_cancelado" . $item->id_itens . "\" type=\"hidden\" name=\"item_cancelado[]\" value=\"0\">
                <input type=\"hidden\" name=\"qtd_solicitada[]\" value=\"" . $item->qtd_solicitada . "\">
                <input type=\"hidden\" name=\"qt_saldo[]\" value=\"" . $item->qt_saldo . "\">
                <input type=\"hidden\" name=\"qt_utilizado[]\" value=\"" . $item->qt_utilizado . "\">
                <input type=\"hidden\" name=\"vl_saldo[]\" value=\"" . $item->vl_saldo . "\">
                <input type=\"hidden\" name=\"vl_utilizado[]\" value=\"" . $item->vl_utilizado . "\">
                <input type=\"hidden\" name=\"valor_item[]\" value=\"" . $item->valor . "\">";

            $row = new Row('row_item' . $item->id_itens);

            $row->addColumn(new Column($btn_group));
            $row->addColumn(new Column($item->cod_despesa));
            $row->addColumn(new Column($item->descr_despesa));
            $row->addColumn(new Column($item->num_extrato));
            $row->addColumn(new Column($item->num_contrato));
            $row->addColumn(new Column($item->num_processo));
            $row->addColumn(new Column($item->descr_mod_compra));
            $row->addColumn(new Column($item->num_licitacao));
            $row->addColumn(new Column($item->dt_inicio));
            $row->addColumn(new Column(($item->dt_fim == '') ? '----------' : $item->dt_fim));
            $row->addColumn(new Column($item->cgc_fornecedor));
            $row->addColumn(new Column($item->nome_fornecedor));
            $row->addColumn(new Column($item->cod_reduzido));
            $row->addColumn(new Column($item->seq_item_processo));
            $row->addColumn(new Column($item->descricao));
            $row->addColumn(new Column('R$ ' . $item->vl_unitario));
            $row->addColumn(new Column($item->qt_contrato));
            $row->addColumn(new Column($item->vl_contrato));
            $row->addColumn(new Column($item->qt_utilizado));
            $row->addColumn(new Column($item->vl_utilizado));
            $row->addColumn(new Column($item->qt_saldo));
            $row->addColumn(new Column($item->vl_saldo));
            $row->addColumn(new Column($item->qtd_solicitada));
            $row->addColumn(new Column('R$ ' . $item->valor));
            $row->addColumn(new Column($inputs));

            $table->addRow($row);
        }
        return $table;
    }

    /**
     * 	Função que retorna uma tabela com as solicitações de alteração de pedidos
     *
     * 	@return string
     */
    public function getSolicAltPedidos(): string {
        $this->openConnection();
        $id_setor = $_SESSION['id_setor'];
        $query = $this->mysqli->query("SELECT solic_alt_pedido.id_pedido, DATE_FORMAT(solic_alt_pedido.data_solicitacao, '%d/%m/%Y') AS data_solicitacao, DATE_FORMAT(solic_alt_pedido.data_analise, '%d/%m/%Y') AS data_analise, solic_alt_pedido.justificativa, solic_alt_pedido.status, pedido.id_usuario FROM solic_alt_pedido, pedido WHERE pedido.id = solic_alt_pedido.id_pedido AND solic_alt_pedido.id_setor = " . $id_setor . ' ORDER BY solic_alt_pedido.id DESC LIMIT ' . LIMIT_MAX) or exit('Erro ao buscar solicitações de alteração de pedidos');
        $this->mysqli = NULL;
        $status = $label = "";
        $table = new Table('', '', array(), false);

        $array_status = ['Reprovado', 'Aprovado', 'Aberto'];
        $array_lb = ['red', 'green', 'orange'];

        while ($solic = $query->fetch_object()) {
            $status = $array_status[$solic->status];
            $label = 'bg-' . $array_lb[$solic->status];
            $solic->justificativa = str_replace("\"", "\'", $solic->justificativa);
            if ($solic->id_usuario == $_SESSION['id']) {
                $row = new Row();
                $row->addColumn(new Column($solic->id_pedido));
                $row->addColumn(new Column($solic->data_solicitacao));
                $row->addColumn(new Column(($solic->status == 2) ? '--------------' : $solic->data_analise));
                $row->addColumn(new Column(new Button('', BTN_DEFAULT, "viewCompl('" . $solic->justificativa . "')", "data-toggle=\"tooltip\"", 'Ver Justificativa', 'eye')));
                $row->addColumn(new Column(new Small('label ' . $label, $status)));

                $table->addRow($row);
            }
        }
        return $table;
    }

    /**
     * 	Função que retorna as solicitações de adiantamento de saldos do setor
     *
     * 	@return string
     */
    public function getSolicAdiSetor(): string {
        $this->openConnection();
        $id_setor = $_SESSION['id_setor'];
        $query = $this->mysqli->query("SELECT id, DATE_FORMAT(data_solicitacao, '%d/%m/%Y') AS data_solicitacao, DATE_FORMAT(data_analise, '%d/%m/%Y') AS data_analise, valor_adiantado, justificativa, status FROM saldos_adiantados WHERE id_setor = " . $id_setor . ' ORDER BY id DESC LIMIT ' . LIMIT_MAX) or exit("Erro ao buscar solicitações de adiantamento.");
        $this->mysqli = NULL;
        $label = $status = "";
        $array_status = ['Reprovado', 'Aprovado', 'Aberto'];
        $array_lb = ['red', 'green', 'orange'];

        $table = new Table('', '', [], false);
        while ($solic = $query->fetch_object()) {
            $status = $array_status[$solic->status];
            $label = 'bg-' . $array_lb[$solic->status];
            $solic->justificativa = str_replace("\"", "\'", $solic->justificativa);
            $solic->valor_adiantado = number_format($solic->valor_adiantado, 3, ',', '.');

            $row = new Row();
            $row->addColumn(new Column($solic->data_solicitacao));
            $row->addColumn(new Column(($solic->status == 2) ? '--------------' : $solic->data_analise));
            $row->addColumn(new Column($solic->valor_adiantado));
            $row->addColumn(new Column(new Button('', BTN_DEFAULT, "viewCompl('" . $solic->justificativa . "')", "data-toggle=\"tooltip\"", 'Ver Justificativa', 'eye')));
            $row->addColumn(new Column(new Small('label ' . $label, $status)));

            $table->addRow($row);
        }
        return $table;
    }

    /**
     * Função para mostrar os itens de um processo pesquisado no menu solicitações.
     *
     * @return string
     */
    public function getConteudoProcesso(string $busca): string {
        $this->openConnection();
        $query = $this->mysqli->query("SELECT id, id_item_processo, nome_fornecedor, cod_reduzido, complemento_item, replace(vl_unitario, ',', '.') AS vl_unitario, qt_contrato, qt_utilizado, vl_utilizado, qt_saldo, vl_saldo FROM itens WHERE num_processo LIKE '%" . $busca . "%' AND cancelado = 0") or exit("Erro ao buscar o conteúdo dos processos.");
        $this->mysqli = NULL;
        $table = new Table('', '', [], false);
        while ($item = $query->fetch_object()) {
            $item->complemento_item = str_replace("\"", "\'", $item->complemento_item);
            $btn = (!isset($_SESSION['editmode'])) ? new Button('', BTN_DEFAULT, "checkItemPedido(" . $item->id . ", '" . $item->vl_unitario . "', " . $item->qt_saldo . ")", "data-toggle=\"tooltip\"", 'Adicionar', 'plus') : new Button('', BTN_DEFAULT, "editaItem(" . $item->id . ")", "data-toggle=\"tooltip\"", 'Editar Informações', 'pencil');
            $input_qtd = (!isset($_SESSION['editmode'])) ? "<input type=\"number\" id=\"qtd" . $item->id . "\" min=\"1\" max=\"" . $item->qt_saldo . "\">" : '';
            $row = new Row();
            $row->addColumn(new Column($btn));
            $row->addColumn(new Column($item->nome_fornecedor));
            $row->addColumn(new Column($item->cod_reduzido));
            if (!isset($_SESSION['editmode'])) {
                $row->addColumn(new Column($input_qtd));
            }
            $row->addColumn(new Column(new Button('', BTN_DEFAULT, "viewCompl('" . $item->complemento_item . "')", "data-toggle=\"tooltip\"", 'Ver Detalhes', 'eye')));
            $row->addColumn(new Column($item->complemento_item, 'none'));
            $row->addColumn(new Column($item->vl_unitario));
            $row->addColumn(new Column($item->qt_saldo));
            $row->addColumn(new Column($item->qt_utilizado));
            $row->addColumn(new Column($item->vl_saldo));
            $row->addColumn(new Column($item->vl_utilizado));
            $row->addColumn(new Column($item->qt_contrato));

            $table->addRow($row);
        }
        return $table;
    }

    /**
     * Função para trazer a linha do item anexado ao pedido
     *
     * @return string
     */
    public function addItemPedido(int $id_item, int $qtd): string {
        $this->openConnection();
        $query = $this->mysqli->query("SELECT id, nome_fornecedor, num_processo, num_licitacao, cod_reduzido, complemento_item, replace(vl_unitario, ',', '.') AS vl_unitario, qt_saldo, qt_contrato, qt_utilizado, vl_saldo, vl_contrato, vl_utilizado FROM itens WHERE id = " . $id_item) or exit("Erro ao buscar ");
        $this->mysqli = NULL;
        $item = $query->fetch_object();
        $item->complemento_item = str_replace("\"", "\'", $item->complemento_item);
        $valor = $qtd * $item->vl_unitario;

        $inputs = "<input class=\"classItens\" type=\"hidden\" name=\"id_item[]\" value=\"" . $id_item . "\">
                   <input type=\"hidden\" name=\"qtd_solicitada[]\" value=\"" . $qtd . "\">
                   <input type=\"hidden\" name=\"qtd_disponivel[]\" value=\"" . $item->qt_saldo . "\">
                   <input type=\"hidden\" name=\"qtd_contrato[]\" value=\"" . $item->qt_contrato . "\">
                   <input type=\"hidden\" name=\"qtd_utilizado[]\" value=\"" . $item->qt_utilizado . "\">
                   <input type=\"hidden\" name=\"vl_saldo[]\" value=\"" . $item->vl_saldo . "\">
                   <input type=\"hidden\" name=\"vl_contrato[]\" value=\"" . $item->vl_contrato . "\">
                   <input type=\"hidden\" name=\"vl_utilizado[]\" value=\"" . $item->vl_utilizado . "\">
                   <input type=\"hidden\" name=\"valor[]\" value=\"" . $valor . "\">";

        $row = new Row('row' . $id_item);

        $row->addColumn(new Column(new Button('', BTN_DEFAULT, "removeTableRow(" . $id_item . ", '" . $valor . "')", "data-toggle=\"tooltip\"", 'Remover do Pedido', 'trash')));
        $row->addColumn(new Column($item->num_processo));
        $row->addColumn(new Column($item->cod_reduzido));
        $row->addColumn(new Column(new Button('', BTN_DEFAULT, "viewCompl('" . $item->complemento_item . "')", "data-toggle=\"tooltip\"", 'Ver Complemento do Item', 'eye')));
        $row->addColumn(new Column('R$ ' . $item->vl_unitario));
        $row->addColumn(new Column($item->nome_fornecedor));
        $row->addColumn(new Column($item->num_licitacao));
        $row->addColumn(new Column($qtd));
        $row->addColumn(new Column('R$ ' . $valor));
        $row->addColumn(new Column($inputs));

        return $row;
    }

    private static final function buildButtonsDraft(int $id_usuario, int $id): string {
        $group = "<div class=\"btn-group\">";

        $btnEdit = $btnDel = '';
        if ($id_usuario == $_SESSION['id']) {
            $btnEdit = new Button('', BTN_DEFAULT . ' btn-sm', "editaPedido(" . $id . ")", "data-toggle=\"tooltip\"", 'Editar', 'pencil');

            $btnDel = new Button('', BTN_DEFAULT . ' btn-sm', "deletePedido(" . $id . ")", "data-toggle=\"tooltip\"", 'Excluir', 'trash');
        }

        $btnPrint = new Button('', BTN_DEFAULT . ' btn-sm', "imprimir(" . $id . ")", "data-toggle=\"tooltip\"", 'Imprimir', 'print');

        $group .= $btnEdit . $btnPrint . $btnDel . '</div>';
        return $group;
    }

    /**
     * Função para retornar os rascunhos para continuar editando.
     *
     * @return string
     */
    public function getRascunhos(): string {
        $this->openConnection();
        $id_setor = $_SESSION['id_setor'];
        $query = $this->mysqli->query("SELECT id, DATE_FORMAT(data_pedido, '%d/%m/%Y') AS data_pedido, pedido.valor, status, pedido.id_usuario FROM pedido WHERE id_setor = " . $id_setor . ' AND alteracao = 1 ORDER BY id DESC LIMIT ' . LIMIT_MAX) or exit("Erro ao buscar rascunhos do setor.");
        $this->mysqli = NULL;

        $table = new Table('', '', [], false);
        while ($rascunho = $query->fetch_object()) {
            $rascunho->valor = number_format($rascunho->valor, 3, ',', '.');

            $row = new Row();
            $row->addColumn(new Column($rascunho->id));
            $row->addColumn(new Column(new Small('label bg-gray', ARRAY_STATUS[$rascunho->status])));
            $row->addColumn(new Column($rascunho->data_pedido));
            $row->addColumn(new Column('R$ ' . $rascunho->valor));
            $row->addColumn(new Column(self::buildButtonsDraft($rascunho->id_usuario, $rascunho->id)));

            $table->addRow($row);
        }
        return $table;
    }

    /**
     * 	Função que retorna o saldo dispónível do setor.
     *
     * 	@return string
     */
    public function getSaldo(int $id_setor): string {
        $this->openConnection();
        $query = $this->mysqli->query("SELECT saldo FROM saldo_setor WHERE id_setor = " . $id_setor) or exit("Erro ao buscar o saldo do setor.");
        if ($query->num_rows < 1) {
            $this->mysqli->query("INSERT INTO saldo_setor VALUES(NULL, " . $id_setor . ", '0.000');") or exit("Erro ao inserir o saldo do setor.");
            $this->mysqli = NULL;
            return '0.000';
        }
        $this->mysqli = NULL;
        $obj = $query->fetch_object();
        $saldo = number_format($obj->saldo, 3, '.', '');
        return $saldo;
    }

    /**
     * Function that returns the content of request for edition.
     * 
     * @param int $id_pedido Request's id.
     * @return string Rows with itens of $id_pedido param.
     */
    public function getConteudoPedido(int $id_pedido): string {
        $retorno = "";
        $this->openConnection();
        $query = $this->mysqli->query('SELECT id_item, qtd FROM itens_pedido WHERE id_pedido = ' . $id_pedido) or exit('Erro ao buscar o conteúdo do pedido');
        $this->mysqli = NULL;
        while ($item = $query->fetch_object()) {
            $retorno .= $this->addItemPedido($item->id_item, $item->qtd);
        }
        return $retorno;
    }

    /**
     * Função para o setor acompanhar o andamento do seu pedido.
     *
     * @return string
     */
    public function getMeusPedidos(string $where = '', array $pedidos = []): string {
        $this->openConnection();
        $id_setor = $_SESSION['id_setor'];
        $query = $this->mysqli->query("SELECT id, DATE_FORMAT(data_pedido, '%d/%m/%Y') AS data_pedido, prioridade, status, valor, id_usuario FROM pedido WHERE id_setor = " . $id_setor . ' AND alteracao = 0 ' . $where . ' ORDER BY id DESC LIMIT ' . LIMIT_MAX) or exit('Erro ao buscar os pedidos do setor');
        $this->mysqli = NULL;

        $table = new Table('', '', [], false);
        while ($pedido = $query->fetch_object()) {
            if (!in_array($pedido->id, $pedidos)) {
                $empenho = $this->verEmpenho($pedido->id);
                if ($empenho == 'EMPENHO SIAFI PENDENTE') {
                    $empenho = '';
                }
                $pedido->valor = number_format($pedido->valor, 3, ',', '.');

                $row = new Row('ped' . $pedido->id);

                $row->addColumn(new Column($pedido->id));
                $row->addColumn(new Column($pedido->data_pedido));
                $row->addColumn(new Column(ARRAY_PRIORIDADE[$pedido->prioridade]));
                $row->addColumn(new Column(new Small('label bg-gray', ARRAY_STATUS[$pedido->status])));
                $row->addColumn(new Column($empenho));
                $row->addColumn(new Column('R$ ' . $pedido->valor));
                $row->addColumn(new Column($this->getFornecedor($pedido->id)));
                $row->addColumn(new Column(self::buildButtonsMyRequests($pedido->id, $pedido->status, $pedido->id_usuario)));

                $table->addRow($row);
            }
        }
        return $table;
    }

    private static final function buildButtonsMyRequests(int $id, int $status, int $id_usuario): string {
        $group = "<div class=\"btn-group\">";

        $btnSolicAlt = ($status == 2 || $status == 5 && $id_usuario == $_SESSION['id']) ? new Button('', BTN_DEFAULT . ' btn-sm', "solicAltPed(" . $id . ")", "data-toggle=\"tooltip\"", 'Solicitar Alteração', 'wrench') : '';

        $btnPrint = new Button('', BTN_DEFAULT . ' btn-sm', "imprimir(" . $id . ")", "data-toggle=\"tooltip\"", 'Imprimir', 'print');

        $group .= $btnSolicAlt . $btnPrint . '</div>';

        return $group;
    }

    /**
     * Build rows with process in database.
     * 
     * @param string $tela If 'recepcao' - add process in tables used by reception, else - search itens of process.
     * @return string Rows with all process.
     */
    public function getProcessos(string $tela): string {
        $sql = 'SELECT DISTINCT num_processo FROM itens';
        $onclick = 'pesquisarProcesso';
        $title = 'Pesquisar Processo';
        $icon = 'search';
        $act = 'Pesquisar';
        if ($tela == 'recepcao') {
            $sql = 'SELECT DISTINCT num_processo FROM itens WHERE num_processo NOT IN (SELECT DISTINCT num_processo FROM processos)';
            $onclick = 'addProcesso';
            $title = 'Adicionar Processo';
            $icon = 'plus';
            $act = 'Adicionar';
        }
        $this->openConnection();
        $query = $this->mysqli->query($sql) or exit('Erro ao buscar os processos');
        $this->mysqli = NULL;
        $table = new Table('', '', [], false);
        while ($processo = $query->fetch_object()) {
            $row = new Row();
            $row->addColumn(new Column($processo->num_processo));
            $row->addColumn(new Column(new Button('', 'btn btn-primary', $onclick . "('" . $processo->num_processo . "', 0)", "data-toggle=\"tooltip\"", $title, $icon)));

            $table->addRow($row);
        }
        return $table;
    }

    private function getSetorTransf(int $id_lancamento) {
        $this->openConnection();
        $query = $this->mysqli->query("SELECT id_setor, valor FROM saldos_lancamentos WHERE id = " . $id_lancamento) or exit("Erro ao buscar setor da transferência");
        $obj = $query->fetch_object();

        $id = ($obj->valor < 0) ? $id_lancamento + 1 : $id_lancamento - 1;

        $query_l = $this->mysqli->query("SELECT saldos_lancamentos.id_setor, setores.nome AS setor, saldos_lancamentos.valor FROM saldos_lancamentos, setores WHERE setores.id = saldos_lancamentos.id_setor AND saldos_lancamentos.id = " . $id) or exit("Erro ao buscar nome do setor da transferência");
        $this->mysqli = NULL;

        $lancamento = $query_l->fetch_object();
        return $lancamento->setor;
    }

    /**
     * 	Função que retorna a tabela com os lançamentos de saldos pelo SOF
     *
     * 	@param $id_setor id do setor
     * 	@return string
     */
    public function getLancamentos(int $id_setor): string {
        $where = ($id_setor != 0) ? ' WHERE id_setor = ' . $id_setor : '';

        $this->openConnection();
        $query = $this->mysqli->query("SELECT id, id_setor, DATE_FORMAT(data, '%d/%m/%Y') AS data, valor, categoria FROM saldos_lancamentos" . $where . ' ORDER BY id DESC LIMIT ' . LIMIT_MAX) or exit('Erro ao buscar informações dos lançamentos');
        $this->mysqli = NULL;

        $table = new Table('', '', [], false);
        while ($lancamento = $query->fetch_object()) {
            $cor = ($lancamento->valor < 0) ? 'red' : 'green';
            $setor_transf = ($lancamento->categoria == 3) ? $this->getSetorTransf($lancamento->id) : '';

            $btn = ($_SESSION['id_setor'] == 2 && $lancamento->categoria != 4) ? new Button('', BTN_DEFAULT, "undoFreeMoney(" . $lancamento->id . ")", "data-toggle=\"tooltip\"", 'Desfazer', 'undo') : '';
            $lancamento->valor = number_format($lancamento->valor, 3, ',', '.');

            $row = new Row();
            $row->addColumn(new Column($btn));
            $row->addColumn(new Column(ARRAY_SETORES[$lancamento->id_setor]));
            $row->addColumn(new Column($lancamento->data));
            $row->addColumn(new Column("<span style=\"color: " . $cor . ";\">" . 'R$ ' . $lancamento->valor . "</span>"));
            $row->addColumn(new Column(ARRAY_CATEGORIA[$lancamento->categoria]));
            $row->addColumn(new Column($setor_transf));

            $table->addRow($row);
        }
        return $table;
    }

}
