<?php

/**
 *  Classe com as funções de busca para impressão do sistema.
 *
 *  @author João Bolsson (joaovictorbolsson@gmail.com).
 *  @since 2017, 28 Jan.
 */
ini_set('display_erros', true);
error_reporting(E_ALL);

include_once 'Conexao.class.php';

class PrintMod extends Conexao {

    private $mysqli;

    public function __construct() {
        parent::__construct();
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
        $lblPedido = "Pedido";
        if ($pedido->pedido_contrato) {
            $lblPedido = "Pedido de Contrato";
        }
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
                <table style=\"font-size: 8pt; margin: 5px;\">
                    <tr>
                        <td style=\"text-align: left;\">" . PrintMod::getGrupoPedido($id_pedido) . "</td>
                        <td style=\"text-align: right;\">" . PrintMod::getEmpenho($id_pedido) . "</td>
                    </tr>
                </table>
                <p><b>Observação da Unidade Solicitante: </b></p>
                <p style=\"font-weight: normal !important;\">	" . $pedido->obs . "</p>
            </fieldset><br>";
        $retorno .= PrintMod::getTableFontes($id_pedido);
        $retorno .= PrintMod::getTableLicitacao($id_pedido);
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

}
