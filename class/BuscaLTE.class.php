<?php

/**
 * Class with the functions used, principally, by file php/buscaLTE.php. Any function that returns data from the database, must be make in this class.
 *
 * Used by the new appearance of SOF (since v2.0)
 *
 * @author João Bolsson (joaovictorbolsson@gmail.com).
 * @since 2017, 15 Jan.
 */
ini_set('display_errors', true);
error_reporting(E_ALL);

spl_autoload_register(function (string $class_name) {
    include_once $class_name . '.class.php';
});

require_once '../defines.php';

final class BuscaLTE {

    /**
     * Default constructor.
     */
    private function __construct() {
        // empty
    }

    public static function getFatAprov(): string {
        /*
         | id            | int(10) unsigned | NO   | PRI | NULL    | auto_increment |
| lancamento    | date             | NO   |     | NULL    |                |
| competencia   | int(2) unsigned  | NO   | MUL | NULL    |                |
| producao      | int(10) unsigned | NO   | MUL | NULL    |                |
| financiamento | int(10) unsigned | NO   | MUL | NULL    |                |
| complexidade  | int(10) unsigned | NO   | MUL | NULL    |                |
| valor         | varchar(30)      | NO   |     | NULL    |                |

         */
        $query = Query::getInstance()->exe("SELECT faturamento.id, DATE_FORMAT(faturamento.lancamento, '%d/%m/%Y') AS lancamento, mes.sigla_mes as competencia, faturamento_producao.nome as producao, faturamento_financiamento.nome as financiamento, faturamento_complexidade.nome AS complexidade, faturamento.valor FROM faturamento, faturamento_complexidade, faturamento_financiamento, faturamento_producao, mes WHERE faturamento.competencia = mes.id AND faturamento.producao = faturamento_producao.id AND faturamento.financiamento = faturamento_financiamento.id AND faturamento.complexidade = faturamento_complexidade.id;");

        $table = new Table('', '', [], true);

        while ($obj = $query->fetch_object()) {
            $row = new Row();
            $row->addComponent(new Column($obj->lancamento));
            $row->addComponent(new Column($obj->competencia));
            $row->addComponent(new Column($obj->producao));
            $row->addComponent(new Column($obj->financiamento));
            $row->addComponent(new Column($obj->complexidade));
            $row->addComponent(new Column("R$ " . $obj->valor));

            $div = "<div class=\"btn-group\">";

            $div .= new Button('', BTN_DEFAULT . ' btn-sm', "editFatAprov(" . $obj->id . ")", "data-toggle = \"tooltip\"", 'Editar', 'pencil');

            $div .= new Button('', BTN_DANGER . ' btn-sm', "removeFatAprov(" . $obj->id . ")", "data-toggle = \"tooltip\"", 'Remover', 'trash');

            $div .= '</div>';

            $row->addComponent(new Column(""));

            $table->addComponent($row);
        }

        return $table->__toString();
    }

    public static function getReceitas(): string {
        $query = Query::getInstance()->exe("SELECT receita.id, tipo.nome AS tipo, mes.sigla_mes, DATE_FORMAT(receita.recebimento, '%d/%m/%Y') AS data, receita.valor, receita.pf, receita.observacoes FROM aihs_receita AS receita, aihs_receita_tipo AS tipo, mes WHERE receita.tipo = tipo.id AND receita.competencia = mes.id;");

        $table = new Table('', '', [], true);

        while ($obj = $query->fetch_object()) {
            $row = new Row();
            $row->addComponent(new Column($obj->tipo));
            $row->addComponent(new Column($obj->sigla_mes));
            $row->addComponent(new Column($obj->data));
            $row->addComponent(new Column("R$ " . $obj->valor));
            $row->addComponent(new Column($obj->pf));
            $row->addComponent(new Column($obj->observacoes));

            $div = "<div class=\"btn-group\">";

            $div .= new Button('', BTN_DEFAULT . ' btn-sm', "editReceita(" . $obj->id . ")", "data-toggle = \"tooltip\"", 'Editar', 'pencil');

            $div .= new Button('', BTN_DANGER . ' btn-sm', "removeReceita(" . $obj->id . ")", "data-toggle = \"tooltip\"", 'Remover', 'trash');

            $div .= '</div>';

            $row->addComponent(new Column($div));

            $table->addComponent($row);
        }

        return $table->__toString();
    }

    public static function getAIHS(): string {
        $query = Query::getInstance()->exe("SELECT aihs.id, aihs.descricao, aihs.grupo, aihs.qtd, aihs.valor, mes.sigla_mes, DATE_FORMAT(aihs.data, '%d/%m/%Y') AS data, aihs_tipos.nome AS tipo FROM aihs, mes, aihs_tipos WHERE mes.id = aihs.mes AND aihs_tipos.id = aihs.tipo;");

        $table = new Table('', '', [], true);

        while ($obj = $query->fetch_object()) {
            $row = new Row();
            $row->addComponent(new Column($obj->tipo));
            $row->addComponent(new Column($obj->grupo));
            $row->addComponent(new Column($obj->descricao));
            $row->addComponent(new Column($obj->qtd));
            $row->addComponent(new Column("R$ " . $obj->valor));
            $row->addComponent(new Column($obj->sigla_mes));
            $row->addComponent(new Column($obj->data));

            $div = "<div class=\"btn-group\">";

            $div .= new Button('', BTN_DEFAULT . ' btn-sm', "editAIHS(" . $obj->id . ")", "data-toggle = \"tooltip\"", 'Editar', 'pencil');

            $div .= new Button('', BTN_DANGER . ' btn-sm', "removeAIHS(" . $obj->id . ")", "data-toggle = \"tooltip\"", 'Remover', 'trash');

            $div .= '</div>';

            $row->addComponent(new Column($div));

            $table->addComponent($row);
        }

        return $table->__toString();
    }


    /**
     * Look for proccess that was not returned to SOF.
     *
     * @return string Table with the informations.
     */
    public static function getProcNaoDev(): string {
        $table = new Table('', 'table table-bordered table-striped', ['Processo', 'Saída', 'Responsável'], true);

        $query = Query::getInstance()->exe("SELECT num_processo, saida, responsavel FROM processos WHERE saida != '----------' AND retorno = '----------';");

        if ($query->num_rows > 0) {
            while ($obj = $query->fetch_object()) {
                $row = new Row();
                $row->addComponent(new Column($obj->num_processo));
                $row->addComponent(new Column($obj->saida));
                $row->addComponent(new Column($obj->responsavel));

                $table->addComponent($row);
            }
        }

        return $table->__toString();
    }

    public static function buildRelProcsVenc(): string {
        $mes = date('n');
        $ano = date('Y');

        $query = Query::getInstance()->exe("SELECT DISTINCT itens_pedido.id_pedido, pedido.valor, itens.num_processo, DATE_FORMAT(itens.dt_fim, '%d/%m/%Y') AS dt_fim, pedido.status, setores.nome AS setor FROM pedido, itens, itens_pedido, setores WHERE pedido.id_setor = setores.id AND pedido.status = 2 AND pedido.id = itens_pedido.id_pedido AND itens.id = itens_pedido.id_item AND MONTH(itens.dt_fim) = " . $mes . " AND YEAR(itens.dt_fim) = " . $ano . " ORDER BY dt_fim DESC;");

        $sum = 0;
        $rel = "
            <fieldset class=\"preg\">
                    <h5>DESCRIÇÃO DO RELATÓRIO</h5>
                    <h6>Pedidos em Vencimento para " . $mes . "/" . $ano . "</h6>";


        $table_setores = [];

        if ($query->num_rows > 0) {
            while ($obj = $query->fetch_object()) {
                if (!array_key_exists($obj->setor, $table_setores)) {
                    $table_setores[$obj->setor] = new Table('', '', ['Pedido', 'Valor', 'Processo', 'Data Fim'], true);
                }
                $sum += floatval($obj->valor);

                $row = new Row();
                $row->addComponent(new Column($obj->id_pedido));
                $row->addComponent(new Column(number_format($obj->valor, 3, ',', '.')));
                $row->addComponent(new Column($obj->num_processo));
                $row->addComponent(new Column($obj->dt_fim));

                $table = $table_setores[$obj->setor];
                if ($table instanceof Table) {
                    $table->addComponent($row);
                }
            }
        }

        $rel .= "<h6>Totalizando: R$ " . number_format($sum, 3, ',', '.') . "</h6>
                </fieldset><br>";

        $tables = "";

        foreach ($table_setores as $key => $value) {
            if ($value instanceof Table) {
                $tables .= "
            <fieldset class=\"preg\">
                    <h5>SETOR: " . $key . " | Pedidos: " . count($value->getComponents()) . "</h5>   </fieldset>";
                $tables .= $value->__toString();
            }
        }
        return $rel . $tables;
    }

    /**
     * The proccess that will be finished in the current month.
     *
     * @return string Table's body with the informations.
     */
    public static function loadProcsVenc(): string {
        $mes = date('n');
        $ano = date('Y');

        $query = Query::getInstance()->exe("SELECT DISTINCT itens_pedido.id_pedido, itens.num_processo, itens.nome_fornecedor, DATE_FORMAT(itens.dt_fim, '%d/%m/%Y') AS dt_fim, pedido.status, setores.nome AS setor FROM pedido, itens, itens_pedido, setores WHERE pedido.id_setor = setores.id AND pedido.status = 2 AND pedido.id = itens_pedido.id_pedido AND itens.id = itens_pedido.id_item AND MONTH(itens.dt_fim) = " . $mes . " AND YEAR(itens.dt_fim) = " . $ano . " ORDER BY dt_fim;");

        $table = new Table('', '', [], false);
        if ($query->num_rows > 0) {
            while ($obj = $query->fetch_object()) {
                $row = new Row();
                $row->addComponent(new Column($obj->id_pedido));
                $row->addComponent(new Column($obj->setor));
                $row->addComponent(new Column($obj->num_processo));
                $row->addComponent(new Column($obj->nome_fornecedor));
                $row->addComponent(new Column($obj->dt_fim));

                $table->addComponent($row);
            }
        }

        return $table->__toString();
    }

    public static function getEmpenho(int $id_pedido): string {
        $query = Query::getInstance()->exe("SELECT empenho, DATE_FORMAT(data, '%d/%m/%Y') AS data FROM pedido_empenho WHERE id_pedido = " . $id_pedido);
        $obj = "";
        if ($query->num_rows > 0) {
            $obj = $query->fetch_object();
        }
        return json_encode($obj);
    }

    public static function getSources(int $id_pedido): string {
        $query = Query::getInstance()->exe("SELECT fonte_recurso, ptres, plano_interno FROM pedido_fonte WHERE id_pedido = " . $id_pedido);
        $obj = "";
        if ($query->num_rows > 0) {
            $obj = $query->fetch_object();
        }
        return json_encode($obj);
    }

    /**
     * @return string Justifies options to make a transference.
     */
    public static function getJustifies(): string {
        $query = Query::getInstance()->exe("SELECT justificativa FROM saldo_justificativa;");

        $return = "";

        while ($obj = $query->fetch_object()) {
            $obj->justificativa = str_replace("\"", "'", $obj->justificativa);
            $return .= "<option value=\"" . $obj->justificativa . "\">" . $obj->justificativa . "</option>";
        }

        return $return;
    }

    /**
     * Gets an array with information of the last register of user.
     * @param int $status If 1 - last register is a log in, else if a log out.
     * @param int $id_user Id of the user to format last register.
     * @return array with 'date' and 'time' of last register of the user.
     */
    private static function formatTimeLast(int $status, int $id_user): array {
        $column = ($status == 1) ? 'entrada' : 'saida';

        $id_last = Busca::getLastRegister($id_user);
        $query = Query::getInstance()->exe("SELECT DATE_FORMAT(" . $column . ", '%d/%m/%Y') AS date, DATE_FORMAT(" . $column . ", '%H:%i:%s') AS time FROM usuario_hora WHERE id = " . $id_last);

        $array = $query->fetch_array();
        return $array;
    }

    private static final function buildButtonsAdminTool(int $id_log): string {
        $div = "<div class=\"btn-group\">";

        $div .= new Button('', 'btn btn-default btn-sm', "editLog(" . $id_log . ")", "data-toggle = \"tooltip\"", 'Editar', 'pencil');

        $div .= '</div>';
        return $div;
    }

    /**
     * @return string Table of administration tool.
     */
    public static function loadAdminTable(): string {
        $query = Query::getInstance()->exe("SELECT usuario_hora.id, usuario.nome, DATE_FORMAT(entrada, '%d/%m/%Y %H:%i:%s') AS entrada, DATE_FORMAT(saida, '%d/%m/%Y %H:%i:%s') AS saida FROM usuario_hora, usuario WHERE usuario.id = usuario_hora.id_usuario ORDER BY usuario_hora.id DESC LIMIT " . LIMIT_LOGS);

        $table = new Table('', '', [], false);
        while ($obj = $query->fetch_object()) {
            $row = new Row();
            $row->addComponent(new Column($obj->nome));
            $row->addComponent(new Column($obj->entrada));
            $obj->saida = ($obj->saida == NULL) ? '--------------------' : $obj->saida;
            $row->addComponent(new Column($obj->saida));
            $row->addComponent(new Column(self::buildButtonsAdminTool($obj->id)));

            $table->addComponent($row);
        }

        return $table;
    }

    public static function refreshTableHora(): string {
        $query = Query::getInstance()->exe('SELECT DISTINCT usuario_hora.id_usuario, usuario.nome FROM usuario_hora, usuario WHERE usuario_hora.id_usuario = usuario.id AND usuario.ativo = 1 ORDER BY usuario.nome ASC');

        $table = new Table('', '', [], false);

        while ($obj = $query->fetch_object()) {
            $info = !Busca::getInfoTime($obj->id_usuario);
            $status = ($info == 1) ? 'Entrada' : 'Saída';
            $color = ($info == 1) ? 'green' : 'red';

            $row = new Row();
            $row->addComponent(new Column($obj->nome));
            $row->addComponent(new Column(new Small('label bg-' . $color, $status)));
            $array = self::formatTimeLast($info, $obj->id_usuario);
            $row->addComponent(new Column($array['time']));
            $row->addComponent(new Column($array['date']));

            $table->addComponent($row);
        }

        return $table;
    }

    /**
     * Function that returns the 'tabs' with the pages of news to edit.
     *
     * @return string
     */
    public static function getTabsNoticiasLTE(): string {
        $query = Query::getInstance()->exe('SELECT id, tabela, nome FROM paginas_post');
        $row = new Row();
        while ($pag = $query->fetch_object()) {
            $radio = "<div class=\"form-group\" style=\"display: inline-block;\">
                    <label>
                        <input id=\"pag" . $pag->id . "\" type=\"radio\" name=\"pag\" class=\"minimal\" onclick=\"carregaPostsPag(" . $pag->id . ")\"/>
                        " . $pag->nome . "
                    </label>
                </div>";
            $row->addComponent(new Column($radio));
        }
        return $row;
    }

    /**
     * Function to return the news table of a page to edition.
     *
     * @param int $table_id
     * @return string
     */
    public static function getNoticiasEditar(int $table_id): string {
        $query = Query::getInstance()->exe("SELECT id, tabela, titulo, DATE_FORMAT(data, '%d/%m/%Y') AS data FROM postagens WHERE ativa = 1 AND tabela = " . $table_id . ' ORDER BY data ASC');
        $table = new Table('', '', [], false);
        while ($post = $query->fetch_object()) {
            $btn_group = "<div class=\"btn-group\">";
            $btn_group .= new Button('', BTN_DEFAULT . ' btn-sm', "editaNoticia(" . $post->id . ", " . $post->tabela . ")", "data-toggle=\"tooltip\"", 'Editar', 'pencil');

            $btn_group .= new Button('', BTN_DEFAULT . ' btn-sm', "excluirNoticia(" . $post->id . ")", "data-toggle=\"tooltip\"", 'Excluir', 'trash');
            $btn_group .= '</div>';

            $row = new Row();

            $row->addComponent(new Column($post->titulo));
            $row->addComponent(new Column($post->data));
            $row->addComponent(new Column($btn_group));

            $table->addComponent($row);
        }
        return $table;
    }

    /**
     * Function to write the options to "Post in" of the administrative panel.
     *
     * @return string
     */
    public static function getPostarEm(): string {
        $return = '';
        $query = Query::getInstance()->exe('SELECT id, nome FROM paginas_post');
        while ($page = $query->fetch_object()) {
            $return .= "<option id=\"op" . $page->id . "\" value=\"" . $page->id . "\">" . $page->nome . "</option>";
        }
        return $return;
    }

    /**
     * @return mixed Object with the quantities of requests.
     */
    public static function getCountSolic() {
        $query = Query::getInstance()->exe('SELECT count(saldos_adiantados.id) AS solic_adi, (SELECT count(solic_alt_pedido.id) FROM solic_alt_pedido WHERE solic_alt_pedido.status = 2) AS solic_alt, (SELECT count(pedido.id) FROM pedido WHERE pedido.status = 2) AS solic_ped FROM saldos_adiantados WHERE saldos_adiantados.status = 2');
        $obj = $query->fetch_object();
        return $obj;
    }

    /**
     * @return string A row with contract options.
     */
    public static function getOptionsContrato(): string {
        $query = Query::getInstance()->exe('SELECT id, nome FROM contrato_tipo');
        $row = new Row();
        while ($obj = $query->fetch_object()) {
            $input = "<div class=\"form-group\">
                        <input type=\"radio\" name=\"tipoCont\" id=\"tipoCont" . $obj->id . "\" class=\"minimal\" value=\"" . $obj->id . "\">&nbsp;" . $obj->nome . "</div>";
            $row->addComponent(new Column($input));
        }

        return $row;
    }

    /**
     * @param bool $users
     * @param int $id_sector
     * @return string Options with the registered users.
     */
    public static function getUsers(bool $users = false, int $id_sector = 0): string {
        $where = ($id_sector != 0) ? 'WHERE id_setor = ' . $id_sector : '';

        if ($users) {
            $where = "WHERE login = 'uapublico'";
        }
        $where .= ($where == '') ? "WHERE ativo = 1" : ' AND ativo = 1';
        $query = Query::getInstance()->exe('SELECT id, nome, id_setor FROM usuario ' . $where . ' ORDER BY nome ASC');

        $return = "";
        while ($user = $query->fetch_object()) {
            $return .= "<option value=\"" . $user->id . "\">" . $user->nome . " (" . ARRAY_SETORES[$user->id_setor] . ")</option>";
        }
        return $return;
    }

    /**
     *
     * @param int $id_sector Sector id.
     * @return string Option with the groups of sector.
     */
    public static function getOptionsGrupos(int $id_sector): string {
        $query = Query::getInstance()->exe('SELECT id, nome, cod FROM setores_grupos WHERE id_setor = ' . $id_sector);
        $return = '';
        if ($query->num_rows > 0) {
            while ($obj = $query->fetch_object()) {
                $return .= "<option value=\"" . $obj->id . "\">" . utf8_encode($obj->nome) . '&nbsp;(' . $obj->cod . ')' . '</option>';
            }
        }
        return $return;
    }

    public static function getOptionsSources(int $id_sector): string {
        $query = Query::getInstance()->exe("SELECT id, valor, fonte_recurso, ptres FROM saldo_fonte WHERE id_setor = " . $id_sector);
        $options = "";
        if ($query->num_rows > 0) {
            while ($obj = $query->fetch_object()) {
                $options .= "<option value=\"" . $obj->id . "\">" . $obj->fonte_recurso . ' (R$ ' . $obj->valor . ')</option>';
            }
        }
        return $options;
    }

    /**
     * Function that build the rows of a table with the the bidding options to make a request
     *
     * @param int $cont Number of radios that must be in every single line.
     * @return string Radios buttons with the bidding options for a request.
     */
    public static function getOptionsLicitacao(int $cont): string {
        $query = Query::getInstance()->exe('SELECT id, nome FROM licitacao_tipo');
        $return = '<tr>';
        $i = 0;
        while ($obj = $query->fetch_object()) {
            if ($i == $cont) {
                $i = 0;
                $return .= '</tr><tr>';
            }
            $return .= "
                <td>
                    <div class=\"form-group\">
                        <input type=\"radio\" name=\"tipoLic\" id=\"tipoLic" . $obj->id . "\" class=\"minimal\" value=\"" . $obj->id . "\" required > " . $obj->nome . "
                    </div>
                </td>";
            $i++;
        }

        return $return;
    }

    /**
     * Function to search informations of the provider of a request.
     *
     * @param int $id_request Request id.
     * @return string Provider.
     */
    public static function getFornecedor(int $id_request): string {
        $query = Query::getInstance()->exe('SELECT itens.nome_fornecedor FROM itens, itens_pedido WHERE itens_pedido.id_item = itens.id AND itens_pedido.id_pedido = ' . $id_request . ' LIMIT 1');
        if ($query->num_rows < 1) {
            return '';
        }

        $obj = $query->fetch_object();
        return $obj->nome_fornecedor;
    }

    /**
     * Function that returns the requests in analysis and the total its.
     *
     * @param int $id_sector Sector id.
     * @return string
     */
    public static function getPedidosAnalise(int $id_sector): string {
        $return = '';
        $query = Query::getInstance()->exe('SELECT valor FROM pedido WHERE id_setor = ' . $id_sector . ' AND status = 2');
        $sum = $cont = 0;
        while ($ped = $query->fetch_object()) {
            $sum += $ped->valor;
            $cont++;
        }
        if ($sum > 0) {
            $sum = number_format($sum, 3, ',', '.');
            $return = "
                <tr>
                    <td colspan=\"2\">Você tem " . $cont . " pedido(s) em análise no total de R$ " . $sum . "</td>
                </tr>";
        }
        return $return;
    }

    /**
     * Function that returns the options with the registered sectors in system.
     *
     * @return string
     */
    public static function getOptionsSetores(array $sectors_in = [], array $sectors_out = []): string {
        $return = '';
        if (count($sectors_in) > 0) {
            $len = count($sectors_in);
            for ($j = 0; $j < $len; $j++) {
                $i = $sectors_in[$j];
                if (!in_array($i, $sectors_out)) {
                    $return .= "<option value=\"" . $i . "\">" . ARRAY_SETORES[$i] . '</option>';
                }
            }
        } else {
            $count = count(ARRAY_SETORES);

            for ($i = 2; $i < $count; $i++) {
                if (!in_array($i, $sectors_out)) {
                    $return .= "<option value=\"" . $i . "\">" . ARRAY_SETORES[$i] . '</option>';
                }
            }
        }
        return $return;
    }

    /**
     * Function that returns the option with exists priorities in system for the requests.
     *
     * @return string
     */
    public static function getOptionsPrioridades(): string {
        $return = "<option value=\"0\">Todas</option>";
        $count = count(ARRAY_PRIORIDADE);

        for ($i = 1; $i < $count; $i++) {
            if (ARRAY_PRIORIDADE[$i] != 'Rascunho') {
                $return .= "<option value=\"" . $i . "\">" . ARRAY_PRIORIDADE[$i] . '</option>';
            }
        }
        return $return;
    }

    public static function getOptionsCategoria(): string {
        $return = '';
        $count = count(ARRAY_CATEGORIA);

        for ($i = 1; $i < $count; $i++) {
            $return .= "<option value=\"" . $i . "\">" . ARRAY_CATEGORIA[$i] . '</option>';
        }
        return $return;
    }

    /**
     * Function that returns the options with the registered status in system.
     *
     * @return string
     */
    public static function getOptionsStatus(): string {
        $return = '';
        $count = count(ARRAY_STATUS);
        for ($i = 2; $i < $count; $i++) {
            $return .= "<option value=\"" . $i . "\">" . ARRAY_STATUS[$i] . '</option>';
        }
        return $return;
    }

    /**
     * Function that returns the options to the type of process to the register of new ones.
     *
     * @return string
     */
    public static function getTiposProcessos(): string {
        $return = '';
        $query = Query::getInstance()->exe('SELECT id, nome FROM processos_tipo');
        while ($type = $query->fetch_object()) {
            $return .= "<option value=\"" . $type->id . "\">" . $type->nome . '</option>';
        }
        return $return;
    }

    /**
     * @param int $id_request Request id.
     * @return bool If the requests is a draft - true, else - false.
     */
    public static function getRequestDraft(int $id_request): bool {
        $query = Query::getInstance()->exe('SELECT prioridade FROM pedido WHERE id = ' . $id_request);
        $obj = $query->fetch_object();
        return ARRAY_PRIORIDADE[$obj->prioridade] == 'Rascunho';
    }

    /**
     * Function that builds the radios buttons in analysis of requests.
     *
     * @param int $cont Number of radios button by line.
     * @return string
     */
    public static function getStatus(int $cont): string {
        $return = '<tr>';
        $i = 0;
        $count = count(ARRAY_STATUS);

        for ($j = 2; $j < $count; $j++) {
            if ($i == $cont) {
                $i = 0;
                $return .= '</tr><tr>';
            }
            $return .= "
                <td>
                    <div class=\"form-group\">
                        <label>
                            <input id=\"st" . $j . "\" type=\"radio\" name=\"fase\" class=\"minimal\" value=\"" . $j . "\"/>" . ARRAY_STATUS[$j] . "</label>
                    </div>
                </td>";
            $i++;
        }
        $return .= '</tr>';
        return $return;
    }

    /**
     * @return string The radios buttons of priorities of the requests.
     */
    public static function getPrioridades(): string {
        $count = count(ARRAY_PRIORIDADE);

        $row = new Row();
        for ($i = 1; $i < $count; $i++) {
            $form_group = "<div class=\"form-group\">
                        <input type=\"radio\" name=\"st\" id=\"st" . ARRAY_PRIORIDADE[$i] . "\" class=\"minimal\" value=\"" . $i . "\"> " . ARRAY_PRIORIDADE[$i] . "
                    </div>";

            $row->addComponent(new Column($form_group));
        }
        return $row;
    }

    /**
     * Function used to returns the table of process of reception.
     *
     * @return string
     */
    public static function getTabelaRecepcao(): string {
        $query = Query::getInstance()->exe('SELECT processos.id, processos.num_processo, processos_tipo.nome AS tipo, processos.estante, processos.prateleira, processos.entrada, processos.saida, processos.responsavel, processos.retorno, processos.obs FROM processos, processos_tipo WHERE processos.tipo = processos_tipo.id ORDER BY id ASC');
        $table = new Table('', '', [], false);
        while ($process = $query->fetch_object()) {
            $process->obs = str_replace("\"", "\'", $process->obs);

            $row = new Row();
            $div = new Component('div', 'btn-group');
            $div->addComponent(new Button('', BTN_DEFAULT, "addProcesso('', " . $process->id . ")", "data-toggle=\"tooltip\"", 'Editar', 'pencil'));
            $row->addComponent(new Column($div));
            $row->addComponent(new Column($process->num_processo));
            $row->addComponent(new Column($process->tipo));
            $row->addComponent(new Column($process->estante));
            $row->addComponent(new Column($process->prateleira));
            $row->addComponent(new Column($process->entrada));
            $row->addComponent(new Column($process->saida));
            $row->addComponent(new Column($process->responsavel));
            $row->addComponent(new Column($process->retorno));
            $row->addComponent(new Column(new Button('', BTN_DEFAULT, "viewCompl('" . $process->obs . "')", "data-toggle=\"tooltip\"", 'Ver Observação', 'eye')));
            $table->addComponent($row);
        }
        return $table;
    }

    /**
     * @return string The permissions for user registration.
     */
    public static function getCheckPermissoes(): string {
        $query = Query::getInstance()->exe("SELECT DISTINCT column_name AS nome FROM information_schema.columns WHERE table_name = 'usuario_permissoes' AND column_name <> 'id_usuario' AND table_schema = '" . Conexao::getInstance()->getDatabase() . "';");
        $return = "";
        $i = 1;
        while ($obj = $query->fetch_object()) {
            $nome = ucfirst($obj->nome);
            $return .= "
                <div class=\"form-group\" style=\"display: inline-block;\">
                    <label>
                        <input id=\"perm" . $i . "\" type=\"checkbox\" name=\"" . $obj->nome . "\" class=\"minimal\"/>
                        " . $nome . "
                    </label>
                </div>";
            $i++;
        }
        return $return;
    }

    /**
     * Function that returns the User's permissions.
     *
     * @param int $id_user User's id
     * @return object JSON with the user's permissions in system.
     */
    public static function getPermissoes(int $id_user) {
        $query = Query::getInstance()->exe('SELECT noticias, saldos, pedidos, recepcao, aihs FROM usuario_permissoes WHERE id_usuario = ' . $id_user);
        $obj_permissions = $query->fetch_object();
        return $obj_permissions;
    }

    private static final function buildButtonsSolicAltPedAdmin(int $id, int $id_pedido): string {
        $group = "<div class=\"btn-group\">";
        $btn_approve = new Button('', BTN_DEFAULT, "analisaSolicAlt(" . $id . ", " . $id_pedido . ", 1)", "data-toggle=\"tooltip\"", 'Aprovar', 'check');
        $btn_reprove = new Button('', BTN_DEFAULT, "analisaSolicAlt(" . $id . ", " . $id_pedido . ", 0)", "data-toggle=\"tooltip\"", 'Reprovar', 'trash');

        $group .= $btn_approve . $btn_reprove . '</div>';
        return $group;
    }

    /**
     * Gets the information of a column in table by id.
     *
     * @param string $table Table to search.
     * @param string $column Column to search.
     * @param int $id Row id.
     * @return string Information in that column.
     */
    public static function showInformation(string $table, string $column, int $id): string {
        $query = Query::getInstance()->exe("SELECT " . $column . " FROM " . $table . " WHERE id=" . $id . " LIMIT 1;");
        $obj = $query->fetch_object();
        return $obj->{$column};
    }

    /**
     * Function that returns the table with the requests change orders to SOF analysis
     *
     * @param int $st
     * @return string
     */
    public static function getAdminSolicAltPedidos(int $st): string {
        $query = Query::getInstance()->exe("SELECT solic_alt_pedido.id, solic_alt_pedido.id_pedido, setores.nome, DATE_FORMAT(solic_alt_pedido.data_solicitacao, '%d/%m/%Y') AS data_solicitacao, DATE_FORMAT(solic_alt_pedido.data_analise, '%d/%m/%Y') AS data_analise, solic_alt_pedido.justificativa, solic_alt_pedido.status FROM solic_alt_pedido, setores WHERE solic_alt_pedido.id_setor = setores.id AND solic_alt_pedido.status = " . $st . " ORDER BY solic_alt_pedido.id DESC LIMIT 200");

        $array_status = ['Reprovado', 'Aprovado', 'Aberto'];
        $array_lb = ['red', 'green', 'orange'];

        $status = $array_status[$st];
        $label = 'bg-' . $array_lb[$st];

        $table = new Table('', '', [], false);
        while ($request = $query->fetch_object()) {
            $btn_group = ($st == 2) ? self::buildButtonsSolicAltPedAdmin($request->id, $request->id_pedido) : '';
            $request->justificativa = str_replace("\"", "\'", $request->justificativa);

            $row = new Row();
            $row->addComponent(new Column($btn_group));
            $row->addComponent(new Column($request->id_pedido));
            $row->addComponent(new Column($request->nome));
            $row->addComponent(new Column($request->data_solicitacao));
            $row->addComponent(new Column(($st == 2) ? '--------------' : $request->data_analise));
            $row->addComponent(new Column(new Button('', 'btn btn-sm btn-primary', "showInformation('solic_alt_pedido', 'justificativa', $request->id);", "data-toggle=\"tooltip\"", 'Ver Justificativa', 'eye')));
            $row->addComponent(new Column(new Small('label pull-right ' . $label, $status)));
            $table->addComponent($row);
        }
        if ($table->isEmpty()) {
            return "";
        }
        return $table;
    }

    /**
     * Function that returns the advances requests sent to SOF analyse.
     *
     * @param int $st Status
     * @return string
     */
    public static function getSolicAdiantamentos(int $st): string {
        $query = Query::getInstance()->exe("SELECT saldos_adiantados.id, setores.nome, DATE_FORMAT(saldos_adiantados.data_solicitacao, '%d/%m/%Y') AS data_solicitacao, DATE_FORMAT(saldos_adiantados.data_analise, '%d/%m/%Y') AS data_analise, saldos_adiantados.valor_adiantado, saldos_adiantados.justificativa FROM saldos_adiantados, setores WHERE saldos_adiantados.id_setor = setores.id AND saldos_adiantados.status = " . $st . " ORDER BY saldos_adiantados.data_solicitacao DESC LIMIT 200");
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
                $row->addComponent(new Column($btn_group));
                $row->addComponent(new Column($solic->nome));
                $row->addComponent(new Column($solic->data_solicitacao));
                $row->addComponent(new Column(($st == 2) ? '---------------' : $solic->data_analise));
                $row->addComponent(new Column($solic->valor_adiantado));
                $row->addComponent(new Column(new Button('', 'btn btn-sm btn-primary', "viewCompl('" . $solic->justificativa . "')", "data-toggle=\"tooltip\"", 'Ver Justificativa', 'eye')));
                $row->addComponent(new Column(new Small('label pull-right bg-' . $label, $status)));

                $table->addComponent($row);
            }
        }
        if ($table->isEmpty()) {
            return "";
        }
        return $table;
    }

    private static final function buildButtonsSolicAdi(int $id): string {
        $div = new Component('div', 'btn-group');

        $div->addComponent(new Button('', BTN_DEFAULT, "analisaAdi(" . $id . ", 1)", "data-toggle=\"tooltip\"", 'Aprovar', 'check'));
        $div->addComponent(new Button('', BTN_DEFAULT, "analisaAdi(" . $id . ", 0)", "data-toggle=\"tooltip\"", 'Reprovar', 'trash'));

        return $div;
    }

    /**
     * @param int $id_request Request id.
     * @return string Date register of the effort.
     */
    private static function verDataEmpenho(int $id_request): string {
        $query = Query::getInstance()->exe("SELECT DATE_FORMAT(data, '%d/%m/%Y') AS data FROM pedido_empenho WHERE id_pedido = " . $id_request . ' LIMIT 1');

        if ($query->num_rows < 1) {
            return '';
        }
        $obj = $query->fetch_object();
        return $obj->data;
    }

    /**
     * Builds the buttons to the analysis.
     *
     * @param int $id Request id.
     * @param int $status Current status of the request.
     * @param int $id_sector Sector id that did the request..
     * @return string
     */
    private static function buildButtons(int $id, int $status, int $id_sector): string {
        $component = new Component('div', 'btn-group');

        if ($status != 3 && $status != 4) {
            if ($_SESSION['id_setor'] == 12) {
                $component->addComponent(new Button('', BTN_DEFAULT, "enviaForn(" . $id . ")", "data-toggle=\"tooltip\"", 'Enviar ao Fornecedor', 'send'));
            } else if ($status == 2) {
                $component->addComponent(new Button('', BTN_DEFAULT, "analisarPedido(" . $id . ", " . $id_sector . ")", "data-toggle=\"tooltip\"", 'Analisar', 'pencil'));
            } else if ($status == 5) {
                $component->addComponent(new Button('', BTN_DEFAULT, "cadFontes(" . $id . ")", "data-toggle=\"tooltip\"", 'Cadastrar Fontes', 'comment'));
            } else if ($status == 6) {
                $component->addComponent(new Button('', BTN_DEFAULT, "cadEmpenho(" . $id . ")", "data-toggle=\"tooltip\"", 'Cadastrar Empenho', 'credit-card'));
            } else if ($status == 7) {
                $component->addComponent(new Button('', BTN_DEFAULT, "enviaOrdenador(" . $id . ")", "data-toggle=\"tooltip\"", 'Enviar ao Ordenador', 'send'));
            } else {
                $component->addComponent(new Button('', BTN_DEFAULT, "getStatus(" . $id . ", " . $id_sector . ")", "data-toggle=\"tooltip\"", 'Alterar Status', 'wrench'));
            }
        }

        if (Busca::verEmpenho($id) != 'EMPENHO SIAFI PENDENTE' && $_SESSION['id_setor'] != 12 && $status > 6) {
            $component->addComponent(new Button('', BTN_DEFAULT, "cadEmpenho(" . $id . ", '" . Busca::verEmpenho($id) . "', '" . self::verDataEmpenho($id) . "')", "data-toggle=\"tooltip\"", 'Cadastrar Empenho', 'credit-card'));
        }

        $component->addComponent(new Button('', BTN_DEFAULT, "imprimir(" . $id . ")", "data-toggle=\"tooltip\"", 'Imprimir', 'print'));
        return $component;
    }

    /**
     * Function that returns the requests to SOF (used in main page).
     *
     * @param string $where
     * @param array $requests
     * @return string
     */
    public static function getSolicitacoesAdmin(string $where = '', array $requests = []): string {
        $query = Query::getInstance()->exe("SELECT id, id_setor, DATE_FORMAT(data_pedido, '%d/%m/%Y') AS data_pedido, prioridade, status, valor, aprov_gerencia FROM pedido WHERE status <> 3 AND alteracao = 0 " . $where . ' ORDER BY id DESC LIMIT ' . LIMIT_MAX);

        $table = new Table('', '', [], false);
        while ($request = $query->fetch_object()) {
            // determines if the request will add in the table
            $flag = false;

            if (!in_array($request->id, $requests)) {
                if ($_SESSION['id_setor'] == 12) {
                    if ($request->status == 8) {
                        $flag = true;
                    }
                } else {
                    $flag = true;
                }
            }

            if ($flag) {
                $btnVerEmpenho = Busca::verEmpenho($request->id);
                if ($btnVerEmpenho == 'EMPENHO SIAFI PENDENTE') {
                    $btnVerEmpenho = '';
                }
                $request->valor = number_format($request->valor, 3, ',', '.');
                $aprovGerencia = ($request->aprov_gerencia) ? new Small('label pull-right bg-gray', 'A', "data-toggle=\"tooltip\"", 'Aprovado pela Gerência') : '';

                $check_all = "
                <div class=\"form-group\">
                    <input type=\"checkbox\" name=\"checkPedRel\" id=\"checkPedRel" . $request->id . "\" value=\"" . $request->id . "\">
                </div>
                " . $aprovGerencia . "";

                $buttons = self::buildButtons($request->id, $request->status, $request->id_setor);

                $row = new Row('rowPedido' . $request->id);

                $row->addComponent(new Column($check_all));
                $row->addComponent(new Column($buttons));
                $row->addComponent(new Column($request->id));
                $row->addComponent(new Column(ARRAY_SETORES[$request->id_setor]));
                $row->addComponent(new Column($request->data_pedido));
                $row->addComponent(new Column(ARRAY_PRIORIDADE[$request->prioridade]));
                $row->addComponent(new Column(ARRAY_STATUS[$request->status]));
                $row->addComponent(new Column("R$ " . $request->valor));
                $row->addComponent(new Column($btnVerEmpenho));
                $row->addComponent(new Column(self::getFornecedor($request->id)));

                $table->addComponent($row);
            }
        }
        if ($table->isEmpty()) {
            return "";
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
     * Function to search teh informations of a request to be analysed.
     *
     * @param int $id_request
     * @return string
     */
    public static function getItensPedidoAnalise(int $id_request): string {
        $query = Query::getInstance()->exe('SELECT itens.qt_contrato, itens.id AS id_itens, itens_pedido.qtd AS qtd_solicitada, itens_pedido.valor, itens.nome_fornecedor, itens.num_licitacao, itens.dt_inicio, itens.dt_fim, itens.cod_reduzido, itens.complemento_item, itens.vl_unitario, itens.qt_saldo, itens.cod_despesa, itens.descr_despesa, itens.num_contrato, itens.num_processo, itens.descr_mod_compra, itens.num_licitacao, itens.cgc_fornecedor, itens.num_extrato, itens.descricao, itens.qt_contrato, itens.vl_contrato, itens.qt_utilizado, itens.vl_utilizado, itens.qt_saldo, itens.vl_saldo, itens.seq_item_processo FROM itens_pedido, itens WHERE itens_pedido.id_pedido = ' . $id_request . ' AND itens_pedido.id_item = itens.id ORDER BY itens.seq_item_processo ASC');

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

            $row->addComponent(new Column($btn_group));
            $row->addComponent(new Column($item->cod_despesa));
            $row->addComponent(new Column($item->descr_despesa));
            $row->addComponent(new Column($item->num_extrato));
            $row->addComponent(new Column($item->num_contrato));
            $row->addComponent(new Column($item->num_processo));
            $row->addComponent(new Column($item->descr_mod_compra));
            $row->addComponent(new Column($item->num_licitacao));
            $row->addComponent(new Column($item->dt_inicio));
            $row->addComponent(new Column(($item->dt_fim == '') ? '----------' : $item->dt_fim));
            $row->addComponent(new Column($item->cgc_fornecedor));
            $row->addComponent(new Column($item->nome_fornecedor));
            $row->addComponent(new Column($item->cod_reduzido));
            $row->addComponent(new Column($item->seq_item_processo));
            $row->addComponent(new Column($item->descricao));
            $row->addComponent(new Column('R$ ' . $item->vl_unitario));
            $row->addComponent(new Column($item->qt_contrato));
            $row->addComponent(new Column($item->vl_contrato));
            $row->addComponent(new Column($item->qt_utilizado));
            $row->addComponent(new Column($item->vl_utilizado));
            $row->addComponent(new Column($item->qt_saldo));
            $row->addComponent(new Column($item->vl_saldo));
            $row->addComponent(new Column($item->qtd_solicitada));
            $row->addComponent(new Column('R$ ' . $item->valor));
            $row->addComponent(new Column($inputs));

            $table->addComponent($row);
        }
        return $table;
    }

    /**
     * Function that returns a table with requests to change orders
     *
     * @return string
     */
    public static function getSolicAltPedidos(): string {
        $id_sector = $_SESSION['id_setor'];
        $query = Query::getInstance()->exe("SELECT solic_alt_pedido.id_pedido, DATE_FORMAT(solic_alt_pedido.data_solicitacao, '%d/%m/%Y') AS data_solicitacao, DATE_FORMAT(solic_alt_pedido.data_analise, '%d/%m/%Y') AS data_analise, solic_alt_pedido.justificativa, solic_alt_pedido.status, pedido.id_usuario FROM solic_alt_pedido, pedido WHERE pedido.id = solic_alt_pedido.id_pedido AND solic_alt_pedido.id_setor = " . $id_sector . ' ORDER BY solic_alt_pedido.id DESC LIMIT ' . LIMIT_MAX);
        $table = new Table('', '', [], false);

        $array_status = ['Reprovado', 'Aprovado', 'Aberto'];
        $array_lb = ['red', 'green', 'orange'];

        while ($request = $query->fetch_object()) {
            $status = $array_status[$request->status];
            $label = 'bg-' . $array_lb[$request->status];
            $request->justificativa = str_replace("\"", "\'", $request->justificativa);
            if ($request->id_usuario == $_SESSION['id']) {
                $row = new Row();
                $row->addComponent(new Column($request->id_pedido));
                $row->addComponent(new Column($request->data_solicitacao));
                $row->addComponent(new Column(($request->status == 2) ? '--------------' : $request->data_analise));
                $row->addComponent(new Column(new Button('', BTN_DEFAULT, "viewCompl('" . $request->justificativa . "')", "data-toggle=\"tooltip\"", 'Ver Justificativa', 'eye')));
                $row->addComponent(new Column(new Small('label ' . $label, $status)));

                $table->addComponent($row);
            }
        }
        return $table;
    }

    /**
     * Function that returns the advance requests of the logged in sector.
     *
     * @return string
     */
    public static function getSolicAdiSetor(): string {
        $id_sector = $_SESSION['id_setor'];
        $query = Query::getInstance()->exe("SELECT id, DATE_FORMAT(data_solicitacao, '%d/%m/%Y') AS data_solicitacao, DATE_FORMAT(data_analise, '%d/%m/%Y') AS data_analise, valor_adiantado, justificativa, status FROM saldos_adiantados WHERE id_setor = " . $id_sector . ' ORDER BY id DESC LIMIT ' . LIMIT_MAX);
        $array_status = ['Reprovado', 'Aprovado', 'Aberto'];
        $array_lb = ['red', 'green', 'orange'];

        $table = new Table('', '', [], false);
        while ($request = $query->fetch_object()) {
            $status = $array_status[$request->status];
            $label = 'bg-' . $array_lb[$request->status];
            $request->justificativa = str_replace("\"", "\'", $request->justificativa);
            $request->valor_adiantado = number_format($request->valor_adiantado, 3, ',', '.');

            $row = new Row();
            $row->addComponent(new Column($request->data_solicitacao));
            $row->addComponent(new Column(($request->status == 2) ? '--------------' : $request->data_analise));
            $row->addComponent(new Column($request->valor_adiantado));
            $row->addComponent(new Column(new Button('', BTN_DEFAULT, "viewCompl('" . $request->justificativa . "')", "data-toggle=\"tooltip\"", 'Ver Justificativa', 'eye')));
            $row->addComponent(new Column(new Small('label ' . $label, $status)));

            $table->addComponent($row);
        }
        return $table;
    }

    /**
     * Function to show the items of a searched process in requests menu.
     *
     * @param string $search
     * @return string
     */
    public static function getConteudoProcesso(string $search): string {
        $sql = "SELECT id, id_item_processo, nome_fornecedor, cod_reduzido, complemento_item, replace(vl_unitario, ',', '.') AS vl_unitario, qt_contrato, qt_utilizado, vl_utilizado, qt_saldo, vl_saldo FROM itens WHERE num_processo LIKE '%" . $search . "%'";

        if (!isset($_SESSION['editmode'])) {
            $sql .= " AND cancelado = 0";
        }
        $query = Query::getInstance()->exe($sql);
        $table = new Table('', '', [], false);
        while ($item = $query->fetch_object()) {
            $item->complemento_item = str_replace("\"", "\'", $item->complemento_item);
            $btn = (!isset($_SESSION['editmode'])) ? new Button('', BTN_DEFAULT, "checkItemPedido(" . $item->id . ", '" . $item->vl_unitario . "', " . $item->qt_saldo . ")", "data-toggle=\"tooltip\"", 'Adicionar', 'plus') : new Button('', BTN_DEFAULT, "editaItem(" . $item->id . ")", "data-toggle=\"tooltip\"", 'Editar Informações', 'pencil');
            $input_qtd = (!isset($_SESSION['editmode'])) ? "<input type=\"number\" id=\"qtd" . $item->id . "\" min=\"1\" max=\"" . $item->qt_saldo . "\">" : '';
            $row = new Row();
            $row->addComponent(new Column($btn));
            $row->addComponent(new Column($item->nome_fornecedor));
            $row->addComponent(new Column($item->cod_reduzido));
            if (!isset($_SESSION['editmode'])) {
                $row->addComponent(new Column($input_qtd));
            }
            $row->addComponent(new Column(new Button('', BTN_DEFAULT, "viewCompl('" . $item->complemento_item . "')", "data-toggle=\"tooltip\"", 'Ver Detalhes', 'eye')));
            $row->addComponent(new Column($item->complemento_item, 'none'));
            $row->addComponent(new Column($item->vl_unitario));
            $row->addComponent(new Column($item->qt_saldo));
            $row->addComponent(new Column($item->qt_utilizado));
            $row->addComponent(new Column($item->vl_saldo));
            $row->addComponent(new Column($item->vl_utilizado));
            $row->addComponent(new Column($item->qt_contrato));

            $table->addComponent($row);
        }
        return $table;
    }

    /**
     * Function to bring the item line attached to the order.
     *
     * @param int $id_item
     * @param int $qtd
     * @return string
     */
    public static function addItemPedido(int $id_item, int $qtd): string {
        $query = Query::getInstance()->exe("SELECT id, nome_fornecedor, num_processo, num_licitacao, cod_reduzido, complemento_item, replace(vl_unitario, ',', '.') AS vl_unitario, qt_saldo, qt_contrato, qt_utilizado, vl_saldo, vl_contrato, vl_utilizado FROM itens WHERE id = " . $id_item);
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

        $row->addComponent(new Column(new Button('', BTN_DEFAULT, "removeTableRow(" . $id_item . ", '" . $valor . "')", "data-toggle=\"tooltip\"", 'Remover do Pedido', 'trash')));
        $row->addComponent(new Column($item->num_processo));
        $row->addComponent(new Column($item->cod_reduzido));
        $row->addComponent(new Column(new Button('', BTN_DEFAULT, "viewCompl('" . $item->complemento_item . "')", "data-toggle=\"tooltip\"", 'Ver Complemento do Item', 'eye')));
        $row->addComponent(new Column('R$ ' . $item->vl_unitario));
        $row->addComponent(new Column($item->nome_fornecedor));
        $row->addComponent(new Column($item->num_licitacao));
        $row->addComponent(new Column($qtd));
        $row->addComponent(new Column('R$ ' . $valor));
        $row->addComponent(new Column($inputs));

        return $row;
    }

    private static final function buildButtonsDraft(int $id): string {
        $group = "<div class=\"btn-group\">";

        $btnEdit = $btnDel = '';
        $btnEdit = new Button('', BTN_DEFAULT . ' btn-sm', "editaPedido(" . $id . ")", "data-toggle=\"tooltip\"", 'Editar', 'pencil');

        $btnDel = new Button('', BTN_DEFAULT . ' btn-sm', "deletePedido(" . $id . ")", "data-toggle=\"tooltip\"", 'Excluir', 'trash');

        $btnPrint = new Button('', BTN_DEFAULT . ' btn-sm', "imprimir(" . $id . ")", "data-toggle=\"tooltip\"", 'Imprimir', 'print');

        $group .= $btnEdit . $btnPrint . $btnDel . '</div>';
        return $group;
    }

    /**
     * Function to return the drafts of the sector logged in.
     *
     * @return string
     */
    public static function getRascunhos(): string {
        $id_sector = $_SESSION['id_setor'];
        $query = Query::getInstance()->exe("SELECT id, DATE_FORMAT(data_pedido, '%d/%m/%Y') AS data_pedido, pedido.valor, status FROM pedido WHERE id_setor = " . $id_sector . ' AND alteracao = 1 ORDER BY id DESC LIMIT ' . LIMIT_MAX);

        $table = new Table('', '', [], false);
        while ($draft = $query->fetch_object()) {
            $draft->valor = number_format($draft->valor, 3, ',', '.');

            $row = new Row();
            $row->addComponent(new Column($draft->id));
            $row->addComponent(new Column(new Small('label bg-gray', ARRAY_STATUS[$draft->status])));
            $row->addComponent(new Column($draft->data_pedido));
            $row->addComponent(new Column('R$ ' . $draft->valor));
            $row->addComponent(new Column(self::buildButtonsDraft($draft->id)));

            $table->addComponent($row);
        }
        return $table;
    }

    /**
     * Function that returns the content of request for edition.
     *
     * @param int $id_request Request's id.
     * @return string Rows with items of $id_request param.
     */
    public static function getConteudoPedido(int $id_request): string {
        $return = '';
        $query = Query::getInstance()->exe('SELECT id_item, qtd FROM itens_pedido WHERE id_pedido = ' . $id_request);
        while ($item = $query->fetch_object()) {
            $return .= self::addItemPedido($item->id_item, $item->qtd);
        }
        return $return;
    }

    /**
     * Function that returns the request did by sector logged in.
     *
     * @param string $where
     * @param array $requests
     * @return string
     */
    public static function getMeusPedidos(string $where = '', array $requests = []): string {
        $id_setor = $_SESSION['id_setor'];
        $query = Query::getInstance()->exe("SELECT id, DATE_FORMAT(data_pedido, '%d/%m/%Y') AS data_pedido, prioridade, status, valor, id_usuario FROM pedido WHERE id_setor = " . $id_setor . ' AND alteracao = 0 ' . $where . ' ORDER BY id DESC LIMIT ' . LIMIT_MAX);

        $table = new Table('', '', [], false);
        while ($pedido = $query->fetch_object()) {
            if (!in_array($pedido->id, $requests)) {
                $empenho = Busca::verEmpenho($pedido->id);
                if ($empenho == 'EMPENHO SIAFI PENDENTE') {
                    $empenho = '';
                }
                $pedido->valor = number_format($pedido->valor, 3, ',', '.');

                $row = new Row('ped' . $pedido->id);

                $row->addComponent(new Column($pedido->id));
                $row->addComponent(new Column($pedido->data_pedido));
                $row->addComponent(new Column(ARRAY_PRIORIDADE[$pedido->prioridade]));
                $row->addComponent(new Column(new Small('label bg-gray', ARRAY_STATUS[$pedido->status])));
                $row->addComponent(new Column($empenho));
                $row->addComponent(new Column('R$ ' . $pedido->valor));
                $row->addComponent(new Column(self::getFornecedor($pedido->id)));
                $row->addComponent(new Column(self::buildButtonsMyRequests($pedido->id, $pedido->status, $pedido->id_usuario)));

                $table->addComponent($row);
            }
        }
        return $table;
    }

    private static final function buildButtonsMyRequests(int $id, int $status, int $id_user): string {
        $group = "<div class=\"btn-group\">";

        $btnSolicAlt = ($status == 2 || $status == 5 && $id_user == $_SESSION['id']) ? new Button('', BTN_DEFAULT . ' btn-sm', "solicAltPed(" . $id . ")", "data-toggle=\"tooltip\"", 'Solicitar Alteração', 'wrench') : '';

        $btnPrint = new Button('', BTN_DEFAULT . ' btn-sm', "imprimir(" . $id . ")", "data-toggle=\"tooltip\"", 'Imprimir', 'print');

        $group .= $btnSolicAlt . $btnPrint . '</div>';

        return $group;
    }

    /**
     * Build rows with process in database.
     *
     * @param string $screen If 'recepcao' - add process in tables used by reception, else - search items of process.
     * @return string Rows with all process.
     */
    public static function getProcessos(string $screen): string {
        $sql = 'SELECT DISTINCT num_processo FROM itens';
        $onclick = 'pesquisarProcesso';
        $title = 'Pesquisar Processo';
        $icon = 'search';
        if ($screen == 'recepcao') {
            $sql = 'SELECT DISTINCT num_processo FROM itens WHERE num_processo NOT IN (SELECT DISTINCT num_processo FROM processos)';
            $onclick = 'addProcesso';
            $title = 'Adicionar Processo';
            $icon = 'plus';
        }
        $query = Query::getInstance()->exe($sql);
        $table = new Table('', '', [], false);
        while ($process = $query->fetch_object()) {
            $row = new Row();
            $row->addComponent(new Column($process->num_processo));
            $row->addComponent(new Column(new Button('', 'btn btn-primary', $onclick . "('" . $process->num_processo . "', 0)", "data-toggle=\"tooltip\"", $title, $icon)));

            $table->addComponent($row);
        }
        return $table;
    }


    public static function getAllProcess(): array {
        $query = Query::getInstance()->exe("SELECT DISTINCT num_processo FROM itens WHERE num_processo;");

        $options = [];
        if ($query) {
            $i = 0;
            while ($obj = $query->fetch_object()) {
                $options[$i++] = $obj->num_processo;
            }
        }
        return $options;
    }

    public static function getOptionsProcessos(): string {
        $array = BuscaLTE::getAllProcess();
        $options = "";
        foreach ($array as $num_processo) {
            $options .= "<option value='" . $num_processo . "'>" . $num_processo . "</option>";
        }
        return $options;
    }

    private static function getSetorTransf(int $id_lancamento) {
        $query = Query::getInstance()->exe('SELECT id_setor, valor FROM saldos_lancamentos WHERE id = ' . $id_lancamento);
        $obj = $query->fetch_object();

        $id = ($obj->valor < 0) ? $id_lancamento + 1 : $id_lancamento - 1;

        $query_l = Query::getInstance()->exe("SELECT saldos_lancamentos.id_setor, setores.nome AS setor, saldos_lancamentos.valor FROM saldos_lancamentos, setores WHERE setores.id = saldos_lancamentos.id_setor AND saldos_lancamentos.id = " . $id);

        $lancamento = $query_l->fetch_object();
        return $lancamento->setor;
    }

    /**
     *    Função que retorna a tabela com os lançamentos de saldos pelo SOF
     *
     * @param int $id_sector Sector id.
     * @return string
     */
    public static function getLancamentos(int $id_sector): string {
        $where = ($id_sector != 0) ? ' WHERE id_setor = ' . $id_sector : '';

        $query = Query::getInstance()->exe("SELECT id, id_setor, DATE_FORMAT(data, '%d/%m/%Y') AS data, round(valor, 3) AS valor, categoria FROM saldos_lancamentos" . $where . ' ORDER BY id DESC LIMIT ' . LIMIT_MAX);

        $table = new Table('', '', [], false);
        while ($lancamento = $query->fetch_object()) {
            $cor = ($lancamento->valor < 0) ? 'red' : 'green';
            $setor_transf = ($lancamento->categoria == 3) ? self::getSetorTransf($lancamento->id) : '';

            $btn = ($_SESSION['id_setor'] == 2 && $lancamento->categoria != 4 && $lancamento->categoria != 2) ? new Button('', BTN_DEFAULT, "undoFreeMoney(" . $lancamento->id . ")", "data-toggle=\"tooltip\"", 'Desfazer', 'undo') : '';

            $db = "";
            if (isset($_SESSION['database'])) {
                $db = $_SESSION['database'];
            }

            if (!empty($db) && $db != 'main') {
                $btn = "";
            }
            $row = new Row();
            $row->addComponent(new Column($btn));
            $row->addComponent(new Column(ARRAY_SETORES[$lancamento->id_setor]));
            $row->addComponent(new Column($lancamento->data));
            $row->addComponent(new Column("<span style=\"color: " . $cor . ";\">" . 'R$ ' . $lancamento->valor . "</span>"));
            $row->addComponent(new Column(ARRAY_CATEGORIA[$lancamento->categoria]));
            $row->addComponent(new Column($setor_transf));

            $table->addComponent($row);
        }
        return $table;
    }

}
