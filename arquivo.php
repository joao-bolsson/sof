<?php
	include_once 'class/Conexao.class.php';
	$obj_Conexao = new Conexao();
	$mysqli = $obj_Conexao->getConexao();

	// Abre o Arquvio no Modo r (para leitura)
	$arquivo = fopen('processos20152.tsv', 'r');

	while (!feof($arquivo)) {
		$linha = fgets($arquivo, 1024);
		$dados = explode('	', $linha);
		if($dados[0] != "ID_ITEM_PROCESSO"){

		$data = $dados[6];
		$ano = substr($data, 6, 4);
		$mes = substr($data, 3, 2);
		$dia = substr($data, 0, 2);
		$data = $ano."-".$mes."-".$dia;
		$dados[9] = $data;

		$data = $dados[7];
		$ano = substr($data, 6, 4);
		$mes = substr($data, 3, 2);
		$dia = substr($data, 0, 2);
		$data = $ano."-".$mes."-".$dia;
		$dados[10] = $data;

		$data = $dados[8];
		$ano = substr($data, 6, 4);
		$mes = substr($data, 3, 2);
		$dia = substr($data, 0, 2);
		$data = $ano."-".$mes."-".$dia;
		$dados[11] = $data;
		/*
id_item_processo, id_item_contrato, cod_despesa, descr_despesa, descr_tipo_doc, num_contrato, num_processo, descr_mod_compra, num_licitacao, dt_inicio, dt_fim, dt_geracao, cgc_fornecedor, nome_fornecedor, num_extrato, cod_estruturado, nome_unidade, cod_reduzido, complemento_item, descricao, id_extrato_contr, vl_unitario, qt_contrato, vl_contrato, qt_utilizado, vl_utilizado, qt_saldo, vl_saldo, id_unidade, ano_orcamento

| id               | int(10) unsigned | NO   | PRI | NULL    | auto_increment |
| 0id_item_processo | int(10) unsigned | YES  |     | NULL    |                |
| 1id_item_contrato | int(10) unsigned | YES  |     | NULL    |                |
| 2cod_despesa      | varchar(15)      | YES  |     | NULL    |                |
| 3descr_despesa    | varchar(100)     | YES  |     | NULL    |                |
| 4descr_tipo_doc   | varchar(80)      | YES  |     | NULL    |                |
| 5num_contrato     | varchar(15)      | YES  |     | NULL    |                |
| 6num_processo     | varchar(25)      | YES  |     | NULL    |                |
| 7descr_mod_compra | varchar(50)      | YES  |     | NULL    |                |
| 8num_licitacao    | varchar(15)      | YES  |     | NULL    |                |
| 9dt_inicio        | date             | YES  |     | NULL    |                |
| 10dt_fim           | date             | YES  |     | NULL    |                |
| 11dt_geracao       | date             | YES  |     | NULL    |                |
| 12cgc_fornecedor   | varchar(20)      | YES  |     | NULL    |                |
| 13nome_fornecedor  | varchar(150)     | YES  |     | NULL    |                |
| 14num_extrato      | varchar(20)      | YES  |     | NULL    |                |
| 15cod_estruturado  | varchar(20)      | YES  |     | NULL    |                |
| 16nome_unidade     | varchar(100)     | YES  |     | NULL    |                |
| 17cod_reduzido     | varchar(20)      | YES  |     | NULL    |                |
| 18complemento_item | text             | YES  |     | NULL    |                |
| 19descricao        | varchar(200)     | YES  |     | NULL    |                |
| 20id_extrato_contr | int(10) unsigned | YES  |     | NULL    |                |
| 21vl_unitario      | varchar(30)      | YES  |     | NULL    |                |
| 22qt_contrato      | int(11)          | YES  |     | NULL    |                |
| 23vl_contrato      | varchar(30)      | YES  |     | NULL    |                |
| 24qt_utilizado     | int(10) unsigned | YES  |     | NULL    |                |
| 25vl_utilizado     | varchar(30)      | YES  |     | NULL    |                |
| 26qt_saldo         | int(11)          | YES  |     | NULL    |                |
| 27vl_saldo         | varchar(30)      | YES  |     | NULL    |                |
| 28id_unidade       | int(10) unsigned | YES  |     | NULL    |                |
| 29ano_orcamento    | int(10) unsigned | YES
*/
			//$insert = $mysqli->query("INSERT INTO itens VALUES(NULL, {$dados[0]}, {$dados[1]}, '{$dados[2]}', '{$dados[3]}', '{$dados[4]}', '{$dados[5]}', '{$dados[6]}', '{$dados[7]}', '{$dados[8]}', '{$dados[9]}', '{$dados[10]}', '{$dados[11]}', '{$dados[12]}', '{$dados[13]}', '{$dados[14]}', '{$dados[15]}', '{$dados[16]}', '{$dados[17]}', '{$dados[18]}', '{$dados[19]}', '{$dados[20]}', '{$dados[21]}', {$dados[22]}, '{$dados[23]}', {$dados[24]}, '{$dados[25]}', {$dados[26]}, '{$dados[27]}', '{$dados[28]}', '{$dados[29]}')");

			echo "INSERT INTO processo VALUES(NULL, {$dados[0]}, {$dados[1]}, '{$dados[2]}', '{$dados[3]}', '{$dados[4]}', '{$dados[5]}', '{$dados[6]}', '{$dados[7]}', '{$dados[8]}', '{$dados[9]}', '{$dados[10]}', '{$dados[11]}', '{$dados[12]}', '{$dados[13]}', '{$dados[14]}', '{$dados[15]}', '{$dados[16]}', '{$dados[17]}', '{$dados[18]}', '{$dados[19]}', '{$dados[20]}', '{$dados[21]}', {$dados[22]}, '{$dados[23]}', {$dados[24]}, '{$dados[25]}', {$dados[26]}, '{$dados[27]}', '{$dados[28]}', '{$dados[29]}')<br><br>";
		}
	}
	fclose($arquivo);
?>
