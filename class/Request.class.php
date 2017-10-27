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
     * @var array Arrays of items of this request.
     */
    private $items;

    /**
     * @var Licitacao
     */
    private $licitacao;

    /**
     * @var MoneySource
     */
    private $moneySource;

    /**
     * @var SectorGroup
     */
    private $group;

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
        $this->moneySource = NULL;
        $this->group = NULL;
        if ($this->id != NEW_REQUEST_ID) {
            $this->fillFieldsFromDB();
            $this->fillItens();
            $this->initMoneySource();
            $this->initGroup();
        }
    }

    private function initGroup() {
        $query = Query::getInstance()->exe("SELECT id_grupo FROM pedido_grupo WHERE id_pedido = " . $this->id);
        if ($query->num_rows > 0) {
            $obj = $query->fetch_object();

            $this->group = new SectorGroup($obj->id_grupo);
        }
    }

    /**
     * Approve by management an array of requests id.
     *
     * @param array $pedidos Request id to approve.
     */
    public static function aprovaGerencia(array $pedidos) {
        if (empty($pedidos)) {
            return;
        }

        $where = '';
        $len = count($pedidos);
        for ($i = 0; $i < $len; $i++) {
            $where .= 'id = ' . $pedidos[$i];
            if ($i < $len - 1) {
                $where .= ' OR ';
            }
        }

        Query::getInstance()->exe('UPDATE pedido SET aprov_gerencia = 1 WHERE ' . $where);
    }

    private function hasSources() {
        return !empty($this->moneySource);
    }

    private function initMoneySource() {
        $query = Query::getInstance()->exe("SELECT id_fonte FROM pedido_id_fonte WHERE id_pedido = " . $this->id);
        if ($query->num_rows > 0) {
            $obj = $query->fetch_object();

            $this->moneySource = new MoneySource($obj->id_fonte);
        }
    }

    private function setDraft() {
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
        foreach ($this->items as $item) {
            if ($item instanceof ItemRequest) {
                Query::getInstance()->exe("INSERT INTO itens_pedido VALUES(NULL, {$this->id}, {$item->getId()}, {$item->getQtRequested()}, '{$item->getItemValueInRequest()}');");
            }
        }
    }

    private function sendToSOF() {
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
        foreach ($this->items as &$item) {
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

        $this->items = [];
        $i = 0;
        foreach ($id_item as $key => $value) {
            $item = new ItemRequest($value);
            $item->setQtdRequested($qtd_solicitada[$key]);
            $this->items[$i++] = $item;
            $this->value += $item->getItemValueInRequest();
        }

        if ($this->priority == 5) {
            $this->setDraft();
        } else {
            $this->sendToSOF();
        }
    }

    /**
     * Edit a new existing request.
     *
     * @param array $id_item Items that will be used in this request.
     * @param array $qtd_solicitada Requested values for each item in $id_item.
     * @param int $prioridade Request priority
     * @param string $obs Request note.
     * @param int $pedido_contrato If is a contract request - 1, else - 0.
     */
    public function editRequest(array $id_item, array $qtd_solicitada, int $prioridade, string $obs, int $pedido_contrato) {
        $this->priority = $prioridade;
        $this->contract_request = $pedido_contrato;
        $this->obs = Query::getInstance()->real_escape_string($obs);

        $this->value = 0.0;
        $this->items = [];
        $i = 0;
        foreach ($id_item as $key => $value) {
            $item = new ItemRequest($value);
            $item->setQtdRequested($qtd_solicitada[$key]);
            $this->items[$i++] = $item;
            $this->value += $item->getItemValueInRequest();
        }

        if ($this->priority == 5) {
            $this->setDraft();
        } else {
            $this->sendToSOF();
        }
    }

    private function fillFieldsFromDB() {
        $query = Query::getInstance()->exe("SELECT id_setor, id_usuario, data_pedido, ref_mes, alteracao, prioridade, status, round(valor, 3) AS valor, obs, pedido_contrato, aprov_gerencia FROM pedido WHERE id = " . $this->id);

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

        if ($query->num_rows > 0 && count($this->items) == 0) {
            $i = 0;
            while ($obj = $query->fetch_object()) {
                $item = new ItemRequest($obj->id_item);
                $item->setQtdRequested($obj->qtd);
                $this->items[$i++] = $item;
            }
        } else {
            Logger::error("Pedido sem itens, id pedido: " . $this->id);
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
        foreach ($this->items as $item) {
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

            foreach ($this->items as $key => &$item) {
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
        $error = Request::checkForErrors($this->id);
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

        if ($this->hasSources()) {
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
        if ($this->hasSources()) {
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
     * @param Licitacao $licitacao
     */
    public function setLicitacao(Licitacao $licitacao) {
        $this->licitacao = $licitacao;

        $idLic = $this->licitacao->getId();
        $tipo = $this->licitacao->getTipo();
        $numero = $this->licitacao->getNumero();
        $uasg = $this->licitacao->getUasg();
        $procOri = $this->licitacao->getProcessoOriginal();
        $geraContrato = $this->licitacao->getGeraContrato();

        if ($idLic == 0) {
            Query::getInstance()->exe("INSERT INTO licitacao VALUES(NULL, {$this->id}, {$tipo}, '{$numero}', '{$uasg}', '{$procOri}', {$geraContrato});");
        } else {
            Query::getInstance()->exe("UPDATE licitacao SET tipo = {$tipo}, numero = '{$numero}', uasg = '{$uasg}', processo_original = '{$procOri}', gera_contrato = {$geraContrato} WHERE id = {$idLic};");
        }
    }

    public static function checkForErrors(int $pedido): bool {
        $query = Query::getInstance()->exe("SELECT id, round(valor, 3) AS valor FROM pedido WHERE id = " . $pedido);
        $obj = $query->fetch_object();

        $ped = Query::getInstance()->exe("SELECT round(sum(valor), 3) AS soma FROM itens_pedido WHERE id_pedido = " . $pedido);
        $obj_ped = $ped->fetch_object();
        if ($obj->valor != $obj_ped->soma) {
            return true;
        }
        return false;
    }

    /**
     * Update the requests value according its items values.
     * @param array $req Array with requests to be updated.
     */
    public static function updateRequests(array $req) {
        $len = count($req);
        for ($i = 0; $i < $len; $i++) {
            $request = $req[$i];
            $obj = Query::getInstance()->exe("SELECT round(sum(valor), 3) AS sum FROM itens_pedido WHERE id_pedido =  " . $request)->fetch_object();
            Query::getInstance()->exe("UPDATE pedido SET valor = '" . $obj->sum . "' WHERE id = " . $request);
        }
    }

    /**
     * @param MoneySource $moneySource
     */
    public function setMoneySource(MoneySource $moneySource) {
        if ($this->hasSources()) {
            // deleta a fonte atual: garante a edição corretamente
            Query::getInstance()->exe("DELETE FROM pedido_id_fonte WHERE id_pedido = " . $this->id);
        }
        $this->moneySource = $moneySource;

        if ($this->priority != 5) {
            // rascunho, o saldo da fonte não deve ser alterado
            $oldValue = $moneySource->getValue();
            $moneySource->setValue($oldValue - $this->value);
        }

        // associa a fonte ao pedido
        $sql = new SQLBuilder(SQLBuilder::$INSERT);
        $sql->setTables(["pedido_id_fonte"]);
        $sql->setValues([NULL, $this->id, $moneySource->getId()]);

        Query::getInstance()->exe($sql->__toString());
    }

    /**
     * @return SectorGroup
     */
    public function getGroup(): SectorGroup {
        return $this->group;
    }

    /**
     * @param SectorGroup $group
     */
    public function setGroup(SectorGroup $group) {
        if (!empty($this->group)) {
            // update
            Query::getInstance()->exe("UPDATE pedido_grupo SET id_grupo = {$group->getId()} WHERE id_pedido = {$this->id} LIMIT 1;");
        } else {
            // insert
            Query::getInstance()->exe("INSERT INTO pedido_grupo VALUES({$this->id}, {$group->getId()});");
        }
        $this->group = $group;
    }

    /**
     * Sets or update the contract of this request.
     *
     * @param int $type Contract type.
     * @param string $siafi Contract SIAFI.
     */
    public function setContract(int $type, string $siafi) {
        $query = Query::getInstance()->exe('SELECT id_tipo FROM pedido_contrato WHERE id_pedido = ' . $this->id);
        $sql = "INSERT INTO pedido_contrato VALUES({$this->id}, {$type}, '{$siafi}');";
        if ($query->num_rows > 0) {
            $sql = "UPDATE pedido_contrato SET id_tipo = $type, siafi = '{$siafi}' WHERE id_pedido = " . $this->id;
        }
        Query::getInstance()->exe($sql);
    }


    /**
     * @return int Request id
     */
    public function getId(): int {
        return $this->id;
    }

    /**
     * @return float
     */
    public function getValue(): float {
        return $this->value;
    }

    /**
     * @return array
     */
    public function getItems(): array {
        return $this->items;
    }

}