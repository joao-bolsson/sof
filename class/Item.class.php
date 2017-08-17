<?php
/**
 * Class that defines an item.
 *
 * @author João Bolsson (joaovictorbolsson@gmail.com).
 * @since 2017, 16 Ago.
 */

final class Item {

    /**
     * @var int Item id.
     */
    private $id;

    private $qt_saldo;
    private $qt_utilizado;
    private $vl_saldo;
    private $vl_utilizado;
    private $vl_unitario;

    /**
     * Item constructor.
     * @param int $id Item id from db.
     */
    public function __construct(int $id) {
        $this->id = $id;
        $this->initItem();
    }

    private function initItem() {
        $query = Query::getInstance()->exe("SELECT qt_saldo, qt_utilizado, vl_saldo, vl_utilizado, vl_unitario FROM itens WHERE id = " . $this->id);
        if ($query->num_rows > 0) {
            $obj = $query->fetch_object();

            $this->qt_saldo = $obj->qt_saldo;
            $this->qt_utilizado = $obj->qt_utilizado;
            $this->vl_saldo = $obj->vl_saldo;
            $this->vl_utilizado = $obj->vl_utilizado;
            $this->vl_unitario = $obj->vl_unitario;
        } else {
            Logger::info("[ERROR] init item error!");
        }
    }
}