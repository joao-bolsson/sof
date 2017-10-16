<?php
/**
 * Class that defines an item.
 *
 * @author João Bolsson (joaovictorbolsson@gmail.com).
 * @since 2017, 16 Ago.
 */

final class ItemRequest extends Item {

    private $qt_requested;

    private $item_value_in_request;

    /**
     * Item Request constructor.
     * @param int $id Item id from db.
     */
    public function __construct(int $id) {
        parent::__construct($id);
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

}