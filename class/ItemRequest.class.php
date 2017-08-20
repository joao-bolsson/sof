<?php
/**
 * Class that defines an item.
 *
 * @author João Bolsson (joaovictorbolsson@gmail.com).
 * @since 2017, 16 Ago.
 */

final class ItemRequest {

    /**
     * @var int Item id.
     */
    private $id;

    private $qt_saldo;
    private $qt_utilizado;
    private $vl_saldo;
    private $vl_utilizado;
    private $vl_unitario;

    private $qt_requested;

    private $item_value_in_request;

    /**
     * @var bool Flag that indicates if this item was cancel.
     */
    private $cancelado;

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

    /**
     * @param bool $cancelado
     */
    public function setCancelado(bool $cancelado) {
        $this->cancelado = $cancelado;
    }

    /**
     * @param int $qt_saldo
     */
    public function setQtSaldo(int $qt_saldo) {
        $this->qt_saldo = $qt_saldo;
    }

    /**
     * @param int $qt_utilizado
     */
    public function setQtUtilizado(int $qt_utilizado) {
        $this->qt_utilizado = $qt_utilizado;
    }

    /**
     * @param float $vl_saldo
     */
    public function setVlSaldo(float $vl_saldo) {
        $this->vl_saldo = $vl_saldo;
    }

    /**
     * @param float $vl_utilizado
     */
    public function setVlUtilizado(float $vl_utilizado) {
        $this->vl_utilizado = $vl_utilizado;
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
    public function getItemValueInRequest(): float {
        return $this->item_value_in_request;
    }

    /**
     * @return int
     */
    public function getQtRequested(): int {
        return $this->qt_requested;
    }

    /**
     * @param int $qt Number of this item requested in a Request.
     */
    public function setQtdRequested(int $qt) {
        $this->qt_requested = $qt;

        $this->item_value_in_request = $this->qt_requested * $this->vl_unitario;
    }

    private function initItem() {
        $query = Query::getInstance()->exe("SELECT qt_saldo, qt_utilizado, vl_saldo, vl_utilizado, vl_unitario, cancelado FROM itens WHERE id = " . $this->id);
        if ($query->num_rows > 0) {
            $obj = $query->fetch_object();

            $this->qt_saldo = $obj->qt_saldo;
            $this->qt_utilizado = $obj->qt_utilizado;
            $this->vl_saldo = $obj->vl_saldo;
            $this->vl_utilizado = $obj->vl_utilizado;
            $this->vl_unitario = $obj->vl_unitario;
            $this->cancelado = $obj->cancelado;
        } else {
            Logger::info("[ERROR] init item error!");
        }
    }
}