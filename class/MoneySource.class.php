<?php

/**
 * Class to represent a source money.
 *
 * @author João Bolsson (joaovictorbolsson@gmail.com).
 * @since 2017, 22 Ago.
 */
final class MoneySource {

    /**
     * @var int
     */
    private $id;

    /**
     * @var Sector
     */
    private $sector;

    /**
     * @var float
     */
    private $value;

    /**
     * @var string
     */
    private $resource;

    /**
     * @var string
     */
    private $ptres;

    /**
     * @var string
     */
    private $internPlan;


    /**
     * MoneySource constructor.
     * @param int $id Money source id.
     */
    public function __construct(int $id) {
        $this->id = $id;
        if ($this->id != 0) {
            $this->initSource();
        }
    }

    private function initSource() {
        $query = Query::getInstance()->exe("SELECT id_setor, round(valor, 3) AS valor, fonte_recurso, ptres, plano_interno FROM saldo_fonte WHERE id = " . $this->id);
        if ($query->num_rows > 0) {
            $obj = $query->fetch_object();

            $this->sector = new Sector($obj->id_setor);
            $this->value = $obj->valor;
            $this->resource = $obj->fonte_recurso;
            $this->ptres = $obj->ptres;
            $this->internPlan = $obj->plano_interno;
        }
    }

    /**
     * @return int
     */
    public function getId(): int {
        return $this->id;
    }

    /**
     * @return Sector
     */
    public function getSector(): Sector {
        return $this->sector;
    }

    /**
     * @return float
     */
    public function getValue(): float {
        return $this->value;
    }

    /**
     * @return string
     */
    public function getResource(): string {
        return $this->resource;
    }

    /**
     * @return string
     */
    public function getPtres(): string {
        return $this->ptres;
    }

    /**
     * @return string
     */
    public function getInternPlan(): string {
        return $this->internPlan;
    }

    /**
     * @param float $value
     */
    public function setValue(float $value) {
        $this->value = $value;
        Query::getInstance()->exe("UPDATE saldo_fonte SET valor = '" . $this->value . "' WHERE id = " . $this->id);
    }


}