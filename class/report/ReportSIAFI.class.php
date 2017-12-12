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
     * @var array Num processo.
     */
    private $num_processo;

    /**
     * @var string Start date.
     */
    private $dateS;

    /**
     * @var string End date.
     */
    private $dateE;

    /**
     * @var string The main sql to execute to build the report.
     */
    private $sql;

    /**
     * Default construct.
     *
     * @param int $sector Sector id.
     * @param int $source MoneySource id.
     * @param array $num_processo Proccess numbers.
     * @param string $dateS Start date (required format: dd/mm/YYYY).
     * @param string $dateE End date (required format: dd/mm/YYYY).
     */
    public function __construct(int $sector, int $source, array $num_processo, string $dateS, string $dateE) {
        $this->sector = $sector;
        if (in_array('Todos', $num_processo)) {
            $this->num_processo = BuscaLTE::getAllProcess();
        } else {
            $this->num_processo = $num_processo;
        }
        $this->dateS = $dateS;
        $this->dateE = $dateE;
        $this->source = new MoneySource($source);
    }

    /**
     * @return string The report header.
     */
    function buildHeader(): string {
        $fieldset = new Component('fieldset', '');
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
        $fieldset = new Component('fieldset', '');

        $dateS = Util::dateFormat($this->dateS);
        $dateE = Util::dateFormat($this->dateE);

        $where_num = "(";
        $len = count($this->num_processo);
        for ($i = 0; $i < $len; $i++) {
            $where_num .= "itens.num_processo='" . $this->num_processo[$i] . "'";
            if ($i != $len - 1) {
                $where_num .= " OR ";
            }
        }
        $where_num .= ")";

        $this->sql = "SELECT pedido_empenho.id_pedido, pedido_empenho.empenho, DATE_FORMAT(pedido_empenho.data, '%d/%m/%Y') AS data, (SELECT itens.num_processo FROM itens, itens_pedido WHERE itens.id = itens_pedido.id_item AND itens_pedido.id_pedido = pedido_empenho.id_pedido LIMIT 1) AS num_processo, DATE_FORMAT((SELECT dt_inicio FROM itens, itens_pedido WHERE itens.id = itens_pedido.id_item AND itens_pedido.id_pedido = pedido_empenho.id_pedido LIMIT 1), '%d/%m/%Y') AS dt_inicio, DATE_FORMAT((SELECT dt_fim FROM itens, itens_pedido WHERE itens.id = itens_pedido.id_item AND itens_pedido.id_pedido = pedido_empenho.id_pedido LIMIT 1), '%d/%m/%Y') AS dt_fim, pedido.valor FROM pedido_empenho, pedido_id_fonte, pedido WHERE pedido.id = pedido_empenho.id_pedido AND pedido_empenho.id_pedido = pedido_id_fonte.id_pedido AND pedido_id_fonte.id_fonte = " . $this->source->getId() . " AND pedido_empenho.id_pedido IN (SELECT DISTINCT itens_pedido.id_pedido FROM itens_pedido, itens WHERE itens_pedido.id_item = itens.id AND " . $where_num . " AND (itens.dt_inicio BETWEEN '" . $dateS . "' AND '" . $dateE . "') AND (itens.dt_fim BETWEEN '" . $dateS . "' AND '" . $dateE . "')) ORDER BY num_processo ASC;";

        Logger::info($this->sql);

        $query = Query::getInstance()->exe($this->sql);
        if ($query->num_rows > 0) {
            // initialize array with the parts of this report
            $parts = [];
            foreach ($this->num_processo as $num_processo) {
                $parts[$num_processo] = new ReportSIAFIPart($num_processo);
                $fieldset->addComponent($parts[$num_processo]);
            }

            while ($obj = $query->fetch_object()) {
                $row = new Row();
                $row->addComponent(new Column($obj->id_pedido));
                $row->addComponent(new Column($obj->empenho));
                $obj->valor = number_format($obj->valor, 3, ',', '.');
                $row->addComponent(new Column("R$ " . $obj->valor));
                $row->addComponent(new Column($obj->dt_inicio . " à " . $obj->dt_fim));

                $part = $parts[$obj->num_processo];
                if ($part instanceof ReportSIAFIPart) {
                    $part->addComponent($row);
                }
            }
        }

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