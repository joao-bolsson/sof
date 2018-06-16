<?php

/**
 * Class with the functions used, principally, by file php/busca.php. Any function that RETURNS data from the database, must be in this class.
 *
 * @author João Bolsson (joaovictorbolsson@gmail.com).
 * @since 2016, 16 Mar.
 */
ini_set('display_errors', true);
error_reporting(E_ALL);

spl_autoload_register(function (string $class_name) {
    include_once $class_name . '.class.php';
});

final class Busca {

    /**
     * Default constructor.
     */
    private function __construct() {
        // empty
    }

    public static function fillContracts(): string {
        $query = Query::getInstance()->exe("SELECT id, numero FROM contrato;");

        $opts = "";
        while ($obj = $query->fetch_object()) {
            $opts .= "<option value=\"{$obj->id}\">{$obj->numero}</option>";
        }
        return $opts;
    }

    public static function getOptionsSectorHasSources() {
        $query = Query::getInstance()->exe("SELECT DISTINCT saldo_fonte.id_setor, setores.nome FROM saldo_fonte, setores WHERE setores.id = saldo_fonte.id_setor");
        if ($query->num_rows > 0) {
            $options = "";

            while ($obj = $query->fetch_object()) {
                $options .= "<option value=\"" . $obj->id_setor . "\">" . $obj->nome . "</option>";
            }
            return $options;
        }
        return "";
    }

    public static function getSourcesForSector(int $id_setor): string {
        $query = Query::getInstance()->exe("SELECT DISTINCT fonte_recurso FROM pedido_fonte, pedido WHERE pedido_fonte.id_pedido = pedido.id AND pedido.id_setor = " . $id_setor . " LIMIT 300;");
        $options = "<option value=\"0\">Ignorar Fontes</option>";
        if ($query->num_rows > 0) {

            while ($obj = $query->fetch_object()) {
                $options .= "<option value=\"" . $obj->fonte_recurso . "\">" . $obj->fonte_recurso . "</option>";
            }
        }
        return $options;
    }

    public static function getSources(int $id): string {
        $query = Query::getInstance()->exe("SELECT id, valor, fonte_recurso, ptres, plano_interno FROM saldo_fonte WHERE id_setor = " . $id);
        $opt = "";

        while ($obj = $query->fetch_object()) {
            $opt .= "<option value=\"" . $obj->id . "\">" . $obj->fonte_recurso . "</option>";
        }

        return $opt;
    }

    public static function scanDataBase() {
        $query = Query::getInstance()->exe("SELECT id, valor FROM pedido;");

        while ($obj = $query->fetch_object()) {
            $ped = Query::getInstance()->exe("SELECT sum(valor) AS soma FROM itens_pedido WHERE id_pedido = " . $obj->id);
            $obj_ped = $ped->fetch_object();
            $total = number_format($obj->valor, 3, '.', '');
            $sum = number_format($obj_ped->soma, 3, '.', '');
            if ($total != $sum) {
                echo "Pedido " . $obj->id . ": " . $total . " -> " . $sum . "<br>";
            }
        }
    }

    public static function getInfoLog(int $id) {
        $query = Query::getInstance()->exe("SELECT usuario.nome, DATE_FORMAT(entrada, '%d/%m/%Y %H:%i:%s') AS entrada, DATE_FORMAT(saida, '%d/%m/%Y %H:%i:%s') AS saida FROM usuario_hora, usuario WHERE usuario_hora.id_usuario = usuario.id AND usuario_hora.id = " . $id);

        $obj = $query->fetch_object();
        $obj->saida = ($obj->saida == NULL) ? date('d/m/Y H:i:s') : $obj->saida;
        return json_encode($obj);
    }

    /**
     * Gets the id of last register of the user specified by param
     * if return NULL, means the user doesn't have a register.
     * @param int $id Id of user.
     * @return int int Id of last register in usuario_hora.
     */
    public static function getLastRegister(int $id = 0) {
        $id_user = ($id == 0) ? $_SESSION['id'] : $id;
        $query_id = Query::getInstance()->exe('SELECT max(id) AS id FROM usuario_hora WHERE id_usuario = ' . $id_user);

        $obj = $query_id->fetch_object();

        return $obj->id;
    }

    /**
     * Verify if user must make an in or out.
     *
     * @param int $id_user User id.
     * @return int If 1 - in, else - out.
     */
    public static function getInfoTime(int $id_user = 0): int {
        $id_last = self::getLastRegister($id_user);

        $return = NULL;

        if ($id_last == NULL) {
            // make a log in
            $return = 1;
        } else {
            // verify if the user must make a login or log out
            $query = Query::getInstance()->exe('SELECT saida FROM usuario_hora WHERE id = ' . $id_last);

            $obj = $query->fetch_object();

            if ($obj->saida == NULL) {
                // log out
                $return = 0;
            } else {
                // login
                $return = 1;
            }
        }

        if ($return === NULL) {
            exit('Error: return is NULL (getInfoTime)');
        }
        return $return;
    }

    public static function getInfoPlano(int $id_request): string {
        $query = Query::getInstance()->exe("SELECT plano FROM pedido_plano WHERE id_pedido = " . $id_request);
        if ($query->num_rows > 0) {
            return $query->fetch_object()->plano;
        }
        return "";
    }

    public static function getInfoContrato(int $id_request) {
        $query = Query::getInstance()->exe('SELECT pedido.pedido_contrato, pedido_contrato.id_tipo, pedido_contrato.siafi FROM pedido, pedido_contrato WHERE pedido.id = pedido_contrato.id_pedido AND pedido.id = ' . $id_request);
        if ($query->num_rows < 1) {
            return false;
        }
        $obj = $query->fetch_object();
        return json_encode($obj);
    }

    public static function getGrupo(int $id_request) {
        $query = Query::getInstance()->exe('SELECT id_grupo FROM pedido_grupo WHERE id_pedido = ' . $id_request);
        if ($query->num_rows < 1) {
            return false;
        }
        $obj = $query->fetch_object();
        return $obj->id_grupo;
    }

    /**
     * @return bool If system is active - true, else - false.
     */
    public static function isActive(): bool {
        $query = Query::getInstance()->exe('SELECT ativo FROM sistema LIMIT 1');
        $obj = $query->fetch_object();
        return $obj->ativo;
    }

    /**
     * Function to return the reported problems.
     *
     * @return string Rows to put in a table that shows the problems.
     */
    public static function getProblemas(): string {
        $query = Query::getInstance()->exe('SELECT setores.nome AS setor, problemas.assunto, problemas.descricao FROM setores, problemas WHERE setores.id = problemas.id_setor ORDER BY problemas.id DESC');
        $table = new Table('', '', [], false);

        while ($problem = $query->fetch_object()) {
            $problem->descricao = Query::getInstance()->real_escape_string($problem->descricao);
            $problem->descricao = str_replace("\"", "'", $problem->descricao);

            $btn = "<button onclick=\"viewCompl('" . $problem->descricao . "');\" class=\"btn btn-default\" type=\"button\" data-toggle=\"tooltip\" title=\"Ver Descrição Informada\">Descrição</button>";

            $row = new Row();
            $row->addComponent(new Column($problem->setor));
            $row->addComponent(new Column($problem->assunto));
            $row->addComponent(new Column($btn));

            $table->addComponent($row);
        }
        return $table;
    }

    /**
     * Function to return the request effort.
     *
     * @param int $id_request Request id.
     * @return string
     */
    public static function verEmpenho(int $id_request): string {
        $query = Query::getInstance()->exe('SELECT empenho FROM pedido_empenho WHERE id_pedido = ' . $id_request);
        if ($query->num_rows < 1) {
            return 'EMPENHO SIAFI PENDENTE';
        } else {
            $obj = $query->fetch_object();
            return $obj->empenho;
        }
    }

    public static function getTotalInOutSaldos(int $sector): string {
        $where = ($sector != 0) ? 'AND id_setor = ' . $sector : '';
        $query_in = Query::getInstance()->exe('SELECT sum(valor) soma FROM saldos_lancamentos WHERE valor > 0 ' . $where);
        $query_out = Query::getInstance()->exe('SELECT sum(valor) soma FROM saldos_lancamentos WHERE valor < 0 ' . $where);

        $obj_in = $query_in->fetch_object();
        $obj_out = $query_out->fetch_object();

        $array = array('entrada' => "Total de Entradas: R$ " . number_format($obj_in->soma, 3, ',', '.'), 'saida' => "Total de Saídas: R$ " . number_format($obj_out->soma, 3, ',', '.'));

        return json_encode($array);
    }

    /**
     * @param int $id_sector Sector id.
     * @return string Sector name.
     */
    public static function getSetorNome(int $id_sector): string {
        $query = Query::getInstance()->exe('SELECT nome FROM setores WHERE id = ' . $id_sector);
        $obj = $query->fetch_object();
        return $obj->nome;
    }

    /**
     * Function used to returns the informations of a process clicked in the reception's table.
     *
     * @param int $id_process Process's id.
     * @return string Process's informations.
     */
    public static function getInfoProcesso(int $id_process): string {
        $query = Query::getInstance()->exe('SELECT num_processo, tipo, estante, prateleira, entrada, saida, responsavel, retorno, obs, vigencia FROM processos WHERE id = ' . $id_process);
        $obj = $query->fetch_object();
        return json_encode($obj);
    }

    /**
     * Function that returns the user's permissions.
     *
     * @param int $id_user User's id.
     * @return object Object with user's permissions in system.
     */
    public static function getPermissoes(int $id_user) {
        $query = Query::getInstance()->exe('SELECT noticias, saldos, pedidos, recepcao FROM usuario_permissoes WHERE id_usuario = ' . $id_user);
        $obj_permissions = $query->fetch_object();
        return $obj_permissions;
    }

    /**
     * Function that returns the news details.
     *
     * @param int $id News id.
     * @return string News information.
     */
    public static function getInfoNoticia(int $id): string {
        $query = Query::getInstance()->exe('SELECT postagem FROM postagens WHERE id = ' . $id);
        $news = $query->fetch_object();
        return html_entity_decode($news->postagem);
    }

    /**
     * Function to show a table with all posts of a certain page.
     *
     * @param string $table Filter by table's name.
     * @return string
     */
    public static function getPostagens(string $table): string {
        $return = '';
        $query = Query::getInstance()->exe("SELECT postagens.id, postagens.titulo, DATE_FORMAT(postagens.data, '%d/%m/%Y') AS data FROM postagens, paginas_post WHERE postagens.tabela = paginas_post.id AND paginas_post.tabela = '{$table}' AND ativa = 1 ORDER BY data ASC");
        while ($post = $query->fetch_object()) {
            $return .= '<tr><td>';
            $return .= html_entity_decode($post->titulo);

            $return .= "<button class=\"btn btn-flat btn-sm\" style=\"text-transform: lowercase !important;font-weight: bold;\" onclick=\"ver_noticia(" . $post->id . ", '" . $table . "', 0);\">...ver mais</button></td>";
            $return .= "<td><span style=\"font-weight: bold;\" class=\"pull-right\">" . $post->data . "</span></td></tr>";
        }
        return $return;
    }

    /**
     * Função para popular os slides na página inicial
     *
     * @param int $slide (1 ou 2)-> o primeiro mostra as últimas notícias e o segundo aleatórias
     * @return string
     */
    public static function getSlide(int $slide): string {
        $order = ($slide == 1) ? 'postagens.data DESC' : 'rand()';
        $return = '';
        $array_anima = array("primeira", "segunda", "terceira", "quarta", "quinta");
        $array_id = array("primeiro", "segundo", "terceiro", "quarto", "quinto");
        $query_post = Query::getInstance()->exe("SELECT postagens.id, postagens.postagem, DATE_FORMAT(postagens.data, '%d/%m/%Y') AS data, paginas_post.tabela, postagens.titulo FROM postagens, paginas_post WHERE postagens.tabela = paginas_post.id AND postagens.ativa = 1 ORDER BY {$order} LIMIT 5");
        $aux = 0;
        while ($post = $query_post->fetch_object()) {
            $array_post = str_split($post->titulo);
            $pos = strlen($post->titulo);
            $title = "";
            for ($i = 0; $i < $pos; $i++) {
                $title .= $array_post[$i];
            }
            $array_post = str_split($post->postagem);
            $pos = strpos($post->postagem, "<img");
            $src = "../sof_files/logo_blue.png";
            if ($pos !== false) {
                $pos = strpos($post->postagem, "src=\"");
                $src = "";
                $i = $pos + 5;
                while ($array_post[$i] != "\"") {
                    $src .= $array_post[$i];
                    $i++;
                }
            }
            $width = "550";

            $pos = strpos($post->postagem, "width: ");
            $posu = strpos($post->postagem, "px;");
            if ($post->tabela != "noticia" || $post->id != 8) {
                if ($pos !== false) {
                    if ($posu !== false) {
                        for ($i = $pos; $i < $posu; $i++) {
                            $width .= $array_post[$i];
                        }
                    }
                }
            }
            $return .= "
                <li id=\"" . $array_id[$aux] . "\" class=\"" . $array_anima[$aux] . "-anima\">
                    <div class=\"card-img\">
                        <img style=\"width: " . $width . "px; height: 275px;\" src=\"" . $src . "\" >
                        <a href=\"javascript:ver_noticia(" . $post->id . ", '" . $post->tabela . "', 1);\" class=\"card-img-heading padding\" style=\"font-weight: bold;\">" . $title . "<span class=\"pull-right\">" . $post->data . "</span></a>
                    </div>
                </li>";
            $aux++;
        }
        return $return;
    }

    /**
     * Function to search a news.
     *
     * @param string $busca Title to search.
     * @return string News with the title similar to $busca.
     */
    public static function pesquisar(string $busca): string {
        $busca = htmlentities($busca);
        $busca = Query::getInstance()->real_escape_string($busca);
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
        $query = Query::getInstance()->exe("SELECT id, tabela, titulo, DATE_FORMAT(data, '%d/%m/%Y') AS data, ativa FROM postagens WHERE titulo LIKE '%{$busca}%' AND ativa = 1 ORDER BY data DESC");
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
     * Function to search the content of a post to edition.
     *
     * @param int $id Post id.
     * @return string
     */
    public static function getPublicacaoEditar(int $id): string {
        $query = Query::getInstance()->exe('SELECT postagem FROM postagens WHERE id = ' . $id);
        $post = $query->fetch_object();
        return $post->postagem;
    }

    /**
     * Function to search others informations to analysis the request, like: balance, total, priority, etc.
     *
     * @param int $id_request Request id.
     * @param int $id_sector Sector id.
     * @return string
     */
    public static function getInfoPedidoAnalise(int $id_request, int $id_sector): string {
        $query = Query::getInstance()->exe('SELECT saldo_setor.saldo, pedido.prioridade, pedido.status, pedido.valor, pedido.obs FROM saldo_setor, pedido WHERE saldo_setor.id_setor = ' . $id_sector . ' AND pedido.id = ' . $id_request);
        $request = $query->fetch_object();
        return json_encode($request);
    }

    /**
     * Function that returns the sector available balance
     *
     * @param int $id_sector Sector id.
     * @return string
     */
    public static function getSaldo(int $id_sector): string {
        $query = Query::getInstance()->exe('SELECT saldo FROM saldo_setor WHERE id_setor = ' . $id_sector);
        if ($query->num_rows < 1) {
            Query::getInstance()->exe("INSERT INTO saldo_setor VALUES(NULL, " . $id_sector . ", '0.000');");
            return '0.000';
        }
        $obj = $query->fetch_object();
        $balance = number_format($obj->saldo, 3, '.', '');
        return $balance;
    }

    /**
     * Function to search informations of a request after click to edit it.
     *
     * @param int $id_request Request id.
     * @param int $id_sector Sector id.
     * @return string
     */
    public static function getPopulaRascunho(int $id_request, int $id_sector): string {
        $query = Query::getInstance()->exe('SELECT saldo_setor.saldo, pedido.valor, pedido.obs FROM saldo_setor, pedido WHERE pedido.id = ' . $id_request . ' AND saldo_setor.id_setor = ' . $id_sector);
        $request = $query->fetch_object();
        return json_encode($request);
    }

    public static function getLicitacao(int $id_request) {
        $query = Query::getInstance()->exe('SELECT id, tipo, numero, uasg, processo_original, gera_contrato FROM licitacao WHERE id_pedido = ' . $id_request);
        $return = false;
        if ($query->num_rows > 0) {
            $obj = $query->fetch_object();
            $return = json_encode($obj);
        }

        return $return;
    }

}
