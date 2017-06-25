<?php

/**
 * Class with the function used to print any information / report of system.
 *
 * @author João Bolsson (joaovictorbolsson@gmail.com).
 * @since 2017, 28 Jan.
 */
ini_set('display_errors', true);
error_reporting(E_ALL);

spl_autoload_register(function (string $class_name) {
    include_once $class_name . '.class.php';
});

require_once '../defines.php';

final class PrintMod {

    /**
     * Default constructor.
     */
    private function __construct() {
        // empty
    }

    /**
     * @param int $id_user User id.
     * @param string $period Period in the format DD/MM/YYYY - DD/MM/YYYY.
     * @return string User hours report.
     */
    public static function relatorioHora(int $id_user, string $period): string {
        $array = explode(' - ', $period);
        $dataI = Util::dateFormat($array[0]);
        $dataF = Util::dateFormat($array[1]);

        $obj_tot_rows = Query::getInstance()->exe("SELECT count(id) AS count FROM usuario_hora WHERE (entrada BETWEEN '" . $dataI . "' AND '" . $dataF . "') AND id_usuario = " . $id_user)->fetch_object();

        $tot_rows = $obj_tot_rows->count;

        $query = Query::getInstance()->exe("SELECT ip, DATE_FORMAT(entrada, '%d/%m/%Y %H:%i:%s') AS entrada, DATE_FORMAT(saida, '%d/%m/%Y %H:%i:%s') AS saida, horas FROM usuario_hora WHERE (entrada BETWEEN '" . $dataI . "' AND '" . $dataF . "') AND id_usuario = " . $id_user . " LIMIT " . LIMIT_HOURS_REPORT);

        $totHoras = number_format(Util::getTotHoras($id_user, $period), 3, ',', '.');
        $info = (Util::isCurrentLoggedIn($id_user)) ? '<h6>Usuário possui uma saída pendente, a última entrada não foi contabilizada no total.</h6>' : '';
        $return = "
            <fieldset class=\"preg\">
                    <h5>DESCRIÇÃO DO RELATÓRIO</h5>
                    <h6>Relatório de Horários: Usuário " . Util::getUserName($id_user) . "
                         | Período: " . $period . "</h6>
                    <h6>Total de Horas: " . $totHoras . "</h6>
                    " . $info . "
                    <h6>Mostrando " . $query->num_rows . " linhas de " . $tot_rows . " encontradas</h6>
                </fieldset><br>";

        $return .= "
            <fieldset class=\"preg\">
                    <h5>REGISTROS NO PONTO ELETRÔNICO</h5>
                </fieldset><br>";
        $return .= "<fieldset class=\"preg\">";

        $table = new Table('', 'prod', ['IP', 'Entrada', 'Saída', 'Horas'], true);
        while ($obj = $query->fetch_object()) {
            $row = new Row();
            $row->addColumn(new Column($obj->ip));
            $row->addColumn(new Column($obj->entrada));
            $obj->saida = ($obj->saida == NULL) ? '--------------------' : $obj->saida;
            $obj->horas = ($obj->horas == NULL) ? '--------------------' : number_format($obj->horas, 3, ',', '.');
            $row->addColumn(new Column($obj->saida));
            $row->addColumn(new Column($obj->horas));
            $table->addRow($row);
        }

        $atestados = self::buildTableAtestados($id_user, $dataI, $dataF);

        $return .= $table . '</fieldset>';

        $return .= "<br>
            <fieldset class=\"preg\">
                    <h5>ATESTADOS NO PERÍODO</h5>
                </fieldset><br>";
        $return .= "<fieldset class=\"preg\">" . $atestados . "</fieldset>";
        return $return;
    }

    public static function buildTableAtestados(int $id_user, string $dataI, string $dataF): Table {
        $table = new Table('', 'prod', ['Data da Falta', 'Horas Abonadas', 'Justificativa'], true);

        $query = Query::getInstance()->exe("SELECT DATE_FORMAT(dia, '%d/%m/%Y') AS data, horas, justificativa FROM usuario_atestados WHERE (dia BETWEEN '" . $dataI . "' AND '" . $dataF . "') AND id_usuario = " . $id_user);

        if ($query->num_rows) {
            while ($obj = $query->fetch_object()) {
                $row = new Row();
                $row->addColumn(new Column($obj->data));
                $row->addColumn(new Column($obj->horas));
                $row->addColumn(new Column($obj->justificativa));

                $table->addRow($row);
            }
        }

        return $table;
    }

    public static function getRelUsers(): string {
        $query = Query::getInstance()->exe('SELECT nome, login, id_setor, email FROM usuario ORDER BY nome ASC');
        $return = "
            <fieldset class=\"preg\">
                    <h5>DESCRIÇÃO DO RELATÓRIO</h5>
                    <h6>Relatório de Usuários Cadastrados no Sistema</h6>
                </fieldset><br>
            <fieldset>";

        $table = new Table('', 'prod', ['Nome', 'Login', 'Setor', 'E-mail'], true);

        while ($user = $query->fetch_object()) {
            $row = new Row();
            $row->addColumn(new Column($user->nome));
            $row->addColumn(new Column($user->login));
            $row->addColumn(new Column(ARRAY_SETORES[$user->id_setor]));
            $row->addColumn(new Column($user->email));

            $table->addRow($row);
        }

        $return .= $table . '</fieldset>';

        return $return;
    }

    /**
     * Function that returns request header to put in PDF document.
     *
     * @param int $id_request
     * @return string
     */
    public static function getHeader(int $id_request): string {
        $query = Query::getInstance()->exe("SELECT pedido.id, DATE_FORMAT(pedido.data_pedido, '%d/%m/%Y') AS data_pedido, EXTRACT(YEAR FROM pedido.data_pedido) AS ano, mes.sigla_mes AS ref_mes, status.nome AS status, pedido.valor AS valor, pedido.obs, pedido.pedido_contrato, prioridade.nome AS prioridade, pedido.aprov_gerencia, pedido.id_usuario FROM prioridade, pedido, mes, status WHERE pedido.prioridade = prioridade.id AND status.id = pedido.status AND pedido.id = " . $id_request . ' AND mes.id = pedido.ref_mes');
        $request = $query->fetch_object();
        $lblRequest = ($request->pedido_contrato) ? 'Pedido de Contrato' : 'Pedido';
        $request->valor = number_format($request->valor, 3, ',', '.');
        $return = "
            <fieldset>
                <table style=\"font-size: 8pt; margin: 5px;\">
                    <tr>
                        <td style=\"text-align: left;\"><b>" . $lblRequest . ":</b> " . $id_request . "</td>
                        <td><b>Data de Envio:</b> " . $request->data_pedido . "</td>
                        <td><b>Situação:</b> " . $request->status . "</td>
                        <td><b>Prioridade:</b> " . $request->prioridade . "</td>
                    </tr>
                </table>
                <p><b>Total do Pedido:</b> R$ " . $request->valor . "</p>
                <p><b>Autor:</b> " . Util::getUserName($request->id_usuario) . "</p>
                <table style=\"font-size: 8pt; margin: 5px;\">
                    <tr>
                        <td style=\"text-align: left;\">" . self::getGrupoPedido($id_request) . "</td>
                        <td style=\"text-align: right;\">" . self::getEmpenho($id_request) . "</td>
                    </tr>
                </table>";
        $return .= ($request->aprov_gerencia) ? '<p><b>Aprovado Pela Gerência</b></p>' : '';
        $return .= "<p><b>Observação da Unidade Solicitante: </b></p>
                <p style=\"font-weight: normal !important;\">	" . $request->obs . "</p>
            </fieldset><br>";
        $return .= self::getTableFontes($id_request);
        $return .= self::getTableLicitacao($id_request);
        return $return;
    }

    private static function getTableLicitacao(int $id_request): string {
        $return = "<fieldset><h5>PEDIDO SEM LICITAÇÃO</h5></fieldset><br>";

        $query = Query::getInstance()->exe("SELECT licitacao.tipo AS id_tipo, licitacao_tipo.nome AS tipo, licitacao.numero, licitacao.uasg, licitacao.processo_original, licitacao.gera_contrato FROM licitacao, licitacao_tipo WHERE licitacao_tipo.id = licitacao.tipo AND licitacao.id_pedido = " . $id_request . ' LIMIT 1');
        if ($query->num_rows > 0) {
            $obj = $query->fetch_object();

            $header = ['Tipo de Licitação', 'Número'];
            if ($obj->id_tipo == 3 || $obj->id_tipo == 4 || $obj->id_tipo == 2) {
                $i = count($header);
                $header[$i] = 'UASG';
                $header[$i + 1] = 'Processo Original';
                $header[$i + 2] = 'Contrato';
            }
            $table = new Table('', '', $header, true);
            $row = new Row();
            $row->addColumn(new Column($obj->tipo));
            $row->addColumn(new Column($obj->numero));

            if (count($header) > 2) {
                $generate = ($obj->gera_contrato == 0) ? 'Não Gera Contrato' : 'Gera Contrato';
                $row->addColumn(new Column($obj->uasg));
                $row->addColumn(new Column($obj->processo_original));
                $row->addColumn(new Column($generate));
            }

            $table->addRow($row);
            $return = "<fieldset class=\"preg\">" . $table . '</fieldset><br>';
        }

        return $return;
    }

    /**
     * Function to returns the resource sources of a request.
     *
     * @param int $id_request Request id.
     * @return string Resource source.
     */
    private static function getTableFontes(int $id_request): string {
        $query = Query::getInstance()->exe('SELECT fonte_recurso, ptres, plano_interno FROM pedido_fonte WHERE id_pedido = ' . $id_request);
        if ($query->num_rows > 0) {
            $source = $query->fetch_object();
            $table = self::buildSourcesTable($source);

            return "<fieldset class = \"preg\">" . $table . '</fieldset><br>';
        } else {
            $query_f = Query::getInstance()->exe("SELECT saldo_fonte.id, saldo_fonte.fonte_recurso, saldo_fonte.ptres, saldo_fonte.plano_interno FROM saldo_fonte, pedido_id_fonte WHERE saldo_fonte.id = pedido_id_fonte.id_fonte AND pedido_id_fonte.id_pedido = " . $id_request);
            if ($query_f->num_rows > 0) {
                $source = $query_f->fetch_object();
                $table = self::buildSourcesTable($source);

                return "<fieldset class = \"preg\">" . $table . '</fieldset><br>';
            }
        }
        return '<fieldset><h5>PEDIDO AGUARDA FONTE DE RECURSO</h5></fieldset><br>';
    }

    private static function buildSourcesTable(stdClass $source): Table {
        $table = new Table('', '', ['Fonte de Recurso', 'PTRES', 'Plano Interno'], true);
        $row = new Row();
        $row->addColumn(new Column($source->fonte_recurso));
        $row->addColumn(new Column($source->ptres));
        $row->addColumn(new Column($source->plano_interno));

        $table->addRow($row);
        return $table;
    }

    private static function getEmpenho(int $id_request): string {
        $query = Query::getInstance()->exe('SELECT contrato_tipo.nome, pedido_contrato.siafi FROM contrato_tipo, pedido_contrato WHERE pedido_contrato.id_tipo = contrato_tipo.id AND pedido_contrato.id_pedido = ' . $id_request);
        $return = '';
        if ($query->num_rows > 0) {
            $obj = $query->fetch_object();
            $return = '<b>Tipo:</b> ' . $obj->nome . " <input type=\"text\" value=\"" . $obj->siafi . "\"/>";
        }
        return $return;
    }

    private static function getGrupoPedido(int $id_request): string {
        $query = Query::getInstance()->exe('SELECT setores_grupos.nome, pedido_grupo.id_pedido FROM setores_grupos, pedido_grupo WHERE pedido_grupo.id_grupo = setores_grupos.id AND pedido_grupo.id_pedido = ' . $id_request);
        if ($query->num_rows > 0) {
            $obj = $query->fetch_object();
            $obj->nome = utf8_encode($obj->nome);
            return '<b>Grupo:</b> ' . $obj->nome;
        }
        return '';
    }

    /**
     * Function to returns the request content (items)
     *
     * @param int $id_request Request id.
     * @return string Request content.
     */
    public static function getContentPedido(int $id_request): string {
        $return = '';
        // PRIMEIRO FAZEMOS O CABEÇALHO REFERENTE AO NUM_LICITACAO
        $query_ini = Query::getInstance()->exe('SELECT DISTINCT itens.num_licitacao, itens.num_processo, itens.dt_inicio, itens.dt_fim FROM itens_pedido, itens WHERE itens.id = itens_pedido.id_item AND itens_pedido.id_pedido = ' . $id_request);
        while ($bidding = $query_ini->fetch_object()) {
            $row = new Row();
            $row->addColumn(new Column('Licitação: ' . $bidding->num_licitacao));
            $row->addColumn(new Column('Processo: ' . $bidding->num_processo));
            $row->addColumn(new Column('Início: ' . $bidding->dt_inicio));
            $row->addColumn(new Column('Fim: ' . ($bidding->dt_fim == '') ? '------------' : $bidding->dt_fim));
            $return .= "
                <fieldset class=\"preg\">
                    <table>" . $row . "</table>
                </fieldset><br>";
            $query_forn = Query::getInstance()->exe('SELECT DISTINCT itens.cgc_fornecedor, itens.nome_fornecedor, itens.num_contrato FROM itens, itens_pedido WHERE itens.id = itens_pedido.id_item AND itens_pedido.id_pedido = ' . $id_request . ' AND itens.num_licitacao = ' . $bidding->num_licitacao);

            // --------------------------------------------------------
            //                FORNECEDORES REFERENTES À LICITAÇÃO
            // --------------------------------------------------------
            while ($provider = $query_forn->fetch_object()) {
                $provider->nome_fornecedor = substr($provider->nome_fornecedor, 0, 40);
                $provider->nome_fornecedor = strtoupper($provider->nome_fornecedor);
                $provider->nome_fornecedor = utf8_encode($provider->nome_fornecedor);
                $return .= "
                    <fieldset style=\"border-bottom: 1px solid black; padding: 5px;\">
                        <table>
                            <tr>
                                <td style=\"text-align: left; font-weight: bold;\">" . $provider->nome_fornecedor . "</td>
                                <td>CNPJ: " . $provider->cgc_fornecedor . "</td>
                                <td>Contrato: " . $provider->num_contrato . "</td>
                            </tr>
                        </table>
                    </fieldset>";
                // ----------------------------------------------------------------------
                //                  ITENS REFERENTES AOS FORNECEDORES
                // ----------------------------------------------------------------------
                $query_items = Query::getInstance()->exe("SELECT itens.cod_reduzido, itens.cod_despesa, itens.seq_item_processo, itens.complemento_item, itens.vl_unitario, itens_pedido.qtd, itens_pedido.valor FROM itens, itens_pedido WHERE itens.id = itens_pedido.id_item AND itens_pedido.id_pedido = " . $id_request . " AND itens.cgc_fornecedor = '" . $provider->cgc_fornecedor . "' ORDER BY CAST(itens.seq_item_processo AS UNSIGNED) ASC");

                $table = new Table('', 'prod', ['Código', 'Item', 'Natureza', 'Descrição', 'Quantidade', 'Valor Unitário', 'Valor Total'], true);
                while ($item = $query_items->fetch_object()) {
                    $item->complemento_item = mb_strtoupper($item->complemento_item, 'UTF-8');
                    $item->valor = number_format($item->valor, 3, ',', '.');
                    $row = new Row();
                    $row->addColumn(new Column($item->cod_reduzido));
                    $row->addColumn(new Column($item->seq_item_processo));
                    $row->addColumn(new Column($item->cod_despesa));
                    $row->addColumn(new Column($item->complemento_item));
                    $row->addColumn(new Column($item->qtd));
                    $row->addColumn(new Column('R$ ' . $item->vl_unitario));
                    $row->addColumn(new Column('R$ ' . $item->valor));

                    $table->addRow($row);
                }
                $return .= $table . '<br>';
            }
        }

        return $return;
    }

    /**
     * Function to returns the request comments.
     *
     * @param $id_request int Request id.
     * @return string SOF comments in request.
     */
    public static function getComentarios(int $id_request): string {
        $return = "";
        $query_emp = Query::getInstance()->exe("SELECT pedido_empenho.empenho, DATE_FORMAT(pedido_empenho.data, '%d/%m/%Y') AS data FROM pedido_empenho WHERE pedido_empenho.id_pedido = " . $id_request);
        if ($query_emp->num_rows > 0) {
            $effort = $query_emp->fetch_object();
            $row = new Row();
            $row->addColumn(new Column('Data Empenho: ' . $effort->data));
            $row->addColumn(new Column('Empenho: ' . $effort->empenho));

            $return = "<fieldset class=\"preg\"><table>" . $row . "</table></fieldset>";
        } else {
            $row = new Row();
            $row->addColumn(new Column('Empenho: EMPENHO SIAFI PENDENTE'));
            $return = "<fieldset class=\"preg\"><table>" . $row . "</table></fieldset>";
        }

        $query = Query::getInstance()->exe("SELECT DATE_FORMAT(comentarios.data_coment, '%d/%m/%Y') AS data_coment, comentarios.comentario FROM comentarios, prioridade WHERE prioridade.id = comentarios.prioridade AND comentarios.id_pedido = " . $id_request);
        if ($query->num_rows > 0) {
            while ($comment = $query->fetch_object()) {
                $return .= "
                    <fieldset>
                        <p style=\"font-weight: normal;\"> <b>Comentário [" . $comment->data_coment . "]:</b> " . $comment->comentario . "</p>
                    </fieldset>";
            }
        } else {
            $return .= "Sem comentários";
        }
        return $return;
    }

    /**
     * Function to returns the sector id of the request.
     *
     * @param int $id_request Request id.
     * @return int Sector id.
     */
    public static function getSetorPedido(int $id_request): int {
        $query = Query::getInstance()->exe('SELECT id_setor FROM pedido WHERE id = ' . $id_request);
        $obj = $query->fetch_object();
        return $obj->id_setor;
    }

    /**
     * Function to make a specific requests report did by the user of SOF.
     *
     * @param array $requests Selected requests by the user.
     * @param int $status Requests status (default = 8)
     * @return string Custom report.
     */
    public static function getRelPed(array $requests, int $status = 8) {
        if (empty($requests)) {
            return '';
        }
        $return = '';
        $where_status = ($status != 0) ? 'AND pedido.status = ' . $status : '';
        $effort = $tb_effort = $where_effort = "";
        if ($status == 8) {
            $where_effort = "AND pedido_empenho.id_pedido = pedido.id";
            $tb_effort = "pedido_empenho, ";
            $effort = ", pedido_empenho.empenho";
        }

        $where_request = '(';
        $len = count($requests);
        for ($i = 0; $i < $len; $i++) {
            $where_request .= 'pedido.id = ' . $requests[$i];
            if ($i != $len - 1) {
                $where_request .= ' OR ';
            }
        }
        $where_request .= ')';

        $query = Query::getInstance()->exe("SELECT pedido.id, setores.nome AS setor, DATE_FORMAT(pedido.data_pedido, '%d/%m/%Y') AS data_pedido, prioridade.nome AS prioridade, status.id AS id_status, status.nome AS status, pedido.valor " . $effort . ' FROM ' . $tb_effort . ' setores, pedido, prioridade, status WHERE status.id = pedido.status ' . $where_effort . ' AND prioridade.id = pedido.prioridade AND pedido.id_setor = setores.id ' . $where_status . ' AND ' . $where_request . ' ORDER BY pedido.id ASC');

        $title = ($status == 8) ? 'Relatório de Empenhos Enviados ao Ordenador' : 'Relatório de Pedidos por Setor e Nível de Prioridade';

        $headers = ['Pedido', 'Fornecedor', 'Setor'];
        $count = count($headers);

        if ($status == 8) {
            $headers[$count] = 'Prioridade';
            $headers[$count + 1] = 'SIAFI';
        } else {
            $headers[$count] = 'Enviado em';
            $headers[$count + 1] = 'Prioridade';
            $headers[$count + 2] = 'Status';
            $headers[$count + 3] = 'Valor';
        }
        $query_tot = Query::getInstance()->exe('SELECT sum(pedido.valor) AS total FROM ' . $tb_effort . ' pedido WHERE 1 > 0 ' . $where_effort . ' AND pedido.alteracao = 0 ' . $where_status . ' AND ' . $where_request);
        $tot = $query_tot->fetch_object();
        $total = ($tot->total > 0) ? 'R$ ' . number_format($tot->total, 3, ',', '.') : 'R$ 0';
        $return .= "<fieldset class=\"preg\">
                    <h5>DESCRIÇÃO DO RELATÓRIO</h5>
                    <h6>" . $title . "</h6>
                    <h6>Pedidos selecionados pelo SOF</h6></fieldset><br>";

        $row = new Row();
        $row->addColumn(new Column(count($requests) . ' selecionados'));
        $row->addColumn(new Column('Totalizando ' . $total));

        $sub_header = "<fieldset class=\"preg\"><table>" . $row . "</table></fieldset>";

        $table = new Table('', 'prod', $headers, true);

        $flag = false;
        $i = 0;
        while ($request = $query->fetch_object()) {
            if ($request->id_status != 8) {
                $flag = true;
                break;
            } else {
                $i++;
            }

            $row = new Row();
            $row->addColumn(new Column($request->id));
            $row->addColumn(new Column(BuscaLTE::getFornecedor($request->id)));
            $row->addColumn(new Column($request->setor));

            if ($status == 8) {
                $row->addColumn(new Column($request->prioridade));
                $row->addColumn(new Column($request->empenho));
            } else {
                $row->addColumn(new Column($request->data_pedido));
                $row->addColumn(new Column($request->prioridade));
                $row->addColumn(new Column($request->status));
                $row->addColumn(new Column('R$ ' . $request->valor));
            }

            $table->addRow($row);
        }
        if ($query->num_rows > 0 && !$flag && $i == count($requests)) {
            $return .= $sub_header . $table;
        } else {
            $return .= 'RELATÓRIO INVÁLIDO: Essa versão suporta apenas pedidos com status de Enviado ao Ordenador.';
            $flag = true;
        }

        if ($status == 8 && !$flag && $i == count($requests)) {
            $return .= Controller::footerOrdenator();
        }
        return $return;
    }

    public static function getRelatorioLib(int $id_sector, array $category, string $dateI, string $dateF): string {
        $return = "<fieldset class=\"preg\">
                <h5>DESCRIÇÃO DO RELATÓRIO</h5>
                <h6>Relatório de Liberações Orçamentárias</h6>
                <h6>Período de Emissão: " . $dateI . " à " . $dateF . "</h6></fieldset><br>";

        $where_sector = ($id_sector != 0) ? 'AND id_setor = ' . $id_sector : '';
        $where_category = ' AND (';

        $len = count($category);
        for ($i = 0; $i < $len; $i++) {
            $where_category .= 'categoria = ' . $category[$i];
            if ($i != $len - 1) {
                $where_category .= ' OR ';
            }
        }
        $where_category .= ')';
        $dataIni = Util::dateFormat($dateI);
        $dataFim = Util::dateFormat($dateF);

        $query = Query::getInstance()->exe("SELECT id_setor, DATE_FORMAT(data, '%d/%m/%Y') AS data, valor, categoria FROM saldos_lancamentos WHERE data BETWEEN '" . $dataIni . "' AND '" . $dataFim . "' " . $where_sector . $where_category . ' ORDER BY id ASC');

        $table = new Table('', 'prod', ['Setor', 'Data', 'Valor', 'Categoria'], true);
        while ($obj = $query->fetch_object()) {
            $row = new Row();
            $row->addColumn(new Column(ARRAY_SETORES[$obj->id_setor]));
            $row->addColumn(new Column($obj->data));
            $row->addColumn(new Column('R$ ' . number_format($obj->valor, 3, ',', '.')));
            $row->addColumn(new Column(ucfirst(ARRAY_CATEGORIA[$obj->categoria])));

            $table->addRow($row);
        }

        $return .= $table;
        return $return;
    }

    /**
     * Function that returns the requests report.
     *
     * @param int $id_sector Sector id.
     * @param array $priority Array with priorities.
     * @param array $status Array with status.
     * @param string $dateI Initial date of report.
     * @param string $dateF Final date of report.
     * @param bool $checkSIAFI Request contains SIAFI.
     * @return string Content of PDF document
     */
    public static function getRelatorioPedidos(int $id_sector, array $priority, array $status, string $dateI, string $dateF, bool $checkSIAFI): string {
        $return = $where_status = $where_priority = '';
        $where_sector = ($id_sector != 0) ? 'AND pedido.id_setor = ' . $id_sector : '';

        if (!in_array(0, $status)) {
            $len = count($status);
            $where_status = "AND (";
            for ($i = 0; $i < $len; $i++) {
                $where_status .= "pedido.status = " . $status[$i];
                if ($i < $len - 1) {
                    $where_status .= " OR ";
                }
            }
            $where_status .= ") ";
        }

        if (!in_array(0, $priority)) {
            $len = count($priority);
            $where_priority = "AND (";
            for ($i = 0; $i < $len; $i++) {
                $where_priority .= "pedido.prioridade = " . $priority[$i];
                if ($i < $len - 1) {
                    $where_priority .= " OR ";
                }
            }
            $where_priority .= ") ";
        }
        $dataIni = Util::dateFormat($dateI);
        $dataFim = Util::dateFormat($dateF);
        $where_effort = $tb_effort = $effort = "";
        if (in_array(8, $status) || $checkSIAFI) {
            $where_effort = "AND pedido_empenho.id_pedido = pedido.id";
            $tb_effort = "pedido_empenho, ";
            $effort = ", pedido_empenho.empenho";
        }

        // retorna o total de pedidos com os parâmetros (LIMIT para mostrar)
        $obj_count = Query::getInstance()->exe("SELECT COUNT(pedido.id) AS total FROM {$tb_effort} setores, pedido, prioridade, status WHERE status.id = pedido.status {$where_sector} {$where_priority} {$where_effort} AND prioridade.id = pedido.prioridade AND pedido.id_setor = setores.id {$where_status} AND pedido.data_pedido BETWEEN '{$dataIni}' AND '{$dataFim}'")->fetch_object();
        $num_rows = $obj_count->total;

        $query = Query::getInstance()->exe("SELECT pedido.id, pedido.id_setor, setores.nome AS setor, DATE_FORMAT(pedido.data_pedido, '%d/%m/%Y') AS data_pedido, prioridade.nome AS prioridade, status.nome AS status, pedido.valor {$effort} FROM {$tb_effort} setores, pedido, prioridade, status WHERE status.id = pedido.status {$where_sector} {$where_priority} {$where_effort} AND prioridade.id = pedido.prioridade AND pedido.id_setor = setores.id {$where_status} AND pedido.data_pedido BETWEEN '{$dataIni}' AND '{$dataFim}' ORDER BY pedido.id DESC LIMIT " . LIMIT_REQ_REPORT);

        $title = 'Relatório de Pedidos por Setor e Nível de Prioridade';
        $headers = ['Enviado em', 'Prioridade', 'Status', 'Valor'];
        if (in_array(8, $status)) {
            $title = 'Relatório de Empenhos Enviados ao Ordenador';
            $headers = ['Prioridade', 'SIAFI'];
        }
        $query_tot = Query::getInstance()->exe("SELECT sum(pedido.valor) AS total FROM {$tb_effort} pedido WHERE 1 > 0 {$where_sector} {$where_priority} {$where_effort} AND pedido.alteracao = 0 {$where_status} AND pedido.data_pedido BETWEEN '{$dataIni}' AND '{$dataFim}';");
        $tot = $query_tot->fetch_object();
        $total = ($tot->total > 0) ? 'R$ ' . number_format($tot->total, 3, ',', '.') : 'R$ 0';

        $row = new Row();
        $row->addColumn(new Column($num_rows . ' resultados encontrados'));
        $row->addColumn(new Column('Mostrando ' . $query->num_rows));
        $row->addColumn(new Column('Totalizando ' . $total));
        $return .= "
                <fieldset class=\"preg\">
                    <h5>DESCRIÇÃO DO RELATÓRIO</h5>
                    <h6>" . $title . "</h6>
                    <h6>Período de Emissão: " . $dateI . " à " . $dateF . "</h6>
                </fieldset><br>
                <fieldset class=\"preg\">
                    <table>" . $row . "</table>
                </fieldset>";
        $th = ['Pedido', 'Fornecedor', 'Setor'];
        $k = 3;
        $len = count($headers);
        for ($i = 0; $i < $len; $i++) {
            $th[$k] = $headers[$i];
            $k++;
        }

        $array_sub_totals = [];
        $table = new Table('', 'prod', $th, true);
        while ($request = $query->fetch_object()) {
            if (!array_key_exists($request->id_setor, $array_sub_totals)) {
                $array_sub_totals[$request->id_setor] = 0;
            }
            $array_sub_totals[$request->id_setor] += $request->valor;

            $row = new Row();
            $row->addColumn(new Column($request->id));
            $row->addColumn(new Column(BuscaLTE::getFornecedor($request->id)));
            $row->addColumn(new Column($request->setor));
            if (in_array(8, $status)) {
                $row->addColumn(new Column($request->prioridade));
                $row->addColumn(new Column($request->empenho));
            } else {
                $row->addColumn(new Column($request->data_pedido));
                $row->addColumn(new Column($request->prioridade));
                $row->addColumn(new Column($request->status));
                $row->addColumn(new Column('R$ ' . $request->valor));
            }

            $table->addRow($row);
        }
        $return .= $table;

        if ($_SESSION['id_setor'] == 2) {
            $return .= "<br><h5>As porcentagens mostradas são em relação ao Total (pag. 1) deste Relatório.</h5>
                <fieldset class=\"preg\"><h5>SUBTOTAIS POR SETOR</h5>";

            $len = count(ARRAY_SETORES);

            $table = new Table('', 'prod', ['Setor', 'Total', 'Porcentagem'], true);
            for ($k = 0; $k < $len; $k++) {
                if (array_key_exists($k, $array_sub_totals)) {
                    $parcial = number_format($array_sub_totals[$k], 3, ',', '.');
                    $porcentagem = number_format(($array_sub_totals[$k] * 100) / $tot->total, 3, ',', '.');

                    $row = new Row();
                    $row->addColumn(new Column(ARRAY_SETORES[$k]));
                    $row->addColumn(new Column($parcial));
                    $row->addColumn(new Column($porcentagem . '%'));

                    $table->addRow($row);
                }
            }
            $return .= $table . '</fieldset><br>' . "<fieldset class=\"preg\"><h5>SUBTOTAIS POR GRUPO</h5>";

            $query_gr = Query::getInstance()->exe("SELECT pedido_grupo.id_grupo, setores_grupos.nome AS ng, pedido.valor {$effort} FROM {$tb_effort} setores, setores_grupos, pedido, prioridade, status, pedido_grupo WHERE setores_grupos.id = pedido_grupo.id_grupo AND pedido_grupo.id_pedido = pedido.id AND status.id = pedido.status {$where_sector} {$where_priority} {$where_effort} AND prioridade.id = pedido.prioridade AND pedido.id_setor = setores.id {$where_status} AND pedido.data_pedido BETWEEN '{$dataIni}' AND '{$dataFim}'");

            $array_gr = []; // guarda o somatorio do grupo
            $gr_indexes = []; // guarda os indices do array de cima
            $gr_names = []; // guarda o nome dos grupos
            $k = 0;
            while ($obj = $query_gr->fetch_object()) {
                $index = 'gr' . $obj->id_grupo;
                if (!array_key_exists($index, $array_gr)) {
                    $array_gr[$index] = 0;
                    $gr_indexes[$k] = $index;
                    $gr_names[$index] = $obj->ng;
                    $k++;
                }
                $array_gr[$index] += $obj->valor;
            }

            $count = count($gr_indexes);

            $table_gr = new Table('', 'prod', ['Grupo', 'Total', 'Porcentagem'], true);
            for ($i = 0; $i < $count; $i++) {
                $parcial = number_format($array_gr[$gr_indexes[$i]], 3, ',', '.');
                $porcentagem = number_format(($array_gr[$gr_indexes[$i]] * 100) / $tot->total, 3, ',', '.');

                $row = new Row();
                $row->addColumn(new Column(utf8_encode($gr_names[$gr_indexes[$i]])));
                $row->addColumn(new Column($parcial));
                $row->addColumn(new Column($porcentagem . '%'));

                $table_gr->addRow($row);
            }

            $return .= $table_gr . '</fieldset><br>';
        }

        if (in_array(8, $status)) {
            $return .= Controller::footerOrdenator();
        }
        return $return;
    }

    /**
     * @param int $type Process type.
     * @return string Report with the process.
     */
    public static function getRelatorioProcessos(int $type): string {
        $return = '';
        $where = ($type != 0) ? ' WHERE id = ' . $type : '';

        $query_proc = Query::getInstance()->exe('SELECT id, nome FROM processos_tipo' . $where);
        while ($tipo_proc = $query_proc->fetch_object()) {
            $query = Query::getInstance()->exe('SELECT processos.num_processo, processos_tipo.nome AS tipo, processos_tipo.id AS id_tipo, processos.estante, processos.prateleira, processos.entrada, processos.saida, processos.responsavel, processos.retorno, processos.obs FROM processos, processos_tipo WHERE processos.tipo = processos_tipo.id AND processos.tipo = ' . $tipo_proc->id . ' ORDER BY processos.tipo ASC');
            if ($query->num_rows > 0) {
                $row = new Row();
                $row->addColumn(new Column('Tipo: ' . $tipo_proc->nome));
                $return .= "<fieldset class=\"preg\"><table>" . $row . "</table></fieldset><br>";
                $table = new Table('', 'prod', ['Processo', 'Tipo', 'Estante', 'Prateleira', 'Entrada', 'Saída', 'Responsável', 'Retorno', 'Obs'], true);
                while ($processo = $query->fetch_object()) {
                    $row = new Row();
                    $row->addColumn(new Column($processo->num_processo));
                    $row->addColumn(new Column($processo->tipo));
                    $row->addColumn(new Column($processo->estante));
                    $row->addColumn(new Column($processo->prateleira));
                    $row->addColumn(new Column($processo->entrada));
                    $row->addColumn(new Column($processo->saida));
                    $row->addColumn(new Column($processo->responsavel));
                    $row->addColumn(new Column($processo->retorno));
                    $row->addColumn(new Column($processo->obs));

                    $table->addRow($row);
                }
                $return .= $table . '<br>';
            }
        }
        $query_all = Query::getInstance()->exe('SELECT DISTINCT num_processo FROM itens WHERE num_processo NOT IN (SELECT DISTINCT num_processo FROM processos)');
        if ($query_all->num_rows > 0) {
            $return .= "<fieldset class=\"preg\"><h5>Processos Não Cadastrados</h5></fieldset><br>";
            $table = new Table('', 'prod', ['Processo', 'Tipo', 'Estante', 'Prateleira'], true);
            while ($processo = $query_all->fetch_object()) {
                $row = new Row();
                $row->addColumn(new Column($processo->num_processo));
                $row->addColumn(new Column('______________________'));
                $row->addColumn(new Column('______________________'));
                $row->addColumn(new Column('______________________'));

                $table->addRow($row);
            }
            $return .= $table;
        }

        return $return;
    }

}
