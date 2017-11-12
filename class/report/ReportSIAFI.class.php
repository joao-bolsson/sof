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
     * @var string Num processo.
     */
    private $num_processo;

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
    public function __construct(int $sector, int $source, string $num_processo) {
        $this->sector = $sector;
        $this->num_processo = $num_processo;
        $this->source = new MoneySource($source);
    }

    /**
     * @return string The report header.
     */
    function buildHeader(): string {
        $fieldset = new Component('fieldset', 'preg');
        $fieldset->addComponent(new Component('h5', '', 'DESCRIÇÃO DO RELATÓRIO'));
        $fieldset->addComponent(new Component('h6', '', 'SIAFI cadastrados por Setor e Fonte de Recurso'));
        $fieldset->addComponent(new Component('h6', '', 'Setor: ' . ARRAY_SETORES[$this->sector]));
        $fieldset->addComponent(new Component('h6', '', 'Fonte de Recurso: ' . $this->source->getResource()));

        return $fieldset;
    }

    /**
     * @return string The report body.
     */
    function buildBody(): string {
        $fieldset = new Component('fieldset', 'prod');
        $table = new Table('', 'prod', ['Pedido', 'SIAFI', 'Data de Empenho'], true);

        $this->sql = "SELECT pedido_empenho.id_pedido, pedido_empenho.empenho, DATE_FORMAT(pedido_empenho.data, '%d/%m/%Y') AS data FROM pedido_empenho, pedido_id_fonte WHERE pedido_empenho.id_pedido = pedido_id_fonte.id_pedido AND pedido_id_fonte.id_fonte = " . $this->source->getId() . " AND pedido_empenho.id_pedido IN (SELECT DISTINCT itens_pedido.id_pedido FROM itens_pedido, itens WHERE itens_pedido.id_item = itens.id AND itens.num_processo='" . $this->num_processo . "');";
        $query = Query::getInstance()->exe($this->sql);
        if ($query->num_rows > 0) {
            while ($obj = $query->fetch_object()) {
                $row = new Row();
                $row->addComponent(new Column($obj->id_pedido));
                $row->addComponent(new Column($obj->empenho));
                $row->addComponent(new Column($obj->data));

                $table->addComponent($row);
            }
        }

        $fieldset->addComponent($table);
        return $fieldset;
    }

    /**
     * @return string The report footer.
     */
    function buildFooter(): string {
        return "";
    }

    /**
     * @return string The string representation of this report.
     */
    public function __toString(): string {
        $report = "";
        try {
            $report .= $this->buildHeader();
        } catch (TypeError $ex) {
            Logger::info("Error on build header of report SIAFI: " . $ex->getMessage());
        }

        $report .= "<br>" . $this->buildBody();

        $report .= $this->buildFooter();

        return $report;
    }

}