<?php
/**
 *  Classe com as funções de busca utilizadas principalmente pelo arquivo php/busca.php
 *  qualquer função que RETORNE dados do banco, devem ser feitas nesta classe
 *
 *  @author João Bolsson
 *  @since Version 1.0
 */
ini_set('display_erros', true);
error_reporting(E_ALL);

include_once 'Conexao.class.php';
class Busca extends Conexao {
	private $obj_Conexao, $mysqli;

	function __construct() {
		//chama o método contrutor da classe Conexao
		parent::__construct();
		//atribuindo valor a variavel que realiza as consultas
		$this->mysqli = parent::getConexao();
	}
	/**
	 *	Função que retorna as permissões do usuario
	 *
	 *	@access public
	 *	@return object
	 */
	public function getPermissoes($id_user) {
		$query = $this->mysqli->query("SELECT usuario_permissoes.noticias, usuario_permissoes.saldos, usuario_permissoes.pedidos FROM usuario_permissoes WHERE usuario_permissoes.id_usuario = {$id_user};");
		$obj_permissoes = $query->fetch_object();
		$query->close();
		return $obj_permissoes;
	}
	# ========================================================================================
	#                                          PÚBLICO
	# ========================================================================================
	//------------------------------------------------------------------------
	/**
	 * Função para adicionar novos inputs para adicionar arquivos
	 *
	 * @access public
	 * @return string
	 */
	public function setInputsArquivo($qtd) {
		$qtd++;
		return "
		<div id=\"file-$qtd\" class=\"tile\">
			<div class=\"tile-side pull-left\">
				<div class=\"avatar avatar-sm avatar-brand\">
					<span class=\"icon\">backup</span>
				</div>
			</div>
			<div class=\"tile-action tile-action-show\">
				<ul class=\"nav nav-list margin-no pull-right\">
					<li>
						<a class=\"text-black-sec waves-attach\" href=\"javascript:dropTile('file-$qtd');\"><span class=\"icon\">delete</span></a>
					</li>
				</ul>
			</div>
			<div class=\"tile-inner\">
				<input id=\"arq-$qtd\" class=\"btn btn-default btn-file\" type=\"file\" name=\"file-$qtd\" style=\"text-transform: none !important;\">
			</div>
		</div>
		";
	}
	//------------------------------------------------------------------------
	/**
	 * Função que busca os detalhes de uma notícia completa
	 *
	 * @access public
	 * @return string
	 */
	public function getInfoNoticia($id) {
		//declarando retorno
		$retorno = "";
		$query = $this->mysqli->query("SELECT postagem FROM postagens WHERE id = {$id};");
		$noticia = $query->fetch_object();
		return html_entity_decode($noticia->postagem);
	}
	// -----------------------------------------------------------------------
	/**
	 *	script temporário
	 *
	 */
	public function upPostagens() {
		$query_teste = $this->mysqli->query("SELECT id FROM postagens;");
		if ($query_teste->num_rows < 1) {
			$query_tabelas = $this->mysqli->query("SELECT tabela FROM paginas_post;");
			while ($tabela = $query_tabelas->fetch_object()) {
				$query = $this->mysqli->query("SELECT {$tabela->tabela}.postagem, {$tabela->tabela}.data, {$tabela->tabela}.ativa FROM {$tabela->tabela};");
				if ($query->num_rows > 0) {
					while ($postagem = $query->fetch_object()) {
						$postagem->postagem = $this->mysqli->real_escape_string($postagem->postagem);

						$inicio = strpos($postagem->postagem, "<h3");
						$fim = strpos($postagem->postagem, "</h3>");

						$titulo = strip_tags(substr($postagem->postagem, $inicio, $fim));
						$this->mysqli->query("INSERT INTO postagens VALUES(NULL, '{$tabela->tabela}', '{$titulo}', '{$postagem->data}', {$postagem->ativa}, '{$postagem->postagem}');");
					}
				}
			}
			while ($postagem = $query->fetch_object()) {
				$obj = $this->mysqli->query("SELECT {$postagem->tabela}.postagem FROM {$postagem->tabela} WHERE {$postagem->tabela}.id = {$postagem->id_postagem};")->fetch_object();
				$obj->postagem = $this->mysqli->real_escape_string($obj->postagem);
				$this->mysqli->query("UPDATE postagens SET postagem = '{$obj->postagem}' WHERE postagens.id_postagem = {$postagem->id_postagem} AND postagens.tabela = '{$postagem->tabela}';");
			}
		}
	}
	//------------------------------------------------------------------------
	/**
	 * Função para mostrar uma tabela com todas as publicações de certa página
	 *
	 * @access public
	 * @param $id_setor -> filtra as notícias por setor
	 *        $tabela -> filtra por nome da tabela
	 * @return string
	 */
	public function getPostagens($id_setor, $tabela) {
		//declarando retorno
		$retorno = "";
		$query = $this->mysqli->query("SELECT postagens.id, postagens.titulo, DATE_FORMAT(postagens.data, '%d/%m/%Y') AS data FROM postagens, paginas_post WHERE postagens.tabela = paginas_post.id AND paginas_post.tabela = '{$tabela}' AND ativa = 1 ORDER BY data ASC;");
		$i = 0;
		while ($postagem = $query->fetch_object()) {
			$retorno .= "<tr><td>";
			$retorno .= html_entity_decode($postagem->titulo);

			$retorno .= "<button class=\"btn btn-flat btn-sm\" style=\"text-transform: lowercase !important;font-weight: bold;\" onclick=\"ver_noticia({$postagem->id}, '{$tabela}', 0);\">...ver mais</button></td>";
			$retorno .= "<td><span style=\"font-weight: bold;\" class=\"pull-right\">{$postagem->data}</span></td></tr>";
		}
		return $retorno;
	}
	//------------------------------------------------------------------------
	/**
	 * Função para popular os slides na página inicial
	 *
	 * @access public
	 * @param $slide (1 ou 2)-> o primeiro mostra as últimas notícias e o segundo aleatórias
	 * @return string
	 */
	public function getSlide($slide) {
		//declarando var order
		$order = "";
		if ($slide == 1) {
			$order = "postagens.data DESC";
		} else {
			$order = "rand()";
		}
		//declarando retorno
		$retorno = "";
		$array_anima = array("primeira", "segunda", "terceira", "quarta", "quinta");
		$array_id = array("primeiro", "segundo", "terceiro", "quarto", "quinto");
		$query_postagem = $this->mysqli->query("SELECT postagens.id, postagens.postagem, DATE_FORMAT(postagens.data, '%d/%m/%Y') AS data, paginas_post.tabela, postagens.titulo FROM postagens, paginas_post WHERE postagens.tabela = paginas_post.id AND postagens.ativa = 1 ORDER BY {$order} LIMIT 5;");
		//variável para contar
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
			<li id=\"{$array_id[$aux]}\" class=\"{$array_anima[$aux]}-anima\">
				<div class=\"card-img\">
					<img style=\"width: {$width}px; height: 275px;\" src=\"{$src}\" >
					<a href=\"javascript:ver_noticia({$postagem->id}, '{$postagem->tabela}', 1);\" class=\"card-img-heading padding\" style=\"font-weight: bold;\">$titulo<span class=\"pull-right\">{$postagem->data}</span></a>
				</div>
			</li>
			";
			$aux++;
		}
		return $retorno;
	}
	//-------------------------------------------------------------------------
	/**
	 * Função para pesquisar alguma publicação
	 *
	 * @access public
	 * @return string
	 */
	public function pesquisar($busca) {
		//declarando retorno
		$retorno = "";
		$busca = htmlentities($busca);
		//escapando string especiais para evitar SQL Injections
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
								<tbody>
									";
		if ($busca == "") {
			$retorno .= "
										<tr>
											<td collspan=\"2\">Digite algo para pesquisar...</td>
											<td></td>
										</tr>
										";
		} else {

			$query = $this->mysqli->query("SELECT postagens.id, postagens.id_postagem, postagens.tabela, postagens.titulo, DATE_FORMAT(postagens.data, '%d/%m/%Y') AS data, postagens.ativa FROM postagens WHERE postagens.titulo LIKE '%{$busca}%' AND postagens.ativa = 1 ORDER BY postagens.data DESC;");
			if ($query->num_rows == 0) {
				$retorno .= "
											<tr>
												<td collspan=\"2\">Nenhum resultado para '{$busca}'</td>
												<td></td>
											</tr>
											";
			} else {
				while ($postagem = $query->fetch_object()) {
					$titulo = html_entity_decode($postagem->titulo);
					$retorno .= "
												<tr>
													<td>{$titulo}<button class=\"btn btn-flat btn-sm\" style=\"text-transform: lowercase !important;font-weight: bold;\" onclick=\"ver_noticia({$postagem->id_postagem}, '{$tabela}', 1);\">...ver mais</button></td>
													<td><span class=\"pull-right\">{$postagem->data}</span></td>
												</tr>
												";
				}
			}
			$retorno .= "
									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div><!-- ./card-main -->
			</div> <!-- ./card -->
			";
		}
		return $retorno;
	}
	# =========================================================================================
	#                                        ADMINISTRADOR
	# =========================================================================================
	// -----------------------------------------------------------------------
	/**
	 *	Função que retorna a tabela com as solicitações de alteração de pedidos
	 *	para o SOF analisar
	 *
	 *	@access public
	 *	@return string
	 */
	public function getAdminSolicAltPedidos($st) {
		$retorno = "";
		$query = $this->mysqli->query("SELECT solic_alt_pedido.id, solic_alt_pedido.id_pedido, setores.nome, DATE_FORMAT(solic_alt_pedido.data_solicitacao, '%d/%m/%Y') AS data_solicitacao, DATE_FORMAT(solic_alt_pedido.data_analise, '%d/%m/%Y') AS data_analise, solic_alt_pedido.justificativa, solic_alt_pedido.status FROM solic_alt_pedido, setores WHERE solic_alt_pedido.id_setor = setores.id AND solic_alt_pedido.status = {$st};");
		$status = $label = "";
		while ($solic = $query->fetch_object()) {
			switch ($solic->status) {
			case 0:
				$status = "Reprovado";
				$label = "label-red";
				break;
			case 1:
				$status = "Aprovado";
				$label = "label-green";
				break;
			default:
				$status = "Aberto";
				$label = "label-orange";
				$solic->data_analise = "--------------";
				break;
			}
			$btn_aprovar = $btn_reprovar = "";
			if ($st == 2) {
				//$solic->mes_subtraido = "---------------";
				$btn_aprovar = "<a title=\"Aprovar\" href=\"javascript:analisaSolicAlt({$solic->id}, {$solic->id_pedido}, 1);\" class=\"modal-close\"><span class=\"icon\">done_all<span></span></span></a>";
				$btn_reprovar = "<a title=\"Reprovar\" href=\"javascript:analisaSolicAlt({$solic->id}, {$solic->id_pedido}, 0);\" class=\"modal-close\"><span class=\"icon\">delete<span></span></span></a>";
			}
			$retorno .= "
			<tr>
				<td>
					{$btn_aprovar}{$btn_reprovar}
				</td>
				<td>{$solic->id_pedido}</td>
				<td>{$solic->nome}</td>
				<td>{$solic->data_solicitacao}</td>
				<td>{$solic->data_analise}</td>
				<td>
					<button onclick=\"viewCompl('{$solic->justificativa}');\" class=\"btn btn-flat waves-attach waves-effect\" type=\"button\" title=\"Ver Justificativa\">JUSTIFICATIVA</button>
				</td>
				<td><span class=\"label {$label}\" style=\"font-size: 11pt !important; font-weight: bold;\">{$status}</span></td>
			</tr>
			";
		}
		$query->close();
		return $retorno;
	}

	// -----------------------------------------------------------------------
	/**
	 *	Função para retornar a tabela de liberar saldos para os setores
	 *	utilizada em adminsolicitacoes.php
	 *
	 *	@access public
	 *	@return string
	 */
	public function getFreeSaldos() {
		$retorno = "";
		$mes_atual = $mes = date("n");
		$ano_atual = $ano = date("Y");
		if ($mes == 1) {
			$mes = 12;
			$ano--;
		} else {
			$mes--;
		}
		// contém os ids dos setores que já tiveram o seu saldo liberado no mês/ano atual
		$exc = "";
		$query_exc = $this->mysqli->query("SELECT saldo_setor.id_setor FROM saldo_setor WHERE mes = {$mes_atual} AND ano = {$ano_atual};");
		while ($result = $query_exc->fetch_object()) {
			$exc .= " AND saldo_setor.id_setor <> {$result->id_setor}";
		}
		$sigla_mes = $this->mysqli->query("SELECT sigla_mes FROM mes WHERE id = {$mes_atual}")->fetch_object()->sigla_mes;
		$query = $this->mysqli->query("SELECT saldo_setor.id_setor, setores.nome, (saldo_padrao - saldo_aditivado) AS saldo_incr FROM saldo_setor, saldo_fixo, setores WHERE saldo_setor.id_setor = saldo_fixo.id_setor AND saldo_setor.id_setor = setores.id AND setores.id <> 1 AND saldo_setor.mes = {$mes} AND saldo_setor.ano = {$ano}{$exc};");
		if ($query->num_rows < 1) {
			$exc = str_replace("saldo_setor", "saldo_fixo", $exc);
			$query = $this->mysqli->query("SELECT setores.id AS id_setor, setores.nome, saldo_fixo.saldo_padrao AS saldo_incr FROM setores, saldo_fixo WHERE setores.id = saldo_fixo.id_setor AND setores.id <> 1{$exc};");
		}
		if ($query->num_rows > 0) {
			while ($saldo = $query->fetch_object()) {
				$retorno .= "
				<tr>
					<td>{$saldo->nome}</td>
					<td>{$saldo->saldo_incr}</td>
					<td>{$sigla_mes} / {$ano_atual}</td>
					<td><a class=\"modal-close\" href=\"javascript:liberaSaldo({$saldo->id_setor}, {$mes_atual}, {$ano_atual}, '{$saldo->saldo_incr}');\" title=\"Liberar Saldo\"><span class=\"icon\">done_all<span></span></span></a></td>
				</tr>
				";
			}
		} else {
			$retorno = "
				<tr>
					<td collspan=\"3\">Nenhum saldo para liberar.</td>
					<td></td>
					<td></td>
					<td></td>
				</tr>
			";
		}
		$query->close();
		return $retorno;
	}

	// -----------------------------------------------------------------------
	/**
	 *	Função que retorna as solicitações de adiantamentos de saldos enviadas ao SOF para análise
	 *
	 *	@access public
	 *	@return string
	 *
	 */
	public function getSolicAdiantamentos($st) {
		$query = $this->mysqli->query("SELECT saldos_adiantados.id, setores.nome, DATE_FORMAT(saldos_adiantados.data_solicitacao, '%d/%m/%Y') AS data_solicitacao, DATE_FORMAT(saldos_adiantados.data_analise, '%d/%m/%Y') AS data_analise, mes.sigla_mes AS mes_subtraido, saldos_adiantados.ano, saldos_adiantados.valor_adiantado, saldos_adiantados.justificativa FROM saldos_adiantados, setores, mes WHERE saldos_adiantados.id_setor = setores.id AND saldos_adiantados.status = {$st} AND saldos_adiantados.mes_subtraido = mes.id ORDER BY saldos_adiantados.data_solicitacao DESC;");
		// declarando retorno
		$retorno = "";
		$status = $label = "";
		switch ($st) {
		case 0:
			$status = "Reprovado";
			$label = "label-red";
			break;
		case 1:
			$status = "Aprovado";
			$label = "label-green";
			break;
		case 2:
			$status = "Aberto";
			$label = "label-orange";
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
					//$solic->mes_subtraido = "---------------";
					$btn_aprovar = "<a title=\"Aprovar\" href=\"javascript:analisaAdi({$solic->id}, 1);\" class=\"modal-close\"><span class=\"icon\">done_all<span></span></span></a>";
					$btn_reprovar = "<a title=\"Reprovar\" href=\"javascript:analisaAdi({$solic->id}, 0);\" class=\"modal-close\"><span class=\"icon\">delete<span></span></span></a>";
				}
				$retorno .= "
				<tr>
					<td>{$btn_reprovar}{$btn_aprovar}</td>
					<td>{$solic->nome}</td>
					<td>{$solic->data_solicitacao}</td>
					<td>{$solic->data_analise}</td>
					<td>{$solic->mes_subtraido} / {$solic->ano}</td>
					<td>R$ {$solic->valor_adiantado}</td>
					<td>
						<button onclick=\"viewCompl('{$solic->justificativa}');\" class=\"btn btn-flat waves-attach waves-effect\" type=\"button\" title=\"Ver Justificativa\">JUSTIFICATIVA</button>
					</td>
					<td><span class=\"label {$label}\" style=\"font-size: 11pt !important; font-weight: bold;\">{$status}</span></td>
				</tr>
				";
			}
		}
		$query->close();
		return $retorno;
	}
	//------------------------------------------------------------------------
	/**
	 * Função para retornar o cabeçalho do pdf do pedido
	 *
	 * @access public
	 * @return string
	 */
	public function getHeader($id_pedido) {
		$pedido = $this->mysqli->query("SELECT id, DATE_FORMAT(data_pedido, '%d/%m/%Y') AS data_pedido, EXTRACT(YEAR FROM data_pedido) AS ano, ref_mes, status, valor FROM pedido WHERE id = {$id_pedido};")->fetch_object();
		$ano = substr($pedido->data_pedido, 0, 4);
		$pedido->valor = str_replace(".", ",", $pedido->valor);
		$retorno = "
		<fieldset>
			<p>
				Pedido: {$id_pedido}
				Data de Envio: {$pedido->data_pedido}&emsp;
				Situação: {$pedido->status}&emsp;
				Ano: {$pedido->ano}&emsp;
				Mês: {$pedido->ref_mes}&emsp;
				Total do Pedido: R$ {$pedido->valor}
			</p>
			<p>Observação: </p>
		</fieldset><br>
		";
		return $retorno;
	}
	//------------------------------------------------------------------------
	/**
	 * Função para retornar o pedido para um relátório separando-o por licitação e fornecedor
	 *
	 * @access public
	 * @return string
	 */
	public function getContentPedido($id_pedido) {
		// declarando retorno
		$retorno = "";
		// PRIMEIRO FAZEMOS O CABEÇALHO REFERENTE AO NUM_LICITACAO
		$query_ini = $this->mysqli->query("SELECT DISTINCT itens.num_licitacao, itens.num_processo, itens.dt_inicio, itens.dt_fim FROM itens_pedido, itens WHERE itens.id = itens_pedido.id_item AND itens_pedido.id_pedido = {$id_pedido};");
		$i = 0;
		while ($licitacao = $query_ini->fetch_object()) {
			$valor_lic = $this->mysqli->query("SELECT sum(itens_pedido.valor) AS soma FROM itens_pedido, itens WHERE itens.id = itens_pedido.id_item AND itens_pedido.id_pedido = {$id_pedido} AND itens.num_licitacao = {$licitacao->num_licitacao};")->fetch_object();
			if ($licitacao->dt_fim == '') {
				$licitacao->dt_fim = "------------";
			}
			$valor_lic->soma = number_format($valor_lic->soma, 3, '.', '.');
			$retorno .= "
			<fieldset class=\"preg\">
				<table>
					<tr>
						<td>Licitação: {$licitacao->num_licitacao}</td>
						<td>Processo: {$licitacao->num_processo}</td>
						<td>Início: {$licitacao->dt_inicio}</td>
						<td>Fim: {$licitacao->dt_fim}</td>
						<td>Total do Pregão: R$ {$valor_lic->soma}</td>
					</tr>
				</table>
			</fieldset><br>
			";
			$query_forn = $this->mysqli->query("SELECT DISTINCT itens.cgc_fornecedor, itens.nome_fornecedor, itens.num_contrato FROM itens, itens_pedido WHERE itens.id = itens_pedido.id_item AND itens_pedido.id_pedido = {$id_pedido} AND itens.num_licitacao = {$licitacao->num_licitacao};");

			// -------------------------------------------------------------------------
			//                FORNECEDORES REFERENTES À LICITAÇÃO
			// -------------------------------------------------------------------------
			while ($fornecedor = $query_forn->fetch_object()) {
				// total do fornecedor
				$tot_forn = $this->mysqli->query("SELECT sum(itens_pedido.valor) AS sum FROM itens_pedido, itens WHERE itens_pedido.id_item = itens.id AND itens_pedido.id_pedido = {$id_pedido} AND itens.cgc_fornecedor = '{$fornecedor->cgc_fornecedor}';")->fetch_object();

				$fornecedor->nome_fornecedor = utf8_encode($fornecedor->nome_fornecedor);
				$fornecedor->nome_fornecedor = substr($fornecedor->nome_fornecedor, 0, 40);
				$fornecedor->nome_fornecedor = strtoupper($fornecedor->nome_fornecedor);
				$tot_forn->sum = number_format($tot_forn->sum, 3, '.', '');
				$retorno .= "
				<fieldset style=\"border-bottom: 1px solid black; padding: 5px;\">
					<table>
						<tr>
							<td style=\"text-align: left; font-weight: bold;\">{$fornecedor->nome_fornecedor}</td>
							<td>Contrato: {$fornecedor->num_contrato}</td>
							<td>Total do Forn.: R$ {$tot_forn->sum}</td>
							<td style=\"text-align: right;\">Empenho SIAFI: <input type=\"text\"/></td>
						</tr>
					</table>
				</fieldset>
				";
				// ----------------------------------------------------------------------
				//                  ITENS REFERENTES AOS FORNECEDORES
				// ----------------------------------------------------------------------
				$query_itens = $this->mysqli->query("SELECT itens.cod_reduzido, itens.complemento_item, itens_pedido.qtd, itens_pedido.valor FROM itens, itens_pedido WHERE itens.id = itens_pedido.id_item AND itens_pedido.id_pedido = {$id_pedido} AND itens.cgc_fornecedor = '{$fornecedor->cgc_fornecedor}'");
				$retorno .= "
				<table class=\"prod\">
					<thead>
						<tr>
							<th>Código</th>
							<th>Descrição</th>
							<th>Quantidade</th>
							<th>Valor</th>
						</tr>
					</thead>
					<tbody>
						";

				while ($item = $query_itens->fetch_object()) {
					$item->complemento_item = utf8_encode($item->complemento_item);
					$item->complemento_item = substr($item->complemento_item, 0, 73);
					$item->complemento_item = mb_strtoupper($item->complemento_item, 'UTF-8');
					$retorno .= "
							<tr>
								<td>{$item->cod_reduzido}</td>
								<td>{$item->complemento_item}</td>
								<td>{$item->qtd}</td>
								<td>R$ {$item->valor}</td>
							</tr>
							";
				}
				$retorno .= "
					</tbody>
				</table><br>
				";
			}
		}
		$query_ini->close();

		return $retorno;

	}
	//------------------------------------------------------------------------
	/**
	 * Função que retorna a tabela com os itens de um pedido para pdf
	 *
	 * @access public
	 * @return string
	 */
	public function getTabelaPDF($id_pedido) {
		$retorno = "";
		$query = $this->mysqli->query("SELECT itens.id, itens.cod_reduzido, itens.cgc_fornecedor, itens.num_licitacao, itens_pedido.qtd, itens_pedido.valor FROM itens, itens_pedido WHERE itens.id = itens_pedido.id AND itens_pedido.id_pedido = {$id_pedido};");
		while ($itens = $query->fetch_object()) {
			$retorno .= "
			<tr>
				<td>{$itens->id}</td>
				<td>{$itens->cod_reduzido}</td>
				<td>{$itens->cgc_fornecedor}</td>
				<td>{$itens->num_licitacao}</td>
				<td>{$itens->qtd}</td>
				<td>R$ {$itens->valor}</td>
			</tr>
			";
		}
		return $retorno;
	}
	// -----------------------------------------------------------------------
	/**
	 *	Função para retornar os comentários de um pedido
	 *
	 *	@access public
	 *	@return string
	 */
	public function getComentarios($id_pedido) {
		$retorno = "";
		$query = $this->mysqli->query("SELECT DATE_FORMAT(comentarios.data_coment, '%d/%m/%Y') AS data_coment, comentarios.prioridade, comentarios.status, comentarios.valor, comentarios.comentario FROM comentarios WHERE comentarios.id_pedido = {$id_pedido};");
		if ($query->num_rows > 0) {
			while ($comentario = $query->fetch_object()) {
				$retorno .= "
				<fieldset class=\"preg\">
					<table>
						<tr>
							<td>Data: {$comentario->data_coment}</td>
							<td>Prioridade: {$comentario->prioridade}</td>
							<td>Status: {$comentario->status}</td>
							<td>Valor: R$ {$comentario->valor}</td>
						</tr>
					</table>
				</fieldset>
				<fieldset>
					<p>{$comentario->comentario}</p>
				</fieldset>
				";
			}
		} else {
			$retorno = "Sem comentários";
		}
		return $retorno;
	}
	//------------------------------------------------------------------------
	/**
	 * Função para trazer todos os usuários cadastrados no sistema
	 * alteração de senhas
	 *
	 * @access public
	 * @return string
	 */
	public function getUsers() {
		//declarando retorno
		$retorno = "";
		$query = $this->mysqli->query("SELECT id, login FROM usuario;");
		while ($user = $query->fetch_object()) {
			$retorno .= "
			<option value=\"{$user->id}\">{$user->login}</option>
			";
		}
		return $retorno;
	}
	//--------------------------------------------------------------------------------
	/**
	 * Função que exibe os arquivos no modal do admin, usada diretamente no index
	 *
	 * @access public
	 * @return string
	 */
	public function getArquivos($busca) {
		//declarando retorno
		$retorno = "";
		$pasta = '../uploads/';
		$diretorio = dir($pasta);

		while ($arquivo = $diretorio->read()) {
			$tipo = pathinfo($pasta . $arquivo);
			$label = "label";
			if ($tipo["extension"] == "jpg"
				|| $tipo["extension"] == "png"
				|| $tipo["extension"] == "jpeg") {
				$tipo = "Imagem";
				$label .= " label-brand";
			} else {
				$tipo = "Documento";
			}
			if ($arquivo != "."
				&& $arquivo != ".."
				&& $tipo != "Imagem") {
				//mostra apenas os documentos pdf e doc
				$retorno .= "
		<tr>
			<td><span class=\"{$label}\" style=\"font-size: 11pt !important; font-weight: bold;\">{$tipo}</span></td>
			<td><a href=\"$pasta$arquivo\">$arquivo</a></td>
			<td><button class=\"btn btn-flat waves-attach waves-effect\" onclick=\"delArquivo('$pasta$arquivo');\"><span class=\"icon\">delete</span><span style=\"font-weight:bold;\">Excluir</span></button></td>
		</tr>
		";
			}
		}
		$diretorio->close();
		return $retorno;
	}
	//---------------------------------------------------------------------------
	/**
	 *	Função que retorna as 'tabs' com as ṕáginas das notícias para editar
	 *
	 *	@access public
	 *	@return string
	 */
	public function getTabsNoticias() {
		$retorno = "";
		$query = $this->mysqli->query("SELECT paginas_post.id, paginas_post.tabela, paginas_post.nome FROM paginas_post;");
		while ($pag = $query->fetch_object()) {
			$retorno .= "
			<td>
				<div class=\"radiobtn radiobtn-adv\">
				<label for=\"pag-{$pag->tabela}\">
						<input type=\"radio\" id=\"pag-{$pag->tabela}\" name=\"pag\" class=\"access-hide\" onclick=\"carregaPostsPag({$pag->id});\">{$pag->nome}
						<span class=\"radiobtn-circle\"></span><span class=\"radiobtn-circle-check\"></span>
					</label>
				</div>
			</td>
			";
		}
		$query->close();
		return $retorno;
	}
	// -------------------------------------------------------------------------------
	/**
	 *	Função para retornar a tabela de notícias de uma página para edição
	 *
	 *	@access public
	 *	@return string
	 */
	public function getNoticiasEditar($tabela) {
		$retorno = "";
		$query = $this->mysqli->query("SELECT postagens.id, postagens.tabela, postagens.titulo, DATE_FORMAT(postagens.data, '%d/%m/%Y') AS data FROM postagens WHERE postagens.ativa = 1 AND postagens.tabela = '{$tabela}' ORDER BY postagens.data ASC;");
		while ($postagem = $query->fetch_object()) {
			$retorno .= "
			<tr>
				<td>{$postagem->data}</td>
				<td>{$postagem->titulo}</td>
				<td>
				<button class=\"btn btn-default btn-sm\" style=\"text-transform: none !important;font-weight: bold;\" onclick=\"editaNoticia({$postagem->id}, {$postagem->tabela}, '{$postagem->data}')\" title=\"Editar\"><span class=\"icon\">create</span></button>
					<button class=\"btn btn-default btn-sm\" style=\"text-transform: none !important;font-weight: bold;\" onclick=\"excluirNoticia({$postagem->id});\" title=\"Excluir\"><span class=\"icon\">delete</span></button>
				</td>
			</tr>
			";
		}
		$query->close();
		return $retorno;
	}
	//-------------------------------------------------------------------------
	/**
	 * Função para buscar conteúdo de uma publicação para edição
	 *
	 * @access public
	 * @return string
	 */
	public function getPublicacaoEditar($id) {
		//declarando retorno
		$retorno = "";
		$publicacao = $this->mysqli->query("SELECT postagens.postagem FROM postagens WHERE id={$id};")->fetch_object();
		return $publicacao->postagem;
	}
	//--------------------------------------------------------------------------
	/**
	 * Função para escrever as opções para "Postar em " do painel administrativo
	 *
	 *
	 * @access public
	 * @return string
	 */
	public function getPostarEm() {
		//declarando retorno
		$retorno = "";
		$query = $this->mysqli->query("SELECT id, nome FROM paginas_post;");
		while ($pagina = $query->fetch_object()) {
			$retorno .= "
			<option id=\"op{$pagina->id}\" value=\"{$pagina->id}\">{$pagina->nome}</option>
			";
		}
		$query->close();
		return $retorno;
	}
	//-------------------------------------------------------------------------------
	/**
	 * Função para retornar as solicitações para o SOF
	 *
	 * @access public
	 * @return string
	 *
	 */
	public function getSolicitacoesAdmin() {
		//declarando retorno
		$retorno = "";
		$query = $this->mysqli->query("SELECT pedido.id, pedido.id_setor, setores.nome AS nome_setor, DATE_FORMAT(pedido.data_pedido, '%d/%m/%Y') AS data_pedido, pedido.ref_mes, pedido.prioridade, pedido.status, pedido.valor FROM pedido, setores WHERE pedido.alteracao = 0 AND pedido.id_setor = setores.id ORDER BY data_pedido DESC;");
		while ($pedido = $query->fetch_object()) {
			$retorno .= "
			<tr>
				<td>
					<a class=\"modal-close\" href=\"javascript:analisarPedido({$pedido->id}, {$pedido->id_setor});\" title=\"Analisar\"><span class=\"icon\">create<span></a>
					<a class=\"modal-close\" href=\"javascript:imprimir({$pedido->id});\" title=\"Imprimir\"><span class=\"icon\">print<span></a>
				</td>
				<td>{$pedido->id}</td>
				<td>{$pedido->nome_setor}</td>
				<td>{$pedido->data_pedido}</td>
				<td>{$pedido->ref_mes}</td>
				<td>{$pedido->prioridade}</td>
				<td>{$pedido->status}</td>
				<td>R$ {$pedido->valor}</td>
			</tr>
			";
		}
		$query->close();
		return $retorno;
	}
	//--------------------------------------------------------------------------------
	/**
	 * Função para trazer as informações de um pedido a ser analisado
	 *
	 * @access public
	 * @return string
	 */
	public function getItensPedidoAnalise($id_pedido) {
		//declarando retorno
		$retorno = "";
		$query = $this->mysqli->query("SELECT itens.qt_contrato, itens.id AS id_itens, itens_pedido.qtd AS qtd_solicitada, itens_pedido.valor, itens.nome_fornecedor, itens.num_licitacao, itens.dt_inicio, itens.dt_fim, itens.dt_geracao, itens.cod_reduzido, itens.complemento_item, itens.vl_unitario, itens.qt_saldo, itens.cod_despesa, itens.descr_despesa, itens.num_contrato, itens.num_processo, itens.descr_mod_compra, itens.num_licitacao, itens.cgc_fornecedor, itens.num_extrato, itens.cod_estruturado, itens.nome_unidade, itens.descricao, itens.qt_contrato, itens.vl_contrato, itens.qt_utilizado, itens.vl_utilizado, itens.qt_saldo, itens.vl_saldo FROM itens_pedido, itens WHERE itens_pedido.id_pedido = {$id_pedido} AND itens_pedido.id_item = itens.id;");

		while ($item = $query->fetch_object()) {
			$item->descr_despesa = utf8_encode($item->descr_despesa);
			$item->descr_mod_compra = utf8_encode($item->descr_mod_compra);
			$item->nome_unidade = utf8_encode($item->nome_unidade);
			$item->complemento_item = utf8_encode($item->complemento_item);
			if ($item->dt_fim == '') {
				$item->dt_fim = "----------";
			}
			$retorno .= "
			<tr id=\"row_item{$item->id_itens}\">
				<td>
					<a class=\"modal-close\" href=\"javascript:cancelaItem({$item->id_itens});\" title=\"Item Cancelado\"><span id=\"icon-cancela-item{$item->id_itens}\" class=\"icon text-red\">cancel<span>
					</a>
				</td>
				<td>{$item->cod_reduzido}</td>
				<td>{$item->cod_despesa}</td>
				<td>{$item->descr_despesa}</td>
				<td>{$item->num_contrato}</td>
				<td>{$item->num_processo}</td>
				<td>{$item->descr_mod_compra}</td>
				<td>{$item->num_licitacao}</td>
				<td>{$item->dt_inicio}</td>
				<td>{$item->dt_fim}</td>
				<td>{$item->dt_geracao}</td>
				<td>{$item->cgc_fornecedor}</td>
				<td>{$item->nome_fornecedor}</td>
				<td>{$item->num_extrato}</td>
				<td>{$item->cod_estruturado}</td>
				<td>{$item->nome_unidade}</td>
				<td>
					<button onclick=\"viewCompl('{$item->complemento_item}');\" class=\"btn btn-flat waves-attach waves-effect\" type=\"button\" title=\"Ver Complemento do Item\">complemento_item</button>
				</td>
				<td>{$item->descricao}</td>
				<td>R$ {$item->vl_unitario}</td>
				<td>{$item->qt_contrato}</td>
				<td>{$item->vl_contrato}</td>
				<td>{$item->qt_utilizado}</td>
				<td>{$item->vl_utilizado}</td>
				<td>{$item->qt_saldo}</td>
				<td>{$item->vl_saldo}</td>
				<td>{$item->qtd_solicitada}</td>
				<td>R$ {$item->valor}</td>
				<td>
					<input type=\"hidden\" name=\"id_item[]\" value=\"{$item->id_itens}\">
					<input id=\"item_cancelado{$item->id_itens}\" type=\"hidden\" name=\"item_cancelado[]\" value=\"0\">
					<input type=\"hidden\" name=\"qtd_solicitada[]\" value=\"{$item->qtd_solicitada}\">
					<input type=\"hidden\" name=\"qt_saldo[]\" value=\"{$item->qt_saldo}\">
					<input type=\"hidden\" name=\"qt_utilizado[]\" value=\"{$item->qt_utilizado}\">
					<input type=\"hidden\" name=\"vl_saldo[]\" value=\"{$item->vl_saldo}\">
					<input type=\"hidden\" name=\"vl_utilizado[]\" value=\"{$item->vl_utilizado}\">
					<input type=\"hidden\" name=\"valor_item[]\" value=\"{$item->valor}\">
				</td>
			</tr>
			";
		}
		$query->close();

		return $retorno;
	}
	//---------------------------------------------------------------------------------
	/**
	 * Função para trazer o restante das informações para analisar o pedido:
	 *               saldo, total, prioridade, fase, etc.
	 *
	 * @access public
	 * @return string
	 *
	 */
	public function getInfoPedidoAnalise($id_pedido, $id_setor) {
		$mes = date("n");
		$ano = date("Y");
		$query = $this->mysqli->query("SELECT saldo_setor.saldo, pedido.prioridade, pedido.status, pedido.ref_mes, pedido.valor FROM saldo_setor, pedido WHERE saldo_setor.id_setor = {$id_setor} AND saldo_setor.mes = {$mes} AND saldo_setor.ano = {$ano} AND pedido.id = {$id_pedido};");
		$pedido = $query->fetch_object();
		$pedido->status = str_replace(" ", "", $pedido->status);
		$query->close();
		return json_encode($pedido);
	}
	# =======================================================================================
	#                                USERS  SETORES
	# =======================================================================================

	// -----------------------------------------------------------------------
	/**
	 *	Função que retorna uma tabela com as solicitações de alteração de pedidos
	 *
	 *	@access public
	 *	@return string
	 */
	public function getSolicAltPedidos($id_setor) {
		$retorno = "";
		$query = $this->mysqli->query("SELECT solic_alt_pedido.id_pedido, DATE_FORMAT(solic_alt_pedido.data_solicitacao, '%d/%m/%Y') AS data_solicitacao, DATE_FORMAT(solic_alt_pedido.data_analise, '%d/%m/%Y') AS data_analise, solic_alt_pedido.justificativa, solic_alt_pedido.status FROM solic_alt_pedido WHERE solic_alt_pedido.id_setor = {$id_setor};");
		$status = $label = "";
		while ($solic = $query->fetch_object()) {
			switch ($solic->status) {
			case 0:
				$status = "Reprovado";
				$label = "label-red";
				break;
			case 1:
				$status = "Aprovado";
				$label = "label-green";
				break;
			default:
				$status = "Aberto";
				$label = "label-orange";
				$solic->data_analise = "--------------";
				break;
			}
			$retorno .= "
			<tr>
				<td>{$solic->id_pedido}</td>
				<td>{$solic->data_solicitacao}</td>
				<td>{$solic->data_analise}</td>
				<td>
					<button onclick=\"viewCompl('{$solic->justificativa}');\" class=\"btn btn-flat waves-attach waves-effect\" type=\"button\" title=\"Ver Justificativa\">JUSTIFICATIVA</button>
				</td>
				<td><span class=\"label {$label}\" style=\"font-size: 11pt !important; font-weight: bold;\">{$status}</span></td>
			</tr>
			";
		}
		$query->close();
		return $retorno;
	}
	// -----------------------------------------------------------------------
	/**
	 *	Função para retornar os meses em php/solicitacoes.php RefMes
	 *
	 *	@access public
	 *	@return string
	 */
	public function getMeses() {
		$retorno = "";
		$query = $this->mysqli->query("SELECT id, sigla_mes FROM mes LIMIT 12;");
		while ($mes = $query->fetch_object()) {
			$retorno .= "
			<option value=\"{$mes->id}\">{$mes->sigla_mes}</option>
			";
		}
		$query->close();
		return $retorno;
	}
	// -----------------------------------------------------------------------
	/**
	 *	Função que retorna as solicitações de adiantamento de saldos do setor
	 *
	 *	@access public
	 *	@return string
	 */
	public function getSolicAdiSetor($id_setor) {
		$retorno = "";
		$query = $this->mysqli->query("SELECT saldos_adiantados.id, DATE_FORMAT(saldos_adiantados.data_solicitacao, '%d/%m/%Y') AS data_solicitacao, DATE_FORMAT(saldos_adiantados.data_analise, '%d/%m/%Y') AS data_analise, mes.sigla_mes, saldos_adiantados.ano, saldos_adiantados.valor_adiantado, saldos_adiantados.justificativa, saldos_adiantados.status FROM saldos_adiantados, mes WHERE saldos_adiantados.id_setor = {$id_setor} AND (mes.id = saldos_adiantados.mes_subtraido || saldos_adiantados.mes_subtraido = 0) ORDER BY saldos_adiantados.id DESC;");
		$label = $status = "";
		while ($solic = $query->fetch_object()) {
			switch ($solic->status) {
			case 0:
				$label = "label-red";
				$status = "Reprovado";
				break;
			case 1:
				$label = "label-green";
				$status = "Aprovado";
				break;
			case 2:
				$label = "label-orange";
				$status = "Aberto";
				$solic->data_analise = "--------------";
				break;
			}
			$retorno .= "
			<tr>
				<td>{$solic->data_solicitacao}</td>
				<td>{$solic->data_analise}</td>
				<td>{$solic->sigla_mes} / {$solic->ano}</td>
				<td>R$ {$solic->valor_adiantado}</td>
				<td>
					<button onclick=\"viewCompl('{$solic->justificativa}');\" class=\"btn btn-flat waves-attach waves-effect\" type=\"button\" title=\"Ver Justificativa\">JUSTIFICATIVA</button>
				</td>
				<td><span class=\"label {$label}\" style=\"font-size: 11pt !important; font-weight: bold;\">{$status}</span></td>
			</tr>
			";
		}
		$query->close();
		return $retorno;
	}
	//------------------------------------------------------------------------
	/**
	 * Função para retornar o saldo disponível do setor logado
	 *
	 * @access public
	 * @return object
	 *
	 */
	public function getSaldoSetor($id_setor) {
		//pegando data do mes
		$mes = date("n");
		$ano = date("Y");
		//executando query
		$query = $this->mysqli->query("SELECT saldo, saldo_suplementado, saldo_aditivado FROM saldo_setor WHERE id_setor = {$id_setor} AND mes = {$mes} AND ano = {$ano};");
		if ($query->num_rows > 0) {
			$obj_query = $query->fetch_object();
		} else {
			$obj_query = new stdClass;
			$obj_query->saldo = "0.000";
			$obj_query->saldo_suplementado = "0.000";
			$obj_query->saldo_aditivado = "0.000";
		}

		return $obj_query;
	}
	//------------------------------------------------------------------------
	/**
	 * Função para retornar o saldo do mês anterior
	 *
	 * @access public
	 * @return string
	 */
	public function getSaldoMesAnterior($id_setor) {
		$mes = date("n");
		$ano = date("Y");
		if ($mes == 1) {
			$mes = 12;
			$ano--;
		} else {
			$mes--;
		}
		//executando query
		$query = $this->mysqli->query("SELECT saldo FROM saldo_setor WHERE id_setor = {$id_setor} AND mes = {$mes} AND ano = {$ano};");

		if ($query->num_rows < 1) {
			return "0";
		} else {
			$obj_query = $query->fetch_object();

			return $obj_query->saldo;
		}
	}
	//---------------------------------------------------------------------------------
	/**
	 * Função para mostrar os itens de um processo pesquisado no menu solicitações
	 *
	 * @access public
	 * @return string
	 */
	public function getConteudoProcesso($busca) {
		//declarando retorno
		$retorno = "";

		$query = $this->mysqli->query("SELECT itens.id, itens.id_item_processo, itens.nome_fornecedor, itens.cod_reduzido, itens.complemento_item, itens.vl_unitario, itens.qt_contrato, itens.qt_utilizado, itens.vl_utilizado, itens.qt_saldo, itens.vl_saldo FROM itens WHERE num_processo LIKE '%{$busca}%' AND cancelado = 0;");

		while ($item = $query->fetch_object()) {
			//remove as aspas do complemento_item
			$item->complemento_item = str_replace("\"", "", $item->complemento_item);
			$item->vl_unitario = str_replace(",", ".", $item->vl_unitario);
			$item->nome_fornecedor = utf8_encode($item->nome_fornecedor);
			$item->complemento_item = utf8_encode($item->complemento_item);

			$retorno .= "
			<tr>
				<td>
					<a class=\"modal-close\" href=\"javascript:checkItemPedido({$item->id}, '{$item->vl_unitario}', {$item->qt_saldo});\"><span class=\"icon\">add<span></a>
				</td>
				<td>{$item->nome_fornecedor}</td>
				<td>{$item->cod_reduzido}</td>
				<td><input type=\"number\" id=\"qtd{$item->id}\" min=\"1\" max=\"$item->qt_saldo\"></td>
				<td>
					<a onclick=\"viewCompl('{$item->complemento_item}');\" class=\"btn btn-flat waves-attach waves-effect\" type=\"button\" title=\"Mais Detalhes\">complemento_item</a>
				</td>
				<td style=\"display: none;\">{$item->complemento_item}</td>
				<td>{$item->vl_unitario}</td>
				<td>{$item->qt_saldo}</td>
				<td>{$item->qt_utilizado}</td>
				<td>{$item->vl_saldo}</td>
				<td>{$item->vl_utilizado}</td>
				<td>{$item->qt_contrato}</td>
			</tr>
			";
		}
		$query->close();
		return $retorno;
	}
	//---------------------------------------------------------------------------------
	/**
	 * Função para trazer a linha do item anexado ao pedido
	 *
	 * @access public
	 * @return string
	 */
	public function addItemPedido($id_item, $qtd) {
		//executando a query
		$query = $this->mysqli->query("SELECT id, nome_fornecedor, num_licitacao, cod_reduzido, complemento_item, vl_unitario, qt_saldo, qt_contrato, qt_utilizado, vl_saldo, vl_contrato, vl_utilizado FROM itens WHERE id = {$id_item};");
		$item = $query->fetch_object();
		$query->close();
		$item->complemento_item = str_replace("\"", "", $item->complemento_item);

		$item->vl_unitario = str_replace(",", ".", $item->vl_unitario);
		$valor = $qtd * $item->vl_unitario;
		$retorno = "
		<tr id=\"row{$id_item}\">
			<td><a class=\"modal-close\" href=\"javascript:removeTableRow($id_item, '$valor');\"><span class=\"icon\">delete</span></a></td>
			<td>{$item->cod_reduzido}</td>
			<td>
				<button onclick=\"viewCompl('{$item->complemento_item}');\" class=\"btn btn-flat waves-attach waves-effect\" type=\"button\" title=\"Ver Complemento do Item\">complemento_item</button>
			</td>
			<td>R$ {$item->vl_unitario}</td>
			<td>{$item->nome_fornecedor}</td>
			<td>{$item->num_licitacao}</td>
			<td>{$qtd}</td>
			<td>R$ {$valor}</td>
			<td>
				<input type=\"hidden\" name=\"id_item[]\" value=\"{$id_item}\">
				<input type=\"hidden\" name=\"qtd_solicitada[]\" value=\"{$qtd}\">
				<input type=\"hidden\" name=\"qtd_disponivel[]\" value=\"{$item->qt_saldo}\">
				<input type=\"hidden\" name=\"qtd_contrato[]\" value=\"{$item->qt_contrato}\">
				<input type=\"hidden\" name=\"qtd_utilizado[]\" value=\"{$item->qt_utilizado}\">
				<input type=\"hidden\" name=\"vl_saldo[]\" value=\"{$item->vl_saldo}\">
				<input type=\"hidden\" name=\"vl_contrato[]\" value=\"{$item->vl_contrato}\">
				<input type=\"hidden\" name=\"vl_utilizado[]\" value=\"{$item->vl_utilizado}\">
				<input type=\"hidden\" name=\"valor[]\" value=\"{$valor}\">
			</td>
		</tr>
		";
		return $retorno;
	}
	//---------------------------------------------------------------------------------
	/**
	 * Função para retornar os rascunhos para continuar editando
	 *
	 * @access public
	 * @return string
	 */
	public function getRascunhos($id_setor) {
		//declarando retorno
		$retorno = "";
		$query = $this->mysqli->query("SELECT id, DATE_FORMAT(data_pedido, '%d/%m/%Y') AS data_pedido, ref_mes, valor FROM pedido WHERE id_setor = {$id_setor} AND alteracao = 1 AND status = 'Rascunho';");

		while ($rascunho = $query->fetch_object()) {
			$retorno .= "
			<tr>
				<td>{$rascunho->ref_mes}</td>
				<td>{$rascunho->data_pedido}</td>
				<td>R$ {$rascunho->valor}</td>
				<td>
					<button class=\"btn btn-default btn-sm\" style=\"text-transform: none !important;font-weight: bold;\" onclick=\"editaPedido({$rascunho->id});\" title=\"Editar\"><span class=\"icon\">create</span></button>
				</td>
			</tr>
			";
		}
		$query->close();
		return $retorno;
	}
	//--------------------------------------------------------------------------------
	/**
	 * Função para retornar o conteúdo de um pedido para edição
	 *
	 * @access public
	 * @return string
	 */
	public function getConteudoPedido($id_pedido) {
		//declarando retorno
		$retorno = "";
		$query = $this->mysqli->query("SELECT itens.qt_contrato, itens.id AS id_itens, itens_pedido.qtd AS qtd_solicitada, itens_pedido.valor, itens.nome_fornecedor, itens.num_licitacao, itens.cod_reduzido, itens.complemento_item, itens.vl_unitario, itens.qt_saldo, itens.qt_contrato, itens.qt_utilizado, itens.vl_saldo, itens.vl_contrato, itens.vl_utilizado FROM itens_pedido, itens WHERE itens_pedido.id_pedido = {$id_pedido} AND itens_pedido.id_item = itens.id");
		while ($item = $query->fetch_object()) {
			$id_item = $item->id_itens;
			$item->complemento_item = str_replace("\"", "", $item->complemento_item);
			$item->vl_unitario = str_replace(",", ".", $item->vl_unitario);

			$retorno .= "
			<tr id=\"row{$id_item}\">
				<td><a class=\"modal-close\" href=\"javascript:removeTableRow($id_item, '$item->valor');\"><span class=\"icon\">delete</span></a></td>
				<td>{$item->cod_reduzido}</td>
				<td>
					<button onclick=\"viewCompl('{$item->complemento_item}');\" class=\"btn btn-flat waves-attach waves-effect\" type=\"button\" title=\"Ver Complemento do Item\">complemento_item</button>
				</td>
				<td>R$ {$item->vl_unitario}</td>
				<td>{$item->nome_fornecedor}</td>
				<td>{$item->num_licitacao}</td>
				<td>{$item->qtd_solicitada}</td>
				<td>R$ {$item->valor}</td>
				<td>
					<input type=\"hidden\" name=\"id_item[]\" value=\"{$id_item}\">
					<input type=\"hidden\" name=\"qtd_solicitada[]\" value=\"{$item->qtd_solicitada}\">
					<input type=\"hidden\" name=\"qtd_disponivel[]\" value=\"{$item->qt_saldo}\">
					<input type=\"hidden\" name=\"qtd_contrato[]\" value=\"{$item->qt_contrato}\">
					<input type=\"hidden\" name=\"qtd_utilizado[]\" value=\"{$item->qt_utilizado}\">
					<input type=\"hidden\" name=\"vl_saldo[]\" value=\"{$item->vl_saldo}\">
					<input type=\"hidden\" name=\"vl_contrato[]\" value=\"{$item->vl_contrato}\">
					<input type=\"hidden\" name=\"vl_utilizado[]\" value=\"{$item->vl_utilizado}\">
					<input type=\"hidden\" name=\"valor[]\" value=\"{$item->valor}\">
				</td>
			</tr>
			";
		}
		$query->close();
		return $retorno;
	}
	//---------------------------------------------------------------------------------
	/**
	 * Função dispara logo após clicar em editar rascunho de pedido
	 *
	 * @access public
	 * @return string
	 *
	 */
	public function getPopulaRascunho($id_pedido, $id_setor) {
		$mes = date("n");
		$ano = date("Y");
		$query = $this->mysqli->query("SELECT saldo_setor.saldo, pedido.ref_mes, pedido.valor FROM saldo_setor, pedido WHERE pedido.id = {$id_pedido} AND saldo_setor.id_setor = {$id_setor} AND saldo_setor.mes = {$mes} AND saldo_setor.ano = {$ano};");
		$pedido = $query->fetch_object();
		$query->close();
		return json_encode($pedido);
	}
	//---------------------------------------------------------------------------------
	/**
	 * Função para o setor acompanhar o andamento do seu pedido
	 *
	 * @access public
	 * @return string
	 *
	 */
	public function getMeusPedidos($id_setor) {
		//declarando retorno
		$retorno = "";
		$query = $this->mysqli->query("SELECT id, DATE_FORMAT(data_pedido, '%d/%m/%Y') AS data_pedido, ref_mes, prioridade, status, valor FROM pedido WHERE id_setor = {$id_setor} AND alteracao = 0 ORDER BY data_pedido DESC;");
		while ($pedido = $query->fetch_object()) {
			$retorno .= "
			<tr>
				<td>{$pedido->ref_mes}</td>
				<td>{$pedido->data_pedido}</td>
				<td>{$pedido->prioridade}</td>
				<td><span class=\"label\" style=\"font-size: 11pt;\">{$pedido->status}</span></td>
				<td>R$ {$pedido->valor}</td>
				<td>
					<button class=\"btn btn-default btn-sm\" style=\"text-transform: none !important;font-weight: bold;\" onclick=\"solicAltPed($pedido->id);\" title=\"Solicitar Alteração\"><span class=\"icon\">build</span></button>
					<button class=\"btn btn-default btn-sm\" style=\"text-transform: none !important;font-weight: bold;\" onclick=\"imprimir({$pedido->id});\" title=\"Imprimir\"><span class=\"icon\">print</span></button>
				</td>
			</tr>
			";
		}
		$query->close();
		return $retorno;
	}
	public function getProcessos() {
		$retorno = "";
		$query = $this->mysqli->query("SELECT DISTINCT num_processo FROM itens;");
		if ($query->num_rows > 0) {
			while ($processo = $query->fetch_object()) {
				$retorno .= "
					<tr>
						<td>{$processo->num_processo}</td>
						<td>
							<button title=\"Pesquisar Processo\" onclick=\"pesquisarProcesso('{$processo->num_processo}')\" style=\"text-transform: none !important;font-weight: bold;\" class=\"btn btn-default btn-sm\"><span class=\"icon\">search</span></button>
						</td>
					</tr>
				";
			}
		} else {
			$retorno = "Ocorreu um erro no servidor. Contate o administrador.";
		}
		return $retorno;
	}
}
?>
