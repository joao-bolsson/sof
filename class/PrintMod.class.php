<?php

/**
 *  Classe com as funções de busca para impressão do sistema.
 *
 *  @author João Bolsson (joaovictorbolsson@gmail.com).
 *  @since 2017, 28 Jan.
 */
ini_set('display_erros', true);
error_reporting(E_ALL);

spl_autoload_register(function (string $class_name) {
    include_once $class_name . '.class.php';
});

require_once '../defines.php';

class PrintMod extends Conexao {

    private $mysqli, $obj_Util, $obj_Busca;

    public function __construct() {
        parent::__construct();
    }

    private function openConnection() {
        if (is_null($this->mysqli)) {
            $this->mysqli = parent::getConexao();
        }
    }

    public function getRelUsers(): string {
        self::openConnection();
        $query = $this->mysqli->query('SELECT nome, login, id_setor, email FROM usuario ORDER BY nome ASC') or exit('Erro ao buscar usuários.');
        $this->mysqli = NULL;
        $retorno = "
            <fieldset class=\"preg\">
                    <h5>DESCRIÇÃO DO RELATÓRIO</h5>
                    <h6>Relatório de Usuários Cadastrados no Sistema</h6>
                </fieldset><br>
            <fieldset>
                <table class=\"prod\">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>Login</th>
                            <th>Setor</th>
                            <th>E-mail</th>
                        </tr>
                    </thead>
                    <tbody>";

        while ($user = $query->fetch_object()) {
            $retorno .= " 
            <tr>
                <td>" . $user->nome . "</td>
                <td>" . $user->login . "</td>
                <td>" . ARRAY_SETORES[$user->id_setor] . "</td>
                <td>" . $user->email . "</td>
            </tr>";
        }

        $retorno .= "</tbody></table></fieldset>";

        return $retorno;
    }

    /**
     * Função para retornar o cabeçalho do pdf do pedido.
     *
     * @return string
     */
    public function getHeader(int $id_pedido): string {
        self::openConnection();
        $query = $this->mysqli->query("SELECT pedido.id, DATE_FORMAT(pedido.data_pedido, '%d/%m/%Y') AS data_pedido, EXTRACT(YEAR FROM pedido.data_pedido) AS ano, mes.sigla_mes AS ref_mes, status.nome AS status, pedido.valor AS valor, pedido.obs, pedido.pedido_contrato, prioridade.nome AS prioridade, pedido.aprov_gerencia, pedido.id_usuario FROM prioridade, pedido, mes, status WHERE pedido.prioridade = prioridade.id AND status.id = pedido.status AND pedido.id = {$id_pedido} AND mes.id = pedido.ref_mes;") or exit("Erro ao formar o cabeçalho do pedido.");
        $this->mysqli = NULL;
        $pedido = $query->fetch_object();
        $lblPedido = "Pedido";
        if ($pedido->pedido_contrato) {
            $lblPedido = "Pedido de Contrato";
        }
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
                <p><b>Autor:</b> " . PrintMod::getUserName($pedido->id_usuario) . "</p>
                <table style=\"font-size: 8pt; margin: 5px;\">
                    <tr>
                        <td style=\"text-align: left;\">" . PrintMod::getGrupoPedido($id_pedido) . "</td>
                        <td style=\"text-align: right;\">" . PrintMod::getEmpenho($id_pedido) . "</td>
                    </tr>
                </table>";
        if ($pedido->aprov_gerencia) {
            $retorno .= "<p><b>Aprovado Pela Gerência</b></p>";
        }
        $retorno .= "<p><b>Observação da Unidade Solicitante: </b></p>
                <p style=\"font-weight: normal !important;\">	" . $pedido->obs . "</p>
            </fieldset><br>";
        $retorno .= PrintMod::getTableFontes($id_pedido);
        $retorno .= PrintMod::getTableLicitacao($id_pedido);
        return $retorno;
    }

    private function getUserName(int $id_user) {
        self::openConnection();

        $query = $this->mysqli->query("SELECT usuario.nome FROM usuario WHERE usuario.id = " . $id_user) or exit("Erro ao buscar o nome do usuario do pedido");
        $obj = $query->fetch_object();

        $this->mysqli = NULL;
        return $obj->nome;
    }

    private function getTableLicitacao(int $id_pedido): string {
        $retorno = "<fieldset>
                <h5>PEDIDO SEM LICITAÇÃO</h5>
                </fieldset><br>";

        self::openConnection();
        $query = $this->mysqli->query("SELECT licitacao.tipo AS id_tipo, licitacao_tipo.nome AS tipo, licitacao.numero, licitacao.uasg, licitacao.processo_original, licitacao.gera_contrato FROM licitacao, licitacao_tipo WHERE licitacao_tipo.id = licitacao.tipo AND licitacao.id_pedido = {$id_pedido};") or exit("Erro ao buscar licitação do pedido.");
        $this->mysqli = NULL;
        if ($query->num_rows == 1) {
            $obj = $query->fetch_object();
            $thead = "";
            $tbody = "";
            if ($obj->id_tipo == 3 || $obj->id_tipo == 4 || $obj->id_tipo == 2) {
                $gera = "Gera Contrato";
                if ($obj->gera_contrato == 0) {
                    $gera = "Não Gera Contrato";
                }
                $thead = "
                    <th>UASG</th>
                    <th>Processo Original</th>
                    <th>Contrato</th>";
                $tbody = "
                    <td>" . $obj->uasg . "</td>
                    <td>" . $obj->processo_original . "</td>
                    <td>" . $gera . "</td>";
            }
            $retorno = "
                <fieldset class=\"preg\">
                    <table>
                        <thead>
                            <tr>
                                <th>Tipo de Licitação</th>
                                <th>Número</th>
                                " . $thead . "
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>" . $obj->tipo . "</td>
                                <td>" . $obj->numero . "</td>
                                " . $tbody . "
                            </tr>
                        </tbody>
                    </table>
                </fieldset><br>";
        }

        return $retorno;
    }

    /**
     * 	Função para retornar as fontes de recurso de um pedido (impressão).
     *
     * 	@param $id_pedido Id do pedido.
     * 	@return Fontes de recurso.
     */
    public function getTableFontes(int $id_pedido): string {
        $retorno = '';
        self::openConnection();
        $query = $this->mysqli->query('SELECT fonte_recurso, ptres, plano_interno FROM pedido_fonte WHERE id_pedido = ' . $id_pedido) or exit('Erro ao buscar fontes do pedido');
        $this->mysqli = NULL;
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
            $retorno = '
                <fieldset>
                    <h5>PEDIDO AGUARDA FONTE DE RECURSO</h5>
                </fieldset><br>';
        }

        return $retorno;
    }

    private function getEmpenho(int $id_pedido): string {
        self::openConnection();

        $query = $this->mysqli->query("SELECT contrato_tipo.nome, pedido_contrato.siafi FROM contrato_tipo, pedido_contrato WHERE pedido_contrato.id_tipo = contrato_tipo.id AND pedido_contrato.id_pedido = {$id_pedido};") or exit("Erro ao buscar o contrato do pedido.");
        $this->mysqli = NULL;
        $retorno = "";
        if ($query->num_rows > 0) {
            $obj = $query->fetch_object();
            $retorno = "<b>Tipo:</b> " . $obj->nome . " <input type=\"text\" value=\"" . $obj->siafi . "\"/>";
        }
        return $retorno;
    }

    private function getGrupoPedido(int $id_pedido): string {
        self::openConnection();
        $query = $this->mysqli->query("SELECT setores_grupos.nome, pedido_grupo.id_pedido FROM setores_grupos, pedido_grupo WHERE pedido_grupo.id_grupo = setores_grupos.id AND pedido_grupo.id_pedido = {$id_pedido};") or exit("Erro ao buscar grupo do pedido.");
        $this->mysqli = NULL;
        $retorno = "";
        if ($query->num_rows > 0) {
            $obj = $query->fetch_object();
            $obj->nome = utf8_encode($obj->nome);
            $retorno = "<b>Grupo:</b> " . $obj->nome;
        }
        return $retorno;
    }

    /**
     * Função para retornar o pedido para um relátório separando-o por licitação e fornecedor
     *
     * @return string
     */
    public function getContentPedido(int $id_pedido): string {
        $retorno = '';
        // PRIMEIRO FAZEMOS O CABEÇALHO REFERENTE AO NUM_LICITACAO
        self::openConnection();
        $query_ini = $this->mysqli->query("SELECT DISTINCT itens.num_licitacao, itens.num_processo, itens.dt_inicio, itens.dt_fim FROM itens_pedido, itens WHERE itens.id = itens_pedido.id_item AND itens_pedido.id_pedido = {$id_pedido};") or exit("Erro ao buscar itens do pedido.");
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
            $query_forn = $this->mysqli->query('SELECT DISTINCT itens.cgc_fornecedor, itens.nome_fornecedor, itens.num_contrato FROM itens, itens_pedido WHERE itens.id = itens_pedido.id_item AND itens_pedido.id_pedido = ' . $id_pedido . ' AND itens.num_licitacao = ' . $licitacao->num_licitacao) or exit('Erro ao buscar fornecedores do pedido');

            // -------------------------------------------------------------------------
            //                FORNECEDORES REFERENTES À LICITAÇÃO
            // -------------------------------------------------------------------------
            while ($fornecedor = $query_forn->fetch_object()) {
                $fornecedor->nome_fornecedor = substr($fornecedor->nome_fornecedor, 0, 40);
                $fornecedor->nome_fornecedor = strtoupper($fornecedor->nome_fornecedor);
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
                $query_itens = $this->mysqli->query("SELECT itens.cod_reduzido, itens.cod_despesa, itens.seq_item_processo, itens.complemento_item, itens.vl_unitario, itens_pedido.qtd, itens_pedido.valor FROM itens, itens_pedido WHERE itens.id = itens_pedido.id_item AND itens_pedido.id_pedido = " . $id_pedido . " AND itens.cgc_fornecedor = '" . $fornecedor->cgc_fornecedor . "'") or exit('Erro ao buscar os itens dos fornecedores do pedido');

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
        $this->mysqli = NULL;

        return $retorno;
    }

    /**
     * 	Função para retornar os comentários de um pedido.
     *
     * 	@return Comentários do SOF em um pedido (Print).
     */
    public function getComentarios(int $id_pedido): string {
        $retorno = "";
        self::openConnection();
        $query_emp = $this->mysqli->query("SELECT pedido_empenho.empenho, DATE_FORMAT(pedido_empenho.data, '%d/%m/%Y') AS data FROM pedido_empenho WHERE pedido_empenho.id_pedido = {$id_pedido};") or exit("Erro ao mostrar o empenho do pedido nos comentários.");
        if ($query_emp->num_rows > 0) {
            $empenho = $query_emp->fetch_object();
            $row = new Row();
            $row->addColumn(new Column('Data Empenho: ' . $empenho->data));
            $row->addColumn(new Column('Empenho: ' . $empenho->empenho));

            $retorno = "
                <fieldset class=\"preg\">
                    <table>" . $row . "</table>
                </fieldset>";
        } else {
            $row = new Row();
            $row->addColumn(new Column('Empenho: EMPENHO SIAFI PENDENTE'));
            $retorno = "
                <fieldset class=\"preg\">
                    <table>" . $row . "</table>
                </fieldset>";
        }

        $query = $this->mysqli->query("SELECT DATE_FORMAT(comentarios.data_coment, '%d/%m/%Y') AS data_coment, comentarios.comentario FROM comentarios, prioridade WHERE prioridade.id = comentarios.prioridade AND comentarios.id_pedido = {$id_pedido};") or exit("Erro ao buscar os comentários do pedido.");
        $this->mysqli = NULL;
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
     * 	Função que retornar o id do setor do pedido.
     *
     * 	@param $id_pedido Id do pedido.
     * 	@return Id do setor que fez o pedido.
     */
    public function getSetorPedido(int $id_pedido): int {
        self::openConnection();
        $query = $this->mysqli->query("SELECT id_setor FROM pedido WHERE id = {$id_pedido};") or exit("Erro ao buscar o id do setor do pedido.");
        $this->mysqli = NULL;
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
        $retorno = "";
        if (empty($pedidos)) {
            return $retorno;
        }

        $where_status = "AND pedido.status = " . $status;
        if ($status == 0) {
            $where_status = '';
        }
        if ($status == 8) {
            $where_empenho = "AND pedido_empenho.id_pedido = pedido.id";
            $tb_empenho = "pedido_empenho, ";
            $empenho = ", pedido_empenho.empenho";
        }
        self::openConnection();

        $where_pedidos = '(';
        $len = count($pedidos);
        for ($i = 0; $i < $len; $i++) {
            $where_pedidos .= 'pedido.id = ' . $pedidos[$i];
            if ($i != $len - 1) {
                $where_pedidos .= ' OR ';
            }
        }
        $where_pedidos .= ')';

        $query = $this->mysqli->query("SELECT pedido.id, setores.nome AS setor, DATE_FORMAT(pedido.data_pedido, '%d/%m/%Y') AS data_pedido, prioridade.nome AS prioridade, status.id AS id_status, status.nome AS status, pedido.valor {$empenho} FROM {$tb_empenho} setores, pedido, prioridade, status WHERE status.id = pedido.status {$where_empenho} AND prioridade.id = pedido.prioridade AND pedido.id_setor = setores.id {$where_status} AND {$where_pedidos} ORDER BY pedido.id ASC;") or exit("Erro ao buscar os pedidos com as especificações do usuário.");

        $titulo = "Relatório de Pedidos por Setor e Nível de Prioridade";
        if ($query) {
            $thead = "
                <th>Enviado em</th>
                <th>Prioridade</th>
                <th>Status</th>
                <th>Valor</th>";
            if ($status == 8) {
                $titulo = "Relatório de Empenhos Enviados ao Ordenador";
                $thead = "
                    <th>Prioridade</th>
                    <th>SIAFI</th>";
            }
            self::openConnection();
            $query_tot = $this->mysqli->query("SELECT sum(pedido.valor) AS total FROM {$tb_empenho} pedido WHERE 1 > 0 {$where_empenho} AND pedido.alteracao = 0 {$where_status};") or exit("Erro ao somar os pedidos.");
            $this->mysqli = NULL;
            $total = "R$ 0";
            $tot = $query_tot->fetch_object();
            if ($tot->total > 0) {
                $total = "R$ " . number_format($tot->total, 3, ',', '.');
            }
            $retorno .= "
                <fieldset class=\"preg\">
                    <h5>DESCRIÇÃO DO RELATÓRIO</h5>
                    <h6>" . $titulo . "</h6>
                    <h6>Pedidos selecionados pelo SOF</h6>
                </fieldset><br>";
            $sub_header = "
                <fieldset class=\"preg\">
                    <table>
                        <tr>
                            <td>" . count($pedidos) . " selecionados</td>
                            <td>Totalizando " . $total . "</td>
                        </tr>
                    </table>
                </fieldset>";
            $table_pedidos = "
                <table class=\"prod\">
                    <thead>
                        <tr>
                            <th>Pedido</th>
                            <th>Fornecedor</th>
                            <th>Setor</th>
                            " . $thead . "
                        </tr>
                    </thead>
                    <tbody>";
            if (is_null($this->obj_Busca)) {
                $this->obj_Busca = new BuscaLTE();
            }
            $flag = false;
            $i = 0;
            while ($pedido = $query->fetch_object()) {
                $tbody = '';
                if ($pedido->id_status != 8) {
                    $flag = true;
                    break;
                } else {
                    $i++;
                }
                if ($status == 8) {
                    $tbody = "
                        <td>" . $pedido->prioridade . "</td>
                        <td>" . $pedido->empenho . "</td>";
                } else {
                    $tbody = "
                        <td>" . $pedido->data_pedido . "</td>
                        <td>" . $pedido->prioridade . "</td>
                        <td>" . $pedido->status . "</td>
                        <td>R$ " . $pedido->valor . "</td>";
                }
                $table_pedidos .= "
                        <tr>
                            <td>" . $pedido->id . "</td>
                            <td>" . $this->obj_Busca->getFornecedor($pedido->id) . "</td>
                            <td>" . $pedido->setor . "</td>
                            " . $tbody . "
                        </tr>";
            }
            $table_pedidos .= "<tbody></table>";
            if ($query->num_rows > 0 && !$flag && $i == count($pedidos)) {
                $retorno .= $sub_header . $table_pedidos;
            } else {
                $retorno .= 'RELATÓRIO INVÁLIDO: Essa versão suporta apenas pedidos com status de Enviado ao Ordenador.';
                $flag = true;
            }
        }
        if ($status == 8 && !$flag && $i == count($pedidos)) {
            $retorno .= "
                <br><br><br>
                <h5 class=\"ass\" style=\"margin-right: 50%; margin-bottom: 0;\">
                _______________________________________________<br>
                RESPONSÁVEL PELA INFORMAÇÃO
                </h5>
                <h5 class=\"ass\" style=\"margin-left: 51%; margin-top: -32px;\">
                _______________________________________________<br>
                RESPONSÁVEL PELO RECEBIMENTO
                </h5><br><br>
                <h4 style=\"text-align: center\" class=\"ass\">Santa Maria, ___ de ___________________ de _____.</h4>";
        }
        $this->obj_Busca = NULL;
        return $retorno;
    }

    public function getRelatorioLib(int $id_setor, array $categoria, string $dataI, string $dataF): string {
        $retorno = "
            <fieldset class=\"preg\">
                <h5>DESCRIÇÃO DO RELATÓRIO</h5>
                <h6>Relatório de Liberações Orçamentárias</h6>
                <h6>Período de Emissão: " . $dataI . " à " . $dataF . "</h6>
            </fieldset><br>";

        $where_setor = ($id_setor != 0) ? 'AND id_setor = ' . $id_setor : '';
        $where_categoria = 'AND (';

        $len = count($categoria);
        for ($i = 0; $i < $len; $i++) {
            $where_categoria .= 'categoria = ' . $categoria[$i];
            if ($i != $len - 1) {
                $where_categoria .= ' OR ';
            }
        }
        $where_categoria .= ')';
        if (is_null($this->obj_Util)) {
            $this->obj_Util = new Util();
        }
        $dataIni = $this->obj_Util->dateFormat($dataI);
        $dataFim = $this->obj_Util->dateFormat($dataF);

        self::openConnection();
        $query = $this->mysqli->query("SELECT id_setor, DATE_FORMAT(data, '%d/%m/%Y') AS data, valor, categoria FROM saldos_lancamentos WHERE data BETWEEN '" . $dataIni . "' AND '" . $dataFim . "' " . $where_setor . $where_categoria . " ORDER BY id ASC") or exit('Erro ao gerar relatório');
        $this->mysqli = NULL;

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
     * 	Função que retonar o relatorio de pedidos.
     *
     * 	@return string Retorna a interface de um documento pdf.
     */
    public function getRelatorioPedidos(int $id_setor, int $prioridade, array $status, string $dataI, string $dataF): string {
        $retorno = "";
        $where_status = '';
        $where_prioridade = "AND pedido.prioridade = " . $prioridade;
        $where_setor = "AND pedido.id_setor = " . $id_setor;

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
        if ($prioridade == 0) {
            $where_prioridade = '';
        }
        if ($id_setor == 0) {
            $where_setor = '';
        }
        if (is_null($this->obj_Util)) {
            $this->obj_Util = new Util();
        }
        $dataIni = $this->obj_Util->dateFormat($dataI);
        $dataFim = $this->obj_Util->dateFormat($dataF);
        $this->obj_Util = NULL;
        $where_empenho = "";
        $tb_empenho = "";
        $empenho = "";
        if ($status == 8) {
            $where_empenho = "AND pedido_empenho.id_pedido = pedido.id";
            $tb_empenho = "pedido_empenho, ";
            $empenho = ", pedido_empenho.empenho";
        }
        self::openConnection();
        $query = $this->mysqli->query("SELECT pedido.id, pedido.id_setor, setores.nome AS setor, DATE_FORMAT(pedido.data_pedido, '%d/%m/%Y') AS data_pedido, prioridade.nome AS prioridade, status.nome AS status, pedido.valor {$empenho} FROM {$tb_empenho} setores, pedido, prioridade, status WHERE status.id = pedido.status {$where_setor} {$where_prioridade} {$where_empenho} AND prioridade.id = pedido.prioridade AND pedido.id_setor = setores.id {$where_status} AND pedido.data_pedido BETWEEN '{$dataIni}' AND '{$dataFim}' ORDER BY pedido.id ASC;") or exit("Erro ao buscar os pedidos com as especificações do usuário.");

        $titulo = "Relatório de Pedidos por Setor e Nível de Prioridade";
        if ($query) {
            $thead = "
                <th>Enviado em</th>
                <th>Prioridade</th>
                <th>Status</th>
                <th>Valor</th>";
            if ($status == 8) {
                $titulo = "Relatório de Empenhos Enviados ao Ordenador";
                $thead = "
                    <th>Prioridade</th>
                    <th>SIAFI</th>";
            }
            self::openConnection();
            $query_tot = $this->mysqli->query("SELECT sum(pedido.valor) AS total FROM {$tb_empenho} pedido WHERE 1 > 0 {$where_setor} {$where_prioridade} {$where_empenho} AND pedido.alteracao = 0 {$where_status} AND pedido.data_pedido BETWEEN '{$dataIni}' AND '{$dataFim}';") or exit("Erro ao somar os pedidos.");
            $this->mysqli = NULL;
            $total = "R$ 0";
            $tot = $query_tot->fetch_object();
            if ($tot->total > 0) {
                $total = "R$ " . number_format($tot->total, 3, ',', '.');
            }
            $retorno .= "
                <fieldset class=\"preg\">
                    <h5>DESCRIÇÃO DO RELATÓRIO</h5>
                    <h6>" . $titulo . "</h6>
                    <h6>Período de Emissão: {$dataI} à {$dataF}</h6>
                </fieldset><br>
                <fieldset class=\"preg\">
                    <table>
                        <tr>
                            <td>" . $query->num_rows . " resultados encontrados</td>
                            <td>Totalizando " . $total . "</td>
                        </tr>
                    </table>
                </fieldset>
                <table class=\"prod\">
                    <thead>
                        <tr>
                            <th>Pedido</th>
                            <th>Fornecedor</th>
                            <th>Setor</th>
                            " . $thead . "
                        </tr>
                    </thead>
                    <tbody>";
            if (is_null($this->obj_Busca)) {
                $this->obj_Busca = new BuscaLTE();
            }

            $array_sub_totais = [];
            while ($pedido = $query->fetch_object()) {
                $tbody = "";
                if (!array_key_exists($pedido->id_setor, $array_sub_totais)) {
                    $array_sub_totais[$pedido->id_setor] = 0;
                }
                $array_sub_totais[$pedido->id_setor] += $pedido->valor;
                if ($status == 8) {
                    $tbody = "
                        <td>" . $pedido->prioridade . "</td>
                        <td>" . $pedido->empenho . "</td>";
                } else {
                    $tbody = "
                        <td>" . $pedido->data_pedido . "</td>
                        <td>" . $pedido->prioridade . "</td>
                        <td>" . $pedido->status . "</td>
                        <td>R$ " . $pedido->valor . "</td>";
                }
                $retorno .= "
                        <tr style=\"\">
                            <td>" . $pedido->id . "</td>
                            <td>" . $this->obj_Busca->getFornecedor($pedido->id) . "</td>
                            <td>" . $pedido->setor . "</td>
                            " . $tbody . "
                        </tr>";
            }
            $retorno .= "<tbody></table>";

            if ($_SESSION['id_setor'] == 2) {
                $retorno .= "<br>
                <h5>As porcentagens mostradas são em relação ao Total (pag. 1) deste Relatório.</h5>
                <fieldset class=\"preg\">
                    <h5>SUBTOTAIS POR SETOR</h5>
                    <table class=\"prod\">
                        <thead>
                            <tr>
                                <th>Setor</th>
                                <th>Total</th>
                                <th>Porcentagem</th>
                            </tr>
                        </thead>
                        <tbody>";

                $len = count(ARRAY_SETORES);

                for ($k = 0; $k < $len; $k++) {
                    if (array_key_exists($k, $array_sub_totais)) {
                        $parcial = number_format($array_sub_totais[$k], 3, ',', '.');
                        $porcentagem = number_format(($array_sub_totais[$k] * 100) / $tot->total, 3, ',', '.');
                        $retorno .= "
                        <tr>
                            <td>" . ARRAY_SETORES[$k] . "</td>
                            <td>" . $parcial . "</td>
                            <td>" . $porcentagem . "%</td>
                        </tr>";
                    }
                }
                $retorno .= "</tbody></table></fieldset><br>";


                $retorno .= "
                <fieldset class=\"preg\">
                    <h5>SUBTOTAIS POR GRUPO (beta)</h5>
                    <table class=\"prod\">
                        <thead>
                            <tr>
                                <th>Grupo</th>
                                <th>Total</th>
                                <th>Porcentagem</th>
                            </tr>
                        </thead>
                        <tbody>";

                self::openConnection();
                $query_gr = $this->mysqli->query("SELECT pedido_grupo.id_grupo, setores_grupos.nome AS ng, pedido.valor {$empenho} FROM {$tb_empenho} setores, setores_grupos, pedido, prioridade, status, pedido_grupo WHERE setores_grupos.id = pedido_grupo.id_grupo AND pedido_grupo.id_pedido = pedido.id AND status.id = pedido.status {$where_setor} {$where_prioridade} {$where_empenho} AND prioridade.id = pedido.prioridade AND pedido.id_setor = setores.id {$where_status} AND pedido.data_pedido BETWEEN '{$dataIni}' AND '{$dataFim}'") or exit('Erro ao gerar subrelatorio de grupos');
                $this->mysqli = NULL;

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
                for ($i = 0; $i < $count; $i++) {
                    $parcial = number_format($array_gr[$gr_indexes[$i]], 3, ',', '.');
                    $porcentagem = number_format(($array_gr[$gr_indexes[$i]] * 100) / $tot->total, 3, ',', '.');
                    $retorno .= "
                    <tr>
                        <td>" . utf8_encode($gr_names[$gr_indexes[$i]]) . "</td>
                        <td>" . $parcial . "</td>
                        <td>" . $porcentagem . "%</td>
                    </tr>";
                }

                $retorno .= "</tbody></table></fieldset><br>";
            }
        }
        if ($status == 8) {
            $retorno .= "
                <br><br><br>
                <h5 class=\"ass\" style=\"margin-right: 50%; margin-bottom: 0;\">
                _______________________________________________<br>
                RESPONSÁVEL PELA INFORMAÇÃO
                </h5>
                <h5 class=\"ass\" style=\"margin-left: 51%; margin-top: -32px;\">
                _______________________________________________<br>
                RESPONSÁVEL PELO RECEBIMENTO
                </h5><br><br>
                <h4 style=\"text-align: center\" class=\"ass\">Santa Maria, ___ de ___________________ de _____.</h4>";
        }
        $this->obj_Busca = NULL;
        return $retorno;
    }

    /**
     * 	Função que retorna um relatório para a recepção dos processos (ajustar)
     *
     * 	@return string
     */
    public function getRelatorioProcessos(int $tipo): string {
        $retorno = "";
        // tratando tipo == 0 primeiro, buscando TODOS os processos
        $where = "";
        if ($tipo != 0) {
            $where = "AND processos.tipo = " . $tipo;
        }
        self::openConnection();
        if ($where == "") {
            $query_proc = $this->mysqli->query("SELECT processos_tipo.id, processos_tipo.nome FROM processos_tipo;") or exit("Erro ao buscar os tipos de processo.");
            while ($tipo_proc = $query_proc->fetch_object()) {
                $query = $this->mysqli->query("SELECT processos.num_processo, processos_tipo.nome AS tipo, processos_tipo.id AS id_tipo, processos.estante, processos.prateleira, processos.entrada, processos.saida, processos.responsavel, processos.retorno, processos.obs FROM processos, processos_tipo WHERE processos.tipo = processos_tipo.id AND processos.tipo = {$tipo_proc->id} ORDER BY processos.tipo ASC;") or exit("Erro ao buscar os processos.");
                if ($query->num_rows > 0) {
                    $retorno .= "
                        <fieldset class=\"preg\">
                                <table>
                                        <tr>
                                            <td>Tipo: " . $tipo_proc->nome . "</td>
                                        </tr>
                                </table>
                        </fieldset><br>
                        <table class=\"prod\">
                            <thead>
                                <tr>
                                    <th>Processo</th>
                                    <th>Tipo</th>
                                    <th>Estante</th>
                                    <th>Prateleira</th>
                                    <th>Entrada</th>
                                    <th>Saída</th>
                                    <th>Responsável</th>
                                    <th>Retorno</th>
                                    <th>Obs</th>
                                </tr>
                            </thead>
                            <tbody>";
                    while ($processo = $query->fetch_object()) {
                        $retorno .= "
                                <tr>
                                    <td>" . $processo->num_processo . "</td>
                                    <td>" . $processo->tipo . "</td>
                                    <td>" . $processo->estante . "</td>
                                    <td>" . $processo->prateleira . "</td>
                                    <td>" . $processo->entrada . "</td>
                                    <td>" . $processo->saida . "</td>
                                    <td>" . $processo->responsavel . "</td>
                                    <td>" . $processo->retorno . "</td>
                                    <td>" . $processo->obs . "</td>
                                </tr>";
                    }
                    $retorno .= "
                            </tbody>
                    </table><br>";
                }
            }
        } else {
            $query_proc = $this->mysqli->query("SELECT processos_tipo.nome FROM processos_tipo WHERE processos_tipo.id = {$tipo};") or exit("Erro ao buscar os tipos de processo.");
            $tipo_proc = $query_proc->fetch_object();
            $retorno .= "
                <fieldset class=\"preg\">
                    <table>
                        <tr>
                            <td>Tipo: " . $tipo_proc->nome . "</td>
                        </tr>
                    </table>
                </fieldset><br>
                <table class=\"prod\">
                    <thead>
                        <tr>
                            <th>Processo</th>
                            <th>Tipo</th>
                            <th>Estante</th>
                            <th>Prateleira</th>
                            <th>Entrada</th>
                            <th>Saída</th>
                            <th>Responsável</th>
                            <th>Retorno</th>
                            <th>Obs</th>
                        </tr>
                    </thead>
                    <tbody>";
            $query = $this->mysqli->query("SELECT processos.num_processo, processos_tipo.nome AS tipo, processos_tipo.id AS id_tipo, processos.estante, processos.prateleira, processos.entrada, processos.saida, processos.responsavel, processos.retorno, processos.obs FROM processos, processos_tipo WHERE processos.tipo = processos_tipo.id {$where} ORDER BY processos.tipo ASC;") or exit("Erro ao buscar os processos.");
            if ($query->num_rows > 0) {
                while ($processo = $query->fetch_object()) {
                    $retorno .= "
                        <tr>
                            <td>" . $processo->num_processo . "</td>
                            <td>" . $processo->tipo . "</td>
                            <td>" . $processo->estante . "</td>
                            <td>" . $processo->prateleira . "</td>
                            <td>" . $processo->entrada . "</td>
                            <td>" . $processo->saida . "</td>
                            <td>" . $processo->responsavel . "</td>
                            <td>" . $processo->retorno . "</td>
                            <td>" . $processo->obs . "</td>
                        </tr>";
                }
            }
            $retorno .= "
                    </tbody>
            </table><br>";
        }
        $this->mysqli = NULL;
        return $retorno;
    }

}
