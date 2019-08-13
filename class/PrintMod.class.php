<?php

/**
 * Class with the function used to print any information / report of system.
 *
 * @author João Bolsson (joaovictorbolsson@gmail.com).
 * @since 2017, 28 Jan.
 */
ini_set('display_errors', true);
error_reporting(E_ALL);

include_once 'report/RequestReport.class.php';

spl_autoload_register(function (string $class_name) {
    include_once $class_name . '.class.php';
});

require_once '../defines.php';

final class PrintMod {


    /**
     * Default constructor.
     */
    private function __construct() {
        // empty
    }

    public static function getRelatorioFaturamento(int $id, int $comp, string $dataI, string $dataF): string {
        $query_contr = Query::getInstance()->exe("SELECT numero_contr FROM contratualizacao WHERE id = " . $id);

        $num = $query_contr->fetch_object()->numero_contr;

        $return = "
            <fieldset class=\"preg\">
                    <h5>DESCRIÇÃO DO RELATÓRIO</h5>
                    <h6>Relatório de Faturamento</h6>
                    <h6>Contrato: " . $num . "</h6>
            </fieldset><br>";

        $return .= "
            <fieldset class=\"preg\">
                    <h5>Valores Fixos</h5>
            </fieldset><br>";

        $query = Query::getInstance()->exe("SELECT contratualizacao_prefix.nome, valor FROM contratualizacao_valores, contratualizacao_prefix WHERE contratualizacao_valores.id_tipo = contratualizacao_prefix.id AND contratualizacao_valores.id_contr = " . $id);

        $return .= "<fieldset>";
        $table = new Table('', 'prod', ['Pré-Fixado', 'Valor'], true);

        while ($obj = $query->fetch_object()) {
            $row = new Row();

            $row->addComponent(new Column($obj->nome));
            $row->addComponent(new Column("R$ " . number_format($obj->valor, 2, ',', '.')));

            $table->addComponent($row);
        }

        $queryTotFix = Query::getInstance()->exe("SELECT SUM(valor) as soma FROM contratualizacao_valores, contratualizacao_prefix WHERE contratualizacao_valores.id_tipo = contratualizacao_prefix.id AND contratualizacao_valores.id_contr = " . $id);

        $totFixos = $queryTotFix->fetch_object()->soma;

        $row = new Row();
        $row->addComponent(new Column("<b>Total</b>"));
        $row->addComponent(new Column("R$ " . number_format($totFixos, 2, ',', '.')));

        $table->addComponent($row);

        $return .= $table->__toString() . "</fieldset><br>";

        $dI = Util::dateFormat($dataI);
        $dF = Util::dateFormat($dataF);

        $return .= "
            <fieldset class=\"preg\">
                    <h5>Valores Variáveis</h5>
                    <h6>Período de Lançamento: {$dataI} - {$dataF}</h6>
            </fieldset><br>";

        $query = Query::getInstance()->exe("SELECT DATE_FORMAT(lancamento, '%d/%m/%Y') AS lancamento, mes.sigla_mes AS competencia, faturamento_producao.nome AS producao, faturamento_financiamento.nome AS financiamento, faturamento_complexidade.nome AS complexidade, valor FROM faturamento, mes, faturamento_producao, faturamento_financiamento, faturamento_complexidade WHERE faturamento.competencia = " . $comp . " AND (faturamento.lancamento BETWEEN '" . $dI . "' AND '" . $dF . "') AND faturamento.competencia = mes.id AND faturamento.producao = faturamento_producao.id AND faturamento.financiamento = faturamento_financiamento.id AND faturamento.complexidade = faturamento_complexidade.id AND id_contr = " . $id);

        $return .= "<fieldset>";
        $table = new Table('', 'prod', ['Lançamento', 'Competência', 'Produção', 'Financiamento', 'Complexidade', 'Valor'], true);

        $totVar = 0;
        while ($obj = $query->fetch_object()) {
            $row = new Row();

            $row->addComponent(new Column($obj->lancamento));
            $row->addComponent(new Column($obj->competencia));
            $row->addComponent(new Column($obj->producao));
            $row->addComponent(new Column($obj->financiamento));
            $row->addComponent(new Column($obj->complexidade));
            $row->addComponent(new Column("R$ " . number_format($obj->valor, 2, ',', '.')));

            $table->addComponent($row);

            if ($obj->producao == "SIA" && $obj->financiamento == "MAC" && $obj->complexidade == "MC") {
                // nao soma
            } else if ($obj->producao == "SIH" && $obj->financiamento == "MAC" && $obj->complexidade == "MC") {
                // nao soma
            } else {
                $totVar += $obj->valor;
            }
        }

        $return .= $table->__toString() . "</fieldset><br>";

        $return .= "
            <fieldset class=\"preg\">
                    <h5>Totais</h5>
                    <h6><b>Fixos:</b> R$ " . number_format($totFixos, 2, ',', '.') . "</h6>
                    <h6><b>Variáveis:</b> R$ " . number_format($totVar, 2, ',', '.') . "</h6>
                    <h6><b>Total:</b> R$ " . number_format($totFixos + $totVar, 2, ',', '.') . "</h6>
            </fieldset>";

        return $return;

    }

    public static function getRelatorioReceitas(int $competencia, int $mesRecebimento): string {
        $query_mes = Query::getInstance()->exe("SELECT sigla_mes FROM mes WHERE id = " . $mesRecebimento);
        $receb = $query_mes->fetch_object()->sigla_mes;

        $query_mes = Query::getInstance()->exe("SELECT sigla_mes FROM mes WHERE id = " . $competencia);
        $comp = $query_mes->fetch_object()->sigla_mes;

        $query = Query::getInstance()->exe("SELECT aihs_receita_tipo.nome AS tipo, DATE_FORMAT(recebimento, '%d/%m/%Y') as recebimento, valor, pf FROM aihs_receita, aihs_receita_tipo WHERE aihs_receita.tipo = aihs_receita_tipo.id AND competencia = " . $competencia . " AND MONTH(recebimento) = " . $mesRecebimento);
        $return = "
            <fieldset class=\"preg\">
                    <h5>DESCRIÇÃO DO RELATÓRIO</h5>
                    <h6>Relatório de Receitas Recebidas</h6>
                    <h6>Competência: " . $comp . "</h6>
                    <h6>Mês de Recebimento: " . $receb . "</h6>
            </fieldset><br>";

        $query_fixos_comp = Query::getInstance()->exe("SELECT sum(valor) AS sum FROM aihs_receita WHERE competencia = " . $competencia . " AND MONTH(recebimento) = " . $mesRecebimento . " AND ((tipo >= 1 AND tipo <= 4) OR tipo = 11);");
        $fixos_comp = $query_fixos_comp->fetch_object()->sum;

        $query_fixos_med = Query::getInstance()->exe("SELECT sum(valor) AS sum FROM aihs_receita WHERE tipo = 5 AND competencia = " . $competencia . " AND MONTH(recebimento) = " . $mesRecebimento);
        $fixos_med = $query_fixos_med->fetch_object()->sum;

        $return .= "
            <fieldset class=\"preg\">
                    <h5>Valores Fixos</h5>
                    <h6>REHUF + IAC + FIDEPS + OUTROS + INTERMINISTERIAL: R$ " . number_format($fixos_comp, 3, ',', '.') . "</h6>
                    <h6>Média Complexidade: R$ " . number_format($fixos_med, 2, ',', '.') . "</h6>
                    <h6>Total Fixo: R$ " . number_format($fixos_comp + $fixos_med, 2, ',', '.') . "</h6>
            </fieldset><br>";

        $query_fixos_alta = Query::getInstance()->exe("SELECT sum(valor) AS sum FROM aihs_receita WHERE tipo = 6 AND competencia = " . $competencia . " AND MONTH(recebimento) = " . $mesRecebimento);
        $fixos_alta = $query_fixos_alta->fetch_object()->sum;

        // tipos starts with 'FAEC'
        $query_ids = Query::getInstance()->exe("SELECT id FROM aihs_receita_tipo WHERE nome LIKE 'FAEC%'");

        $where_id = "competencia = " . $competencia . " AND MONTH(recebimento) = " . $mesRecebimento . " AND (";
        while ($obj = $query_ids->fetch_object()) {
            $where_id .= "tipo = " . $obj->id . " OR ";
        }

        $where_id .= "tipo = 0)";

        $query_fixos_faec = Query::getInstance()->exe("SELECT sum(valor) AS sum FROM aihs_receita WHERE " . $where_id);
        $fixos_faec = $query_fixos_faec->fetch_object()->sum;

        $return .= "
            <fieldset class=\"preg\">
                    <h5>Outros Valores Variáveis</h5>
                    <h6>Alta Complexidade: R$ " . number_format($fixos_alta, 2, ',', '.') . "</h6>
                    <h6>FAEC: R$ " . number_format($fixos_faec, 2, ',', '.') . "</h6>
                    <h6>Total: R$ " . number_format($fixos_alta + $fixos_faec, 2, ',', '.') . "</h6>
            </fieldset><br>";

        $return .= "
            <fieldset class=\"preg\">
                    <h6>Total Geral: R$ " . number_format($fixos_comp + $fixos_med + $fixos_alta + $fixos_faec, 2, ',', '.') . "</h6>
            </fieldset><br>
            <fieldset>";

        $table = new Table('', 'prod', ['Tipo', 'Recebimento', 'Valor', 'PF'], true);

        while ($obj = $query->fetch_object()) {
            $row = new Row();
            $row->addComponent(new Column($obj->tipo));
            $row->addComponent(new Column($obj->recebimento));
            $row->addComponent(new Column("R$ " . number_format($obj->valor, 2, ',', '.')));
            $row->addComponent(new Column($obj->pf));

            $table->addComponent($row);
        }

        $return .= $table . '</fieldset>';

        return $return;
    }

    /**
     * @param int $id_user User id.
     * @param string $period Period in the format DD/MM/YYYY - DD/MM/YYYY.
     * @return string User hours report.
     */
    public static function relatorioHora(int $id_user, string $period): string {
        $array = explode(' - ', $period);
        $dataI = Util::dateFormat($array[0]);
        $dataF = Util::dateFormat($array[1]);

        $obj_tot_rows = Query::getInstance()->exe("SELECT count(id) AS count FROM usuario_hora WHERE (entrada BETWEEN '" . $dataI . "' AND '" . $dataF . "') AND id_usuario = " . $id_user)->fetch_object();

        $tot_rows = $obj_tot_rows->count;

        $query = Query::getInstance()->exe("SELECT ip, DATE_FORMAT(entrada, '%d/%m/%Y %H:%i:%s') AS entrada, DATE_FORMAT(saida, '%d/%m/%Y %H:%i:%s') AS saida, horas FROM usuario_hora WHERE (entrada BETWEEN '" . $dataI . "' AND '" . $dataF . "') AND id_usuario = " . $id_user . " LIMIT " . LIMIT_HOURS_REPORT);

        $totHoras = number_format(Util::getTotHoras($id_user, $period), 3, ',', '.');
        $info = (Util::isCurrentLoggedIn($id_user)) ? '<h6>Usuário possui uma saída pendente, a última entrada não foi contabilizada no total.</h6>' : '';
        $return = "
            <fieldset class=\"preg\">
                    <h5>DESCRIÇÃO DO RELATÓRIO</h5>
                    <h6>Relatório de Horários: Usuário " . Util::getUserName($id_user) . "
                         | Período: " . $period . "</h6>
                    <h6>Total de Horas: " . $totHoras . "</h6>
                    " . $info . "
                    <h6>Mostrando " . $query->num_rows . " linhas de " . $tot_rows . " encontradas</h6>
                </fieldset><br>";

        $return .= "
            <fieldset class=\"preg\">
                    <h5>REGISTROS NO PONTO ELETRÔNICO</h5>
                </fieldset><br>";
        $return .= "<fieldset class=\"preg\">";

        $table = new Table('', 'prod', ['IP', 'Entrada', 'Saída', 'Horas'], true);
        while ($obj = $query->fetch_object()) {
            $row = new Row();
            $row->addComponent(new Column($obj->ip));
            $row->addComponent(new Column($obj->entrada));
            $obj->saida = ($obj->saida == NULL) ? '--------------------' : $obj->saida;
            $obj->horas = ($obj->horas == NULL) ? '--------------------' : number_format($obj->horas, 3, ',', '.');
            $row->addComponent(new Column($obj->saida));
            $row->addComponent(new Column($obj->horas));
            $table->addComponent($row);
        }

        $atestados = self::buildTableAtestados($id_user, $dataI, $dataF);

        $return .= $table . '</fieldset>';

        $return .= "<br>
            <fieldset class=\"preg\">
                    <h5>ATESTADOS NO PERÍODO</h5>
                </fieldset><br>";
        $return .= "<fieldset class=\"preg\">" . $atestados . "</fieldset>";
        return $return;
    }

    public static function buildTableAtestados(int $id_user, string $dataI, string $dataF): Table {
        $table = new Table('', 'prod', ['Data da Falta', 'Horas Abonadas', 'Justificativa'], true);

        $query = Query::getInstance()->exe("SELECT DATE_FORMAT(dia, '%d/%m/%Y') AS data, horas, justificativa FROM usuario_atestados WHERE (dia BETWEEN '" . $dataI . "' AND '" . $dataF . "') AND id_usuario = " . $id_user);

        if ($query->num_rows) {
            while ($obj = $query->fetch_object()) {
                $row = new Row();
                $row->addComponent(new Column($obj->data));
                $row->addComponent(new Column($obj->horas));
                $row->addComponent(new Column($obj->justificativa));

                $table->addComponent($row);
            }
        }

        return $table;
    }

    /**
     * @param int $id Contract id.
     * @return string The complete report about a contract.
     */
    public static function getRelContrato(int $id, bool $tudo, bool $notaB, bool $reajusteB, bool $aguardaOrcamentoB, bool $pagaB): string {
        $query = Query::getInstance()->exe("SELECT numero, teto, DATE_FORMAT(dt_inicio, '%d/%m/%Y') AS dt_inicio, DATE_FORMAT(dt_fim, '%d/%m/%Y') AS dt_fim, mensalidade FROM contrato WHERE id = " . $id);

        if (!$query) {
            Logger::error("Erro na query para relatório de contrato");
            return "ERRO";
        }
        $contract = $query->fetch_object();

        $nota = $notaB ? 1 : 0;
        $reajusteDif = $reajusteB ? ">" : "<=";
        $aguardaOrcamento = $aguardaOrcamentoB ? 1 : 0;
        $paga = $pagaB ? 1 : 0;

        $filters = " AND nota = " . $nota . " AND reajuste " . $reajusteDif . " 0 AND aguardaOrcamento = " . $aguardaOrcamento . " AND paga = " . $paga;

        if ($tudo) {
            $filters = "";
        }

        $query_sum = Query::getInstance()->exe("SELECT (SELECT teto FROM contrato WHERE id = " . $id . ") - SUM(valor) AS saldo, SUM(valor) AS sum FROM mensalidade WHERE id_contr = " . $id . $filters);
        $values = $query_sum->fetch_object();

        $rel = "
            <fieldset class=\"preg\">
                    <h5>RELATÓRIO DE CONTRATO</h5>
                </fieldset><br>
            <fieldset>
                <table style=\"font-size: 8pt; margin: 5px;\">
                    <tr>
                        <td style=\"text-align: left;\"><b>Contrato:</b> " . $contract->numero . "</td>
                        <td><b>Teto:</b> R$ " . $contract->teto . "</td>
                        <td><b>Vigência:</b> " . $contract->dt_inicio . " - " . $contract->dt_fim . "</td>
                        <td><b>Mensalidade:</b> R$" . $contract->mensalidade . "</td>
                    </tr>
                </table>
                <p><b>Total de Mensalidades:</b> R$ " . $values->sum . "</p>
                <p><b>Saldo Disponível:</b> R$ " . $values->saldo . "</p>
                <table style=\"font-size: 8pt; margin: 5px;\">
                    <tr>
                        <td><b>Tudo:</b> " . ($tudo ? "Sim" : "Não") . "</td>
                        <td><b>Nota:</b> " . ($notaB ? "Sim" : "Não") . "</td>
                        <td><b>Reajuste:</b> " . ($reajusteB ? "Sim" : "Não") . "</td>
                        <td><b>Aguarda Orçamento:</b> " . ($aguardaOrcamentoB ? "Sim" : "Não") . "</td>
                        <td><b>Paga:</b> " . ($pagaB ? "Sim" : "Não") . "</td>
                    </tr>
                </table>
            </fieldset><br>";

        $table = new Table('', 'prod', ['Período', 'Valor', 'Nota', 'Reajuste'], true);

        $query_mensalidade = Query::getInstance()->exe("SELECT mes.sigla_mes, ano.ano, valor, nota, reajuste FROM mes, ano, mensalidade WHERE id_contr = " . $id . " AND mensalidade.id_mes = mes.id AND mensalidade.id_ano = ano.id" . $filters);

        while ($obj = $query_mensalidade->fetch_object()) {
            $row = new Row();
            $row->addComponent(new Column($obj->sigla_mes . "/" . $obj->ano));
            $row->addComponent(new Column("R$ " . $obj->valor));
            $row->addComponent(new Column($obj->nota ? "Sim" : "Não"));
            $row->addComponent(new Column("R$ " . $obj->reajuste));

            $table->addComponent($row);
        }

        $rel .= "<fieldset>" . $table->__toString() . "</fieldset>";

        return $rel;
    }

    public static function getRelUsers(): string {
        $query = Query::getInstance()->exe('SELECT nome, login, id_setor, email FROM usuario WHERE ativo = 1 ORDER BY nome ASC;');
        $return = "
            <fieldset class=\"preg\">
                    <h5>DESCRIÇÃO DO RELATÓRIO</h5>
                    <h6>Relatório de Usuários Cadastrados no Sistema</h6>
                </fieldset><br>
            <fieldset>";

        $table = new Table('', 'prod', ['Nome', 'Login', 'Setor', 'E-mail'], true);

        while ($user = $query->fetch_object()) {
            $row = new Row();
            $row->addComponent(new Column($user->nome));
            $row->addComponent(new Column($user->login));
            $row->addComponent(new Column(ARRAY_SETORES[$user->id_setor]));
            $row->addComponent(new Column($user->email));

            $table->addComponent($row);
        }

        $return .= $table . '</fieldset>';

        return $return;
    }

    /**
     * Function that returns request header to put in PDF document.
     *
     * @param int $id_request
     * @return string
     */
    public static function getHeader(int $id_request): string {
        $query = Query::getInstance()->exe("SELECT pedido.id, DATE_FORMAT(pedido.data_pedido, '%d/%m/%Y') AS data_pedido, EXTRACT(YEAR FROM pedido.data_pedido) AS ano, mes.sigla_mes AS ref_mes, status.nome AS status, pedido.valor AS valor, pedido.obs, pedido.pedido_contrato, prioridade.nome AS prioridade, pedido.aprov_gerencia, pedido.id_usuario, pedido.procSei, pedido.pedSei FROM prioridade, pedido, mes, status WHERE pedido.prioridade = prioridade.id AND status.id = pedido.status AND pedido.id = " . $id_request . ' AND mes.id = pedido.ref_mes');
        $request = $query->fetch_object();
        $lblRequest = ($request->pedido_contrato) ? 'Pedido de Contrato' : 'Pedido';
        $request->valor = number_format($request->valor, 3, ',', '.');
        $return = "
            <fieldset>
                <table style=\"font-size: 8pt; margin: 5px;\">
                    <tr>
                        <td style=\"text-align: left;\"><b>" . $lblRequest . ":</b> " . $id_request . "</td>
                        <td><b>Data de Envio:</b> " . $request->data_pedido . "</td>
                        <td><b>Situação:</b> " . $request->status . "</td>
                        <td><b>Prioridade:</b> " . $request->prioridade . "</td>
                    </tr>
                </table>
                <p><b>Total do Pedido:</b> R$ " . $request->valor . "</p>
                <p><b>Autor:</b> " . Util::getUserName($request->id_usuario) . "</p>
                <table style=\"font-size: 8pt; margin: 5px;\">
                    <tr>
                        <td style=\"text-align: left;\">" . self::getGrupoPedido($id_request) . "</td>
                        <td style=\"text-align: right;\">" . self::getEmpenho($id_request) . "</td>
                    </tr>
                    <tr>
                        <td style=\"text-align: left;\"><b>Processo SEI: </b>" . $request->procSei . "</td>
                        <td style=\"text-align: left;\"><b>Número SEI: </b>" . $request->pedSei . "</td>
                    </tr>
                </table>";
        $return .= ($request->aprov_gerencia) ? '<p><b>Aprovado Pela Gerência</b></p>' : '';

        $workPlan = self::getWorkPlan($id_request);
        if (!empty($workPlan)) {
            $return .= "<p><b>Plano de Trabalho: </b> " . $workPlan . " </p>";
        }

        $return .= "<p><b>Observação da Unidade Solicitante: </b></p>
                <p style=\"font-weight: normal !important;\">	" . $request->obs . "</p>
            </fieldset><br>";
        $return .= self::getTableFontesAndLicitacao($id_request);
        return $return;
    }

    private static function getWorkPlan(int $id_request): string {
        $query = Query::getInstance()->exe("SELECT plano FROM pedido_plano WHERE id_pedido = " . $id_request);

        if ($query->num_rows > 0) {
            $obj = $query->fetch_object();

            return $obj->plano;
        }
        return "";
    }

    private static function getTableFontesAndLicitacao(int $id_request): string {
        $query = Query::getInstance()->exe("SELECT licitacao.tipo AS id_tipo, licitacao_tipo.nome AS tipo, licitacao.numero, licitacao.uasg, licitacao.processo_original, licitacao.gera_contrato FROM licitacao, licitacao_tipo WHERE licitacao_tipo.id = licitacao.tipo AND licitacao.id_pedido = " . $id_request . ' LIMIT 1');

        $tableFontes = self::getTableFontes($id_request);
        $tableLicitacao = new Table('', '', [], true);

        $tipo = 0;
        if ($query->num_rows > 0) {
            $obj = $query->fetch_object();

            $tipo = $obj->id_tipo;
            $showSources = $tipo == 6 && !$tableFontes->isEmpty();
            $header = ['Tipo de Licitação', 'Número'];
            $i = count($header);
            if ($tipo == 3 || $tipo == 4 || $tipo == 2) {
                $header[$i] = 'UASG';
                $header[$i + 1] = 'Processo Original';
                $header[$i + 2] = 'Contrato';
            } else if ($showSources) {
                $headers = $tableFontes->getHeaders();
                foreach ($headers as $h) {
                    $header[$i++] = $h;
                }
            }
            $tableLicitacao->setHeaders($header);
            $row = new Row();
            $row->addComponent(new Column($obj->tipo));
            $row->addComponent(new Column($obj->numero));

            if (count($header) > 2 && !$showSources) {
                $generate = ($obj->gera_contrato == 0) ? 'Não Gera Contrato' : 'Gera Contrato';
                $row->addComponent(new Column($obj->uasg));
                $row->addComponent(new Column($obj->processo_original));
                $row->addComponent(new Column($generate));
            }

            if ($showSources) {
                $comps = $tableFontes->getComponents();
                foreach ($comps as $comp) {
                    if ($comp instanceof Row) {
                        $columns = $comp->getComponents();
                        foreach ($columns as $col) {
                            $row->addComponent($col);
                        }
                    }
                }
            }
            $tableLicitacao->addComponent($row);
        }

        $return = "";
        if ($tableFontes->isEmpty()) {
            $return .= '<fieldset><h5>PEDIDO AGUARDA FONTE DE RECURSO</h5></fieldset><br>';
        } else if ($tipo != 6) {
            $return .= "<fieldset class=\"preg\">" . $tableFontes . "</fieldset><br>";
        }

        if ($tableLicitacao->isEmpty()) {
            $return .= "<fieldset><h5>PEDIDO SEM LICITAÇÃO</h5></fieldset><br>";
        } else {
            $return .= "<fieldset class=\"preg\">" . $tableLicitacao . "</fieldset><br>";
        }
        return $return;
    }

    /**
     * Function to returns the resource sources of a request.
     *
     * @param int $id_request Request id.
     * @return Table Resource source.
     */
    private static function getTableFontes(int $id_request): Table {
        $query = Query::getInstance()->exe('SELECT fonte_recurso, ptres, plano_interno FROM pedido_fonte WHERE id_pedido = ' . $id_request);
        $table = new Table('', '', ['Fonte de Recurso', 'PTRES', 'Plano Interno'], true);

        if ($query->num_rows > 0) {
            $source = $query->fetch_object();

            $row = new Row();
            $row->addComponent(new Column($source->fonte_recurso));
            $row->addComponent(new Column($source->ptres));
            $row->addComponent(new Column($source->plano_interno));

            $table->addComponent($row);
        }
        return $table;
    }

    private static function getEmpenho(int $id_request): string {
        $query = Query::getInstance()->exe('SELECT contrato_tipo.nome, pedido_contrato.siafi FROM contrato_tipo, pedido_contrato WHERE pedido_contrato.id_tipo = contrato_tipo.id AND pedido_contrato.id_pedido = ' . $id_request);
        $return = '';
        if ($query->num_rows > 0) {
            $obj = $query->fetch_object();
            $return = '<b>Tipo:</b> ' . $obj->nome . " <input type=\"text\" value=\"" . $obj->siafi . "\"/>";
        }
        return $return;
    }

    private static function getGrupoPedido(int $id_request): string {
        $query = Query::getInstance()->exe('SELECT setores_grupos.nome, pedido_grupo.id_pedido FROM setores_grupos, pedido_grupo WHERE pedido_grupo.id_grupo = setores_grupos.id AND pedido_grupo.id_pedido = ' . $id_request);
        if ($query->num_rows > 0) {
            $obj = $query->fetch_object();
            $obj->nome = utf8_encode($obj->nome);
            return '<b>Grupo:</b> ' . $obj->nome;
        }
        return '';
    }

    /**
     * Function to returns the request content (items)
     *
     * @param int $id_request Request id.
     * @return string Request content.
     */
    public static function getContentPedido(int $id_request): string {
        $return = '';
        // PRIMEIRO FAZEMOS O CABEÇALHO REFERENTE AO NUM_LICITACAO
        $query_ini = Query::getInstance()->exe("SELECT DISTINCT itens.num_licitacao, itens.num_processo, DATE_FORMAT(itens.dt_inicio, '%d/%m/%Y') AS dt_inicio, DATE_FORMAT(itens.dt_fim, '%d/%m/%Y') AS dt_fim FROM itens_pedido, itens WHERE itens.id = itens_pedido.id_item AND itens_pedido.id_pedido = " . $id_request);
        while ($bidding = $query_ini->fetch_object()) {
            $row = new Row();
            $row->addComponent(new Column('Licitação: ' . $bidding->num_licitacao));
            $row->addComponent(new Column('Processo: ' . $bidding->num_processo));
            $row->addComponent(new Column('Início: ' . $bidding->dt_inicio));
            $row->addComponent(new Column('Fim: ' . (empty($bidding->dt_fim) ? '------------' : $bidding->dt_fim)));
            $return .= "
                <fieldset class=\"preg\">
                    <table>" . $row . "</table>
                </fieldset><br>";
            $query_forn = Query::getInstance()->exe("SELECT DISTINCT itens.cgc_fornecedor, itens.nome_fornecedor, itens.num_contrato FROM itens, itens_pedido WHERE itens.id = itens_pedido.id_item AND itens_pedido.id_pedido = " . $id_request . " AND itens.num_licitacao = '" . $bidding->num_licitacao . "'");

            $fornecedores = [];

            while ($provider = $query_forn->fetch_object()) {
                $fornecedores[$provider->cgc_fornecedor] = $provider->nome_fornecedor;
            }

            $query_forn = Query::getInstance()->exe("SELECT DISTINCT itens.cgc_fornecedor, itens.num_contrato FROM itens, itens_pedido WHERE itens.id = itens_pedido.id_item AND itens_pedido.id_pedido = " . $id_request . " AND itens.num_licitacao = '" . $bidding->num_licitacao . "'");

            // --------------------------------------------------------
            //                FORNECEDORES REFERENTES À LICITAÇÃO
            // --------------------------------------------------------
            while ($provider = $query_forn->fetch_object()) {
                $nome_fornecedor = $fornecedores[$provider->cgc_fornecedor];
                $nome_fornecedor = substr($nome_fornecedor, 0, 40);
                $nome_fornecedor = strtoupper($nome_fornecedor);
                $nome_fornecedor = utf8_encode($nome_fornecedor);
                $return .= "
                    <fieldset style=\"border-bottom: 1px solid black; padding: 5px;\">
                        <table>
                            <tr>
                                <td style=\"text-align: left; font-weight: bold;\">" . $nome_fornecedor . "</td>
                                <td>CNPJ: " . $provider->cgc_fornecedor . "</td>
                                <td>Contrato: " . $provider->num_contrato . "</td>
                            </tr>
                        </table>
                    </fieldset>";
                // ----------------------------------------------------------------------
                //                  ITENS REFERENTES AOS FORNECEDORES
                // ----------------------------------------------------------------------
                $query_items = Query::getInstance()->exe("SELECT itens.cod_reduzido, itens.cod_despesa, itens.seq_item_processo, itens.complemento_item, itens.vl_unitario, itens_pedido.qtd, itens_pedido.valor FROM itens, itens_pedido WHERE itens.id = itens_pedido.id_item AND itens_pedido.id_pedido = " . $id_request . " AND itens.cgc_fornecedor = '" . $provider->cgc_fornecedor . "' ORDER BY CAST(itens.seq_item_processo AS UNSIGNED) ASC");

                $table = new Table('', 'prod', ['Código', 'ItemRequest', 'Natureza', 'Descrição', 'Quantidade', 'Valor Unitário', 'Valor Total'], true);
                while ($item = $query_items->fetch_object()) {
                    $mb = mb_detect_encoding($item->complemento_item, 'UTF-8, ISO-8859-1');
                    $item->complemento_item = mb_strtoupper($item->complemento_item, $mb);
                    $item->valor = number_format($item->valor, 3, ',', '.');
                    $row = new Row();
                    $row->addComponent(new Column($item->cod_reduzido));
                    $row->addComponent(new Column($item->seq_item_processo));
                    $row->addComponent(new Column($item->cod_despesa));

                    // fix #124 problematic CO²<K, by example
                    $item->complemento_item = str_replace("<", "< ", $item->complemento_item);

                    $compl = new Column($item->complemento_item);
                    $compl->setFontSize(7);
                    $row->addComponent($compl);
                    $row->addComponent(new Column($item->qtd));
                    $row->addComponent(new Column('R$ ' . $item->vl_unitario));
                    $row->addComponent(new Column('R$ ' . $item->valor));

                    $table->addComponent($row);
                }
                $return .= $table . '<br>';
            }
        }

        return $return;
    }

    /**
     * Function to returns the request comments.
     *
     * @param $id_request int Request id.
     * @return string SOF comments in request.
     */
    public static function getComentarios(int $id_request): string {
        $return = "";
        $query_emp = Query::getInstance()->exe("SELECT pedido_empenho.empenho, DATE_FORMAT(pedido_empenho.data, '%d/%m/%Y') AS data FROM pedido_empenho WHERE pedido_empenho.id_pedido = " . $id_request);
        if ($query_emp->num_rows > 0) {
            $effort = $query_emp->fetch_object();
            $row = new Row();
            $row->addComponent(new Column('Data Empenho: ' . $effort->data));
            $row->addComponent(new Column('Empenho: ' . $effort->empenho));

            $return = "<fieldset class=\"preg\"><table>" . $row . "</table></fieldset>";
        } else {
            $row = new Row();
            $row->addComponent(new Column('Empenho: EMPENHO SIAFI PENDENTE'));
            $return = "<fieldset class=\"preg\"><table>" . $row . "</table></fieldset>";
        }

        $query = Query::getInstance()->exe("SELECT DATE_FORMAT(comentarios.data_coment, '%d/%m/%Y') AS data_coment, comentarios.comentario FROM comentarios, prioridade WHERE prioridade.id = comentarios.prioridade AND comentarios.id_pedido = " . $id_request);
        if ($query->num_rows > 0) {
            while ($comment = $query->fetch_object()) {
                $return .= "
                    <fieldset>
                        <p style=\"font-weight: normal;\"> <b>Comentário [" . $comment->data_coment . "]:</b> " . $comment->comentario . "</p>
                    </fieldset>";
            }
        } else {
            $return .= "Sem comentários";
        }
        return $return;
    }

    /**
     * Function to returns the sector id of the request.
     *
     * @param int $id_request Request id.
     * @return int Sector id.
     */
    public static function getSetorPedido(int $id_request): int {
        $query = Query::getInstance()->exe('SELECT id_setor FROM pedido WHERE id = ' . $id_request);
        $obj = $query->fetch_object();
        return $obj->id_setor;
    }

    /**
     * Function to make a specific requests report did by the user of SOF.
     *
     * @param array $requests Selected requests by the user.
     * @param int $status Requests status (default = 8)
     * @return string Custom report.
     */
    public static function getRelPed(array $requests, int $status = 8) {
        if (empty($requests)) {
            return '';
        }
        $return = '';
        $where_status = ($status != 0) ? 'AND pedido.status = ' . $status : '';
        $effort = $tb_effort = $where_effort = "";
        if ($status == 8) {
            $where_effort = "AND pedido_empenho.id_pedido = pedido.id";
            $tb_effort = "pedido_empenho, ";
            $effort = ", pedido_empenho.empenho";
        }

        $where_request = '(';
        $len = count($requests);
        for ($i = 0; $i < $len; $i++) {
            $where_request .= 'pedido.id = ' . $requests[$i];
            if ($i != $len - 1) {
                $where_request .= ' OR ';
            }
        }
        $where_request .= ')';

        $query = Query::getInstance()->exe("SELECT pedido.id, setores.nome AS setor, DATE_FORMAT(pedido.data_pedido, '%d/%m/%Y') AS data_pedido, prioridade.nome AS prioridade, status.id AS id_status, status.nome AS status, pedido.valor " . $effort . ' FROM ' . $tb_effort . ' setores, pedido, prioridade, status WHERE status.id = pedido.status ' . $where_effort . ' AND prioridade.id = pedido.prioridade AND pedido.id_setor = setores.id ' . $where_status . ' AND ' . $where_request . ' ORDER BY pedido.id ASC');

        $title = ($status == 8) ? 'Relatório de Empenhos Enviados ao Ordenador' : 'Relatório de Pedidos por Setor e Nível de Prioridade';

        $headers = ['Pedido', 'Fornecedor', 'Setor'];
        $count = count($headers);

        if ($status == 8) {
            $headers[$count] = 'Prioridade';
            $headers[$count + 1] = 'SIAFI';
        } else {
            $headers[$count] = 'Enviado em';
            $headers[$count + 1] = 'Prioridade';
            $headers[$count + 2] = 'Status';
            $headers[$count + 3] = 'Valor';
        }
        $query_tot = Query::getInstance()->exe('SELECT sum(pedido.valor) AS total FROM ' . $tb_effort . ' pedido WHERE 1 > 0 ' . $where_effort . ' AND pedido.alteracao = 0 ' . $where_status . ' AND ' . $where_request);
        $tot = $query_tot->fetch_object();
        $total = ($tot->total > 0) ? 'R$ ' . number_format($tot->total, 3, ',', '.') : 'R$ 0';
        $return .= "<fieldset class=\"preg\">
                    <h5>DESCRIÇÃO DO RELATÓRIO</h5>
                    <h6>" . $title . "</h6>
                    <h6>Pedidos selecionados pelo SOF</h6></fieldset><br>";

        $row = new Row();
        $row->addComponent(new Column(count($requests) . ' selecionados'));
        $row->addComponent(new Column('Totalizando ' . $total));

        $sub_header = "<fieldset class=\"preg\"><table>" . $row . "</table></fieldset>";

        $table = new Table('', 'prod', $headers, true);

        $flag = false;
        $i = 0;
        while ($request = $query->fetch_object()) {
            if ($request->id_status != 8) {
                $flag = true;
                break;
            } else {
                $i++;
            }

            $row = new Row();
            $row->addComponent(new Column($request->id));
            $row->addComponent(new Column(BuscaLTE::getFornecedor($request->id)));
            $row->addComponent(new Column($request->setor));

            if ($status == 8) {
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
        if ($query->num_rows > 0 && !$flag && $i == count($requests)) {
            $return .= $sub_header . $table;
        } else {
            $return .= 'RELATÓRIO INVÁLIDO: Essa versão suporta apenas pedidos com status de Enviado ao Ordenador.';
            $flag = true;
        }

        if ($status == 8 && !$flag && $i == count($requests)) {
            $return .= Controller::footerOrdenator();
        }
        return $return;
    }

    public static function getRelatorioLib(int $id_sector, array $category, string $dateI, string $dateF): string {
        $where_sector = ($id_sector != 0) ? 'AND id_setor = ' . $id_sector : '';
        $where_category = ' AND (';

        $categories = "";
        $len = count($category);
        for ($i = 0; $i < $len; $i++) {
            $categories .= ucfirst(ARRAY_CATEGORIA[$category[$i]]);
            $where_category .= 'categoria = ' . $category[$i];
            if ($i != $len - 1) {
                $where_category .= ' OR ';
                $categories .= ", ";
            }
        }
        $where_category .= ')';

        $return = "<fieldset class=\"preg\">
                <h5>DESCRIÇÃO DO RELATÓRIO</h5>
                <h6>Relatório de Liberações Orçamentárias</h6>
                <h6>Período de Emissão: " . $dateI . " à " . $dateF . "</h6>
                <h6>Setor: " . ARRAY_SETORES[$id_sector] . "</h6>
                <h6>Categoria: " . $categories . "</h6></fieldset><br>";

        $dataIni = Util::dateFormat($dateI);
        $dataFim = Util::dateFormat($dateF);

        $query = Query::getInstance()->exe("SELECT DATE_FORMAT(data, '%d/%m/%Y') AS data, valor, categoria FROM saldos_lancamentos WHERE data BETWEEN '" . $dataIni . "' AND '" . $dataFim . "' " . $where_sector . $where_category . ' ORDER BY id ASC');

        $table = new Table('', 'prod', ['Data', 'Valor'], true);
        $sum = 0;
        while ($obj = $query->fetch_object()) {
            $sum += $obj->valor;

            $row = new Row();
            $row->addComponent(new Column($obj->data));
            $row->addComponent(new Column('R$ ' . number_format($obj->valor, 3, ',', '.')));

            $table->addComponent($row);
        }

        $return .= $table;

        $return .= "<fieldset class='preg'>
<h5>Somatório: " . number_format($sum, 3, ',', '.') . "</h5>
</fieldset>";
        return $return;
    }

    /**
     * Function that returns the requests report.
     *
     * @param int $id_sector Sector id.
     * @param array $priority Array with priorities.
     * @param array $status Array with status.
     * @param string $dateI Initial date of report.
     * @param string $dateF Final date of report.
     * @param bool $checkSIAFI Request contains SIAFI.
     * @param string $fonte Source id.
     * @return string Content of PDF document
     */
    public static function getRelatorioPedidos(int $id_sector, array $priority, array $status, string $dateI, string $dateF, bool $checkSIAFI, string $fonte): string {
        $report = new RequestReport($id_sector, $dateI, $dateF);
        $report->setPriority($priority);
        $report->setStatus($status);
        $report->setCheckSIAFI($checkSIAFI);
        $report->setSource($fonte);

        return $report;
    }

    /**
     * @param int $type Process type.
     * @return string Report with the process.
     */
    public static function getRelatorioProcessos(int $type): string {
        $return = '';
        $where = ($type != 0) ? ' WHERE id = ' . $type : '';

        $query_proc = Query::getInstance()->exe('SELECT id, nome FROM processos_tipo' . $where);
        while ($tipo_proc = $query_proc->fetch_object()) {
            $query = Query::getInstance()->exe('SELECT processos.num_processo, processos_tipo.nome AS tipo, processos_tipo.id AS id_tipo, processos.estante, processos.prateleira, processos.entrada, processos.saida, processos.responsavel, processos.retorno, processos.obs FROM processos, processos_tipo WHERE processos.tipo = processos_tipo.id AND processos.tipo = ' . $tipo_proc->id . ' ORDER BY processos.tipo ASC');
            if ($query->num_rows > 0) {
                $row = new Row();
                $row->addComponent(new Column('Tipo: ' . $tipo_proc->nome));
                $return .= "<fieldset class=\"preg\"><table>" . $row . "</table></fieldset><br>";
                $table = new Table('', 'prod', ['Processo', 'Tipo', 'Estante', 'Prateleira', 'Entrada', 'Saída', 'Responsável', 'Retorno', 'Obs'], true);
                while ($processo = $query->fetch_object()) {
                    $row = new Row();
                    $row->addComponent(new Column($processo->num_processo));
                    $row->addComponent(new Column($processo->tipo));
                    $row->addComponent(new Column($processo->estante));
                    $row->addComponent(new Column($processo->prateleira));
                    $row->addComponent(new Column($processo->entrada));
                    $row->addComponent(new Column($processo->saida));
                    $row->addComponent(new Column($processo->responsavel));
                    $row->addComponent(new Column($processo->retorno));
                    $row->addComponent(new Column($processo->obs));

                    $table->addComponent($row);
                }
                $return .= $table . '<br>';
            }
        }
        $query_all = Query::getInstance()->exe('SELECT DISTINCT num_processo FROM itens WHERE num_processo NOT IN (SELECT DISTINCT num_processo FROM processos)');
        if ($query_all->num_rows > 0) {
            $return .= "<fieldset class=\"preg\"><h5>Processos Não Cadastrados</h5></fieldset><br>";
            $table = new Table('', 'prod', ['Processo', 'Tipo', 'Estante', 'Prateleira'], true);
            while ($processo = $query_all->fetch_object()) {
                $row = new Row();
                $row->addComponent(new Column($processo->num_processo));
                $row->addComponent(new Column('______________________'));
                $row->addComponent(new Column('______________________'));
                $row->addComponent(new Column('______________________'));

                $table->addComponent($row);
            }
            $return .= $table;
        }

        return $return;
    }

}
