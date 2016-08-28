<?php
/**
 *  Classe com as funções de cadastrados utilizadas pelo arquivo php/geral.php
 *  toda função de ENTRADA de dados no banco devem ficar nesta classe
 *
 *  @author João Bolsson
 *  @since Version 1.0
 */
ini_set('display_erros', true);
error_reporting(E_ALL);

include_once 'Conexao.class.php';
include_once 'Busca.class.php';
class Geral extends Conexao {

	private $mysqli, $obj_Busca;
	function __construct() {
		//chama o método contrutor da classe Conexao
		parent::__construct();
		$this->mysqli = parent::getConexao();
	}
	// ------------------------------------------------------------------------------
	/**
	 *	Função para cadastrar novo tipo de processo.
	 *
	 *	@access public
	 *	@return bool.
	 */
	public function newTypeProcess($tipo): bool{
		$tipo = $this->mysqli->real_escape_string($tipo);
		$insert = $this->mysqli->query("INSERT INTO processos_tipo VALUES(NULL, '{$tipo}');");
		if ($insert) {
			return true;
		}
		return false;
	}
	// ------------------------------------------------------------------------------
	/**
	 *	Função para cadastrar/editar um processo
	 *
	 *	@access public
	 *	@param $dados é um array que contém os dados do processo
	 *	@param $dados["id_processo"] contém o id do processo, se for 0 então é para adc, se não dar update
	 *	@return bool
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
	// ------------------------------------------------------------------------------
	/**
	 *	Função que importa itens por SQL
	 *
	 *	@access public
	 *	@return bool
	 */
	public function importaItens($array_sql): bool {
		for ($i = 0; $i < count($array_sql); $i++) {
			$query = $array_sql[$i];
			$this->mysqli->query($query);
		}
		$this->mysqli->close();
		return true;
	}
	// ------------------------------------------------------------------------------
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
	// -------------------------------------------------------------------------
	/**
	 *	Função usada para o usuário alterar a sua senha
	 *
	 *	@access public
	 *	@return bool
	 */
	public function altInfoUser($id_user, $nome, $email, $novaSenha, $senhaAtual): bool{
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
	// -------------------------------------------------------------------------
	/**
	 *	Função que analisa as solicitações de alteração de pedido
	 *
	 *	@access public
	 *	@return bool
	 */
	public function analisaSolicAlt($id_solic, $id_pedido, $acao): bool{
		$hoje = date('Y-m-d');
		$update = $this->mysqli->query("UPDATE solic_alt_pedido SET data_analise = '{$hoje}', status = {$acao} WHERE id = {$id_solic};");
		if ($acao) {
			$update_alteracao = $this->mysqli->query("UPDATE pedido SET alteracao = {$acao}, prioridade = 'rascunho', status = 'Rascunho' WHERE id = {$id_pedido};");
		}
		return true;
	}
	// -------------------------------------------------------------------------
	/**
	 *	Função que envia uma solicitação de alteração de pedido ao SOF.
	 *
	 *	@access public
	 *	@return bool
	 */
	public function solicAltPedido($id_pedido, $id_setor, $justificativa): bool{
		$hoje = date('Y-m-d');
		$justificativa = $this->mysqli->real_escape_string($justificativa);
		$solicita = $this->mysqli->query("INSERT INTO solic_alt_pedido VALUES(NULL, {$id_pedido}, {$id_setor}, '{$hoje}', '0000-00-00', '{$justificativa}', 2);");
		if ($solicita) {
			return true;
		}
		return false;
	}
	// -------------------------------------------------------------------------
	/**
	 *	Função que verifica cada vez que o site for acessado se existem solicitações
	 *	de adiantamento que venceram (feitas em meses anteriores), se sim, reprova
	 *
	 *	@access public
	 *	@return bool
	 */
	public function checkSolicAdi(): bool{
		$hoje = date('Y-m-d');
		$mes_atual = date("n");
		$ano_atual = date("Y");
		$update = $this->mysqli->query("UPDATE saldos_adiantados SET data_analise = '{$hoje}', status = 0 WHERE (SELECT EXTRACT(MONTH FROM data_solicitacao)) <> {$mes_atual} OR (SELECT EXTRACT(YEAR FROM data_solicitacao)) <> {$ano_atual};");
		if ($update) {
			return true;
		}
		return false;
	}
	// -------------------------------------------------------------------------
	/**
	 *	Função para liberação de saldo de um setor
	 *
	 *	@access public
	 *	@param $id_setor Comment.
	 *	@param $valor Comment.
	 *	@param $saldo_atual Comment.
	 *	@return bool
	 */
	public function liberaSaldo($id_setor, $valor, $saldo_atual): bool{
		$saldo = $saldo_atual + $valor;
		$verifica = $this->mysqli->query("SELECT saldo_setor.id FROM saldo_setor WHERE saldo_setor.id_setor = {$id_setor};");
		if ($verifica->num_rows < 1) {
			$query = $this->mysqli->query("INSERT INTO saldo_setor VALUES(NULL, {$id_setor}, '0.000');");
		}
		$update = $this->mysqli->query("UPDATE saldo_setor SET saldo = '{$saldo}' WHERE id_setor = {$id_setor};");
		$hoje = date('Y-m-d');
		$insert = $this->mysqli->query("INSERT INTO saldos_lancamentos VALUES(NULL, {$id_setor}, '{$hoje}', '{$valor}');");
		if ($insert) {
			return true;
		}
		return false;
	}
	// -------------------------------------------------------------------------
	/**
	 *	Função para aprovar uma solicitação de adiantamento
	 *
	 *	@access public
	 *	@param $acao 0 -> reprovado | 1 -> aprovado
	 *	@return bool
	 */
	public function analisaAdi($id, $acao): bool {
		/*
			$hoje = date('Y-m-d');
			$mes_subtraido = $mes = date("n");
			$ano_subtraido = $ano = date("Y");
			if ($mes_subtraido == 12) {
				$mes_subtraido = 1;
				$ano_subtraido++;
			} else {
				$mes_subtraido++;
			}
			if (!$acao) {
				// se reprovado
				$mes_subtraido = 13;
			}
			$update = $this->mysqli->query("UPDATE saldos_adiantados SET data_analise = '{$hoje}', mes_subtraido = {$mes_subtraido}, ano = {$ano_subtraido}, status = {$acao} WHERE id = {$id}");
			if ($update) {
				if (!$acao) {
					// se reprovado retorna
					return true;
				}
				// selecionando a soma entre o valor adiantado aprovado com o saldo atual do setor no mês
				$query = $this->mysqli->query("SELECT saldo_setor.id_setor, saldo_setor.saldo + saldos_adiantados.valor_adiantado AS saldo_total, saldo_setor.saldo_aditivado + saldos_adiantados.valor_adiantado AS total_aditivado FROM saldo_setor, saldos_adiantados WHERE saldos_adiantados.id = {$id} AND saldos_adiantados.id_setor = saldo_setor.id_setor AND saldos_adiantados.mes_subtraido = {$mes_subtraido} AND saldos_adiantados.ano = {$ano_subtraido} AND saldo_setor.mes = {$mes} AND saldo_setor.ano = {$ano};");
				$adiantamento = $query->fetch_object();
				$query->close();

				$update_saldo = $this->mysqli->query("UPDATE saldo_setor SET saldo = '{$adiantamento->saldo_total}', saldo_aditivado = '{$adiantamento->total_aditivado}' WHERE id_setor = {$adiantamento->id_setor} AND mes = {$mes} AND ano = {$ano};");
				if ($update_saldo) {
					return true;
				} else {
					return false;
				}
			} else {
				return false;
			}
		*/
		return true;
	}
	// -------------------------------------------------------------------------
	/**
	 *	Função para enviar um pedido de adiantamento de saldo para o SOF
	 *
	 *	@access public
	 *	@return bool
	 */
	public function solicitaAdiantamento($id_setor, $valor, $justificativa): bool{
		$valor = $this->mysqli->real_escape_string($valor);
		$justificativa = $this->mysqli->real_escape_string($justificativa);
		$hoje = date('Y-m-d');
		$ano = date("Y");

		$insere = $this->mysqli->query("INSERT INTO saldos_adiantados VALUES(NULL, {$id_setor}, '{$hoje}', '0000-00-00', 13, {$ano},'{$valor}', '{$justificativa}', 2);");

		if ($insere) {
			return true;
		}
		return false;
	}
	//--------------------------------------------------------------------------
	/**
	 *   Função para alterar a senha de um usuário
	 *
	 *   @access public
	 *   @return bool
	 */
	public function updateSenha($id_user, $senha): bool{
		$update = $this->mysqli->query("UPDATE usuario SET senha = '{$senha}' WHERE id = {$id_user}");
		if ($update) {
			return true;
		}
		return false;
	}
	//--------------------------------------------------------------------------
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
	//----------------------------------------------------------------------------
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
	//-----------------------------------------------------------------------------------
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
	//----------------------------------------------------------------------------------
	/**
	 *   Função para enviar um pedido ao SOF
	 *
	 *   @access public
	 *	 @param $id_item Array com os ids dos itens do pedido.
	 *   @param $qtd Array com as quantidades dos itens do pedido.
	 *   @param $valor Array com os valores dos itens do pedido.
	 *   @param $pedido Id do pedido. Se 0, pedido novo, senão editando rascunho ou enviando ao SOF.
	 *   @return bool
	 */
	public function insertPedido($id_setor, $id_item, $qtd_solicitada, $qtd_disponivel, $qtd_contrato, $qtd_utilizado, $vl_saldo, $vl_contrato, $vl_utilizado, $valor, $total_pedido, $saldo_total, $prioridade, $obs, $pedido) {

		$obs = $this->mysqli->real_escape_string($obs);
		$retorno = false;
		$hoje = date('Y-m-d');
		$mes = date("n");

		if ($prioridade == 5) {
			if ($pedido == 0) {
				// NOVO
				//inserindo os dados iniciais do pedido
				$query_pedido = $this->mysqli->query("INSERT INTO pedido VALUES(NULL, {$id_setor}, '{$hoje}', '{$mes}', 1, {$prioridade}, 1, '{$total_pedido}', '{$obs}');");
				$pedido = $this->mysqli->insert_id;
			} else {
				//remover resgistros antigos do rascunho
				$this->mysqli->query("DELETE FROM itens_pedido WHERE id_pedido = {$pedido};");
				$this->mysqli->query("UPDATE pedido SET data_pedido = '{$hoje}', ref_mes = {$mes}, prioridade = {$prioridade}, valor = '{$total_pedido}', obs = '{$obs}' WHERE id = {$pedido};");
			}
			//inserindo os itens do pedido
			for ($i = 0; $i < count($id_item); $i++) {
				$this->mysqli->query("INSERT INTO itens_pedido VALUES(NULL, {$pedido}, {$id_item[$i]}, {$qtd_solicitada[$i]}, '{$valor[$i]}');");
			}
		} else {
			// atualiza saldo
			$this->mysqli->query("UPDATE saldo_setor SET saldo = '{$saldo_total}' WHERE id_setor = {$id_setor};");
			// enviado ao sof
			if ($pedido == 0) {
				//inserindo os dados iniciais do pedido
				$query_pedido = $this->mysqli->query("INSERT INTO pedido VALUES(NULL, {$id_setor}, '{$hoje}', '{$mes}', 0, {$prioridade}, 2, '{$total_pedido}', '{$obs}');");
				$pedido = $this->mysqli->insert_id;
			} else {
				// atualizando pedido
				$this->mysqli->query("UPDATE pedido SET data_pedido = '{$hoje}', ref_mes = {$mes}, alteracao = 0, prioridade = {$prioridade}, status = 2, valor = '{$total_pedido}', obs = '{$obs}' WHERE id = {$pedido};");
			}
			//remover resgistros antigos do pedido
			$this->mysqli->query("DELETE FROM itens_pedido WHERE id_pedido = {$pedido};");
			// alterando infos dos itens solicitados
			for ($i = 0; $i < count($id_item); $i++) {
				$this->mysqli->query("INSERT INTO itens_pedido VALUES(NULL, {$pedido}, {$id_item[$i]}, {$qtd_solicitada[$i]}, '{$valor[$i]}');");
				// qtd_disponivel == qt_saldo
				$qtd_disponivel[$i] -= $qtd_solicitada[$i];
				$qtd_utilizado[$i] += $qtd_solicitada[$i];
				if ($vl_saldo[$i] == 0) {
					$vl_saldo[$i] = $vl_contrato[$i];
				}
				$vl_saldo[$i] -= $valor[$i];
				$vl_utilizado[$i] += $valor[$i];
				$this->mysqli->query("UPDATE itens SET qt_saldo = {$qtd_disponivel[$i]}, qt_utilizado = {$qtd_utilizado[$i]}, vl_saldo = '{$vl_saldo[$i]}', vl_utilizado = '{$vl_utilizado[$i]}' WHERE id = {$id_item[$i]};");
			}
		}
		$retorno = true;
		return $retorno;
	}
	//------------------------------------------------------------------------------------------
	/**
	 *	Função para analisar um pedido, enviar comentários, alterar status, desativar itens
	 *	cancelados, retornar para o setor
	 *
	 *	@access public
	 *	@param $id_item -> array com os ids dos itens utilizados no pedido
	 *	@param $item_cancelado -> cada posição está associada ao array $id_item, se na posição x $id_item estiver 1, então o item na posição x de $id_item foi cancelado
	 *	@return bool
	 */
	public function pedidoAnalisado($id_pedido, $fase, $prioridade, $id_item, $item_cancelado, $qtd_solicitada, $qt_saldo, $qt_utilizado, $vl_saldo, $vl_utilizado, $valor_item, $saldo_setor, $total_pedido, $comentario) {
		// selecionando o id do setor que fez o pedido
		$obj_id = $this->mysqli->query("SELECT id_setor FROM pedido WHERE id = {$id_pedido};");
		$id_setor = $obj_id->id_setor;
		// alterar o status do pedido
		$alteracao = 0;
		if ($fase == 1) {
			$alteracao = 1;
			$prioridade = 5;
		}
		$update_st = $this->mysqli->query("UPDATE pedido SET status = '{$fase}', prioridade = '{$prioridade}', alteracao = {$alteracao} WHERE id = {$id_pedido};");
		// verificando itens cancelados
		for ($i = 0; $i < count($id_item); $i++) {
			if ($item_cancelado[$i]) {
				// o item de id = $id_item[$i] foi cancelado e deve ser desativado, sua qt_solicitada deve ser devolvida à qt_saldo, e a qt_utilizada deve ser subtraida da qt_solicitada, bem como os valores de saldo e utilizado
				$qt_saldo[$i] += $qt_solicitada[$i];
				$qt_utilizado[$i] -= $qtd_solicitada[$i];
				$vl_saldo[$i] += $valor_item[$i];
				$vl_utilizado -= $valor_item[$i];
				$this->mysqli->query("UPDATE itens SET qt_saldo = '{$qt_saldo[$i]}', qt_utilizado = '{$qt_utilizado[$i]}', vl_saldo = '{$vl_saldo[$i]}', vl_utilizado = '{$vl_utilizado[$i]}', cancelado = 1 WHERE id = {$id_item[$i]};");
				// o saldo do setor deve ser incrementado do valor total do item que foi solicitado mas cancelado
				$saldo_setor += $valor_item[$i];
				$saldo_setor = number_format($saldo_setor, 3, '.', '');
				$this->mysqli->query("UPDATE saldo_setor SET saldo = '{$saldo_setor}' WHERE id_setor = {$id_setor};");
				// o pedido também deve ser alterado
				$total_pedido -= $valor_item[$i];
				$this->mysqli->query("UPDATE pedido SET valor = '{$total_pedido}' WHERE id = {$id_pedido};");
				$this->mysqli->query("DELETE FROM itens_pedido WHERE id_pedido = {$id_pedido} AND id_item = {$id_item[$i]};");
			}
		}
		// inserindo comentário da análise
		$hoje = date('Y-m-d');
		$comentario = $this->mysqli->real_escape_string($comentario);
		$obj_tot = $this->mysqli->query("SELECT valor FROM pedido WHERE id = {$id_pedido};")->fetch_object();
		$this->mysqli->query("INSERT INTO comentarios VALUES(NULL, {$id_pedido}, '{$hoje}', '{$prioridade}', '{$fase}', '{$obj_tot->valor}', '{$comentario}');");
		return true;
	}
}

?>
