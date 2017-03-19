<?php

/**
 *  Classe com as funções de busca para impressão do sistema.
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

    private static $INSTANCE;

    public static function getInstance(): PrintMod {
        if (empty(self::$INSTANCE)) {
            self::$INSTANCE = new PrintMod();
        }
        return self::$INSTANCE;
    }

    private function __construct() {
        // empty
    }

    /**
     * @param int $id_usuario Id do usuario
     * @param string $periodo Período no formato 03/03/2017 - 05/04/2017
     * @return string Relatório dos registros do usuário.
     */
    public function relatorioHora(int $id_usuario, string $periodo): string {
        $array = explode(' - ', $periodo);
        $dataI = Util::getInstance()->dateFormat($array[0]);
        $dataF = Util::getInstance()->dateFormat($array[1]);

        $query = Query::getInstance()->exe("SELECT ip, DATE_FORMAT(entrada, '%d/%m/%Y %H:%i:%s') AS entrada, DATE_FORMAT(saida, '%d/%m/%Y %H:%i:%s') AS saida, horas FROM usuario_hora WHERE (entrada BETWEEN '" . $dataI . "' AND '" . $dataF . "') AND id_usuario = " . $id_usuario);

        $totHoras = number_format(Util::getInstance()->getTotHoras($id_usuario, $periodo), 3, ',', '.');
        $info = (Util::getInstance()->isCurrentLoggedIn($id_usuario)) ? '<h6>Usuário possui uma saída pendente, a última entrada não foi contabilizada no total.</h6>' : '';
        $retorno = "
            <fieldset class=\"preg\">
                    <h5>DESCRIÇÃO DO RELATÓRIO</h5>
                    <h6>Relatório de Horários: Usuário " . Util::getInstance()->getUserName($id_usuario) . "
                         | Período: " . $periodo . "</h6>
                    <h6>Total de Horas: " . $totHoras . "</h6>
                    " . $info . "
                </fieldset><br>";

        $retorno .= "<fieldset class=\"preg\">";

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

        $retorno .= $table . '</fieldset>';
        return $retorno;
    }

    public function getRelUsers(): string {
        $query = Query::getInstance()->exe('SELECT nome, login, id_setor, email FROM usuario ORDER BY nome ASC');
        $retorno = "
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

        $retorno .= $table . '</fieldset>';

        return $retorno;
    }

    /**
     * Function that returns request header to put in PDF document.
     *
     * @param int $id_pedido
     * @return string
     */
    public function getHeader(int $id_pedido): string {
        $query = Query::getInstance()->exe("SELECT pedido.id, DATE_FORMAT(pedido.data_pedido, '%d/%m/%Y') AS data_pedido, EXTRACT(YEAR FROM pedido.data_pedido) AS ano, mes.sigla_mes AS ref_mes, status.nome AS status, pedido.valor AS valor, pedido.obs, pedido.pedido_contrato, prioridade.nome AS prioridade, pedido.aprov_gerencia, pedido.id_usuario FROM prioridade, pedido, mes, status WHERE pedido.prioridade = prioridade.id AND status.id = pedido.status AND pedido.id = " . $id_pedido . ' AND mes.id = pedido.ref_mes');
        $pedido = $query->fetch_object();
        $lblPedido = ($pedido->pedido_contrato) ? 'Pedido de Contrato' : 'Pedido';
        $pedido->valor = number_format($pedido->valor, 3, ',', '.');
        $retorno = "
            <fieldset>
                <table style=\"font-size: 8pt; margin: 5px;\">
                    <tr>
                        <td style=\"text-align: left;\"><b>" . $lblPedido . ":</b> " . $id_pedido . "</td>
                        <td><b>Data de Envio:</b> " . $pedido->data_pedido . "</td>
                        <td><b>Situação:</b> " . $pedido->status . "</td>
                        <td><b>Prioridade:</b> " . $pedido->prioridade . "</td>
                    </tr>
                </table>
                <p><b>Total do Pedido:</b> R$ " . $pedido->valor . "</p>
                <p><b>Autor:</b> " . Util::getInstance()->getUserName($pedido->id_usuario) . "</p>
                <table style=\"font-size: 8pt; margin: 5px;\">
                    <tr>
                        <td style=\"text-align: left;\">" . $this->getGrupoPedido($id_pedido) . "</td>
                        <td style=\"text-align: right;\">" . $this->getEmpenho($id_pedido) . "</td>
                    </tr>
                </table>";
        $retorno .= ($pedido->aprov_gerencia) ? '<p><b>Aprovado Pela Gerência</b></p>' : '';
        $retorno .= "<p><b>Observação da Unidade Solicitante: </b></p>
                <p style=\"font-weight: normal !important;\">	" . $pedido->obs . "</p>
            </fieldset><br>";
        $retorno .= $this->getTableFontes($id_pedido);
        $retorno .= $this->getTableLicitacao($id_pedido);
        return $retorno;
    }

    private function getTableLicitacao(int $id_pedido): string {
        $retorno = "<fieldset><h5>PEDIDO SEM LICITAÇÃO</h5></fieldset><br>";

        $query = Query::getInstance()->exe("SELECT licitacao.tipo AS id_tipo, licitacao_tipo.nome AS tipo, licitacao.numero, licitacao.uasg, licitacao.processo_original, licitacao.gera_contrato FROM licitacao, licitacao_tipo WHERE licitacao_tipo.id = licitacao.tipo AND licitacao.id_pedido = " . $id_pedido . ' LIMIT 1');
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
                $gera = ($obj->gera_contrato == 0) ? 'Não Gera Contrato' : 'Gera Contrato';
                $row->addColumn(new Column($obj->uasg));
                $row->addColumn(new Column($obj->processo_original));
                $row->addColumn(new Column($gera));
            }

            $table->addRow($row);
            $retorno = "<fieldset class=\"preg\">" . $table . '</fieldset><br>';
        }

        return $retorno;
    }

    /**
     *    Função para retornar as fontes de recurso de um pedido (impressão).
     *
     * @param $id_pedido int Id do pedido.
     * @return string Fontes de recurso.
     */
    public function getTableFontes(int $id_pedido): string {
        $retorno = '';
        $query = Query::getInstance()->exe('SELECT fonte_recurso, ptres, plano_interno FROM pedido_fonte WHERE id_pedido = ' . $id_pedido);
        if ($query->num_rows > 0) {
            $fonte = $query->fetch_object();
            $table = new Table('', '', ['Fonte de Recurso', 'PTRES', 'Plano Interno'], true);
            $row = new Row();
            $row->addColumn(new Column($fonte->fonte_recurso));
            $row->addColumn(new Column($fonte->ptres));
            $row->addColumn(new Column($fonte->plano_interno));

            $table->addRow($row);

            $retorno = "<fieldset class = \"preg\">" . $table . '</fieldset><br>';
        } else {
            $retorno = '<fieldset><h5>PEDIDO AGUARDA FONTE DE RECURSO</h5></fieldset><br>';
        }

        return $retorno;
    }

    private function getEmpenho(int $id_pedido): string {
        $query = Query::getInstance()->exe('SELECT contrato_tipo.nome, pedido_contrato.siafi FROM contrato_tipo, pedido_contrato WHERE pedido_contrato.id_tipo = contrato_tipo.id AND pedido_contrato.id_pedido = ' . $id_pedido);
        $retorno = '';
        if ($query->num_rows > 0) {
            $obj = $query->fetch_object();
            $retorno = '<b>Tipo:</b> ' . $obj->nome . " <input type=\"text\" value=\"" . $obj->siafi . "\"/>";
        }
        return $retorno;
    }

    private function getGrupoPedido(int $id_pedido): string {
        $query = Query::getInstance()->exe('SELECT setores_grupos.nome, pedido_grupo.id_pedido FROM setores_grupos, pedido_grupo WHERE pedido_grupo.id_grupo = setores_grupos.id AND pedido_grupo.id_pedido = ' . $id_pedido);
        $retorno = '';
        if ($query->num_rows > 0) {
            $obj = $query->fetch_object();
            $obj->nome = utf8_encode($obj->nome);
            $retorno = '<b>Grupo:</b> ' . $obj->nome;
        }
        return $retorno;
    }

    /**
     * Função para retornar o pedido para um relátório separando-o por licitação e fornecedor
     *
     * @param int $id_pedido
     * @return string
     */
    public function getContentPedido(int $id_pedido): string {
        $retorno = '';
        // PRIMEIRO FAZEMOS O CABEÇALHO REFERENTE AO NUM_LICITACAO
        $query_ini = Query::getInstance()->exe('SELECT DISTINCT itens.num_licitacao, itens.num_processo, itens.dt_inicio, itens.dt_fim FROM itens_pedido, itens WHERE itens.id = itens_pedido.id_item AND itens_pedido.id_pedido = ' . $id_pedido);
        while ($licitacao = $query_ini->fetch_object()) {
            $row = new Row();
            $row->addColumn(new Column('Licitação: ' . $licitacao->num_licitacao));
            $row->addColumn(new Column('Processo: ' . $licitacao->num_processo));
            $row->addColumn(new Column('Início: ' . $licitacao->dt_inicio));
            $row->addColumn(new Column('Fim: ' . ($licitacao->dt_fim == '') ? '------------' : $licitacao->dt_fim));
            $retorno .= "
                <fieldset class=\"preg\">
                    <table>" . $row . "</table>
                </fieldset><br>";
            $query_forn = Query::getInstance()->exe('SELECT DISTINCT itens.cgc_fornecedor, itens.nome_fornecedor, itens.num_contrato FROM itens, itens_pedido WHERE itens.id = itens_pedido.id_item AND itens_pedido.id_pedido = ' . $id_pedido . ' AND itens.num_licitacao = ' . $licitacao->num_licitacao);

            // -------------------------------------------------------------------------
            //                FORNECEDORES REFERENTES À LICITAÇÃO
            // -------------------------------------------------------------------------
            while ($fornecedor = $query_forn->fetch_object()) {
                $fornecedor->nome_fornecedor = substr($fornecedor->nome_fornecedor, 0, 40);
                $fornecedor->nome_fornecedor = strtoupper($fornecedor->nome_fornecedor);
                $fornecedor->nome_fornecedor = utf8_encode($fornecedor->nome_fornecedor);
                $retorno .= "
                    <fieldset style=\"border-bottom: 1px solid black; padding: 5px;\">
                        <table>
                            <tr>
                                <td style=\"text-align: left; font-weight: bold;\">" . $fornecedor->nome_fornecedor . "</td>
                                <td>CNPJ: " . $fornecedor->cgc_fornecedor . "</td>
                                <td>Contrato: " . $fornecedor->num_contrato . "</td>
                            </tr>
                        </table>
                    </fieldset>";
                // ----------------------------------------------------------------------
                //                  ITENS REFERENTES AOS FORNECEDORES
                // ----------------------------------------------------------------------
                $query_itens = Query::getInstance()->exe("SELECT itens.cod_reduzido, itens.cod_despesa, itens.seq_item_processo, itens.complemento_item, itens.vl_unitario, itens_pedido.qtd, itens_pedido.valor FROM itens, itens_pedido WHERE itens.id = itens_pedido.id_item AND itens_pedido.id_pedido = " . $id_pedido . " AND itens.cgc_fornecedor = '" . $fornecedor->cgc_fornecedor . "' ORDER BY CAST(itens.seq_item_processo AS UNSIGNED) ASC");

                $table = new Table('', 'prod', ['Código', 'Item', 'Natureza', 'Descrição', 'Quantidade', 'Valor Unitário', 'Valor Total'], true);
                while ($item = $query_itens->fetch_object()) {
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
                $retorno .= $table . '<br>';
            }
        }

        return $retorno;
    }

    /**
     * Function to returns the request comments.
     *
     * @param $id_pedido int Request id.
     * @return string SOF comments in request.
     */
    public function getComentarios(int $id_pedido): string {
        $retorno = "";
        $query_emp = Query::getInstance()->exe("SELECT pedido_empenho.empenho, DATE_FORMAT(pedido_empenho.data, '%d/%m/%Y') AS data FROM pedido_empenho WHERE pedido_empenho.id_pedido = " . $id_pedido);
        if ($query_emp->num_rows > 0) {
            $empenho = $query_emp->fetch_object();
            $row = new Row();
            $row->addColumn(new Column('Data Empenho: ' . $empenho->data));
            $row->addColumn(new Column('Empenho: ' . $empenho->empenho));

            $retorno = "<fieldset class=\"preg\"><table>" . $row . "</table></fieldset>";
        } else {
            $row = new Row();
            $row->addColumn(new Column('Empenho: EMPENHO SIAFI PENDENTE'));
            $retorno = "<fieldset class=\"preg\"><table>" . $row . "</table></fieldset>";
        }

        $query = Query::getInstance()->exe("SELECT DATE_FORMAT(comentarios.data_coment, '%d/%m/%Y') AS data_coment, comentarios.comentario FROM comentarios, prioridade WHERE prioridade.id = comentarios.prioridade AND comentarios.id_pedido = " . $id_pedido);
        if ($query->num_rows > 0) {
            while ($comentario = $query->fetch_object()) {
                $retorno .= "
                    <fieldset>
                        <p style=\"font-weight: normal;\"> <b>Comentário [" . $comentario->data_coment . "]:</b> " . $comentario->comentario . "</p>
                    </fieldset>";
            }
        } else {
            $retorno .= "Sem comentários";
        }
        return $retorno;
    }

    /**
     *    Função que retornar o id do setor do pedido.
     *
     * @param $id_pedido int Id do pedido.
     * @return int Id do setor que fez o pedido.
     */
    public function getSetorPedido(int $id_pedido): int {
        $query = Query::getInstance()->exe('SELECT id_setor FROM pedido WHERE id = ' . $id_pedido);
        $obj = $query->fetch_object();
        return $obj->id_setor;
    }

    /**
     * Função para fazer um relatório específico de pedidos feito pelo usuário do SOF.
     * @param array $pedidos Array com os pedidos selecionados
     * @param int $status Status dos pedidos (default = 8)
     * @return string Relatório personalizado pelo SOF.
     */
    public function getRelPed(array $pedidos, int $status = 8) {
        if (empty($pedidos)) {
            return '';
        }
        $retorno = '';
        $where_status = ($status != 0) ? 'AND pedido.status = ' . $status : '';
        $empenho = $tb_empenho = $where_empenho = "";
        if ($status == 8) {
            $where_empenho = "AND pedido_empenho.id_pedido = pedido.id";
            $tb_empenho = "pedido_empenho, ";
            $empenho = ", pedido_empenho.empenho";
        }

        $where_pedidos = '(';
        $len = count($pedidos);
        for ($i = 0; $i < $len; $i++) {
            $where_pedidos .= 'pedido.id = ' . $pedidos[$i];
            if ($i != $len - 1) {
                $where_pedidos .= ' OR ';
            }
        }
        $where_pedidos .= ')';

        $query = Query::getInstance()->exe("SELECT pedido.id, setores.nome AS setor, DATE_FORMAT(pedido.data_pedido, '%d/%m/%Y') AS data_pedido, prioridade.nome AS prioridade, status.id AS id_status, status.nome AS status, pedido.valor " . $empenho . ' FROM ' . $tb_empenho . ' setores, pedido, prioridade, status WHERE status.id = pedido.status ' . $where_empenho . ' AND prioridade.id = pedido.prioridade AND pedido.id_setor = setores.id ' . $where_status . ' AND ' . $where_pedidos . ' ORDER BY pedido.id ASC');

        $titulo = ($status == 8) ? 'Relatório de Empenhos Enviados ao Ordenador' : 'Relatório de Pedidos por Setor e Nível de Prioridade';

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
        $query_tot = Query::getInstance()->exe('SELECT sum(pedido.valor) AS total FROM ' . $tb_empenho . ' pedido WHERE 1 > 0 ' . $where_empenho . ' AND pedido.alteracao = 0 ' . $where_status . ' AND ' . $where_pedidos);
        $tot = $query_tot->fetch_object();
        $total = ($tot->total > 0) ? 'R$ ' . number_format($tot->total, 3, ',', '.') : 'R$ 0';
        $retorno .= "<fieldset class=\"preg\">
                    <h5>DESCRIÇÃO DO RELATÓRIO</h5>
                    <h6>" . $titulo . "</h6>
                    <h6>Pedidos selecionados pelo SOF</h6></fieldset><br>";

        $row = new Row();
        $row->addColumn(new Column(count($pedidos) . ' selecionados'));
        $row->addColumn(new Column('Totalizando ' . $total));

        $sub_header = "<fieldset class=\"preg\"><table>" . $row . "</table></fieldset>";

        $table = new Table('', 'prod', $headers, true);

        $flag = false;
        $i = 0;
        while ($pedido = $query->fetch_object()) {
            if ($pedido->id_status != 8) {
                $flag = true;
                break;
            } else {
                $i++;
            }

            $row = new Row();
            $row->addColumn(new Column($pedido->id));
            $row->addColumn(new Column(BuscaLTE::getInstance()->getFornecedor($pedido->id)));
            $row->addColumn(new Column($pedido->setor));

            if ($status == 8) {
                $row->addColumn(new Column($pedido->prioridade));
                $row->addColumn(new Column($pedido->empenho));
            } else {
                $row->addColumn(new Column($pedido->data_pedido));
                $row->addColumn(new Column($pedido->prioridade));
                $row->addColumn(new Column($pedido->status));
                $row->addColumn(new Column('R$ ' . $pedido->valor));
            }

            $table->addRow($row);
        }
        if ($query->num_rows > 0 && !$flag && $i == count($pedidos)) {
            $retorno .= $sub_header . $table;
        } else {
            $retorno .= 'RELATÓRIO INVÁLIDO: Essa versão suporta apenas pedidos com status de Enviado ao Ordenador.';
            $flag = true;
        }

        if ($status == 8 && !$flag && $i == count($pedidos)) {
            $retorno .= Controller::footerOrdenator();
        }
        return $retorno;
    }

    public function getRelatorioLib(int $id_setor, array $categoria, string $dataI, string $dataF): string {
        $retorno = "<fieldset class=\"preg\">
                <h5>DESCRIÇÃO DO RELATÓRIO</h5>
                <h6>Relatório de Liberações Orçamentárias</h6>
                <h6>Período de Emissão: " . $dataI . " à " . $dataF . "</h6></fieldset><br>";

        $where_setor = ($id_setor != 0) ? 'AND id_setor = ' . $id_setor : '';
        $where_categoria = ' AND (';

        $len = count($categoria);
        for ($i = 0; $i < $len; $i++) {
            $where_categoria .= 'categoria = ' . $categoria[$i];
            if ($i != $len - 1) {
                $where_categoria .= ' OR ';
            }
        }
        $where_categoria .= ')';
        $dataIni = Util::getInstance()->dateFormat($dataI);
        $dataFim = Util::getInstance()->dateFormat($dataF);

        $query = Query::getInstance()->exe("SELECT id_setor, DATE_FORMAT(data, '%d/%m/%Y') AS data, valor, categoria FROM saldos_lancamentos WHERE data BETWEEN '" . $dataIni . "' AND '" . $dataFim . "' " . $where_setor . $where_categoria . ' ORDER BY id ASC');

        $table = new Table('', 'prod', ['Setor', 'Data', 'Valor', 'Categoria'], true);
        while ($obj = $query->fetch_object()) {
            $row = new Row();
            $row->addColumn(new Column(ARRAY_SETORES[$obj->id_setor]));
            $row->addColumn(new Column($obj->data));
            $row->addColumn(new Column('R$ ' . number_format($obj->valor, 3, ',', '.')));
            $row->addColumn(new Column(ucfirst(ARRAY_CATEGORIA[$obj->categoria])));

            $table->addRow($row);
        }

        $retorno .= $table;
        return $retorno;
    }

    /**
     * Function that returns the requests report.
     *
     * @param int $id_setor
     * @param int $prioridade
     * @param array $status
     * @param string $dataI
     * @param string $dataF
     * @param bool $checkSaifi
     * @return string Content of PDF document
     */
    public function getRelatorioPedidos(int $id_setor, int $prioridade, array $status, string $dataI, string $dataF, bool $checkSaifi): string {
        $retorno = '';
        $where_status = '';
        $where_prioridade = ($prioridade != 0) ? 'AND pedido.prioridade = ' . $prioridade : '';
        $where_setor = ($id_setor != 0) ? 'AND pedido.id_setor = ' . $id_setor : '';

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
        $dataIni = Util::getInstance()->dateFormat($dataI);
        $dataFim = Util::getInstance()->dateFormat($dataF);
        $where_empenho = $tb_empenho = $empenho = "";
        if (in_array(8, $status) || $checkSaifi) {
            $where_empenho = "AND pedido_empenho.id_pedido = pedido.id";
            $tb_empenho = "pedido_empenho, ";
            $empenho = ", pedido_empenho.empenho";
        }

        // retorna o total de pedidos com os parâmetros (LIMIT para mostrar)
        $obj_count = Query::getInstance()->exe("SELECT COUNT(pedido.id) AS total FROM {$tb_empenho} setores, pedido, prioridade, status WHERE status.id = pedido.status {$where_setor} {$where_prioridade} {$where_empenho} AND prioridade.id = pedido.prioridade AND pedido.id_setor = setores.id {$where_status} AND pedido.data_pedido BETWEEN '{$dataIni}' AND '{$dataFim}'")->fetch_object();
        $num_rows = $obj_count->total;

        $query = Query::getInstance()->exe("SELECT pedido.id, pedido.id_setor, setores.nome AS setor, DATE_FORMAT(pedido.data_pedido, '%d/%m/%Y') AS data_pedido, prioridade.nome AS prioridade, status.nome AS status, pedido.valor {$empenho} FROM {$tb_empenho} setores, pedido, prioridade, status WHERE status.id = pedido.status {$where_setor} {$where_prioridade} {$where_empenho} AND prioridade.id = pedido.prioridade AND pedido.id_setor = setores.id {$where_status} AND pedido.data_pedido BETWEEN '{$dataIni}' AND '{$dataFim}' ORDER BY pedido.id DESC LIMIT " . LIMIT_REQ_REPORT);

        $titulo = 'Relatório de Pedidos por Setor e Nível de Prioridade';
        $headers = ['Enviado em', 'Prioridade', 'Status', 'Valor'];
        if (in_array(8, $status)) {
            $titulo = 'Relatório de Empenhos Enviados ao Ordenador';
            $headers = ['Prioridade', 'SIAFI'];
        }
        $query_tot = Query::getInstance()->exe("SELECT sum(pedido.valor) AS total FROM {$tb_empenho} pedido WHERE 1 > 0 {$where_setor} {$where_prioridade} {$where_empenho} AND pedido.alteracao = 0 {$where_status} AND pedido.data_pedido BETWEEN '{$dataIni}' AND '{$dataFim}';");
        $tot = $query_tot->fetch_object();
        $total = ($tot->total > 0) ? 'R$ ' . number_format($tot->total, 3, ',', '.') : 'R$ 0';

        $row = new Row();
        $row->addColumn(new Column($num_rows . ' resultados encontrados'));
        $row->addColumn(new Column('Mostrando ' . $query->num_rows));
        $row->addColumn(new Column('Totalizando ' . $total));
        $retorno .= "
                <fieldset class=\"preg\">
                    <h5>DESCRIÇÃO DO RELATÓRIO</h5>
                    <h6>" . $titulo . "</h6>
                    <h6>Período de Emissão: " . $dataI . " à " . $dataF . "</h6>
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

        $array_sub_totais = [];
        $table = new Table('', 'prod', $th, true);
        while ($pedido = $query->fetch_object()) {
            if (!array_key_exists($pedido->id_setor, $array_sub_totais)) {
                $array_sub_totais[$pedido->id_setor] = 0;
            }
            $array_sub_totais[$pedido->id_setor] += $pedido->valor;

            $row = new Row();
            $row->addColumn(new Column($pedido->id));
            $row->addColumn(new Column(BuscaLTE::getInstance()->getFornecedor($pedido->id)));
            $row->addColumn(new Column($pedido->setor));
            if (in_array(8, $status)) {
                $row->addColumn(new Column($pedido->prioridade));
                $row->addColumn(new Column($pedido->empenho));
            } else {
                $row->addColumn(new Column($pedido->data_pedido));
                $row->addColumn(new Column($pedido->prioridade));
                $row->addColumn(new Column($pedido->status));
                $row->addColumn(new Column('R$ ' . $pedido->valor));
            }

            $table->addRow($row);
        }
        $retorno .= $table;

        if ($_SESSION['id_setor'] == 2) {
            $retorno .= "<br><h5>As porcentagens mostradas são em relação ao Total (pag. 1) deste Relatório.</h5>
                <fieldset class=\"preg\"><h5>SUBTOTAIS POR SETOR</h5>";

            $len = count(ARRAY_SETORES);

            $table = new Table('', 'prod', ['Setor', 'Total', 'Porcentagem'], true);
            for ($k = 0; $k < $len; $k++) {
                if (array_key_exists($k, $array_sub_totais)) {
                    $parcial = number_format($array_sub_totais[$k], 3, ',', '.');
                    $porcentagem = number_format(($array_sub_totais[$k] * 100) / $tot->total, 3, ',', '.');

                    $row = new Row();
                    $row->addColumn(new Column(ARRAY_SETORES[$k]));
                    $row->addColumn(new Column($parcial));
                    $row->addColumn(new Column($porcentagem . '%'));

                    $table->addRow($row);
                }
            }
            $retorno .= $table . '</fieldset><br>' . "<fieldset class=\"preg\"><h5>SUBTOTAIS POR GRUPO</h5>";

            $query_gr = Query::getInstance()->exe("SELECT pedido_grupo.id_grupo, setores_grupos.nome AS ng, pedido.valor {$empenho} FROM {$tb_empenho} setores, setores_grupos, pedido, prioridade, status, pedido_grupo WHERE setores_grupos.id = pedido_grupo.id_grupo AND pedido_grupo.id_pedido = pedido.id AND status.id = pedido.status {$where_setor} {$where_prioridade} {$where_empenho} AND prioridade.id = pedido.prioridade AND pedido.id_setor = setores.id {$where_status} AND pedido.data_pedido BETWEEN '{$dataIni}' AND '{$dataFim}'");

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

            $retorno .= $table_gr . '</fieldset><br>';
        }

        if (in_array(8, $status)) {
            $retorno .= Controller::footerOrdenator();
        }
        return $retorno;
    }

    /**
     * @param int $tipo Tipo de processo.
     * @return string Relatório com os processos.
     */
    public function getRelatorioProcessos(int $tipo): string {
        $retorno = '';
        $where = ($tipo != 0) ? ' WHERE id = ' . $tipo : '';

        $query_proc = Query::getInstance()->exe('SELECT id, nome FROM processos_tipo' . $where);
        while ($tipo_proc = $query_proc->fetch_object()) {
            $query = Query::getInstance()->exe('SELECT processos.num_processo, processos_tipo.nome AS tipo, processos_tipo.id AS id_tipo, processos.estante, processos.prateleira, processos.entrada, processos.saida, processos.responsavel, processos.retorno, processos.obs FROM processos, processos_tipo WHERE processos.tipo = processos_tipo.id AND processos.tipo = ' . $tipo_proc->id . ' ORDER BY processos.tipo ASC');
            if ($query->num_rows > 0) {
                $row = new Row();
                $row->addColumn(new Column('Tipo: ' . $tipo_proc->nome));
                $retorno .= "<fieldset class=\"preg\"><table>" . $row . "</table></fieldset><br>";
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
                $retorno .= $table . '<br>';
            }
        }
        $query_all = Query::getInstance()->exe('SELECT DISTINCT num_processo FROM itens WHERE num_processo NOT IN (SELECT DISTINCT num_processo FROM processos)');
        if ($query_all->num_rows > 0) {
            $retorno .= "<fieldset class=\"preg\"><h5>Processos Não Cadastrados</h5></fieldset><br>";
            $table = new Table('', 'prod', ['Processo', 'Tipo', 'Estante', 'Prateleira'], true);
            while ($processo = $query_all->fetch_object()) {
                $row = new Row();
                $row->addColumn(new Column($processo->num_processo));
                $row->addColumn(new Column('______________________'));
                $row->addColumn(new Column('______________________'));
                $row->addColumn(new Column('______________________'));

                $table->addRow($row);
            }
            $retorno .= $table;
        }

        return $retorno;
    }

}
