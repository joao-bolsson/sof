<?php

/**
 *  Classe com as funções de cadastrados utilizadas pelo arquivo php/geral.php
 *  toda função de ENTRADA de dados no banco devem ficar nesta classe
 *
 *  @author João Bolsson
 *  @since 2016, 16 Mar
 */
ini_set('display_erros', true);
error_reporting(E_ALL);

include_once 'Conexao.class.php';
include_once 'Busca.class.php';

class Geral extends Conexao {

    private $mysqli;

    function __construct() {
        //chama o método contrutor da classe Conexao
        parent::__construct();
        $this->mysqli = parent::getConexao();
        $this->obj_Busca = new Busca();
    }

    /**
     * Insere um grupo ao pedido.
     * @param int $pedido Id do pedido.
     * @param int $grupo id do grupo para inserir ao pedido.
     */
    public function insertGrupoPedido(int $pedido, int $grupo) {
        $this->mysqli = parent::getConexao();
        $this->mysqli->query("INSERT INTO pedido_grupo VALUES({$pedido}, {$grupo});") or exit("Ocorreu um erro ao cadastrar o grupo do pedido. Contate o administrador.");
        $this->mysqli->close();
    }

    /**
     * 	Funçao para mudar o status do pedido para 'Enviado ao Fornecedor' (UA)
     *
     * 	@access public
     * 	@param $id_pedido Id do pedido.
     */
    public function enviaFornecedor(int $id_pedido) {
        $this->mysqli = parent::getConexao();
        $this->mysqli->query("UPDATE pedido SET status = 9 WHERE id = {$id_pedido};");
        // registra log
        $hoje = date('Y-m-d');
        $this->mysqli->query("INSERT INTO pedido_log_status VALUES({$id_pedido}, 9, '{$hoje}');");
        $this->mysqli->close();
    }

    /**
     * 	Função para enviar um pedido ao ordenador.
     *
     * 	@access public
     * 	@return if success - true, else false.
     */
    public function enviaOrdenador(int $id_pedido): bool {
        $query = $this->mysqli->query("UPDATE pedido SET status = '8' WHERE id = {$id_pedido};");
        if ($query) {
            // registra log
            $hoje = date('Y-m-d');
            $this->mysqli->query("INSERT INTO pedido_log_status VALUES({$id_pedido}, 8, '{$hoje}');");
            return true;
        }
        return false;
    }

    /**
     * 	Função para cadastrar fontes do pedido (status == Aguarda Orçamento)
     *
     * 	@access public
     * 	@return If inserts all datas - true, else false.
     */
    public function cadastraFontes(int $id_pedido, string $fonte, string $ptres, string $plano): bool {
        $fonte = $this->mysqli->real_escape_string($fonte);
        $ptres = $this->mysqli->real_escape_string($ptres);
        $plano = $this->mysqli->real_escape_string($plano);

        $fonte = str_replace("\"", "'", $fonte);
        $ptres = str_replace("\"", "'", $ptres);
        $plano = str_replace("\"", "'", $plano);
        $query = $this->mysqli->query("INSERT INTO pedido_fonte VALUES(NULL, {$id_pedido}, \"{$fonte}\", \"{$ptres}\", \"{$plano}\");");

        if ($query) {
            // muda o status do pedido -> Aguarda SIAFI
            $query_st = $this->mysqli->query("UPDATE pedido SET status = '6' WHERE id = {$id_pedido};");
            if ($query_st) {
                // registra log
                $hoje = date('Y-m-d');
                $this->mysqli->query("INSERT INTO pedido_log_status VALUES({$id_pedido}, 6, '{$hoje}');");
                return true;
            }
            return false;
        }
        return false;
    }

    /**
     * 	Função para resetar o sistema para o estado orinal
     *
     * 	@access public
     */
    public function resetSystem() {
        // DELETE
        $this->mysqli->query("DELETE FROM comentarios;");
        $this->mysqli->query("DELETE FROM itens_pedido;");
        $this->mysqli->query("DELETE FROM pedido_empenho;");
        $this->mysqli->query("DELETE FROM pedido_fonte;");
        $this->mysqli->query("DELETE FROM processos;");
        $this->mysqli->query("DELETE FROM saldo_setor;");
        $this->mysqli->query("DELETE FROM saldos_adiantados;");
        $this->mysqli->query("DELETE FROM saldos_lancamentos;");
        $this->mysqli->query("DELETE FROM saldos_transferidos;");
        $this->mysqli->query("DELETE FROM solic_alt_pedido;");
        $this->mysqli->query("DELETE FROM itens;");
        $this->mysqli->query("DELETE FROM licitacao;");
        $this->mysqli->query("DELETE FROM pedido_log_status;");
        $this->mysqli->query("DELETE FROM pedido;");

        // ALTER TABLE

        $this->mysqli->query("alter table comentarios auto_increment = 1;");
        $this->mysqli->query("alter table itens_pedido auto_increment=1;");
        $this->mysqli->query("alter table pedido_empenho auto_increment = 1;");
        $this->mysqli->query("alter table pedido_fonte auto_increment = 1;");
        $this->mysqli->query("alter table processos auto_increment = 1;");
        $this->mysqli->query("alter table saldo_setor auto_increment = 1;");
        $this->mysqli->query("alter table saldos_adiantados auto_increment = 1;");
        $this->mysqli->query("alter table saldos_lancamentos auto_increment = 1;");
        $this->mysqli->query("alter table saldos_transferidos auto_increment = 1;");
        $this->mysqli->query("alter table solic_alt_pedido auto_increment = 1;");
        $this->mysqli->query("alter table itens auto_increment = 1;");
        $this->mysqli->query("alter table licitacao auto_increment = 1;");
        $this->mysqli->query("alter table pedido_log_status auto_increment = 1;");
        $this->mysqli->query("alter table pedido auto_increment = 1;");
    }

    /**
     * 	Função para os usuários relatarem problemas no site
     *
     * 	@access public
     */
    public function insereProblema(int $id_setor, string $assunto, string $descricao) {
        $assunto = $this->mysqli->real_escape_string($assunto);
        $descricao = $this->mysqli->real_escape_string($descricao);
        $sql = "INSERT INTO problemas VALUES(NULL, " . $id_setor . ", '" . $assunto . "', '" . $descricao . "');";
        $query = $this->mysqli->query($sql);
    }

    /**
     * 	Função para editar informações de um item
     *
     * 	@access public
     * 	@param $obj_dados Objeto com as informações para edição
     * 	@return bool
     */
    public function editItem($dados) {
        $query_qtd = $this->mysqli->query("SELECT sum(itens_pedido.qtd) AS soma FROM itens_pedido where itens_pedido.id_item = {$dados->idItem};");
        if ($query_qtd->num_rows > 0) {
            $obj_qtd = $query_qtd->fetch_object();
            $soma = $obj_qtd->soma;
            if ($dados->qtContrato < $soma || $dados->qtUtilizada < $soma) {
                return false;
            }
        }
        $dados->compItem = $this->mysqli->real_escape_string($dados->compItem);
        $this->mysqli->query("UPDATE itens SET itens.complemento_item = '{$dados->compItem}', itens.vl_unitario = '{$dados->vlUnitario}', itens.qt_contrato = {$dados->qtContrato}, itens.qt_utilizado = {$dados->qtUtilizada}, itens.vl_utilizado = '{$dados->vlUtilizado}', itens.qt_saldo = {$dados->qtSaldo}, itens.vl_saldo = '{$dados->vlSaldo}' WHERE itens.id = {$dados->idItem};");

        // seleciona infos dos pedidos que contém o item editado e que não passaram da análise
        $query = $this->mysqli->query("SELECT itens_pedido.id_pedido, itens_pedido.qtd, itens_pedido.valor AS valor_item, pedido.id_setor, pedido.valor AS valor_pedido, saldo_setor.saldo FROM itens_pedido, pedido, saldo_setor WHERE saldo_setor.id_setor = pedido.id_setor AND itens_pedido.id_item = {$dados->idItem} AND itens_pedido.id_pedido = pedido.id AND pedido.status <= 2;");

        $saldo_setor = 0;
        while ($obj = $query->fetch_object()) {
            $valorItem = $obj->qtd * $dados->vlUnitario;
            $this->mysqli->query("UPDATE itens_pedido SET itens_pedido.valor = '{$valorItem}' WHERE itens_pedido.id_item = {$dados->idItem} AND itens_pedido.id_pedido = {$obj->id_pedido};");
            $saldo_setor = $obj->saldo + $obj->valor_item;
            $saldo_setor -= $valorItem;
            $saldo_setor = number_format($saldo_setor, 3, '.', '');
            // alterando o saldo do setor
            $this->mysqli->query("UPDATE saldo_setor SET saldo_setor.saldo = '{$saldo_setor}' WHERE saldo_setor.id_setor = {$obj->id_setor};");
            $valorPedido = $obj->valor_pedido - $obj->valor_item;
            $valorPedido += $valorItem;
            $valorPedido = number_format($valorPedido, 3, '.', '');
            // alterando o valor total do pedido
            $this->mysqli->query("UPDATE pedido SET pedido.valor = '{$valorPedido}' WHERE pedido.id = {$obj->id_pedido};");
        }

        $this->mysqli->close();
        return true;
    }

    /**
     * 	Função para alterar somente o status do pedido
     *
     * 	@access public
     * 	@param $id_pedido Id do pedido
     * 	@param $id_setor Id do setor que fez o pedido
     * 	@param $comentario Comentário do SOF.
     * 	@param $status Novo status do pedido
     * 	@return bool
     */
    public function altStatus($id_pedido, $id_setor, $comentario, $status): bool {
        $update = $this->mysqli->query("UPDATE pedido SET status = {$status} WHERE id = {$id_pedido};");
        if (!$update) {
            return false;
        }
        $obj = $this->mysqli->query("SELECT pedido.prioridade, pedido.valor FROM pedido WHERE id = {$id_pedido};")->fetch_object();
        $hoje = date('Y-m-d');
        // registra log
        $this->mysqli->query("INSERT INTO pedido_log_status VALUES({$id_pedido}, {$status}, '{$hoje}');");
        if (strlen($comentario) > 0) {
            $comentario = $this->mysqli->real_escape_string($comentario);
            $comment = $this->mysqli->query("INSERT INTO comentarios VALUES(NULL, {$id_pedido}, '{$hoje}', {$obj->prioridade}, {$status}, '{$obj->valor}', '{$comentario}');");
            if (!$comment) {
                return false;
            }
        }
        $this->mysqli->close();
        return true;
    }

    /**
     * 	Função que cadastra o empenho de um pedido
     *
     * 	@access public
     * @param $id_pedido Id do pedido
     * 	@param $empenho Empenho a ser cadastrado
     * 	@return bool
     */
    public function cadastraEmpenho($id_pedido, $empenho, $data): bool {
        $empenho = $this->mysqli->real_escape_string($empenho);
        // verifica se o pedido ja não possui empenho
        $query_check = $this->mysqli->query("SELECT pedido_empenho.id FROM pedido_empenho WHERE pedido_empenho.id = {$id_pedido};");
        $sql = "";
        if ($query_check->num_rows < 1) {
            // cadastrando empenho
            $sql = "INSERT INTO pedido_empenho VALUES(NULL, {$id_pedido}, '{$empenho}', '{$data}');";
        } else {
            // alterando empenho
            $sql = "UPDATE pedido_empenho SET pedido_empenho.empenho = '{$empenho}', pedido_empenho.data = '{$data}' WHERE pedido_empenho.id_pedido = {$id_pedido};";
        }
        $query_cad = $this->mysqli->query($sql);
        if (!$query_cad) {
            return false;
        }
        // mudando status do pedido
        $query = $this->mysqli->query("UPDATE pedido SET status = 7 WHERE id = {$id_pedido};");
        if (!$query) {
            // registra log
            $hoje = date('Y-m-d');
            $this->mysqli->query("INSERT INTO pedido_log_status VALUES({$id_pedido}, 7, '{$hoje}');");
            return false;
        }
        return true;
    }

    /**
     * 	Função que transfere um valor do saldo de um setor para outro
     *
     * 	@access public
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
        $query_saldo_ori = $this->mysqli->query("SELECT saldo FROM saldo_setor WHERE id_setor = {$ori};");
        if ($query_saldo_ori->num_rows < 1) {
            $this->mysqli->query("INSERT INTO saldo_setor VALUES(NULL, {$ori}, '0.000');");
        } else {
            $obj = $query_saldo_ori->fetch_object();
            $saldo_ori = $obj->saldo;
        }

        if ($valor > $saldo_ori) {
            return false;
        }
        $valor = number_format($valor, 3, '.', '');
        // registrando a transferência
        $justificativa = $this->mysqli->real_escape_string($justificativa);
        $this->mysqli->query("INSERT INTO saldos_transferidos VALUES(NULL, {$ori}, {$dest}, '{$valor}', '{$justificativa}');");
        // registrando na tabela de lançamentos
        $hoje = date('Y-m-d');
        $this->mysqli->query("INSERT INTO saldos_lancamentos VALUES(NULL, {$ori}, '{$hoje}', '-{$valor}', 3), (NULL, {$dest}, '{$hoje}', '{$valor}', 3);");
        // atualizando o saldos dos setores
        // atualizando o saldo do setor origem
        $saldo_ori -= $valor;
        $saldo_ori = number_format($saldo_ori, 3, '.', '');
        $this->mysqli->query("UPDATE saldo_setor SET saldo = '{$saldo_ori}' WHERE id_setor = {$ori};");
        // atualizando o saldo do setor destino
        $saldo_dest = '0';
        // selecionando o saldo do setor destino
        $query_saldo_dest = $this->mysqli->query("SELECT saldo FROM saldo_setor WHERE id_setor = {$dest};");
        if ($query_saldo_dest->num_rows < 1) {
            $this->mysqli->query("INSERT INTO saldo_setor VALUES(NULL, {$dest}, '0.000');");
        } else {
            $obj = $query_saldo_dest->fetch_object();
            $saldo_dest = $obj->saldo;
        }
        $saldo_dest += $valor;
        $saldo_dest = number_format($saldo_dest, 3, '.', '');
        $this->mysqli->query("UPDATE saldo_setor SET saldo = '{$saldo_dest}' WHERE id_setor = {$dest};");
        return true;
    }

    /**
     * 	Função para cadastrar novo tipo de processo.
     *
     * 	@access public
     * 	@return bool.
     */
    public function newTypeProcess($tipo): bool {
        $tipo = $this->mysqli->real_escape_string($tipo);
        $insert = $this->mysqli->query("INSERT INTO processos_tipo VALUES(NULL, '{$tipo}');");
        if ($insert) {
            return true;
        }
        return false;
    }

    /**
     * 	Função para cadastrar/editar um processo
     *
     * 	@access public
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
            $dados[$i] = $this->mysqli->real_escape_string($dados[$i]);
        }
        if ($dados[0] == 0) {
            //  ["id_processo", "num_processo", "tipo", "estante", "prateleira", "entrada", "saida", "responsavel", "retorno", "obs"];
            // INSERT
            $query = $this->mysqli->query("INSERT INTO processos VALUES(NULL, '{$dados[1]}', '{$dados[2]}', '{$dados[3]}', '{$dados[4]}', '{$dados[5]}', '{$dados[6]}', '{$dados[7]}', '{$dados[8]}', '{$dados[9]}');");
            if ($query) {
                return true;
            }
            return false;
        } else {
            $query = $this->mysqli->query("UPDATE processos SET num_processo = '{$dados[1]}', tipo = '{$dados[2]}', estante = '{$dados[3]}', prateleira = '{$dados[4]}', entrada = '{$dados[5]}', saida = '{$dados[6]}', responsavel = '{$dados[7]}', retorno = '{$dados[8]}', obs = '{$dados[9]}' WHERE id = {$dados[0]};");
            if ($query) {
                return true;
            }
            return false;
        }
    }

    /**
     * 	Função que importa itens por SQL
     *
     * 	@access public
     * 	@return bool
     */
    public function importaItens(array $array_sql): bool {
        $len = count($array_sql);
        for ($i = 0; $i < $len; $i++) {
            $query = $array_sql[$i];
            $this->mysqli->query($query);
        }
        $this->mysqli->close();
        return true;
    }

    /**
     *  Função para dar update numa senha de acordo com o email
     *
     *  @access public
     *  @return string
     */
    public function resetSenha($email, $senha) {
        // evita SQL Injections
        $email = $this->mysqli->real_escape_string($email);
        // verificando se o e-mail consta no sistema
        $query_email = $this->mysqli->query("SELECT id FROM usuario WHERE email = '{$email}';");
        if ($query_email->num_rows == 1) {
            $id = $query_email->fetch_object()->id;
            // criptografando a senha
            $senha = crypt($senha);
            $query = $this->mysqli->query("UPDATE usuario SET senha = '{$senha}' WHERE id = {$id};");
            if ($query) {
                return "Sucesso";
            }
            return false;
        }
        return false;
    }

    /**
     * 	Função usada para o usuário alterar a sua senha
     *
     * 	@access public
     * 	@return bool
     */
    public function altInfoUser($id_user, $nome, $email, $novaSenha, $senhaAtual): bool {
        $query = $this->mysqli->query("SELECT senha FROM usuario WHERE id = {$id_user};")->fetch_object();
        if (crypt($senhaAtual, $query->senha) == $query->senha) {
            $nome = $this->mysqli->real_escape_string($nome);
            $email = $this->mysqli->real_escape_string($email);
            $novaSenha = crypt($novaSenha);
            $altera = $this->mysqli->query("UPDATE usuario SET nome = '{$nome}', email = '{$email}', senha = '{$novaSenha}' WHERE id = {$id_user};");
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
     * 	Função que analisa as solicitações de alteração de pedido
     *
     * 	@access public
     * 	@return bool
     */
    public function analisaSolicAlt(int $id_solic, int $id_pedido, int $acao): bool {
        // TODO: revisar log
        $hoje = date('Y-m-d');
        $this->mysqli->query("UPDATE solic_alt_pedido SET data_analise = '{$hoje}', status = {$acao} WHERE id = {$id_solic};");
        if ($acao) {
            $this->mysqli->query("UPDATE pedido SET alteracao = {$acao}, prioridade = 5, status = 1 WHERE id = {$id_pedido};");
            // registra log
            $this->mysqli->query("INSERT INTO pedido_log_status VALUES({$id_pedido}, 1, '{$hoje}');");
        } else {
            // registra log
            $this->mysqli->query("INSERT INTO pedido_log_status VALUES({$id_pedido}, {$status}, '{$hoje}');");
        }
        $this->mysqli->close();
        return true;
    }

    /**
     * 	Função que envia uma solicitação de alteração de pedido ao SOF.
     *
     * 	@access public
     * 	@return Uma mesagem expressando o resultado da solicitação.
     */
    public function solicAltPedido(int $id_pedido, int $id_setor, string $justificativa): string {
        $hoje = date('Y-m-d');
        $justificativa = $this->mysqli->real_escape_string($justificativa);
        $solicita = $this->mysqli->query("INSERT INTO solic_alt_pedido VALUES(NULL, {$id_pedido}, {$id_setor}, '{$hoje}', NULL, '{$justificativa}', 2);");
        if ($solicita) {
            return "Sua solicitação será análisada. Caso seja aprovada, seu pedido estará na sessão 'Rascunhos'";
        }
        return "Não foi possível fazer essa solicitação. Contate o administrador.";
    }

    /**
     * 	Função para liberação de saldo de um setor
     *
     * 	@access public
     * 	@param $id_setor Comment.
     * 	@param $valor Comment.
     * 	@param $saldo_atual Comment.
     * 	@return bool
     */
    public function liberaSaldo($id_setor, $valor, $saldo_atual): bool {
        $saldo = $saldo_atual + $valor;
        $saldo = number_format($saldo, 3, '.', '');
        $verifica = $this->mysqli->query("SELECT saldo_setor.id FROM saldo_setor WHERE saldo_setor.id_setor = {$id_setor};");
        if ($verifica->num_rows < 1) {
            $query = $this->mysqli->query("INSERT INTO saldo_setor VALUES(NULL, {$id_setor}, '0.000');");
        }
        $update = $this->mysqli->query("UPDATE saldo_setor SET saldo = '{$saldo}' WHERE id_setor = {$id_setor};");
        if ($id_setor != 2) {
            $saldo_sof = $this->obj_Busca->getSaldo(2) - $valor;
            $saldo_sof = number_format($saldo_sof, 3, '.', '');
            $this->mysqli->query("UPDATE saldo_setor SET saldo = '{$saldo_sof}' WHERE id_setor = 2;");
        }
        $hoje = date('Y-m-d');
        $insert = $this->mysqli->query("INSERT INTO saldos_lancamentos VALUES(NULL, {$id_setor}, '{$hoje}', '{$valor}', 1);");
        if ($insert) {
            $this->mysqli->close();
            return true;
        }
        return false;
    }

    /**
     * 	Função para aprovar uma solicitação de adiantamento
     *
     * 	@access public
     * 	@param $acao 0 -> reprovado | 1 -> aprovado
     * 	@return bool
     */
    public function analisaAdi(int $id, int $acao): bool {
        $hoje = date('Y-m-d');

        $this->mysqli->query("UPDATE saldos_adiantados SET data_analise = '{$hoje}', status = {$acao} WHERE id = {$id};");
        if (!$acao) {
            // se reprovado retorna
            return true;
        }
        $obj = $this->mysqli->query("SELECT saldos_adiantados.id_setor, saldo_setor.saldo + saldos_adiantados.valor_adiantado AS saldo_final, saldos_adiantados.valor_adiantado FROM saldo_setor, saldos_adiantados WHERE saldos_adiantados.id = {$id} AND saldo_setor.id_setor = saldos_adiantados.id_setor;")->fetch_object();
        $obj->saldo_final = number_format($obj->saldo_final, 3, '.', '');
        // fazendo o lançamento da operação
        $this->mysqli->query("INSERT INTO saldos_lancamentos VALUES(NULL, {$obj->id_setor}, '{$hoje}', '{$obj->valor_adiantado}', 2);");
        $this->mysqli->query("UPDATE saldo_setor SET saldo = '{$obj->saldo_final}' WHERE id_setor = {$obj->id_setor};");

        return true;
    }

    /**
     * 	Função para enviar um pedido de adiantamento de saldo para o SOF
     *
     * 	@access public
     * 	@return bool
     */
    public function solicitaAdiantamento(int $id_setor, string $valor, string $justificativa): bool {
        $valor = $this->mysqli->real_escape_string($valor);
        $justificativa = $this->mysqli->real_escape_string($justificativa);
        $hoje = date('Y-m-d');
        $valor = number_format($valor, 3, '.', '');
        $insere = $this->mysqli->query("INSERT INTO saldos_adiantados VALUES(NULL, {$id_setor}, '{$hoje}', NULL, '{$valor}', '{$justificativa}', 2);");

        if ($insere) {
            return true;
        }
        return false;
    }

    /**
     *   Função para alterar a senha de um usuário
     *
     *   @access public
     *   @return bool
     */
    public function updateSenha($id_user, $senha): bool {
        $update = $this->mysqli->query("UPDATE usuario SET senha = '{$senha}' WHERE id = {$id_user}");
        if ($update) {
            return true;
        }
        return false;
    }

    /**
     * Função para inserir postagem
     *
     * @access public
     * @return int
     */
    public function setPost($data, $postagem, $pag) {
        //escapando string especiais para evitar SQL Injections

        $data = $this->mysqli->real_escape_string($data);
        $postagem = $this->mysqli->real_escape_string($postagem);
        $pag = $this->mysqli->real_escape_string($pag);
        //dizendo ao banco que foi adicionada mais uma notícia e o local onde ela deve ser mostrada

        $inicio = strpos($postagem, "<h3");
        $fim = strpos($postagem, "</h3>");
        $titulo = strip_tags(substr($postagem, $inicio, $fim));

        $query_post = $this->mysqli->query("INSERT INTO postagens
          VALUES(NULL, {$pag}, '{$titulo}', '{$data}', 1, '{$postagem}');");

        return true;
    }

    /**
     *   Função para editar uma postagem
     *
     *   @access public
     *   @return bool
     */
    public function editPost($data, $id, $postagem, $pag) {
        //escapando string especiais para evitar SQL Injections
        $postagem = $this->mysqli->real_escape_string($postagem);

        $inicio = strpos($postagem, "<h3");
        $fim = strpos($postagem, "</h3>");
        $titulo = strip_tags(substr($postagem, $inicio, $fim));

        //alterando a tabela
        $update = $this->mysqli->query("UPDATE postagens SET tabela = {$pag}, titulo = '{$titulo}', data = '{$data}', postagem = '{$postagem}' WHERE id = {$id};");

        return true;
    }

    /**
     *   Função para excluir uma publicação
     *       a publicação não é totalmente excluída, apenas o sistema passará a não mostrá-la
     *
     *   @access public
     *   @return bool
     */
    public function excluirNoticia($id) {
        //escapando string especiais para evitar SQL Injections
        $id = $this->mysqli->real_escape_string($id);

        $query = $this->mysqli->query("UPDATE postagens SET ativa = 0 WHERE id = {$id};");
        if ($query) {
            $obj = $this->mysqli->query("SELECT postagens.tabela FROM postagens WHERE postagens.id = {$id};")->fetch_object();
            return $obj->tabela;
        }
        return false;
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

        $this->mysqli->query($query) or exit("Ocorreu um erro no cadastro da licitação. Contate o administrador.");
        $this->mysqli->close();
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
    public function insertPedido($id_user, $id_setor, $id_item, $qtd_solicitada, $qtd_disponivel, $qtd_contrato, $qtd_utilizado, $vl_saldo, $vl_contrato, $vl_utilizado, $valor, $total_pedido, $saldo_total, $prioridade, $obs, &$pedido) {

        $obs = $this->mysqli->real_escape_string($obs);
        $hoje = date('Y-m-d');
        $mes = date("n");

        if ($prioridade == 5) {
            if ($pedido == 0) {
                // NOVO
                //inserindo os dados iniciais do pedido
                $query_pedido = $this->mysqli->query("INSERT INTO pedido VALUES(NULL, {$id_setor}, {$id_user}, '{$hoje}', '{$mes}', 1, {$prioridade}, 1, '{$total_pedido}', '{$obs}');") or exit("Ocorreu um erro ao inserir o pedido.");
                $pedido = $this->mysqli->insert_id;
            } else {
                //remover resgistros antigos do rascunho
                $this->mysqli->query("DELETE FROM itens_pedido WHERE id_pedido = {$pedido};") or exit("Ocorreu um erro ao remover os registros antigos do pedido.");
                $this->mysqli->query("UPDATE pedido SET data_pedido = '{$hoje}', ref_mes = {$mes}, prioridade = {$prioridade}, valor = '{$total_pedido}', obs = '{$obs}' WHERE id = {$pedido};") or exit("Ocorreu um erro ao atualizar o pedido.");
            }
            //inserindo os itens do pedido
            for ($i = 0; $i < count($id_item); $i++) {
                $this->mysqli->query("INSERT INTO itens_pedido VALUES(NULL, {$pedido}, {$id_item[$i]}, {$qtd_solicitada[$i]}, '{$valor[$i]}');") or exit("Ocorreu um erro ao inserir um item no pedido.");
            }
        } else {
            // atualiza saldo
            $this->mysqli->query("UPDATE saldo_setor SET saldo = '{$saldo_total}' WHERE id_setor = {$id_setor};") or exit("Ocorreu um erro ao atualizar o saldo do setor.");
            // enviado ao sof
            if ($pedido == 0) {
                //inserindo os dados iniciais do pedido
                $query_pedido = $this->mysqli->query("INSERT INTO pedido VALUES(NULL, {$id_setor}, {$id_user}, '{$hoje}', '{$mes}', 0, {$prioridade}, 2, '{$total_pedido}', '{$obs}');") or exit("Ocorreu um erro ao inserir os dados iniciais do pedido.");
                $pedido = $this->mysqli->insert_id;
            } else {
                // atualizando pedido
                $this->mysqli->query("UPDATE pedido SET data_pedido = '{$hoje}', ref_mes = {$mes}, alteracao = 0, prioridade = {$prioridade}, status = 2, valor = '{$total_pedido}', obs = '{$obs}' WHERE id = {$pedido};") or exit("Ocorreu um erro ao atualizar o pedido existente.");
            }
            //remover resgistros antigos do pedido
            $this->mysqli->query("DELETE FROM itens_pedido WHERE id_pedido = {$pedido};") or exit("Ocorreu um erro ao remover os itens antigos do pedido existente.");
            // alterando infos dos itens solicitados
            for ($i = 0; $i < count($id_item); $i++) {
                $this->mysqli->query("INSERT INTO itens_pedido VALUES(NULL, {$pedido}, {$id_item[$i]}, {$qtd_solicitada[$i]}, '{$valor[$i]}');") or exit("Ocorreu um erro ao inserir um item ao pedido existente.");
                // qtd_disponivel == qt_saldo
                $qtd_disponivel[$i] -= $qtd_solicitada[$i];
                $qtd_utilizado[$i] += $qtd_solicitada[$i];
                if ($vl_saldo[$i] == 0) {
                    $vl_saldo[$i] = $vl_contrato[$i];
                }
                $vl_saldo[$i] -= $valor[$i];
                $vl_utilizado[$i] += $valor[$i];
                $this->mysqli->query("UPDATE itens SET qt_saldo = {$qtd_disponivel[$i]}, qt_utilizado = {$qtd_utilizado[$i]}, vl_saldo = '{$vl_saldo[$i]}', vl_utilizado = '{$vl_utilizado[$i]}' WHERE id = {$id_item[$i]};") or exit("Ocorreu um erro ao atualizar as informação de um item do pedido.");
            }
        }
        return true;
    }

    /**
     * 	Função para analisar um pedido, enviar comentários, alterar status, desativar itens
     * 	cancelados, retornar para o setor
     *
     * 	@access public
     * 	@param $id_item -> array com os ids dos itens utilizados no pedido
     * 	@param $item_cancelado -> cada posição está associada ao array $id_item, se na posição x $id_item estiver 1, então o item na posição x de $id_item foi cancelado
     * 	@return bool
     */
    public function pedidoAnalisado($id_pedido, $fase, $prioridade, $id_item, $item_cancelado, $qtd_solicitada, $qt_saldo, $qt_utilizado, $vl_saldo, $vl_utilizado, $valor_item, $saldo_setor, $total_pedido, $comentario) {
        // selecionando o id do setor que fez o pedido
        $obj_id = $this->mysqli->query("SELECT id_setor FROM pedido WHERE id = {$id_pedido};")->fetch_object();
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
                        $this->mysqli->query("DELETE FROM itens_pedido WHERE id_pedido = {$id_pedido} AND id_item = {$id_item[$i]};");
                        $total_pedido -= $valor_item[$i];
                    }
                    $this->mysqli->query("UPDATE itens SET qt_saldo = '{$qt_saldo[$i]}', qt_utilizado = '{$qt_utilizado[$i]}', vl_saldo = '{$vl_saldo[$i]}', vl_utilizado = '{$vl_utilizado[$i]}'{$cancelado} WHERE id = {$id_item[$i]};");
                    $saldo_setor += $valor_item[$i];
                }
            }
        }
        // alterar o status do pedido
        $alteracao = 0;
        if ($fase == 3 || $fase == 4) {
            // somente se o pedido for reprovado ou aprovado
            $total_pedido = number_format($total_pedido, 3, '.', '');
            $this->mysqli->query("UPDATE pedido SET valor = '{$total_pedido}' WHERE id = {$id_pedido};");
            if ($fase == 3) {
                // reprovado
                $alteracao = 1;
                $prioridade = 5;
            } else {
                // aprovado
                $this->mysqli->query("INSERT INTO saldos_lancamentos VALUES(NULL, {$id_setor}, '{$hoje}', '-{$total_pedido}', 4);");
                // próxima fase
                $fase++;
            }
            $saldo_setor = number_format($saldo_setor, 3, '.', '');
            $this->mysqli->query("UPDATE saldo_setor SET saldo = '{$saldo_setor}' WHERE id_setor = {$id_setor};");
        }
        $this->mysqli->query("UPDATE pedido SET status = {$fase}, prioridade = {$prioridade}, alteracao = {$alteracao} WHERE id = {$id_pedido};");
        if (strlen($comentario) > 0) {
            // inserindo comentário da análise
            $comentario = $this->mysqli->real_escape_string($comentario);
            $obj_tot = $this->mysqli->query("SELECT valor FROM pedido WHERE id = {$id_pedido};")->fetch_object();
            $this->mysqli->query("INSERT INTO comentarios VALUES(NULL, {$id_pedido}, '{$hoje}', {$prioridade}, {$fase}, '{$obj_tot->valor}', '{$comentario}');");
        }
        $this->mysqli->close();
        return true;
    }

    /**
     * 	Função para deletar um pedido (rascunhos)
     *
     * 	@access public
     * 	@return string
     */
    public function deletePedido(int $id_pedido): string {
        $this->mysqli->query("DELETE FROM comentarios WHERE comentarios.id_pedido = {$id_pedido};") or exit("Erro ao remover comentarios");
        $this->mysqli->query("DELETE FROM itens_pedido WHERE itens_pedido.id_pedido = {$id_pedido};") or exit("Erro ao remover os itens do pedido");
        $this->mysqli->query("DELETE FROM pedido_empenho WHERE pedido_empenho.id_pedido = {$id_pedido};") or exit("Erro ao remover o empenho do pedido.");
        $this->mysqli->query("DELETE FROM pedido_fonte WHERE pedido_fonte.id_pedido = {$id_pedido};") or exit("Erro ao remover as fontes do pedido.");
        $this->mysqli->query("DELETE FROM solic_alt_pedido WHERE solic_alt_pedido.id_pedido = {$id_pedido};") or exit("Erro ao remover as solicitações de alteração do pedido.");
        $this->mysqli->query("DELETE FROM pedido WHERE pedido.id = {$id_pedido};") or exit("Erro ao remover o pedido.");
        return "true";
    }

}

?>
