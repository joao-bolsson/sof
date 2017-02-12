<?php

/**
 *  Classe com as funções de busca utilizadas principalmente pelo arquivo php/busca.php
 *  qualquer função que RETORNE dados do banco, devem ser feitas nesta classe
 *
 *  @author João Bolsson (joaovictorbolsson@gmail.com).
 *  @since 2016, 16 Mar.
 */
ini_set('display_erros', true);
error_reporting(E_ALL);

include_once 'Conexao.class.php';
include_once 'Util.class.php';

class Busca extends Conexao {

    private $mysqli, $obj_Util;

    function __construct() {
        //chama o método contrutor da classe Conexao
        parent::__construct();
        $this->obj_Util = new Util();
    }

    private function openConnection() {
        if (is_null($this->mysqli)) {
            $this->mysqli = parent::getConexao();
        }
    }

    /**
     * É importante que tabela selecionada tenha id e nome como colunas.
     * @param string $table tabel mysql
     * @return array array com a coluna nome da table
     */
    public function getArrayDefines(string $table): array {
        Busca::openConnection();
        $query = $this->mysqli->query('SELECT * FROM ' . $table) or exit('Erro ao buscar os status cadastrados: ');

        $array = [NULL];
        while ($obj = $query->fetch_object()) {
            $array[$obj->id] = $obj->nome;
        }

        $this->mysqli = NULL;

        return $array;
    }

    public function testValores() {
        Busca::openConnection();

        $query_ped = $this->mysqli->query("SELECT pedido.id, pedido.id_setor, pedido.valor, saldo_setor.saldo FROM pedido, saldo_setor WHERE saldo_setor.id_setor = pedido.id_setor;");
        while ($pedido = $query_ped->fetch_object()) {
            $query = $this->mysqli->query("SELECT sum(valor) AS soma FROM itens_pedido WHERE itens_pedido.id_pedido = " . $pedido->id . ";");
            $obj = $query->fetch_object();
            $novo_saldo = $pedido->saldo;
            if ($obj->soma != $pedido->valor) {
                if ($obj->soma == NULL) {
                    echo "sem itens<br>";
                    $obj->soma = 0;
                    $novo_saldo += $pedido->valor;
                } else if ($obj->soma < $pedido->valor) {
                    echo "soma < valor<br>";
                    $dif = $pedido->valor - $obj->soma;
                    $novo_saldo += $dif;
                } else if ($obj->soma > $pedido->valor) {
                    echo "soma > valor<br>";
                    $dif = $obj->soma - $pedido->valor;
                    $novo_saldo -= $dif;
                }
                if ($obj->soma == 0 && $pedido->valor == 0) {
                    echo "soma zero, valor zero <br>";
                } else {
                    $this->mysqli->query("UPDATE pedido SET valor = {$obj->soma} WHERE id = {$pedido->id};");
                    echo "atualiza valor do pedido {$pedido->id} <br>";
                }
            }
            if ($novo_saldo != $pedido->saldo) {
                $this->mysqli->query("UPDATE saldo_setor SET saldo = {$novo_saldo} WHERE id_setor = {$pedido->id_setor};");
                echo "atualiza saldo do setor {$pedido->id_setor} <br>";
            }
        }
        $this->mysqli = NULL;
    }

    public function getInfoContrato(int $id_pedido) {
        Busca::openConnection();
        $query = $this->mysqli->query("SELECT pedido.pedido_contrato, pedido_contrato.id_tipo, pedido_contrato.siafi FROM pedido, pedido_contrato WHERE pedido.id = pedido_contrato.id_pedido AND pedido.id = {$id_pedido};") or exit("Erro ao buscar informações do contrato.");
        $this->mysqli = NULL;
        if ($query->num_rows < 1) {
            return false;
        }
        $obj = $query->fetch_object();
        return json_encode($obj);
    }

    public function getGrupo(int $id_pedido) {
        Busca::openConnection();
        $query = $this->mysqli->query("SELECT id_grupo FROM pedido_grupo WHERE id_pedido = " . $id_pedido);
        $this->mysqli = NULL;
        if ($query->num_rows < 1) {
            return false;
        }
        $obj = $query->fetch_object();
        return $obj->id_grupo;
    }

    /**
     * @return bool Se o sistema está ativo - true, senão - false.
     */
    public function isActive(): bool {
        Busca::openConnection();
        $query = $this->mysqli->query("SELECT ativo FROM sistema LIMIT 1;") or exit("Ocorreu um erro ao tentar verificar a disponibilidade do sistema. Contate o administrador.");
        $obj = $query->fetch_object();
        $this->mysqli = NULL;
        return $obj->ativo;
    }

    /**
     * Função para retornar uma string para mostrar o total dos pedidos com determinado status.
     * @param int $status status dos pedidos para somar.
     * @return string String "Totalizando R$ x".
     */
    public function getTotalByStatus(int $status): string {
        Busca::openConnection();
        $query = $this->mysqli->query("SELECT sum(valor) AS total FROM pedido WHERE status = " . $status) or exit("Erro ao buscar o total pelo status.");
        $this->mysqli = NULL;
        $tot = $query->fetch_object();
        $tot->total = number_format($tot->total, 3, ',', '.');
        return "Totalizando R$ " . $tot->total;
    }

    /**
     * 	Função para retornar os processos que estão nos pedidos com suas datas de vencimento
     *
     * 	@param $pedido Id do pedido.
     * 	@return Uma tabela com os processos e as informações dele.
     */
    public function getProcessosPedido(int $pedido): string {
        Busca::openConnection();
        $query = $this->mysqli->query("SELECT DISTINCT itens.num_processo, itens.dt_fim FROM itens, itens_pedido WHERE itens_pedido.id_pedido = " . $pedido . " AND itens_pedido.id_item = itens.id;") or exit("Erro ao buscar os processos do pedido.");
        $this->mysqli = NULL;
        $retorno = "";
        while ($processo = $query->fetch_object()) {
            $retorno .= "
                <tr>
                    <td>" . $processo->num_processo . "</td>
                    <td>" . $processo->dt_fim . "</td>
                </tr>";
        }
        return $retorno;
    }

    /**
     * 	Função para retornar os problemas relatados
     *
     * 	@return string Linhas para uma tabela mostrar os problemas.
     */
    public function getProblemas(): string {
        Busca::openConnection();
        $query = $this->mysqli->query("SELECT setores.nome AS setor, problemas.assunto, problemas.descricao FROM setores, problemas WHERE setores.id = problemas.id_setor ORDER BY problemas.id DESC;") or exit("Erro ao buscar os problemas.");
        $retorno = "";
        while ($problema = $query->fetch_object()) {
            $problema->descricao = $this->mysqli->real_escape_string($problema->descricao);
            $problema->descricao = str_replace("\"", "'", $problema->descricao);
            $retorno .= "
                <tr>
                    <td>" . $problema->setor . "</td>
                    <td>" . $problema->assunto . "</td>
                    <td>
                        <button onclick=\"viewCompl('" . $problema->descricao . "');\" class=\"btn btn-flat waves-attach waves-effect\" type=\"button\" title=\"Descrição\">Descrição</button>
                    </td>
                </tr>";
        }
        $this->mysqli = NULL;
        return $retorno;
    }

    /**
     * 	Função que que retorna informações de um item para possível edição
     *
     * 	@param Id do item da tbela itens
     * 	@return object
     */
    public function getInfoItem($id_item) {
        Busca::openConnection();
        $query = $this->mysqli->query("SELECT cod_despesa, cod_reduzido, dt_fim, complemento_item, replace(vl_unitario, ',', '.') AS vl_unitario, qt_contrato, replace(vl_contrato, ',', '.') AS vl_contrato, qt_utilizado, replace(vl_utilizado, ',', '.') AS vl_utilizado, qt_saldo, replace(vl_saldo, ',', '.') AS vl_saldo, seq_item_processo FROM itens WHERE id = " . $id_item) or exit("Erro ao buscar informações do item.");
        $this->mysqli = NULL;
        $obj = $query->fetch_object();
        return json_encode($obj);
    }

    /**
     * 	Função que retornar o empenho de um pedido
     *
     * 	@param $id_pedido Id do pedido.
     * 	@return string
     */
    public function verEmpenho(int $id_pedido): string {
        $retorno = '';
        Busca::openConnection();
        $query = $this->mysqli->query("SELECT empenho FROM pedido_empenho WHERE id_pedido = " . $id_pedido) or exit("Erro so ver empenho.");
        $this->mysqli = NULL;
        if ($query->num_rows < 1) {
            $retorno = 'EMPENHO SIAFI PENDENTE';
        } else {
            $obj = $query->fetch_object();
            $retorno = $obj->empenho;
        }
        return $retorno;
    }

    public function getTotalInOutSaldos(int $id_setor): string {
        $where = "";
        if ($id_setor != 0) {
            $where = "AND id_setor = " . $id_setor;
        }
        Busca::openConnection();
        $query_in = $this->mysqli->query("SELECT sum(valor) soma FROM saldos_lancamentos WHERE valor > 0 " . $where) or exit("Erro ao buscar informações dos totais de entrada e saída do setor.");
        $query_out = $this->mysqli->query("SELECT sum(valor) soma FROM saldos_lancamentos WHERE valor < 0 " . $where) or exit("Erro ao buscar informações dos totais de entrada e saída do setor.");
        $this->mysqli = NULL;

        $obj_in = $query_in->fetch_object();
        $obj_out = $query_out->fetch_object();

        $array = array('entrada' => "Total de Entradas: R$ " . number_format($obj_in->soma, 3, ',', '.'),
            'saida' => "Total de Saídas: R$ " . number_format($obj_out->soma, 3, ',', '.'));

        return json_encode($array);
    }

    /**
     * Retorna o nome do setor.
     * @param int $id_setor id do setor.
     */
    public function getSetorNome(int $id_setor): string {
        Busca::openConnection();
        $query = $this->mysqli->query("SELECT nome FROM setores WHERE id = " . $id_setor) or exit("Erro ao buscar o nome do setor.");
        $this->mysqli = NULL;
        $obj = $query->fetch_object();
        return $obj->nome;
    }

    /**
     *   Função utilizada para retornar as informações de um processo clicado da tabela da recepção.
     *
     *   @return Informações do processo.
     */
    public function getInfoProcesso(int $id_processo): string {
        Busca::openConnection();
        $query = $this->mysqli->query("SELECT num_processo, tipo, estante, prateleira, entrada, saida, responsavel, retorno, obs FROM processos WHERE id = " . $id_processo) or exit("Erro ao buscar informações dos processos.");
        $this->mysqli = NULL;
        $obj = $query->fetch_object();
        return json_encode($obj);
    }

    /**
     * 	Função que retorna as permissões do usuário.
     *
     * 	@return JSON com as permissões do usuário no sistema.
     */
    public function getPermissoes(int $id_user) {
        Busca::openConnection();
        $query = $this->mysqli->query("SELECT noticias, saldos, pedidos, recepcao FROM usuario_permissoes WHERE id_usuario = " . $id_user) or exit("Erro ao buscar permissões do usuário.");
        $this->mysqli = NULL;
        $obj_permissoes = $query->fetch_object();
        return $obj_permissoes;
    }

    /**
     * Função que busca os detalhes de uma notícia completa.
     *
     * @return Informação da postagem.
     */
    public function getInfoNoticia(int $id): string {
        Busca::openConnection();
        $query = $this->mysqli->query("SELECT postagem FROM postagens WHERE id = " . $id) or exit("Erro ao buscar informações da notícia.");
        $this->mysqli = NULL;
        $noticia = $query->fetch_object();
        return html_entity_decode($noticia->postagem);
    }

    /**
     * Função para mostrar uma tabela com todas as publicações de certa página
     *
     * @param $tabela -> filtra por nome da tabela
     * @return string
     */
    public function getPostagens(string $tabela): string {
        //declarando retorno
        $retorno = "";
        Busca::openConnection();
        $query = $this->mysqli->query("SELECT postagens.id, postagens.titulo, DATE_FORMAT(postagens.data, '%d/%m/%Y') AS data FROM postagens, paginas_post WHERE postagens.tabela = paginas_post.id AND paginas_post.tabela = '{$tabela}' AND ativa = 1 ORDER BY data ASC;") or exit("Erro ao buscar postagens.");
        $this->mysqli = NULL;
        while ($postagem = $query->fetch_object()) {
            $retorno .= "<tr><td>";
            $retorno .= html_entity_decode($postagem->titulo);

            $retorno .= "<button class=\"btn btn-flat btn-sm\" style=\"text-transform: lowercase !important;font-weight: bold;\" onclick=\"ver_noticia(" . $postagem->id . ", '" . $tabela . "', 0);\">...ver mais</button></td>";
            $retorno .= "<td><span style=\"font-weight: bold;\" class=\"pull-right\">" . $postagem->data . "</span></td></tr>";
        }
        return $retorno;
    }

    /**
     * Função para popular os slides na página inicial
     *
     * @param $slide (1 ou 2)-> o primeiro mostra as últimas notícias e o segundo aleatórias
     * @return string
     */
    public function getSlide(int $slide): string {
        $order = "";
        if ($slide == 1) {
            $order = "postagens.data DESC";
        } else {
            $order = "rand()";
        }
        $retorno = "";
        $array_anima = array("primeira", "segunda", "terceira", "quarta", "quinta");
        $array_id = array("primeiro", "segundo", "terceiro", "quarto", "quinto");
        Busca::openConnection();
        $query_postagem = $this->mysqli->query("SELECT postagens.id, postagens.postagem, DATE_FORMAT(postagens.data, '%d/%m/%Y') AS data, paginas_post.tabela, postagens.titulo FROM postagens, paginas_post WHERE postagens.tabela = paginas_post.id AND postagens.ativa = 1 ORDER BY {$order} LIMIT 5;") or exit("Erro ao buscar notícias dos slides.");
        $this->mysqli = NULL;
        $aux = 0;
        while ($postagem = $query_postagem->fetch_object()) {
            $array_post = str_split($postagem->titulo);
            $pos = strlen($postagem->titulo);
            $titulo = "";
            for ($i = 0; $i < $pos; $i++) {
                $titulo .= $array_post[$i];
            }
            $array_post = str_split($postagem->postagem);
            $pos = strpos($postagem->postagem, "<img");
            $src = "../sof_files/logo_blue.png";
            if ($pos !== false) {
                $pos = strpos($postagem->postagem, "src=\"");
                $src = "";
                $i = $pos + 5;
                while ($array_post[$i] != "\"") {
                    $src .= $array_post[$i];
                    $i++;
                }
            }
            $width = "550";

            $pos = strpos($postagem->postagem, "width: ");
            $posu = strpos($postagem->postagem, "px;");
            if ($postagem->tabela != "noticia" || $postagem->id != 8) {
                if ($pos !== false) {
                    if ($posu !== false) {
                        for ($i = $pos; $i < $posu; $i++) {
                            $width .= $array_post[$i];
                        }
                    }
                }
            }
            $retorno .= "
                <li id=\"" . $array_id[$aux] . "\" class=\"" . $array_anima[$aux] . "-anima\">
                    <div class=\"card-img\">
                        <img style=\"width: " . $width . "px; height: 275px;\" src=\"" . $src . "\" >
                        <a href=\"javascript:ver_noticia(" . $postagem->id . ", '" . $postagem->tabela . "', 1);\" class=\"card-img-heading padding\" style=\"font-weight: bold;\">" . $titulo . "<span class=\"pull-right\">" . $postagem->data . "</span></a>
                    </div>
                </li>";
            $aux++;
        }
        return $retorno;
    }

    /**
     * Função para pesquisar alguma publicação.
     *
     * @return Publicações com o título parecido com $busca.
     */
    public function pesquisar(string $busca): string {
        $retorno = "";
        $busca = htmlentities($busca);
        //escapando string especiais para evitar SQL Injections
        Busca::openConnection();
        $busca = $this->mysqli->real_escape_string($busca);
        $retorno = "
            <div class=\"card\">
                <div class=\"card-main\">
                    <div class=\"card-header card-brand\">
                        <div class=\"card-header-side pull-left\">
                            <p class=\"card-heading\">Publicações</p>
                        </div>
                    </div><!--  ./card-header -->
                    <div class=\"card-inner margin-top-no\">
                        <div class=\"card-table\">
                            <div class=\"table-responsive\">
                                <table class=\"table\">
                                    <thead>
                                        <th>Título</th>
                                        <th class=\"pull-right\">Data de Publicação</th>
                                    </thead>
                                    <tbody>";
        $query = $this->mysqli->query("SELECT id, tabela, titulo, DATE_FORMAT(data, '%d/%m/%Y') AS data, ativa FROM postagens WHERE titulo LIKE '%{$busca}%' AND ativa = 1 ORDER BY data DESC;") or exit("Erro ao pesquisar notícias.");
        $this->mysqli = NULL;
        if ($query->num_rows < 1) {
            $retorno .= "
                                        <tr>
                                            <td colspan=\"2\">Nenhum resultado para '" . $busca . "'</td>
                                            <td></td>
                                        </tr>";
        } else {
            while ($postagem = $query->fetch_object()) {
                $titulo = html_entity_decode($postagem->titulo);
                $retorno .= "
                                        <tr>
                                            <td>" . $titulo . "<button class=\"btn btn-flat btn-sm\" style=\"text-transform: lowercase !important;font-weight: bold;\" onclick=\"ver_noticia(" . $postagem->id . ", '" . $postagem->tabela . "', 1);\">...ver mais</button></td>
                                            <td><span class=\"pull-right\">" . $postagem->data . "</span></td>
                                        </tr>";
            }
        }
        $retorno .= "
                                    </tbody>
                                </table>
                            </div><!-- ./table-responsive -->
                        </div><!-- ./card-table -->
                    </div><!-- ./card-inner -->
                </div><!-- ./card-main -->
            </div> <!-- ./card -->";
        return $retorno;
    }

    /**
     * 	Função que retorna as 'tabs' com as ṕáginas das notícias para editar.
     *
     * 	@return string
     */
    public function getTabsNoticias(): string {
        $retorno = "";
        Busca::openConnection();
        $query = $this->mysqli->query("SELECT id, tabela, nome FROM paginas_post") or exit("Erro ao buscar as abas de notícias para edição.");
        $this->mysqli = NULL;
        while ($pag = $query->fetch_object()) {
            $retorno .= "
                <td>
                    <div class=\"radiobtn radiobtn-adv\">
                        <label for=\"pag-" . $pag->tabela . "\">
                            <input type=\"radio\" id=\"pag-" . $pag->tabela . "\" name=\"pag\" class=\"access-hide\" onclick=\"carregaPostsPag(" . $pag->id . ");\">" . $pag->nome . "
                            <span class=\"radiobtn-circle\"></span><span class=\"radiobtn-circle-check\"></span>
                        </label>
                    </div>
                </td>";
        }
        return $retorno;
    }

    /**
     * 	Função para retornar a tabela de notícias de uma página para edição
     *
     * 	@return string
     */
    public function getNoticiasEditar(int $tabela): string {
        $retorno = "";
        Busca::openConnection();
        $query = $this->mysqli->query("SELECT id, tabela, titulo, DATE_FORMAT(data, '%d/%m/%Y') AS data FROM postagens WHERE ativa = 1 AND tabela = " . $tabela . " ORDER BY data ASC;") or exit("Erro ao buscar as notícias para editar.");
        $this->mysqli = NULL;
        while ($postagem = $query->fetch_object()) {
            $retorno .= "
                <tr>
                    <td>" . $postagem->data . "</td>
                    <td>" . $postagem->titulo . "</td>
                    <td>
                        <button class=\"btn btn-default btn-sm\" style=\"text-transform: none !important;font-weight: bold;\" onclick=\"editaNoticia(" . $postagem->id . ", " . $postagem->tabela . ", '" . $postagem->data . "')\" title=\"Editar\"><span class=\"icon\">create</span></button>
                        <button class=\"btn btn-default btn-sm\" style=\"text-transform: none !important;font-weight: bold;\" onclick=\"excluirNoticia(" . $postagem->id . ");\" title=\"Excluir\"><span class=\"icon\">delete</span></button>
                    </td>
                </tr>";
        }
        return $retorno;
    }

    /**
     * Função para buscar conteúdo de uma publicação para edição.
     *
     * @return string
     */
    public function getPublicacaoEditar(int $id): string {
        Busca::openConnection();
        $query = $this->mysqli->query("SELECT postagem FROM postagens WHERE id = " . $id) or exit("Erro ao buscar postagem.");
        $this->mysqli = NULL;
        $publicacao = $query->fetch_object();
        return $publicacao->postagem;
    }

    /**
     * Função para escrever as opções para "Postar em " do painel administrativo
     *
     * @return string
     */
    public function getPostarEm(): string {
        $retorno = "";
        Busca::openConnection();
        $query = $this->mysqli->query("SELECT id, nome FROM paginas_post;") or exit("Erro ao buscar as páginas para postagem.");
        $this->mysqli = NULL;
        while ($pagina = $query->fetch_object()) {
            $retorno .= "<option id=\"op" . $pagina->id . "\" value=\"" . $pagina->id . "\">" . $pagina->nome . "</option>";
        }
        return $retorno;
    }

    /**
     * Função para trazer o restante das informações para analisar o pedido:
     *               saldo, total, prioridade, fase, etc.
     *   
     * @return string
     */
    public function getInfoPedidoAnalise(int $id_pedido, int $id_setor): string {
        Busca::openConnection();
        $query = $this->mysqli->query("SELECT saldo_setor.saldo, pedido.prioridade, pedido.status, pedido.valor, pedido.obs FROM saldo_setor, pedido WHERE saldo_setor.id_setor = {$id_setor} AND pedido.id = {$id_pedido};") or exit("Erro ao buscar as informações do pedido para análise.");
        $this->mysqli = NULL;
        $pedido = $query->fetch_object();
        return json_encode($pedido);
    }

    /**
     * 	Função que retorna o saldo dispónível do setor.
     *
     * 	@return string
     */
    public function getSaldo(int $id_setor): string {
        Busca::openConnection();
        $query = $this->mysqli->query("SELECT saldo FROM saldo_setor WHERE id_setor = " . $id_setor) or exit("Erro ao buscar o saldo do setor.");
        if ($query->num_rows < 1) {
            $this->mysqli->query("INSERT INTO saldo_setor VALUES(NULL, " . $id_setor . ", '0.000');") or exit("Erro ao inserir o saldo do setor.");
            $this->mysqli = NULL;
            return '0.000';
        }
        $this->mysqli = NULL;
        $obj = $query->fetch_object();
        $saldo = number_format($obj->saldo, 3, '.', '');
        return $saldo;
    }

    /**
     * Função dispara logo após clicar em editar rascunho de pedido.
     *
     * @return string
     */
    public function getPopulaRascunho(int $id_pedido, int $id_setor): string {
        Busca::openConnection();
        $query = $this->mysqli->query("SELECT saldo_setor.saldo, pedido.valor, pedido.obs FROM saldo_setor, pedido WHERE pedido.id = " . $id_pedido . " AND saldo_setor.id_setor = " . $id_setor) or exit("Erro ao buscar informações do rascunho.");
        $this->mysqli = NULL;
        $pedido = $query->fetch_object();
        return json_encode($pedido);
    }

    public function getLicitacao(int $id_pedido) {
        Busca::openConnection();
        $query = $this->mysqli->query("SELECT id, tipo, numero, uasg, processo_original, gera_contrato FROM licitacao WHERE id_pedido = " . $id_pedido) or exit("Erro ao buscar as licitações do pedido.");
        $this->mysqli = NULL;
        $retorno = false;
        if ($query->num_rows > 0) {
            $obj = $query->fetch_object();
            $retorno = json_encode($obj);
        }

        return $retorno;
    }

}
