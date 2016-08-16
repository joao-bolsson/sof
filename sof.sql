-- phpMyAdmin SQL Dump
-- version 4.6.3deb1~trusty.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: 14-Ago-2016 às 21:14
-- Versão do servidor: 5.5.50-0ubuntu0.14.04.1
-- PHP Version: 7.0.9-1+deb.sury.org~trusty+1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sof`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `comentarios`
--

CREATE TABLE `comentarios` (
  `id` int(10) UNSIGNED NOT NULL,
  `id_pedido` int(10) UNSIGNED NOT NULL,
  `data_coment` date NOT NULL,
  `prioridade` int(1) UNSIGNED NOT NULL,
  `status` int(1) UNSIGNED NOT NULL,
  `valor` varchar(50) NOT NULL,
  `comentario` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estrutura da tabela `itens`
--

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
  `cancelado` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estrutura da tabela `itens_pedido`
--

CREATE TABLE `itens_pedido` (
  `id` int(10) UNSIGNED NOT NULL,
  `id_pedido` int(10) UNSIGNED NOT NULL,
  `id_item` int(10) UNSIGNED NOT NULL,
  `qtd` int(10) UNSIGNED NOT NULL,
  `valor` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estrutura da tabela `pedido`
--

CREATE TABLE `pedido` (
  `id` int(10) UNSIGNED NOT NULL,
  `id_setor` int(10) UNSIGNED NOT NULL,
  `data_pedido` date NOT NULL,
  `ref_mes` int(2) UNSIGNED NOT NULL,
  `alteracao` tinyint(1) NOT NULL,
  `prioridade` int(1) UNSIGNED NOT NULL,
  `status` int(1) UNSIGNED NOT NULL,
  `valor` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estrutura da tabela `prioridade`
--

CREATE TABLE `prioridade` (
  `id` int(1) UNSIGNED NOT NULL,
  `nome` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Extraindo dados da tabela `prioridade`
--

INSERT INTO `prioridade` (`id`, `nome`) VALUES
(1, 'normal'),
(2, 'preferencial'),
(3, 'urgente'),
(4, 'emergencial'),
(5, 'rascunho');

-- --------------------------------------------------------

--
-- Estrutura da tabela `processos`
--

CREATE TABLE `processos` (
  `id` int(10) UNSIGNED NOT NULL,
  `num_processo` varchar(25) NOT NULL,
  `tipo` varchar(30) NOT NULL,
  `estante` varchar(30) NOT NULL,
  `prateleira` varchar(30) NOT NULL,
  `entrada` varchar(10) NOT NULL,
  `saida` varchar(10) NOT NULL,
  `responsavel` varchar(30) NOT NULL,
  `retorno` varchar(10) NOT NULL,
  `obs` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estrutura da tabela `saldos_adiantados`
--

CREATE TABLE `saldos_adiantados` (
  `id` int(10) UNSIGNED NOT NULL,
  `id_setor` int(10) UNSIGNED NOT NULL,
  `data_solicitacao` date NOT NULL,
  `data_analise` date NOT NULL,
  `mes_subtraido` int(2) UNSIGNED NOT NULL,
  `ano` int(4) UNSIGNED NOT NULL,
  `valor_adiantado` varchar(50) NOT NULL,
  `justificativa` text NOT NULL,
  `status` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estrutura da tabela `saldo_fixo`
--

CREATE TABLE `saldo_fixo` (
  `id` int(10) UNSIGNED NOT NULL,
  `id_setor` int(10) UNSIGNED NOT NULL,
  `saldo_padrao` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Extraindo dados da tabela `saldo_fixo`
--

INSERT INTO `saldo_fixo` (`id`, `id_setor`, `saldo_padrao`) VALUES
(1, 1, '0.000'),
(2, 2, '0.000'),
(3, 3, '2000000.000'),
(4, 4, '500000.000'),
(5, 5, '1500000.000'),
(6, 6, '200000.000'),
(7, 7, '250000.000'),
(8, 8, '100000.000'),
(9, 9, '300000.000'),
(10, 10, '310000.000'),
(11, 11, '2000000.000'),
(12, 12, '2000000.000'),
(13, 13, '2000000.000'),
(14, 14, '2000000.000'),
(15, 15, '2000000.000'),
(16, 16, '2000000.000');

-- --------------------------------------------------------

--
-- Estrutura da tabela `saldo_setor`
--

CREATE TABLE `saldo_setor` (
  `id` int(10) UNSIGNED NOT NULL,
  `id_setor` int(10) UNSIGNED NOT NULL,
  `saldo` varchar(50) NOT NULL,
  `saldo_suplementado` varchar(50) NOT NULL,
  `saldo_aditivado` varchar(50) NOT NULL,
  `mes` int(2) UNSIGNED NOT NULL,
  `ano` int(4) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estrutura da tabela `setores`
--

CREATE TABLE `setores` (
  `id` int(10) UNSIGNED NOT NULL,
  `nome` varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Extraindo dados da tabela `setores`
--

INSERT INTO `setores` (`id`, `nome`) VALUES
(1, 'PÃºblico'),
(2, 'Setor de OrÃ§amento e FinanÃ§as'),
(3, 'FarmÃ¡cia de Medicamentos'),
(4, 'FarmÃ¡cia de Materiais'),
(5, 'Almoxarifado Geral'),
(6, 'DivisÃ£o de LogÃ­stica'),
(7, 'UO Traumato'),
(8, 'UO Dispensas de LicitaÃ§Ã£o'),
(9, 'NutriÃ§Ã£o'),
(10, 'DivisÃ£o Administrativa Financeira'),
(11, 'NVE'),
(12, 'Unidade de Apoio'),
(13, 'Psiquiatria'),
(14, 'Radiologia'),
(15, 'SGPTI'),
(16, 'UO Protese Auditiva');

-- --------------------------------------------------------

--
-- Estrutura da tabela `solic_alt_pedido`
--

CREATE TABLE `solic_alt_pedido` (
  `id` int(10) UNSIGNED NOT NULL,
  `id_pedido` int(10) UNSIGNED NOT NULL,
  `id_setor` int(10) UNSIGNED NOT NULL,
  `data_solicitacao` date NOT NULL,
  `data_analise` date NOT NULL,
  `justificativa` text NOT NULL,
  `status` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estrutura da tabela `status`
--

CREATE TABLE `status` (
  `id` int(1) UNSIGNED NOT NULL,
  `nome` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Extraindo dados da tabela `status`
--

INSERT INTO `status` (`id`, `nome`) VALUES
(1, 'Rascunho'),
(2, 'Em Analise'),
(3, 'Reprovado'),
(4, 'Aprovado'),
(5, 'Aguarda Orcamento'),
(6, 'Empenhado'),
(7, 'Enviado ao Ordenador'),
(8, 'Recebido da Unidade de Aprovacao');

-- --------------------------------------------------------

--
-- Estrutura da tabela `usuario`
--

CREATE TABLE `usuario` (
  `id` int(10) UNSIGNED NOT NULL,
  `nome` varchar(30) NOT NULL,
  `login` varchar(15) NOT NULL,
  `senha` varchar(34) NOT NULL,
  `id_setor` int(10) UNSIGNED NOT NULL,
  `email` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Extraindo dados da tabela `usuario`
--

INSERT INTO `usuario` (`id`, `nome`, `login`, `senha`, `id_setor`, `email`) VALUES
(1, 'JoÃ£o Bolsson', 'joao', '$1$/ek2CsOE$O.unChFRZ4sHFVs3wRwTY/', 2, 'joaovictorbolsson@gmail.com'),
(2, 'FarmÃ¡cia de Medicamentos', 'farmacia', '$1$R.D6UD4W$hJ.SXctdcArzyMs/30HCI1', 3, 'teste_ola@gmail.com'),
(3, 'Farmacia de Materiais', 'farmateriais', '$1$UOfSgz1p$gdFpvb37zKezEPqYZYjNU1', 4, 'joaovictorbolsson@gmail.com'),
(4, 'Almoxarifado Geral', 'almoxarifado', '$1$fzKLXJL4$TJNKtI50IdScQxn5990cX0', 5, 'joaovictorbolsson@gmail.com'),
(5, 'Divisao de Logistica', 'divlog', '$1$2NB./YBc$P.7AV4DtacU1fYCtI9zsH1', 6, 'joaovictorbolsson@gmail.com'),
(6, 'UO Traumato', 'uotraumato', '$1$PQbgUgC1$cFeOLcqwL9n6DoPQkhVq2/', 7, 'joaovictorbolsson@gmail.com'),
(7, 'UO Dispensas de Licitacao', 'uodisp', '$1$beMT2G77$0mocKkwHwwXO95LqPWYp1/', 8, 'joaovictorbolsson@gmail.com'),
(8, 'Nutricao', 'nutricao', '$1$qTKe.0zX$E.Kbi3YBEVNz1G9KcHSqD1', 9, 'joaovictorbolsson@gmail.com'),
(9, 'Divisao Administrativa Finance', 'divadmin', '$1$OZtbi1V0$Lu2Qp1RUFtjRuA2u7mQEi0', 10, 'joaovictorbolsson@gmail.com'),
(10, 'Iara', 'iara', '$1$/TgtrTgd$FKlo.4KrTGnbH5G5xLNc80', 2, 'iara@ufsm.br'),
(11, 'Recepcao', 'recepcao', '$1$6RmFqcEN$5Elpbeu3wuEGs9.Cu4s6Q0', 2, 'joaovictorbolsson@gmail.com'),
(12, 'User NVE', 'nve', '$1$6RmFqcEN$5Elpbeu3wuEGs9.Cu4s6Q0', 11, 'joaovictorbolsson@gmail.com'),
(13, 'User Unidade de Apoio', 'apoio', '$1$6RmFqcEN$5Elpbeu3wuEGs9.Cu4s6Q0', 12, 'joaovictorbolsson@gmail.com'),
(14, 'User Psiquiatria', 'psiquiatria', '$1$6RmFqcEN$5Elpbeu3wuEGs9.Cu4s6Q0', 13, 'joaovictorbolsson@gmail.com'),
(15, 'User Radiologia', 'radio', '$1$6RmFqcEN$5Elpbeu3wuEGs9.Cu4s6Q0', 14, 'joaovictorbolsson@gmail.com'),
(16, 'User SGPTI', 'sgpti', '$1$6RmFqcEN$5Elpbeu3wuEGs9.Cu4s6Q0', 15, 'joaovictorbolsson@gmail.com'),
(17, 'User Protese Auditiva', 'protauditiva', '$1$6RmFqcEN$5Elpbeu3wuEGs9.Cu4s6Q0', 16, 'joaovictorbolsson@gmail.com');

-- --------------------------------------------------------

--
-- Estrutura da tabela `usuario_permissoes`
--

CREATE TABLE `usuario_permissoes` (
  `id` int(10) UNSIGNED NOT NULL,
  `id_usuario` int(10) UNSIGNED NOT NULL,
  `noticias` tinyint(1) NOT NULL,
  `saldos` tinyint(1) NOT NULL,
  `pedidos` tinyint(1) NOT NULL,
  `recepcao` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Extraindo dados da tabela `usuario_permissoes`
--

INSERT INTO `usuario_permissoes` (`id`, `id_usuario`, `noticias`, `saldos`, `pedidos`, `recepcao`) VALUES
(1, 1, 1, 1, 1, 0),
(2, 2, 0, 0, 0, 0),
(3, 3, 0, 0, 0, 0),
(4, 4, 0, 0, 0, 0),
(5, 5, 0, 0, 0, 0),
(6, 6, 0, 0, 0, 0),
(7, 7, 0, 0, 0, 0),
(8, 8, 0, 0, 0, 0),
(9, 9, 0, 0, 0, 0),
(10, 10, 1, 1, 1, 0),
(11, 11, 0, 0, 0, 1),
(12, 12, 0, 0, 0, 0),
(13, 13, 0, 0, 0, 0),
(14, 14, 0, 0, 0, 0),
(15, 15, 0, 0, 0, 0),
(16, 16, 0, 0, 0, 0),
(17, 17, 0, 0, 0, 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `comentarios`
--
ALTER TABLE `comentarios`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_pedido` (`id_pedido`),
  ADD KEY `status` (`status`),
  ADD KEY `prioridade` (`prioridade`);

--
-- Indexes for table `itens`
--
ALTER TABLE `itens`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `itens_pedido`
--
ALTER TABLE `itens_pedido`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_pedido` (`id_pedido`),
  ADD KEY `id_item` (`id_item`);

--
-- Indexes for table `pedido`
--
ALTER TABLE `pedido`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_setor` (`id_setor`),
  ADD KEY `ref_mes` (`ref_mes`),
  ADD KEY `status` (`status`),
  ADD KEY `prioridade` (`prioridade`);

--
-- Indexes for table `prioridade`
--
ALTER TABLE `prioridade`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `processos`
--
ALTER TABLE `processos`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `saldos_adiantados`
--
ALTER TABLE `saldos_adiantados`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_setor` (`id_setor`),
  ADD KEY `mes_subtraido` (`mes_subtraido`);

--
-- Indexes for table `saldo_fixo`
--
ALTER TABLE `saldo_fixo`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_setor` (`id_setor`);

--
-- Indexes for table `saldo_setor`
--
ALTER TABLE `saldo_setor`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_setor` (`id_setor`),
  ADD KEY `mes` (`mes`);

--
-- Indexes for table `setores`
--
ALTER TABLE `setores`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `solic_alt_pedido`
--
ALTER TABLE `solic_alt_pedido`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_pedido` (`id_pedido`),
  ADD KEY `id_setor` (`id_setor`);

--
-- Indexes for table `status`
--
ALTER TABLE `status`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_setor` (`id_setor`);

--
-- Indexes for table `usuario_permissoes`
--
ALTER TABLE `usuario_permissoes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `comentarios`
--
ALTER TABLE `comentarios`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `itens`
--
ALTER TABLE `itens`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `itens_pedido`
--
ALTER TABLE `itens_pedido`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `pedido`
--
ALTER TABLE `pedido`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `prioridade`
--
ALTER TABLE `prioridade`
  MODIFY `id` int(1) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT for table `processos`
--
ALTER TABLE `processos`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `saldos_adiantados`
--
ALTER TABLE `saldos_adiantados`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `saldo_fixo`
--
ALTER TABLE `saldo_fixo`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;
--
-- AUTO_INCREMENT for table `saldo_setor`
--
ALTER TABLE `saldo_setor`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `setores`
--
ALTER TABLE `setores`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;
--
-- AUTO_INCREMENT for table `solic_alt_pedido`
--
ALTER TABLE `solic_alt_pedido`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `status`
--
ALTER TABLE `status`
  MODIFY `id` int(1) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
--
-- AUTO_INCREMENT for table `usuario`
--
ALTER TABLE `usuario`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;
--
-- AUTO_INCREMENT for table `usuario_permissoes`
--
ALTER TABLE `usuario_permissoes`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;
--
-- Constraints for dumped tables
--

--
-- Limitadores para a tabela `comentarios`
--
ALTER TABLE `comentarios`
  ADD CONSTRAINT `comentarios_ibfk_1` FOREIGN KEY (`id_pedido`) REFERENCES `pedido` (`id`),
  ADD CONSTRAINT `comentarios_ibfk_2` FOREIGN KEY (`status`) REFERENCES `status` (`id`),
  ADD CONSTRAINT `comentarios_ibfk_3` FOREIGN KEY (`prioridade`) REFERENCES `prioridade` (`id`);

--
-- Limitadores para a tabela `itens_pedido`
--
ALTER TABLE `itens_pedido`
  ADD CONSTRAINT `itens_pedido_ibfk_1` FOREIGN KEY (`id_pedido`) REFERENCES `pedido` (`id`),
  ADD CONSTRAINT `itens_pedido_ibfk_2` FOREIGN KEY (`id_item`) REFERENCES `itens` (`id`);

--
-- Limitadores para a tabela `pedido`
--
ALTER TABLE `pedido`
  ADD CONSTRAINT `pedido_ibfk_1` FOREIGN KEY (`id_setor`) REFERENCES `setores` (`id`),
  ADD CONSTRAINT `pedido_ibfk_2` FOREIGN KEY (`ref_mes`) REFERENCES `mes` (`id`),
  ADD CONSTRAINT `pedido_ibfk_3` FOREIGN KEY (`status`) REFERENCES `status` (`id`),
  ADD CONSTRAINT `pedido_ibfk_4` FOREIGN KEY (`prioridade`) REFERENCES `prioridade` (`id`);

--
-- Limitadores para a tabela `saldos_adiantados`
--
ALTER TABLE `saldos_adiantados`
  ADD CONSTRAINT `saldos_adiantados_ibfk_1` FOREIGN KEY (`id_setor`) REFERENCES `setores` (`id`),
  ADD CONSTRAINT `saldos_adiantados_ibfk_2` FOREIGN KEY (`mes_subtraido`) REFERENCES `mes` (`id`);

--
-- Limitadores para a tabela `saldo_fixo`
--
ALTER TABLE `saldo_fixo`
  ADD CONSTRAINT `saldo_fixo_ibfk_1` FOREIGN KEY (`id_setor`) REFERENCES `setores` (`id`);

--
-- Limitadores para a tabela `saldo_setor`
--
ALTER TABLE `saldo_setor`
  ADD CONSTRAINT `saldo_setor_ibfk_1` FOREIGN KEY (`id_setor`) REFERENCES `setores` (`id`),
  ADD CONSTRAINT `saldo_setor_ibfk_2` FOREIGN KEY (`mes`) REFERENCES `mes` (`id`);

--
-- Limitadores para a tabela `solic_alt_pedido`
--
ALTER TABLE `solic_alt_pedido`
  ADD CONSTRAINT `solic_alt_pedido_ibfk_1` FOREIGN KEY (`id_pedido`) REFERENCES `pedido` (`id`),
  ADD CONSTRAINT `solic_alt_pedido_ibfk_2` FOREIGN KEY (`id_setor`) REFERENCES `setores` (`id`);

--
-- Limitadores para a tabela `usuario`
--
ALTER TABLE `usuario`
  ADD CONSTRAINT `usuario_ibfk_1` FOREIGN KEY (`id_setor`) REFERENCES `setores` (`id`);

--
-- Limitadores para a tabela `usuario_permissoes`
--
ALTER TABLE `usuario_permissoes`
  ADD CONSTRAINT `usuario_permissoes_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
