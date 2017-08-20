<?php
/**
 * Class that represents a sector and its attributes.
 *
 * @author João Bolsson (joaovictorbolsson@gmail.com).
 * @since 2017, 19 Ago.
 */

final class Sector {

    /**
     * @var int Sector id.
     */
    private $id;

    /**
     * @var float Sector balance.
     */
    private $money;

    /**
     * Sector constructor.
     * @param int $id Sector id.
     */
    public function __construct(int $id) {
        $this->id = $id;
        $this->money = 0.000;
        $this->fillFields();
    }

    private function fillFields() {
        $query = Query::getInstance()->exe('SELECT round(saldo, 3) AS saldo FROM saldo_setor WHERE id_setor = ' . $this->id);
        if ($query->num_rows > 0) {
            $obj = $query->fetch_object();
            $this->money = $obj->saldo;
        }

        return $this->money;
    }

    /**
     * Update the sector balance by its sources values.
     */
    public function updateMoney() {
        $query = Query::getInstance()->exe("SELECT round(sum(valor), 3) AS saldo FROM saldo_fonte WHERE id_setor = " . $this->id);

        if ($query->num_rows > 0) {
            $obj = $query->fetch_object();

            $this->setMoney($obj->saldo);
        }
    }

    /**
     * @param float $money
     */
    public function setMoney(float $money) {
        $this->money = $money;
        Query::getInstance()->exe("UPDATE saldo_setor SET saldo = '" . $this->money . "' WHERE id_setor = " . $this->id);
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
    public function getMoney(): float {
        return $this->money;
    }


}