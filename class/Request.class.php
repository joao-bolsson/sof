<?php
/**
 * Class that represents a request (table "pedidos" in db).
 *
 * @author João Bolsson (joaovictorbolsson@gmail.com).
 * @since 2017, 13 Ago.
 */

defined('NEW_REQUEST_ID') or define('NEW_REQUEST_ID', 0);

final class Request {

    /**
     * Request id.
     * @var int
     */
    private $id;

    /**
     * User id that made this request.
     * @var int
     */
    private $id_user;

    /**Sector id that made this request.
     * @var int
     */
    private $id_sector;

    /** Request date.
     * @var string
     */
    private $request_date;

    /**Month referenced by this request.
     * @var int
     */
    private $ref_month;

    /** This request can be changed by source sector.
     * @var bool
     */
    private $change;

    /** Request priority.
     * @var int
     */
    private $priority;

    /**
     * Request status.
     * @var int
     */
    private $status;

    /**
     * @param int $status New request status.
     */
    public function setStatus(int $status) {
        $this->status = $status;
        if ($this->id != NEW_REQUEST_ID) {
            Query::getInstance()->exe('UPDATE pedido SET status = ' . $this->status . ' WHERE id = ' . $this->id);
        }
    }

    /**
     * Request value
     * @var string
     */
    private $value;

    /**
     * Observation (optional).
     * @var string
     */
    private $obs;

    /**
     * Flag that indicates if this request is a contract request.
     * @var bool
     */
    private $contract_request;

    /**
     * Flag that indicates if this request was approved by management.
     * @var bool
     */
    private $approv_manager;

    /**
     * @var array Arrays of itens of this request.
     */
    private $itens;

    /**
     * Default construct.
     * @param int $id Id request (0 for a no existing request in db).
     */
    public function __construct(int $id) {
        $this->id = $id;
        if ($this->id != NEW_REQUEST_ID) {
            $this->fillFieldsFromDB();
        }
    }

    private function fillFieldsFromDB() {
        $query = Query::getInstance()->exe("SELECT id_setor, id_usuario, data_pedido, ref_mes, alteracao, prioridade, status, valor, obs, pedido_contrato, aprov_gerencia FROM pedido WHERE id = " . $this->id);

        $obj = $query->fetch_object();

        $this->id_user = $obj->id_usuario;
        $this->id_sector = $obj->id_setor;
        $this->request_date = $obj->data_pedido;
        $this->ref_month = $obj->ref_mes;
        $this->change = $obj->alteracao;
        $this->priority = $obj->prioridade;
        $this->status = $obj->status;
        $this->value = $obj->valor;
        $this->obs = $obj->obs;
        $this->contract_request = $obj->pedido_contrato;
        $this->approv_manager = $obj->aprov_gerencia;

        $this->fillItens();
    }

    private function fillItens() {
        $query = Query::getInstance()->exe("SELECT id_item, qtd, valor FROM itens_pedido WHERE id_pedido = " . $this->id);

        if ($query->num_rows > 0 && count($this->itens) == 0) {
            $i = 0;
            while ($obj = $query->fetch_object()) {
                $this->itens[$i++] = new Item($obj->id);
            }
        } else {
            Logger::info("[ERROR] pedido sem itens, id pedido: " . $this->id);
        }
    }

    /**
     * Add a comment to this request.
     * @param string $comment Comment to insert for this request.
     */
    public function addComment(string $comment) {
        if (strlen($comment) > 0) {
            $hoje = date('Y-m-d');
            $escaped_comment = Query::getInstance()->real_escape_string($comment);
            $builder = new SQLBuilder(SQLBuilder::$INSERT);
            $builder->setTables(['comentarios']);
            $builder->setValues([NULL, $this->id, $hoje, $this->priority, $this->status, $this->value, $escaped_comment]);

            Query::getInstance()->exe($builder->__toString());
        }
    }

    public function manage($fase, $prioridade, $id_item, $item_cancelado, $qtd_solicitada, $qt_saldo, $qt_utilizado, $vl_saldo, $vl_utilizado, $valor_item, $saldo_setor, $total_pedido, $comentario) {
        $hoje = date('Y-m-d');
        $total_pedido_float = $total_pedido;
        $id_setor = $this->id_sector;

        $has_sources = Geral::existsSources($this->id);
        // verificando itens cancelados, somente quando passam pela análise
        if ($fase <= 4) {
            if (in_array(1, $item_cancelado) || in_array(true, $item_cancelado) || $fase == 3) {
                // só percorre os itens se tiver algum item cancelado ou se o pedido for reprovado
                for ($i = 0; $i < count($id_item); $i++) {
                    $qt_saldo[$i] += $qtd_solicitada[$i];
                    $qt_utilizado[$i] -= $qtd_solicitada[$i];
                    $vl_saldo[$i] += $valor_item[$i];
                    $vl_utilizado[$i] -= $valor_item[$i];
                    $cancelado = '';
                    if ($item_cancelado[$i]) {
                        $cancelado = ', cancelado = 1';
                        Query::getInstance()->exe('DELETE FROM itens_pedido WHERE id_pedido = ' . $this->id . ' AND id_item = ' . $id_item[$i]);
                        $total_pedido -= $valor_item[$i];
                        $total_pedido_float -= $valor_item[$i];
                    }
                    Query::getInstance()->exe("UPDATE itens SET qt_saldo = '{$qt_saldo[$i]}', qt_utilizado = '{$qt_utilizado[$i]}', vl_saldo = '{$vl_saldo[$i]}', vl_utilizado = '{$vl_utilizado[$i]}'{$cancelado} WHERE id = " . $id_item[$i]);
                    if ($has_sources) {
                        $saldo_setor += $valor_item[$i];
                        // se não tiver fonte, o saldo do setor não é alterado e a o valor do pedido retorna ao sof
                    }
                }
            }
        }
        // alterar o status do pedido
        $alteracao = 0;
        if ($fase == 2 || $fase == 3 || $fase == 4) {
            $total_pedido = number_format($total_pedido, 3, '.', '');
            Query::getInstance()->exe("UPDATE pedido SET valor = '" . $total_pedido . "' WHERE id = " . $this->id);
            if ($fase == 3) {
                // reprovado
                $alteracao = 1;
                $prioridade = 5;
                if ($has_sources) {
                    // se for reprovado, devolve o valor retirado da fonte
                    $query_fonte = Query::getInstance()->exe("SELECT saldo_fonte.id AS id_fonte, saldo_fonte.valor AS saldo_fonte, pedido.valor AS valor_pedido FROM saldo_fonte, pedido_id_fonte, pedido WHERE pedido.id = pedido_id_fonte.id_pedido AND pedido_id_fonte.id_fonte = saldo_fonte.id AND pedido.id = " . $this->id);
                    if ($query_fonte->num_rows > 0) {
                        $obj = $query_fonte->fetch_object();
                        $new_vl = $obj->saldo_fonte + $obj->valor_pedido;
                        $new_vl = number_format($new_vl, 3, '.', '');
                        Query::getInstance()->exe("UPDATE saldo_fonte SET valor = '" . $new_vl . "' WHERE id_setor = " . $id_setor . " AND id = " . $obj->id_fonte);
                    }
                } else {
                    // devolve o valor do pedido ao sof
                    $saldo_sof = Busca::getSaldo(2);
                    $saldo_sof += $total_pedido_float;
                    $saldo_sof = number_format($saldo_sof, 3, '.', '');

                    Query::getInstance()->exe("UPDATE saldo_setor SET saldo = '" . $saldo_sof . "' WHERE id_setor = 2");

                    // coloca na tabela de lançamentos
                    // valor do pedido volta ao SOF

                    $sqlBuilder = new SQLBuilder(SQLBuilder::$INSERT);
                    $sqlBuilder->setTables(['saldos_lancamentos']);
                    $sqlBuilder->setValues([null, $id_setor, $hoje, -$total_pedido_float, 5]);
                    Query::getInstance()->exe($sqlBuilder->__toString());

                    $sqlBuilder->setValues([null, 2, $hoje, $total_pedido_float, 5]);
                    Query::getInstance()->exe($sqlBuilder->__toString());
                }
            } else if ($fase == 4) {
                // aprovado
                Query::getInstance()->exe("INSERT INTO saldos_lancamentos VALUES(NULL, {$id_setor}, '{$hoje}', '-{$total_pedido}', 4);");
                // próxima fase
                $fase++;
                // não precisa cadastrar fontes, elas já estão cadastradas
                if ($has_sources) {
                    $fase++;
                }
            }
        }
        Query::getInstance()->exe("UPDATE pedido SET status = " . $fase . ", prioridade = " . $prioridade . ", alteracao = " . $alteracao . " WHERE id = " . $this->id);
        $this->addComment($comentario);
        $error = Geral::checkForErrors($this->id);
        if ($error) {
            Logger::error("Pedido quebrado em pedidoAnalisado: " . $this->id);
        }
        return true;
    }

    /**
     * Delete a request from db.
     * @param int $id Request id to delete
     */
    public static function delete(int $id) {
        Query::getInstance()->exe("DELETE FROM pedido_id_fonte WHERE id_pedido = " . $id);
        Query::getInstance()->exe('DELETE FROM licitacao WHERE licitacao.id_pedido = ' . $id);
        Query::getInstance()->exe("DELETE FROM pedido_contrato WHERE pedido_contrato.id_pedido = " . $id);
        Query::getInstance()->exe("DELETE FROM pedido_grupo WHERE pedido_grupo.id_pedido = " . $id);
        Query::getInstance()->exe("DELETE FROM comentarios WHERE comentarios.id_pedido = " . $id);
        Query::getInstance()->exe("DELETE FROM itens_pedido WHERE itens_pedido.id_pedido = " . $id);
        Query::getInstance()->exe("DELETE FROM pedido_empenho WHERE pedido_empenho.id_pedido = " . $id);
        Query::getInstance()->exe("DELETE FROM pedido_fonte WHERE pedido_fonte.id_pedido = " . $id);
        Query::getInstance()->exe("DELETE FROM solic_alt_pedido WHERE solic_alt_pedido.id_pedido = " . $id);
        Query::getInstance()->exe("DELETE FROM pedido_log_status WHERE pedido_log_status.id_pedido = " . $id);
        Query::getInstance()->exe("DELETE FROM pedido WHERE pedido.id = " . $id);
    }

}