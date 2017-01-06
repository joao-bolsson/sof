SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;


CREATE TABLE `comentarios` (
  `id` int(10) UNSIGNED NOT NULL,
  `id_pedido` int(10) UNSIGNED NOT NULL,
  `data_coment` date NOT NULL,
  `prioridade` int(1) UNSIGNED NOT NULL,
  `status` int(1) UNSIGNED NOT NULL,
  `valor` varchar(50) NOT NULL,
  `comentario` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `itens` (
  `id` int(10) UNSIGNED NOT NULL,
  `id_item_processo` int(10) UNSIGNED DEFAULT NULL,
  `id_item_contrato` int(10) UNSIGNED DEFAULT NULL,
  `cod_despesa` varchar(15) DEFAULT NULL,
  `descr_despesa` varchar(100) DEFAULT NULL,
  `descr_tipo_doc` varchar(80) DEFAULT NULL,
  `num_contrato` varchar(15) DEFAULT NULL,
  `num_processo` varchar(25) DEFAULT NULL,
  `descr_mod_compra` varchar(50) DEFAULT NULL,
  `num_licitacao` varchar(15) DEFAULT NULL,
  `dt_inicio` varchar(15) DEFAULT NULL,
  `dt_fim` varchar(15) DEFAULT NULL,
  `dt_geracao` varchar(15) DEFAULT NULL,
  `cgc_fornecedor` varchar(20) DEFAULT NULL,
  `nome_fornecedor` varchar(150) DEFAULT NULL,
  `num_extrato` varchar(20) DEFAULT NULL,
  `cod_estruturado` varchar(20) DEFAULT NULL,
  `nome_unidade` varchar(100) DEFAULT NULL,
  `cod_reduzido` varchar(20) DEFAULT NULL,
  `complemento_item` text,
  `descricao` varchar(200) DEFAULT NULL,
  `id_extrato_contr` int(10) UNSIGNED DEFAULT NULL,
  `vl_unitario` varchar(30) DEFAULT NULL,
  `qt_contrato` int(11) DEFAULT NULL,
  `vl_contrato` varchar(30) DEFAULT NULL,
  `qt_utilizado` int(10) UNSIGNED DEFAULT NULL,
  `vl_utilizado` varchar(30) DEFAULT NULL,
  `qt_saldo` int(11) DEFAULT NULL,
  `vl_saldo` varchar(30) DEFAULT NULL,
  `id_unidade` int(10) UNSIGNED DEFAULT NULL,
  `ano_orcamento` int(10) UNSIGNED DEFAULT NULL,
  `cancelado` tinyint(1) NOT NULL,
  `chave` varchar(50) DEFAULT NULL,
  `seq_item_processo` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `itens_pedido` (
  `id` int(10) UNSIGNED NOT NULL,
  `id_pedido` int(10) UNSIGNED NOT NULL,
  `id_item` int(10) UNSIGNED NOT NULL,
  `qtd` int(10) UNSIGNED NOT NULL,
  `valor` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `licitacao` (
  `id` int(10) UNSIGNED NOT NULL,
  `id_pedido` int(10) UNSIGNED NOT NULL,
  `tipo` tinyint(3) UNSIGNED NOT NULL,
  `numero` varchar(30) DEFAULT NULL,
  `uasg` varchar(30) DEFAULT NULL,
  `processo_original` varchar(30) DEFAULT NULL,
  `gera_contrato` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `licitacao_tipo` (
  `id` tinyint(3) UNSIGNED NOT NULL,
  `nome` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `mes` (
  `id` int(2) UNSIGNED NOT NULL,
  `sigla_mes` varchar(3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `paginas_post` (
  `id` int(10) UNSIGNED NOT NULL,
  `tabela` varchar(30) NOT NULL,
  `nome` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `pedido` (
  `id` int(10) UNSIGNED NOT NULL,
  `id_setor` int(10) UNSIGNED NOT NULL,
  `id_usuario` int(10) UNSIGNED NOT NULL,
  `data_pedido` date NOT NULL,
  `ref_mes` int(2) UNSIGNED NOT NULL,
  `alteracao` tinyint(1) NOT NULL,
  `prioridade` int(1) UNSIGNED NOT NULL,
  `status` int(1) UNSIGNED NOT NULL,
  `valor` varchar(50) NOT NULL,
  `obs` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `pedido_empenho` (
  `id` int(10) UNSIGNED NOT NULL,
  `id_pedido` int(10) UNSIGNED NOT NULL,
  `empenho` varchar(30) NOT NULL,
  `data` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `pedido_fonte` (
  `id` int(10) UNSIGNED NOT NULL,
  `id_pedido` int(10) UNSIGNED NOT NULL,
  `fonte_recurso` varchar(100) DEFAULT NULL,
  `ptres` varchar(50) DEFAULT NULL,
  `plano_interno` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `pedido_grupo` (
  `id_pedido` int(10) UNSIGNED NOT NULL,
  `id_grupo` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `pedido_log_status` (
  `id_pedido` int(10) UNSIGNED NOT NULL,
  `id_status` int(1) UNSIGNED NOT NULL,
  `data` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `postagens` (
  `id` int(10) UNSIGNED NOT NULL,
  `tabela` int(10) UNSIGNED NOT NULL,
  `titulo` varchar(100) NOT NULL,
  `data` date NOT NULL,
  `ativa` tinyint(1) NOT NULL,
  `postagem` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `prioridade` (
  `id` int(1) UNSIGNED NOT NULL,
  `nome` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `problemas` (
  `id` int(10) UNSIGNED NOT NULL,
  `id_setor` int(10) UNSIGNED NOT NULL,
  `assunto` varchar(30) NOT NULL,
  `descricao` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `processos` (
  `id` int(10) UNSIGNED NOT NULL,
  `num_processo` varchar(25) NOT NULL,
  `tipo` int(1) UNSIGNED NOT NULL,
  `estante` varchar(30) NOT NULL,
  `prateleira` varchar(30) NOT NULL,
  `entrada` varchar(10) NOT NULL,
  `saida` varchar(10) NOT NULL,
  `responsavel` varchar(30) NOT NULL,
  `retorno` varchar(10) NOT NULL,
  `obs` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `processos_tipo` (
  `id` int(1) UNSIGNED NOT NULL,
  `nome` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `saldos_adiantados` (
  `id` int(10) UNSIGNED NOT NULL,
  `id_setor` int(10) UNSIGNED NOT NULL,
  `data_solicitacao` date NOT NULL,
  `data_analise` date DEFAULT NULL,
  `valor_adiantado` varchar(50) NOT NULL,
  `justificativa` text NOT NULL,
  `status` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `saldos_lancamentos` (
  `id` int(10) UNSIGNED NOT NULL,
  `id_setor` int(10) UNSIGNED NOT NULL,
  `data` date NOT NULL,
  `valor` varchar(50) NOT NULL,
  `categoria` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `saldos_transferidos` (
  `id` int(10) UNSIGNED NOT NULL,
  `id_setor_ori` int(10) UNSIGNED NOT NULL,
  `id_setor_dest` int(10) UNSIGNED NOT NULL,
  `valor` varchar(50) NOT NULL,
  `justificativa` text
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `saldo_categoria` (
  `id` int(1) NOT NULL,
  `nome` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `saldo_setor` (
  `id` int(10) UNSIGNED NOT NULL,
  `id_setor` int(10) UNSIGNED NOT NULL,
  `saldo` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `setores` (
  `id` int(10) UNSIGNED NOT NULL,
  `nome` varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `setores_grupos` (
  `id` int(10) UNSIGNED NOT NULL,
  `id_setor` int(10) UNSIGNED NOT NULL,
  `cod` varchar(10) DEFAULT NULL,
  `nome` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `sistema` (
  `ativo` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `solic_alt_pedido` (
  `id` int(10) UNSIGNED NOT NULL,
  `id_pedido` int(10) UNSIGNED NOT NULL,
  `id_setor` int(10) UNSIGNED NOT NULL,
  `data_solicitacao` date NOT NULL,
  `data_analise` date DEFAULT NULL,
  `justificativa` text NOT NULL,
  `status` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `status` (
  `id` int(1) UNSIGNED NOT NULL,
  `nome` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `usuario` (
  `id` int(10) UNSIGNED NOT NULL,
  `nome` varchar(40) NOT NULL,
  `login` varchar(15) NOT NULL,
  `senha` varchar(34) NOT NULL,
  `id_setor` int(10) UNSIGNED NOT NULL,
  `email` varchar(40) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `usuario_permissoes` (
  `id_usuario` int(10) UNSIGNED NOT NULL,
  `noticias` tinyint(1) NOT NULL,
  `saldos` tinyint(1) NOT NULL,
  `pedidos` tinyint(1) NOT NULL,
  `recepcao` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


ALTER TABLE `comentarios`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_pedido` (`id_pedido`),
  ADD KEY `status` (`status`),
  ADD KEY `prioridade` (`prioridade`);

ALTER TABLE `itens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `chave` (`chave`);

ALTER TABLE `itens_pedido`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_pedido` (`id_pedido`),
  ADD KEY `id_item` (`id_item`);

ALTER TABLE `licitacao`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_pedido` (`id_pedido`),
  ADD KEY `tipo` (`tipo`);

ALTER TABLE `licitacao_tipo`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `mes`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `paginas_post`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `pedido`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_setor` (`id_setor`),
  ADD KEY `ref_mes` (`ref_mes`),
  ADD KEY `status` (`status`),
  ADD KEY `prioridade` (`prioridade`),
  ADD KEY `id_usuario` (`id_usuario`);

ALTER TABLE `pedido_empenho`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_pedido` (`id_pedido`);

ALTER TABLE `pedido_fonte`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_pedido` (`id_pedido`);

ALTER TABLE `pedido_grupo`
  ADD KEY `id_pedido` (`id_pedido`),
  ADD KEY `id_grupo` (`id_grupo`);

ALTER TABLE `pedido_log_status`
  ADD KEY `id_pedido` (`id_pedido`),
  ADD KEY `id_status` (`id_status`);

ALTER TABLE `postagens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tabela` (`tabela`);

ALTER TABLE `prioridade`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `problemas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_setor` (`id_setor`);

ALTER TABLE `processos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tipo` (`tipo`);

ALTER TABLE `processos_tipo`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `saldos_adiantados`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_setor` (`id_setor`);

ALTER TABLE `saldos_lancamentos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_setor` (`id_setor`),
  ADD KEY `categoria` (`categoria`);

ALTER TABLE `saldos_transferidos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_setor_ori` (`id_setor_ori`),
  ADD KEY `id_setor_dest` (`id_setor_dest`);

ALTER TABLE `saldo_categoria`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `saldo_setor`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_setor` (`id_setor`);

ALTER TABLE `setores`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `setores_grupos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_setor` (`id_setor`);

ALTER TABLE `solic_alt_pedido`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_pedido` (`id_pedido`),
  ADD KEY `id_setor` (`id_setor`);

ALTER TABLE `status`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `usuario`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `login` (`login`),
  ADD KEY `id_setor` (`id_setor`);

ALTER TABLE `usuario_permissoes`
  ADD UNIQUE KEY `id_usuario` (`id_usuario`);


ALTER TABLE `comentarios`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `itens`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `itens_pedido`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `licitacao`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `licitacao_tipo`
  MODIFY `id` tinyint(3) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
ALTER TABLE `mes`
  MODIFY `id` int(2) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;
ALTER TABLE `paginas_post`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
ALTER TABLE `pedido`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `pedido_empenho`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `pedido_fonte`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `postagens`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;
ALTER TABLE `prioridade`
  MODIFY `id` int(1) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
ALTER TABLE `problemas`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
ALTER TABLE `processos`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `processos_tipo`
  MODIFY `id` int(1) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;
ALTER TABLE `saldos_adiantados`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `saldos_lancamentos`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `saldos_transferidos`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `saldo_categoria`
  MODIFY `id` int(1) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
ALTER TABLE `saldo_setor`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
ALTER TABLE `setores`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;
ALTER TABLE `setores_grupos`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=89;
ALTER TABLE `solic_alt_pedido`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `status`
  MODIFY `id` int(1) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
ALTER TABLE `usuario`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

ALTER TABLE `comentarios`
  ADD CONSTRAINT `comentarios_ibfk_1` FOREIGN KEY (`id_pedido`) REFERENCES `pedido` (`id`),
  ADD CONSTRAINT `comentarios_ibfk_2` FOREIGN KEY (`status`) REFERENCES `status` (`id`),
  ADD CONSTRAINT `comentarios_ibfk_3` FOREIGN KEY (`prioridade`) REFERENCES `prioridade` (`id`);

ALTER TABLE `itens_pedido`
  ADD CONSTRAINT `itens_pedido_ibfk_1` FOREIGN KEY (`id_pedido`) REFERENCES `pedido` (`id`),
  ADD CONSTRAINT `itens_pedido_ibfk_2` FOREIGN KEY (`id_item`) REFERENCES `itens` (`id`);

ALTER TABLE `licitacao`
  ADD CONSTRAINT `licitacao_ibfk_1` FOREIGN KEY (`id_pedido`) REFERENCES `pedido` (`id`),
  ADD CONSTRAINT `licitacao_ibfk_2` FOREIGN KEY (`tipo`) REFERENCES `licitacao_tipo` (`id`);

ALTER TABLE `pedido`
  ADD CONSTRAINT `pedido_ibfk_1` FOREIGN KEY (`id_setor`) REFERENCES `setores` (`id`),
  ADD CONSTRAINT `pedido_ibfk_2` FOREIGN KEY (`ref_mes`) REFERENCES `mes` (`id`),
  ADD CONSTRAINT `pedido_ibfk_3` FOREIGN KEY (`status`) REFERENCES `status` (`id`),
  ADD CONSTRAINT `pedido_ibfk_4` FOREIGN KEY (`prioridade`) REFERENCES `prioridade` (`id`),
  ADD CONSTRAINT `pedido_ibfk_5` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id`);

ALTER TABLE `pedido_empenho`
  ADD CONSTRAINT `pedido_empenho_ibfk_1` FOREIGN KEY (`id_pedido`) REFERENCES `pedido` (`id`);

ALTER TABLE `pedido_fonte`
  ADD CONSTRAINT `pedido_fonte_ibfk_1` FOREIGN KEY (`id_pedido`) REFERENCES `pedido` (`id`);

ALTER TABLE `pedido_grupo`
  ADD CONSTRAINT `pedido_grupo_ibfk_1` FOREIGN KEY (`id_pedido`) REFERENCES `pedido` (`id`),
  ADD CONSTRAINT `pedido_grupo_ibfk_2` FOREIGN KEY (`id_grupo`) REFERENCES `setores_grupos` (`id`);

ALTER TABLE `pedido_log_status`
  ADD CONSTRAINT `pedido_log_status_ibfk_1` FOREIGN KEY (`id_pedido`) REFERENCES `pedido` (`id`),
  ADD CONSTRAINT `pedido_log_status_ibfk_2` FOREIGN KEY (`id_status`) REFERENCES `status` (`id`);

ALTER TABLE `postagens`
  ADD CONSTRAINT `postagens_ibfk_1` FOREIGN KEY (`tabela`) REFERENCES `paginas_post` (`id`);

ALTER TABLE `problemas`
  ADD CONSTRAINT `problemas_ibfk_1` FOREIGN KEY (`id_setor`) REFERENCES `setores` (`id`);

ALTER TABLE `processos`
  ADD CONSTRAINT `processos_ibfk_1` FOREIGN KEY (`tipo`) REFERENCES `processos_tipo` (`id`);

ALTER TABLE `saldos_adiantados`
  ADD CONSTRAINT `saldos_adiantados_ibfk_1` FOREIGN KEY (`id_setor`) REFERENCES `setores` (`id`);

ALTER TABLE `saldos_lancamentos`
  ADD CONSTRAINT `saldos_lancamentos_ibfk_1` FOREIGN KEY (`id_setor`) REFERENCES `setores` (`id`),
  ADD CONSTRAINT `saldos_lancamentos_ibfk_2` FOREIGN KEY (`categoria`) REFERENCES `saldo_categoria` (`id`);

ALTER TABLE `saldos_transferidos`
  ADD CONSTRAINT `saldos_transferidos_ibfk_1` FOREIGN KEY (`id_setor_ori`) REFERENCES `setores` (`id`),
  ADD CONSTRAINT `saldos_transferidos_ibfk_2` FOREIGN KEY (`id_setor_dest`) REFERENCES `setores` (`id`);

ALTER TABLE `saldo_setor`
  ADD CONSTRAINT `saldo_setor_ibfk_1` FOREIGN KEY (`id_setor`) REFERENCES `setores` (`id`);

ALTER TABLE `setores_grupos`
  ADD CONSTRAINT `setores_grupos_ibfk_1` FOREIGN KEY (`id_setor`) REFERENCES `setores` (`id`);

ALTER TABLE `solic_alt_pedido`
  ADD CONSTRAINT `solic_alt_pedido_ibfk_1` FOREIGN KEY (`id_pedido`) REFERENCES `pedido` (`id`),
  ADD CONSTRAINT `solic_alt_pedido_ibfk_2` FOREIGN KEY (`id_setor`) REFERENCES `setores` (`id`);

ALTER TABLE `usuario`
  ADD CONSTRAINT `usuario_ibfk_1` FOREIGN KEY (`id_setor`) REFERENCES `setores` (`id`);

ALTER TABLE `usuario_permissoes`
  ADD CONSTRAINT `usuario_permissoes_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
