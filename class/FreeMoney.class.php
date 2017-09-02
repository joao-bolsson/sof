<?php
/**
 * Class that represents a free money (saldos_lancamentos).
 *
 * @author João Bolsson (joaovictorbolsson@gmail.com).
 * @since 2017, 30 Ago.
 */

final class FreeMoney {

    /**
     * @var int
     */
    private $id;

    /**
     * @var Sector
     */
    private $sector;

    /**
     * @var string
     */
    private $date;

    /**
     * @var float
     */
    private $value;

    /**
     * @var int
     */
    private $category;

    /**
     * FreeMoney constructor.
     * @param int $id Free money id.
     */
    public function __construct(int $id) {
        $this->id = $id;
        if ($this->id != 0) {
            $this->init();
        }
    }

    private function init() {
        $query = Query::getInstance()->exe("SELECT id_setor, data, round(valor, 3) AS valor, categoria FROM saldos_lancamentos WHERE id = " . $this->id);

        if ($query->num_rows > 0) {
            $obj = $query->fetch_object();

            $this->sector = new Sector($obj->id_setor);
            $this->date = $obj->data;
            $this->value = $obj->valor;
            $this->category = $obj->categoria;
        }
    }

    /**
     * Undo this money release.
     */
    public function undo() {
        if ($this->category == 4) {
            return;
        }

        if ($this->category == 3) { // transferencia
            $this->undoTransf();
            return;
        } else {
            $sectorMoney = $this->sector->getMoney();
            $sectorMoney -= $this->value;
            $this->sector->setMoney($sectorMoney);
        }

        // apaga registros
        Query::getInstance()->exe("DELETE FROM saldos_lancamentos WHERE saldos_lancamentos.id = " . $this->id);

        if ($this->category == 2) {
            Query::getInstance()->exe("UPDATE saldos_adiantados SET saldos_adiantados.status = 0 WHERE saldos_adiantados.id_setor = " . $this->sector->getId() . " AND saldos_adiantados.valor_adiantado = '" . $this->value . "' AND saldos_adiantados.status = 1 AND saldos_adiantados.data_analise = '" . $this->date . "' LIMIT 1;");
        }
    }

    private function undoTransf() {
        // id da origem ou do destino
        $otherId = 0;

        if ($this->value > 0) { // destino
            $otherId = $this->id - 1;
        } else if ($this->value < 0) { // origem
            $otherId = $this->id + 1;
        } else {
            // nothing to do
            return;
        }

        $otherFreeMoney = new FreeMoney($otherId);
        $otherNewMoney = $otherFreeMoney->sector->getMoney();
        $otherFreeMoney->sector->setMoney($otherNewMoney - $this->value);

        $newMoney = $this->sector->getMoney();
        $this->sector->setMoney($newMoney - $this->value);

        // apaga os registros
        Query::getInstance()->exe("DELETE FROM saldos_lancamentos WHERE saldos_lancamentos.id = " . $this->id . " OR saldos_lancamentos.id = " . $otherId);

        $id_ori = $id_dest = 0;
        if ($this->value > 0) { // destino
            $id_dest = $otherFreeMoney->sector->getId();
            $id_ori = $this->sector->getId();
        } else { // origem
            $id_ori = $otherFreeMoney->sector->getId();
            $id_dest = $this->sector->getId();
        }

        Query::getInstance()->exe("DELETE FROM saldos_transferidos WHERE saldos_transferidos.id_setor_ori = " . $id_ori . " AND saldos_transferidos.id_setor_dest = " . $id_dest . " AND saldos_transferidos.valor = '" . $this->value . "' LIMIT 1;");
    }

}