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
        $query = Query::getInstance()->exe("SELECT round(valor, 3) AS valor, fonte_recurso, ptres, plano_interno FROM saldo_fonte WHERE id = " . $this->id);
        if ($query->num_rows > 0) {
            $obj = $query->fetch_object();

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

    /**
     * @param array $sectors Sectors that will have this money source.
     * @param string $souce Source.
     * @param string $ptres PTRES.
     * @param string $plan Intern plan.
     */
    public static function newSourceToSectors(array $sectors, string $souce, string $ptres, string $plan) {
        foreach ($sectors as $sector) {
            $builder = new SQLBuilder(SQLBuilder::$INSERT);
            $builder->setTables(['saldo_fonte']);
            $builder->setValues([NULL, $sector, 0, $souce, $ptres, $plan]);

            Query::getInstance()->exe($builder->__toString());
        }
    }


}