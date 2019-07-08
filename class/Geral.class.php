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

// TODO: capturar erro da execucao da query e retornar false em alguns metodos com retorno estatico

final class Geral {

    /**
     * Default constructor.
     */
    private function __construct() {
        // empty
    }

    public static function removeReceita(int $id) {
        Query::getInstance()->exe("DELETE FROM aihs_receita WHERE id = " . $id);
    }

    public static function removeAIHS(int $id) {
        Query::getInstance()->exe("DELETE FROM aihs WHERE id = " . $id);
    }

    public static function cadReceita(int $id, int $tipo, int $mes, string $data, float $valor, string $pf, string $obs): bool {
        if ($id > 0) {
            self::removeReceita($id);
        }
        $dt = Util::dateFormat($data);
        $builder = new SQLBuilder(SQLBuilder::$INSERT);
        $builder->setTables(['aihs_receita']);
        $builder->setValues([NULL, $tipo, $mes, $dt, $valor, $pf, $obs]);

        $query = Query::getInstance()->exe($builder->__toString());
        if ($query) {
            return true;
        }
        return false;
    }

    public static function cadAIHS(int $id, string $data, int $mes, int $qtd, float $valor, int $tipo, string $grupo, string $descricao): bool {
        if ($id > 0) {
            self::removeAIHS($id);
        }
        $dt = Util::dateFormat($data);

        $builder = new SQLBuilder(SQLBuilder::$INSERT);
        $builder->setTables(['aihs']);
        $builder->setValues([NULL, $dt, $mes, $qtd, $valor, $tipo, $grupo, $descricao]);

        $query = Query::getInstance()->exe($builder->__toString());
        if ($query) {
            return true;
        }
        return false;
    }

    /**
     * Removes a contract by the given id.
     *
     * @param int $id Contract id.
     * @return bool If operation succeed - true, else - false.
     */
    public static function remContract(int $id): bool {
        Query::getInstance()->exe("DELETE FROM contrato_empresa WHERE id_contrato = " . $id);
        Query::getInstance()->exe("DELETE FROM mensalidade WHERE id_contr = " . $id);
        $query = Query::getInstance()->exe("DELETE FROM contrato WHERE id = " . $id);

        if ($query) {
            return true;
        }
        return false;
    }

    public static function cadMensalidade(int $id, int $contr, int $ano, int $mes, int $grupo, float $valor, bool $nota, float $reajuste, bool $aguardaOrc, bool $paga): bool {
        $builder = new SQLBuilder(SQLBuilder::$INSERT);
        $builder->setTables(['mensalidade']);

        $query_saldo = Query::getInstance()->exe("SELECT (SELECT teto FROM contrato WHERE id = " . $contr . ") - SUM(valor) AS saldo FROM mensalidade WHERE id_contr = " . $contr);

        $saldo = $query_saldo->fetch_object()->saldo;
        if (empty($saldo)) {
            // saldo ok
            $saldo = $valor + 1;
        }

        if ($valor > $saldo) {
            Logger::error("Can't insert mensalidade, value is bigger than saldo");
            return false;
        }

        if ($id > 0) {
            $builder->setType(SQLBuilder::$UPDATE);
            $builder->setColumns(['id_contr', 'id_mes', 'id_ano', 'id_grupo', 'valor', 'nota', 'reajuste', 'aguardaOrcamento', 'paga']);
            $builder->setValues([$contr, $mes, $ano, $grupo, $valor, $nota, $reajuste, $aguardaOrc, $paga]);
            $builder->setWhere("id=" . $id);
        } else {
            // insert new entry
            $builder->setValues([NULL, $contr, $mes, $ano, $grupo, $valor, $nota, $reajuste, $aguardaOrc, $paga]);
        }

        Query::getInstance()->exe($builder->__toString());
        return true;
    }

    public static function cadEmpresa(string $nome, string $cnpj, array $contratos, array $grupos): bool {
        $nome = Query::getInstance()->real_escape_string($nome);

        $builder = new SQLBuilder(SQLBuilder::$INSERT);
        $builder->setTables(['empresa']);
        $builder->setValues([NULL, $nome, $cnpj]);

        Query::getInstance()->exe($builder->__toString());
        $id = Query::getInstance()->getInsertId();

        if (!empty($grupos)) {
            $sql = "INSERT INTO empresa_grupo VALUES";
            foreach ($grupos AS $grupo) {
                $sql .= "(" . $grupo . ", " . $id . "),";
            }
            $pos = strrpos($sql, ",");
            $sql[$pos] = ";";
            // insere os grupos
            Query::getInstance()->exe($sql);
        }

        if (!empty($contratos)) {
            $sql = "INSERT INTO contrato_empresa VALUES";
            foreach ($contratos AS $contrato) {
                $sql .= "(" . $id . ", " . $contrato . "),";
            }
            $pos = strrpos($sql, ",");
            $sql[$pos] = ";";
            // insere os contratos
            Query::getInstance()->exe($sql);
        }
        return true;
    }

    public static function cadContract(int $id, string $numero, float $teto, string $dt_inicio, string $dt_fim, float $mensalidade): bool {
        $numero = Query::getInstance()->real_escape_string($numero);

        $builder = new SQLBuilder(SQLBuilder::$INSERT);
        $builder->setTables(['contrato']);
        if ($id == 0) {
            $builder->setValues([NULL, $numero, $teto, $dt_inicio, $dt_fim, $mensalidade]);
        } else {
            $builder->setType(SQLBuilder::$UPDATE);
            $builder->setColumns(['numero', 'teto', 'dt_inicio', 'dt_fim', 'mensalidade']);
            $builder->setValues([$numero, $teto, $dt_inicio, $dt_fim, $mensalidade]);
            $builder->setWhere("id = " . $id);
        }
        Query::getInstance()->exe($builder->__toString());
        return true;
    }

    /**
     *    Função para cadastrar fontes do pedido (status == Aguarda Orçamento)
     *
     * @return bool If inserts all datas - true, else false.
     */
    public static function cadastraFontes(int $id_pedido, string $fonte, string $ptres, string $plano): bool {
        // garante a edição e evita informações duplicadas
        Query::getInstance()->exe("DELETE FROM pedido_fonte WHERE id_pedido=" . $id_pedido);

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
     * @param int $acao 0 -> reprovado ,1 -> aprovado
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

}
