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
     * @var int
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

    private $today;
    private $mes;

    /**
     * Request value
     * @var float
     */
    private $value;

    /**
     * Observation (optional).
     * @var string
     */
    private $obs;

    /**
     * Flag that indicates if this request is a contract request.
     * @var int
     */
    private $contract_request;

    /**
     * Flag that indicates if this request was approved by management.
     * @var int
     */
    private $approv_manager;

    /**
     * @var array Arrays of itens of this request.
     */
    private $itens;

    /**
     * @var bool Flag that indicates if this request has sources.
     */
    private $has_sources;

    /**
     * Default construct.
     * @param int $id Id request (0 for a no existing request in db).
     */
    public function __construct(int $id) {
        $this->today = date('Y-m-d');
        $this->mes = date("n");
        $this->id = $id;
        $this->value = 0;
        $this->approv_manager = 0;
        if ($this->id != NEW_REQUEST_ID) {
            $this->fillFieldsFromDB();
            $this->fillItens();
            $this->has_sources = Geral::existsSources($this->id);
        }
    }

    /**
     * Insert a new request in DB.
     *
     * @param int $id_user User that made this request.
     * @param int $id_setor Sector that made this request.
     * @param array $id_item Items that will be used in this request.
     * @param array $qtd_solicitada Requested values for each item in $id_item.
     * @param int $prioridade Request priority
     * @param string $obs Request note.
     * @param int $pedido_contrato If is a contract request - 1, else - 0.
     */
    public function insertNewRequest(int $id_user, int $id_setor, array $id_item, array $qtd_solicitada, int $prioridade, string $obs, int $pedido_contrato) {

        $this->priority = $prioridade;
        $this->id_sector = $id_setor;
        $this->id_user = $id_user;
        $this->contract_request = $pedido_contrato;
        $this->obs = Query::getInstance()->real_escape_string($obs);

        $this->itens = [];
        $i = 0;
        foreach ($id_item as $key => $value) {
            $item = new ItemRequest($value);
            $item->setQtdRequested($qtd_solicitada[$key]);
            $this->itens[$i++] = $item;
            $this->value += $item->getItemValueInRequest();
        }

        if ($this->priority == 5) {
            if ($this->id == NEW_REQUEST_ID) {
                $this->change = 1;
                $this->status = 1;
                //inserindo os dados iniciais do pedido
                Query::getInstance()->exe("INSERT INTO pedido VALUES(NULL, {$this->id_sector}, {$this->id_user}, '{$this->today}', '{$this->mes}', {$this->change}, {$this->priority}, {$this->status}, '{$this->value}', '{$this->obs}', {$this->contract_request}, {$this->approv_manager});");
                $this->id = Query::getInstance()->getInsertId();
            } else {
                //remover resgistros antigos do rascunho
                Query::getInstance()->exe("DELETE FROM itens_pedido WHERE id_pedido = " . $this->id);
                Query::getInstance()->exe("UPDATE pedido SET data_pedido = '{$this->today}', ref_mes = {$this->mes}, prioridade = {$this->priority}, valor = '{$this->value}', obs = '{$this->obs}', pedido_contrato = {$this->contract_request}, aprov_gerencia = {$this->approv_manager} WHERE id = " . $this->id);
            }

            //inserindo os itens do pedido
            foreach ($this->itens as $item) {
                if ($item instanceof ItemRequest) {
                    Query::getInstance()->exe("INSERT INTO itens_pedido VALUES(NULL, {$this->id}, {$item->getId()}, {$item->getQtRequested()}, '{$item->getItemValueInRequest()}');");
                }
            }
        } else {
            $this->updateSectorMoney();
            $this->status = 2;
            $this->change = 0;
            // enviado ao sof
            if ($this->id == NEW_REQUEST_ID) {
                //inserindo os dados iniciais do pedido
                Query::getInstance()->exe("INSERT INTO pedido VALUES(NULL, {$this->id_sector}, {$this->id_user}, '{$this->today}', '{$this->mes}', {$this->change}, {$this->priority}, {$this->status}, '{$this->value}', '{$this->obs}', {$this->contract_request}, {$this->approv_manager});");
                $this->id = Query::getInstance()->getInsertId();
            } else {
                // atualizando pedido
                Query::getInstance()->exe("UPDATE pedido SET data_pedido = '{$this->today}', ref_mes = {$this->mes}, alteracao = {$this->change}, prioridade = {$this->priority}, status = {$this->status}, valor = '{$this->value}', obs = '{$this->obs}', pedido_contrato = {$this->contract_request}, aprov_gerencia = {$this->approv_manager} WHERE id = " . $this->id);
            }
            //remover resgistros antigos do pedido
            Query::getInstance()->exe("DELETE FROM itens_pedido WHERE id_pedido = " . $this->id);

            // alterando infos dos itens solicitados
            foreach ($this->itens as &$item) {
                if ($item instanceof ItemRequest) {
                    $qt_requested = $item->getQtRequested();
                    $vl_in_request = $item->getItemValueInRequest();
                    Query::getInstance()->exe("INSERT INTO itens_pedido VALUES(NULL, {$this->id}, {$item->getId()}, {$qt_requested}, '{$vl_in_request}');");

                    $oldQtSaldo = $item->getQtSaldo();
                    $item->setQtSaldo($oldQtSaldo - $qt_requested);

                    $oldQtUtilizado = $item->getQtUtilizado();
                    $item->setQtUtilizado($oldQtUtilizado + $qt_requested);

                    $oldVlSaldo = $item->getVlSaldo();
                    if ($oldVlSaldo == 0) {
                        $oldVlSaldo = $item->getVlContrato();
                    }

                    $item->setVlSaldo($oldVlSaldo - $vl_in_request);

                    $oldVlUtilizado = $item->getVlUtilizado();
                    $item->setVlUtilizado($oldVlUtilizado + $vl_in_request);
                }
            }

            $this->updateItens();
        }
        $error = Geral::checkForErrors($this->id);
        if ($error) {
            Logger::error("Pedido quebrado em insertPedido: " . $this->id);
        }
        Geral::updateRequests([$this->id]);
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

    private function fillItens() {
        $query = Query::getInstance()->exe("SELECT id_item, qtd, valor FROM itens_pedido WHERE id_pedido = " . $this->id);

        if ($query->num_rows > 0 && count($this->itens) == 0) {
            $i = 0;
            while ($obj = $query->fetch_object()) {
                $item = new ItemRequest($obj->id_item);
                $item->setQtdRequested($obj->qtd);
                $this->itens[$i++] = $item;
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

    private function updateItens() {
        $toDelete = [];
        $i = 0;
        foreach ($this->itens as $item) {
            if ($item instanceof ItemRequest) {
                $cancelado = '';
                if ($item->isCancelado()) {
                    $cancelado = ', cancelado = 1';
                    $this->value -= $item->getItemValueInRequest();
                    $toDelete[$i++] = $item->getId();
                }
                Query::getInstance()->exe("UPDATE itens SET qt_saldo = '{$item->getQtSaldo()}', qt_utilizado = '{$item->getQtUtilizado()}', vl_saldo = '{$item->getVlSaldo()}', vl_utilizado = '{$item->getVlUtilizado()}'{$cancelado} WHERE id = " . $item->getId());
            }
        }

        if (!empty($toDelete)) {
            foreach ($toDelete as $id) {
                Query::getInstance()->exe('DELETE FROM itens_pedido WHERE id_pedido = ' . $this->id . ' AND id_item = ' . $id);
            }

            Query::getInstance()->exe("UPDATE pedido SET valor = '" . $this->value . "' WHERE id = " . $this->id);
        }
    }

    /**
     * @param int $fase New status.
     * @param array $item_cancelado Possible canceled items.
     */
    public function manage(int $fase, array $item_cancelado) {
        $this->status = $fase;

        // verificando itens cancelados, somente quando passam pela análise
        if (in_array(1, $item_cancelado) || in_array(true, $item_cancelado) || $this->status == 3) {
            // só percorre os itens se tiver algum item cancelado ou se o pedido for reprovado

            foreach ($this->itens as $key => &$item) {
                if ($item instanceof ItemRequest) {
                    $qtRequested = $item->getQtRequested();
                    $vlItemInRequest = $item->getItemValueInRequest();

                    $oldQtSaldo = $item->getQtSaldo();
                    $item->setQtSaldo($oldQtSaldo + $qtRequested);

                    $oldQtUtilizado = $item->getQtUtilizado();
                    $item->setQtUtilizado($oldQtUtilizado - $qtRequested);

                    $oldVlSaldo = $item->getVlSaldo();
                    $item->setVlSaldo($oldVlSaldo + $vlItemInRequest);

                    $oldVlUtilizado = $item->getVlUtilizado();
                    $item->setVlUtilizado($oldVlUtilizado - $vlItemInRequest);

                    $item->setCancelado($item_cancelado[$key]);
                }
            }
            $this->updateItens();
        }

        if ($this->status == 3) {
            $this->reprove();
        } else if ($this->status == 4) {
            $this->approve();
        }

        $this->update();
        $error = Geral::checkForErrors($this->id);
        if ($error) {
            Logger::error("Pedido quebrado em pedidoAnalisado: " . $this->id);
        }
    }

    private function updateSectorMoney() {
        $sector = new Sector($this->id_sector);

        $sector->updateMoney();
    }

    private function update() {
        $formattedValue = number_format($this->value, 3, '.', '');
        Query::getInstance()->exe("UPDATE pedido SET alteracao = " . $this->change . ", prioridade = " . $this->priority . ", status = " . $this->status . ", valor = '" . $formattedValue . "' WHERE id = " . $this->id);
    }

    private function reprove() {
        $this->change = 1;
        $this->priority = 5;

        if ($this->has_sources) {
            // devolve o valor do pedido para a fonte
            $query_fonte = Query::getInstance()->exe("SELECT saldo_fonte.id AS id_fonte, saldo_fonte.valor AS saldo_fonte FROM saldo_fonte, pedido_id_fonte WHERE pedido_id_fonte.id_fonte = saldo_fonte.id AND pedido_id_fonte.id_pedido = " . $this->id);
            if ($query_fonte->num_rows > 0) {
                $obj = $query_fonte->fetch_object();
                $new_vl = $obj->saldo_fonte + $this->value;
                $new_vl = number_format($new_vl, 3, '.', '');
                Query::getInstance()->exe("UPDATE saldo_fonte SET valor = '" . $new_vl . "' WHERE id_setor = " . $this->id_sector . " AND id = " . $obj->id_fonte);
                $this->updateSectorMoney();
            }
        } else {
            // devolve o valor do pedido ao sof
            $sof = new Sector(2);
            $oldMoney = $sof->getMoney();
            $sof->setMoney($oldMoney + $this->value);

            // coloca na tabela de lançamentos

            $sqlBuilder = new SQLBuilder(SQLBuilder::$INSERT);
            $sqlBuilder->setTables(['saldos_lancamentos']);
            $sqlBuilder->setValues([null, $this->id_sector, $this->today, -$this->value, 5]);
            Query::getInstance()->exe($sqlBuilder->__toString());

            $sqlBuilder->setValues([null, $sof->getId(), $this->today, $this->value, 5]);
            Query::getInstance()->exe($sqlBuilder->__toString());
        }
    }

    private function approve() {
        Query::getInstance()->exe("INSERT INTO saldos_lancamentos VALUES(NULL, {$this->id_sector}, '{$this->today}', '-{$this->value}', 4);");
        // próxima fase
        $this->status++;
        // não precisa cadastrar fontes, elas já estão cadastradas
        if ($this->has_sources) {
            $this->status++;
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
     * @return int Request id
     */
    public function getId(): int {
        return $this->id;
    }

}