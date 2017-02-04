<?php

/**
 *  Classe com as funções de busca utilizadas principalmente pelo arquivo php/busca.php
 *  qualquer função que RETORNE dados do banco, devem ser feitas nesta classe
 * 
 *  Usada para a nova aparência da parte administrativa do SOF.
 *
 *  @author João Bolsson (joaovictorbolsson@gmail.com).
 *  @since 2017, 15 Jan.
 */
ini_set('display_erros', true);
error_reporting(E_ALL);

include_once 'Conexao.class.php';
include_once 'Util.class.php';

class BuscaLTE extends Conexao {

    private $mysqli, $obj_Util;

    function __construct() {
        //chama o método contrutor da classe Conexao
        parent::__construct();
        $this->obj_Util = new Util();
    }

    /**
     * Retorna a quantidade de solicitações de adiantamento, alt pedidos e de pedidos em análise.
     * @return type Objeto com as quantidades de solicitações.
     */
    public function getCountSolic() {
        if (is_null($this->mysqli)) {
            $this->mysqli = parent::getConexao();
        }
        $query = $this->mysqli->query("SELECT count(saldos_adiantados.id) AS solic_adi, (SELECT count(solic_alt_pedido.id) FROM solic_alt_pedido WHERE solic_alt_pedido.status = 2) AS solic_alt, (SELECT count(pedido.id) FROM pedido WHERE pedido.status = 2) AS solic_ped FROM saldos_adiantados WHERE saldos_adiantados.status = 2;") or exit("Erro ao buscar número de solicitações.");
        $this->mysqli = NULL;
        $obj = $query->fetch_object();
        return $obj;
    }

    public function getOptionsContrato() {
        if (is_null($this->mysqli)) {
            $this->mysqli = parent::getConexao();
        }
        $query = $this->mysqli->query("SELECT contrato_tipo.id, contrato_tipo.nome FROM contrato_tipo;") or exit("Erro ao buscar opções de contrato.");
        $this->mysqli = NULL;
        $retorno = "";
        while ($obj = $query->fetch_object()) {
            $retorno .= "
                <td>
                    <div class=\"form-group\">
                        <input type=\"radio\" name=\"tipoCont\" id=\"tipoCont" . $obj->id . "\" class=\"minimal\" value=\"" . $obj->id . "\">"
                    . $obj->nome . "
                    </div>
                </td>";
        }

        return $retorno;
    }

    /**
     * @return string Lista de usuários cadastrados.
     */
    public function getUsers(): string {
        if (is_null($this->mysqli)) {
            $this->mysqli = parent::getConexao();
        }
        $query = $this->mysqli->query("SELECT usuario.id, usuario.nome, setores.nome AS setor FROM usuario, setores WHERE usuario.id_setor = setores.id ORDER BY nome ASC;") or exit("Erro ao buscar usuários.");
        $this->mysqli = NULL;

        $retorno = "";
        while ($user = $query->fetch_object()) {
            $retorno .= "<option value=\"" . $user->id . "\">" . $user->nome . " (" . $user->setor . ")</option>";
        }
        return $retorno;
    }

    /**
     * 
     * @param int $id_setor Id do setor para retornar os grupos.
     * @return string Options dentro de um <select> com os grupos de um setor.
     */
    public function getOptionsGrupos(int $id_setor): string {
        if (is_null($this->mysqli)) {
            $this->mysqli = parent::getConexao();
        }
        $query = $this->mysqli->query("SELECT setores_grupos.id, setores_grupos.nome FROM setores_grupos WHERE setores_grupos.id_setor = {$id_setor};") or exit("Erro ao buscar grupos.");
        $this->mysqli = NULL;
        $retorno = "";
        if ($query->num_rows >= 1) {
            $retorno = "";
            while ($obj = $query->fetch_object()) {
                $obj->nome = utf8_encode($obj->nome);
                $retorno .= "<option value=\"" . $obj->id . "\">" . $obj->nome . "</option>";
            }
        }
        return $retorno;
    }

    /**
     * Função para construir as linhas de uma tabela com as opções de licitação para fazer um pedido.
     * @param int $cont Número de radios que devem aparecer em cada linha.
     * @return string Radios buttons com opções de licitação para um pedido.
     */
    public function getOptionsLicitacao(int $cont): string {
        if (is_null($this->mysqli)) {
            $this->mysqli = parent::getConexao();
        }
        $query = $this->mysqli->query("SELECT id, nome FROM licitacao_tipo;") or exit("Erro ao buscar opções de licitação.");
        $this->mysqli = NULL;
        $retorno = "<tr>";
        $i = 0;
        while ($obj = $query->fetch_object()) {
            if ($i == $cont) {
                $i = 0;
                $retorno .= "</tr><tr>";
            }
            $retorno .= "
                <td>
                    <div class=\"form-group\">
                        <input type=\"radio\" name=\"tipoLic\" id=\"tipoLic" . $obj->id . "\" class=\"minimal\" value=\"" . $obj->id . "\" required > " . $obj->nome . "
                    </div>
                </td>";
            $i++;
        }

        return $retorno;
    }

    /**
     * 	Função para retornar uma lista de pedidos conforme o relatório.
     *
     * 	@param $status Status da lista de pedidos no relatório.
     * 	@return Lista de pedidos conforme o solicitado.
     */
    public function getRelatorio(int $status): string {
        $retorno = "";
        $order = "ORDER BY pedido.id DESC";
        $alteracao = "AND pedido.alteracao = 0";
        if ($status == 3) {
            // reprovado
            $alteracao = "AND pedido.alteracao = 1";
        } else if ($status == 5) {
            // aguarda orçamento
            // por nível de prioridade e por montante (pedidos urgentes que somam até R$ 100.000)
            $order = "ORDER BY pedido.id DESC, pedido.prioridade DESC, pedido.valor DESC";
        } else if ($status == 2) {
            // em análise
            $order = "ORDER BY pedido.id DESC, pedido.prioridade DESC";
        }
        if (is_null($this->mysqli)) {
            $this->mysqli = parent::getConexao();
        }
        $query = $this->mysqli->query("SELECT pedido.id, DATE_FORMAT(pedido.data_pedido, '%d/%m/%Y') AS data_pedido, mes.sigla_mes AS ref_mes, prioridade.nome AS prioridade, status.nome AS status, pedido.valor FROM pedido, mes, prioridade, status WHERE prioridade.id = pedido.prioridade AND status.id = pedido.status AND mes.id = pedido.ref_mes AND pedido.status = {$status} {$alteracao} {$order};") or exit("Erro ao gerar relatório.");
        $this->mysqli = NULL;
        while ($pedido = $query->fetch_object()) {
            $empenho = BuscaLTE::verEmpenho($pedido->id);
            $btnVerProcesso = "";
            if ($status == 2) {
                // em análise
                // por ordem descrecente do término do processo
                // botão para os processos presentes no pedido e suas datas
                $btnVerProcesso = "
                    <button type=\"button\" class=\"btn btn-primary\" onclick=\"verProcessos(" . $pedido->id . ");\" title=\"Ver Processos\"><i class=\"fa fa-eye\"></i></button>";
            }
            $pedido->valor = number_format($pedido->valor, 3, ',', '.');
            $retorno .= "
                <tr>
                    <td>" . $pedido->id . "</td>
                    <td>" . $pedido->ref_mes . "</td>
                    <td>" . $pedido->data_pedido . "</td>
                    <td>" . $pedido->prioridade . "</td>
                    <td><small class=\"label pull-right bg-gray\">" . $pedido->status . "</small></td>
                    <td>" . $empenho . "</td>
                    <td>R$ " . $pedido->valor . "</td>
                    <td>
                        <button type=\"button\" class=\"btn btn-primary\" onclick=\"imprimir(" . $pedido->id . ");\" title=\"Imprimir\"><i class=\"fa fa-print\"></i></button>
                            " . $btnVerProcesso . "
                    </td>
                </tr>";
        }
        return $retorno;
    }

    /**
     * 	Função para retornar os radios buttons para gerar relatórios por status.
     *
     * 	@return Colunas com alguns status.
     */
    public function getRadiosStatusRel(): string {
        $retorno = "<tr>";
        $i = 0;
        $cont = 5;
        if (is_null($this->mysqli)) {
            $this->mysqli = parent::getConexao();
        }
        $query = $this->mysqli->query("SELECT status.id, status.nome FROM status WHERE status.id <> 1;") or exit("Erro ao buscar as opções de status.");
        $this->mysqli = NULL;
        while ($status = $query->fetch_object()) {
            if ($i == $cont) {
                $i = 0;
                $retorno .= "</tr><tr>";
            }
            $retorno .= "
            <td>
                <div class=\"form-group\">
                    <label>
                        <input id=\"relStatus{$status->id}\" type=\"radio\" name=\"relatorio\" class=\"minimal\" value=\"{$status->id}\"/>
                       " . $status->nome . "
                    </label>
                </div>
            </td>";
            $i++;
        }
        $retorno .= "</tr>";
        return $retorno;
    }

    /**
     * Função para trazer informação do fornecedor de um pedido.
     * @param int $id_pedido Id do pedido.
     * @return string Fornecedor do pedido
     */
    public function getFornecedor(int $id_pedido): string {
        if (is_null($this->mysqli)) {
            $this->mysqli = parent::getConexao();
        }
        $query = $this->mysqli->query("SELECT itens.nome_fornecedor FROM itens, itens_pedido WHERE itens_pedido.id_item = itens.id AND itens_pedido.id_pedido = " . $id_pedido . " LIMIT 1;") or exit("Erro ao buscar fornecedor do pedido");
        $this->mysqli = NULL;
        if ($query->num_rows < 1) {
            return '';
        }

        $obj = $query->fetch_object();
        return $obj->nome_fornecedor;
    }

    /**
     * 	Função que retorna os pedidos em análise e o total deles
     *
     * 	@param $id_setor id do setor
     * 	@return string
     */
    public function getPedidosAnalise(int $id_setor): string {
        $retorno = "";
        if (is_null($this->mysqli)) {
            $this->mysqli = parent::getConexao();
        }
        $query = $this->mysqli->query("SELECT sum(pedido.valor) AS soma FROM pedido WHERE pedido.id_setor = {$id_setor} AND pedido.status = 2;") or exit("Erro ao buscar informações dos pedidos em análise.");
        $this->mysqli = NULL;
        if ($query->num_rows > 0) {
            $obj = $query->fetch_object();
            $soma = number_format($obj->soma, 3, ',', '.');
            if ($soma > 0) {
                $retorno = "
                <tr>
                    <td colspan=\"2\">Você tem " . $query->num_rows . " pedido(s) em análise no total de R$ " . $soma . "</td>
                </tr>";
            }
        }
        return $retorno;
    }

    /**
     * 	Função que retornar o empenho de um pedido
     *
     * 	@param $id_pedido Id do pedido.
     * 	@return string
     */
    public function verEmpenho(int $id_pedido): string {
        $retorno = '';
        if (is_null($this->mysqli)) {
            $this->mysqli = parent::getConexao();
        }
        $query = $this->mysqli->query("SELECT empenho FROM pedido_empenho WHERE id_pedido = {$id_pedido};") or exit("Erro so ver empenho.");
        $this->mysqli = NULL;
        if ($query->num_rows < 1) {
            $retorno = 'EMPENHO SIAFI PENDENTE';
        } else {
            $obj = $query->fetch_object();
            $retorno = $obj->empenho;
        }
        return $retorno;
    }

    /**
     * 	Função que retorna as options com os setores cadastrados no sistema
     *
     * 	@return string
     */
    public function getOptionsSetores(): string {
        $retorno = "";
        if (is_null($this->mysqli)) {
            $this->mysqli = parent::getConexao();
        }
        $query = $this->mysqli->query("SELECT setores.id, setores.nome FROM setores WHERE setores.id <> 1 ORDER BY setores.nome ASC;") or exit("Erro ao buscar os setores cadastrados no sistema.");
        $this->mysqli = NULL;
        while ($setor = $query->fetch_object()) {
            $retorno .= "<option value=\"" . $setor->id . "\">" . $setor->nome . "</option>";
        }
        return $retorno;
    }

    /**
     * 	Função que retornar as options com as prioridades existentes no sistemas para os pedidos
     *
     * 	@return string
     */
    public function getOptionsPrioridades(): string {
        $retorno = "";
        if (is_null($this->mysqli)) {
            $this->mysqli = parent::getConexao();
        }
        $query = $this->mysqli->query("SELECT prioridade.id, prioridade.nome FROM prioridade WHERE prioridade.nome <> 'rascunho';") or exit("Erro ao buscar as prioridades");
        $this->mysqli = NULL;
        while ($prioridade = $query->fetch_object()) {
            $retorno .= "<option value=\"" . $prioridade->id . "\">" . $prioridade->nome . "</option>";
        }
        return $retorno;
    }

    /**
     * 	Função que retorna as options com os status de pedidos
     *
     * 	@return string
     */
    public function getOptionsStatus(): string {
        $retorno = "";
        if (is_null($this->mysqli)) {
            $this->mysqli = parent::getConexao();
        }
        $query = $this->mysqli->query("SELECT status.id, status.nome FROM status WHERE status.nome <> 'Rascunho';") or exit("Erro ao buscar as opções de status.");
        $this->mysqli = NULL;
        if ($query && $query->num_rows > 0) {
            while ($status = $query->fetch_object()) {
                $retorno .= "<option value=\"" . $status->id . "\">" . $status->nome . "</option>";
            }
        }
        return $retorno;
    }

    /**
     * 	Função que retorna as options para tipo de processos para o cadastro de novos processos.
     *
     * 	@return string
     */
    public function getTiposProcessos(): string {
        $retorno = "";
        if (is_null($this->mysqli)) {
            $this->mysqli = parent::getConexao();
        }
        $query = $this->mysqli->query("SELECT id, nome FROM processos_tipo;") or exit("Erro ao buscar os tipos de processo.");
        $this->mysqli = NULL;
        while ($tipo = $query->fetch_object()) {
            $retorno .= "<option value=\"" . $tipo->id . "\">" . $tipo->nome . "</option>";
        }
        return $retorno;
    }

    /**
     * 	Função que retornar se um pedido é ou não rascunho.
     *
     * 	@param $id_pedido id do pedido.
     * 	@return Se o pedido é um rascunho - true, senão false.
     */
    public function getRequestDraft(int $id_pedido): bool {
        if (is_null($this->mysqli)) {
            $this->mysqli = parent::getConexao();
        }
        $query = $this->mysqli->query("SELECT prioridade.nome FROM pedido, prioridade WHERE pedido.id = {$id_pedido} AND pedido.prioridade = prioridade.id;") or exit("Erro ao buscar prioridade do pedido.");
        $this->mysqli = NULL;
        $obj = $query->fetch_object();
        return $obj->nome == 'Rascunho';
    }

    /**
     * 	Função que constroi os radioBtn da análise dos pedidos.
     *
     * 	@param $cont Número de radioBtn por linha.
     * 	@return string
     */
    public function getStatus(int $cont): string {
        $retorno = "<tr>";
        $i = 0;
        if (is_null($this->mysqli)) {
            $this->mysqli = parent::getConexao();
        }
        $query = $this->mysqli->query("SELECT status.id, status.nome FROM status WHERE status.id <> 1;") or exit("Erro ao buscar as opções de status.");
        $this->mysqli = NULL;
        while ($status = $query->fetch_object()) {
            if ($i == $cont) {
                $i = 0;
                $retorno .= "</tr><tr>";
            }
            $retorno .= "
                <td>
                    <div class=\"form-group\">
                        <label>
                            <input id=\"st{$status->id}\" type=\"radio\" name=\"fase\" class=\"minimal\" value=\"{$status->id}\"/>
                           " . $status->nome . "
                        </label>
                    </div>
                </td>";
            $i++;
        }
        $retorno .= "</tr>";
        return $retorno;
    }

    /**
     * 	Função que retornar os radioBtn das prioridades dos pedidos.
     *
     * 	@return Opções de prioridades para os pedidos.
     */
    public function getPrioridades(): string {
        $retorno = "";
        if (is_null($this->mysqli)) {
            $this->mysqli = parent::getConexao();
        }
        $query = $this->mysqli->query("SELECT id, nome FROM prioridade;") or exit("Erro ao buscar prioridades.");
        $this->mysqli = NULL;
        while ($prioridade = $query->fetch_object()) {
            $retorno .= "
                <td>
                    <div class=\"form-group\">
                        <input type=\"radio\" name=\"st\" id=\"st" . $prioridade->nome . "\" class=\"minimal\" value=\"" . $prioridade->id . "\"> " . $prioridade->nome . "
                    </div>
                </td>";
        }
        return $retorno;
    }

    /**
     *   Função utilizada para retornar a tabela dos processos da recepção.
     *
     *   @return string
     */
    public function getTabelaRecepcao(): string {
        $retorno = "";
        if (is_null($this->mysqli)) {
            $this->mysqli = parent::getConexao();
        }
        $query = $this->mysqli->query("SELECT processos.id, processos.num_processo, processos_tipo.nome as tipo, processos.estante, processos.prateleira, processos.entrada, processos.saida, processos.responsavel, processos.retorno, processos.obs FROM processos, processos_tipo WHERE processos.tipo = processos_tipo.id ORDER BY id ASC;") or exit("Erro ao formar a tabela da recepção.");
        while ($processo = $query->fetch_object()) {
            $processo->obs = $this->mysqli->real_escape_string($processo->obs);
            $processo->obs = str_replace("\"", "'", $processo->obs);
            $retorno .= "
                <tr>
                    <td>
                    <div class=\"btn-group\">
                        <button type=\"button\" class=\"btn btn-default\" onclick=\"addProcesso('', " . $processo->id . ")\"><i class=\"fa fa-pencil\"></i></button>
                    </div>
                    </td>
                    <td>" . $processo->num_processo . "</td>
                    <td>" . $processo->tipo . "</td>
                    <td>" . $processo->estante . "</td>
                    <td>" . $processo->prateleira . "</td>
                    <td>" . $processo->entrada . "</td>
                    <td>" . $processo->saida . "</td>
<td>" . $processo->responsavel . "</td>
                    <td>" . $processo->retorno . "</td>
                    <td>
                        <button onclick=\"viewCompl('" . $processo->obs . "');\" class=\"btn btn-default\" type=\"button\" title=\"Ver Observação\">OBS</button>
                    </td>
                </tr>";
        }
        $this->mysqli = NULL;
        return $retorno;
    }

    /**
     * Retorna as permissões para cadastro de usuários.
     */
    public function getCheckPermissoes(): string {
        if (is_null($this->mysqli)) {
            $this->mysqli = parent::getConexao();
        }
        $query = $this->mysqli->query("SELECT column_name AS nome FROM information_schema.columns WHERE table_name = 'usuario_permissoes' AND column_name <> 'id_usuario';") or exit("Erro ao buscar permissões.");
        $this->mysqli = NULL;
        $retorno = "";
        $i = 1;
        while ($obj = $query->fetch_object()) {
            $nome = ucfirst($obj->nome);
            $retorno .= "
                <div class=\"form-group\" style=\"display: inline-block;\">
                    <label>
                        <input id=\"perm{$i}\" type=\"checkbox\" name=\"" . $obj->nome . "\" class=\"minimal\"/>
                        " . $nome . "
                    </label>
                </div>";
            $i++;
        }
        return $retorno;
    }

    /**
     * 	Função que retorna as permissões do usuário.
     *
     * 	@return JSON com as permissões do usuário no sistema.
     */
    public function getPermissoes(int $id_user) {
        if (is_null($this->mysqli)) {
            $this->mysqli = parent::getConexao();
        }
        $query = $this->mysqli->query("SELECT usuario_permissoes.noticias, usuario_permissoes.saldos, usuario_permissoes.pedidos, usuario_permissoes.recepcao FROM usuario_permissoes WHERE usuario_permissoes.id_usuario = {$id_user};") or exit("Erro ao buscar permissões do usuário.");
        $this->mysqli = NULL;
        $obj_permissoes = $query->fetch_object();
        return $obj_permissoes;
    }

    /**
     * 	Função que retorna a tabela com as solicitações de alteração de pedidos	para o SOF analisar
     *
     * 	@return string
     */
    public function getAdminSolicAltPedidos(int $st): string {
        $retorno = "";
        if (is_null($this->mysqli)) {
            $this->mysqli = parent::getConexao();
        }
        $query = $this->mysqli->query("SELECT solic_alt_pedido.id, solic_alt_pedido.id_pedido, setores.nome, DATE_FORMAT(solic_alt_pedido.data_solicitacao, '%d/%m/%Y') AS data_solicitacao, DATE_FORMAT(solic_alt_pedido.data_analise, '%d/%m/%Y') AS data_analise, solic_alt_pedido.justificativa, solic_alt_pedido.status FROM solic_alt_pedido, setores WHERE solic_alt_pedido.id_setor = setores.id AND solic_alt_pedido.status = {$st} ORDER BY solic_alt_pedido.id DESC;") or exit("Erro ao buscar as solicitações de alteração de pedidos enviados ao SOF.");
        $status = $label = "";
        while ($solic = $query->fetch_object()) {
            switch ($solic->status) {
                case 0:
                    $status = 'Reprovado';
                    $label = 'red';
                    break;
                case 1:
                    $status = 'Aprovado';
                    $label = 'green';
                    break;
                default:
                    $status = 'Aberto';
                    $label = 'orange';
                    $solic->data_analise = '--------------';
                    break;
            }
            $btn_aprovar = $btn_reprovar = "";
            if ($st == 2) {
                $btn_aprovar = "<button class=\"btn btn-default\" type=\"button\" title=\"Aprovar\" onclick=\"analisaSolicAlt(" . $solic->id . ", " . $solic->id_pedido . ", 1)\"><i class=\"fa fa-check\"></i></button>";
                $btn_reprovar = "<button class=\"btn btn-default\" type=\"button\" title=\"Reprovar\" onclick=\"analisaSolicAlt(" . $solic->id . ", " . $solic->id_pedido . ", 0)\"><i class=\"fa fa-trash\"></i></button>";
            }
            $solic->justificativa = $this->mysqli->real_escape_string($solic->justificativa);
            $solic->justificativa = str_replace("\"", "'", $solic->justificativa);
            $retorno .= "
                <tr>
                    <td>" . $btn_aprovar . $btn_reprovar . "</td>
                    <td>" . $solic->id_pedido . "</td>
                    <td>" . $solic->nome . "</td>
                    <td>" . $solic->data_solicitacao . "</td>
                    <td>" . $solic->data_analise . "</td>
                    <td>
                        <button onclick=\"viewCompl('" . $solic->justificativa . "')\" class=\"btn btn-sm btn-primary\" type=\"button\" title=\"Ver Justificativa\">JUSTIFICATIVA</button>
                    </td>
                    <td><small class=\"label pull-right bg-" . $label . "\">" . $status . "</small></td>
                </tr>";
        }
        $this->mysqli = NULL;
        return $retorno;
    }

    /**
     * 	Função que retorna as solicitações de adiantamentos de saldos enviadas ao SOF para análise.
     *
     * 	@param $st Status
     * 	@return string
     */
    public function getSolicAdiantamentos(int $st): string {
        if (is_null($this->mysqli)) {
            $this->mysqli = parent::getConexao();
        }
        $query = $this->mysqli->query("SELECT saldos_adiantados.id, setores.nome, DATE_FORMAT(saldos_adiantados.data_solicitacao, '%d/%m/%Y') AS data_solicitacao, DATE_FORMAT(saldos_adiantados.data_analise, '%d/%m/%Y') AS data_analise, saldos_adiantados.valor_adiantado, saldos_adiantados.justificativa FROM saldos_adiantados, setores WHERE saldos_adiantados.id_setor = setores.id AND saldos_adiantados.status = {$st} ORDER BY saldos_adiantados.data_solicitacao DESC;") or exit("Erro ao buscar solicitações de adiantamento.");
        $retorno = "";
        $status = $label = "";
        switch ($st) {
            case 0:
                $status = 'Reprovado';
                $label = 'red';
                break;
            case 1:
                $status = 'Aprovado';
                $label = 'green';
                break;
            case 2:
                $status = 'Aberto';
                $label = 'orange';
                break;
            default:
                break;
        }
        if ($query) {
            while ($solic = $query->fetch_object()) {
                $btn_aprovar = $btn_reprovar = "";
                if ($st == 2) {
                    // em análise / aberto
                    $solic->data_analise = "---------------";
                    $btn_aprovar = "<button class=\"btn btn-default\" type=\"button\" title=\"Aprovar\" onclick=\"analisaAdi(" . $solic->id . ", 1)\"><i class=\"fa fa-check\"></i></button>";
                    $btn_reprovar = "<button class=\"btn btn-default\" type=\"button\" title=\"Reprovar\" onclick=\"javascript:analisaAdi(" . $solic->id . ", 0)\"><i class=\"fa fa-trash\"></i></button>";
                }
                $solic->justificativa = $this->mysqli->real_escape_string($solic->justificativa);
                $solic->justificativa = str_replace("\"", "'", $solic->justificativa);
                $solic->valor_adiantado = number_format($solic->valor_adiantado, 3, ',', '.');
                $retorno .= "
                    <tr>
                        <td>" . $btn_aprovar . $btn_reprovar . "</td>
                        <td>" . $solic->nome . "</td>
                        <td>" . $solic->data_solicitacao . "</td>
                        <td>" . $solic->data_analise . "</td>
                        <td>R$ " . $solic->valor_adiantado . "</td>
                        <td>
                            <button onclick=\"viewCompl('" . $solic->justificativa . "');\" class=\"btn btn-sm btn-primary\" type=\"button\" title=\"Ver Justificativa\">JUSTIFICATIVA</button>
                        </td>
                        <td><small class=\"label pull-right bg-" . $label . "\">" . $status . "</small></td>
                    </tr>";
            }
        }
        $this->mysqli = NULL;
        return $retorno;
    }

    /**
     * @param int $id_pedido Id do pedido
     * @return string Data do cadastro do empenho
     */
    private function verDataEmpenho(int $id_pedido): string {
        if (is_null($this->mysqli)) {
            $this->mysqli = parent::getConexao();
        }
        $query = $this->mysqli->query("SELECT DATE_FORMAT(pedido_empenho.data, '%d/%m/%Y') AS data FROM pedido_empenho WHERE pedido_empenho.id_pedido = {$id_pedido} LIMIT 1;");
        $this->mysqli = NULL;

        if ($query->num_rows < 1) {
            return '';
        }
        $obj = $query->fetch_object();
        return $obj->data;
    }

    /**
     * Função para retornar as solicitações para o SOF.
     *
     * @return string
     *
     */
    public function getSolicitacoesAdmin(): string {
        $retorno = "";
        if (is_null($this->mysqli)) {
            $this->mysqli = parent::getConexao();
        }
        $query = $this->mysqli->query("SELECT pedido.id, pedido.id_setor, setores.nome AS nome_setor, DATE_FORMAT(pedido.data_pedido, '%d/%m/%Y') AS data_pedido, mes.sigla_mes AS ref_mes, prioridade.nome AS prioridade, status.nome AS status, status.id AS id_status, pedido.valor, pedido.aprov_gerencia FROM pedido, setores, mes, prioridade, status WHERE status.id = pedido.status AND pedido.status <> 3 AND prioridade.id = pedido.prioridade AND mes.id = pedido.ref_mes AND pedido.alteracao = 0 AND pedido.id_setor = setores.id ORDER BY pedido.id DESC LIMIT 500;") or exit("Erro ao buscar os pedidos que foram mandados ao SOF.");
        $this->mysqli = NULL;
        while ($pedido = $query->fetch_object()) {
            $btnAnalisar = "";
            if ($pedido->status != 'Reprovado' && $pedido->status != 'Aprovado') {
                if ($_SESSION['id_setor'] == 12) {
                    $btnAnalisar = "<button type=\"button\" class=\"btn btn-default\" onclick=\"enviaForn(" . $pedido->id . ");\" data-toggle=\"tooltip\" title=\"Enviar ao Fornecedor\"><i class=\"fa fa-send\"></i></button>";
                } else if ($pedido->status == 'Em Analise') {
                    $btnAnalisar = "<button type=\"button\" class=\"btn btn-default\" onclick=\"javascript:analisarPedido(" . $pedido->id . ", " . $pedido->id_setor . ");\" data-toggle=\"tooltip\" title=\"Analisar\"><i class=\"fa fa-pencil\"></i></button>";
                } else if ($pedido->status == 'Aguarda Orcamento') {
                    $btnAnalisar = "<button type=\"button\" class=\"btn btn-default\" onclick=\"cadFontes(" . $pedido->id . ");\" data-toggle=\"tooltip\" title=\"Cadastrar Fontes\"><i class=\"fa fa-comment\"></i></button>";
                } else if ($pedido->status == 'Aguarda SIAFI') {
                    $btnAnalisar = "<button type=\"button\" class=\"btn btn-default\" onclick=\"cadEmpenho(" . $pedido->id . ");\" data-toggle=\"tooltip\" title=\"Cadastrar Empenho\"><i class=\"fa fa-credit-card\"></i></button>";
                } else if ($pedido->status == 'Empenhado') {
                    $btnAnalisar = "<button type=\"button\" class=\"btn btn-default\" onclick=\"enviaOrdenador(" . $pedido->id . ");\" data-toggle=\"tooltip\" title=\"Enviar ao Ordenador\"><i class=\"fa fa-send\"></i></button>";
                } else {
                    $btnAnalisar = "<button type=\"button\" class=\"btn btn-default\" onclick=\"javascript:getStatus(" . $pedido->id . ", " . $pedido->id_setor . ");\" data-toggle=\"tooltip\" title=\"Alterar Status\"><i class=\"fa fa-wrench\"></i></button>";
                }
            }
            $btnVerEmpenho = BuscaLTE::verEmpenho($pedido->id);
            if ($btnVerEmpenho == 'EMPENHO SIAFI PENDENTE') {
                $btnVerEmpenho = '';
            } else if ($_SESSION['id_setor'] != 12) {
                $btnAnalisar .= "<button type=\"button\" class=\"btn btn-default\" onclick=\"javascript:cadEmpenho(" . $pedido->id . ", '" . BuscaLTE::verEmpenho($pedido->id) . "', '" . BuscaLTE::verDataEmpenho($pedido->id) . "');\" data-toggle=\"tooltip\" title=\"Cadastrar Empenho\"><i class=\"fa fa-credit-card\"></i></button>";
            }
            $pedido->valor = number_format($pedido->valor, 3, ',', '.');
            $aprovGerencia = '';
            if ($pedido->aprov_gerencia) {
                $aprovGerencia = "<small class=\"label pull-right bg-gray\" data-toggle=\"tooltip\" title=\"Aprovado pela Gerência\">A</small>";
            }

            $linha = "
                <tr id=\"rowPedido" . $pedido->id . "\">
                    <td>
                        <div class=\"form-group\">
                            <input type=\"checkbox\" name=\"checkPedRel\" id=\"checkPedRel" . $pedido->id . "\" value=\"" . $pedido->id . "\">
                        </div>
                    </td>
                    <td>
                        <div class=\"btn-group\">
                            " . $btnAnalisar . "
                            <button type=\"button\" class=\"btn btn-default\" onclick=\"javascript:imprimir(" . $pedido->id . ");\" data-toggle=\"tooltip\" title=\"Imprimir\"><i class=\"fa fa-print\"></i></button>
                        </div>
                    </td>
                    <td>" . $pedido->id . $aprovGerencia . "</td>
                    <td>" . $pedido->nome_setor . "</td>
                    <td>" . $pedido->data_pedido . "</td>
                    <td>" . $pedido->ref_mes . "</td>
                    <td>" . $pedido->prioridade . "</td>
                    <td>" . $pedido->status . "</td>
                    <td>R$ " . $pedido->valor . "</td>
                    <td>
                        " . $btnVerEmpenho . "
                    </td>
                    <td>
                    " . BuscaLTE::getFornecedor($pedido->id) . "
                    </td>
                </tr>";
            if ($_SESSION['id_setor'] == 12) {
                if ($pedido->status == 'Enviado ao Ordenador') {
                    $retorno .= $linha;
                }
            } else {
                $retorno .= $linha;
            }
        }
        return $retorno;
    }

    /**
     * Função para trazer as informações de um pedido a ser analisado.
     *
     * @return string
     */
    public function getItensPedidoAnalise(int $id_pedido): string {
        $retorno = "";
        if (is_null($this->mysqli)) {
            $this->mysqli = parent::getConexao();
        }
        $query = $this->mysqli->query("SELECT itens.qt_contrato, itens.id AS id_itens, itens_pedido.qtd AS qtd_solicitada, itens_pedido.valor, itens.nome_fornecedor, itens.num_licitacao, itens.dt_inicio, itens.dt_fim, itens.cod_reduzido, itens.complemento_item, itens.vl_unitario, itens.qt_saldo, itens.cod_despesa, itens.descr_despesa, itens.num_contrato, itens.num_processo, itens.descr_mod_compra, itens.num_licitacao, itens.cgc_fornecedor, itens.num_extrato, itens.descricao, itens.qt_contrato, itens.vl_contrato, itens.qt_utilizado, itens.vl_utilizado, itens.qt_saldo, itens.vl_saldo, itens.seq_item_processo FROM itens_pedido, itens WHERE itens_pedido.id_pedido = {$id_pedido} AND itens_pedido.id_item = itens.id ORDER BY itens.seq_item_processo ASC;") or exit("Erro ao buscar os itens do pedido para análise.");
        while ($item = $query->fetch_object()) {
            if ($item->dt_fim == '') {
                $item->dt_fim = "----------";
            }
            $item->complemento_item = $this->mysqli->real_escape_string($item->complemento_item);
            $item->complemento_item = str_replace("\"", "'", $item->complemento_item);
            $retorno .= "
                <tr id=\"row_item" . $item->id_itens . "\">
                    <td>
                        <div class=\"btn-group\">
                            <button type=\"button\" class=\"btn btn-default\" onclick=\"cancelaItem(" . $item->id_itens . ");\" title=\"Item Cancelado\"><i id=\"icon-cancela-item" . $item->id_itens . "\" class=\"text-red fa fa-close\"></i>
                            </button>
                            <button type=\"button\" class=\"btn btn-default\" onclick=\"editaItem(" . $item->id_itens . ");\" title=\"Editar\"><i class=\"fa fa-pencil\"></i>
                            </button>
                            <button type=\"button\" class=\"btn btn-default\" onclick=\"viewCompl('" . $item->complemento_item . "');\"  title=\"Ver Complemento do Item\"><i class=\"fa fa-file-text\"></i>
                            </button>
                        </div>
                    </td>
                    <td>" . $item->cod_despesa . "</td>
                    <td>" . $item->descr_despesa . "</td>
                    <td>" . $item->num_extrato . "</td>
                    <td>" . $item->num_contrato . "</td>
                    <td>" . $item->num_processo . "</td>
                    <td>" . $item->descr_mod_compra . "</td>
                    <td>" . $item->num_licitacao . "</td>
                    <td>" . $item->dt_inicio . "</td>
                    <td>" . $item->dt_fim . "</td>
                    <td>" . $item->cgc_fornecedor . "</td>
                    <td>" . $item->nome_fornecedor . "</td>
                    <td>" . $item->cod_reduzido . "</td>
                    <td>" . $item->seq_item_processo . "</td>
                    <td>" . $item->descricao . "</td>
                    <td>R$ " . $item->vl_unitario . "</td>
                    <td>" . $item->qt_contrato . "</td>
                    <td>" . $item->vl_contrato . "</td>
                    <td>" . $item->qt_utilizado . "</td>
                    <td>" . $item->vl_utilizado . "</td>
                    <td>" . $item->qt_saldo . "</td>
                    <td>" . $item->vl_saldo . "</td>
                    <td>" . $item->qtd_solicitada . "</td>
                    <td>R$ " . $item->valor . "</td>
                    <td>
                        <input type=\"hidden\" name=\"id_item[]\" value=\"" . $item->id_itens . "\">
                        <input id=\"item_cancelado" . $item->id_itens . "\" type=\"hidden\" name=\"item_cancelado[]\" value=\"0\">
                        <input type=\"hidden\" name=\"qtd_solicitada[]\" value=\"" . $item->qtd_solicitada . "\">
                        <input type=\"hidden\" name=\"qt_saldo[]\" value=\"" . $item->qt_saldo . "\">
                        <input type=\"hidden\" name=\"qt_utilizado[]\" value=\"" . $item->qt_utilizado . "\">
                        <input type=\"hidden\" name=\"vl_saldo[]\" value=\"" . $item->vl_saldo . "\">
                        <input type=\"hidden\" name=\"vl_utilizado[]\" value=\"" . $item->vl_utilizado . "\">
                        <input type=\"hidden\" name=\"valor_item[]\" value=\"" . $item->valor . "\">
                    </td>
                </tr>";
        }
        $this->mysqli = NULL;
        return $retorno;
    }

    /**
     * 	Função que retorna uma tabela com as solicitações de alteração de pedidos
     *
     * 	@return string
     */
    public function getSolicAltPedidos(int $id_setor): string {
        $retorno = "";
        if (is_null($this->mysqli)) {
            $this->mysqli = parent::getConexao();
        }
        $query = $this->mysqli->query("SELECT solic_alt_pedido.id_pedido, DATE_FORMAT(solic_alt_pedido.data_solicitacao, '%d/%m/%Y') AS data_solicitacao, DATE_FORMAT(solic_alt_pedido.data_analise, '%d/%m/%Y') AS data_analise, solic_alt_pedido.justificativa, solic_alt_pedido.status, pedido.id_usuario FROM solic_alt_pedido, pedido WHERE pedido.id = solic_alt_pedido.id_pedido AND solic_alt_pedido.id_setor = {$id_setor} ORDER BY solic_alt_pedido.id DESC;") or exit("Erro ao buscar solicitações de alteração de pedidos.");
        $status = $label = "";
        while ($solic = $query->fetch_object()) {
            switch ($solic->status) {
                case 0:
                    $status = "Reprovado";
                    $label = "bg-red";
                    break;
                case 1:
                    $status = "Aprovado";
                    $label = "bg-green";
                    break;
                default:
                    $status = "Aberto";
                    $label = "bg-orange";
                    $solic->data_analise = "--------------";
                    break;
            }
            $solic->justificativa = $this->mysqli->real_escape_string($solic->justificativa);
            $solic->justificativa = str_replace("\"", "'", $solic->justificativa);
            if ($solic->id_usuario == $_SESSION['id']) {
                $retorno .= "
                <tr>
                    <td>" . $solic->id_pedido . "</td>
                    <td>" . $solic->data_solicitacao . "</td>
                    <td>" . $solic->data_analise . "</td>
                    <td>
                        <button onclick=\"viewCompl('" . $solic->justificativa . "');\" class=\"btn btn-default\" type=\"button\" title=\"Ver Justificativa\">JUSTIFICATIVA</button>
                    </td>
                    <td><small class=\"label " . $label . "\">" . $status . "</small></td>
                </tr>";
            }
        }
        $this->mysqli = NULL;
        return $retorno;
    }

    /**
     * 	Função que retorna as solicitações de adiantamento de saldos do setor
     *
     * 	@return string
     */
    public function getSolicAdiSetor(int $id_setor): string {
        $retorno = "";
        if (is_null($this->mysqli)) {
            $this->mysqli = parent::getConexao();
        }
        $query = $this->mysqli->query("SELECT saldos_adiantados.id, DATE_FORMAT(saldos_adiantados.data_solicitacao, '%d/%m/%Y') AS data_solicitacao, DATE_FORMAT(saldos_adiantados.data_analise, '%d/%m/%Y') AS data_analise, saldos_adiantados.valor_adiantado, saldos_adiantados.justificativa, saldos_adiantados.status FROM saldos_adiantados WHERE saldos_adiantados.id_setor = {$id_setor} ORDER BY saldos_adiantados.id DESC;") or exit("Erro ao buscar solicitações de adiantamento.");
        $label = $status = "";
        while ($solic = $query->fetch_object()) {
            switch ($solic->status) {
                case 0:
                    $label = "bg-red";
                    $status = "Reprovado";
                    break;
                case 1:
                    $label = "bg-green";
                    $status = "Aprovado";
                    break;
                case 2:
                    $label = "bg-orange";
                    $status = "Aberto";
                    $solic->data_analise = "--------------";
                    break;
            }
            $solic->justificativa = $this->mysqli->real_escape_string($solic->justificativa);
            $solic->justificativa = str_replace("\"", "'", $solic->justificativa);
            $solic->valor_adiantado = number_format($solic->valor_adiantado, 3, ',', '.');
            $retorno .= "
                <tr>
                    <td>" . $solic->data_solicitacao . "</td>
                    <td>" . $solic->data_analise . "</td>
                    <td>R$ " . $solic->valor_adiantado . "</td>
                    <td>
                        <button onclick=\"viewCompl('" . $solic->justificativa . "');\" class=\"btn btn-default\" type=\"button\" title=\"Ver Justificativa\">Justificativa</button>
                    </td>
                    <td><small class=\"label " . $label . "\">" . $status . "</small></td>
                </tr>";
        }
        $this->mysqli = NULL;
        return $retorno;
    }

    /**
     * Função para mostrar os itens de um processo pesquisado no menu solicitações.
     *
     * @return string
     */
    public function getConteudoProcesso(string $busca): string {
        $retorno = "";

        if (is_null($this->mysqli)) {
            $this->mysqli = parent::getConexao();
        }
        $query = $this->mysqli->query("SELECT itens.id, itens.id_item_processo, itens.nome_fornecedor, itens.cod_reduzido, itens.complemento_item, replace(itens.vl_unitario, ',', '.') AS vl_unitario, itens.qt_contrato, itens.qt_utilizado, itens.vl_utilizado, itens.qt_saldo, itens.vl_saldo FROM itens WHERE num_processo LIKE '%{$busca}%' AND cancelado = 0;") or exit("Erro ao buscar o conteúdo dos processos.");

        while ($item = $query->fetch_object()) {
            //remove as aspas do complemento_item
            $item->complemento_item = $this->mysqli->real_escape_string($item->complemento_item);
            $item->complemento_item = str_replace("\"", "'", $item->complemento_item);
            $btn = $input_qtd = '';
            if (!isset($_SESSION['editmode'])) {
                $btn = "<button type=\"button\" class=\"btn btn-default\" onclick=\"checkItemPedido(" . $item->id . ", '" . $item->vl_unitario . "', " . $item->qt_saldo . ")\" data-toggle=\"tooltip\" title=\"Adicionar\"><span class=\"fa fa-plus\"></span></button>";
                $input_qtd = "<td><input type=\"number\" id=\"qtd" . $item->id . "\" min=\"1\" max=\"" . $item->qt_saldo . "\"></td>";
            } else {
                $btn = "<button type=\"button\" class=\"btn btn-default\" onclick=\"editInfoItem(" . $item->id . ")\" data-toggle=\"tooltip\" title=\"Editar Informações\"><span class=\"fa fa-pencil\"></span></button>";
            }
            $retorno .= "
                <tr>
                    <td>" . $btn . "</td>
                    <td>" . $item->nome_fornecedor . "</td>
                    <td>" . $item->cod_reduzido . "</td>
                    " . $input_qtd . "
                    <td>
                        <button type=\"button\" onclick=\"viewCompl('" . $item->complemento_item . "');\" class=\"btn btn-default\" data-toggle=\"tooltip\" title=\"Mais Detalhes\"><span class=\"fa fa-eye\"></span></button>
                    </td>
                    <td style=\"display: none;\">" . $item->complemento_item . "</td>
                    <td>" . $item->vl_unitario . "</td>
                    <td>" . $item->qt_saldo . "</td>
                    <td>" . $item->qt_utilizado . "</td>
                    <td>" . $item->vl_saldo . "</td>
                    <td>" . $item->vl_utilizado . "</td>
                    <td>" . $item->qt_contrato . "</td>
                </tr>";
        }
        $this->mysqli = NULL;
        return $retorno;
    }

    /**
     * Função para trazer a linha do item anexado ao pedido
     *
     * @return string
     */
    public function addItemPedido(int $id_item, int $qtd): string {
        if (is_null($this->mysqli)) {
            $this->mysqli = parent::getConexao();
        }
        $query = $this->mysqli->query("SELECT itens.id, itens.nome_fornecedor, itens.num_licitacao, itens.cod_reduzido, itens.complemento_item, replace(itens.vl_unitario, ',', '.') AS vl_unitario, itens.qt_saldo, itens.qt_contrato, itens.qt_utilizado, itens.vl_saldo, itens.vl_contrato, itens.vl_utilizado FROM itens WHERE itens.id = {$id_item};") or exit("Erro ao buscar ");
        $item = $query->fetch_object();
        $item->complemento_item = $this->mysqli->real_escape_string($item->complemento_item);
        $item->complemento_item = str_replace("\"", "'", $item->complemento_item);
        $valor = $qtd * $item->vl_unitario;
        $retorno = "
            <tr id=\"row" . $id_item . "\">
                <td><button type=\"button\" class=\"btn btn-default\" onclick=\"removeTableRow(" . $id_item . ", '" . $valor . "')\"><span class=\"fa fa-trash\"></span></a></td>
                <td>" . $item->cod_reduzido . "</td>
                <td>
                    <button onclick=\"viewCompl('" . $item->complemento_item . "');\" class=\"btn btn-default\" type=\"button\" title=\"Ver Complemento do Item\"><span class=\"fa fa-eye\"></span></button>
                </td>
                <td>R$ " . $item->vl_unitario . "</td>
                <td>" . $item->nome_fornecedor . "</td>
                <td>" . $item->num_licitacao . "</td>
                <td>" . $qtd . "</td>
                <td>R$ " . $valor . "</td>
                <td>
                    <input class=\"classItens\" type=\"hidden\" name=\"id_item[]\" value=\"" . $id_item . "\">
                    <input type=\"hidden\" name=\"qtd_solicitada[]\" value=\"" . $qtd . "\">
                    <input type=\"hidden\" name=\"qtd_disponivel[]\" value=\"" . $item->qt_saldo . "\">
                    <input type=\"hidden\" name=\"qtd_contrato[]\" value=\"" . $item->qt_contrato . "\">
                    <input type=\"hidden\" name=\"qtd_utilizado[]\" value=\"" . $item->qt_utilizado . "\">
                    <input type=\"hidden\" name=\"vl_saldo[]\" value=\"" . $item->vl_saldo . "\">
                    <input type=\"hidden\" name=\"vl_contrato[]\" value=\"" . $item->vl_contrato . "\">
                    <input type=\"hidden\" name=\"vl_utilizado[]\" value=\"" . $item->vl_utilizado . "\">
                    <input type=\"hidden\" name=\"valor[]\" value=\"" . $valor . "\">
                </td>
            </tr>";
        $this->mysqli = NULL;
        return $retorno;
    }

    /**
     * Função para retornar os rascunhos para continuar editando.
     *
     * @return string
     */
    public function getRascunhos(int $id_setor): string {
        $retorno = "";
        if (is_null($this->mysqli)) {
            $this->mysqli = parent::getConexao();
        }
        $query = $this->mysqli->query("SELECT pedido.id, DATE_FORMAT(pedido.data_pedido, '%d/%m/%Y') AS data_pedido, mes.sigla_mes AS ref_mes, pedido.valor, status.nome AS status, pedido.id_usuario FROM pedido, mes, status WHERE pedido.id_setor = {$id_setor} AND pedido.alteracao = 1 AND mes.id = pedido.ref_mes AND status.id = pedido.status ORDER BY pedido.id DESC;") or exit("Erro ao buscar rascunhos do setor.");
        $this->mysqli = NULL;

        while ($rascunho = $query->fetch_object()) {
            $rascunho->valor = number_format($rascunho->valor, 3, ',', '.');
            $btnEdit = '';
            $btnDel = '';
            if ($rascunho->id_usuario == $_SESSION['id']) {
                $btnEdit = "<button type=\"button\" class=\"btn btn-default btn-sm\" onclick=\"editaPedido(" . $rascunho->id . ");\" title=\"Editar\"><i class=\"fa fa-pencil\"></i></button>";
                $btnDel = "<button type=\"button\" class=\"btn btn-default btn-sm\" onclick=\"deletePedido(" . $rascunho->id . ");\" title=\"Excluir\"><i class=\"fa fa-trash\"></i></button>";
            }
            $retorno .= "
                <tr>
                    <td>" . $rascunho->id . "</td>
                    <td><small class=\"label bg-gray\">" . $rascunho->status . "</small></td>
                    <td>" . $rascunho->ref_mes . "</td>
                    <td>" . $rascunho->data_pedido . "</td>
                    <td>R$ " . $rascunho->valor . "</td>
                    <td>
                        " . $btnEdit . "
                        <button type=\"button\" class=\"btn btn-default btn-sm\" onclick=\"imprimir(" . $rascunho->id . ");\" title=\"Imprimir\"><i class=\"fa fa-print\"></i></button>
                        " . $btnDel . "
                    </td>
                </tr>";
        }
        return $retorno;
    }

    /**
     * 	Função que retorna o saldo dispónível do setor.
     *
     * 	@return string
     */
    public function getSaldo(int $id_setor): string {
        if (is_null($this->mysqli)) {
            $this->mysqli = parent::getConexao();
        }
        $query = $this->mysqli->query("SELECT saldo_setor.saldo FROM saldo_setor WHERE saldo_setor.id_setor = {$id_setor};") or exit("Erro ao buscar o saldo do setor.");
        if ($query->num_rows < 1) {
            $this->mysqli->query("INSERT INTO saldo_setor VALUES(NULL, {$id_setor}, '0.000');") or exit("Erro ao inserir o saldo do setor.");
            $this->mysqli = NULL;
            return '0.000';
        }
        $this->mysqli = NULL;
        $obj = $query->fetch_object();
        $saldo = number_format($obj->saldo, 3, '.', '');
        return $saldo;
    }

    /**
     * Função para retornar o conteúdo de um pedido para edição
     *
     * @access public
     * @return string
     */
    public function getConteudoPedido(int $id_pedido): string {
        $retorno = "";
        if (is_null($this->mysqli)) {
            $this->mysqli = parent::getConexao();
        }
        $query = $this->mysqli->query("SELECT itens.qt_contrato, itens.id AS id_itens, itens_pedido.qtd AS qtd_solicitada, itens_pedido.valor, itens.nome_fornecedor, itens.num_licitacao, itens.cod_reduzido, itens.complemento_item, replace(itens.vl_unitario, ',', '.') AS vl_unitario, itens.qt_saldo, itens.qt_contrato, itens.qt_utilizado, itens.vl_saldo, itens.vl_contrato, itens.vl_utilizado FROM itens_pedido, itens WHERE itens_pedido.id_pedido = {$id_pedido} AND itens_pedido.id_item = itens.id") or exit("Erro ao buscar o conteúdo do pedido.");
        while ($item = $query->fetch_object()) {
            $id_item = $item->id_itens;
            $item->complemento_item = $this->mysqli->real_escape_string($item->complemento_item);
            $item->complemento_item = str_replace("\"", "'", $item->complemento_item);
            $retorno .= "
                <tr id=\"row" . $id_item . "\">
                    <td><button type=\"button\" class=\"btn btn-default\" onclick=\"removeTableRow(" . $id_item . ", '" . $item->valor . "');\" title=\"Remover\"><i class=\"fa fa-trash\"></i></button></td>
                    <td>" . $item->cod_reduzido . "</td>
                    <td>
                        <button type=\"button\" class=\"btn btn-default\" onclick=\"viewCompl('" . $item->complemento_item . "');\"  title=\"Ver Complemento do Item\"><i class=\"fa fa-eye\"></i>
                    </td>
                    <td>R$ " . $item->vl_unitario . "</td>
                    <td>" . $item->nome_fornecedor . "</td>
                    <td>" . $item->num_licitacao . "</td>
                    <td>" . $item->qtd_solicitada . "</td>
                    <td>R$ " . $item->valor . "</td>
                    <td>
                        <input type=\"hidden\" name=\"id_item[]\" value=\"" . $id_item . "\">
                        <input type=\"hidden\" name=\"qtd_solicitada[]\" value=\"" . $item->qtd_solicitada . "\">
                        <input type=\"hidden\" name=\"qtd_disponivel[]\" value=\"" . $item->qt_saldo . "\">
                        <input type=\"hidden\" name=\"qtd_contrato[]\" value=\"" . $item->qt_contrato . "\">
                        <input type=\"hidden\" name=\"qtd_utilizado[]\" value=\"" . $item->qt_utilizado . "\">
                        <input type=\"hidden\" name=\"vl_saldo[]\" value=\"" . $item->vl_saldo . "\">
                        <input type=\"hidden\" name=\"vl_contrato[]\" value=\"" . $item->vl_contrato . "\">
                        <input type=\"hidden\" name=\"vl_utilizado[]\" value=\"" . $item->vl_utilizado . "\">
                        <input type=\"hidden\" name=\"valor[]\" value=\"" . $item->valor . "\">
                    </td>
                </tr>";
        }
        $this->mysqli = NULL;
        return $retorno;
    }

    /**
     * Função para o setor acompanhar o andamento do seu pedido.
     *
     * @return string
     */
    public function getMeusPedidos(int $id_setor): string {
        $retorno = "";
        if (is_null($this->mysqli)) {
            $this->mysqli = parent::getConexao();
        }
        $query = $this->mysqli->query("SELECT pedido.id, DATE_FORMAT(pedido.data_pedido, '%d/%m/%Y') AS data_pedido, mes.sigla_mes AS ref_mes, prioridade.nome AS prioridade, status.nome AS status, pedido.valor, pedido.id_usuario FROM pedido, mes, prioridade, status WHERE prioridade.id = pedido.prioridade AND status.id = pedido.status AND pedido.id_setor = {$id_setor} AND pedido.alteracao = 0 AND mes.id = pedido.ref_mes ORDER BY pedido.id DESC;") or exit("Erro ao buscar os pedidos do setor.");
        $this->mysqli = NULL;
        while ($pedido = $query->fetch_object()) {
            $empenho = BuscaLTE::verEmpenho($pedido->id);
            if ($empenho == 'EMPENHO SIAFI PENDENTE') {
                $empenho = '';
            }
            $pedido->valor = number_format($pedido->valor, 3, ',', '.');
            $btnSolicAlt = "";
            if ($pedido->status == 'Em Analise' || $pedido->status == 'Aguarda Orcamento' && $pedido->id_usuario == $_SESSION['id']) {
                $btnSolicAlt = "<button type=\"button\" class=\"btn btn-default btn-sm\" onclick=\"solicAltPed(" . $pedido->id . ");\" title=\"Solicitar Alteração\"><i class=\"fa fa-wrench\"></i></button>";
            }
            $retorno .= "
                <tr>
                    <td>" . $pedido->id . "</td>
                    <td>" . $pedido->ref_mes . "</td>
                    <td>" . $pedido->data_pedido . "</td>
                    <td>" . $pedido->prioridade . "</td>
                    <td><small class=\"label bg-gray\">" . $pedido->status . "</small></td>
                    <td>" . $empenho . "</td>
                    <td>R$ " . $pedido->valor . "</td>
                    <td>
                        " . $btnSolicAlt . "
                        <button type=\"button\" class=\"btn btn-default btn-sm\" onclick=\"imprimir(" . $pedido->id . ");\" title=\"Imprimir\"><i class=\"fa fa-print\"></i></button>
                    </td>
                </tr>";
        }
        return $retorno;
    }

    /**
     * Retorna todos os processos existes no banco.
     * 
     * @param string $tela Se "recepcao" os processos são usadas para uma coisa se não, são usados para construir um pedido.
     * @return string LInhas com os processos para colocar numa tabela.
     */
    public function getProcessos(string $tela): string {
        $retorno = "";
        $sql = "SELECT DISTINCT itens.num_processo FROM itens;";
        $onclick = "pesquisarProcesso";
        $title = "Pesquisar Processo";
        $icon = "fa-search";
        $act = 'Pesquisar';
        if ($tela == "recepcao") {
            $sql = "SELECT DISTINCT itens.num_processo FROM itens WHERE itens.num_processo NOT IN (SELECT DISTINCT processos.num_processo FROM processos);";
            $onclick = "addProcesso";
            $title = "Adicionar Processo";
            $icon = "fa-plus";
            $act = 'Adicionar';
        }
        if (is_null($this->mysqli)) {
            $this->mysqli = parent::getConexao();
        }
        $query = $this->mysqli->query($sql) or exit("Erro ao buscar os processos.");
        $this->mysqli = NULL;
        while ($processo = $query->fetch_object()) {
            $retorno .= "
                <tr>
                    <td>" . $processo->num_processo . "</td>
                    <td>
                        <button type=\"button\" title=\"" . $title . "\" onclick=\"" . $onclick . "('" . $processo->num_processo . "', 0)\" class=\"btn btn-primary\"><i class=\"fa " . $icon . "\"></i> " . $act . "</button>
                    </td>
                </tr>";
        }
        return $retorno;
    }

    private function getSetorTransf(int $id_lancamento) {
        if (is_null($this->mysqli)) {
            $this->mysqli = parent::getConexao();
        }
        $query = $this->mysqli->query("SELECT saldos_lancamentos.id_setor, saldos_lancamentos.valor FROM saldos_lancamentos WHERE id = " . $id_lancamento) or exit("Erro ao buscar setor da transferência");
        $obj = $query->fetch_object();
        if ($obj->valor < 0) { // pega o destino
            $id_lancamento++;
        } else {
            $id_lancamento--;
        }
        $query_l = $this->mysqli->query("SELECT saldos_lancamentos.id_setor, setores.nome AS setor, saldos_lancamentos.valor FROM saldos_lancamentos, setores WHERE setores.id = saldos_lancamentos.id_setor AND saldos_lancamentos.id = " . $id_lancamento) or exit("Erro ao buscar nome do setor da transferência");
        $this->mysqli = NULL;

        $lancamento = $query_l->fetch_object();
        return $lancamento->setor;
    }

    /**
     * 	Função que retorna a tabela com os lançamentos de saldos pelo SOF
     *
     * 	@param $id_setor id do setor
     * 	@return string
     */
    public function getLancamentos(int $id_setor): string {
        $retorno = "";
        $where = "";
        if ($id_setor != 0) {
            $where = "AND saldos_lancamentos.id_setor = " . $id_setor;
        }
        if (is_null($this->mysqli)) {
            $this->mysqli = parent::getConexao();
        }
        $query = $this->mysqli->query("SELECT saldos_lancamentos.id, setores.nome, DATE_FORMAT(saldos_lancamentos.data, '%d/%m/%Y') AS data, saldos_lancamentos.valor, saldos_lancamentos.categoria AS id_categoria, saldo_categoria.nome AS categoria, saldo_categoria.id AS id_categoria FROM setores, saldos_lancamentos, saldo_categoria WHERE setores.id = saldos_lancamentos.id_setor {$where} AND saldos_lancamentos.categoria = saldo_categoria.id ORDER BY saldos_lancamentos.id DESC;") or exit("Erro ao buscar informações dos lançamentos.");
        $this->mysqli = NULL;
        $cor = '';
        while ($lancamento = $query->fetch_object()) {
            if ($lancamento->valor < 0) {
                $cor = 'red';
            } else {
                $cor = 'green';
            }
            $setor_transf = '';
            if ($lancamento->id_categoria == 3) { // transferencia
                $setor_transf = BuscaLTE::getSetorTransf($lancamento->id);
            }

            $btn = '';
            if ($_SESSION['id_setor'] == 2 && ($lancamento->id_categoria == 1 || $lancamento->id_categoria == 2)) {
                $btn = "<button type=\"button\" data-toggle=\"tooltip\" title=\"Desfazer\" onclick=\"undoFreeMoney(".$lancamento->id.")\" class=\"btn btn-default\"><i class=\"fa fa-undo\"></i></button>";
            }
            $lancamento->valor = number_format($lancamento->valor, 3, ',', '.');
            $retorno .= "
                <tr>
                    <td>" . $btn . "</td>
                    <td>" . $lancamento->nome . "</td>
                    <td>" . $lancamento->data . "</td>
                    <td style=\"color: " . $cor . ";\">R$ " . $lancamento->valor . "</td>
                    <td>" . $lancamento->categoria . "</td>
                    <td>" . $setor_transf . "</td>
                </tr>";
        }
        return $retorno;
    }

}
