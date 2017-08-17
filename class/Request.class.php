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