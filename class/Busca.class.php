<?php

/**
 *  Classe com as funções de busca utilizadas principalmente pelo arquivo php/busca.php
 *  qualquer função que RETORNE dados do banco, devem ser feitas nesta classe
 *
 *  @author João Bolsson
 *  @since 2016, 16 Mar
 */
ini_set('display_erros', true);
error_reporting(E_ALL);

include_once 'Conexao.class.php';
include_once 'Util.class.php';

class Busca extends Conexao {

    private $mysqli, $obj_Util;

    function __construct() {
        //chama o método contrutor da classe Conexao
        parent::__construct();
        //atribuindo valor a variavel que realiza as consultas
        $this->mysqli = parent::getConexao();
        $this->obj_Util = new Util();
    }

    public function getTotalByStatus(int $status): string {
        $query = $this->mysqli->query("SELECT sum(pedido.valor) AS total FROM pedido WHERE pedido.status = {$status};");
        $tot = $query->fetch_object();
        $tot->total = number_format($tot->total, 3, ',', '.');
        return "Totalizando R$ " . $tot->total;
    }

    /**
     * 	Função para retornar uma lista de pedidos conforme o relatório
     *
     * 	@access public
     * 	@param $status Status da lista de pedidos no relatório
     * 	@return Lista de pedidos conforme o solicitado
     */
    public function getRelatorio(int $status): string {
        $retorno = "";
        $order = "ORDER BY pedido.id DESC";
        $alteracao = "AND pedido.alteracao = 0";
        if ($status == 3) {
            // reprovado
            $alteracao = "AND pedido.alteracao = 1";
        } else if ($status == 5) {
            // aguarda orçamento
            // por nível de prioridade e por montante (pedidos urgentes que somam até R$ 100.000)
            $order = "ORDER BY pedido.id DESC, pedido.prioridade DESC, pedido.valor DESC";
        } else if ($status == 2) {
            // em análise
            $order = "ORDER BY pedido.id DESC, pedido.prioridade DESC";
        }
        $query = $this->mysqli->query("SELECT pedido.id, DATE_FORMAT(pedido.data_pedido, '%d/%m/%Y') AS data_pedido, mes.sigla_mes AS ref_mes, prioridade.nome AS prioridade, status.nome AS status, pedido.valor FROM pedido, mes, prioridade, status WHERE prioridade.id = pedido.prioridade AND status.id = pedido.status AND mes.id = pedido.ref_mes AND pedido.status = {$status} {$alteracao} {$order};");
        while ($pedido = $query->fetch_object()) {
            $empenho = Busca::verEmpenho($pedido->id);
            $btnVerProcesso = "";
            if ($status == 2) {
                // em análise
                // por ordem descrecente do término do processo
                // botão para os processos presentes no pedido e suas datas
                $btnVerProcesso = "
                    <button class=\"btn btn-default btn-sm\" style=\"text-transform: none !important;font-weight: bold;\" onclick=\"verProcessos(" . $pedido->id . ");\" title=\"Ver Processos\"><span class=\"icon\">remove_red_eye</span></button>";
            }
            $pedido->valor = number_format($pedido->valor, 3, ',', '.');
            $retorno .= "
                <tr>
                    <td>" . $pedido->id . "</td>
                    <td>" . $pedido->ref_mes . "</td>
                    <td>" . $pedido->data_pedido . "</td>
                    <td>" . $pedido->prioridade . "</td>
                    <td><span class=\"label\" style=\"font-size: 11pt;\">" . $pedido->status . "</span></td>
                    <td>" . $empenho . "</td>
                    <td>R$ " . $pedido->valor . "</td>
                    <td>
                        <button class=\"btn btn-default btn-sm\" style=\"text-transform: none !important;font-weight: bold;\" onclick=\"imprimir(" . $pedido->id . ");\" title=\"Imprimir\"><span class=\"icon\">print</span></button>
                            " . $btnVerProcesso . "
                    </td>
                </tr>";
        }
        $query->close();
        return $retorno;
    }

    /**
     * 	Função para retornar os processos que estão nos pedidos com suas datas de vencimento
     *
     * 	@access public
     * 	@param $pedido Id do pedido
     * 	@return Uma tabela com os processos e as informações dele
     */
    public function getProcessosPedido(int $pedido): string {
        $query = $this->mysqli->query("SELECT DISTINCT itens.num_processo, itens.dt_fim FROM itens, itens_pedido WHERE itens_pedido.id_pedido = {$pedido} AND itens_pedido.id_item = itens.id;");
        $retorno = "";
        while ($processo = $query->fetch_object()) {
            $retorno .= "
                <tr>
                    <td>" . $processo->num_processo . "</td>
                    <td>" . $processo->dt_fim . "</td>
                </tr>";
        }
        return $retorno;
    }

    /**
     * 	Função para retornar os radios buttons para gerar relatórios por status
     *
     * 	@access public
     * 	@return Colunas com alguns status
     */
    public function getRadiosStatusRel(): string {
        $retorno = "<tr>";
        $i = 0;
        $cont = 5;
        $query = $this->mysqli->query("SELECT status.id, status.nome FROM status WHERE status.id <> 1;");
        while ($status = $query->fetch_object()) {
            if ($i == $cont) {
                $i = 0;
                $retorno .= "</tr><tr>";
            }
            $retorno .= "
            <td>
                <div class=\"radiobtn radiobtn-adv\">
                    <label for=\"relStatus{$status->id}\">
                    	<input type=\"radio\" name=\"relatorio\" id=\"relStatus{$status->id}\" class=\"access-hide\" onchange=\"changeReport('status-{$status->id}');\">{$status->nome}
                        <span class=\"radiobtn-circle\"></span><span class=\"radiobtn-circle-check\"></span>
                    </label>
                </div>
            </td>";
            $i++;
        }
        $retorno .= "</tr>";
        $query->close();
        return $retorno;
    }

    /**
     * 	Função para retornar os problemas relatados
     *
     * 	@access public
     * 	@return string
     */
    public function getProblemas(): string {
        $query = $this->mysqli->query("SELECT setores.nome AS setor, problemas.assunto, problemas.descricao FROM setores, problemas WHERE setores.id = problemas.id_setor ORDER BY problemas.id DESC;");
        $retorno = "";
        while ($problema = $query->fetch_object()) {
            $problema->descricao = $this->mysqli->real_escape_string($problema->descricao);
            $problema->descricao = str_replace("\"", "'", $problema->descricao);
            $retorno .= "
                <tr>
                    <td>" . $problema->setor . "</td>
                    <td>" . $problema->assunto . "</td>
                    <td>
                        <button onclick=\"viewCompl('" . $problema->descricao . "');\" class=\"btn btn-flat waves-attach waves-effect\" type=\"button\" title=\"Descrição\">Descrição</button>
                    </td>
                </tr>";
        }
        $query->close();
        return $retorno;
    }

    /**
     * 	Função que retonar o relatorio de pedidos
     *
     * 	@access public
     * 	@return string
     */
    public function getRelatorioPedidos(int $id_setor, int $prioridade, int $status, string $dataI, string $dataF): string {
        $retorno = "";
        $where_status = "AND pedido.status = " . $status;
        $where_prioridade = "AND pedido.prioridade = " . $prioridade;
        $where_setor = "AND pedido.id_setor = " . $id_setor;
        if ($status == 0) {
            $where_status = '';
        }
        if ($prioridade == 0) {
            $where_prioridade = '';
        }
        if ($id_setor == 0) {
            $where_setor = '';
        }
        $dataIni = $this->obj_Util->dateFormat($dataI);
        $dataFim = $this->obj_Util->dateFormat($dataF);
        $where_empenho = "";
        $tb_empenho = "";
        $empenho = "";
        if ($status == 8) {
            $where_empenho = "AND pedido_empenho.id_pedido = pedido.id";
            $tb_empenho = "pedido_empenho, ";
            $empenho = ", pedido_empenho.empenho";
        }
        $query = $this->mysqli->query("SELECT pedido.id, setores.nome AS setor, DATE_FORMAT(pedido.data_pedido, '%d/%m/%Y') AS data_pedido, mes.sigla_mes AS mes, prioridade.nome AS prioridade, status.nome AS status, pedido.valor {$empenho} FROM {$tb_empenho} pedido, setores, mes, prioridade, status WHERE mes.id = pedido.ref_mes AND setores.id = pedido.id_setor AND prioridade.id = pedido.prioridade AND status.id = pedido.status {$where_setor} {$where_prioridade} {$where_empenho} AND pedido.alteracao = 0 {$where_status} AND pedido.data_pedido BETWEEN '{$dataIni}' AND '{$dataFim}';");
        $titulo = "Relatório de Pedidos por Setor e Nível de Prioridade";
        if ($query) {
            $thead = "
                <th>Data</th>
                <th>Mês</th>
                <th>Prioridade</th>
                <th>Status</th>
                <th>Valor</th>";
            if ($status == 8) {
                $titulo = "Relatório de Empenhos Enviados ao Ordenador";
                $thead = "
                    <th>Prioridade</th>
                    <th>SIAFI</th>";
            }
            $query_tot = $this->mysqli->query("SELECT sum(pedido.valor) AS total FROM pedido WHERE 1>0 {$where_setor} {$where_prioridade} {$where_empenho} AND pedido.alteracao = 0 {$where_status} AND pedido.data_pedido BETWEEN '{$dataIni}' AND '{$dataFim}';");
            $total = '';
            if ($query_tot) {
                $tot = $query_tot->fetch_object();
                $total = "R$ " . $tot->total;
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
                            <th>Setor</th>
                            " . $thead . "
                        </tr>
                    </thead>
                    <tbody>";
            while ($pedido = $query->fetch_object()) {
                $tbody = "";
                if ($status == 8) {
                    $tbody = "
                        <td>" . $pedido->prioridade . "</td>
                        <td>" . $pedido->empenho . "</td>";
                } else {
                    $tbody = "
                        <td>" . $pedido->data_pedido . "</td>
                        <td>" . $pedido->mes . "</td>
                        <td>" . $pedido->prioridade . "</td>
                        <td>" . $pedido->status . "</td>
                        <td>R$ " . $pedido->valor . "</td>";
                }
                $retorno .= "
                        <tr>
                            <td>" . $pedido->id . "</td>
                            <td>" . $pedido->setor . "</td>
                            " . $tbody . "
                        </tr>";
            }
            $query->close();
            $retorno .= "<tbody></table>";
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
        return $retorno;
    }

    /**
     * 	Função que retorna os pedidos em análise e o total deles
     *
     * 	@access public
     * 	@param $id_setor id do setor
     * 	@return string
     */
    public function getPedidosAnalise(int $id_setor): string {
        $retorno = "";
        $query = $this->mysqli->query("SELECT pedido.valor FROM pedido WHERE pedido.id_setor = {$id_setor} AND pedido.status = 2;");
        if ($query->num_rows > 0) {
            $soma = 0;
            while ($obj = $query->fetch_object()) {
                $soma += $obj->valor;
            }
            $soma = number_format($soma, 3, ',', '.');
            $retorno = "
            <tr>
                <td colspan=\"2\">Você tem " . $query->num_rows . " pedido(s) em análise no total de R$ " . $soma . "</td>
                <td></td>
            </tr>";
        }
        $query->close();
        return $retorno;
    }

    /**
     * 	Função que que retorna informações de um item para possível edição
     *
     * 	@access public
     * 	@param Id do item da tbela itens
     * 	@return object
     */
    public function getInfoItem($id_item) {
        $query = $this->mysqli->query("SELECT itens.complemento_item, replace(itens.vl_unitario, ',', '.') AS vl_unitario, itens.qt_contrato, replace(itens.vl_contrato, ',', '.') AS vl_contrato, itens.qt_utilizado, replace(itens.vl_utilizado, ',', '.') AS vl_utilizado, itens.qt_saldo, replace(itens.vl_saldo, ',', '.') AS vl_saldo FROM itens WHERE itens.id = {$id_item};");
        $obj = $query->fetch_object();
        $query->close();
        return json_encode($obj);
    }

    /**
     * 	Função que retornar o empenho de um pedido
     *
     * 	@access public
     * 	@param $id_pedido Id do pedido.
     * 	@return string
     */
    public function verEmpenho(int $id_pedido): string {
        $retorno = '';
        $query = $this->mysqli->query("SELECT empenho FROM pedido_empenho WHERE id_pedido = {$id_pedido};");
        if ($query->num_rows < 1) {
            $retorno = 'EMPENHO SIAFI PENDENTE';
        } else {
            $obj = $query->fetch_object();
            $retorno = $obj->empenho;
        }
        $query->close();
        return $retorno;
    }

    /**
     * 	Função que retorna a tabela com os lançamentos de saldos pelo SOF
     *
     * 	@access public
     * 	@param $id_setor id do setor
     * 	@return string
     */
    public function getLancamentos(int $id_setor): string {
        $retorno = "";
        $where = "";
        if ($id_setor != 0) {
            $where = "AND saldos_lancamentos.id_setor = " . $id_setor;
        }
        $query = $this->mysqli->query("SELECT setores.nome, DATE_FORMAT(saldos_lancamentos.data, '%d/%m/%Y') AS data, saldos_lancamentos.valor, saldo_categoria.nome AS categoria FROM setores, saldos_lancamentos, saldo_categoria WHERE setores.id = saldos_lancamentos.id_setor {$where} AND saldos_lancamentos.categoria = saldo_categoria.id ORDER BY saldos_lancamentos.id DESC;");
        $cor = '';
        while ($lancamento = $query->fetch_object()) {
            if ($lancamento->valor < 0) {
                $cor = 'red';
            } else {
                $cor = 'green';
            }
            $lancamento->valor = number_format($lancamento->valor, 3, ',', '.');
            $retorno .= "
                <tr>
                    <td>" . $lancamento->nome . "</td>
                    <td>" . $lancamento->data . "</td>
                    <td style=\"color: " . $cor . ";\">R$ " . $lancamento->valor . "</td>
                    <td>" . $lancamento->categoria . "</td>
                </tr>";
        }
        return $retorno;
    }

    function getTotalInOutSaldos(int $id_setor): string {
        $entrada = $saida = 0;
        $where = "";
        if ($id_setor != 0) {
            $where = "AND saldos_lancamentos.id_setor = " . $id_setor;
        }
        $query = $this->mysqli->query("SELECT saldos_lancamentos.valor FROM setores, saldos_lancamentos WHERE setores.id = saldos_lancamentos.id_setor {$where};");
        while ($lancamento = $query->fetch_object()) {
            if ($lancamento->valor < 0) {
                $saida -= $lancamento->valor;
            } else {
                $entrada += $lancamento->valor;
            }
        }
        $entrada = number_format($entrada, 3, ',', '.');
        $saida = number_format($saida, 3, ',', '.');
        $array = array('entrada' => "Total de Entradas: R$ " . $entrada,
            'saida' => "Total de Saídas: R$ " . $saida);

        return json_encode($array);
    }

    /**
     * 	Função que retorna as options com os setores cadastrados no sistema
     *
     * 	@access public
     * 	@return string
     */
    public function getOptionsSetores(): string {
        $retorno = "";
        $query = $this->mysqli->query("SELECT setores.id, setores.nome FROM setores WHERE setores.id <> 1;");
        while ($setor = $query->fetch_object()) {
            $retorno .= "<option value=\"" . $setor->id . "\">" . $setor->nome . "</option>";
        }
        $query->close();
        return $retorno;
    }

    /**
     * 	Função que retornar as options com as prioridades existentes no sistemas para os pedidos
     *
     * 	@access public
     * 	@return string
     */
    public function getOptionsPrioridades(): string {
        $retorno = "";
        $query = $this->mysqli->query("SELECT prioridade.id, prioridade.nome FROM prioridade WHERE prioridade.nome <> 'rascunho';");
        while ($prioridade = $query->fetch_object()) {
            $retorno .= "<option value=\"" . $prioridade->id . "\">" . $prioridade->nome . "</option>";
        }
        $query->close();
        return $retorno;
    }

    /**
     * 	Função que retorna as options com os status de pedidos
     *
     * 	@access public
     * 	@return string
     */
    public function getOptionsStatus(): string {
        $retorno = "";
        $query = $this->mysqli->query("SELECT status.id, status.nome FROM status WHERE status.nome <> 'Rascunho';");
        if ($query && $query->num_rows > 0) {
            while ($status = $query->fetch_object()) {
                $retorno .= "<option value=\"" . $status->id . "\">" . $status->nome . "</option>";
            }
        }
        return $retorno;
    }

    /**
     * 	Função que retorna um relatório para a recepção dos processos (ajustar)
     *
     * 	@access public
     * 	@return string
     */
    public function getRelatorioProcessos(int $tipo): string {
        $retorno = "";
        // tratando tipo == 0 primeiro, buscando TODOS os processos
        $where = "";
        if ($tipo != 0) {
            $where = "AND processos.tipo = " . $tipo;
        }
        if ($where == "") {
            $query_proc = $this->mysqli->query("SELECT processos_tipo.id, processos_tipo.nome FROM processos_tipo;");
            while ($tipo_proc = $query_proc->fetch_object()) {
                $query = $this->mysqli->query("SELECT processos.num_processo, processos_tipo.nome AS tipo, processos_tipo.id AS id_tipo, processos.estante, processos.prateleira, processos.entrada, processos.saida, processos.responsavel, processos.retorno, processos.obs FROM processos, processos_tipo WHERE processos.tipo = processos_tipo.id AND processos.tipo = {$tipo_proc->id} ORDER BY processos.tipo ASC;");
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
            $query_proc = $this->mysqli->query("SELECT processos_tipo.nome FROM processos_tipo WHERE processos_tipo.id = {$tipo};");
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
            $query = $this->mysqli->query("SELECT processos.num_processo, processos_tipo.nome AS tipo, processos_tipo.id AS id_tipo, processos.estante, processos.prateleira, processos.entrada, processos.saida, processos.responsavel, processos.retorno, processos.obs FROM processos, processos_tipo WHERE processos.tipo = processos_tipo.id {$where} ORDER BY processos.tipo ASC;");
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
        return $retorno;
    }

    /**
     * 	Função que retorna as options para tipo de processos para o cadastro de novos processos.
     *
     * 	@access public
     * 	@return string
     */
    public function getTiposProcessos(): string {
        $retorno = "";
        $query = $this->mysqli->query("SELECT id, nome FROM processos_tipo;");
        while ($tipo = $query->fetch_object()) {
            $tipo->nome = utf8_encode($tipo->nome);
            $retorno .= "<option value=\"" . $tipo->id . "\">" . $tipo->nome . "</option>";
        }
        $query->close();
        return $retorno;
    }

    /**
     * 	Função que retornar o id do setor do pedido
     *
     * 	@access public
     * 	@param $id_pedido Id do pedido.
     * 	@return int.
     */
    public function getSetorPedido(int $id_pedido): int {
        $query = $this->mysqli->query("SELECT id_setor FROM pedido WHERE id = {$id_pedido};");
        $obj = $query->fetch_object();
        $query->close();
        return $obj->id_setor;
    }

    /**
     * 	Função que retornar se um pedido é ou não rascunho
     *
     * 	@access public
     * 	@param $id_pedido id do pedido
     * 	@return bool
     */
    public function getRequestDraft(int $id_pedido): bool {
        $query = $this->mysqli->query("SELECT prioridade.nome FROM pedido, prioridade WHERE pedido.id = 3 AND pedido.prioridade = prioridade.id;");
        $obj = $query->fetch_object();
        $query->close();
        if ($obj->nome == 'rascunho') {
            return true;
        }
        return false;
    }

    /**
     * 	Função que constroi os radioBtn da análise dos pedidos.
     *
     * 	@access public
     * 	@param $cont Número de radioBtn por linha.
     * 	@return string
     */
    public function getStatus(int $cont): string {
        $retorno = "<tr>";
        $i = 0;
        $query = $this->mysqli->query("SELECT status.id, status.nome FROM status WHERE status.id <> 1;");
        while ($status = $query->fetch_object()) {
            if ($i == $cont) {
                $i = 0;
                $retorno .= "</tr><tr>";
            }
            $retorno .= "
                <td>
                    <div class=\"radiobtn radiobtn-adv\">
                        <label for=\"st" . $status->id . "\">
                            <input type=\"radio\" name=\"fase\" required id=\"st" . $status->id . "\" class=\"access-hide\" value=\"" . $status->id . "\">" . $status->nome . "
                            <span class=\"radiobtn-circle\"></span><span class=\"radiobtn-circle-check\"></span>
                        </label>
                    </div>
                </td>";
            $i++;
        }
        $retorno .= "</tr>";
        $query->close();
        return $retorno;
    }

    /**
     * 	Função que retornar os radioBtn das prioridades dos pedidos.
     *
     * 	@access public
     * 	@return string
     */
    public function getPrioridades(): string {
        $retorno = "";
        $query = $this->mysqli->query("SELECT id, nome FROM prioridade;");
        while ($prioridade = $query->fetch_object()) {
            $retorno .= "
                <td>
                    <div class=\"radiobtn radiobtn-adv\">
                        <label for=\"st" . $prioridade->nome . "\">
                            <input type=\"radio\" name=\"st\" id=\"st" . $prioridade->nome . "\" class=\"access-hide\" checked=\"\" value=\"" . $prioridade->id . "\">" . $prioridade->nome . "
                            <span class=\"radiobtn-circle\"></span><span class=\"radiobtn-circle-check\"></span>
                        </label>
                    </div>
                </td>";
        }
        $query->close();
        return $retorno;
    }

    /**
     *   Função utilizada para retornar as informações de um processo clicado da tabela da recepção
     *
     *   @access public
     *   @return string
     */
    public function getInfoProcesso(int $id_processo): string {
        $query = $this->mysqli->query("SELECT processos.num_processo, processos.tipo, processos.estante, processos.prateleira, processos.entrada, processos.saida, processos.responsavel, processos.retorno, processos.obs FROM processos WHERE processos.id = {$id_processo};");
        $obj = $query->fetch_object();
        $query->close();
        return json_encode($obj);
    }

    /**
     *   Função utilizada para retornar a tabela dos processos da recepção
     *
     *   @access public
     *   @return string
     */
    public function getTabelaRecepcao(): string {
        $retorno = "";
        $query = $this->mysqli->query("SELECT processos.id, processos.num_processo, processos_tipo.nome as tipo, processos.estante, processos.prateleira, processos.entrada, processos.saida, processos.responsavel, processos.retorno, processos.obs FROM processos, processos_tipo WHERE processos.tipo = processos_tipo.id ORDER BY id ASC;");

        while ($processo = $query->fetch_object()) {
            $processo->obs = $this->mysqli->real_escape_string($processo->obs);
            $processo->obs = str_replace("\"", "'", $processo->obs);
            $retorno .= "
                <tr>
                    <td>
                        <a class=\"modal-close\" href=\"javascript:addProcesso('', " . $processo->id . ");\"><span class=\"icon\">mode_edit</span></a>
                    </td>
                    <td>" . $processo->num_processo . "</td>
                    <td>" . $processo->tipo . "</td>
                    <td>" . $processo->estante . "</td>
                    <td>" . $processo->prateleira . "</td>
                    <td>" . $processo->entrada . "</td>
                    <td>" . $processo->saida . "</td>
<td>" . $processo->responsavel . "</td>
                    <td>" . $processo->retorno . "</td>
                    <td>
                            <button onclick=\"viewCompl('" . $processo->obs . "');\" class=\"btn btn-flat waves-attach waves-effect\" type=\"button\" title=\"Ver Observação\">OBS</button>
                    </td>
                </tr>";
        }
        $query->fetch_object();
        return $retorno;
    }

    /**
     * 	Função que retorna as permissões do usuario
     *
     * 	@access public
     * 	@return object
     */
    public function getPermissoes(int $id_user) {
        $query = $this->mysqli->query("SELECT usuario_permissoes.noticias, usuario_permissoes.saldos, usuario_permissoes.pedidos, usuario_permissoes.recepcao FROM usuario_permissoes WHERE usuario_permissoes.id_usuario = {$id_user};");
        $obj_permissoes = $query->fetch_object();
        $query->close();
        return $obj_permissoes;
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

    /**
     * Função que busca os detalhes de uma notícia completa
     *
     * @access public
     * @return string
     */
    public function getInfoNoticia(int $id): string {
        //declarando retorno
        $retorno = "";
        $query = $this->mysqli->query("SELECT postagem FROM postagens WHERE id = {$id};");
        $noticia = $query->fetch_object();
        return html_entity_decode($noticia->postagem);
    }

    /**
     * 	script temporário
     *
     */
    public function upPostagens() {
        $query_teste = $this->mysqli->query("SELECT id FROM postagens;");
        if ($query_teste->num_rows < 1) {
            $query_tabelas = $this->mysqli->query("SELECT tabela FROM paginas_post;");
            while ($tabela = $query_tabelas->fetch_object()) {
                $query = $this->mysqli->query("SELECT {$tabela->tabela}.postagem, {$tabela->tabela}.data, {$tabela->tabela}.ativa FROM {$tabela->tabela};");
                if ($query->num_rows > 0) {
                    while ($postagem = $query->fetch_object()) {
                        $postagem->postagem = $this->mysqli->real_escape_string($postagem->postagem);

                        $inicio = strpos($postagem->postagem, "<h3");
                        $fim = strpos($postagem->postagem, "</h3>");

                        $titulo = strip_tags(substr($postagem->postagem, $inicio, $fim));
                        $this->mysqli->query("INSERT INTO postagens VALUES(NULL, '{$tabela->tabela}', '{$titulo}', '{$postagem->data}', {$postagem->ativa}, '{$postagem->postagem}');");
                    }
                }
            }
            while ($postagem = $query->fetch_object()) {
                $obj = $this->mysqli->query("SELECT {$postagem->tabela}.postagem FROM {$postagem->tabela} WHERE {$postagem->tabela}.id = {$postagem->id_postagem};")->fetch_object();
                $obj->postagem = $this->mysqli->real_escape_string($obj->postagem);
                $this->mysqli->query("UPDATE postagens SET postagem = '{$obj->postagem}' WHERE postagens.id_postagem = {$postagem->id_postagem} AND postagens.tabela = '{$postagem->tabela}';");
            }
        }
    }

    /**
     * Função para mostrar uma tabela com todas as publicações de certa página
     *
     * @access public
     * @param $tabela -> filtra por nome da tabela
     * @return string
     */
    public function getPostagens($tabela): string {
        //declarando retorno
        $retorno = "";
        $query = $this->mysqli->query("SELECT postagens.id, postagens.titulo, DATE_FORMAT(postagens.data, '%d/%m/%Y') AS data FROM postagens, paginas_post WHERE postagens.tabela = paginas_post.id AND paginas_post.tabela = '{$tabela}' AND ativa = 1 ORDER BY data ASC;");
        $i = 0;
        while ($postagem = $query->fetch_object()) {
            $retorno .= "<tr><td>";
            $retorno .= html_entity_decode($postagem->titulo);

            $retorno .= "<button class=\"btn btn-flat btn-sm\" style=\"text-transform: lowercase !important;font-weight: bold;\" onclick=\"ver_noticia(" . $postagem->id . ", '" . $tabela . "', 0);\">...ver mais</button></td>";
            $retorno .= "<td><span style=\"font-weight: bold;\" class=\"pull-right\">" . $postagem->data . "</span></td></tr>";
        }
        return $retorno;
    }

    /**
     * Função para popular os slides na página inicial
     *
     * @access public
     * @param $slide (1 ou 2)-> o primeiro mostra as últimas notícias e o segundo aleatórias
     * @return string
     */
    public function getSlide(int $slide): string {
        //declarando var order
        $order = "";
        if ($slide == 1) {
            $order = "postagens.data DESC";
        } else {
            $order = "rand()";
        }
        //declarando retorno
        $retorno = "";
        $array_anima = array("primeira", "segunda", "terceira", "quarta", "quinta");
        $array_id = array("primeiro", "segundo", "terceiro", "quarto", "quinto");
        $query_postagem = $this->mysqli->query("SELECT postagens.id, postagens.postagem, DATE_FORMAT(postagens.data, '%d/%m/%Y') AS data, paginas_post.tabela, postagens.titulo FROM postagens, paginas_post WHERE postagens.tabela = paginas_post.id AND postagens.ativa = 1 ORDER BY {$order} LIMIT 5;");
        //variável para contar
        $aux = 0;
        while ($postagem = $query_postagem->fetch_object()) {
            $array_post = str_split($postagem->titulo);
            $pos = strlen($postagem->titulo);
            $titulo = "";
            for ($i = 0; $i < $pos; $i++) {
                $titulo .= $array_post[$i];
            }
            $array_post = str_split($postagem->postagem);
            $pos = strpos($postagem->postagem, "<img");
            $src = "../sof_files/logo_blue.png";
            if ($pos !== false) {
                $pos = strpos($postagem->postagem, "src=\"");
                $src = "";
                $i = $pos + 5;
                while ($array_post[$i] != "\"") {
                    $src .= $array_post[$i];
                    $i++;
                }
            }
            $width = "550";

            $pos = strpos($postagem->postagem, "width: ");
            $posu = strpos($postagem->postagem, "px;");
            if ($postagem->tabela != "noticia" || $postagem->id != 8) {
                if ($pos !== false) {
                    if ($posu !== false) {
                        for ($i = $pos; $i < $posu; $i++) {
                            $width .= $array_post[$i];
                        }
                    }
                }
            }
            $retorno .= "
                <li id=\"" . $array_id[$aux] . "\" class=\"" . $array_anima[$aux] . "-anima\">
                    <div class=\"card-img\">
                        <img style=\"width: " . $width . "px; height: 275px;\" src=\"" . $src . "\" >
                        <a href=\"javascript:ver_noticia(" . $postagem->id . ", '" . $postagem->tabela . "', 1);\" class=\"card-img-heading padding\" style=\"font-weight: bold;\">" . $titulo . "<span class=\"pull-right\">" . $postagem->data . "</span></a>
                    </div>
                </li>";
            $aux++;
        }
        return $retorno;
    }

    /**
     * Função para pesquisar alguma publicação
     *
     * @access public
     * @return string
     */
    public function pesquisar(string $busca): string {
        //declarando retorno
        $retorno = "";
        $busca = htmlentities($busca);
        //escapando string especiais para evitar SQL Injections
        $busca = $this->mysqli->real_escape_string($busca);
        $retorno = "
            <div class=\"card\">
                <div class=\"card-main\">
                    <div class=\"card-header card-brand\">
                        <div class=\"card-header-side pull-left\">
                            <p class=\"card-heading\">Publicações</p>
                        </div>
                    </div><!--  ./card-header -->
                    <div class=\"card-inner margin-top-no\">
                        <div class=\"card-table\">
                            <div class=\"table-responsive\">
                                <table class=\"table\">
                                    <thead>
                                        <th>Título</th>
                                        <th class=\"pull-right\">Data de Publicação</th>
                                    </thead>
                                    <tbody>";
        $query = $this->mysqli->query("SELECT postagens.id, postagens.tabela, postagens.titulo, DATE_FORMAT(postagens.data, '%d/%m/%Y') AS data, postagens.ativa FROM postagens WHERE postagens.titulo LIKE '%{$busca}%' AND postagens.ativa = 1 ORDER BY postagens.data DESC;");
        if ($query->num_rows < 1) {
            $retorno .= "
                                        <tr>
                                            <td colspan=\"2\">Nenhum resultado para '" . $busca . "'</td>
                                            <td></td>
                                        </tr>";
        } else {
            while ($postagem = $query->fetch_object()) {
                $titulo = html_entity_decode($postagem->titulo);
                $retorno .= "
                                        <tr>
                                            <td>" . $titulo . "<button class=\"btn btn-flat btn-sm\" style=\"text-transform: lowercase !important;font-weight: bold;\" onclick=\"ver_noticia(" . $postagem->id . ", '" . $postagem->tabela . "', 1);\">...ver mais</button></td>
                                            <td><span class=\"pull-right\">" . $postagem->data . "</span></td>
                                        </tr>";
            }
        }
        $retorno .= "
                                    </tbody>
                                </table>
                            </div><!-- ./table-responsive -->
                        </div><!-- ./card-table -->
                    </div><!-- ./card-inner -->
                </div><!-- ./card-main -->
            </div> <!-- ./card -->";
        return $retorno;
    }

    /**
     * 	Função que retorna a tabela com as solicitações de alteração de pedidos
     * 	para o SOF analisar
     *
     * 	@access public
     * 	@return string
     */
    public function getAdminSolicAltPedidos(int $st): string {
        $retorno = "";
        $query = $this->mysqli->query("SELECT solic_alt_pedido.id, solic_alt_pedido.id_pedido, setores.nome, DATE_FORMAT(solic_alt_pedido.data_solicitacao, '%d/%m/%Y') AS data_solicitacao, DATE_FORMAT(solic_alt_pedido.data_analise, '%d/%m/%Y') AS data_analise, solic_alt_pedido.justificativa, solic_alt_pedido.status FROM solic_alt_pedido, setores WHERE solic_alt_pedido.id_setor = setores.id AND solic_alt_pedido.status = {$st};");
        $status = $label = "";
        while ($solic = $query->fetch_object()) {
            switch ($solic->status) {
                case 0:
                    $status = "Reprovado";
                    $label = "label-red";
                    break;
                case 1:
                    $status = "Aprovado";
                    $label = "label-green";
                    break;
                default:
                    $status = "Aberto";
                    $label = "label-orange";
                    $solic->data_analise = "--------------";
                    break;
            }
            $btn_aprovar = $btn_reprovar = "";
            if ($st == 2) {
                $btn_aprovar = "<a title=\"Aprovar\" href=\"javascript:analisaSolicAlt(" . $solic->id . ", " . $solic->id_pedido . ", 1);\" class=\"modal-close\"><span class=\"icon\">done_all<span></span></span></a>";
                $btn_reprovar = "<a title=\"Reprovar\" href=\"javascript:analisaSolicAlt(" . $solic->id . ", " . $solic->id_pedido . ", 0);\" class=\"modal-close\"><span class=\"icon\">delete<span></span></span></a>";
            }
            $solic->justificativa = $this->mysqli->real_escape_string($solic->justificativa);
            $solic->justificativa = str_replace("\"", "'", $solic->justificativa);
            $retorno .= "
                <tr>
                    <td>" . $btn_aprovar . $btn_reprovar . "</td>
                    <td>" . $solic->id_pedido . "</td>
                    <td>" . $solic->nome . "</td>
                    <td>" . $solic->data_solicitacao . "</td>
                    <td>" . $solic->data_analise . "</td>
                    <td>
                        <button onclick=\"viewCompl('" . $solic->justificativa . "');\" class=\"btn btn-flat waves-attach waves-effect\" type=\"button\" title=\"Ver Justificativa\">JUSTIFICATIVA</button>
                    </td>
                    <td><span class=\"label " . $label . "\" style=\"font-size: 11pt !important; font-weight: bold;\">" . $status . "</span></td>
                </tr>";
        }
        $query->close();
        return $retorno;
    }

    /**
     * 	Função que retorna as solicitações de adiantamentos de saldos enviadas ao SOF para análise
     *
     * 	@access public
     * 	@param $st Status
     * 	@return string
     */
    public function getSolicAdiantamentos(int $st): string {
        $query = $this->mysqli->query("SELECT saldos_adiantados.id, setores.nome, DATE_FORMAT(saldos_adiantados.data_solicitacao, '%d/%m/%Y') AS data_solicitacao, DATE_FORMAT(saldos_adiantados.data_analise, '%d/%m/%Y') AS data_analise, saldos_adiantados.valor_adiantado, saldos_adiantados.justificativa FROM saldos_adiantados, setores WHERE saldos_adiantados.id_setor = setores.id AND saldos_adiantados.status = {$st} ORDER BY saldos_adiantados.data_solicitacao DESC;");
        // declarando retorno
        $retorno = "";
        $status = $label = "";
        switch ($st) {
            case 0:
                $status = "Reprovado";
                $label = "label-red";
                break;
            case 1:
                $status = "Aprovado";
                $label = "label-green";
                break;
            case 2:
                $status = "Aberto";
                $label = "label-orange";
                break;
            default:
                break;
        }
        if ($query) {
            while ($solic = $query->fetch_object()) {
                $btn_aprovar = $btn_reprovar = "";
                if ($st == 2) {
                    // em análise / aberto
                    $solic->data_analise = "---------------";
                    //$solic->mes_subtraido = "---------------";
                    $btn_aprovar = "<a title=\"Aprovar\" href=\"javascript:analisaAdi(" . $solic->id . ", 1);\" class=\"modal-close\"><span class=\"icon\">done_all<span></span></span></a>";
                    $btn_reprovar = "<a title=\"Reprovar\" href=\"javascript:analisaAdi(" . $solic->id . ", 0);\" class=\"modal-close\"><span class=\"icon\">delete<span></span></span></a>";
                }
                $solic->justificativa = $this->mysqli->real_escape_string($solic->justificativa);
                $solic->justificativa = str_replace("\"", "'", $solic->justificativa);
                $solic->valor_adiantado = number_format($solic->valor_adiantado, 3, ',', '.');
                $retorno .= "
                    <tr>
                        <td>" . $btn_reprovar . $btn_aprovar . "</td>
                        <td>" . $solic->nome . "</td>
                        <td>" . $solic->data_solicitacao . "</td>
                        <td>" . $solic->data_analise . "</td>
                        <td>R$ " . $solic->valor_adiantado . "</td>
                        <td>
                            <button onclick=\"viewCompl('" . $solic->justificativa . "');\" class=\"btn btn-flat waves-attach waves-effect\" type=\"button\" title=\"Ver Justificativa\">JUSTIFICATIVA</button>
                        </td>
                        <td><span class=\"label " . $label . "\" style=\"font-size: 11pt !important; font-weight: bold;\">" . $status . "</span></td>
                    </tr>";
            }
        }
        $query->close();
        return $retorno;
    }

    /**
     * Função para retornar o cabeçalho do pdf do pedido
     *
     * @access public
     * @return string
     */
    public function getHeader(int $id_pedido): string {
        $pedido = $this->mysqli->query("SELECT pedido.id, DATE_FORMAT(pedido.data_pedido, '%d/%m/%Y') AS data_pedido, EXTRACT(YEAR FROM pedido.data_pedido) AS ano, mes.sigla_mes AS ref_mes, status.nome AS status, replace(pedido.valor, '.', ',') AS valor, pedido.obs, prioridade.nome AS prioridade FROM prioridade, pedido, mes, status WHERE pedido.prioridade = prioridade.id AND status.id = pedido.status AND pedido.id = {$id_pedido} AND mes.id = pedido.ref_mes;")->fetch_object();
        $empenho = "Empenho: " . Busca::verEmpenho($id_pedido);
        $pedido->valor = number_format($pedido->valor, 3, ',', '.');
        $retorno = "
            <fieldset>
                <p>
                    Pedido: " . $id_pedido . "
                    Data de Envio: " . $pedido->data_pedido . ".&emsp;
                    Situação: " . $pedido->status . "&emsp;
                    Prioridade: " . $pedido->prioridade . "&emsp;
                    Total do Pedido: R$ " . $pedido->valor . "
                </p>
                <p>" . $empenho . "</p>
                <p>Observação da Unidade Solicitante: </p>
                <p style=\"font-weight: normal !important;\">	" . $pedido->obs . "</p>
            </fieldset><br>";
        $retorno .= Busca::getTableFontes($id_pedido);
        return $retorno;
    }

    /**
     * 	Função para retornar as fontes de recurso de um pedido (impressão).
     *
     * 	@access public
     * 	@param $id_pedido Id do pedido.
     * 	@return Fontes de recurso.
     */
    public function getTableFontes(int $id_pedido): string {
        $retorno = "";
        $query = $this->mysqli->query("SELECT pedido_fonte.fonte_recurso, pedido_fonte.ptres, pedido_fonte.plano_interno FROM pedido_fonte WHERE pedido_fonte.id_pedido = {$id_pedido};");

        if ($query->num_rows > 0) {
            $fonte = $query->fetch_object();
            $retorno = "
                <fieldset class=\"preg\">
                    <table>
                        <thead>
                            <tr>
                                <th>Fonte de Recurso</th>
                                <th>PTRES</th>
                                <th>Plano Interno</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>" . $fonte->fonte_recurso . "</td>
                                <td>" . $fonte->ptres . "</td>
                                <td>" . $fonte->plano_interno . "</td>
                            </tr>
                        </tbody>
                    </table>
                </fieldset><br>";
        } else {
            $retorno = "
                <fieldset>
                <h5>PEDIDO AGUARDA FONTE DE RECURSO</h5>
                </fieldset><br>";
        }

        return $retorno;
    }

    /**
     * Função para retornar o pedido para um relátório separando-o por licitação e fornecedor
     *
     * @access public
     * @return string
     */
    public function getContentPedido(int $id_pedido): string {
        // declarando retorno
        $retorno = "";
        // PRIMEIRO FAZEMOS O CABEÇALHO REFERENTE AO NUM_LICITACAO
        $query_ini = $this->mysqli->query("SELECT DISTINCT itens.num_licitacao, itens.num_processo, itens.dt_inicio, itens.dt_fim FROM itens_pedido, itens WHERE itens.id = itens_pedido.id_item AND itens_pedido.id_pedido = {$id_pedido};");
        $i = 0;
        while ($licitacao = $query_ini->fetch_object()) {
            if ($licitacao->dt_fim == '') {
                $licitacao->dt_fim = "------------";
            }
            $retorno .= "
                <fieldset class=\"preg\">
                    <table>
                        <tr>
                            <td>Licitação: " . $licitacao->num_licitacao . "</td>
                            <td>Processo: " . $licitacao->num_processo . "</td>
                            <td>Início: " . $licitacao->dt_inicio . "</td>
                            <td>Fim: " . $licitacao->dt_fim . "</td>
                        </tr>
                    </table>
                </fieldset><br>";
            $query_forn = $this->mysqli->query("SELECT DISTINCT itens.cgc_fornecedor, itens.nome_fornecedor, itens.num_contrato FROM itens, itens_pedido WHERE itens.id = itens_pedido.id_item AND itens_pedido.id_pedido = {$id_pedido} AND itens.num_licitacao = {$licitacao->num_licitacao};");

            // -------------------------------------------------------------------------
            //                FORNECEDORES REFERENTES À LICITAÇÃO
            // -------------------------------------------------------------------------
            while ($fornecedor = $query_forn->fetch_object()) {
                // total do fornecedor
                $tot_forn = $this->mysqli->query("SELECT sum(itens_pedido.valor) AS sum FROM itens_pedido, itens WHERE itens_pedido.id_item = itens.id AND itens_pedido.id_pedido = {$id_pedido} AND itens.cgc_fornecedor = '{$fornecedor->cgc_fornecedor}';")->fetch_object();

                $fornecedor->nome_fornecedor = substr($fornecedor->nome_fornecedor, 0, 40);
                $fornecedor->nome_fornecedor = strtoupper($fornecedor->nome_fornecedor);
                $tot_forn->sum = number_format($tot_forn->sum, 3, ',', '.');
                $retorno .= "
                    <fieldset style=\"border-bottom: 1px solid black; padding: 5px;\">
                        <table>
                            <tr>
                                <td style=\"text-align: left; font-weight: bold;\">" . $fornecedor->nome_fornecedor . "</td>
                                <td>Contrato: " . $fornecedor->num_contrato . "</td>
                                <td>Total do Forn.: R$ " . $tot_forn->sum . "</td>
                            </tr>
                        </table>
                    </fieldset>";
                // ----------------------------------------------------------------------
                //                  ITENS REFERENTES AOS FORNECEDORES
                // ----------------------------------------------------------------------
                $query_itens = $this->mysqli->query("SELECT itens.cod_reduzido, itens.seq_item_processo, itens.complemento_item, itens_pedido.qtd, itens_pedido.valor FROM itens, itens_pedido WHERE itens.id = itens_pedido.id_item AND itens_pedido.id_pedido = {$id_pedido} AND itens.cgc_fornecedor = '{$fornecedor->cgc_fornecedor}'");
                $retorno .= "
                    <table class=\"prod\">
                        <thead>
                            <tr>
                                <th>Código</th>
                                <th>Item</th>
                                <th>Descrição</th>
                                <th>Quantidade</th>
                                <th>Valor</th>
                            </tr>
                        </thead>
                        <tbody>";

                while ($item = $query_itens->fetch_object()) {
                    $item->complemento_item = mb_strtoupper($item->complemento_item, 'UTF-8');
                    $item->valor = number_format($item->valor, 3, ',', '.');
                    $retorno .= "
                            <tr>
                                <td>" . $item->cod_reduzido . "</td>
                                <td>" . $item->seq_item_processo . "</td>
                                <td>" . $item->complemento_item . "</td>
                                <td>" . $item->qtd . "</td>
                                <td>R$ " . $item->valor . "</td>
                            </tr>";
                }
                $retorno .= "
                        </tbody>
                </table><br>";
            }
        }
        $query_ini->close();

        return $retorno;
    }

    /**
     * Função que retorna a tabela com os itens de um pedido para pdf
     *
     * @access public
     * @return string
     */
    public function getTabelaPDF(int $id_pedido): string {
        $retorno = "";
        $query = $this->mysqli->query("SELECT itens.id, itens.cod_reduzido, itens.cgc_fornecedor, itens.num_licitacao, itens_pedido.qtd, itens_pedido.valor FROM itens, itens_pedido WHERE itens.id = itens_pedido.id AND itens_pedido.id_pedido = {$id_pedido};");
        while ($itens = $query->fetch_object()) {
            $retorno .= "
                <tr>
                    <td>" . $itens->id . "</td>
                    <td>" . $itens->cod_reduzido . "</td>
                    <td>" . $itens->cgc_fornecedor . "</td>
                    <td>" . $itens->num_licitacao . "</td>
                    <td>" . $itens->qtd . "</td>
                    <td>R$ " . $itens->valor . "</td>
                </tr>";
        }
        return $retorno;
    }

    /**
     * 	Função para retornar os comentários de um pedido
     *
     * 	@access public
     * 	@return string
     */
    public function getComentarios(int $id_pedido): string {
        $retorno = "";
        $query = $this->mysqli->query("SELECT DATE_FORMAT(comentarios.data_coment, '%d/%m/%Y') AS data_coment, prioridade.nome AS prioridade, comentarios.valor, comentarios.comentario FROM comentarios, prioridade WHERE prioridade.id = comentarios.prioridade AND comentarios.id_pedido = {$id_pedido};");
        if ($query->num_rows > 0) {
            while ($comentario = $query->fetch_object()) {
                $retorno .= "
                    <fieldset class=\"preg\">
                        <table>
                            <tr>
                                <td>Data do Comentário: " . $comentario->data_coment . "</td>
                                <td>Prioridade: " . $comentario->prioridade . "</td>
                                <td>Valor: R$ " . $comentario->valor . "</td>
                            </tr>
                        </table>
                    </fieldset>
                    <fieldset>
                        <p style=\"font-weight: normal;\">" . $comentario->comentario . "</p>
                    </fieldset>";
            }
        } else {
            $retorno = "Sem comentários";
        }
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
     * 	Função que retorna as 'tabs' com as ṕáginas das notícias para editar
     *
     * 	@access public
     * 	@return string
     */
    public function getTabsNoticias(): string {
        $retorno = "";
        $query = $this->mysqli->query("SELECT paginas_post.id, paginas_post.tabela, paginas_post.nome FROM paginas_post;");
        while ($pag = $query->fetch_object()) {
            $retorno .= "
                <td>
                    <div class=\"radiobtn radiobtn-adv\">
                        <label for=\"pag-" . $pag->tabela . "\">
                            <input type=\"radio\" id=\"pag-" . $pag->tabela . "\" name=\"pag\" class=\"access-hide\" onclick=\"carregaPostsPag(" . $pag->id . ");\">" . $pag->nome . "
                            <span class=\"radiobtn-circle\"></span><span class=\"radiobtn-circle-check\"></span>
                        </label>
                    </div>
                </td>";
        }
        $query->close();
        return $retorno;
    }

    /**
     * 	Função para retornar a tabela de notícias de uma página para edição
     *
     * 	@access public
     * 	@return string
     */
    public function getNoticiasEditar(int $tabela): string {
        $retorno = "";
        $query = $this->mysqli->query("SELECT postagens.id, postagens.tabela, postagens.titulo, DATE_FORMAT(postagens.data, '%d/%m/%Y') AS data FROM postagens WHERE postagens.ativa = 1 AND postagens.tabela = {$tabela} ORDER BY postagens.data ASC;");
        while ($postagem = $query->fetch_object()) {
            $retorno .= "
                <tr>
                    <td>" . $postagem->data . "</td>
                    <td>" . $postagem->titulo . "</td>
                    <td>
                        <button class=\"btn btn-default btn-sm\" style=\"text-transform: none !important;font-weight: bold;\" onclick=\"editaNoticia(" . $postagem->id . ", " . $postagem->tabela . ", '" . $postagem->data . "')\" title=\"Editar\"><span class=\"icon\">create</span></button>
                        <button class=\"btn btn-default btn-sm\" style=\"text-transform: none !important;font-weight: bold;\" onclick=\"excluirNoticia(" . $postagem->id . ");\" title=\"Excluir\"><span class=\"icon\">delete</span></button>
                    </td>
                </tr>";
        }
        $query->close();
        return $retorno;
    }

    /**
     * Função para buscar conteúdo de uma publicação para edição
     *
     * @access public
     * @return string
     */
    public function getPublicacaoEditar(int $id): string {
        $publicacao = $this->mysqli->query("SELECT postagens.postagem FROM postagens WHERE id={$id};")->fetch_object();
        return $publicacao->postagem;
    }

    /**
     * Função para escrever as opções para "Postar em " do painel administrativo
     *
     *
     * @access public
     * @return string
     */
    public function getPostarEm(): string {
        //declarando retorno
        $retorno = "";
        $query = $this->mysqli->query("SELECT id, nome FROM paginas_post;");
        while ($pagina = $query->fetch_object()) {
            $retorno .= "<option id=\"op" . $pagina->id . "\" value=\"" . $pagina->id . "\">" . $pagina->nome . "</option>";
        }
        $query->close();
        return $retorno;
    }

    /**
     * Função para retornar as solicitações para o SOF
     *
     * @access public
     * @return string
     *
     */
    public function getSolicitacoesAdmin(): string {
        //declarando retorno
        $retorno = "";
        $query = $this->mysqli->query("SELECT pedido.id, pedido.id_setor, setores.nome AS nome_setor, DATE_FORMAT(pedido.data_pedido, '%d/%m/%Y') AS data_pedido, mes.sigla_mes AS ref_mes, prioridade.nome AS prioridade, status.nome AS status, status.id AS id_status, pedido.valor FROM pedido, setores, mes, prioridade, status WHERE status.id = pedido.status AND pedido.status <> 3 AND prioridade.id = pedido.prioridade AND mes.id = pedido.ref_mes AND pedido.alteracao = 0 AND pedido.id_setor = setores.id ORDER BY pedido.id DESC LIMIT 100;");
        while ($pedido = $query->fetch_object()) {
            $btnAnalisar = "";
            if ($pedido->status != 'Reprovado' && $pedido->status != 'Aprovado') {
                if ($_SESSION['id_setor'] == 12) {
                    $btnAnalisar = "<a class=\"modal-close\" href=\"javascript:enviaForn(" . $pedido->id . ");\" title=\"Enviar ao Fornecedor\"><span class=\"icon\">send<span></a>";
                } else if ($pedido->status == 'Em Analise') {
                    $btnAnalisar = "<a class=\"modal-close\" href=\"javascript:analisarPedido(" . $pedido->id . ", " . $pedido->id_setor . ");\" title=\"Analisar\"><span class=\"icon\">create<span></a>";
                } else if ($pedido->status == 'Aguarda Orcamento') {
                    $btnAnalisar = "<a class=\"modal-close\" href=\"javascript:cadFontes(" . $pedido->id . ");\" title=\"Cadastrar Fontes\"><span class=\"icon\">mode_comment<span></a>";
                } else if ($pedido->status == 'Aguarda SIAFI') {
                    $btnAnalisar = "<a class=\"modal-close\" href=\"javascript:cadEmpenho(" . $pedido->id . ");\" title=\"Cadastrar Empenho\"><span class=\"icon\">payment<span></a>";
                } else if ($pedido->status == 'Empenhado') {
                    $btnAnalisar = "<a class=\"modal-close\" href=\"javascript:enviaOrdenador(" . $pedido->id . ");\" title=\"Enviar ao Ordenador\"><span class=\"icon\">send<span></a>";
                } else {
                    $btnAnalisar = "<a class=\"modal-close\" href=\"javascript:getStatus(" . $pedido->id . ", " . $pedido->id_setor . ");\" title=\"Alterar Status\"><span class=\"icon\">build<span></a>";
                }
            }
            $btnVerEmpenho = Busca::verEmpenho($pedido->id);
            if ($btnVerEmpenho == 'EMPENHO SIAFI PENDENTE') {
                $btnVerEmpenho = '';
            }
            $btnVerProcesso = "
                    <a class=\"modal-close\" href=\"javascript:verProcessos(" . $pedido->id . ");\" title=\"Ver Processos\"><span class=\"icon\">remove_red_eye</span></a>";
            $pedido->valor = number_format($pedido->valor, 3, ',', '.');
            $linha = "
                <tr id=\"rowPedido" . $pedido->id . "\">
                    <td>
                        " . $btnVerProcesso . $btnAnalisar . "
                        <a class=\"modal-close\" href=\"javascript:imprimir(" . $pedido->id . ");\" title=\"Imprimir\"><span class=\"icon\">print<span></a>
                    </td>
                    <td>" . $pedido->id . "</td>
                    <td>" . $pedido->nome_setor . "</td>
                    <td>" . $pedido->data_pedido . "</td>
                    <td>" . $pedido->ref_mes . "</td>
                    <td>" . $pedido->prioridade . "</td>
                    <td>" . $pedido->status . "</td>
                    <td>R$ " . $pedido->valor . "</td>
                    <td>
                        " . $btnVerEmpenho . "
                    </td>
                </tr>";
            if ($_SESSION['id_setor'] == 12) {
                if ($pedido->status == 'Enviado ao Ordenador') {
                    $retorno .= $linha;
                }
            } else {
                $retorno .= $linha;
            }
        }
        $query->close();
        return $retorno;
    }

    /**
     * Função para trazer as informações de um pedido a ser analisado
     *
     * @access public
     * @return string
     */
    public function getItensPedidoAnalise(int $id_pedido): string {
        //declarando retorno
        $retorno = "";
        $query = $this->mysqli->query("SELECT itens.qt_contrato, itens.id AS id_itens, itens_pedido.qtd AS qtd_solicitada, itens_pedido.valor, itens.nome_fornecedor, itens.num_licitacao, itens.dt_inicio, itens.dt_fim, itens.dt_geracao, itens.cod_reduzido, itens.complemento_item, itens.vl_unitario, itens.qt_saldo, itens.cod_despesa, itens.descr_despesa, itens.num_contrato, itens.num_processo, itens.descr_mod_compra, itens.num_licitacao, itens.cgc_fornecedor, itens.num_extrato, itens.descricao, itens.qt_contrato, itens.vl_contrato, itens.qt_utilizado, itens.vl_utilizado, itens.qt_saldo, itens.vl_saldo, itens.seq_item_processo FROM itens_pedido, itens WHERE itens_pedido.id_pedido = {$id_pedido} AND itens_pedido.id_item = itens.id;");

        while ($item = $query->fetch_object()) {
            if ($item->dt_fim == '') {
                $item->dt_fim = "----------";
            }
            $item->complemento_item = $this->mysqli->real_escape_string($item->complemento_item);
            $item->complemento_item = str_replace("\"", "'", $item->complemento_item);
            $retorno .= "
                <tr id=\"row_item" . $item->id_itens . "\">
                    <td>
                        <a class=\"modal-close\" href=\"javascript:cancelaItem(" . $item->id_itens . ");\" title=\"Item Cancelado\"><span id=\"icon-cancela-item" . $item->id_itens . "\" class=\"icon text-red\">cancel<span>
                        </a>
                        <a class=\"modal-close\" href=\"javascript:editaItem(" . $item->id_itens . ");\" title=\"Editar\"><span class=\"icon\">edit<span>
                        </a>
                        <a class=\"modal-close\" href=\"javascript:viewCompl('" . $item->complemento_item . "');\"  title=\"Ver Complemento do Item\"><span class=\"icon\">assignment<span></a>
                    </td>
                    <td>" . $item->cod_reduzido . "</td>
                    <td>" . $item->seq_item_processo . "</td>
                    <td>" . $item->cod_despesa . "</td>
                    <td>" . $item->descr_despesa . "</td>
                    <td>" . $item->num_contrato . "</td>
                    <td>" . $item->num_processo . "</td>
                    <td>" . $item->descr_mod_compra . "</td>
                    <td>" . $item->num_licitacao . "</td>
                    <td>" . $item->dt_inicio . "</td>
                    <td>" . $item->dt_fim . "</td>
                    <td>" . $item->dt_geracao . "</td>
                    <td>" . $item->cgc_fornecedor . "</td>
                    <td>" . $item->nome_fornecedor . "</td>
                    <td>" . $item->num_extrato . "</td>
                    <td>" . $item->descricao . "</td>
                    <td>R$ " . $item->vl_unitario . "</td>
                    <td>" . $item->qt_contrato . "</td>
                    <td>" . $item->vl_contrato . "</td>
                    <td>" . $item->qt_utilizado . "</td>
                    <td>" . $item->vl_utilizado . "</td>
                    <td>" . $item->qt_saldo . "</td>
                    <td>" . $item->vl_saldo . "</td>
                    <td>" . $item->qtd_solicitada . "</td>
                    <td>R$ " . $item->valor . "</td>
                    <td>
                        <input type=\"hidden\" name=\"id_item[]\" value=\"" . $item->id_itens . "\">
                        <input id=\"item_cancelado" . $item->id_itens . "\" type=\"hidden\" name=\"item_cancelado[]\" value=\"0\">
                        <input type=\"hidden\" name=\"qtd_solicitada[]\" value=\"" . $item->qtd_solicitada . "\">
                        <input type=\"hidden\" name=\"qt_saldo[]\" value=\"" . $item->qt_saldo . "\">
                        <input type=\"hidden\" name=\"qt_utilizado[]\" value=\"" . $item->qt_utilizado . "\">
                        <input type=\"hidden\" name=\"vl_saldo[]\" value=\"" . $item->vl_saldo . "\">
                        <input type=\"hidden\" name=\"vl_utilizado[]\" value=\"" . $item->vl_utilizado . "\">
                        <input type=\"hidden\" name=\"valor_item[]\" value=\"" . $item->valor . "\">
                    </td>
                </tr>";
        }
        $query->close();

        return $retorno;
    }

    /**
     * Função para trazer o restante das informações para analisar o pedido:
     *               saldo, total, prioridade, fase, etc.
     *
     * @access public
     * @return string
     *
     */
    public function getInfoPedidoAnalise(int $id_pedido, int $id_setor): string {
        $query = $this->mysqli->query("SELECT saldo_setor.saldo, pedido.prioridade, pedido.status, pedido.valor, pedido.obs FROM saldo_setor, pedido WHERE saldo_setor.id_setor = {$id_setor} AND pedido.id = {$id_pedido};");
        $pedido = $query->fetch_object();
        $query->close();
        return json_encode($pedido);
    }

    /**
     * 	Função que retorna uma tabela com as solicitações de alteração de pedidos
     *
     * 	@access public
     * 	@return string
     */
    public function getSolicAltPedidos(int $id_setor): string {
        $retorno = "";
        $query = $this->mysqli->query("SELECT solic_alt_pedido.id_pedido, DATE_FORMAT(solic_alt_pedido.data_solicitacao, '%d/%m/%Y') AS data_solicitacao, DATE_FORMAT(solic_alt_pedido.data_analise, '%d/%m/%Y') AS data_analise, solic_alt_pedido.justificativa, solic_alt_pedido.status FROM solic_alt_pedido WHERE solic_alt_pedido.id_setor = {$id_setor} ORDER BY id DESC;");
        $status = $label = "";
        while ($solic = $query->fetch_object()) {
            switch ($solic->status) {
                case 0:
                    $status = "Reprovado";
                    $label = "label-red";
                    break;
                case 1:
                    $status = "Aprovado";
                    $label = "label-green";
                    break;
                default:
                    $status = "Aberto";
                    $label = "label-orange";
                    $solic->data_analise = "--------------";
                    break;
            }
            $solic->justificativa = $this->mysqli->real_escape_string($solic->justificativa);
            $solic->justificativa = str_replace("\"", "'", $solic->justificativa);
            $retorno .= "
                <tr>
                    <td>" . $solic->id_pedido . "</td>
                    <td>" . $solic->data_solicitacao . "</td>
                    <td>" . $solic->data_analise . "</td>
                    <td>
                        <button onclick=\"viewCompl('" . $solic->justificativa . "');\" class=\"btn btn-flat waves-attach waves-effect\" type=\"button\" title=\"Ver Justificativa\">JUSTIFICATIVA</button>
                    </td>
                    <td><span class=\"label " . $label . "\" style=\"font-size: 11pt !important; font-weight: bold;\">" . $status . "</span></td>
                </tr>";
        }
        $query->close();
        return $retorno;
    }

    /**
     * 	Função para retornar os meses em php/solicitacoes.php RefMes
     *
     * 	@access public
     * 	@return string
     */
    public function getMeses(): string {
        $retorno = $selected = "";
        $mes_atual = date('n');
        $query = $this->mysqli->query("SELECT id, sigla_mes FROM mes LIMIT 12;");
        while ($mes = $query->fetch_object()) {
            if ($mes->id == $mes_atual) {
                $selected = "selected";
            }
            $retorno .= "<option value=\"" . $mes->id . "\" " . $selected . ">" . $mes->sigla_mes . "</option>";
            $selected = "";
        }
        $query->close();
        return $retorno;
    }

    /**
     * 	Função que retorna as solicitações de adiantamento de saldos do setor
     *
     * 	@access public
     * 	@return string
     */
    public function getSolicAdiSetor(int $id_setor): string {
        $retorno = "";
        $query = $this->mysqli->query("SELECT saldos_adiantados.id, DATE_FORMAT(saldos_adiantados.data_solicitacao, '%d/%m/%Y') AS data_solicitacao, DATE_FORMAT(saldos_adiantados.data_analise, '%d/%m/%Y') AS data_analise, saldos_adiantados.valor_adiantado, saldos_adiantados.justificativa, saldos_adiantados.status FROM saldos_adiantados WHERE saldos_adiantados.id_setor = {$id_setor} ORDER BY saldos_adiantados.id DESC;");
        $label = $status = "";
        while ($solic = $query->fetch_object()) {
            switch ($solic->status) {
                case 0:
                    $label = "label-red";
                    $status = "Reprovado";
                    break;
                case 1:
                    $label = "label-green";
                    $status = "Aprovado";
                    break;
                case 2:
                    $label = "label-orange";
                    $status = "Aberto";
                    $solic->data_analise = "--------------";
                    break;
            }
            $solic->justificativa = $this->mysqli->real_escape_string($solic->justificativa);
            $solic->justificativa = str_replace("\"", "'", $solic->justificativa);
            $solic->valor_adiantado = number_format($solic->valor_adiantado, 3, ',', '.');
            $retorno .= "
                <tr>
                    <td>" . $solic->data_solicitacao . "</td>
                    <td>" . $solic->data_analise . "</td>
                    <td>R$ " . $solic->valor_adiantado . "</td>
                    <td>
                        <button onclick=\"viewCompl('" . $solic->justificativa . "');\" class=\"btn btn-flat waves-attach waves-effect\" type=\"button\" title=\"Ver Justificativa\">JUSTIFICATIVA</button>
                    </td>
                    <td><span class=\"label " . $label . "\" style=\"font-size: 11pt !important; font-weight: bold;\">" . $status . "</span></td>
                </tr>";
        }
        $query->close();
        return $retorno;
    }

    /**
     * Função para mostrar os itens de um processo pesquisado no menu solicitações
     *
     * @access public
     * @return string
     */
    public function getConteudoProcesso(string $busca): string {
        //declarando retorno
        $retorno = "";

        $query = $this->mysqli->query("SELECT itens.id, itens.id_item_processo, itens.nome_fornecedor, itens.cod_reduzido, itens.complemento_item, replace(itens.vl_unitario, ',', '.') AS vl_unitario, itens.qt_contrato, itens.qt_utilizado, itens.vl_utilizado, itens.qt_saldo, itens.vl_saldo FROM itens WHERE num_processo LIKE '%{$busca}%' AND cancelado = 0;");

        while ($item = $query->fetch_object()) {
            //remove as aspas do complemento_item
            $item->complemento_item = $this->mysqli->real_escape_string($item->complemento_item);
            $item->complemento_item = str_replace("\"", "'", $item->complemento_item);
            $retorno .= "
                <tr>
                    <td>
                            <a class=\"modal-close\" href=\"javascript:checkItemPedido(" . $item->id . ", '" . $item->vl_unitario . "', " . $item->qt_saldo . ");\"><span class=\"icon\">add<span></a>
                    </td>
                    <td>" . $item->nome_fornecedor . "</td>
                    <td>" . $item->cod_reduzido . "</td>
                    <td><input type=\"number\" id=\"qtd" . $item->id . "\" min=\"1\" max=\"" . $item->qt_saldo . "\"></td>
                    <td>
                            <a onclick=\"viewCompl('" . $item->complemento_item . "');\" class=\"btn btn-flat waves-attach waves-effect\" type=\"button\" title=\"Mais Detalhes\">complemento_item</a>
                    </td>
                    <td style=\"display: none;\">" . $item->complemento_item . "</td>
                    <td>" . $item->vl_unitario . "</td>
                    <td>" . $item->qt_saldo . "</td>
                    <td>" . $item->qt_utilizado . "</td>
                    <td>" . $item->vl_saldo . "</td>
                    <td>" . $item->vl_utilizado . "</td>
                    <td>" . $item->qt_contrato . "</td>
                </tr>";
        }
        $query->close();
        return $retorno;
    }

    /**
     * Função para trazer a linha do item anexado ao pedido
     *
     * @access public
     * @return string
     */
    public function addItemPedido(int $id_item, int $qtd): string {
        //executando a query
        $query = $this->mysqli->query("SELECT itens.id, itens.nome_fornecedor, itens.num_licitacao, itens.cod_reduzido, itens.complemento_item, replace(itens.vl_unitario, ',', '.') AS vl_unitario, itens.qt_saldo, itens.qt_contrato, itens.qt_utilizado, itens.vl_saldo, itens.vl_contrato, itens.vl_utilizado FROM itens WHERE itens.id = {$id_item};");
        $item = $query->fetch_object();
        $query->close();
        $item->complemento_item = $this->mysqli->real_escape_string($item->complemento_item);
        $item->complemento_item = str_replace("\"", "'", $item->complemento_item);
        $valor = $qtd * $item->vl_unitario;
        $retorno = "
            <tr id=\"row" . $id_item . "\">
                <td><a class=\"modal-close\" href=\"javascript:removeTableRow(" . $id_item . ", '" . $valor . "');\"><span class=\"icon\">delete</span></a></td>
                <td>" . $item->cod_reduzido . "</td>
                <td>
                    <button onclick=\"viewCompl('" . $item->complemento_item . "');\" class=\"btn btn-flat waves-attach waves-effect\" type=\"button\" title=\"Ver Complemento do Item\">complemento_item</button>
                </td>
                <td>R$ " . $item->vl_unitario . "</td>
                <td>" . $item->nome_fornecedor . "</td>
                <td>" . $item->num_licitacao . "</td>
                <td>" . $qtd . "</td>
                <td>R$ " . $valor . "</td>
                <td>
                    <input class=\"classItens\" type=\"hidden\" name=\"id_item[]\" value=\"" . $id_item . "\">
                    <input type=\"hidden\" name=\"qtd_solicitada[]\" value=\"" . $qtd . "\">
                    <input type=\"hidden\" name=\"qtd_disponivel[]\" value=\"" . $item->qt_saldo . "\">
                    <input type=\"hidden\" name=\"qtd_contrato[]\" value=\"" . $item->qt_contrato . "\">
                    <input type=\"hidden\" name=\"qtd_utilizado[]\" value=\"" . $item->qt_utilizado . "\">
                    <input type=\"hidden\" name=\"vl_saldo[]\" value=\"" . $item->vl_saldo . "\">
                    <input type=\"hidden\" name=\"vl_contrato[]\" value=\"" . $item->vl_contrato . "\">
                    <input type=\"hidden\" name=\"vl_utilizado[]\" value=\"" . $item->vl_utilizado . "\">
                    <input type=\"hidden\" name=\"valor[]\" value=\"" . $valor . "\">
                </td>
            </tr>";
        return $retorno;
    }

    /**
     * Função para retornar os rascunhos para continuar editando
     *
     * @access public
     * @return string
     */
    public function getRascunhos(int $id_setor): string {
        //declarando retorno
        $retorno = "";
        $query = $this->mysqli->query("SELECT pedido.id, DATE_FORMAT(pedido.data_pedido, '%d/%m/%Y') AS data_pedido, mes.sigla_mes AS ref_mes, pedido.valor, status.nome AS status FROM pedido, mes, status WHERE pedido.id_setor = {$id_setor} AND pedido.alteracao = 1 AND mes.id = pedido.ref_mes AND status.id = pedido.status ORDER BY pedido.id DESC;");

        while ($rascunho = $query->fetch_object()) {
            $rascunho->valor = number_format($rascunho->valor, 3, ',', '.');
            $retorno .= "
                <tr>
                    <td>" . $rascunho->id . "</td>
                    <td><span class=\"label\" style=\"font-size: 11pt;\">" . $rascunho->status . "</span></td>
                    <td>" . $rascunho->ref_mes . "</td>
                    <td>" . $rascunho->data_pedido . "</td>
                    <td>R$ " . $rascunho->valor . "</td>
                    <td>
                        <button class=\"btn btn-default btn-sm\" style=\"text-transform: none !important;font-weight: bold;\" onclick=\"editaPedido(" . $rascunho->id . ");\" title=\"Editar\"><span class=\"icon\">create</span></button>
                        <button class=\"btn btn-default btn-sm\" style=\"text-transform: none !important;font-weight: bold;\" onclick=\"imprimir(" . $rascunho->id . ");\" title=\"Imprimir\"><span class=\"icon\">print</span></button>
                        <button class=\"btn btn-default btn-sm\" style=\"text-transform: none !important;font-weight: bold;\" onclick=\"deletePedido(" . $rascunho->id . ");\" title=\"Excluir\"><span class=\"icon\">delete</span></button>
                    </td>
                </tr>";
        }
        $query->close();
        return $retorno;
    }

    /**
     * 	Função que retorna o saldo dispónível do setor
     *
     * 	@access public
     * 	@return string
     */
    public function getSaldo(int $id_setor): string {
        $query = $this->mysqli->query("SELECT saldo_setor.saldo FROM saldo_setor WHERE saldo_setor.id_setor = {$id_setor};");
        if ($query->num_rows < 1) {
            $this->mysqli->query("INSERT INTO saldo_setor VALUES(NULL, {$id_setor}, '0.000');");
            return '0.000';
        }
        $obj = $query->fetch_object();
        $saldo = number_format($obj->saldo, 3, '.', '');
        return $saldo;
    }

    /**
     * Função para retornar o conteúdo de um pedido para edição
     *
     * @access public
     * @return string
     */
    public function getConteudoPedido(int $id_pedido): string {
        $retorno = "";
        $query = $this->mysqli->query("SELECT itens.qt_contrato, itens.id AS id_itens, itens_pedido.qtd AS qtd_solicitada, itens_pedido.valor, itens.nome_fornecedor, itens.num_licitacao, itens.cod_reduzido, itens.complemento_item, replace(itens.vl_unitario, ',', '.') AS vl_unitario, itens.qt_saldo, itens.qt_contrato, itens.qt_utilizado, itens.vl_saldo, itens.vl_contrato, itens.vl_utilizado FROM itens_pedido, itens WHERE itens_pedido.id_pedido = {$id_pedido} AND itens_pedido.id_item = itens.id");
        while ($item = $query->fetch_object()) {
            $id_item = $item->id_itens;
            $item->complemento_item = str_replace("\"", "'", $item->complemento_item);
            $retorno .= "
                <tr id=\"row" . $id_item . "\">
                    <td><a class=\"modal-close\" href=\"javascript:removeTableRow(" . $id_item . ", '" . $item->valor . "');\"><span class=\"icon\">delete</span></a></td>
                    <td>" . $item->cod_reduzido . "</td>
                    <td>
                        <button onclick=\"viewCompl(\"" . $item->complemento_item . "\");\" class=\"btn btn-flat waves-attach waves-effect\" type=\"button\" title=\"Ver Complemento do Item\">complemento_item</button>
                    </td>
                    <td>R$ " . $item->vl_unitario . "</td>
                    <td>" . $item->nome_fornecedor . "</td>
                    <td>" . $item->num_licitacao . "</td>
                    <td>" . $item->qtd_solicitada . "</td>
                    <td>R$ " . $item->valor . "</td>
                    <td>
                        <input type=\"hidden\" name=\"id_item[]\" value=\"" . $id_item . "\">
                        <input type=\"hidden\" name=\"qtd_solicitada[]\" value=\"" . $item->qtd_solicitada . "\">
                        <input type=\"hidden\" name=\"qtd_disponivel[]\" value=\"" . $item->qt_saldo . "\">
                        <input type=\"hidden\" name=\"qtd_contrato[]\" value=\"" . $item->qt_contrato . "\">
                        <input type=\"hidden\" name=\"qtd_utilizado[]\" value=\"" . $item->qt_utilizado . "\">
                        <input type=\"hidden\" name=\"vl_saldo[]\" value=\"" . $item->vl_saldo . "\">
                        <input type=\"hidden\" name=\"vl_contrato[]\" value=\"" . $item->vl_contrato . "\">
                        <input type=\"hidden\" name=\"vl_utilizado[]\" value=\"" . $item->vl_utilizado . "\">
                        <input type=\"hidden\" name=\"valor[]\" value=\"" . $item->valor . "\">
                    </td>
                </tr>";
        }
        $query->close();
        return $retorno;
    }

    /**
     * Função dispara logo após clicar em editar rascunho de pedido
     *
     * @access public
     * @return string
     *
     */
    public function getPopulaRascunho(int $id_pedido, int $id_setor): string {
        $query = $this->mysqli->query("SELECT saldo_setor.saldo, pedido.valor, pedido.obs FROM saldo_setor, pedido WHERE pedido.id = {$id_pedido} AND saldo_setor.id_setor = {$id_setor};");
        $pedido = $query->fetch_object();
        $query->close();
        return json_encode($pedido);
    }

    /**
     * Função para o setor acompanhar o andamento do seu pedido
     *
     * @access public
     * @return string
     *
     */
    public function getMeusPedidos(int $id_setor): string {
        //declarando retorno
        $retorno = "";
        $query = $this->mysqli->query("SELECT pedido.id, DATE_FORMAT(pedido.data_pedido, '%d/%m/%Y') AS data_pedido, mes.sigla_mes AS ref_mes, prioridade.nome AS prioridade, status.nome AS status, pedido.valor FROM pedido, mes, prioridade, status WHERE prioridade.id = pedido.prioridade AND status.id = pedido.status AND pedido.id_setor = {$id_setor} AND pedido.alteracao = 0 AND mes.id = pedido.ref_mes ORDER BY pedido.id DESC;");
        while ($pedido = $query->fetch_object()) {
            $empenho = Busca::verEmpenho($pedido->id);
            if ($empenho == 'EMPENHO SIAFI PENDENTE') {
                $empenho = '';
            }
            $pedido->valor = number_format($pedido->valor, 3, ',', '.');
            $retorno .= "
                <tr>
                    <td>" . $pedido->id . "</td>
                    <td>" . $pedido->ref_mes . "</td>
                    <td>" . $pedido->data_pedido . "</td>
                    <td>" . $pedido->prioridade . "</td>
                    <td><span class=\"label\" style=\"font-size: 11pt;\">" . $pedido->status . "</span></td>
                    <td>" . $empenho . "</td>
                    <td>R$ " . $pedido->valor . "</td>
                    <td>
                        <button class=\"btn btn-default btn-sm\" style=\"text-transform: none !important;font-weight: bold;\" onclick=\"solicAltPed(" . $pedido->id . ");\" title=\"Solicitar Alteração\"><span class=\"icon\">build</span></button>
                        <button class=\"btn btn-default btn-sm\" style=\"text-transform: none !important;font-weight: bold;\" onclick=\"imprimir(" . $pedido->id . ");\" title=\"Imprimir\"><span class=\"icon\">print</span></button>
                    </td>
                </tr>";
        }
        $query->close();
        return $retorno;
    }

    /**
     * 	Colocar documentação aqui
     *
     * 	@access public
     * 	@return string
     */
    public function getProcessos(string $tela): string {
        $retorno = "";
        $sql = "SELECT DISTINCT itens.num_processo FROM itens;";
        $onclick = "pesquisarProcesso";
        $title = "Pesquisar Processo";
        $icon = "search";
        if ($tela == "recepcao") {
            $sql = "SELECT DISTINCT itens.num_processo FROM itens WHERE itens.num_processo NOT IN (SELECT DISTINCT processos.num_processo FROM processos);";
            $onclick = "addProcesso";
            $title = "Adicionar Processo";
            $icon = "add";
        }
        $query = $this->mysqli->query($sql);
        while ($processo = $query->fetch_object()) {
            $retorno .= "
                <tr>
                    <td>" . $processo->num_processo . "</td>
                    <td>
                        <button title=\"" . $title . "\" onclick=\"" . $onclick . "('" . $processo->num_processo . "', 0)\" style=\"text-transform: none !important;font-weight: bold;\" class=\"btn btn-default btn-sm\"><span class=\"icon\">" . $icon . "</span></button>
                    </td>
                </tr>";
        }
        return $retorno;
    }

}

?>
