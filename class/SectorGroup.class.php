<?php
/**
 * Class that defines a sector group (see "setores_grupos" table).
 *
 * @author João Bolsson (joaovictorbolsson@gmail.com).
 * @since 2017, 23 Ago.
 */

final class SectorGroup {

    /**
     * @var int
     */
    private $id;

    /**
     * @var Sector Owner sector for this group.
     */
    private $sector;

    /**
     * @var string Group code.
     */
    private $cod;

    /**
     * @var string Group name.
     */
    private $name;

    /**
     * SectorGroup constructor.
     * @param int $id Sector group id.
     */
    public function __construct(int $id) {
        $this->id = $id;
        if ($this->id != 0) {
            $this->init();
        }
    }

    private function init() {
        $query = Query::getInstance()->exe("SELECT id_setor, cod, nome FROM setores_grupos WHERE id = " . $this->id);
        if ($query->num_rows > 0) {
            $obj = $query->fetch_object();

            $this->sector = new Sector($obj->id_setor);
            $this->cod = $obj->cod;
            $this->name = $obj->nome;
        }
    }

    /**
     * @return Sector
     */
    public function getSector(): Sector {
        return $this->sector;
    }

    /**
     * @return string
     */
    public function getCod(): string {
        return $this->cod;
    }

    /**
     * @return string
     */
    public function getName(): string {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getId(): int {
        return $this->id;
    }


}