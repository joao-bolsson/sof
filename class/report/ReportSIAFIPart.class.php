<?php
/**
 * @author João Bolsson (joaovictorbolsson@gmail.com).
 * @since 2017, 25 Nov.
 */

final class ReportSIAFIPart extends Component implements Report {

    /**
     * @var string Part name.
     */
    private $name;

    /**
     * @var Table Table to show on this part.
     */
    private $table;

    /**
     * @var array Array with sub-parts.
     */
    private $parts;

    /**
     * @var float Sum of this part.
     */
    private $sum = 0.0;

    /**
     * Default constructor.
     *
     * @param string $name Part name.
     */
    public function __construct(string $name) {
        parent::__construct('', '');
        $this->name = $name;

        $this->table = new Table('', 'prod', ['Pedido', 'SIAFI', 'Valor', 'Vigência', 'Licitação'], true);
        $this->parts = [];
    }

    public function buildHeader(): string {
        $fieldset = new Component('fieldset', '');
        $fieldset->addComponent(new Component('h6', '', $this->name . " | Total: R$ " . number_format($this->sum, 3, ',', '.')));

        return $fieldset;
    }

    public function buildBody(): string {
        $body = "";
        if (!$this->table->isEmpty()) {
            $body .= $this->table->__toString();
        }

        foreach ($this->parts as $part) {
            if ($part instanceof ReportSIAFIPart) {
                $body .= $part->__toString();
            }
        }
        return $body;
    }

    public function buildFooter(): string {
        return "";
    }

    public function __toString(): string {
        if ($this->table->isEmpty() && count($this->parts) == 0) {
            return "";
        }
        $part = $this->buildHeader();

        $part .= "<br>" . $this->buildBody();

        $part .= $this->buildFooter();

        return $part;
    }

    /**
     * Gets a part by name.
     *
     * @param string $name The part name
     * @return ReportSIAFIPart The sub-part.
     */
    public function getPart(string $name): ReportSIAFIPart {
        foreach ($this->parts as $part) {
            if ($part instanceof ReportSIAFIPart) {
                if ($part->name == $name) {
                    return $part;
                }
            }
        }

        $part = new ReportSIAFIPart($name);
        $this->addComponent($part);
        return $part;
    }

    public function addComponent(Component $component) {
        if ($component instanceof Row) {
            $this->table->addComponent($component);
        } else if ($component instanceof ReportSIAFIPart) {
            $len = count($this->parts);
            $this->parts[$len] = $component;
        }
    }

    /**
     * Increment the sum of this report part.
     *
     * @param float $val Value to increment.
     */
    public function incrementSum(float $val) {
        $this->sum += $val;
    }

}
