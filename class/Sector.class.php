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

    private $today;

    /**
     * @var array All money sources of this sector.
     */
    private $moneySources;

    /**
     * Sector constructor.
     * @param int $id Sector id.
     */
    public function __construct(int $id) {
        $this->id = $id;
        $this->money = 0.000;
        $this->today = date('Y-m-d');
        $this->moneySources = [];
        $this->fillFields();
    }

    private function fillFields() {
        $query = Query::getInstance()->exe('SELECT round(saldo, 3) AS saldo FROM saldo_setor WHERE id_setor = ' . $this->id);
        if ($query->num_rows > 0) {
            $obj = $query->fetch_object();
            $this->money = floatval($obj->saldo);
        } else {
            Query::getInstance()->exe("INSERT INTO saldo_setor VALUES(NULL, {$this->id}, '0.000');");
        }

        return $this->money;
    }

    /**
     * @param float $money
     */
    public function setMoney(float $money) {
        $this->money = $money;
        Query::getInstance()->exe("UPDATE saldo_setor SET saldo = '" . $this->money . "' WHERE id_setor = " . $this->id);
    }

    /**
     * @param Sector $sector Destination sector (will receive the value).
     * @param float $valor Transference value between two sectors.
     * @param string $just Justificative.
     * @return bool If success - true, else - false.
     */
    public function transferMoneyTo(Sector $sector, float $valor, string $just): bool {
        if ($this->id != 2) {
            // only SOF can make transferences to other sectors
            return false;
        }
        $saldo_ori = $this->money;

        if ($valor > $saldo_ori) {
            return false;
        }
        // registrando a transferência
        $justificativa = Query::getInstance()->real_escape_string($just);
        Query::getInstance()->exe("INSERT INTO saldos_transferidos VALUES(NULL, {$this->id}, {$sector->id}, '{$valor}', '{$justificativa}');");
        // registrando na tabela de lançamentos
        Query::getInstance()->exe("INSERT INTO saldos_lancamentos VALUES(NULL, {$this->id}, '{$this->today}', '-{$valor}', 3), (NULL, {$sector->id}, '{$this->today}', '{$valor}', 3);");
        // atualizando o saldo do setor origem (SOF)
        $saldo_ori -= $valor;
        $this->setMoney($saldo_ori);
        // atualizando o saldo do setor destino
        $saldo_dest = $sector->money + $valor;
        $sector->setMoney($saldo_dest);
        return true;
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