<?php
/**
 * @author João Bolsson (joaovictorbolsson@gmail.com).
 * @since 2017, 25 Nov.
 */

final class ReportSIAFIPart extends Component implements Report {

    /**
     * @var string Process number of this part.
     */
    private $num_processo;

    /**
     * @var Table Table to show on this part.
     */
    private $table;

    /**
     * Default constructor.
     *
     * @param string $num_processo Process number.
     */
    public function __construct(string $num_processo) {
        parent::__construct('', '');
        $this->num_processo = $num_processo;

        $this->table = new Table('', 'prod', ['Pedido', 'SIAFI', 'Valor', 'Vigência'], true);
    }

    public function buildHeader(): string {
        $fieldset = new Component('fieldset', '');
        $fieldset->addComponent(new Component('h6', '', 'Processo: ' . $this->num_processo));

        return $fieldset;
    }

    public function buildBody(): string {
        return $this->table;
    }

    public function buildFooter(): string {
        return "";
    }

    public function __toString(): string {
        if (!$this->table->isEmpty()) {

            $part = $this->buildHeader();

            $part .= "<br>" . $this->buildBody();

            $part .= $this->buildFooter();

            return $part;
        }
        return "";
    }

    public function addComponent(Component $component) {
        if ($component instanceof Row) {
            $this->table->addComponent($component);
        }
    }

}