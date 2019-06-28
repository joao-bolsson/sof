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
     * @var array String source.
     */
    private $source = [];

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
     * @var float Sum of the values of this report.
     */
    private $sum = 0.0;

    /**
     * @var array Parts of this report.
     */
    private $parts = [];

    /**
     * Default construct.
     *
     * @param int $sector Sector id.
     * @param array $source MoneySource id.
     * @param array $num_processo Proccess numbers.
     * @param string $dateS Start date (required format: dd/mm/YYYY).
     * @param string $dateE End date (required format: dd/mm/YYYY).
     */
    public function __construct(int $sector, array $source, array $num_processo, string $dateS, string $dateE) {
        $this->sector = $sector;
        if (in_array('Todos', $num_processo)) {
            $this->num_processo = BuscaLTE::getAllProcess();
        } else {
            $this->num_processo = $num_processo;
        }
        $this->dateS = $dateS;
        $this->dateE = $dateE;

        $i = 0;
        foreach ($source as $moneySource) {
            if ($moneySource != 0) {
                $this->source[$i++] = $moneySource;
            }
        }

        $this->buildParts();
    }

    /**
     * @return string The report header.
     */
    function buildHeader(): string {
        $fieldset = new Component('fieldset', '');
        $fieldset->addComponent(new Component('h5', '', 'DESCRIÇÃO DO RELATÓRIO'));
        $fieldset->addComponent(new Component('h6', '', 'SIAFI cadastrados por Setor e Fonte de Recurso'));
        $fieldset->addComponent(new Component('h6', '', 'Setor: ' . ARRAY_SETORES[$this->sector]));

        $dateS = Util::dateFormat($this->dateS);
        $dateE = Util::dateFormat($this->dateE);

        $fieldset->addComponent(new Component('h6', '', 'Empenhos cadastrados: ' . $dateS . " ~ " . $dateE));
        $fieldset->addComponent(new Component('h6', '', 'Total: R$ ' . number_format($this->sum, 3, ',', '.')));

        return $fieldset;
    }

    /**
     * @return string The report body.
     */
    function buildBody(): string {
        $fieldset = new Component('fieldset', '');

        foreach ($this->parts as $part) {
            $fieldset->addComponent($part);
        }

        return $fieldset;
    }

    private function buildParts() {
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

        $where_source = "";
        $len = count($this->source);

        if ($len > 0) {
            $where_source = "AND (";
            for ($i = 0; $i < $len; $i++) {
                $source = $this->source[$i];
                $where_source .= "pedido_fonte.fonte_recurso = \"" . $source . "\"";
                if ($i != $len - 1) {
                    $where_source .= " OR ";
                }
            }
            $where_source .= ")";
        }

        $this->sql = "SELECT pedido_empenho.id_pedido, pedido_empenho.empenho, DATE_FORMAT(pedido_empenho.data, '%d/%m/%Y') AS data, (SELECT itens.num_processo FROM itens, itens_pedido WHERE itens.id = itens_pedido.id_item AND itens_pedido.id_pedido = pedido_empenho.id_pedido LIMIT 1) AS num_processo, DATE_FORMAT((SELECT dt_inicio FROM itens, itens_pedido WHERE itens.id = itens_pedido.id_item AND itens_pedido.id_pedido = pedido_empenho.id_pedido LIMIT 1), '%d/%m/%Y') AS dt_inicio, DATE_FORMAT((SELECT dt_fim FROM itens, itens_pedido WHERE itens.id = itens_pedido.id_item AND itens_pedido.id_pedido = pedido_empenho.id_pedido LIMIT 1), '%d/%m/%Y') AS dt_fim, pedido.valor, licitacao_tipo.nome AS licitacao, pedido_fonte.fonte_recurso FROM pedido_empenho, pedido_fonte, pedido, licitacao, licitacao_tipo WHERE pedido.id_setor = ". $this->sector ." AND pedido.id = licitacao.id_pedido AND licitacao.tipo = licitacao_tipo.id AND pedido.id = pedido_empenho.id_pedido AND (pedido_empenho.data BETWEEN '" . $dateS . "' AND '" . $dateE . "') AND pedido_empenho.id_pedido = pedido_fonte.id_pedido " . $where_source . " AND pedido_empenho.id_pedido IN (SELECT DISTINCT itens_pedido.id_pedido FROM itens_pedido, itens WHERE itens_pedido.id_item = itens.id AND " . $where_num . ") ORDER BY num_processo ASC;";
        $query = Query::getInstance()->exe($this->sql);
        if ($query->num_rows > 0) {
            // initialize array with the parts of this report
            $this->parts = [];
            foreach ($this->source as $source) {
                $this->parts['sourceId' . $source] = new ReportSIAFIPart('Fonte: ' . $source);
            }

            while ($obj = $query->fetch_object()) {
                $row = new Row();
                $row->addComponent(new Column($obj->id_pedido));
                $row->addComponent(new Column($obj->empenho));
                $row->addComponent(new Column("R$ " . number_format($obj->valor, 3, ',', '.')));
                $row->addComponent(new Column($obj->dt_inicio . " à " . $obj->dt_fim));
                $row->addComponent(new Column($obj->licitacao));

                if (!array_key_exists('sourceId' . $obj->fonte_recurso, $this->parts)) {
                    $this->parts['sourceId' . $obj->fonte_recurso] = new ReportSIAFIPart('Fonte: ' . $obj->fonte_recurso);
                }

                $part = $this->parts['sourceId' . $obj->fonte_recurso];
                if ($part instanceof ReportSIAFIPart) {
                    $procPart = $part->getPart('Processo: ' . $obj->num_processo);
                    $procPart->addComponent($row);

                    // increment the sum of all report and of the separated part as well
                    $part->incrementSum($obj->valor);
                    $procPart->incrementSum($obj->valor);

                    $this->sum += $obj->valor;
                }
            }
        }
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
        $report = $this->buildHeader();

        $report .= "<br>" . $this->buildBody();

        $report .= $this->buildFooter();

        return $report;
    }

}
