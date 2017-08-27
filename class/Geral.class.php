<?php

/**
 *  Classe com as funções de cadastrados utilizadas pelo arquivo php/geral.php
 *  toda função de ENTRADA de dados no banco devem ficar nesta classe
 *
 * @author João Bolsson (joaovictorbolsson@gmail.com).
 * @since 2016, 16 Mar.
 */
ini_set('display_errors', true);
error_reporting(E_ALL);

require_once '../defines.php';

spl_autoload_register(function (string $class_name) {
    include_once $class_name . '.class.php';
});

final class Geral {

    /**
     * Default constructor.
     */
    private function __construct() {
        // empty
    }

    public static function scanDataBase() {
        $query = Query::getInstance()->exe("SELECT id, round(valor, 3) AS valor FROM pedido;");

        while ($obj = $query->fetch_object()) {
            $ped = Query::getInstance()->exe("SELECT round(sum(valor), 3) AS soma FROM itens_pedido WHERE id_pedido = " . $obj->id);
            $obj_ped = $ped->fetch_object();
            $total = $obj->valor;
            $sum = $obj_ped->soma;
            if ($total != $sum) {
                echo "Corrige pedido: " . $obj->id . "\n";
                Query::getInstance()->exe("UPDATE pedido SET valor = '" . $sum . "' WHERE id = " . $obj->id);
            }
        }
    }

    public static function verifySectors() {
        $query = Query::getInstance()->exe("SELECT id FROM setores WHERE id > 1;");
        while ($obj = $query->fetch_object()) {
            $sector = new Sector($obj->id);
            $sector->updateMoney();
        }
    }

    /**
     * Update the requests value according its items values.
     * @param array $req Array with requests to be updated.
     */
    public static function updateRequests(array $req) {
        $len = count($req);
        for ($i = 0; $i < $len; $i++) {
            $request = $req[$i];
            $obj = Query::getInstance()->exe("SELECT round(sum(valor), 3) AS sum FROM itens_pedido WHERE id_pedido =  " . $request)->fetch_object();
            Query::getInstance()->exe("UPDATE pedido SET valor = '" . $obj->sum . "' WHERE id = " . $request);
        }
    }

    /**
     *
     * @param array $dados Informações que serão inseridas na tabela de itens.
     * @param array $campos Campos da coluna no banco que deverão ser abastecidos
     */
    public static function cadItensRP(array $dados, array $campos) {
        if (empty($dados)) {
            exit("Nenhum dado foi recebido para o cadastro");
        }
        $fields = $insert_dados = '(';

        $len = count($campos);
        for ($i = 0; $i < $len; $i++) {
            $fields .= $campos[$i];
            $aux = Query::getInstance()->real_escape_string($dados[$campos[$i]]);
            $info = str_replace("\"", "'", $aux);
            $insert_dados .= "\"" . $info . "\"";
            if ($i != $len - 1) {
                $fields .= ', ';
                $insert_dados .= ', ';
            }
        }
        $fields .= ')';
        $insert_dados .= ')';

        Query::getInstance()->exe("INSERT IGNORE INTO itens " . $fields . " VALUES " . $insert_dados);
    }

    public static function editItemFactory($dados) {
        if (empty($dados)) {
            exit('Factory data is empty');
        }

        $attach_fields = ['id_item_processo', 'id_item_contrato', 'descr_tipo_doc', 'num_contrato', 'num_processo', 'descr_mod_compra', 'num_licitacao', 'dt_inicio', 'dt_fim', 'dt_geracao', 'cgc_fornecedor', 'nome_fornecedor', 'nome_unidade', 'cod_estruturado', 'num_extrato', 'descricao', 'id_extrato_contr', 'id_unidade', 'ano_orcamento'];

        $sets = ", ";
        $len = count($attach_fields);
        for ($i = 0; $i < $len; $i++) {
            $aux = Query::getInstance()->real_escape_string($dados->{$attach_fields[$i]});
            $info = str_replace("\"", "'", $aux);
            $sets .= $attach_fields[$i] . "=\"" . $info . "\"";
            if ($i != $len - 1) {
                $sets .= ", ";
            }
        }

        Query::getInstance()->exe("UPDATE itens SET cod_despesa = '" . $dados->cod_despesa . "', descr_despesa = '" . $dados->descr_despesa . "', cod_reduzido = '" . $dados->cod_reduzido . "', seq_item_processo = '" . $dados->seq_item_processo . "' {$sets} WHERE id = " . $dados->id . " LIMIT 1;");
    }

    /**
     * Desfaz uma liberação orçamentária. Suporta apenas do tipo 'normal' até a v2.1.4.
     * @param int $id_lancamento Id do lançamento.
     */
    public static function undoFreeMoney(int $id_lancamento) {
        // seleciona os dados da liberação
        $query = Query::getInstance()->exe("SELECT saldos_lancamentos.id_setor, saldos_lancamentos.data, saldos_lancamentos.valor, saldos_lancamentos.categoria, saldo_setor.saldo FROM saldos_lancamentos, saldo_setor WHERE saldo_setor.id_setor = saldos_lancamentos.id_setor AND saldos_lancamentos.id = " . $id_lancamento);

        $obj = $query->fetch_object();

        if ($obj->categoria == 4) {
            return;
        }
        $novo_saldo = $obj->saldo;
        if ($obj->categoria == 3) { // transferencia
            self::undoTransf($id_lancamento);
            return;
        } else {
            $novo_saldo -= $obj->valor;
        }

        if ($novo_saldo != $obj->saldo && $obj->categoria != 3) {
            // apaga registros
            Query::getInstance()->exe("DELETE FROM saldos_lancamentos WHERE saldos_lancamentos.id = " . $id_lancamento);
            if ($obj->categoria == 2) {
                Query::getInstance()->exe("UPDATE saldos_adiantados SET saldos_adiantados.status = 0 WHERE saldos_adiantados.id_setor = " . $obj->id_setor . " AND saldos_adiantados.valor_adiantado = '" . $obj->valor . "' AND saldos_adiantados.status = 1 AND saldos_adiantados.data_analise = '" . $obj->data . "' LIMIT 1;");
            }
            Query::getInstance()->exe("UPDATE saldo_setor SET saldo = '" . $novo_saldo . "' WHERE id_setor = " . $obj->id_setor);
        }
    }

    private static function updateSaldosUndoTransf(int $id_ori, int $id_dest, string $saldo_ori, string $saldo_dest) {
        Query::getInstance()->exe("UPDATE saldo_setor SET saldo = '" . $saldo_ori . "' WHERE id_setor = " . $id_ori);
        Query::getInstance()->exe("UPDATE saldo_setor SET saldo = '" . $saldo_dest . "' WHERE id_setor = " . $id_dest);
    }

    private static function undoTransf(int $id_lancamento) {

        // seleciona os dados da liberação
        $query = Query::getInstance()->exe("SELECT saldos_lancamentos.id_setor, saldos_lancamentos.data, saldos_lancamentos.valor, saldos_lancamentos.categoria, saldo_setor.saldo FROM saldos_lancamentos, saldo_setor WHERE saldo_setor.id_setor = saldos_lancamentos.id_setor AND saldos_lancamentos.id = " . $id_lancamento);

        $obj = $query->fetch_object();
        $obj_dest = $obj_ori = $id_lancamentoA = $id_lancamentoB = $id_ori = $id_dest = $valor = $saldo_ori = $saldo_dest = NULL;
        if ($obj->valor > 0) { // destino
            $obj_dest = $obj;
            $id_lancamentoB = $id_lancamento;
            $id_lancamento--;
            $id_lancamentoA = $id_lancamento;

            $obj_ori = $query_tr = Query::getInstance()->exe("SELECT saldos_lancamentos.id_setor, saldos_lancamentos.data, saldos_lancamentos.valor, saldos_lancamentos.categoria, saldo_setor.saldo FROM saldos_lancamentos, saldo_setor WHERE saldo_setor.id_setor = saldos_lancamentos.id_setor AND saldos_lancamentos.id = " . $id_lancamento)->fetch_object();
        } else if ($obj->valor < 0) { // origem
            $obj_ori = $obj;
            $id_lancamentoB = $id_lancamento;
            $id_lancamento++;
            $id_lancamentoA = $id_lancamento;

            $obj_dest = $query_tr = Query::getInstance()->exe("SELECT saldos_lancamentos.id_setor, saldos_lancamentos.data, saldos_lancamentos.valor, saldos_lancamentos.categoria, saldo_setor.saldo FROM saldos_lancamentos, saldo_setor WHERE saldo_setor.id_setor = saldos_lancamentos.id_setor AND saldos_lancamentos.id = " . $id_lancamento)->fetch_object();
        }

        if (!is_null($obj_dest)) {
            $valor = $obj_dest->valor;
            $id_dest = $obj_dest->id_setor;
            $saldo_dest = $obj_dest->saldo - $obj_dest->valor;
        }

        if (!is_null($obj_ori)) {
            $id_ori = $obj_ori->id_setor;
            $saldo_ori = $obj_ori->saldo - $obj_ori->valor;
        }

        if ($id_ori != $id_dest) {
            self::updateSaldosUndoTransf($id_ori, $id_dest, $saldo_ori, $saldo_dest);
            // apaga registros
            Query::getInstance()->exe("DELETE FROM saldos_lancamentos WHERE saldos_lancamentos.id = " . $id_lancamentoA . "  OR saldos_lancamentos.id = " . $id_lancamentoB);
            Query::getInstance()->exe("DELETE FROM saldos_transferidos WHERE saldos_transferidos.id_setor_ori = " . $id_ori . " AND saldos_transferidos.id_setor_dest = " . $id_dest . " AND saldos_transferidos.valor = '" . $valor . "' LIMIT 1;");
        }
    }

    public static function aprovaGerencia(array $pedidos) {
        if (empty($pedidos)) {
            return;
        }

        $where = '';
        $len = count($pedidos);
        for ($i = 0; $i < $len; $i++) {
            $where .= 'id = ' . $pedidos[$i];
            if ($i < $len - 1) {
                $where .= ' OR ';
            }
        }

        Query::getInstance()->exe('UPDATE pedido SET aprov_gerencia = 1 WHERE ' . $where);
    }

    public static function insertPedContr(int $id_pedido, int $id_tipo, string $siafi) {
        $query = Query::getInstance()->exe('SELECT id_tipo FROM pedido_contrato WHERE id_pedido = ' . $id_pedido);
        $sql = "INSERT INTO pedido_contrato VALUES({$id_pedido}, {$id_tipo}, '{$siafi}');";
        if ($query->num_rows > 0) {
            $sql = "UPDATE pedido_contrato SET id_tipo = $id_tipo, siafi = '{$siafi}' WHERE id_pedido = " . $id_pedido;
        }
        Query::getInstance()->exe($sql);
    }

    /**
     *    Função para cadastrar fontes do pedido (status == Aguarda Orçamento)
     *
     * @return bool If inserts all datas - true, else false.
     * @deprecated Use Request->setMoneySource()
     */
    public static function cadastraFontes(int $id_pedido, string $fonte, string $ptres, string $plano): bool {
        $fonte = Query::getInstance()->real_escape_string($fonte);
        $ptres = Query::getInstance()->real_escape_string($ptres);
        $plano = Query::getInstance()->real_escape_string($plano);

        $fonte = str_replace("\"", "'", $fonte);
        $ptres = str_replace("\"", "'", $ptres);
        $plano = str_replace("\"", "'", $plano);
        Query::getInstance()->exe("INSERT INTO pedido_fonte VALUES(NULL, {$id_pedido}, \"{$fonte}\", \"{$ptres}\", \"{$plano}\");");

        Query::getInstance()->exe('UPDATE pedido SET status = 6 WHERE id = ' . $id_pedido);
        return true;
    }

    /**
     *    Função para os usuários relatarem problemas no site.
     */
    public static function insereProblema(int $id_setor, string $assunto, string $descricao) {
        $assunto = Query::getInstance()->real_escape_string($assunto);
        $descricao = Query::getInstance()->real_escape_string($descricao);

        Query::getInstance()->exe("INSERT INTO problemas VALUES(NULL, " . $id_setor . ", '" . $assunto . "', '" . $descricao . "');");
    }

    /**
     * Function to edit information of an item
     *
     * @param object $data Object with the informations to edition.
     * @return bool
     */
    public static function editItem($data): bool {
        $query_qtd = Query::getInstance()->exe("SELECT sum(itens_pedido.qtd) AS soma FROM itens_pedido WHERE itens_pedido.id_item = " . $data->id);
        if ($query_qtd->num_rows > 0) {
            $obj_qtd = $query_qtd->fetch_object();
            $sum = $obj_qtd->soma;
            if ($data->qt_contrato < $sum || $data->qt_utilizado < $sum) {
                return false;
            }
        }
        $data->complemento_item = Query::getInstance()->real_escape_string($data->complemento_item);
        Query::getInstance()->exe("UPDATE itens SET itens.complemento_item = '{$data->complemento_item}', itens.vl_unitario = '{$data->vl_unitario}', itens.qt_contrato = {$data->qt_contrato}, itens.qt_utilizado = {$data->qt_utilizado}, itens.vl_utilizado = '{$data->vl_utilizado}', itens.qt_saldo = {$data->qt_saldo}, itens.vl_saldo = '{$data->vl_saldo}' WHERE itens.id = " . $data->id);

        // seleciona infos dos pedidos que contém o item editado e que não passaram da análise
        $query = Query::getInstance()->exe("SELECT itens_pedido.id_pedido, itens_pedido.qtd, itens_pedido.valor AS valor_item, pedido.id_setor, pedido.valor AS valor_pedido, saldo_setor.saldo FROM itens_pedido, pedido, saldo_setor WHERE saldo_setor.id_setor = pedido.id_setor AND itens_pedido.id_item = {$data->id} AND itens_pedido.id_pedido = pedido.id AND pedido.status <= 2;");

        $pedidos = [];
        $i = 0;
        while ($obj = $query->fetch_object()) {
            $valorItem = $obj->qtd * $data->vl_unitario;
            Query::getInstance()->exe("UPDATE itens_pedido SET itens_pedido.valor = '{$valorItem}' WHERE itens_pedido.id_item = {$data->id} AND itens_pedido.id_pedido = " . $obj->id_pedido);
            $saldo_setor = $obj->saldo + $obj->valor_item;
            $saldo_setor -= $valorItem;
            $saldo_setor = number_format($saldo_setor, 3, '.', '');
            // alterando o saldo do setor
            Query::getInstance()->exe("UPDATE saldo_setor SET saldo_setor.saldo = '{$saldo_setor}' WHERE saldo_setor.id_setor = " . $obj->id_setor);

            $pedidos[$i++] = $obj->id_pedido;
        }
        $len = count($pedidos);
        for ($i = 0; $i < $len; $i++) {
            $error = self::checkForErrors($pedidos[$i]);
            if ($error) {
                Logger::error("Pedido quebrado em editItem: " . $pedidos[$i]);
            }
        }
        self::updateRequests($pedidos);
        return true;
    }

    /**
     * Function that register the request effort.
     *
     * @param int $id_request Request id.
     * @param string $effort Effort to be registered.
     * @param string $date Effort date.
     * @return bool
     */
    public static function cadastraEmpenho(int $id_request, string $effort, string $date): bool {
        $effort = Query::getInstance()->real_escape_string($effort);

        $query_check = Query::getInstance()->exe('SELECT pedido_empenho.id, pedido.status FROM pedido_empenho, pedido WHERE pedido_empenho.id_pedido= pedido.id AND pedido.id = ' . $id_request);

        if ($query_check->num_rows < 1) {
            Query::getInstance()->exe("INSERT INTO pedido_empenho VALUES(NULL, {$id_request}, '{$effort}', '{$date}');");
            Query::getInstance()->exe('UPDATE pedido SET status = 7 WHERE id = ' . $id_request);
        } else {
            $obj = $query_check->fetch_object();
            if ($obj->status == 6) {
                Query::getInstance()->exe('UPDATE pedido SET status = 7 WHERE id = ' . $id_request);
            }

            Query::getInstance()->exe("UPDATE pedido_empenho SET empenho = '{$effort}', data = '{$date}' WHERE id_pedido = " . $id_request);
        }
        return true;
    }

    /**
     *    Função para cadastrar novo tipo de processo.
     */
    public static function newTypeProcess(string $tipo): bool {
        $tipo = Query::getInstance()->real_escape_string($tipo);
        Query::getInstance()->exe("INSERT INTO processos_tipo VALUES(NULL, '{$tipo}');");
        return true;
    }

    /**
     *    Função para cadastrar/editar um processo
     *
     * @param array $dados É um array que contém os dados do processo
     * @param $dados ["id_processo"] contém o id do processo, se for 0 então é para adc, se não dar update
     * @return bool
     */
    public static function updateProcesso($dados): bool {
        for ($i = 0; $i < count($dados); $i++) {
            $dados[$i] = trim($dados[$i]);
            if ($dados[$i] == "") {
                $dados[$i] = "----------";
            }
            $dados[$i] = Query::getInstance()->real_escape_string($dados[$i]);
        }
        if ($dados[0] == 0) {
            // INSERT
            Query::getInstance()->exe("INSERT INTO processos VALUES(NULL, '{$dados[1]}', '{$dados[2]}', '{$dados[3]}', '{$dados[4]}', '{$dados[5]}', '{$dados[6]}', '{$dados[7]}', '{$dados[8]}', '{$dados[9]}', '{$dados[10]}');");
        } else {
            Query::getInstance()->exe("UPDATE processos SET num_processo = '{$dados[1]}', tipo = '{$dados[2]}', estante = '{$dados[3]}', prateleira = '{$dados[4]}', entrada = '{$dados[5]}', saida = '{$dados[6]}', responsavel = '{$dados[7]}', retorno = '{$dados[8]}', obs = '{$dados[9]}', vigencia = '{$dados[10]}' WHERE id = {$dados[0]};");
        }
        return true;
    }

    /**
     *  Função para dar update numa senha de acordo com o email.
     */
    public static function resetSenha(string $email, string $senha) {
        // evita SQL Injections
        $email = Query::getInstance()->real_escape_string($email);
        // verificando se o e-mail consta no sistema
        $query_email = Query::getInstance()->exe("SELECT id FROM usuario WHERE email = '{$email}' AND ativo = 1 LIMIT 1;");
        if ($query_email->num_rows == 1) {
            $id = $query_email->fetch_object()->id;
            // criptografando a senha
            $senha = crypt($senha, SALT);
            Query::getInstance()->exe("UPDATE usuario SET senha = '{$senha}' WHERE id = " . $id);
            return "Sucesso";
        }
        return false;
    }

    /**
     *    Função usada para o usuário alterar a sua senha
     */
    public static function altInfoUser($id_user, $nome, $email, $novaSenha, $senhaAtual): bool {
        $query_exe = Query::getInstance()->exe('SELECT senha FROM usuario WHERE id = ' . $id_user);
        $usuario = $query_exe->fetch_object();
        if (crypt($senhaAtual, $usuario->senha) == $usuario->senha) {
            $nome = Query::getInstance()->real_escape_string($nome);
            $email = Query::getInstance()->real_escape_string($email);
            $novaSenha = crypt($novaSenha, SALT);
            Query::getInstance()->exe("UPDATE usuario SET nome = '{$nome}', email = '{$email}', senha = '{$novaSenha}' WHERE id = " . $id_user);
            $_SESSION["nome"] = $nome;
            $_SESSION["email"] = $email;
            return true;
        } else {
            // remove all session variables
            session_unset();
            // destroy the session
            session_destroy();
            return false;
        }
    }

    /**
     *    Função que analisa as solicitações de alteração de pedido.
     */
    public static function analisaSolicAlt(int $id_solic, int $id_pedido, int $acao): bool {
        $hoje = date('Y-m-d');
        Query::getInstance()->exe("UPDATE solic_alt_pedido SET data_analise = '{$hoje}', status = {$acao} WHERE id = " . $id_solic);
        if ($acao) {
            Query::getInstance()->exe("UPDATE pedido SET alteracao = {$acao}, prioridade = 5, status = 1 WHERE id = " . $id_pedido);
        }
        return true;
    }

    /**
     *    Função que envia uma solicitação de alteração de pedido ao SOF.
     * @return string Uma mesagem expressando o resultado da solicitação.
     */
    public static function solicAltPedido(int $id_pedido, int $id_setor, string $justificativa): string {
        $hoje = date('Y-m-d');
        $justificativa = Query::getInstance()->real_escape_string($justificativa);
        Query::getInstance()->exe("INSERT INTO solic_alt_pedido VALUES(NULL, {$id_pedido}, {$id_setor}, '{$hoje}', NULL, '{$justificativa}', 2);");
        return "Sua solicitação será análisada. Caso seja aprovada, seu pedido estará na sessão 'Rascunhos'";
    }

    /**
     *    Função para aprovar uma solicitação de adiantamento
     *
     * @param int $id
     * @param int $acao 0 -> reprovado | 1 -> aprovado
     * @return bool
     */
    public static function analisaAdi(int $id, int $acao): bool {
        $hoje = date('Y-m-d');

        Query::getInstance()->exe("UPDATE saldos_adiantados SET data_analise = '{$hoje}', status = {$acao} WHERE id = " . $id);
        if (!$acao) {
            // se reprovado retorna
            return true;
        }
        $query = Query::getInstance()->exe('SELECT saldos_adiantados.id_setor, saldo_setor.saldo + saldos_adiantados.valor_adiantado AS saldo_final, saldos_adiantados.valor_adiantado FROM saldo_setor, saldos_adiantados WHERE saldos_adiantados.id = ' . $id . ' AND saldo_setor.id_setor = saldos_adiantados.id_setor');
        $obj = $query->fetch_object();
        $obj->saldo_final = number_format($obj->saldo_final, 3, '.', '');
        // fazendo o lançamento da operação
        Query::getInstance()->exe("INSERT INTO saldos_lancamentos VALUES(NULL, {$obj->id_setor}, '{$hoje}', '{$obj->valor_adiantado}', 2);");
        Query::getInstance()->exe("UPDATE saldo_setor SET saldo = '{$obj->saldo_final}' WHERE id_setor = " . $obj->id_setor);
        return true;
    }

    /**
     *    Função para enviar um pedido de adiantamento de saldo para o SOF.
     */
    public static function solicitaAdiantamento(int $id_setor, string $valor, string $justificativa): bool {
        $valor = Query::getInstance()->real_escape_string($valor);
        $justificativa = Query::getInstance()->real_escape_string($justificativa);
        $hoje = date('Y-m-d');
        $valor = number_format($valor, 3, '.', '');
        Query::getInstance()->exe("INSERT INTO saldos_adiantados VALUES(NULL, {$id_setor}, '{$hoje}', NULL, '{$valor}', '{$justificativa}', 2);");
        return true;
    }

    /**
     *   Função para alterar a senha de um usuário.
     */
    public static function updateSenha($id_user, $senha): bool {
        Query::getInstance()->exe("UPDATE usuario SET senha = '{$senha}' WHERE id = " . $id_user);
        return true;
    }

    /**
     * Função para inserir postagem.
     */
    public static function setPost($data, $postagem, $pag) {
        $data = Query::getInstance()->real_escape_string($data);
        $postagem = Query::getInstance()->real_escape_string($postagem);
        $pag = Query::getInstance()->real_escape_string($pag);

        $inicio = strpos($postagem, "<h3");
        $fim = strpos($postagem, "</h3>");
        $titulo = strip_tags(substr($postagem, $inicio, $fim));

        Query::getInstance()->exe("INSERT INTO postagens VALUES(NULL, {$pag}, '{$titulo}', '{$data}', 1, '{$postagem}');");

        return true;
    }

    /**
     *   Função para editar uma postagem.
     */
    public static function editPost($data, $id, $postagem, $pag) {
        $postagem = Query::getInstance()->real_escape_string($postagem);

        $inicio = strpos($postagem, "<h3");
        $fim = strpos($postagem, "</h3>");
        $titulo = strip_tags(substr($postagem, $inicio, $fim));

        Query::getInstance()->exe("UPDATE postagens SET tabela = {$pag}, titulo = '{$titulo}', data = '{$data}', postagem = '{$postagem}' WHERE id = " . $id);

        return true;
    }

    /**
     *   Função para excluir uma publicação a publicação não é totalmente excluída, apenas o sistema passará a não mostrá-la.
     */
    public static function excluirNoticia(int $id) {
        $id = Query::getInstance()->real_escape_string($id);

        Query::getInstance()->exe('UPDATE postagens SET ativa = 0 WHERE id = ' . $id);
        $query = Query::getInstance()->exe('SELECT postagens.tabela FROM postagens WHERE postagens.id = ' . $id);
        $obj = $query->fetch_object();
        return $obj->tabela;
    }

    public static function checkForErrors(int $pedido): bool {
        $query = Query::getInstance()->exe("SELECT id, round(valor, 3) AS valor FROM pedido WHERE id = " . $pedido);
        $obj = $query->fetch_object();

        $ped = Query::getInstance()->exe("SELECT round(sum(valor), 3) AS soma FROM itens_pedido WHERE id_pedido = " . $pedido);
        $obj_ped = $ped->fetch_object();
        if ($obj->valor != $obj_ped->soma) {
            return true;
        }
        return false;
    }

    public static function existsSources(int $id_request): bool {
        $query = Query::getInstance()->exe("SELECT id FROM pedido_id_fonte WHERE id_pedido = " . $id_request);

        return $query->num_rows > 0;
    }

}
