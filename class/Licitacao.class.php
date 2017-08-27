<?php
/**
 * Class that represents a bidding.
 *
 * @author João Bolsson (joaovictorbolsson@gmail.com).
 * @since 2017, 20 Ago.
 */

final class Licitacao {

    /**
     * @var int
     */
    private $id;

    /**
     * @var int
     */
    private $tipo;

    /**
     * @var string
     */
    private $numero;

    /**
     * @var string
     */
    private $uasg;

    /**
     * @var string
     */
    private $processo_original;

    /**
     * @var int
     */
    private $gera_contrato;

    /**
     * Licitacao constructor.
     * @param int $id Licitacao id.
     * @param string $numero Licitacao number.
     * @param string $uasg Licitacao UASG.
     * @param string $procOri Licitacao source proccess.
     * @param int $tipo Licitacao type.
     * @param int $geraContrato If this bidding must generate a contract - true, else - false.
     */
    public function __construct(int $id, string $numero, string $uasg, string $procOri, int $tipo, int $geraContrato) {
        $this->id = $id;
        $this->tipo = $tipo;
        $this->numero = $numero;
        $this->uasg = $uasg;
        $this->processo_original = $procOri;
        $this->gera_contrato = $geraContrato;

        if ($this->tipo != 3 && $this->tipo != 4 && $this->tipo != 2) {
            // Adesão, Compra Compartilhada ou Inexibilidade
            $this->uasg = "";
            $this->processo_original = "";
            $this->gera_contrato = 0;
        }
    }

    /**
     * @return int
     */
    public function getId(): int {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getTipo(): int {
        return $this->tipo;
    }

    /**
     * @return string
     */
    public function getNumero(): string {
        return $this->numero;
    }

    /**
     * @return string
     */
    public function getUasg(): string {
        return $this->uasg;
    }

    /**
     * @return string
     */
    public function getProcessoOriginal(): string {
        return $this->processo_original;
    }

    /**
     * @return int
     */
    public function getGeraContrato(): int {
        return $this->gera_contrato;
    }


}