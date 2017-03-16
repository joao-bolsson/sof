<?php

/**
 *  Classe com as funções de cadastrados utilizadas pelo arquivo php/geral.php
 *  toda função de ENTRADA de dados no banco devem ficar nesta classe
 *
 *  @author João Bolsson (joaovictorbolsson@gmail.com).
 *  @since 2016, 16 Mar.
 */
ini_set('display_erros', true);
error_reporting(E_ALL);

require_once '../defines.php';

spl_autoload_register(function (string $class_name) {
    include_once $class_name . '.class.php';
});

final class Geral {

    private static $INSTANCE;

    public static function getInstance(): Geral {
        if (empty(self::$INSTANCE)) {
            self::$INSTANCE = new Geral();
        }
        return self::$INSTANCE;
    }

    private function __construct() {
        // empty
    }

    public function editLog(int $id, string $entrada, string $saida) {
        $dateTimeE = DateTime::createFromFormat('d/m/Y H:i:s', $entrada);
        $in = $dateTimeE->format('Y-m-d H:i:s');

        $dateTimeS = DateTime::createFromFormat('d/m/Y H:i:s', $saida);
        $out = $dateTimeS->format('Y-m-d H:i:s');

        Query::getInstance()->exe("UPDATE usuario_hora SET entrada = '" . $in . "', saida = '" . $out . "' WHERE id = " . $id);

        // atualiza horas realizadas
        $horas = Util::getInstance()->getTimeDiffHora($id);
        Query::getInstance()->exe('UPDATE usuario_hora SET horas = ' . $horas . ' WHERE id = ' . $id);
    }

    public function pointRegister(int $log) {
        $id_usuario = $_SESSION['id'];
        if ($log == 1) {
            $ip = filter_input(INPUT_SERVER, 'REMOTE_ADDR');
            // entrada
            Query::getInstance()->exe('INSERT INTO usuario_hora VALUES(NULL, ' . $id_usuario . ", NOW(), NULL, NULL, '" . $ip . "')");
        } else {
            // saída
            $id_last = Busca::getInstance()->getLastRegister();
            Query::getInstance()->exe('UPDATE usuario_hora SET saida = NOW() WHERE id = ' . $id_last);
            // atualiza horas realizadas
            $horas = Util::getInstance()->getTimeDiffHora($id_last);
            Query::getInstance()->exe('UPDATE usuario_hora SET horas = ' . $horas . ' WHERE id = ' . $id_last);
        }
    }

    /**
     * 
     * @param array $dados Informações que serão inseridas na tabela de itens.
     * @param array $campos Campos da coluna no banco que deverão ser abastecidos
     */
    public function cadItensRP(array $dados, array $campos) {
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

    public function editItemFactory($dados) {
        if (empty($dados)) {
            exit('Factory data is empty');
        }
        Query::getInstance()->exe("UPDATE itens SET cod_despesa = '" . $dados->codDespesa . "', descr_despesa = '" . $dados->descrDespesa . "', cod_reduzido = '" . $dados->codReduzido . "', dt_fim = '" . $dados->dtFim . "', seq_item_processo = '" . $dados->seqItemProcesso . "' WHERE id = " . $dados->idItem . " LIMIT 1;");
    }

    /**
     * Desfaz uma liberação orçamentária. Suporta apenas do tipo 'normal' até a v2.1.4.
     * @param int $id_lancamento Id do lançamento.
     */
    public function undoFreeMoney(int $id_lancamento) {
        // seleciona os dados da liberação
        $query = Query::getInstance()->exe("SELECT saldos_lancamentos.id_setor, saldos_lancamentos.data, saldos_lancamentos.valor, saldos_lancamentos.categoria, saldo_setor.saldo FROM saldos_lancamentos, saldo_setor WHERE saldo_setor.id_setor = saldos_lancamentos.id_setor AND saldos_lancamentos.id = " . $id_lancamento);

        $obj = $query->fetch_object();

        if ($obj->categoria == 4) {
            return;
        }
        $novo_saldo = $obj->saldo;
        if ($obj->categoria == 3) { // transferencia
            $this->undoTransf($id_lancamento);
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

    private function updateSaldosUndoTransf(int $id_ori, int $id_dest, string $saldo_ori, string $saldo_dest) {
        Query::getInstance()->exe("UPDATE saldo_setor SET saldo = '" . $saldo_ori . "' WHERE id_setor = " . $id_ori);
        Query::getInstance()->exe("UPDATE saldo_setor SET saldo = '" . $saldo_dest . "' WHERE id_setor = " . $id_dest);
    }

    private function undoTransf(int $id_lancamento) {

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
            $this->updateSaldosUndoTransf($id_ori, $id_dest, $saldo_ori, $saldo_dest);
            // apaga registros
            Query::getInstance()->exe("DELETE FROM saldos_lancamentos WHERE saldos_lancamentos.id = " . $id_lancamentoA . "  OR saldos_lancamentos.id = " . $id_lancamentoB);
            Query::getInstance()->exe("DELETE FROM saldos_transferidos WHERE saldos_transferidos.id_setor_ori = " . $id_ori . " AND saldos_transferidos.id_setor_dest = " . $id_dest . " AND saldos_transferidos.valor = '" . $valor . "' LIMIT 1;");
        }
    }

    public function aprovaGerencia(array $pedidos) {
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

    public function insertPedContr(int $id_pedido, int $id_tipo, string $siafi) {
        $query = Query::getInstance()->exe('SELECT id_tipo FROM pedido_contrato WHERE id_pedido = ' . $id_pedido);
        $sql = "INSERT INTO pedido_contrato VALUES({$id_pedido}, {$id_tipo}, '{$siafi}');";
        if ($query->num_rows > 0) {
            $sql = "UPDATE pedido_contrato SET id_tipo = $id_tipo, siafi = '{$siafi}' WHERE id_pedido = " . $id_pedido;
        }
        Query::getInstance()->exe($sql);
    }

    /**
     * Cadastra um usuário.
     * @param string $nome Nome do usuário.
     * @param string $login Login.
     * @param string $email E-mail para contato e perda de senha. 
     * @param int $setor Id do setor do usuário.
     * @return string Senha do usuário feita pelo sistema.
     */
    public function cadUser(string $nome, string $login, string $email, int $setor, string $senha): int {
        $senha_crp = crypt($senha, SALT);
        Query::getInstance()->exe("INSERT INTO usuario VALUES(NULL, '{$nome}', '{$login}', '{$senha_crp}', {$setor}, '{$email}');");
        $id = Query::getInstance()->getInsertId();

        return $id;
    }

    public function cadPermissao(int $usuario, int $noticias, int $saldos, int $pedidos, int $recepcao) {
        Query::getInstance()->exe("INSERT INTO usuario_permissoes VALUES({$usuario}, {$noticias}, {$saldos}, {$pedidos}, {$recepcao});");
    }

    /**
     * Insere um grupo ao pedido.
     * @param int $pedido Id do pedido.
     * @param int $grupo id do grupo para inserir ao pedido.
     */
    public function insertGrupoPedido(int $pedido, int $grupo, bool $existe) {
        $sql = ($existe) ? "UPDATE pedido_grupo SET id_grupo = {$grupo} WHERE id_pedido = {$pedido} LIMIT 1;" : "INSERT INTO pedido_grupo VALUES({$pedido}, {$grupo});";
        Query::getInstance()->exe($sql);
    }

    /**
     * 	Funçao para mudar o status do pedido para 'Enviado ao Fornecedor' (UA)
     *
     * 	@param $id_pedido Id do pedido.
     */
    public function enviaFornecedor(int $id_pedido) {
        Query::getInstance()->exe('UPDATE pedido SET status = 9 WHERE id = ' . $id_pedido);
    }

    /**
     * 	Função para enviar um pedido ao ordenador.
     *
     * 	@return if success - true, else false.
     */
    public function enviaOrdenador(int $id_pedido): bool {
        Query::getInstance()->exe('UPDATE pedido SET status = 8 WHERE id = ' . $id_pedido);
        return true;
    }

    /**
     * 	Função para cadastrar fontes do pedido (status == Aguarda Orçamento)
     *
     * 	@return If inserts all datas - true, else false.
     */
    public function cadastraFontes(int $id_pedido, string $fonte, string $ptres, string $plano): bool {
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
     * 	Função para resetar o sistema para o estado orinal
     */
    public function resetSystem() {
        $tables = ['comentarios', 'itens_pedido', 'pedido_empenho', 'pedido_fonte', 'processos', 'saldo_setor', 'saldos_adiantados', 'saldos_lancamentos', 'saldos_transferidos', 'solic_alt_pedido', 'itens', 'licitacao', 'pedido_grupo', 'pedido_log_status', 'pedido_contrato', 'pedido'];

        $len = count($tables);
        for ($i = 0; $i < $len; $i++) {
            $tb = $tables[$i];
            // DELETE
            Query::getInstance()->exe('DELETE FROM ' . $tb);
            // ALTER TABLE
            Query::getInstance()->exe('alter table ' . $tb . ' auto_increment = 1');
        }
    }

    /**
     * 	Função para os usuários relatarem problemas no site.
     */
    public function insereProblema(int $id_setor, string $assunto, string $descricao) {
        $assunto = Query::getInstance()->real_escape_string($assunto);
        $descricao = Query::getInstance()->real_escape_string($descricao);

        Query::getInstance()->exe("INSERT INTO problemas VALUES(NULL, " . $id_setor . ", '" . $assunto . "', '" . $descricao . "');");
    }

    /**
     * 	Função para editar informações de um item
     *
     * 	@param $dados Objeto com as informações para edição
     * 	@return bool
     */
    public function editItem($dados) {
        $query_qtd = Query::getInstance()->exe("SELECT sum(itens_pedido.qtd) AS soma FROM itens_pedido where itens_pedido.id_item = " . $dados->idItem);
        if ($query_qtd->num_rows > 0) {
            $obj_qtd = $query_qtd->fetch_object();
            $soma = $obj_qtd->soma;
            if ($dados->qtContrato < $soma || $dados->qtUtilizada < $soma) {
                return false;
            }
        }
        $dados->compItem = Query::getInstance()->real_escape_string($dados->compItem);
        Query::getInstance()->exe("UPDATE itens SET itens.complemento_item = '{$dados->compItem}', itens.vl_unitario = '{$dados->vlUnitario}', itens.qt_contrato = {$dados->qtContrato}, itens.qt_utilizado = {$dados->qtUtilizada}, itens.vl_utilizado = '{$dados->vlUtilizado}', itens.qt_saldo = {$dados->qtSaldo}, itens.vl_saldo = '{$dados->vlSaldo}' WHERE itens.id = " . $dados->idItem);

        // seleciona infos dos pedidos que contém o item editado e que não passaram da análise
        $query = Query::getInstance()->exe("SELECT itens_pedido.id_pedido, itens_pedido.qtd, itens_pedido.valor AS valor_item, pedido.id_setor, pedido.valor AS valor_pedido, saldo_setor.saldo FROM itens_pedido, pedido, saldo_setor WHERE saldo_setor.id_setor = pedido.id_setor AND itens_pedido.id_item = {$dados->idItem} AND itens_pedido.id_pedido = pedido.id AND pedido.status <= 2;");

        $saldo_setor = 0;
        while ($obj = $query->fetch_object()) {
            $valorItem = $obj->qtd * $dados->vlUnitario;
            Query::getInstance()->exe("UPDATE itens_pedido SET itens_pedido.valor = '{$valorItem}' WHERE itens_pedido.id_item = {$dados->idItem} AND itens_pedido.id_pedido = " . $obj->id_pedido);
            $saldo_setor = $obj->saldo + $obj->valor_item;
            $saldo_setor -= $valorItem;
            $saldo_setor = number_format($saldo_setor, 3, '.', '');
            // alterando o saldo do setor
            Query::getInstance()->exe("UPDATE saldo_setor SET saldo_setor.saldo = '{$saldo_setor}' WHERE saldo_setor.id_setor = " . $obj->id_setor);
            $valorPedido = $obj->valor_pedido - $obj->valor_item;
            $valorPedido += $valorItem;
            $valorPedido = number_format($valorPedido, 3, '.', '');
            // alterando o valor total do pedido
            Query::getInstance()->exe("UPDATE pedido SET pedido.valor = '{$valorPedido}' WHERE pedido.id = " . $obj->id_pedido);
        }
        return true;
    }

    /**
     * 	Função para alterar somente o status do pedido
     *
     * 	@param $id_pedido Id do pedido
     * 	@param $id_setor Id do setor que fez o pedido
     * 	@param $comentario Comentário do SOF.
     * 	@param $status Novo status do pedido
     * 	@return bool
     */
    public function altStatus($id_pedido, $id_setor, $comentario, $status): bool {
        Query::getInstance()->exe("UPDATE pedido SET status = {$status} WHERE id = " . $id_pedido);
        $query = Query::getInstance()->exe("SELECT pedido.prioridade, pedido.valor FROM pedido WHERE id = " . $id_pedido);
        $obj = $query->fetch_object();
        $hoje = date('Y-m-d');
        if (strlen($comentario) > 0) {
            $comentario = Query::getInstance()->real_escape_string($comentario);
            Query::getInstance()->exe("INSERT INTO comentarios VALUES(NULL, {$id_pedido}, '{$hoje}', {$obj->prioridade}, {$status}, '{$obj->valor}', '{$comentario}');");
        }
        return true;
    }

    /**
     * 	Função que cadastra o empenho de um pedido.
     *
     *  @param $id_pedido Id do pedido.
     * 	@param $empenho Empenho a ser cadastrado.
     * 	@return bool
     */
    public function cadastraEmpenho(int $id_pedido, string $empenho, string $data): bool {
        $empenho = Query::getInstance()->real_escape_string($empenho);
        // verifica se o pedido ja não possui empenho
        $query_check = Query::getInstance()->exe('SELECT id FROM pedido_empenho WHERE id_pedido = ' . $id_pedido);
        $sql = "";
        if ($query_check->num_rows < 1) {
            // cadastrando empenho
            $sql = "INSERT INTO pedido_empenho VALUES(NULL, {$id_pedido}, '{$empenho}', '{$data}');";
            // mudando status do pedido
            Query::getInstance()->exe('UPDATE pedido SET status = 7 WHERE id = ' . $id_pedido);
        } else {
            // alterando empenho
            $sql = "UPDATE pedido_empenho SET pedido_empenho.empenho = '{$empenho}', pedido_empenho.data = '{$data}' WHERE pedido_empenho.id_pedido = " . $id_pedido;
        }
        Query::getInstance()->exe($sql);
        return true;
    }

    /**
     * 	Função que transfere um valor do saldo de um setor para outro
     *
     * 	@param $ori Setor de origem do saldo.
     * 	@param $dest Setor de destino do saldo.
     * 	@param $valor Valor que será transferido.
     * 	@param $just Justificativa da transferência.
     * 	@return bool
     */
    public function transfereSaldo($ori, $dest, $valor, $just): bool {
        $valor = number_format($valor, 3, '.', '');
        $saldo_ori = '0';
        // selecionando o saldo do setor origem
        $query_saldo_ori = Query::getInstance()->exe('SELECT saldo FROM saldo_setor WHERE id_setor = ' . $ori);
        if ($query_saldo_ori->num_rows < 1) {
            Query::getInstance()->exe("INSERT INTO saldo_setor VALUES(NULL, {$ori}, '0.000');");
        } else {
            $obj = $query_saldo_ori->fetch_object();
            $saldo_ori = $obj->saldo;
        }

        if ($valor > $saldo_ori) {
            return false;
        }
        $valor = number_format($valor, 3, '.', '');
        // registrando a transferência
        $justificativa = Query::getInstance()->real_escape_string($just);
        Query::getInstance()->exe("INSERT INTO saldos_transferidos VALUES(NULL, {$ori}, {$dest}, '{$valor}', '{$justificativa}');");
        // registrando na tabela de lançamentos
        $hoje = date('Y-m-d');
        Query::getInstance()->exe("INSERT INTO saldos_lancamentos VALUES(NULL, {$ori}, '{$hoje}', '-{$valor}', 3), (NULL, {$dest}, '{$hoje}', '{$valor}', 3);");
        // atualizando o saldo do setor origem
        $saldo_ori -= $valor;
        $saldo_ori = number_format($saldo_ori, 3, '.', '');
        Query::getInstance()->exe("UPDATE saldo_setor SET saldo = '{$saldo_ori}' WHERE id_setor = " . $ori);
        // atualizando o saldo do setor destino
        $saldo_dest = '0';
        // selecionando o saldo do setor destino
        $query_saldo_dest = Query::getInstance()->exe('SELECT saldo FROM saldo_setor WHERE id_setor = ' . $dest);
        if ($query_saldo_dest->num_rows < 1) {
            Query::getInstance()->exe("INSERT INTO saldo_setor VALUES(NULL, {$dest}, '0.000');");
        } else {
            $obj = $query_saldo_dest->fetch_object();
            $saldo_dest = $obj->saldo;
        }
        $saldo_dest += $valor;
        $saldo_dest = number_format($saldo_dest, 3, '.', '');
        Query::getInstance()->exe("UPDATE saldo_setor SET saldo = '{$saldo_dest}' WHERE id_setor = " . $dest);
        return true;
    }

    /**
     * 	Função para cadastrar novo tipo de processo.
     */
    public function newTypeProcess(string $tipo): bool {
        $tipo = Query::getInstance()->real_escape_string($tipo);
        Query::getInstance()->exe("INSERT INTO processos_tipo VALUES(NULL, '{$tipo}');");
        return true;
    }

    /**
     * 	Função para cadastrar/editar um processo
     *
     * 	@param $dados é um array que contém os dados do processo
     * 	@param $dados["id_processo"] contém o id do processo, se for 0 então é para adc, se não dar update
     * 	@return bool
     */
    public function updateProcesso($dados): bool {
        for ($i = 0; $i < count($dados); $i++) {
            $dados[$i] = trim($dados[$i]);
            if ($dados[$i] == "") {
                $dados[$i] = "----------";
            }
            $dados[$i] = Query::getInstance()->real_escape_string($dados[$i]);
        }
        if ($dados[0] == 0) {
            // INSERT
            Query::getInstance()->exe("INSERT INTO processos VALUES(NULL, '{$dados[1]}', '{$dados[2]}', '{$dados[3]}', '{$dados[4]}', '{$dados[5]}', '{$dados[6]}', '{$dados[7]}', '{$dados[8]}', '{$dados[9]}');");
        } else {
            Query::getInstance()->exe("UPDATE processos SET num_processo = '{$dados[1]}', tipo = '{$dados[2]}', estante = '{$dados[3]}', prateleira = '{$dados[4]}', entrada = '{$dados[5]}', saida = '{$dados[6]}', responsavel = '{$dados[7]}', retorno = '{$dados[8]}', obs = '{$dados[9]}' WHERE id = {$dados[0]};");
        }
        return true;
    }

    /**
     * 	Função que importa itens por SQL.
     */
    public function importaItens(array $array_sql): bool {
        $len = count($array_sql);
        for ($i = 0; $i < $len; $i++) {
            $query = $array_sql[$i];
            Query::getInstance()->exe($query);
        }
        return true;
    }

    /**
     *  Função para dar update numa senha de acordo com o email.
     */
    public function resetSenha(string $email, string $senha) {
        // evita SQL Injections
        $email = Query::getInstance()->real_escape_string($email);
        // verificando se o e-mail consta no sistema
        $query_email = Query::getInstance()->exe("SELECT id FROM usuario WHERE email = '{$email}' LIMIT 1;");
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
     * 	Função usada para o usuário alterar a sua senha
     */
    public function altInfoUser($id_user, $nome, $email, $novaSenha, $senhaAtual): bool {
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
     * 	Função que analisa as solicitações de alteração de pedido.
     */
    public function analisaSolicAlt(int $id_solic, int $id_pedido, int $acao): bool {
        $hoje = date('Y-m-d');
        Query::getInstance()->exe("UPDATE solic_alt_pedido SET data_analise = '{$hoje}', status = {$acao} WHERE id = " . $id_solic);
        if ($acao) {
            Query::getInstance()->exe("UPDATE pedido SET alteracao = {$acao}, prioridade = 5, status = 1 WHERE id = " . $id_pedido);
        }
        return true;
    }

    /**
     * 	Função que envia uma solicitação de alteração de pedido ao SOF.
     * 	@return Uma mesagem expressando o resultado da solicitação.
     */
    public function solicAltPedido(int $id_pedido, int $id_setor, string $justificativa): string {
        $hoje = date('Y-m-d');
        $justificativa = Query::getInstance()->real_escape_string($justificativa);
        Query::getInstance()->exe("INSERT INTO solic_alt_pedido VALUES(NULL, {$id_pedido}, {$id_setor}, '{$hoje}', NULL, '{$justificativa}', 2);");
        return "Sua solicitação será análisada. Caso seja aprovada, seu pedido estará na sessão 'Rascunhos'";
    }

    /**
     * 	Função para liberação de saldo de um setor
     *
     * 	@param $id_setor Comment.
     * 	@param $valor Comment.
     * 	@param $saldo_atual Comment.
     */
    public function liberaSaldo($id_setor, $valor, $saldo_atual): bool {
        $saldo = $saldo_atual + $valor;
        $saldo = number_format($saldo, 3, '.', '');
        $verifica = Query::getInstance()->exe("SELECT saldo_setor.id FROM saldo_setor WHERE saldo_setor.id_setor = " . $id_setor);
        if ($verifica->num_rows < 1) {
            Query::getInstance()->exe("INSERT INTO saldo_setor VALUES(NULL, {$id_setor}, '0.000');");
        }
        Query::getInstance()->exe("UPDATE saldo_setor SET saldo = '{$saldo}' WHERE id_setor = " . $id_setor);
        if ($id_setor != 2) {
            $saldo_sof = Busca::getInstance()->getSaldo(2) - $valor;
            $saldo_sof = number_format($saldo_sof, 3, '.', '');
            Query::getInstance()->exe("UPDATE saldo_setor SET saldo = '{$saldo_sof}' WHERE id_setor = 2;");
        }
        $hoje = date('Y-m-d');
        Query::getInstance()->exe("INSERT INTO saldos_lancamentos VALUES(NULL, {$id_setor}, '{$hoje}', '{$valor}', 1);");
        return true;
    }

    /**
     * 	Função para aprovar uma solicitação de adiantamento
     *
     * 	@param $acao 0 -> reprovado | 1 -> aprovado
     * 	@return bool
     */
    public function analisaAdi(int $id, int $acao): bool {
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
     * 	Função para enviar um pedido de adiantamento de saldo para o SOF.
     */
    public function solicitaAdiantamento(int $id_setor, string $valor, string $justificativa): bool {
        $valor = Query::getInstance()->real_escape_string($valor);
        $justificativa = Query::getInstance()->real_escape_string($justificativa);
        $hoje = date('Y-m-d');
        $valor = number_format($valor, 3, '.', '');
        $insere = Query::getInstance()->exe("INSERT INTO saldos_adiantados VALUES(NULL, {$id_setor}, '{$hoje}', NULL, '{$valor}', '{$justificativa}', 2);");
        return true;
    }

    /**
     *   Função para alterar a senha de um usuário.
     */
    public function updateSenha($id_user, $senha): bool {
        Query::getInstance()->exe("UPDATE usuario SET senha = '{$senha}' WHERE id = " . $id_user);
        return true;
    }

    /**
     * Função para inserir postagem.
     */
    public function setPost($data, $postagem, $pag) {
        $data = Query::getInstance()->real_escape_string($data);
        $postagem = Query::getInstance()->real_escape_string($postagem);
        $pag = Query::getInstance()->real_escape_string($pag);

        $inicio = strpos($postagem, "<h3");
        $fim = strpos($postagem, "</h3>");
        $titulo = strip_tags(substr($postagem, $inicio, $fim));

        $query_post = Query::getInstance()->exe("INSERT INTO postagens VALUES(NULL, {$pag}, '{$titulo}', '{$data}', 1, '{$postagem}');");

        return true;
    }

    /**
     *   Função para editar uma postagem.
     */
    public function editPost($data, $id, $postagem, $pag) {
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
    public function excluirNoticia(int $id) {
        $id = Query::getInstance()->real_escape_string($id);

        Query::getInstance()->exe('UPDATE postagens SET ativa = 0 WHERE id = ' . $id);
        $query = Query::getInstance()->exe('SELECT postagens.tabela FROM postagens WHERE postagens.id = ' . $id);
        $obj = $query->fetch_object();
        return $obj->tabela;
    }

    /**
     * Função para cadastrar uma licitação.
     * 
     * UASG e procOri podem ser NULL.
     * 
     * @param string $numero Número informado no formulário.
     * @param string $uasg Uasg, se o tipo for adesao ou compra compartilhada.
     * @param string $procOri Processo Original, se o tipo for adesao ou compra compartilhada.
     * @param int $tipo Tipo de licitação.
     * @param int $pedido Id do pedido.
     * @param int $idLic id da licitação.
     * @param int $geraContrato flag que determina se gera ou nao contrato - apenas mostra na impressao.
     * @return bool
     */
    public function insertLicitacao(string $numero, $uasg, $procOri, int $tipo, int $pedido, int $idLic, int $geraContrato): bool {
        if ($tipo != 3 && $tipo != 4 && $tipo != 2) { // Adesão, Compra Compartilhada ou Inexibilidade
            $uasg = "";
            $procOri = "";
            $geraContrato = 0;
        }

        $query = "";
        if ($idLic == 0) {
            $query = "INSERT INTO licitacao VALUES(NULL, {$pedido}, {$tipo}, '{$numero}', '{$uasg}', '{$procOri}', {$geraContrato});";
        } else {
            $query = "UPDATE licitacao SET tipo = {$tipo}, numero = '{$numero}', uasg = '{$uasg}', processo_original = '{$procOri}', gera_contrato = {$geraContrato} WHERE id = {$idLic};";
        }

        Query::getInstance()->exe($query);
        return true;
    }

    /**
     *   Função para enviar um pedido ao SOF
     *
     *   @access public
     * 	 @param $id_item Array com os ids dos itens do pedido.
     *   @param $qtd Array com as quantidades dos itens do pedido.
     *   @param $valor Array com os valores dos itens do pedido.
     *   @param $pedido Id do pedido. Se 0, pedido novo, senão editando rascunho ou enviando ao SOF.
     *   @return bool
     */
    public function insertPedido($id_user, $id_setor, $id_item, $qtd_solicitada, $qtd_disponivel, $qtd_contrato, $qtd_utilizado, $vl_saldo, $vl_contrato, $vl_utilizado, $valor, $total_pedido, $saldo_total, $prioridade, $obs, &$pedido, $pedido_contrato) {

        $obs = Query::getInstance()->real_escape_string($obs);
        $hoje = date('Y-m-d');
        $mes = date("n");

        if ($prioridade == 5) {
            if ($pedido == 0) {
                // NOVO
                //inserindo os dados iniciais do pedido
                $query_pedido = Query::getInstance()->exe("INSERT INTO pedido VALUES(NULL, {$id_setor}, {$id_user}, '{$hoje}', '{$mes}', 1, {$prioridade}, 1, '{$total_pedido}', '{$obs}', {$pedido_contrato}, 0);");
                $pedido = Query::getInstance()->getInsertId();
            } else {
                //remover resgistros antigos do rascunho
                Query::getInstance()->exe("DELETE FROM itens_pedido WHERE id_pedido = " . $pedido);
                Query::getInstance()->exe("UPDATE pedido SET data_pedido = '{$hoje}', ref_mes = {$mes}, prioridade = {$prioridade}, valor = '{$total_pedido}', obs = '{$obs}', pedido_contrato = {$pedido_contrato}, aprov_gerencia = 0 WHERE id = " . $pedido);
            }
            //inserindo os itens do pedido
            for ($i = 0; $i < count($id_item); $i++) {
                Query::getInstance()->exe("INSERT INTO itens_pedido VALUES(NULL, {$pedido}, {$id_item[$i]}, {$qtd_solicitada[$i]}, '{$valor[$i]}');");
            }
        } else {
            // atualiza saldo
            Query::getInstance()->exe("UPDATE saldo_setor SET saldo = '{$saldo_total}' WHERE id_setor = " . $id_setor);
            // enviado ao sof
            if ($pedido == 0) {
                //inserindo os dados iniciais do pedido
                $query_pedido = Query::getInstance()->exe("INSERT INTO pedido VALUES(NULL, {$id_setor}, {$id_user}, '{$hoje}', '{$mes}', 0, {$prioridade}, 2, '{$total_pedido}', '{$obs}', {$pedido_contrato}, 0);");
                $pedido = Query::getInstance()->getInsertId();
            } else {
                // atualizando pedido
                Query::getInstance()->exe("UPDATE pedido SET data_pedido = '{$hoje}', ref_mes = {$mes}, alteracao = 0, prioridade = {$prioridade}, status = 2, valor = '{$total_pedido}', obs = '{$obs}', pedido_contrato = {$pedido_contrato}, aprov_gerencia = 0 WHERE id = " . $pedido);
            }
            //remover resgistros antigos do pedido
            Query::getInstance()->exe("DELETE FROM itens_pedido WHERE id_pedido = " . $pedido);
            // alterando infos dos itens solicitados
            for ($i = 0; $i < count($id_item); $i++) {
                Query::getInstance()->exe("INSERT INTO itens_pedido VALUES(NULL, {$pedido}, {$id_item[$i]}, {$qtd_solicitada[$i]}, '{$valor[$i]}');");
                // qtd_disponivel == qt_saldo
                $qtd_disponivel[$i] -= $qtd_solicitada[$i];
                $qtd_utilizado[$i] += $qtd_solicitada[$i];
                if ($vl_saldo[$i] == 0) {
                    $vl_saldo[$i] = $vl_contrato[$i];
                }
                $vl_saldo[$i] -= $valor[$i];
                $vl_utilizado[$i] += $valor[$i];
                Query::getInstance()->exe("UPDATE itens SET qt_saldo = {$qtd_disponivel[$i]}, qt_utilizado = {$qtd_utilizado[$i]}, vl_saldo = '{$vl_saldo[$i]}', vl_utilizado = '{$vl_utilizado[$i]}' WHERE id = {$id_item[$i]};");
            }
        }
        return true;
    }

    /**
     * 	Função para analisar um pedido, enviar comentários, alterar status, desativar itens
     * 	cancelados, retornar para o setor
     *
     * 	@param $id_item -> array com os ids dos itens utilizados no pedido
     * 	@param $item_cancelado -> cada posição está associada ao array $id_item, se na posição x $id_item estiver 1, então o item na posição x de $id_item foi cancelado
     * 	@return bool
     */
    public function pedidoAnalisado($id_pedido, $fase, $prioridade, $id_item, $item_cancelado, $qtd_solicitada, $qt_saldo, $qt_utilizado, $vl_saldo, $vl_utilizado, $valor_item, $saldo_setor, $total_pedido, $comentario) {
        $query = Query::getInstance()->exe('SELECT id_setor FROM pedido WHERE id = ' . $id_pedido);
        // selecionando o id do setor que fez o pedido
        $obj_id = $query->fetch_object();
        $id_setor = $obj_id->id_setor;
        $hoje = date('Y-m-d');
        // verificando itens cancelados, somente quando passam pela análise
        if ($fase <= 4) {
            if (in_array(1, $item_cancelado) || in_array(true, $item_cancelado) || $fase == 3) {
                // só percorre os itens se tiver algum item cancelado ou se o pedido for reprovado
                for ($i = 0; $i < count($id_item); $i++) {
                    $qt_saldo[$i] += $qtd_solicitada[$i];
                    $qt_utilizado[$i] -= $qtd_solicitada[$i];
                    $vl_saldo[$i] += $valor_item[$i];
                    $vl_utilizado[$i] -= $valor_item[$i];
                    $cancelado = '';
                    if ($item_cancelado[$i]) {
                        $cancelado = ', cancelado = 1';
                        Query::getInstance()->exe('DELETE FROM itens_pedido WHERE id_pedido = ' . $id_pedido . ' AND id_item = ' . $id_item[$i]);
                        $total_pedido -= $valor_item[$i];
                    }
                    Query::getInstance()->exe("UPDATE itens SET qt_saldo = '{$qt_saldo[$i]}', qt_utilizado = '{$qt_utilizado[$i]}', vl_saldo = '{$vl_saldo[$i]}', vl_utilizado = '{$vl_utilizado[$i]}'{$cancelado} WHERE id = " . $id_item[$i]);
                    $saldo_setor += $valor_item[$i];
                }
            }
        }
        // alterar o status do pedido
        $alteracao = 0;
        if ($fase == 2 || $fase == 3 || $fase == 4) {
            $total_pedido = number_format($total_pedido, 3, '.', '');
            Query::getInstance()->exe("UPDATE pedido SET valor = '" . $total_pedido . "' WHERE id = " . $id_pedido);
            if ($fase == 3) {
                // reprovado
                $alteracao = 1;
                $prioridade = 5;
            } else if ($fase == 4) {
                // aprovado
                Query::getInstance()->exe("INSERT INTO saldos_lancamentos VALUES(NULL, {$id_setor}, '{$hoje}', '-{$total_pedido}', 4);");
                // próxima fase
                $fase++;
            }
            $saldo_setor = number_format($saldo_setor, 3, '.', '');
            Query::getInstance()->exe("UPDATE saldo_setor SET saldo = '{$saldo_setor}' WHERE id_setor = {$id_setor};");
        }
        Query::getInstance()->exe("UPDATE pedido SET status = " . $fase . ", prioridade = " . $prioridade . ", alteracao = " . $alteracao . " WHERE id = " . $id_pedido);
        if (strlen($comentario) > 0) {
            // inserindo comentário da análise
            $comentario = Query::getInstance()->real_escape_string($comentario);
            $query_vl = Query::getInstance()->exe("SELECT valor FROM pedido WHERE id = " . $id_pedido);
            $obj_tot = $query_vl->fetch_object();
            Query::getInstance()->exe("INSERT INTO comentarios VALUES(NULL, {$id_pedido}, '{$hoje}', {$prioridade}, {$fase}, '{$obj_tot->valor}', '{$comentario}');");
        }
        return true;
    }

    /**
     * 	Função para deletar um pedido (rascunhos).
     */
    public function deletePedido(int $id_pedido): string {
        Query::getInstance()->exe('DELETE FROM licitacao WHERE licitacao.id_pedido = ' . $id_pedido);
        Query::getInstance()->exe("DELETE FROM pedido_contrato WHERE pedido_contrato.id_pedido = " . $id_pedido);
        Query::getInstance()->exe("DELETE FROM pedido_grupo WHERE pedido_grupo.id_pedido = " . $id_pedido);
        Query::getInstance()->exe("DELETE FROM comentarios WHERE comentarios.id_pedido = " . $id_pedido);
        Query::getInstance()->exe("DELETE FROM itens_pedido WHERE itens_pedido.id_pedido = " . $id_pedido);
        Query::getInstance()->exe("DELETE FROM pedido_empenho WHERE pedido_empenho.id_pedido = " . $id_pedido);
        Query::getInstance()->exe("DELETE FROM pedido_fonte WHERE pedido_fonte.id_pedido = " . $id_pedido);
        Query::getInstance()->exe("DELETE FROM solic_alt_pedido WHERE solic_alt_pedido.id_pedido = " . $id_pedido);
        Query::getInstance()->exe("DELETE FROM pedido_log_status WHERE pedido_log_status.id_pedido = " . $id_pedido);
        Query::getInstance()->exe("DELETE FROM pedido WHERE pedido.id = " . $id_pedido);
        return "true";
    }

}
