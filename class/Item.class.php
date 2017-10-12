<?php
/**
 *
 *
 * @author João Bolsson (joaovictorbolsson@gmail.com).
 * @since 2017, 12 Oct.
 */

class Item {

    /**
     * @var int Item id.
     */
    private $id;
    /**
     * @var int Id item processo
     */
    private $id_item_processo;

    /**
     * @var int Id item contrato
     */
    private $id_item_contrato;

    /**
     * @var string Cod despesa
     */
    private $cod_despesa;

    /**
     * @var string Descrição despesa
     */
    private $descr_despesa;

    /**
     * @var string Descrição tipo doc
     */
    private $descr_tipo_doc;

    /**
     * @var string Número do contrato
     */
    private $num_contrato;

    /**
     * @var string Número do Processo
     */
    private $num_processo;

    /**
     * @var string Descrição mod compra
     */
    private $descr_mod_compra;

    /**
     * @var string Número da Licitação
     */
    private $num_licitacao;

    /**
     * @var string Data início
     */
    private $dt_inicio;

    /**
     * @var string Data fim
     */
    private $dt_fim;

    /**
     * @var string Data geração
     */
    private $dt_geracao;

    /**
     * @var string CGC Fornecedor
     */
    private $cgc_fornecedor;

    /**
     * @var string Nome do fornecedor
     */
    private $nome_fornecedor;

    /**
     * @var string Número extrato
     */
    private $num_extrato;

    /**
     * @var string Código estruturado
     */
    private $cod_estruturado;

    /**
     * @var string Nome da Unidade
     */
    private $nome_unidade;

    /**
     * @var string Código reduzido
     */
    private $cod_reduzido;

    /**
     * @var string Complemento item
     */
    private $complemento_item;

    /**
     * @var string Descrição
     */
    private $descricao;

    /**
     * @var int Id extrato contrato
     */
    private $id_extrato_contr;

    /**
     * @var float Valor unitário
     */
    private $vl_unitario;

    /**
     * @var int Quantidade Contrato
     */
    private $qt_contrato;

    /**
     * @var float Valor contrato
     */
    private $vl_contrato;

    /**
     * @var int Quantidade Utilizada
     */
    private $qt_utilizado;

    /**
     * @var float Valor utilizado
     */
    private $vl_utilizado;

    /**
     * @var int Quantidade Saldo
     */
    private $qt_saldo;

    /**
     * @var float Valor saldo
     */
    private $vl_saldo;

    /**
     * @var int Id da unidade
     */
    private $id_unidade;

    /**
     * @var int Ano Orçamento
     */
    private $ano_orcamento;

    /**
     * @var bool Flag that indicates if this item was cancel.
     */
    private $cancelado;

    /**
     * @var string Chave
     */
    private $chave;

    /**
     * @var string Seq item processo.
     */
    private $seq_item_processo;

    /**
     * @return bool
     */
    public function isCancelado(): bool {
        return $this->cancelado;
    }

    /**
     * Item constructor.
     * @param int $id Item id from db.
     */
    public function __construct(int $id) {
        $this->id = $id;
        $this->initItem();
    }

    private function initItem() {
        $query = Query::getInstance()->exe("SELECT id_item_processo, id_item_contrato, cod_despesa, descr_despesa, descr_tipo_doc, num_contrato, num_processo, descr_mod_compra, num_licitacao, dt_inicio, dt_fim, dt_geracao, cgc_fornecedor, nome_fornecedor, num_extrato, cod_estruturado, nome_unidade, cod_reduzido, complemento_item, descricao, id_extrato_contr, round(replace(vl_unitario, ',', '.'), 4) AS vl_unitario, qt_contrato, round(replace(vl_contrato, ',', '.'), 4) AS vl_contrato, qt_utilizado, round(replace(vl_utilizado, ',', '.'), 4) AS vl_utilizado, qt_saldo, round(replace(vl_saldo, ',', '.'), 4) AS vl_saldo, id_unidade, ano_orcamento, chave, seq_item_processo, cancelado FROM itens WHERE id = " . $this->id);

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

}