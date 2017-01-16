<?php

/**
 *  Classe com as funções de busca utilizadas principalmente pelo arquivo php/busca.php
 *  qualquer função que RETORNE dados do banco, devem ser feitas nesta classe
 * 
 *  Usada para a nova aparência da parte administrativa do SOF.
 *
 *  @author João Bolsson (joaovictorbolsson@gmail.com).
 *  @since 2016, 16 Mar.
 */
ini_set('display_erros', true);
error_reporting(E_ALL);

include_once 'Conexao.class.php';
include_once 'Util.class.php';

class BuscaLTE extends Conexao {

    private $mysqli, $obj_Util;

    function __construct() {
        //chama o método contrutor da classe Conexao
        parent::__construct();
        $this->obj_Util = new Util();
    }

    /**
     * Retorna a quantidade de solicitações de adiantamento, alt pedidos e de pedidos em análise.
     * @return type Objeto com as quantidades de solicitações.
     */
    public function getCountSolic() {
        if (is_null($this->mysqli)) {
            $this->mysqli = parent::getConexao();
        }
        $query = $this->mysqli->query("SELECT count(saldos_adiantados.id) AS solic_adi, (SELECT count(solic_alt_pedido.id) FROM solic_alt_pedido WHERE solic_alt_pedido.status = 2) AS solic_alt, (SELECT count(pedido.id) FROM pedido WHERE pedido.status = 2) AS solic_ped FROM saldos_adiantados WHERE saldos_adiantados.status = 2;") or exit("Erro ao buscar número de solicitações.");
        $this->mysqli = NULL;
        $obj = $query->fetch_object();
        return $obj;
    }

    public function getInfoContrato(int $id_pedido) {
        if (is_null($this->mysqli)) {
            $this->mysqli = parent::getConexao();
        }
        $query = $this->mysqli->query("SELECT pedido.pedido_contrato, pedido_contrato.id_tipo, pedido_contrato.siafi FROM pedido, pedido_contrato WHERE pedido.id = pedido_contrato.id_pedido AND pedido.id = {$id_pedido};") or exit("Erro ao buscar informações do contrato.");
        $this->mysqli = NULL;
        if ($query->num_rows < 1) {
            return false;
        }
        $obj = $query->fetch_object();
        return json_encode($obj);
    }

    public function getOptionsContrato() {
        if (is_null($this->mysqli)) {
            $this->mysqli = parent::getConexao();
        }
        $query = $this->mysqli->query("SELECT contrato_tipo.id, contrato_tipo.nome FROM contrato_tipo;") or exit("Erro ao buscar opções de contrato.");
        $this->mysqli = NULL;
        $retorno = "";
        while ($obj = $query->fetch_object()) {
            $retorno .= "
                <td>
                    <div class=\"radiobtn radiobtn-adv\">
                        <label for=\"tipoCont" . $obj->id . "\">
                            <input type=\"radio\" name=\"tipoCont\" id=\"tipoCont" . $obj->id . "\" class=\"access-hide\" value=\"" . $obj->id . "\" onchange=\"changeTipoContr(this);\">" . $obj->nome . "
                            <span class=\"radiobtn-circle\"></span><span class=\"radiobtn-circle-check\"></span>
                        </label>
                    </div>
                </td>";
        }

        return $retorno;
    }

    /**
     * @return string Lista de usuários cadastrados.
     */
    public function getUsers(): string {
        if (is_null($this->mysqli)) {
            $this->mysqli = parent::getConexao();
        }
        $query = $this->mysqli->query("SELECT usuario.id, usuario.nome FROM usuario;") or exit("Erro ao buscar usuários.");
        $this->mysqli = NULL;

        $retorno = "";
        while ($user = $query->fetch_object()) {
            $retorno .= "<option value=\"" . $user->id . "\">" . $user->nome . "</option>";
        }
        return $retorno;
    }

    public function getGrupo(int $id_pedido) {
        if (is_null($this->mysqli)) {
            $this->mysqli = parent::getConexao();
        }
        $query = $this->mysqli->query("SELECT pedido_grupo.id_grupo FROM pedido_grupo WHERE pedido_grupo.id_pedido = {$id_pedido};");
        $this->mysqli = NULL;
        if ($query->num_rows < 1) {
            return false;
        }
        $obj = $query->fetch_object();
        return $obj->id_grupo;
    }

    /**
     * @return bool Se o sistema está ativo - true, senão - false.
     */
    public function isActive(): bool {
        if (is_null($this->mysqli)) {
            $this->mysqli = parent::getConexao();
        }
        $query = $this->mysqli->query("SELECT ativo FROM sistema LIMIT 1;") or exit("Ocorreu um erro ao tentar verificar a disponibilidade do sistema. Contate o administrador.");
        $obj = $query->fetch_object();
        $this->mysqli = NULL;
        return $obj->ativo;
    }

    /**
     * 
     * @param int $id_setor Id do setor para retornar os grupos.
     * @return string Options dentro de um <select> com os grupos de um setor.
     */
    public function getOptionsGrupos(int $id_setor): string {
        if (is_null($this->mysqli)) {
            $this->mysqli = parent::getConexao();
        }
        $query = $this->mysqli->query("SELECT setores_grupos.id, setores_grupos.nome FROM setores_grupos WHERE setores_grupos.id_setor = {$id_setor};") or exit("Erro ao buscar grupos.");
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
        if (is_null($this->mysqli)) {
            $this->mysqli = parent::getConexao();
        }
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
                    <div class=\"radiobtn radiobtn-adv\">
                        <label for=\"tipoLic" . $obj->id . "\">
                            <input type=\"radio\" name=\"tipoLic\" id=\"tipoLic" . $obj->id . "\" class=\"access-hide\" value=\"" . $obj->id . "\" required onchange=\"changeTipoLic(this);\">" . $obj->nome . "
                            <span class=\"radiobtn-circle\"></span><span class=\"radiobtn-circle-check\"></span>
                        </label>
                    </div>
                </td>";
            $i++;
        }

        return $retorno;
    }

    /**
     * Função para retornar uma string para mostrar o total dos pedidos com determinado status.
     * @param int $status status dos pedidos para somar.
     * @return string String "Totalizando R$ x".
     */
    public function getTotalByStatus(int $status): string {
        if (is_null($this->mysqli)) {
            $this->mysqli = parent::getConexao();
        }
        $query = $this->mysqli->query("SELECT sum(pedido.valor) AS total FROM pedido WHERE pedido.status = {$status};") or exit("Erro ao buscar o total pelo status.");
        $this->mysqli = NULL;
        $tot = $query->fetch_object();
        $tot->total = number_format($tot->total, 3, ',', '.');
        return "Totalizando R$ " . $tot->total;
    }

    /**
     * 	Função para retornar uma lista de pedidos conforme o relatório.
     *
     * 	@param $status Status da lista de pedidos no relatório.
     * 	@return Lista de pedidos conforme o solicitado.
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
        if (is_null($this->mysqli)) {
            $this->mysqli = parent::getConexao();
        }
        $query = $this->mysqli->query("SELECT pedido.id, DATE_FORMAT(pedido.data_pedido, '%d/%m/%Y') AS data_pedido, mes.sigla_mes AS ref_mes, prioridade.nome AS prioridade, status.nome AS status, pedido.valor FROM pedido, mes, prioridade, status WHERE prioridade.id = pedido.prioridade AND status.id = pedido.status AND mes.id = pedido.ref_mes AND pedido.status = {$status} {$alteracao} {$order};") or exit("Erro ao gerar relatório.");
        $this->mysqli = NULL;
        while ($pedido = $query->fetch_object()) {
            $empenho = BuscaLTE::verEmpenho($pedido->id);
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
        return $retorno;
    }

    /**
     * 	Função para retornar os processos que estão nos pedidos com suas datas de vencimento
     *
     * 	@param $pedido Id do pedido.
     * 	@return Uma tabela com os processos e as informações dele.
     */
    public function getProcessosPedido(int $pedido): string {
        if (is_null($this->mysqli)) {
            $this->mysqli = parent::getConexao();
        }
        $query = $this->mysqli->query("SELECT DISTINCT itens.num_processo, itens.dt_fim FROM itens, itens_pedido WHERE itens_pedido.id_pedido = {$pedido} AND itens_pedido.id_item = itens.id;") or exit("Erro ao buscar os processos do pedido.");
        $this->mysqli = NULL;
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
     * 	Função para retornar os radios buttons para gerar relatórios por status.
     *
     * 	@return Colunas com alguns status.
     */
    public function getRadiosStatusRel(): string {
        $retorno = "<tr>";
        $i = 0;
        $cont = 5;
        if (is_null($this->mysqli)) {
            $this->mysqli = parent::getConexao();
        }
        $query = $this->mysqli->query("SELECT status.id, status.nome FROM status WHERE status.id <> 1;") or exit("Erro ao buscar as opções de status.");
        $this->mysqli = NULL;
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
        return $retorno;
    }

    /**
     * 	Função para retornar os problemas relatados
     *
     * 	@return string Linhas para uma tabela mostrar os problemas.
     */
    public function getProblemas(): string {
        if (is_null($this->mysqli)) {
            $this->mysqli = parent::getConexao();
        }
        $query = $this->mysqli->query("SELECT setores.nome AS setor, problemas.assunto, problemas.descricao FROM setores, problemas WHERE setores.id = problemas.id_setor ORDER BY problemas.id DESC;") or exit("Erro ao buscar os problemas.");
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
        $this->mysqli = NULL;
        return $retorno;
    }

    /**
     * 	Função que retonar o relatorio de pedidos.
     *
     * 	@return string Retorna a interface de um documento pdf.
     */
    public function getRelatorioPedidos(int $id_setor, int $prioridade, int $status, string $dataI, string $dataF): string {
        $retorno = "";
        $where_status = "AND pedido_log_status.id_status = " . $status;
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
        if (is_null($this->mysqli)) {
            $this->mysqli = parent::getConexao();
        }
        + $query = $this->mysqli->query("SELECT pedido_log_status.id_pedido AS id, setores.nome AS setor, DATE_FORMAT(pedido_log_status.data, '%d/%m/%Y') AS data_pedido, prioridade.nome AS prioridade, status.nome AS status, pedido.valor {$empenho} FROM {$tb_empenho} pedido_log_status, setores, pedido, prioridade, status WHERE status.id = pedido_log_status.id_status {$where_setor} {$where_prioridade} {$where_empenho} AND prioridade.id = pedido.prioridade AND pedido.id = pedido_log_status.id_pedido AND pedido.id_setor = setores.id {$where_status} AND pedido_log_status.data BETWEEN '{$dataIni}' AND '{$dataFim}' ORDER BY pedido_log_status.id_pedido ASC;") or exit("Erro ao buscar os pedidos com as especificações do usuário.");

        $titulo = "Relatório de Pedidos por Setor e Nível de Prioridade";
        if ($query) {
            $thead = "
                <th>Data</th>
                <th>Prioridade</th>
                <th>Status</th>
                <th>Valor</th>";
            if ($status == 8) {
                $titulo = "Relatório de Empenhos Enviados ao Ordenador";
                $thead = "
                    <th>Prioridade</th>
                    <th>SIAFI</th>";
            }
            $query_tot = $this->mysqli->query("SELECT sum(pedido.valor) AS total FROM {$tb_empenho} pedido, pedido_log_status WHERE pedido_log_status.id_pedido = pedido.id {$where_setor} {$where_prioridade} {$where_empenho} AND pedido.alteracao = 0 {$where_status} AND pedido_log_status.data BETWEEN '{$dataIni}' AND '{$dataFim}';") or exit("Erro ao somar os pedidos.");
            $total = "R$ 0";
            $tot = $query_tot->fetch_object();
            if ($tot->total > 0) {
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
        $this->mysqli = NULL;
        return $retorno;
    }

    /**
     * 	Função que retorna os pedidos em análise e o total deles
     *
     * 	@param $id_setor id do setor
     * 	@return string
     */
    public function getPedidosAnalise(int $id_setor): string {
        $retorno = "";
        if (is_null($this->mysqli)) {
            $this->mysqli = parent::getConexao();
        }
        $query = $this->mysqli->query("SELECT sum(pedido.valor) AS soma FROM pedido WHERE pedido.id_setor = {$id_setor} AND pedido.status = 2;") or exit("Erro ao buscar informações dos pedidos em análise.");
        $this->mysqli = NULL;
        if ($query->num_rows > 0) {
            $obj = $query->fetch_object();
            $soma = number_format($obj->soma, 3, ',', '.');
            if ($soma > 0) {
                $retorno = "
                <tr>
                    <td colspan=\"2\">Você tem " . $query->num_rows . " pedido(s) em análise no total de R$ " . $soma . "</td>
                    <td></td>
                </tr>";
            }
        }
        return $retorno;
    }

    /**
     * 	Função que que retorna informações de um item para possível edição
     *
     * 	@param Id do item da tbela itens
     * 	@return object
     */
    public function getInfoItem($id_item) {
        if (is_null($this->mysqli)) {
            $this->mysqli = parent::getConexao();
        }
        $query = $this->mysqli->query("SELECT itens.complemento_item, replace(itens.vl_unitario, ',', '.') AS vl_unitario, itens.qt_contrato, replace(itens.vl_contrato, ',', '.') AS vl_contrato, itens.qt_utilizado, replace(itens.vl_utilizado, ',', '.') AS vl_utilizado, itens.qt_saldo, replace(itens.vl_saldo, ',', '.') AS vl_saldo FROM itens WHERE itens.id = {$id_item};") or exit("Erro ao buscar informações do item.");
        $this->mysqli = NULL;
        $obj = $query->fetch_object();
        return json_encode($obj);
    }

    /**
     * 	Função que retornar o empenho de um pedido
     *
     * 	@param $id_pedido Id do pedido.
     * 	@return string
     */
    public function verEmpenho(int $id_pedido): string {
        $retorno = '';
        if (is_null($this->mysqli)) {
            $this->mysqli = parent::getConexao();
        }
        $query = $this->mysqli->query("SELECT empenho FROM pedido_empenho WHERE id_pedido = {$id_pedido};") or exit("Erro so ver empenho.");
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
     * 	Função que retorna a tabela com os lançamentos de saldos pelo SOF
     *
     * 	@param $id_setor id do setor
     * 	@return string
     */
    public function getLancamentos(int $id_setor): string {
        $retorno = "";
        $where = "";
        if ($id_setor != 0) {
            $where = "AND saldos_lancamentos.id_setor = " . $id_setor;
        }
        if (is_null($this->mysqli)) {
            $this->mysqli = parent::getConexao();
        }
        $query = $this->mysqli->query("SELECT setores.nome, DATE_FORMAT(saldos_lancamentos.data, '%d/%m/%Y') AS data, saldos_lancamentos.valor, saldo_categoria.nome AS categoria FROM setores, saldos_lancamentos, saldo_categoria WHERE setores.id = saldos_lancamentos.id_setor {$where} AND saldos_lancamentos.categoria = saldo_categoria.id ORDER BY saldos_lancamentos.id DESC;") or exit("Erro ao buscar informações dos lançamentos.");
        $this->mysqli = NULL;
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

    public function getTotalInOutSaldos(int $id_setor): string {
        $entrada = $saida = 0;
        $where = "";
        if ($id_setor != 0) {
            $where = "AND saldos_lancamentos.id_setor = " . $id_setor;
        }
        if (is_null($this->mysqli)) {
            $this->mysqli = parent::getConexao();
        }
        $query = $this->mysqli->query("SELECT saldos_lancamentos.valor FROM setores, saldos_lancamentos WHERE setores.id = saldos_lancamentos.id_setor {$where};") or exit("Erro ao buscar informações dos totais de entrada e saída do setor.");
        $this->mysqli = NULL;
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
     * 	@return string
     */
    public function getOptionsSetores(): string {
        $retorno = "";
        if (is_null($this->mysqli)) {
            $this->mysqli = parent::getConexao();
        }
        $query = $this->mysqli->query("SELECT setores.id, setores.nome FROM setores WHERE setores.id <> 1;") or exit("Erro ao buscar os setores cadastrados no sistema.");
        $this->mysqli = NULL;
        while ($setor = $query->fetch_object()) {
            $retorno .= "<option value=\"" . $setor->id . "\">" . $setor->nome . "</option>";
        }
        return $retorno;
    }

    /**
     * Retorna o nome do setor.
     * @param int $id_setor id do setor.
     */
    public function getSetorNome(int $id_setor): string {
        if (is_null($this->mysqli)) {
            $this->mysqli = parent::getConexao();
        }
        $query = $this->mysqli->query("SELECT setores.nome FROM setores WHERE setores.id = " . $id_setor . ";") or exit("Erro ao buscar o nome do setor.");
        $this->mysqli = NULL;
        $obj = $query->fetch_object();
        return $obj->nome;
    }

    /**
     * 	Função que retornar as options com as prioridades existentes no sistemas para os pedidos
     *
     * 	@return string
     */
    public function getOptionsPrioridades(): string {
        $retorno = "";
        if (is_null($this->mysqli)) {
            $this->mysqli = parent::getConexao();
        }
        $query = $this->mysqli->query("SELECT prioridade.id, prioridade.nome FROM prioridade WHERE prioridade.nome <> 'rascunho';") or exit("Erro ao buscar as prioridades");
        $this->mysqli = NULL;
        while ($prioridade = $query->fetch_object()) {
            $retorno .= "<option value=\"" . $prioridade->id . "\">" . $prioridade->nome . "</option>";
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
        if (is_null($this->mysqli)) {
            $this->mysqli = parent::getConexao();
        }
        $query = $this->mysqli->query("SELECT status.id, status.nome FROM status WHERE status.nome <> 'Rascunho';") or exit("Erro ao buscar as opções de status.");
        $this->mysqli = NULL;
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
     * 	@return string
     */
    public function getRelatorioProcessos(int $tipo): string {
        $retorno = "";
        // tratando tipo == 0 primeiro, buscando TODOS os processos
        $where = "";
        if ($tipo != 0) {
            $where = "AND processos.tipo = " . $tipo;
        }
        if (is_null($this->mysqli)) {
            $this->mysqli = parent::getConexao();
        }
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

    /**
     * 	Função que retorna as options para tipo de processos para o cadastro de novos processos.
     *
     * 	@return string
     */
    public function getTiposProcessos(): string {
        $retorno = "";
        if (is_null($this->mysqli)) {
            $this->mysqli = parent::getConexao();
        }
        $query = $this->mysqli->query("SELECT id, nome FROM processos_tipo;") or exit("Erro ao buscar os tipos de processo.");
        $this->mysqli = NULL;
        while ($tipo = $query->fetch_object()) {
            $tipo->nome = utf8_encode($tipo->nome);
            $retorno .= "<option value=\"" . $tipo->id . "\">" . $tipo->nome . "</option>";
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
        if (is_null($this->mysqli)) {
            $this->mysqli = parent::getConexao();
        }
        $query = $this->mysqli->query("SELECT id_setor FROM pedido WHERE id = {$id_pedido};") or exit("Erro ao buscar o id do setor do pedido.");
        $this->mysqli = NULL;
        $obj = $query->fetch_object();
        return $obj->id_setor;
    }

    /**
     * 	Função que retornar se um pedido é ou não rascunho.
     *
     * 	@param $id_pedido id do pedido.
     * 	@return Se o pedido é um rascunho - true, senão false.
     */
    public function getRequestDraft(int $id_pedido): bool {
        if (is_null($this->mysqli)) {
            $this->mysqli = parent::getConexao();
        }
        $query = $this->mysqli->query("SELECT prioridade.nome FROM pedido, prioridade WHERE pedido.id = {$id_pedido} AND pedido.prioridade = prioridade.id;") or exit("Erro ao buscar prioridade do pedido.");
        $this->mysqli = NULL;
        $obj = $query->fetch_object();
        return $obj->nome == 'Rascunho';
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
        if (is_null($this->mysqli)) {
            $this->mysqli = parent::getConexao();
        }
        $query = $this->mysqli->query("SELECT status.id, status.nome FROM status WHERE status.id <> 1;") or exit("Erro ao buscar as opções de status.");
        $this->mysqli = NULL;
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
        return $retorno;
    }

    /**
     * 	Função que retornar os radioBtn das prioridades dos pedidos.
     *
     * 	@return Opções de prioridades para os pedidos.
     */
    public function getPrioridades(): string {
        $retorno = "";
        if (is_null($this->mysqli)) {
            $this->mysqli = parent::getConexao();
        }
        $query = $this->mysqli->query("SELECT id, nome FROM prioridade;") or exit("Erro ao buscar prioridades.");
        $this->mysqli = NULL;
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
        return $retorno;
    }

    /**
     *   Função utilizada para retornar as informações de um processo clicado da tabela da recepção.
     *
     *   @return Informações do processo.
     */
    public function getInfoProcesso(int $id_processo): string {
        if (is_null($this->mysqli)) {
            $this->mysqli = parent::getConexao();
        }
        $query = $this->mysqli->query("SELECT processos.num_processo, processos.tipo, processos.estante, processos.prateleira, processos.entrada, processos.saida, processos.responsavel, processos.retorno, processos.obs FROM processos WHERE processos.id = {$id_processo};") or exit("Erro ao buscar informações dos processos.");
        $this->mysqli = NULL;
        $obj = $query->fetch_object();
        return json_encode($obj);
    }

    /**
     *   Função utilizada para retornar a tabela dos processos da recepção.
     *
     *   @return string
     */
    public function getTabelaRecepcao(): string {
        $retorno = "";
        if (is_null($this->mysqli)) {
            $this->mysqli = parent::getConexao();
        }
        $query = $this->mysqli->query("SELECT processos.id, processos.num_processo, processos_tipo.nome as tipo, processos.estante, processos.prateleira, processos.entrada, processos.saida, processos.responsavel, processos.retorno, processos.obs FROM processos, processos_tipo WHERE processos.tipo = processos_tipo.id ORDER BY id ASC;") or exit("Erro ao formar a tabela da recepção.");
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
        $this->mysqli = NULL;
        return $retorno;
    }

    /**
     * Retorna as permissões para cadastro de usuários.
     */
    public function getCheckPermissoes(): string {
        if (is_null($this->mysqli)) {
            $this->mysqli = parent::getConexao();
        }
        $query = $this->mysqli->query("SELECT column_name AS nome FROM information_schema.columns WHERE table_name = 'usuario_permissoes' AND column_name <> 'id_usuario';") or exit("Erro ao buscar permissões.");
        $this->mysqli = NULL;
        $retorno = "";
        $i = 1;
        while ($obj = $query->fetch_object()) {
            $nome = ucfirst($obj->nome);
            $retorno .= "
                <div class=\"form-group\" style=\"display: inline-block;\">
                    <label>
                        <input id=\"perm{$i}\" type=\"checkbox\" name=\"" . $obj->nome . "\" class=\"minimal\"/>
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
        if (is_null($this->mysqli)) {
            $this->mysqli = parent::getConexao();
        }
        $query = $this->mysqli->query("SELECT usuario_permissoes.noticias, usuario_permissoes.saldos, usuario_permissoes.pedidos, usuario_permissoes.recepcao FROM usuario_permissoes WHERE usuario_permissoes.id_usuario = {$id_user};") or exit("Erro ao buscar permissões do usuário.");
        $this->mysqli = NULL;
        $obj_permissoes = $query->fetch_object();
        return $obj_permissoes;
    }

    /**
     * Função que busca os detalhes de uma notícia completa.
     *
     * @return Informação da postagem.
     */
    public function getInfoNoticia(int $id): string {
        if (is_null($this->mysqli)) {
            $this->mysqli = parent::getConexao();
        }
        $query = $this->mysqli->query("SELECT postagem FROM postagens WHERE id = {$id};") or exit("Erro ao buscar informações da notícia.");
        $this->mysqli = NULL;
        $noticia = $query->fetch_object();
        return html_entity_decode($noticia->postagem);
    }

    /**
     * Função para mostrar uma tabela com todas as publicações de certa página
     *
     * @param $tabela -> filtra por nome da tabela
     * @return string
     */
    public function getPostagens(string $tabela): string {
        //declarando retorno
        $retorno = "";
        if (is_null($this->mysqli)) {
            $this->mysqli = parent::getConexao();
        }
        $query = $this->mysqli->query("SELECT postagens.id, postagens.titulo, DATE_FORMAT(postagens.data, '%d/%m/%Y') AS data FROM postagens, paginas_post WHERE postagens.tabela = paginas_post.id AND paginas_post.tabela = '{$tabela}' AND ativa = 1 ORDER BY data ASC;") or exit("Erro ao buscar postagens.");
        $this->mysqli = NULL;
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
     * @param $slide (1 ou 2)-> o primeiro mostra as últimas notícias e o segundo aleatórias
     * @return string
     */
    public function getSlide(int $slide): string {
        $order = "";
        if ($slide == 1) {
            $order = "postagens.data DESC";
        } else {
            $order = "rand()";
        }
        $retorno = "";
        $array_anima = array("primeira", "segunda", "terceira", "quarta", "quinta");
        $array_id = array("primeiro", "segundo", "terceiro", "quarto", "quinto");
        if (is_null($this->mysqli)) {
            $this->mysqli = parent::getConexao();
        }
        $query_postagem = $this->mysqli->query("SELECT postagens.id, postagens.postagem, DATE_FORMAT(postagens.data, '%d/%m/%Y') AS data, paginas_post.tabela, postagens.titulo FROM postagens, paginas_post WHERE postagens.tabela = paginas_post.id AND postagens.ativa = 1 ORDER BY {$order} LIMIT 5;") or exit("Erro ao buscar notícias dos slides.");
        $this->mysqli = NULL;
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
     * Função para pesquisar alguma publicação.
     *
     * @return Publicações com o título parecido com $busca.
     */
    public function pesquisar(string $busca): string {
        $retorno = "";
        $busca = htmlentities($busca);
        //escapando string especiais para evitar SQL Injections
        if (is_null($this->mysqli)) {
            $this->mysqli = parent::getConexao();
        }
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
        $query = $this->mysqli->query("SELECT postagens.id, postagens.tabela, postagens.titulo, DATE_FORMAT(postagens.data, '%d/%m/%Y') AS data, postagens.ativa FROM postagens WHERE postagens.titulo LIKE '%{$busca}%' AND postagens.ativa = 1 ORDER BY postagens.data DESC;") or exit("Erro ao pesquisar notícias.");
        $this->mysqli = NULL;
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
     * 	Função que retorna a tabela com as solicitações de alteração de pedidos	para o SOF analisar
     *
     * 	@return string
     */
    public function getAdminSolicAltPedidos(int $st): string {
        $retorno = "";
        if (is_null($this->mysqli)) {
            $this->mysqli = parent::getConexao();
        }
        $query = $this->mysqli->query("SELECT solic_alt_pedido.id, solic_alt_pedido.id_pedido, setores.nome, DATE_FORMAT(solic_alt_pedido.data_solicitacao, '%d/%m/%Y') AS data_solicitacao, DATE_FORMAT(solic_alt_pedido.data_analise, '%d/%m/%Y') AS data_analise, solic_alt_pedido.justificativa, solic_alt_pedido.status FROM solic_alt_pedido, setores WHERE solic_alt_pedido.id_setor = setores.id AND solic_alt_pedido.status = {$st} ORDER BY solic_alt_pedido.id DESC;") or exit("Erro ao buscar as solicitações de alteração de pedidos enviados ao SOF.");
        $status = $label = "";
        while ($solic = $query->fetch_object()) {
            switch ($solic->status) {
                case 0:
                    $status = 'Reprovado';
                    $label = 'red';
                    break;
                case 1:
                    $status = 'Aprovado';
                    $label = 'green';
                    break;
                default:
                    $status = 'Aberto';
                    $label = 'orange';
                    $solic->data_analise = '--------------';
                    break;
            }
            $btn_aprovar = $btn_reprovar = "";
            if ($st == 2) {
                $btn_aprovar = "<a title=\"Aprovar\" href=\"javascript:analisaSolicAlt(" . $solic->id . ", " . $solic->id_pedido . ", 1);\"><i class=\"fa fa-check\"></i></a>";
                $btn_reprovar = "<a title=\"Reprovar\" href=\"javascript:analisaSolicAlt(" . $solic->id . ", " . $solic->id_pedido . ", 0);\"><i class=\"fa fa-trash\"></i></a>";
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
                        <button onclick=\"viewCompl('" . $solic->justificativa . "');\" class=\"btn btn-sm btn-primary\" type=\"button\" title=\"Ver Justificativa\">JUSTIFICATIVA</button>
                    </td>
                    <td><small class=\"label pull-right bg-" . $label . "\">" . $status . "</small></td>
                </tr>";
        }
        $this->mysqli = NULL;
        return $retorno;
    }

    /**
     * 	Função que retorna as solicitações de adiantamentos de saldos enviadas ao SOF para análise.
     *
     * 	@param $st Status
     * 	@return string
     */
    public function getSolicAdiantamentos(int $st): string {
        if (is_null($this->mysqli)) {
            $this->mysqli = parent::getConexao();
        }
        $query = $this->mysqli->query("SELECT saldos_adiantados.id, setores.nome, DATE_FORMAT(saldos_adiantados.data_solicitacao, '%d/%m/%Y') AS data_solicitacao, DATE_FORMAT(saldos_adiantados.data_analise, '%d/%m/%Y') AS data_analise, saldos_adiantados.valor_adiantado, saldos_adiantados.justificativa FROM saldos_adiantados, setores WHERE saldos_adiantados.id_setor = setores.id AND saldos_adiantados.status = {$st} ORDER BY saldos_adiantados.data_solicitacao DESC;") or exit("Erro ao buscar solicitações de adiantamento.");
        $retorno = "";
        $status = $label = "";
        switch ($st) {
            case 0:
                $status = 'Reprovado';
                $label = 'red';
                break;
            case 1:
                $status = 'Aprovado';
                $label = 'green';
                break;
            case 2:
                $status = 'Aberto';
                $label = 'orange';
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
                    $btn_aprovar = "<a title=\"Aprovar\" href=\"javascript:analisaAdi(" . $solic->id . ", 1);\"><i class=\"fa fa-check\"></i></a>";
                    $btn_reprovar = "<a title=\"Reprovar\" href=\"javascript:analisaAdi(" . $solic->id . ", 0);\"><i class=\"fa fa-trash\"></i></a>";
                }
                $solic->justificativa = $this->mysqli->real_escape_string($solic->justificativa);
                $solic->justificativa = str_replace("\"", "'", $solic->justificativa);
                $solic->valor_adiantado = number_format($solic->valor_adiantado, 3, ',', '.');
                $retorno .= "
                    <tr>
                        <td>" . $btn_aprovar . $btn_reprovar . "</td>
                        <td>" . $solic->nome . "</td>
                        <td>" . $solic->data_solicitacao . "</td>
                        <td>" . $solic->data_analise . "</td>
                        <td>R$ " . $solic->valor_adiantado . "</td>
                        <td>
                            <button onclick=\"viewCompl('" . $solic->justificativa . "');\" class=\"btn btn-sm btn-primary\" type=\"button\" title=\"Ver Justificativa\">JUSTIFICATIVA</button>
                        </td>
                        <td><small class=\"label pull-right bg-" . $label . "\">" . $status . "</small></td>
                    </tr>";
            }
        }
        $this->mysqli = NULL;
        return $retorno;
    }

    private function getGrupoPedido(int $id_pedido): string {
        if (is_null($this->mysqli)) {
            $this->mysqli = parent::getConexao();
        }
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

    private function getEmpenho(int $id_pedido): string {
        if (is_null($this->mysqli)) {
            $this->mysqli = parent::getConexao();
        }

        $query = $this->mysqli->query("SELECT contrato_tipo.nome, pedido_contrato.siafi FROM contrato_tipo, pedido_contrato WHERE pedido_contrato.id_tipo = contrato_tipo.id AND pedido_contrato.id_pedido = {$id_pedido};") or exit("Erro ao buscar o contrato do pedido.");
        $this->mysqli = NULL;
        $retorno = "";
        if ($query->num_rows > 0) {
            $obj = $query->fetch_object();
            $retorno = "<b>Tipo de Empenho:</b> " . $obj->nome . " <input type=\"text\" value=\"" . $obj->siafi . "\"/>";
        }
        return $retorno;
    }

    /**
     * Função para retornar o cabeçalho do pdf do pedido.
     *
     * @return string
     */
    public function getHeader(int $id_pedido): string {
        if (is_null($this->mysqli)) {
            $this->mysqli = parent::getConexao();
        }
        $query = $this->mysqli->query("SELECT pedido.id, DATE_FORMAT(pedido.data_pedido, '%d/%m/%Y') AS data_pedido, EXTRACT(YEAR FROM pedido.data_pedido) AS ano, mes.sigla_mes AS ref_mes, status.nome AS status, replace(pedido.valor, '.', ',') AS valor, pedido.obs, pedido.pedido_contrato, prioridade.nome AS prioridade FROM prioridade, pedido, mes, status WHERE pedido.prioridade = prioridade.id AND status.id = pedido.status AND pedido.id = {$id_pedido} AND mes.id = pedido.ref_mes;") or exit("Erro ao formar o cabeçalho do pedido.");
        $this->mysqli = NULL;
        $pedido = $query->fetch_object();
        $pedido->valor = number_format($pedido->valor, 3, ',', '.');
        $lblPedido = "Pedido";
        if ($pedido->pedido_contrato) {
            $lblPedido = "Pedido de Contrato";
        }
        $retorno = "
            <fieldset>
                <p>
                    <b>" . $lblPedido . ":</b> " . $id_pedido . "
                    <b>Data de Envio:</b> " . $pedido->data_pedido . ".&emsp;
                    <b>Situação:</b> " . $pedido->status . "&emsp;
                    <b>Prioridade:</b> " . $pedido->prioridade . "&emsp;
                    <b>Total do Pedido:</b> R$ " . $pedido->valor . "
                </p>
                <p>" . BuscaLTE::getGrupoPedido($id_pedido) . "&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;" . BuscaLTE::getEmpenho($id_pedido) . "</p>
                <p><b>Observação da Unidade Solicitante: </b></p>
                <p style=\"font-weight: normal !important;\">	" . $pedido->obs . "</p>
            </fieldset><br>";
        $retorno .= BuscaLTE::getTableFontes($id_pedido);
        $retorno .= BuscaLTE::getTableLicitacao($id_pedido);
        return $retorno;
    }

    private function getTableLicitacao(int $id_pedido): string {
        $retorno = "<fieldset>
                <h5>PEDIDO SEM LICITAÇÃO</h5>
                </fieldset><br>";

        if (is_null($this->mysqli)) {
            $this->mysqli = parent::getConexao();
        }
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
        $retorno = "";
        if (is_null($this->mysqli)) {
            $this->mysqli = parent::getConexao();
        }
        $query = $this->mysqli->query("SELECT pedido_fonte.fonte_recurso, pedido_fonte.ptres, pedido_fonte.plano_interno FROM pedido_fonte WHERE pedido_fonte.id_pedido = {$id_pedido};") or exit("Erro ao buscar fontes do pedido.");
        $this->mysqli = NULL;
        if ($query->num_rows > 0) {
            $fonte = $query->fetch_object();
            $retorno = "
            <fieldset class = \"preg\">
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
     * @return string
     */
    public function getContentPedido(int $id_pedido): string {
        $retorno = "";
        // PRIMEIRO FAZEMOS O CABEÇALHO REFERENTE AO NUM_LICITACAO
        if (is_null($this->mysqli)) {
            $this->mysqli = parent::getConexao();
        }
        $query_ini = $this->mysqli->query("SELECT DISTINCT itens.num_licitacao, itens.num_processo, itens.dt_inicio, itens.dt_fim FROM itens_pedido, itens WHERE itens.id = itens_pedido.id_item AND itens_pedido.id_pedido = {$id_pedido};") or exit("Erro ao buscar itens do pedido.");
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
            $query_forn = $this->mysqli->query("SELECT DISTINCT itens.cgc_fornecedor, itens.nome_fornecedor, itens.num_contrato FROM itens, itens_pedido WHERE itens.id = itens_pedido.id_item AND itens_pedido.id_pedido = {$id_pedido} AND itens.num_licitacao = {$licitacao->num_licitacao};") or exit("Erro ao buscar fornecedores do pedido.");

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
                $query_itens = $this->mysqli->query("SELECT itens.cod_reduzido, itens.cod_despesa, itens.seq_item_processo, itens.complemento_item, itens.vl_unitario, itens_pedido.qtd, itens_pedido.valor FROM itens, itens_pedido WHERE itens.id = itens_pedido.id_item AND itens_pedido.id_pedido = {$id_pedido} AND itens.cgc_fornecedor = '{$fornecedor->cgc_fornecedor}'") or exit("Erro ao buscar os itens dos fornecedores do pedido.");
                $retorno .= "
                    <table class=\"prod\">
                        <thead>
                            <tr>
                                <th>Código</th>
                                <th>Item</th>
                                <th>Natureza</th>
                                <th>Descrição</th>
                                <th>Quantidade</th>
                                <th>Valor Unitário</th>
                                <th>Valor Total</th>
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
                                <td>" . $item->cod_despesa . "</td>
                                <td>" . $item->complemento_item . "</td>
                                <td>" . $item->qtd . "</td>
                                <td>R$ " . $item->vl_unitario . "</td>
                                <td>R$ " . $item->valor . "</td>
                            </tr>";
                }
                $retorno .= "
                        </tbody>
                </table><br>";
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
        if (is_null($this->mysqli)) {
            $this->mysqli = parent::getConexao();
        }
        $query_emp = $this->mysqli->query("SELECT pedido_empenho.empenho, DATE_FORMAT(pedido_empenho.data, '%d/%m/%Y') AS data FROM pedido_empenho WHERE pedido_empenho.id_pedido = {$id_pedido};") or exit("Erro ao mostrar o empenho do pedido nos comentários.");
        if ($query_emp->num_rows > 0) {
            $empenho = $query_emp->fetch_object();
            $retorno = "
                <fieldset class=\"preg\">
                    <table>
                        <tr>
                            <td>Data Empenho: " . $empenho->data . "</td>
                            <td>Empenho: " . $empenho->empenho . "</td>
                        </tr>
                    </table>
                </fieldset>";
        } else {
            $retorno = "
                <fieldset class=\"preg\">
                    <table>
                        <tr>
                            <td>Empenho: EMPENHO SIAFI PENDENTE </td>;
                        </tr>
                    </table>
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
     * 	Função que retorna as 'tabs' com as ṕáginas das notícias para editar.
     *
     * 	@return string
     */
    public function getTabsNoticias(): string {
        $retorno = "";
        if (is_null($this->mysqli)) {
            $this->mysqli = parent::getConexao();
        }
        $query = $this->mysqli->query("SELECT paginas_post.id, paginas_post.tabela, paginas_post.nome FROM paginas_post;") or exit("Erro ao buscar as abas de notícias para edição.");
        $this->mysqli = NULL;
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
        return $retorno;
    }

    /**
     * 	Função para retornar a tabela de notícias de uma página para edição
     *
     * 	@return string
     */
    public function getNoticiasEditar(int $tabela): string {
        $retorno = "";
        if (is_null($this->mysqli)) {
            $this->mysqli = parent::getConexao();
        }
        $query = $this->mysqli->query("SELECT postagens.id, postagens.tabela, postagens.titulo, DATE_FORMAT(postagens.data, '%d/%m/%Y') AS data FROM postagens WHERE postagens.ativa = 1 AND postagens.tabela = {$tabela} ORDER BY postagens.data ASC;") or exit("Erro ao buscar as notícias para editar.");
        $this->mysqli = NULL;
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
        return $retorno;
    }

    /**
     * Função para buscar conteúdo de uma publicação para edição.
     *
     * @return string
     */
    public function getPublicacaoEditar(int $id): string {
        if (is_null($this->mysqli)) {
            $this->mysqli = parent::getConexao();
        }
        $query = $this->mysqli->query("SELECT postagens.postagem FROM postagens WHERE id={$id};") or exit("Erro ao buscar postagem.");
        $this->mysqli = NULL;
        $publicacao = $query->fetch_object();
        return $publicacao->postagem;
    }

    /**
     * Função para escrever as opções para "Postar em " do painel administrativo
     *
     * @return string
     */
    public function getPostarEm(): string {
        $retorno = "";
        if (is_null($this->mysqli)) {
            $this->mysqli = parent::getConexao();
        }
        $query = $this->mysqli->query("SELECT id, nome FROM paginas_post;") or exit("Erro ao buscar as páginas para postagem.");
        $this->mysqli = NULL;
        while ($pagina = $query->fetch_object()) {
            $retorno .= "<option id=\"op" . $pagina->id . "\" value=\"" . $pagina->id . "\">" . $pagina->nome . "</option>";
        }
        return $retorno;
    }

    /**
     * Função para retornar as solicitações para o SOF.
     *
     * @return string
     *
     */
    public function getSolicitacoesAdmin(): string {
        $retorno = "";
        if (is_null($this->mysqli)) {
            $this->mysqli = parent::getConexao();
        }
        $query = $this->mysqli->query("SELECT pedido.id, pedido.id_setor, setores.nome AS nome_setor, DATE_FORMAT(pedido.data_pedido, '%d/%m/%Y') AS data_pedido, mes.sigla_mes AS ref_mes, prioridade.nome AS prioridade, status.nome AS status, status.id AS id_status, pedido.valor FROM pedido, setores, mes, prioridade, status WHERE status.id = pedido.status AND pedido.status <> 3 AND prioridade.id = pedido.prioridade AND mes.id = pedido.ref_mes AND pedido.alteracao = 0 AND pedido.id_setor = setores.id ORDER BY pedido.id DESC LIMIT 500;") or exit("Erro ao buscar os pedidos que foram mandados ao SOF.");
        $this->mysqli = NULL;
        while ($pedido = $query->fetch_object()) {
            $btnAnalisar = "";
            if ($pedido->status != 'Reprovado' && $pedido->status != 'Aprovado') {
                if ($_SESSION['id_setor'] == 12) {
                    $btnAnalisar = "<a href=\"javascript:enviaForn(" . $pedido->id . ");\" title=\"Enviar ao Fornecedor\"><i class=\"fa fa-send\"></i></a>";
                } else if ($pedido->status == 'Em Analise') {
                    $btnAnalisar = "<a href=\"javascript:analisarPedido(" . $pedido->id . ", " . $pedido->id_setor . ");\" title=\"Analisar\"><i class=\"fa fa-pencil\"></i></a>";
                } else if ($pedido->status == 'Aguarda Orcamento') {
                    $btnAnalisar = "<a href=\"javascript:cadFontes(" . $pedido->id . ");\" title=\"Cadastrar Fontes\"><i class=\"fa fa-pie-chart\"></i></a>";
                } else if ($pedido->status == 'Aguarda SIAFI') {
                    $btnAnalisar = "<a href=\"javascript:cadEmpenho(" . $pedido->id . ");\" title=\"Cadastrar Empenho\"><i class=\"fa fa-comment\"></i></a>";
                } else if ($pedido->status == 'Empenhado') {
                    $btnAnalisar = "<a href=\"javascript:enviaOrdenador(" . $pedido->id . ");\" title=\"Enviar ao Ordenador\"><i class=\"fa fa-send\"></i></a>";
                } else {
                    $btnAnalisar = "<a href=\"javascript:getStatus(" . $pedido->id . ", " . $pedido->id_setor . ");\" title=\"Alterar Status\"><i class=\"fa fa-wrench\"></i></a>";
                }
            }
            $btnVerEmpenho = BuscaLTE::verEmpenho($pedido->id);
            if ($btnVerEmpenho == 'EMPENHO SIAFI PENDENTE') {
                $btnVerEmpenho = '';
            }
            $pedido->valor = number_format($pedido->valor, 3, ',', '.');
            $linha = "
                <tr id=\"rowPedido" . $pedido->id . "\">
                    <td>
                        " . $btnAnalisar . "
                        <a href=\"javascript:imprimir(" . $pedido->id . ");\" title=\"Imprimir\"><i class=\"fa fa-print\"></i></a>
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
        return $retorno;
    }

    /**
     * Função para trazer as informações de um pedido a ser analisado.
     *
     * @return string
     */
    public function getItensPedidoAnalise(int $id_pedido): string {
        $retorno = "";
        if (is_null($this->mysqli)) {
            $this->mysqli = parent::getConexao();
        }
        $query = $this->mysqli->query("SELECT itens.qt_contrato, itens.id AS id_itens, itens_pedido.qtd AS qtd_solicitada, itens_pedido.valor, itens.nome_fornecedor, itens.num_licitacao, itens.dt_inicio, itens.dt_fim, itens.cod_reduzido, itens.complemento_item, itens.vl_unitario, itens.qt_saldo, itens.cod_despesa, itens.descr_despesa, itens.num_contrato, itens.num_processo, itens.descr_mod_compra, itens.num_licitacao, itens.cgc_fornecedor, itens.num_extrato, itens.descricao, itens.qt_contrato, itens.vl_contrato, itens.qt_utilizado, itens.vl_utilizado, itens.qt_saldo, itens.vl_saldo, itens.seq_item_processo FROM itens_pedido, itens WHERE itens_pedido.id_pedido = {$id_pedido} AND itens_pedido.id_item = itens.id ORDER BY itens.seq_item_processo ASC;") or exit("Erro ao buscar os itens do pedido para análise.");
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
                    <td>" . $item->cod_despesa . "</td>
                    <td>" . $item->descr_despesa . "</td>
                    <td>" . $item->num_extrato . "</td>
                    <td>" . $item->num_contrato . "</td>
                    <td>" . $item->num_processo . "</td>
                    <td>" . $item->descr_mod_compra . "</td>
                    <td>" . $item->num_licitacao . "</td>
                    <td>" . $item->dt_inicio . "</td>
                    <td>" . $item->dt_fim . "</td>
                    <td>" . $item->cgc_fornecedor . "</td>
                    <td>" . $item->nome_fornecedor . "</td>
                    <td>" . $item->cod_reduzido . "</td>
                    <td>" . $item->seq_item_processo . "</td>
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
        $this->mysqli = NULL;
        return $retorno;
    }

    /**
     * Função para trazer o restante das informações para analisar o pedido:
     *               saldo, total, prioridade, fase, etc.
     *   
     * @return string
     */
    public function getInfoPedidoAnalise(int $id_pedido, int $id_setor): string {
        if (is_null($this->mysqli)) {
            $this->mysqli = parent::getConexao();
        }
        $query = $this->mysqli->query("SELECT saldo_setor.saldo, pedido.prioridade, pedido.status, pedido.valor, pedido.obs FROM saldo_setor, pedido WHERE saldo_setor.id_setor = {$id_setor} AND pedido.id = {$id_pedido};") or exit("Erro ao buscar as informações do pedido para análise.");
        $this->mysqli = NULL;
        $pedido = $query->fetch_object();
        return json_encode($pedido);
    }

    /**
     * 	Função que retorna uma tabela com as solicitações de alteração de pedidos
     *
     * 	@return string
     */
    public function getSolicAltPedidos(int $id_setor): string {
        $retorno = "";
        if (is_null($this->mysqli)) {
            $this->mysqli = parent::getConexao();
        }
        $query = $this->mysqli->query("SELECT solic_alt_pedido.id_pedido, DATE_FORMAT(solic_alt_pedido.data_solicitacao, '%d/%m/%Y') AS data_solicitacao, DATE_FORMAT(solic_alt_pedido.data_analise, '%d/%m/%Y') AS data_analise, solic_alt_pedido.justificativa, solic_alt_pedido.status FROM solic_alt_pedido WHERE solic_alt_pedido.id_setor = {$id_setor} ORDER BY id DESC;") or exit("Erro ao buscar solicitações de alteração de pedidos.");
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
        $this->mysqli = NULL;
        return $retorno;
    }

    /**
     * 	Função que retorna as solicitações de adiantamento de saldos do setor
     *
     * 	@return string
     */
    public function getSolicAdiSetor(int $id_setor): string {
        $retorno = "";
        if (is_null($this->mysqli)) {
            $this->mysqli = parent::getConexao();
        }
        $query = $this->mysqli->query("SELECT saldos_adiantados.id, DATE_FORMAT(saldos_adiantados.data_solicitacao, '%d/%m/%Y') AS data_solicitacao, DATE_FORMAT(saldos_adiantados.data_analise, '%d/%m/%Y') AS data_analise, saldos_adiantados.valor_adiantado, saldos_adiantados.justificativa, saldos_adiantados.status FROM saldos_adiantados WHERE saldos_adiantados.id_setor = {$id_setor} ORDER BY saldos_adiantados.id DESC;") or exit("Erro ao buscar solicitações de adiantamento.");
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
        $this->mysqli = NULL;
        return $retorno;
    }

    /**
     * Função para mostrar os itens de um processo pesquisado no menu solicitações.
     *
     * @return string
     */
    public function getConteudoProcesso(string $busca): string {
        $retorno = "";

        if (is_null($this->mysqli)) {
            $this->mysqli = parent::getConexao();
        }
        $query = $this->mysqli->query("SELECT itens.id, itens.id_item_processo, itens.nome_fornecedor, itens.cod_reduzido, itens.complemento_item, replace(itens.vl_unitario, ',', '.') AS vl_unitario, itens.qt_contrato, itens.qt_utilizado, itens.vl_utilizado, itens.qt_saldo, itens.vl_saldo FROM itens WHERE num_processo LIKE '%{$busca}%' AND cancelado = 0;") or exit("Erro ao buscar o conteúdo dos processos.");

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
        $this->mysqli = NULL;
        return $retorno;
    }

    /**
     * Função para trazer a linha do item anexado ao pedido
     *
     * @return string
     */
    public function addItemPedido(int $id_item, int $qtd): string {
        if (is_null($this->mysqli)) {
            $this->mysqli = parent::getConexao();
        }
        $query = $this->mysqli->query("SELECT itens.id, itens.nome_fornecedor, itens.num_licitacao, itens.cod_reduzido, itens.complemento_item, replace(itens.vl_unitario, ',', '.') AS vl_unitario, itens.qt_saldo, itens.qt_contrato, itens.qt_utilizado, itens.vl_saldo, itens.vl_contrato, itens.vl_utilizado FROM itens WHERE itens.id = {$id_item};") or exit("Erro ao buscar ");
        $item = $query->fetch_object();
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
        $this->mysqli = NULL;
        return $retorno;
    }

    /**
     * Função para retornar os rascunhos para continuar editando.
     *
     * @return string
     */
    public function getRascunhos(int $id_setor): string {
        $retorno = "";
        if (is_null($this->mysqli)) {
            $this->mysqli = parent::getConexao();
        }
        $query = $this->mysqli->query("SELECT pedido.id, DATE_FORMAT(pedido.data_pedido, '%d/%m/%Y') AS data_pedido, mes.sigla_mes AS ref_mes, pedido.valor, status.nome AS status FROM pedido, mes, status WHERE pedido.id_setor = {$id_setor} AND pedido.alteracao = 1 AND mes.id = pedido.ref_mes AND status.id = pedido.status ORDER BY pedido.id DESC;") or exit("Erro ao buscar rascunhos do setor.");
        $this->mysqli = NULL;

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
        return $retorno;
    }

    /**
     * 	Função que retorna o saldo dispónível do setor.
     *
     * 	@return string
     */
    public function getSaldo(int $id_setor): string {
        if (is_null($this->mysqli)) {
            $this->mysqli = parent::getConexao();
        }
        $query = $this->mysqli->query("SELECT saldo_setor.saldo FROM saldo_setor WHERE saldo_setor.id_setor = {$id_setor};") or exit("Erro ao buscar o saldo do setor.");
        if ($query->num_rows < 1) {
            $this->mysqli->query("INSERT INTO saldo_setor VALUES(NULL, {$id_setor}, '0.000');") or exit("Erro ao inserir o saldo do setor.");
            $this->mysqli = NULL;
            return '0.000';
        }
        $this->mysqli = NULL;
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
        if (is_null($this->mysqli)) {
            $this->mysqli = parent::getConexao();
        }
        $query = $this->mysqli->query("SELECT itens.qt_contrato, itens.id AS id_itens, itens_pedido.qtd AS qtd_solicitada, itens_pedido.valor, itens.nome_fornecedor, itens.num_licitacao, itens.cod_reduzido, itens.complemento_item, replace(itens.vl_unitario, ',', '.') AS vl_unitario, itens.qt_saldo, itens.qt_contrato, itens.qt_utilizado, itens.vl_saldo, itens.vl_contrato, itens.vl_utilizado FROM itens_pedido, itens WHERE itens_pedido.id_pedido = {$id_pedido} AND itens_pedido.id_item = itens.id") or exit("Erro ao buscar o conteúdo do pedido.");
        while ($item = $query->fetch_object()) {
            $id_item = $item->id_itens;
            $item->complemento_item = $this->mysqli->real_escape_string($item->complemento_item);
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
        $this->mysqli = NULL;
        return $retorno;
    }

    /**
     * Função dispara logo após clicar em editar rascunho de pedido.
     *
     * @return string
     */
    public function getPopulaRascunho(int $id_pedido, int $id_setor): string {
        if (is_null($this->mysqli)) {
            $this->mysqli = parent::getConexao();
        }
        $query = $this->mysqli->query("SELECT saldo_setor.saldo, pedido.valor, pedido.obs FROM saldo_setor, pedido WHERE pedido.id = {$id_pedido} AND saldo_setor.id_setor = {$id_setor};") or exit("Erro ao buscar informações do rascunho.");
        $this->mysqli = NULL;
        $pedido = $query->fetch_object();
        return json_encode($pedido);
    }

    /**
     * Função para o setor acompanhar o andamento do seu pedido.
     *
     * @return string
     */
    public function getMeusPedidos(int $id_setor): string {
        $retorno = "";
        if (is_null($this->mysqli)) {
            $this->mysqli = parent::getConexao();
        }
        $query = $this->mysqli->query("SELECT pedido.id, DATE_FORMAT(pedido.data_pedido, '%d/%m/%Y') AS data_pedido, mes.sigla_mes AS ref_mes, prioridade.nome AS prioridade, status.nome AS status, pedido.valor FROM pedido, mes, prioridade, status WHERE prioridade.id = pedido.prioridade AND status.id = pedido.status AND pedido.id_setor = {$id_setor} AND pedido.alteracao = 0 AND mes.id = pedido.ref_mes ORDER BY pedido.id DESC;") or exit("Erro ao buscar os pedidos do setor.");
        $this->mysqli = NULL;
        while ($pedido = $query->fetch_object()) {
            $empenho = BuscaLTE::verEmpenho($pedido->id);
            if ($empenho == 'EMPENHO SIAFI PENDENTE') {
                $empenho = '';
            }
            $pedido->valor = number_format($pedido->valor, 3, ',', '.');
            $btnSolicAlt = "";
            if ($pedido->status == 'Em Analise' || $pedido->status == 'Aguarda Orcamento') {
                $btnSolicAlt = "<button class=\"btn btn-default btn-sm\" style=\"text-transform: none !important;font-weight: bold;\" onclick=\"solicAltPed(" . $pedido->id . ");\" title=\"Solicitar Alteração\"><span class=\"icon\">build</span></button>";
            }
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
                        " . $btnSolicAlt . "
                        <button class=\"btn btn-default btn-sm\" style=\"text-transform: none !important;font-weight: bold;\" onclick=\"imprimir(" . $pedido->id . ");\" title=\"Imprimir\"><span class=\"icon\">print</span></button>
                    </td>
                </tr>";
        }
        return $retorno;
    }

    /**
     * Retorna todos os processos existes no banco.
     * 
     * @param string $tela Se "recepcao" os processos são usadas para uma coisa se não, são usados para construir um pedido.
     * @return string LInhas com os processos para colocar numa tabela.
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
        if (is_null($this->mysqli)) {
            $this->mysqli = parent::getConexao();
        }
        $query = $this->mysqli->query($sql) or exit("Erro ao buscar os processos.");
        $this->mysqli = NULL;
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

    public function getLicitacao(int $id_pedido) {
        if (is_null($this->mysqli)) {
            $this->mysqli = parent::getConexao();
        }
        $query = $this->mysqli->query("SELECT licitacao.id, licitacao.tipo, licitacao.numero, licitacao.uasg, licitacao.processo_original FROM licitacao WHERE licitacao.id_pedido = {$id_pedido};") or exit("Erro ao buscar as licitações do pedido.");
        $this->mysqli = NULL;
        $retorno = false;
        if ($query->num_rows > 0) {
            $obj = $query->fetch_object();
            $retorno = json_encode($obj);
        }

        return $retorno;
    }

}

?>
