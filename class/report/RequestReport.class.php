<?php
/**
 *  Class to define a requests report.
 *
 * @author João Bolsson (joaovictorbolsson@gmail.com).
 * @since 2017, 29 Oct.
 */

include_once 'Report.class.php';

class RequestReport implements Report {

    /**
     * @var int Sector id.
     */
    private $sector;

    /**
     * @var array An array with priority of the requests.
     */
    private $priority;

    /**
     * @var array An array with status of the requests.
     */
    private $status;

    /**
     * @var string Start date.
     */
    private $dateS;

    /**
     * @var string End date.
     */
    private $dateE;

    /**
     * @var bool True to consider only requests with siafi, else - all.
     */
    private $checkSIAFI;

    /**
     * @var MoneySource The money source.
     */
    private $source;

    /**
     * @var string The report title.
     */
    private $title;

    private $foundEntries = 0;

    private $shownRows = 0;

    private $totalMoney = 0.0;

    /**
     * @var string Main sql to generate the table with requests.
     */
    private $sql;

    /**
     * @var bool True if this report is of the requests sent to orderer, else - false.
     */
    private $sentToOrderer;

    /**
     * @var array Headers of the main table of this report.
     */
    private $headers;


    /**
     * ====================
     * Where params
     * ====================
     */

    private $where_status;

    private $where_priority;

    private $where_sector;

    private $where_effort;

    private $tb_effort;

    private $effort;

    private $request_source_id;

    private $where_source;

    /**
     * Default constructor.
     *
     * @param int $sector Referenced sector on this report.
     * @param string $dateS Start date to generate the report.
     * @param string $dateE End date to generate the report.
     */
    public function __construct(int $sector, string $dateS, string $dateE) {
        $this->sector = $sector;
        $this->dateS = $dateS;
        $this->dateE = $dateE;

        // default values
        $this->priority = [];
        $this->status = [];
        $this->checkSIAFI = false;
        $this->source = null;
        $this->sentToOrderer = false;

        $this->title = 'Relatório de Pedidos por Setor e Nível de Prioridade';
        $this->sql = "";
        $this->headers = ['Pedido', 'Fornecedor', 'Enviado em', 'Prioridade', 'Status', 'Valor'];

        // where values
        $this->where_sector = 'AND pedido.id_setor = ' . $this->sector;
        $this->where_status = "";
        $this->where_priority = "";
        $this->where_sector = "";
        $this->where_effort = "";
        $this->tb_effort = "";
        $this->effort = "";
        $this->request_source_id = "";
        $this->where_source = "";
    }

    /**
     * @param array $priority Priorities on this report.
     */
    public function setPriority(array $priority) {
        $this->priority = $priority;

        if (!in_array(0, $this->priority)) {
            $len = count($this->priority);
            $this->where_priority = "AND (";
            for ($i = 0; $i < $len; $i++) {
                $this->where_priority .= "pedido.prioridade = " . $this->priority[$i];
                if ($i < $len - 1) {
                    $this->where_priority .= " OR ";
                }
            }
            $this->where_priority .= ") ";
        }
    }

    /**
     * @param array $status Status on this report.
     */
    public function setStatus(array $status) {
        $this->status = $status;

        $this->sentToOrderer = in_array(8, $this->status);
        if ($this->sentToOrderer) {
            $this->title = 'Relatório de Empenhos Enviados ao Ordenador';
            $this->headers = ['Pedido', 'Fornecedor', 'Prioridade', 'SIAFI'];

            // where values
            $this->where_effort = "AND pedido_empenho.id_pedido = pedido.id";
            $this->tb_effort = "pedido_empenho, ";
            $this->effort = ", pedido_empenho.empenho";
        }

        if (!in_array(0, $this->status)) {
            $len = count($this->status);
            $this->where_status = "AND (";
            for ($i = 0; $i < $len; $i++) {
                $this->where_status .= "pedido.status = " . $this->status[$i];
                if ($i < $len - 1) {
                    $this->where_status .= " OR ";
                }
            }
            $this->where_status .= ") ";
        }
    }

    /**
     * @param bool $checkSIAFI
     */
    public function setCheckSIAFI(bool $checkSIAFI) {
        $this->checkSIAFI = $checkSIAFI;
        if ($this->checkSIAFI) {
            $this->where_effort = "AND pedido_empenho.id_pedido = pedido.id";
            $this->tb_effort = "pedido_empenho, ";
            $this->effort = ", pedido_empenho.empenho";
        }
    }

    /**
     * @param int $source Referenced source.
     */
    public function setSource(int $source) {
        if ($source != 0) {
            $this->source = new MoneySource($source);

            $this->request_source_id = ", pedido_id_fonte";
            $this->where_source = "pedido_id_fonte.id_pedido = pedido.id AND pedido_id_fonte.id_fonte = " . $this->source->getId() . " AND";
        }
    }

    private function prepareToPrint() {
        $dataIni = Util::dateFormat($this->dateS);
        $dataFim = Util::dateFormat($this->dateE);

        $from = "{$this->tb_effort} pedido" . $this->request_source_id . ", prioridade, status WHERE " . $this->where_source . " status.id = pedido.status {$this->where_sector} {$this->where_priority} {$this->where_effort} AND prioridade.id = pedido.prioridade {$this->where_status} AND pedido.data_pedido BETWEEN '{$dataIni}' AND '{$dataFim}'";

        $obj_count = Query::getInstance()->exe("SELECT COUNT(pedido.id) AS total FROM " . $from)->fetch_object();
        $this->foundEntries = $obj_count->total;

        $this->sql = "SELECT pedido.id, DATE_FORMAT(pedido.data_pedido, '%d/%m/%Y') AS data_pedido, prioridade.nome AS prioridade, status.nome AS status, pedido.valor {$this->effort} FROM " . $from . " ORDER BY pedido.id DESC";

        $query_tot = Query::getInstance()->exe("SELECT round(replace(sum(pedido.valor), ',', '.'), 3) AS total FROM " . $from);
        $tot = $query_tot->fetch_object();
        $this->totalMoney = $tot->total;
    }

    /**
     * @return string The report header.
     */
    public function buildHeader(): string {
        $fieldset = new Component('fieldset', 'preg');
        $fieldset->addComponent(new Component('h5', '', 'DESCRIÇÃO DO RELATÓRIO'));
        $fieldset->addComponent(new Component('h6', '', $this->title));
        if ($this->source != null) {
            $fieldset->addComponent(new Component('h6', '', 'Fonte de Recurso: ' . $this->source->getResource()));
        }
        $fieldset->addComponent(new Component('h6', '', 'Setor: ' . ARRAY_SETORES[$this->sector]));
        $fieldset->addComponent(new Component('h6', '', 'Período de Emissão: ' . $this->dateS . ' à ' . $this->dateE));

        $fieldset_results = new Component('fieldset', 'preg');
        $row = new Row();
        $row->addComponent(new Column($this->foundEntries . ' resultados encontrados'));
        $row->addComponent(new Column('Totalizando R$ ' . number_format($this->totalMoney, 3, ',', '.')));

        $fieldset_results->addComponent(new Component('table', '', $row->__toString()));

        return $fieldset . "<br>" . $fieldset_results;
    }

    /**
     * @return string The report body.
     */
    public function buildBody(): string {
        $table = new Table('', 'prod', $this->headers, true);

        $query = Query::getInstance()->exe($this->sql);
        if ($query) {
            while ($request = $query->fetch_object()) {
                $row = new Row();
                $row->addComponent(new Column($request->id));
                $forn = BuscaLTE::getFornecedor($request->id);

                $mb = mb_detect_encoding($forn, 'UTF-8, ISO-8859-1');

                if ($mb == 'ISO-8859-1') {
                    $forn = utf8_encode($forn);
                }

                $row->addComponent(new Column($forn));
                if ($this->sentToOrderer) {
                    $row->addComponent(new Column($request->prioridade));
                    $row->addComponent(new Column($request->empenho));
                } else {
                    $row->addComponent(new Column($request->data_pedido));
                    $row->addComponent(new Column($request->prioridade));
                    $row->addComponent(new Column($request->status));
                    $row->addComponent(new Column('R$ ' . $request->valor));
                }

                $table->addComponent($row);
            }
        }

        return $table;
    }

    /**
     * @return string The report footer.
     */
    public function buildFooter(): string {
        $footer = "";
        if ($_SESSION['id_setor'] == 2) {
            $footer = "<fieldset class=\"preg\"><h5>SUBTOTAIS POR GRUPO</h5>";

            $dataIni = Util::dateFormat($this->dateS);
            $dataFim = Util::dateFormat($this->dateE);

            $query_gr = Query::getInstance()->exe("SELECT pedido_grupo.id_grupo, setores_grupos.nome AS ng, pedido.valor {$this->effort} FROM {$this->tb_effort} setores, setores_grupos, pedido " . $this->request_source_id . ", prioridade, status, pedido_grupo WHERE " . $this->where_source . " setores_grupos.id = pedido_grupo.id_grupo AND pedido_grupo.id_pedido = pedido.id AND status.id = pedido.status {$this->where_sector} {$this->where_priority} {$this->where_effort} AND prioridade.id = pedido.prioridade AND pedido.id_setor = setores.id {$this->where_status} AND pedido.data_pedido BETWEEN '{$dataIni}' AND '{$dataFim}'");

            $array_gr = []; // guarda o somatorio do grupo
            $gr_indexes = []; // guarda os indices do array de cima
            $gr_names = []; // guarda o nome dos grupos
            $k = 0;
            while ($obj = $query_gr->fetch_object()) {
                $index = 'gr' . $obj->id_grupo;
                if (!array_key_exists($index, $array_gr)) {
                    $array_gr[$index] = 0;
                    $gr_indexes[$k] = $index;
                    $gr_names[$index] = $obj->ng;
                    $k++;
                }
                $array_gr[$index] += $obj->valor;
            }

            $count = count($gr_indexes);

            $table_gr = new Table('', 'prod', ['Grupo', 'Total', 'Porcentagem'], true);
            for ($i = 0; $i < $count; $i++) {
                $parcial = number_format($array_gr[$gr_indexes[$i]], 3, ',', '.');
                $porcentagem = number_format(($array_gr[$gr_indexes[$i]] * 100) / $this->totalMoney, 3, ',', '.');

                $row = new Row();
                $row->addComponent(new Column(utf8_encode($gr_names[$gr_indexes[$i]])));
                $row->addComponent(new Column($parcial));
                $row->addComponent(new Column($porcentagem . '%'));

                $table_gr->addComponent($row);
            }

            $footer .= $table_gr . '</fieldset><br>';
        }

        if ($this->sentToOrderer) {
            $footer .= Controller::footerOrdenator();
        }
        return $footer;
    }

    /**
     * @return string The string representation os this class.
     */
    public function __toString(): string {
        $this->prepareToPrint();

        $report = $this->buildHeader();

        $report .= $this->buildBody();

        $report .= $this->buildFooter();

        return $report;
    }

}