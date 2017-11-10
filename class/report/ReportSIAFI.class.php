<?php
/***
 * @author João Bolsson (joaovictorbolsson@gmail.com).
 * @since 2017, 10 Nov.
 */

include_once 'Report.class.php';

class ReportSIAFI implements Report {

    /**
     * @var int Sector id
     */
    private $sector;

    /**
     * @var int MoneySource source.
     */
    private $source;

    /**
     * @var string The main sql to execute to build the report.
     */
    private $sql;

    /**
     * Default construct.
     *
     * @param int $sector Sector id.
     * @param int $source MoneySource id.
     */
    public function __construct(int $sector, int $source) {
        $this->sector = $sector;
        $this->source = new MoneySource($source);
    }

    /**
     * @return string The report header.
     */
    function buildHeader(): string {
        $fieldset = new Component('fieldset', 'preg');
        $fieldset->addComponent(new Component('h5', '', 'DESCRIÇÃO DO RELATÓRIO'));
        $fieldset->addComponent(new Component('h6', '', 'SIAFI cadastrados por Setor e Fonte de Recurso'));
        $fieldset->addComponent(new Component('h6', '', 'Fonte de Recurso: ' . $this->source->getResource()));
        $fieldset->addComponent(new Component('h6', '', 'Setor: ' . ARRAY_SETORES[$this->sector]));

        return $fieldset;
    }

    /**
     * @return string The report body.
     */
    function buildBody(): string {
        return "";
    }

    /**
     * @return string The report footer.
     */
    function buildFooter(): string {
        return "";
    }

    private function prepareToPrint() {

    }

    function __toString(): string {
        $this->prepareToPrint();

        $report = $this->buildHeader();

        $report .= $this->buildBody();

        $report .= $this->buildFooter();

        return $report;
    }

}