<?php
/**
 * Base class for anything type of Item.
 *
 * @author João Bolsson (joaovictorbolsson@gmail.com).
 * @since 2017, 12 Oct.
 */

class Item implements JsonSerializable {

    /**
     * @var int Item id.
     */
    protected $id;
    /**
     * @var int Id item processo
     */
    protected $id_item_processo;

    /**
     * @var int Id item contrato
     */
    protected $id_item_contrato;

    /**
     * @var string Cod despesa
     */
    protected $cod_despesa;

    /**
     * @var string Descrição despesa
     */
    protected $descr_despesa;

    /**
     * @var string Descrição tipo doc
     */
    protected $descr_tipo_doc;

    /**
     * @var string Número do contrato
     */
    protected $num_contrato;

    /**
     * @var string Número do Processo
     */
    protected $num_processo;

    /**
     * @var string Descrição mod compra
     */
    protected $descr_mod_compra;

    /**
     * @var string Número da Licitação
     */
    protected $num_licitacao;

    /**
     * @var string Data início
     */
    protected $dt_inicio;

    /**
     * @var string Data fim
     */
    protected $dt_fim;

    /**
     * @var string Data geração
     */
    protected $dt_geracao;

    /**
     * @var string CGC Fornecedor
     */
    protected $cgc_fornecedor;

    /**
     * @var string Nome do fornecedor
     */
    protected $nome_fornecedor;

    /**
     * @var string Número extrato
     */
    protected $num_extrato;

    /**
     * @var string Código estruturado
     */
    protected $cod_estruturado;

    /**
     * @var string Nome da Unidade
     */
    protected $nome_unidade;

    /**
     * @var string Código reduzido
     */
    protected $cod_reduzido;

    /**
     * @var string Complemento item
     */
    protected $complemento_item;

    /**
     * @var string Descrição
     */
    protected $descricao;

    /**
     * @var int Id extrato contrato
     */
    protected $id_extrato_contr;

    /**
     * @var float Valor unitário
     */
    protected $vl_unitario;

    /**
     * @var int Quantidade Contrato
     */
    protected $qt_contrato;

    /**
     * @var float Valor contrato
     */
    protected $vl_contrato;

    /**
     * @var int Quantidade Utilizada
     */
    protected $qt_utilizado;

    /**
     * @var float Valor utilizado
     */
    protected $vl_utilizado;

    /**
     * @var int Quantidade Saldo
     */
    protected $qt_saldo;

    /**
     * @var float Valor saldo
     */
    protected $vl_saldo;

    /**
     * @var int Id da unidade
     */
    protected $id_unidade;

    /**
     * @var int Ano Orçamento
     */
    protected $ano_orcamento;

    /**
     * @var bool Flag that indicates if this item was cancel.
     */
    protected $cancelado;

    /**
     * @var string Chave
     */
    protected $chave;

    /**
     * @var string Seq item processo.
     */
    protected $seq_item_processo;

    /**
     * @var int Default id to define a new item.
     */
    public static $NEW_ITEM = 0;

    /**
     * ==========================
     * Custom Attributes
     * ==========================
     */

    protected $min_qt_contrato = 0;

    protected $min_qt_utilizado = 0;

    /**
     * Item constructor.
     * @param int $id Item id from db or self::NEW_ITEM if is a new item.
     */
    public function __construct(int $id) {
        $this->id = $id;
        if ($this->id != self::$NEW_ITEM) {
            $this->setMinValues();
            $this->initItem();
        }
    }

    private function setMinValues() {
        $query = Query::getInstance()->exe("SELECT sum(itens_pedido.qtd) AS soma FROM itens_pedido, pedido WHERE itens_pedido.id_item = " . $this->id . " AND itens_pedido.id_pedido = pedido.id AND pedido.status != 1 AND pedido.status != 3;");

        if ($query->num_rows > 0) {
            $obj = $query->fetch_object();

            $this->min_qt_contrato = $obj->soma;
            $this->min_qt_utilizado = $obj->soma;
        }
    }

    /**
     * Method called when this object is encoded with json_encode.
     *
     * @return array
     */
    public function jsonSerialize() {
        return get_object_vars($this);
    }

    private function canUpdate(): bool {
        $query_qtd = Query::getInstance()->exe("SELECT sum(itens_pedido.qtd) AS soma FROM itens_pedido, pedido WHERE itens_pedido.id_item = " . $this->id . " AND itens_pedido.id_pedido = pedido.id AND pedido.status != 1 AND pedido.status != 3");
        if ($query_qtd->num_rows > 0) {
            $obj_qtd = $query_qtd->fetch_object();
            $sum = $obj_qtd->soma;
            if ($this->qt_contrato < $sum || $this->qt_utilizado < $sum) {
                return false;
            }
        }
        return true;
    }

    /**
     * Update a item in DB or insert a new one.
     *
     * @return bool Is success - true, else - false.
     */
    public function update(): bool {
        $dt_inicio = Util::dateFormat($this->dt_inicio);
        $dt_fim = Util::dateFormat($this->dt_fim);
        $dt_geracao = Util::dateFormat($this->dt_geracao);

        if ($this->id == self::$NEW_ITEM) {
            // insert
            $builder = new SQLBuilder(SQLBuilder::$INSERT);
            $builder->setTables(['itens']);
            $builder->setColumns(['id', 'id_item_processo', 'id_item_contrato', 'cod_despesa', 'descr_despesa', 'descr_tipo_doc', 'num_contrato', 'num_processo', 'descr_mod_compra', 'num_licitacao', 'dt_inicio', 'dt_fim', 'dt_geracao', 'cgc_fornecedor', 'nome_fornecedor', 'num_extrato', 'cod_estruturado', 'nome_unidade', 'cod_reduzido', 'complemento_item', 'descricao', 'id_extrato_contr', 'vl_unitario', 'qt_contrato', 'vl_contrato', 'qt_utilizado', 'vl_utilizado', 'qt_saldo', 'vl_saldo', 'id_unidade', 'ano_orcamento', 'cancelado', 'chave', 'seq_item_processo']);
            $builder->setValues([NULL, $this->id_item_processo, $this->id_item_contrato, $this->cod_despesa, $this->descr_despesa, $this->descr_tipo_doc, $this->num_contrato, $this->num_processo, $this->descr_mod_compra, $this->num_licitacao, $dt_inicio, $dt_fim, $dt_geracao, $this->cgc_fornecedor, $this->nome_fornecedor, $this->num_extrato, $this->cod_estruturado, $this->nome_unidade, $this->cod_reduzido, $this->complemento_item, $this->descricao, $this->id_extrato_contr, $this->vl_unitario, $this->qt_contrato, $this->vl_contrato, $this->qt_utilizado, $this->vl_utilizado, $this->qt_saldo, $this->vl_saldo, $this->id_unidade, $this->ano_orcamento, $this->cancelado, $this->chave, $this->seq_item_processo]);

            Query::getInstance()->exe($builder->__toString());
            $this->id = Query::getInstance()->getInsertId();
        } else if (!$this->canUpdate()) {
            return false;
        } else {
            // update
            $builder = new SQLBuilder(SQLBuilder::$UPDATE);
            $builder->setTables(['itens']);
            $builder->setColumns(['id_item_processo', 'id_item_contrato', 'cod_despesa', 'descr_despesa', 'descr_tipo_doc', 'num_contrato', 'num_processo', 'descr_mod_compra', 'num_licitacao', 'dt_inicio', 'dt_fim', 'dt_geracao', 'cgc_fornecedor', 'nome_fornecedor', 'num_extrato', 'cod_estruturado', 'nome_unidade', 'cod_reduzido', 'complemento_item', 'descricao', 'id_extrato_contr', 'vl_unitario', 'qt_contrato', 'vl_contrato', 'qt_utilizado', 'vl_utilizado', 'qt_saldo', 'vl_saldo', 'id_unidade', 'ano_orcamento', 'cancelado', 'seq_item_processo']);
            $builder->setValues([$this->id_item_processo, $this->id_item_contrato, $this->cod_despesa, $this->descr_despesa, $this->descr_tipo_doc, $this->num_contrato, $this->num_processo, $this->descr_mod_compra, $this->num_licitacao, $dt_inicio, $dt_fim, $dt_geracao, $this->cgc_fornecedor, $this->nome_fornecedor, $this->num_extrato, $this->cod_estruturado, $this->nome_unidade, $this->cod_reduzido, $this->complemento_item, $this->descricao, $this->id_extrato_contr, $this->vl_unitario, $this->qt_contrato, $this->vl_contrato, $this->qt_utilizado, $this->vl_utilizado, $this->qt_saldo, $this->vl_saldo, $this->id_unidade, $this->ano_orcamento, $this->cancelado, $this->seq_item_processo]);
            $builder->setWhere("id = " . $this->id);

            Query::getInstance()->exe($builder->__toString());
        }
        return true;
    }

    protected function initItem() {
        $query = Query::getInstance()->exe("SELECT id_item_processo, id_item_contrato, cod_despesa, descr_despesa, descr_tipo_doc, num_contrato, num_processo, descr_mod_compra, num_licitacao, DATE_FORMAT(dt_inicio, '%d/%m/%Y') AS dt_inicio, DATE_FORMAT(dt_fim, '%d/%m/%Y') AS dt_fim, DATE_FORMAT(dt_geracao, '%d/%m/%Y') AS dt_geracao, cgc_fornecedor, nome_fornecedor, num_extrato, cod_estruturado, nome_unidade, cod_reduzido, complemento_item, descricao, id_extrato_contr, round(replace(vl_unitario, ',', '.'), 4) AS vl_unitario, qt_contrato, round(replace(vl_contrato, ',', '.'), 4) AS vl_contrato, qt_utilizado, round(replace(vl_utilizado, ',', '.'), 4) AS vl_utilizado, qt_saldo, round(replace(vl_saldo, ',', '.'), 4) AS vl_saldo, id_unidade, ano_orcamento, chave, seq_item_processo, cancelado FROM itens WHERE id = " . $this->id);

        if ($query->num_rows > 0) {
            $obj = $query->fetch_object();

            $this->id_item_processo = $obj->id_item_processo;
            $this->id_item_contrato = $obj->id_item_contrato;
            $this->cod_despesa = $obj->cod_despesa;
            $this->descr_despesa = $obj->descr_despesa;
            $this->descr_tipo_doc = $obj->descr_tipo_doc;
            $this->num_contrato = $obj->num_contrato;
            $this->num_processo = $obj->num_processo;
            $this->descr_mod_compra = $obj->descr_mod_compra;
            $this->num_licitacao = $obj->num_licitacao;
            $this->dt_inicio = $obj->dt_inicio;
            $this->dt_fim = $obj->dt_fim;
            $this->dt_geracao = $obj->dt_geracao;
            $this->cgc_fornecedor = $obj->cgc_fornecedor;
            $this->nome_fornecedor = $obj->nome_fornecedor;
            $this->num_extrato = $obj->num_extrato;
            $this->cod_estruturado = $obj->cod_estruturado;
            $this->nome_unidade = $obj->nome_unidade;
            $this->cod_reduzido = $obj->cod_reduzido;
            $this->complemento_item = $obj->complemento_item;
            $this->descricao = $obj->descricao;
            $this->id_extrato_contr = $obj->id_extrato_contr;

            $this->vl_unitario = $obj->vl_unitario;
            $this->qt_contrato = $obj->qt_contrato;
            $this->vl_contrato = $obj->vl_contrato;
            $this->qt_utilizado = $obj->qt_utilizado;
            $this->vl_utilizado = $obj->vl_utilizado;
            $this->qt_saldo = $obj->qt_saldo;
            $this->vl_saldo = $obj->vl_saldo;

            $this->id_unidade = $obj->id_unidade;
            $this->ano_orcamento = $obj->ano_orcamento;
            $this->chave = $obj->chave;
            $this->seq_item_processo = $obj->seq_item_processo;
            $this->cancelado = $obj->cancelado;
        } else {
            Logger::info("[ERROR] init item error!");
        }

    }

    /**
     * @param int $qt_saldo
     */
    public function setQtSaldo(int $qt_saldo = null) {
        if (!is_null($qt_saldo)) {
            $this->qt_saldo = $qt_saldo;
        }
    }

    /**
     * @param int $qt_utilizado
     */
    public function setQtUtilizado(int $qt_utilizado = null) {
        if (!is_null($qt_utilizado)) {
            $this->qt_utilizado = $qt_utilizado;
        }
    }

    /**
     * @param float $vl_saldo
     */
    public function setVlSaldo(float $vl_saldo = null) {
        if (!is_null($vl_saldo)) {
            $this->vl_saldo = $vl_saldo;
        }
    }

    /**
     * @param float $vl_utilizado
     */
    public function setVlUtilizado(float $vl_utilizado = null) {
        if (!is_null($vl_utilizado)) {
            $this->vl_utilizado = $vl_utilizado;
        }
    }

    /**
     * @return int
     */
    public function getQtSaldo(): int {
        return $this->qt_saldo;
    }

    /**
     * @return int
     */
    public function getQtUtilizado(): int {
        return $this->qt_utilizado;
    }

    /**
     * @return int
     */
    public function getId(): int {
        return $this->id;
    }

    /**
     * @return float
     */
    public function getVlSaldo(): float {
        return $this->vl_saldo;
    }

    /**
     * @return float
     */
    public function getVlUtilizado(): float {
        return $this->vl_utilizado;
    }

    /**
     * @return float
     */
    public function getVlContrato(): float {
        return $this->vl_contrato;
    }

    /**
     * @return bool
     */
    public function isCancelado(): bool {
        return $this->cancelado;
    }

    /**
     * @param int $id_item_processo
     */
    public function setIdItemProcesso(int $id_item_processo = null) {
        if (!is_null($id_item_processo)) {
            $this->id_item_processo = $id_item_processo;
        }
    }

    /**
     * @param int $id_item_contrato
     */
    public function setIdItemContrato(int $id_item_contrato = null) {
        if (!is_null($id_item_contrato)) {
            $this->id_item_contrato = $id_item_contrato;
        }
    }

    /**
     * @param string $cod_despesa
     */
    public function setCodDespesa(string $cod_despesa = null) {
        if (!is_null($cod_despesa)) {
            $this->cod_despesa = $cod_despesa;
        }
    }

    /**
     * @param string $descr_despesa
     */
    public function setDescrDespesa(string $descr_despesa = null) {
        if (!is_null($descr_despesa)) {
            $this->descr_despesa = $descr_despesa;
        }
    }

    /**
     * @param string $descr_tipo_doc
     */
    public function setDescrTipoDoc(string $descr_tipo_doc = null) {
        if (!is_null($descr_tipo_doc)) {
            $this->descr_tipo_doc = $descr_tipo_doc;
        }
    }

    /**
     * @param string $num_contrato
     */
    public function setNumContrato(string $num_contrato = null) {
        if (!is_null($num_contrato)) {
            $this->num_contrato = $num_contrato;
        }
    }

    /**
     * @param string $num_processo
     */
    public function setNumProcesso(string $num_processo = null) {
        if (!is_null($num_processo)) {
            $this->num_processo = $num_processo;
        }
    }

    /**
     * @param string $descr_mod_compra
     */
    public function setDescrModCompra(string $descr_mod_compra = null) {
        if (!is_null($descr_mod_compra)) {
            $this->descr_mod_compra = $descr_mod_compra;
        }
    }

    /**
     * @param string $num_licitacao
     */
    public function setNumLicitacao(string $num_licitacao = null) {
        if (!is_null($num_licitacao)) {
            $this->num_licitacao = $num_licitacao;
        }
    }

    /**
     * @param string $dt_inicio (Kepp format dd/mm/YYYY)
     */
    public function setDtInicio(string $dt_inicio = null) {
        if (!is_null($dt_inicio)) {
            $this->dt_inicio = $dt_inicio;
        }
    }

    /**
     * @param string $dt_fim (Kepp format dd/mm/YYYY)
     */
    public function setDtFim(string $dt_fim = null) {
        if (!is_null($dt_fim)) {
            $this->dt_fim = $dt_fim;
        }
    }

    /**
     * @param string $dt_geracao (Kepp format dd/mm/YYYY)
     */
    public function setDtGeracao(string $dt_geracao = null) {
        if (!is_null($dt_geracao)) {
            $this->dt_geracao = $dt_geracao;
        }
    }

    /**
     * @param string $cgc_fornecedor
     */
    public function setCgcFornecedor(string $cgc_fornecedor = null) {
        if (!is_null($cgc_fornecedor)) {
            $this->cgc_fornecedor = $cgc_fornecedor;
        }
    }

    /**
     * @param string $nome_fornecedor
     */
    public function setNomeFornecedor(string $nome_fornecedor = null) {
        if (!is_null($nome_fornecedor)) {
            $this->nome_fornecedor = $nome_fornecedor;
        }
    }

    /**
     * @param string $num_extrato
     */
    public function setNumExtrato(string $num_extrato = null) {
        if (!is_null($num_extrato)) {
            $this->num_extrato = $num_extrato;
        }
    }

    /**
     * @param string $cod_estruturado
     */
    public function setCodEstruturado(string $cod_estruturado = null) {
        if (!is_null($cod_estruturado)) {
            $this->cod_estruturado = $cod_estruturado;
        }
    }

    /**
     * @param string $nome_unidade
     */
    public function setNomeUnidade(string $nome_unidade = null) {
        if (!is_null($nome_unidade)) {
            $this->nome_unidade = $nome_unidade;
        }
    }

    /**
     * @param string $cod_reduzido
     */
    public function setCodReduzido(string $cod_reduzido = null) {
        if (!is_null($cod_reduzido)) {
            $this->cod_reduzido = $cod_reduzido;
        }
    }

    /**
     * @param string $complemento_item
     */
    public function setComplementoItem(string $complemento_item = null) {
        if (!is_null($complemento_item)) {
            $this->complemento_item = Query::getInstance()->real_escape_string($complemento_item);
        }
    }

    /**
     * @param string $descricao
     */
    public function setDescricao(string $descricao = null) {
        if (!is_null($descricao)) {
            $this->descricao = $descricao;
        }
    }

    /**
     * @param int $id_extrato_contr
     */
    public function setIdExtratoContr(int $id_extrato_contr = null) {
        if (!is_null($id_extrato_contr)) {
            $this->id_extrato_contr = $id_extrato_contr;
        }
    }

    /**
     * @param float $vl_unitario
     */
    public function setVlUnitario(float $vl_unitario = null) {
        if (!is_null($vl_unitario)) {
            $this->vl_unitario = $vl_unitario;
            $this->vl_saldo = $this->qt_saldo * $this->vl_unitario;
            $this->vl_contrato = $this->qt_contrato * $this->vl_unitario;
            $this->vl_utilizado = $this->vl_utilizado * $this->vl_unitario;

            // seleciona infos dos pedidos que contém o item editado e que não passaram da análise
            $query = Query::getInstance()->exe("SELECT itens_pedido.id_pedido, itens_pedido.qtd, itens_pedido.valor AS valor_item, pedido.id_setor, pedido.valor AS valor_pedido, saldo_setor.saldo FROM itens_pedido, pedido, saldo_setor WHERE saldo_setor.id_setor = pedido.id_setor AND itens_pedido.id_item = {$this->id} AND itens_pedido.id_pedido = pedido.id AND pedido.status <= 2;");

            $pedidos = [];
            $i = 0;
            while ($obj = $query->fetch_object()) {
                $valorItem = $obj->qtd * $this->vl_unitario;
                Query::getInstance()->exe("UPDATE itens_pedido SET itens_pedido.valor = '{$valorItem}' WHERE itens_pedido.id_item = {$this->id} AND itens_pedido.id_pedido = " . $obj->id_pedido);
                $saldo_setor = $obj->saldo + $obj->valor_item;
                $saldo_setor -= $valorItem;
                $saldo_setor = number_format($saldo_setor, 3, '.', '');
                // alterando o saldo do setor
                Query::getInstance()->exe("UPDATE saldo_setor SET saldo_setor.saldo = '{$saldo_setor}' WHERE saldo_setor.id_setor = " . $obj->id_setor);

                $pedidos[$i++] = $obj->id_pedido;
            }

            $len = count($pedidos);
            for ($i = 0; $i < $len; $i++) {
                $error = Request::checkForErrors($pedidos[$i]);
                if ($error) {
                    Logger::error("Pedido quebrado em editItem: " . $pedidos[$i]);
                }
            }
            Request::updateRequests($pedidos);
        }
    }

    /**
     * @param int $qt_contrato
     */
    public function setQtContrato(int $qt_contrato = null) {
        if (!is_null($qt_contrato)) {
            $this->qt_contrato = $qt_contrato;
        }
    }

    /**
     * @param float $vl_contrato
     */
    public function setVlContrato(float $vl_contrato = null) {
        if (!is_null($vl_contrato)) {
            $this->vl_contrato = $vl_contrato;
        }
    }

    /**
     * @param int $id_unidade
     */
    public function setIdUnidade(int $id_unidade = null) {
        if (!is_null($id_unidade)) {
            $this->id_unidade = $id_unidade;
        }
    }

    /**
     * @param int $ano_orcamento
     */
    public function setAnoOrcamento(int $ano_orcamento = null) {
        if (!is_null($ano_orcamento)) {
            $this->ano_orcamento = $ano_orcamento;
        }
    }

    /**
     * @param bool $cancelado
     */
    public function setCancelado(bool $cancelado = false) {
        $this->cancelado = $cancelado;
    }

    /**
     * @param string $chave
     */
    public function setChave(string $chave = null) {
        if (!is_null($chave)) {
            $this->chave = $chave;
        }
    }

    /**
     * @param string $seq_item_processo
     */
    public function setSeqItemProcesso(string $seq_item_processo = null) {
        if (!is_null($seq_item_processo)) {
            $this->seq_item_processo = $seq_item_processo;
        }
    }

    /**
     * @return int
     */
    public function getIdItemProcesso(): int {
        return $this->id_item_processo;
    }

    /**
     * @return int
     */
    public function getIdItemContrato(): int {
        return $this->id_item_contrato;
    }

    /**
     * @return string
     */
    public function getCodDespesa(): string {
        return $this->cod_despesa;
    }

    /**
     * @return string
     */
    public function getDescrDespesa(): string {
        return $this->descr_despesa;
    }

    /**
     * @return string
     */
    public function getDescrTipoDoc(): string {
        return $this->descr_tipo_doc;
    }

    /**
     * @return string
     */
    public function getNumContrato(): string {
        return $this->num_contrato;
    }

    /**
     * @return string
     */
    public function getNumProcesso(): string {
        return $this->num_processo;
    }

    /**
     * @return string
     */
    public function getDescrModCompra(): string {
        return $this->descr_mod_compra;
    }

    /**
     * @return string
     */
    public function getNumLicitacao(): string {
        return $this->num_licitacao;
    }

    /**
     * @return string
     */
    public function getDtInicio(): string {
        return $this->dt_inicio;
    }

    /**
     * @return string
     */
    public function getDtFim(): string {
        return $this->dt_fim;
    }

    /**
     * @return string
     */
    public function getDtGeracao(): string {
        return $this->dt_geracao;
    }

    /**
     * @return string
     */
    public function getCgcFornecedor(): string {
        return $this->cgc_fornecedor;
    }

    /**
     * @return string
     */
    public function getNomeFornecedor(): string {
        return $this->nome_fornecedor;
    }

    /**
     * @return string
     */
    public function getNumExtrato(): string {
        return $this->num_extrato;
    }

    /**
     * @return string
     */
    public function getCodEstruturado(): string {
        return $this->cod_estruturado;
    }

    /**
     * @return string
     */
    public function getNomeUnidade(): string {
        return $this->nome_unidade;
    }

    /**
     * @return string
     */
    public function getCodReduzido(): string {
        return $this->cod_reduzido;
    }

    /**
     * @return string
     */
    public function getComplementoItem(): string {
        return $this->complemento_item;
    }

    /**
     * @return string
     */
    public function getDescricao(): string {
        return $this->descricao;
    }

    /**
     * @return int
     */
    public function getIdExtratoContr(): int {
        return $this->id_extrato_contr;
    }

    /**
     * @return float
     */
    public function getVlUnitario(): float {
        return $this->vl_unitario;
    }

    /**
     * @return int
     */
    public function getQtContrato(): int {
        return $this->qt_contrato;
    }

    /**
     * @return int
     */
    public function getIdUnidade(): int {
        return $this->id_unidade;
    }

    /**
     * @return int
     */
    public function getAnoOrcamento(): int {
        return $this->ano_orcamento;
    }

    /**
     * @return string
     */
    public function getChave(): string {
        return $this->chave;
    }

    /**
     * @return string
     */
    public function getSeqItemProcesso(): string {
        return $this->seq_item_processo;
    }


}