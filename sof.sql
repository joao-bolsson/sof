-- phpMyAdmin SQL Dump
-- version 4.6.4deb1+deb.cihar.com~trusty.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: 04-Set-2016 às 22:03
-- Versão do servidor: 5.5.50-0ubuntu0.14.04.1
-- PHP Version: 7.0.10-2+deb.sury.org~trusty+1

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
-- Estrutura da tabela `mes`
--

CREATE TABLE `mes` (
  `id` int(2) UNSIGNED NOT NULL,
  `sigla_mes` varchar(3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Extraindo dados da tabela `mes`
--

INSERT INTO `mes` (`id`, `sigla_mes`) VALUES
(1, 'Jan'),
(2, 'Fev'),
(3, 'Mar'),
(4, 'Abr'),
(5, 'Mai'),
(6, 'Jun'),
(7, 'Jul'),
(8, 'Ago'),
(9, 'Set'),
(10, 'Out'),
(11, 'Nov'),
(12, 'Dez'),
(13, '---');

-- --------------------------------------------------------

--
-- Estrutura da tabela `paginas_post`
--

CREATE TABLE `paginas_post` (
  `id` int(10) UNSIGNED NOT NULL,
  `tabela` varchar(30) NOT NULL,
  `nome` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Extraindo dados da tabela `paginas_post`
--

INSERT INTO `paginas_post` (`id`, `tabela`, `nome`) VALUES
(1, 'boaspraticas', 'Boas PrÃ¡ticas'),
(2, 'comemoracoes', 'ComemoraÃ§Ãµes'),
(3, 'dinamicas', 'DinÃ¢micas'),
(4, 'noticia', 'NotÃ­cias'),
(5, 'publico_interno', 'PÃºblico Interno'),
(6, 'tutoriais', 'POPs e Tutoriais');

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
  `valor` varchar(50) NOT NULL,
  `obs` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Extraindo dados da tabela `pedido`
--

INSERT INTO `pedido` (`id`, `id_setor`, `data_pedido`, `ref_mes`, `alteracao`, `prioridade`, `status`, `valor`, `obs`) VALUES
(1, 3, '2016-08-28', 8, 0, 1, 7, '2550.000', 'Teste teste teste teste teste teste teste teste teste teste teste teste teste teste teste teste teste teste teste teste teste teste teste teste teste teste teste teste teste teste teste teste teste teste teste teste teste teste teste teste teste teste teste teste teste teste teste teste teste teste teste teste teste teste teste teste teste teste teste teste teste teste teste teste teste teste teste teste teste teste teste teste teste teste teste teste teste teste teste teste teste teste teste teste teste teste teste teste teste '),
(2, 3, '2016-08-30', 8, 0, 1, 6, '1737.420', 'Sem observaÃ§Ãµes.'),
(3, 3, '2016-09-04', 9, 0, 1, 2, '11.100', 'Testando');

-- --------------------------------------------------------

--
-- Estrutura da tabela `pedido_empenho`
--

CREATE TABLE `pedido_empenho` (
  `id` int(10) UNSIGNED NOT NULL,
  `id_pedido` int(10) UNSIGNED NOT NULL,
  `empenho` varchar(30) NOT NULL,
  `data` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estrutura da tabela `postagens`
--

CREATE TABLE `postagens` (
  `id` int(10) UNSIGNED NOT NULL,
  `tabela` int(10) UNSIGNED NOT NULL,
  `titulo` varchar(100) NOT NULL,
  `data` date NOT NULL,
  `ativa` tinyint(1) NOT NULL,
  `postagem` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Extraindo dados da tabela `postagens`
--

INSERT INTO `postagens` (`id`, `tabela`, `titulo`, `data`, `ativa`, `postagem`) VALUES
(1, 1, 'Kit dos Gestores/Fiscais de Contrato', '2015-09-21', 1, '<h3>Kit dos Gestores/Fiscais de Contrato</h3><hr><p style=""><img class="fr-dib fr-draggable" src="../uploads/boaspraticas1.jpg" data-name="boaspraticas1.jpg" style="width: 539px;"></p><p style="">No m&ecirc;s de setembro de 2015, tivemos a satisfa&ccedil;&atilde;o de compartilhar, por solicita&ccedil;&atilde;o do Analista Administrativo Leonardo Ferreira Viana, do Hospital Universit&aacute;rio Cassiano Ant&ocirc;nio Moraes &ndash; HUCAM, de Vit&oacute;ria &ndash; ES, o Kit dos Gestores/Fiscais de Contratos elaborado pelo SOF.</p><p style=""><br></p>'),
(2, 1, 'Visita do Contador Bruno Martini &ndash; HEUFPEL', '2015-10-22', 1, '<h3>Visita do Contador Bruno Martini &ndash; HEUFPEL</h3><hr><p style=""><img class="fr-dib fr-draggable" src="../uploads/82b711138325c1c967dd26f2467969e2eef584f6.jpg" style="width: 443px;"></p><p style="">Entre os dias 19 e 21 de outubro de 2015, recebemos a visita do Contador Bruno Martini, do Hospital Escola da Universidade Federal de Pelotas, a fim de mapear os processos do SOF com o objetivo de subsidiar o in&iacute;cio das atividades do HEUFPEL enquanto filial da Empresa Brasileira de Servi&ccedil;os Hospitalares &ndash; EBSERH.</p><p style=""><br></p>'),
(3, 1, 'Testandosasas', '2016-03-29', 0, '<h3>Testandosasas</h3><hr><p>asashaus</p>'),
(4, 1, 'AKAKAKA', '2016-04-07', 0, '<h3>AKAKAKA</h3><hr><p>SASKAJSKAJJSKA</p>'),
(5, 1, 'gyygygygygy', '2016-04-08', 0, '<h3>gyygygygygy</h3><hr><p>kkkk</p>'),
(6, 1, 'Publicado o I Caderno de Exec. Or&ccedil;ament. do HUSM', '2015-10-02', 1, '<h3 style="">Publicado o I Caderno de Exec. Or&ccedil;ament. do HUSM</h3><p style=""><img class="fr-dib fr-draggable" src="../uploads/9293f3d2ce59193a6c719b982c87325e98f3c5d0.png" style="width: 539px;"></p><p style=""><br></p><p style="">Em 01 de outubro de 2015, &eacute; enviado &agrave; gr&aacute;fica o primeiro Caderno de Execu&ccedil;&atilde;o Or&ccedil;ament&aacute;ria do Hospital Universit&aacute;rio cuja edi&ccedil;&atilde;o ser&aacute; quadrimestral.</p><p style=""><br></p>'),
(7, 2, 'Confraterniza&ccedil;&atilde;o', '2015-10-16', 1, '<h3 style="">Confraterniza&ccedil;&atilde;o</h3><hr><p style=""><img class="fr-dib fr-draggable" src="../uploads/Almoco-no-Etnias.jpg" data-name="Almoco-no-Etnias.jpg" style="width: 539px;"></p><p style="">Em outubro de 2015, almo&ccedil;o de confraterniza&ccedil;&atilde;o entre colegas e amigos no restaurante Etnias. Um presente carinhoso do colega Paulo Francisco.</p><p style=""><br></p>'),
(8, 2, 'Impress&atilde;o do 1&ordm; Caderno de Execu&ccedil;&atilde;o Or&ccedil;ament&aacute;ria', '2015-10-01', 0, '<h3>Impress&atilde;o do 1&ordm; Caderno de Execu&ccedil;&atilde;o Or&ccedil;ament&aacute;ria</h3><p><br></p><p>Em 01/10/2015, para alegria do SOF, ap&oacute;s nove meses do in&iacute;cio das atividades do setor, o I Caderno de Execu&ccedil;&atilde;o Or&ccedil;ament&aacute;ria do HUSM est&aacute; pronto para ser encaminhado &agrave; gr&aacute;fica.</p><p><img class="fr-dib fr-draggable" src="http://localhost:8080/sof/uploads/caderno01.png" style="width: 426px;"></p>'),
(9, 2, 'Despedida do Antigo Setor de Contabilidade e Or&ccedil;amento do HUSM', '2015-12-07', 1, '<h3>Despedida do Antigo Setor de Contabilidade e Or&ccedil;amento do HUSM</h3><hr><p style="">Ao final de Dezembro de 2014, houve a confraterniza&ccedil;&atilde;o que marcou a despedida do Setor de Contabilidade e Or&ccedil;amento do HUSM. <br><br>A partir desse momento, nascem o Setor de Or&ccedil;amento e Finan&ccedil;as e o Setor de Contabilidade Fiscal e Controladoria do HUSM/EBSERH.</p><p style=""><img class="fr-dib fr-draggable" src="../uploads/come01.png" style="width: 474px;"></p>'),
(10, 2, 'klakslask', '2016-04-07', 0, '<h3>klakslask</h3><hr><p>skalsklakskasa</p>'),
(11, 2, 'klakslask', '2016-04-07', 0, '<h3>klakslask</h3><hr><p>skalsklakskasa</p>'),
(12, 2, 'Comemora&ccedil;&atilde;o Teste', '2016-04-07', 0, '<h3>Comemora&ccedil;&atilde;o Teste</h3><hr><p>olaaaa tudo bein?</p>'),
(13, 2, '', '2016-07-23', 1, '<p style=""><img class="fr-dib fr-draggable" src="../uploads/9d343d4678341bc87b12324a528a1247e47b17fe.png" data-name="9d343d4678341bc87b12324a528a1247e47b17fe.png" style="width: 300px;">testando em 19 de abril</p><p style="">visualizando o teste em 23 de julho</p>'),
(14, 2, 'testando em 19 de abril', '2016-04-19', 0, '<h3><img class="fr-dib fr-draggable" src="../uploads/9d343d4678341bc87b12324a528a1247e47b17fe.png" data-name="9d343d4678341bc87b12324a528a1247e47b17fe.png" style="width: 300px;">testando em 19 de abril</h3>'),
(15, 3, 'Din&acirc;mica da Formiga', '2015-08-21', 1, '<h3 style="">Din&acirc;mica da Formiga</h3><p style=""><br></p><p style=""><img class="fr-dib fr-draggable" src="../uploads/din06-min.png" data-name="din06-min.png" style="width: 539px;"></p><p style="">Em um primeiro momento, o grupo foi reunido em c&iacute;rculo e cada um dos membros foi orientado a passar uma formiguinha imagin&aacute;ria para o colega ao lado.<br><br>Ap&oacute;s a formiguinha ser entregue ao &uacute;ltimo participante do c&iacute;rculo, o grupo recebe a orienta&ccedil;&atilde;o de dar um beijo na posi&ccedil;&atilde;o exata onde colocou a formiguinha no companheiro.<br><br>De uma forma descontra&iacute;da, foi criada a oportunidade para cada um colocar-se no lugar do outro diante da situa&ccedil;&atilde;o de receber a formiga, uma vez que todos tiveram de dar um beijinho no local onde colocaram a formiguinha no colega.<br><br>O objetivo dessa din&acirc;mica foi oportunizar a reflex&atilde;o acerca da empatia, um dos valores do Setor.</p>'),
(16, 3, 'Din&acirc;mica Farm&aacute;cia', '2016-04-07', 0, '<h3>Din&acirc;mica Farm&aacute;cia</h3><hr><p>EAI? BELEZA?</p>'),
(17, 4, 'Parcelas Referentes aos Sub-Repasses da Receita SUS', '2015-11-20', 1, '<h3>Parcelas Referentes aos Sub-Repasses da Receita SUS</h3><hr><p style=""><img class="fr-dib fr-draggable" src="../uploads/83127f58e0a63273e69a17940b064aceea939c86.png" style="width: 539px;"></p><p style="">Memorando Circular, enviado pela EBSERH, informando o parcelamento dos recursos financeiros da produ&ccedil;&atilde;o SUS.</p><p style="">Clique <a class="fr-strong" href="../uploads/Sub-repasse-da-receita-SUS.pdf" target="_blank">aqui</a> para visualizar o documento.</p>'),
(18, 4, 'Prazo de Empenho para as despesas de Fonte de produ&ccedil;&atilde;o SUS', '2015-11-20', 1, '<h3 style="">Prazo de Empenho para as despesas de Fonte de produ&ccedil;&atilde;o SUS</h3><hr><p style=""><img class="fr-dib fr-draggable" src="../uploads/e46ff3a74b4652bdf729c87e12419d151af32233.png" style="width: 539px;"></p><p style="">Memorando Circular, enviado pela EBSERH, informando os prazos limite para execu&ccedil;&atilde;o dos cr&eacute;ditos recebidos por destaque e para as dota&ccedil;&otilde;es do pr&oacute;prio &oacute;rg&atilde;o.</p><p style=""><br></p><p style=""><a class="fr-strong" href="../uploads/Prazo-de-empenho-para-as-despesas-de-fonte-de-producao-SUS.pdf" target="_blank">Memorando 383/SOF</a></p>'),
(19, 4, 'Relat&oacute;rios de Empenhos Abertos Referentes aos dois Primeiros Quadrimestres de 2015', '2015-10-22', 1, '<h3 style="">Relat&oacute;rios de Empenhos Abertos Referentes aos dois Primeiros Quadrimestres de 2015</h3><hr><p style=""><img class="fr-dib fr-draggable" src="../uploads/1801165a2e1ca373e87bcba418fdbca554e18e77.png" style="width: 539px;"></p><p style="">Memorando emitido pelo SOF solicitando o estorno dos saldos de empenhos cujos insumos n&atilde;o foram entregues nos dois primeiros quadrimestres de 2015.</p><p style=""><a class="fr-strong" href="../uploads/Relatorios-de-empenhos-abertos-referentes-aos-dois-primeiros-quadrimestres-de-2015.pdf" target="_blank">Memorando 354/SOF</a></p>'),
(20, 4, 'Anula&ccedil;&atilde;o de Empenhos', '2015-11-24', 1, '<h3>Anula&ccedil;&atilde;o de Empenhos</h3><hr><p style=""><img class="fr-dib fr-draggable" src="../uploads/7d134308cad8ab2c1b24de3c97b72a3fda60a52b.png" style="width: 539px;"></p><p style="">Memorando emitido pelo SOF comunicando anula&ccedil;&atilde;o de empenhos com pend&ecirc;ncias de entrega.</p><p style=""><a class="fr-strong" href="../uploads/Anulacao-de-empenhos.pdf" target="_blank">Memorando Circular 384/SOF</a></p>'),
(21, 4, 'Comunicado aos Fornecedores', '2015-12-03', 1, '<h3 style="">Comunicado aos Fornecedores</h3><p style="">Of&iacute;cio aos Fornecedores sobre a Suspens&atilde;o no Fornecimento de Insumos/Servi&ccedil;os</p><p style=""><a href="" target="_blank"><img class="fr-dib fr-draggable" src="../uploads/2df7d20584ffa380cc5e0c211008832b8b1bab4e.png" style="width: 539px;"></a></p><p style=""><a class="fr-strong" href="../uploads/Para-os-Fornecedores.pdf" target="_blank">Of&iacute;cio/Circular n&ordm; 009/2015-GA/HUSM</a></p>'),
(22, 4, 'P&aacute;scoa Solid&aacute;ria do SOF', '2016-03-28', 1, '<h3 style=""><strong>P&aacute;scoa Solid&aacute;ria do SOF</strong></h3><p style=""><img class="fr-dib fr-draggable" src="../uploads/8f1ceca976c6a2f5d24ad317503eae77954dee56.jpg" style="width: 506px;"></p><p>Representantes do Setor de Or&ccedil;amento e Finan&ccedil;as realizam a entrega de caixas de leite e chocolates para as crian&ccedil;as da Escolinha Esta&ccedil;&atilde;o dos Ventos na quinta-feira que antecede &agrave; P&aacute;scoa.</p><p style="">Para saber como colaborar, clique <a href="http://www.estacaodosventos.com.br/" target="_blank">aqui</a>!</p><p style=""><br></p><p style=""><br></p>'),
(23, 4, 'Natal Solid&aacute;rio do SOF', '2016-03-25', 0, '<h3>Natal Solid&aacute;rio do SOF</h3><hr><p>Equipe do Setor de Or&ccedil;amento e Finan&ccedil;as doa ventiladores para as crian&ccedil;as do Centro de Desenvolvimento Comunit&aacute;rio Esta&ccedil;&atilde;o dos Ventos.&nbsp;</p><p>Para saber como colaborar, clique <a class="fr-strong" href="http://www.estacaodosventos.com.br/" target="_blank"><span style="color: rgb(97, 189, 109); font-weight: normal;">aqui</span></a>.</p><p style=""><img class="fr-dib fr-draggable" src="../uploads/2c5e8e9901ed71d4146c5c8d81784f9573d5e0d2.png" style="width: 300px;"></p>'),
(24, 4, 'Not&iacute;cia Editada', '2016-03-25', 0, '<h3>Not&iacute;cia Editada</h3><hr><p style="">Editando not&iacute;cia...</p>'),
(25, 4, 'Natal Solid&aacute;rio do SOF', '2015-12-26', 1, '<h3 style="">Natal Solid&aacute;rio do SOF</h3><hr><p style=""><img data-name="2c5e8e9901ed71d4146c5c8d81784f9573d5e0d2.png" class="fr-dib fr-draggable" src="../uploads/2c5e8e9901ed71d4146c5c8d81784f9573d5e0d2.png" style="width: 300px;"></p><p style="">Em dezembro de 2015, a equipe do Setor de Or&ccedil;amento e Finan&ccedil;as doa ventiladores para as crian&ccedil;as do Centro de Desenvolvimento Comunit&aacute;rio Esta&ccedil;&atilde;o dos Ventos.&nbsp;</p><p style="">Para saber como voc&ecirc; pode colaborar, clique <a href="http://www.estacaodosventos.com.br/" target="_blank">aqui</a>!</p><p style=""><br></p>'),
(26, 4, 'Editando', '2016-03-31', 0, '<h3>Editando</h3><hr><p style="">alalalala</p>'),
(27, 4, 'Editando essa not&iacute;ciaA', '2016-03-31', 0, '<h3 style="">Editando essa not&iacute;ciaA</h3><hr><p style="">sashaushuashuaus</p>'),
(28, 4, 'oiaaaal', '2016-03-31', 0, '<h3 style="">oiaaaal</h3><hr><p style="">askajska</p>'),
(29, 4, 'Editara', '2016-03-31', 0, '<h3 style="">Editara</h3><hr><p style="">sahsuahushuahsu</p>'),
(30, 4, 'Data Retroativa', '2016-03-06', 0, '<h3 style="">Data Retroativa</h3><hr><p style="">estou voltando no passado shuahsuhua</p>'),
(31, 4, 'aasasasas', '2016-03-07', 0, '<h3 style="">aasasasas</h3><hr>'),
(32, 4, 'Teste Visualiza&ccedil;&atilde;o', '2016-04-08', 0, '<h3 style="">Teste Visualiza&ccedil;&atilde;o</h3><hr><p style="">Esta &eacute; uma publica&ccedil;&atilde;o que deve ser visualizada somente por membros do Setor de Or&ccedil;amento e Finan&ccedil;as do HUSM.</p><p style="">Se voc&ecirc; est&aacute; lendo esta publica&ccedil;&atilde;o e n&atilde;o possui um usu&aacute;rio e senha do Setor, das duas uma:&nbsp;</p><ul><li style="">voc&ecirc; n&atilde;o &eacute; do sof</li><li style="">voc&ecirc; &eacute; do sof e n&atilde;o tem um login, pe&ccedil;a para o administrador do sistema</li></ul><p style=""><br></p>'),
(33, 4, 'Ebserh adquire mais de 1,3 mil computadores para hospitais universit&aacute;rios', '2016-03-26', 0, '<h3 style="">Ebserh adquire mais de 1,3 mil computadores para hospitais universit&aacute;rios</h3><hr><p style="">Investimento &eacute; de R$ 4,68 milh&otilde;es e a entrega ser&aacute; em aproximadamente 30 dias</p><p style="text-align: justify;"><img class="fr-draggable fr-dii fr-fil" src="http://andes-ufsc.org.br/wp-content/uploads/2015/10/fotoresize.jpg" style="width: 279px;"><em><strong>Bras&iacute;lia (DF)</strong></em> &ndash; Com o intuito de renovar o parque tecnol&oacute;gico dos hospitais universit&aacute;rios federais, a Empresa Brasileira de Servi&ccedil;os Hospitalares (Ebserh) adquiriu 1.348 microcomputadores para 32 unidades. O investimento &eacute; de R$ 4,68 milh&otilde;es com recursos do Banco Nacional de Desenvolvimento Econ&ocirc;mico e Social (BNDES).</p><p style="text-align: justify;">&ldquo;A parceria junto ao BNDES &eacute; de suma import&acirc;ncia e visa prover a infraestrutura adequada de TI nos hospitais universit&aacute;rios para a utiliza&ccedil;&atilde;o do sistema Aplicativo de Gest&atilde;o para Hospitais Universit&aacute;rios Federais (AGHU), melhorando assim o funcionamento do hospital e o atendimento &agrave; popula&ccedil;&atilde;o por meio do SUS&rdquo;, ressaltou o presidente da Ebserh, professor Newton Lima.</p><p style="text-align: justify;"><br></p><p style="text-align: justify;">Os computadores j&aacute; v&ecirc;m com sistema operacional instalado e a entrega se dar&aacute; em aproximadamente 30 dias. Essa aquisi&ccedil;&atilde;o comp&otilde;e um conjunto de a&ccedil;&otilde;es que vem sendo desenvolvidas pela estatal por meio da Diretoria de Gest&atilde;o de Processos e Tecnologia da Informa&ccedil;&atilde;o (DGPTI). Desde que assumiu a gest&atilde;o de 37 hospitais universit&aacute;rios, a Ebserh j&aacute; entregou 9.468 computadores.</p><p style="text-align: justify;">De acordo com o Diretor De Gest&atilde;o De Processos e Tecnologia da Informa&ccedil;&atilde;o (DGPTI), Cristiano Cabral, os hospitais alocar&atilde;o os equipamentos dependendo de suas necessidades. &ldquo;Os computadores ser&atilde;o entregues, contribuindo para a moderniza&ccedil;&atilde;o do parque tecnol&oacute;gico dos hospitais universit&aacute;rios federais e ser&atilde;o essenciais para ampliar o atendimento, monitoramento e controle atrav&eacute;s do uso dos sistemas AGHU e SIG da Rede Ebserh&rdquo;, esclareceu.</p><p style="text-align: justify;"><em><strong><u>Outras aquisi&ccedil;&otilde;es</u></strong></em></p><p>Desde o in&iacute;cio de suas atividades em 2012, a Ebserh adquiriu outros produtos na &aacute;rea de Tecnologia da Informa&ccedil;&atilde;o como 1.066 ativos de rede e componentes, 196 servidores, 11 racks e 14 unidades de armazenamento, totalizando investimentos na ordem de R$ 45,9 milh&otilde;es.</p><p>Al&eacute;m disso, foram adquiridos 23 cont&ecirc;ineres datacenters (CDC), tendo sido entregues 15 unidades. 12 CDCs j&aacute; est&atilde;o em pleno funcionamento, atendendo 12 hospitais e duas maternidades. Para estes j&aacute; em opera&ccedil;&atilde;o, incluindo a parte da elabora&ccedil;&atilde;o do projeto, foram investidos mais de R$ 26,4 milh&otilde;es. Este ano, h&aacute; previs&atilde;o de se inaugurar mais nove CDCs e um datacenter indoor (sala cofre), que atender&aacute; a mais 12 unidades hospitalares da rede Ebserh.</p><p>O datacenter &eacute; uma solu&ccedil;&atilde;o modular de r&aacute;pida implanta&ccedil;&atilde;o, longa durabilidade e tolerante a desastres, equipado com nobreaks modulares, gerador e sistema de g&aacute;s para combate a inc&ecirc;ndio. O pr&oacute;ximo cont&ecirc;iner ser&aacute; inaugurado no Hospital Universit&aacute;rio Maria Aparecida Pedrossian da Universidade Federal do Mato Grosso do Sul (Humap-UFMS), em Campo Grande, no dia 10 de mar&ccedil;o.</p><p style=""><em><u><strong>Coordenadoria de Comunica&ccedil;&atilde;o Social da Ebserh</strong></u></em></p>'),
(34, 4, 'asasasasas', '2016-04-07', 0, '<h3>asasasasas</h3><hr><p>sasasas</p>'),
(35, 4, 'shuasuhasuhsauh', '2016-04-08', 0, '<h3 style="">shuasuhasuhsauh</h3><hr><p style="">aalsalsa</p>'),
(36, 4, 'Not&iacute;cia para teste', '2016-04-09', 0, '<h3>Not&iacute;cia para teste</h3><hr><p>uollll</p>'),
(37, 4, '', '2016-04-19', 0, '<p>testando em 19 de abril</p>'),
(38, 4, '', '2016-04-19', 0, '<p>testando em 19 de abril</p>'),
(39, 4, 'Nova Not&iacute;cia', '2016-04-19', 0, '<h3 style="">Nova Not&iacute;cia</h3><hr><p style="">Este &eacute; um exemplo de nova not&iacute;cia.</p><p style="">Se nenhum erro ocorrer, ela ser&aacute; exclu&iacute;da.</p>'),
(40, 4, 'Cadastro Atualizado no SICAF', '2016-05-06', 1, '<h3 style=""><strong>Cadastro Atualizado no SICAF</strong></h3><p style=""><strong><img class="fr-dib fr-draggable" src="../uploads/6fd28f42ca45a53a37bc26a6d6fbf74a1d77d3e3.png" style="width: 539px;"></strong><br></p>'),
(41, 4, 'SOF no Projeto Catalunha', '2015-06-01', 1, '<h3><strong>SOF no Projeto Catalunha</strong></h3><p><img class="fr-dib fr-draggable" src="../uploads/3573a3ec1b1efce0be34689527b83b007f584d6d.png" style="width: 539px;"></p><p>Entre 25 e 29 de maio, em Natal - RN, o SOF participou &nbsp;do &ldquo;<em>Compartilhamento de experi&ecirc;ncias e desenvolvimento de mecanismos e instrumentos para o aprimoramento da gest&atilde;o econ&ocirc;mico-financeira dos Hospitais Universit&aacute;rios Federais (HUF)&rdquo;,&nbsp;</em>uma parceria entre a EBSERH e o Cons&oacute;rcio Hospitalar da Catalunha/Espanha.</p><p>Tamb&eacute;m participaram do encontro os hospitais HUOL-UFRN e o HU-UFMA.&nbsp;</p><p><br></p>'),
(42, 4, 'Curso de Capacita&ccedil;&atilde;o para a Equipe do SOF', '2016-05-10', 1, '<h3 style=""><strong>Curso de Capacita&ccedil;&atilde;o para a Equipe do SOF</strong></h3><p style=""><img class="fr-dib fr-draggable" src="../uploads/8aeb2fb67030e254d396b010dc8492e29183f788.jpg" style="width: 539px;"></p><p style="">No dia 10 de maio ocorreu o I Curso de Capacita&ccedil;&atilde;o para a Equipe do SOF ministrado pela Contadora Greice Eccel Pontelli que atua como T&eacute;cnica em Contabilidade no Setor de Avalia&ccedil;&atilde;o e Controladoria do HUSM.&nbsp;</p><p style="">O treinamento teve como objetivo capacitar a equipe do Setor de Or&ccedil;amento e Finan&ccedil;as no que diz respeito &agrave;s Funcionalidades do Portal de Compras Governamentais.</p><p style=""><img class="fr-dib fr-draggable" src="../uploads/d7ddad8e65a37db4cbcefbb3ae85a16ec0025742.jpg" style="width: 539px;"></p><p style=""><br></p><p style=""><br></p>'),
(43, 6, 'Emiss&atilde;o de GRU', '2015-10-22', 1, '<h3 style="">Emiss&atilde;o de GRU</h3><hr><p style="">Ap&oacute;s o pagamento, enviar c&oacute;pia do comprovante de pagamento e da GRU para o <a href="http://localhost:8080/sof/view/faleconosco.php" target="_blank">e-mail do SOF</a>.</p><p style=""><br></p><p style=""><span class="fr-video fr-dvb fr-draggable" contenteditable="false" draggable="true"><iframe src="//www.youtube.com/embed/Hx4bw0CWnFQ" allowfullscreen="" frameborder="0" height="360" width="640"></iframe></span>\r\n<br></p>'),
(44, 3, 'Nova Not&iacute;cia', '2016-07-23', 0, '<h3 style="">Nova Not&iacute;cia</h3><hr><p style="">Dessa vez, muito mais segura!</p><p style="">Essa not&iacute;cia ser&aacute; usada para testes.</p><p style=""><br></p><p style="">Ela foi editada aqui.</p><p style=""><br></p><p style="">Mudando de p&aacute;gina.</p><p style=""><br></p><p style="">Excluindo.</p>');

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
  `tipo` int(1) UNSIGNED NOT NULL,
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
-- Estrutura da tabela `processos_tipo`
--

CREATE TABLE `processos_tipo` (
  `id` int(1) UNSIGNED NOT NULL,
  `nome` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Extraindo dados da tabela `processos_tipo`
--

INSERT INTO `processos_tipo` (`id`, `nome`) VALUES
(1, 'Adesao'),
(2, 'Bolsa de Estudante'),
(3, 'Comodato'),
(4, 'Compra Compartilhada'),
(5, 'Dispensa de Licitacao'),
(6, 'Equipamento Permanente'),
(7, 'Inexigibilidade'),
(8, 'Multas e Juros'),
(9, 'Nao se aplica'),
(10, 'Reconhecimento de Divida'),
(11, 'Registro de Precos'),
(12, 'Taxa de Inscricao');

-- --------------------------------------------------------

--
-- Estrutura da tabela `saldos_adiantados`
--

CREATE TABLE `saldos_adiantados` (
  `id` int(10) UNSIGNED NOT NULL,
  `id_setor` int(10) UNSIGNED NOT NULL,
  `data_solicitacao` date NOT NULL,
  `data_analise` date NOT NULL,
  `valor_adiantado` varchar(50) NOT NULL,
  `justificativa` text NOT NULL,
  `status` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estrutura da tabela `saldos_lancamentos`
--

CREATE TABLE `saldos_lancamentos` (
  `id` int(10) UNSIGNED NOT NULL,
  `id_setor` int(10) UNSIGNED NOT NULL,
  `data` date NOT NULL,
  `valor` varchar(50) NOT NULL,
  `categoria` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estrutura da tabela `saldos_transferidos`
--

CREATE TABLE `saldos_transferidos` (
  `id` int(10) UNSIGNED NOT NULL,
  `id_setor_ori` int(10) UNSIGNED NOT NULL,
  `id_setor_dest` int(10) UNSIGNED NOT NULL,
  `valor` varchar(50) NOT NULL,
  `justificativa` text
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estrutura da tabela `saldo_categoria`
--

CREATE TABLE `saldo_categoria` (
  `id` int(1) NOT NULL,
  `nome` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Extraindo dados da tabela `saldo_categoria`
--

INSERT INTO `saldo_categoria` (`id`, `nome`) VALUES
(1, 'normal'),
(2, 'adiantamento'),
(3, 'transferencia'),
(4, 'pedido');

-- --------------------------------------------------------

--
-- Estrutura da tabela `saldo_setor`
--

CREATE TABLE `saldo_setor` (
  `id` int(10) UNSIGNED NOT NULL,
  `id_setor` int(10) UNSIGNED NOT NULL,
  `saldo` varchar(50) NOT NULL
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
-- Indexes for table `mes`
--
ALTER TABLE `mes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `paginas_post`
--
ALTER TABLE `paginas_post`
  ADD PRIMARY KEY (`id`);

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
-- Indexes for table `pedido_empenho`
--
ALTER TABLE `pedido_empenho`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_pedido` (`id_pedido`);

--
-- Indexes for table `postagens`
--
ALTER TABLE `postagens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tabela` (`tabela`);

--
-- Indexes for table `prioridade`
--
ALTER TABLE `prioridade`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `processos`
--
ALTER TABLE `processos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tipo` (`tipo`);

--
-- Indexes for table `processos_tipo`
--
ALTER TABLE `processos_tipo`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `saldos_adiantados`
--
ALTER TABLE `saldos_adiantados`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_setor` (`id_setor`);

--
-- Indexes for table `saldos_lancamentos`
--
ALTER TABLE `saldos_lancamentos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_setor` (`id_setor`),
  ADD KEY `categoria` (`categoria`);

--
-- Indexes for table `saldos_transferidos`
--
ALTER TABLE `saldos_transferidos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_setor_ori` (`id_setor_ori`),
  ADD KEY `id_setor_dest` (`id_setor_dest`);

--
-- Indexes for table `saldo_categoria`
--
ALTER TABLE `saldo_categoria`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `saldo_setor`
--
ALTER TABLE `saldo_setor`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_setor` (`id_setor`);

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
-- AUTO_INCREMENT for table `mes`
--
ALTER TABLE `mes`
  MODIFY `id` int(2) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;
--
-- AUTO_INCREMENT for table `paginas_post`
--
ALTER TABLE `paginas_post`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT for table `pedido`
--
ALTER TABLE `pedido`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `pedido_empenho`
--
ALTER TABLE `pedido_empenho`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `postagens`
--
ALTER TABLE `postagens`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;
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
-- AUTO_INCREMENT for table `processos_tipo`
--
ALTER TABLE `processos_tipo`
  MODIFY `id` int(1) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;
--
-- AUTO_INCREMENT for table `saldos_adiantados`
--
ALTER TABLE `saldos_adiantados`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `saldos_lancamentos`
--
ALTER TABLE `saldos_lancamentos`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `saldos_transferidos`
--
ALTER TABLE `saldos_transferidos`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `saldo_categoria`
--
ALTER TABLE `saldo_categoria`
  MODIFY `id` int(1) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
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
-- Limitadores para a tabela `pedido_empenho`
--
ALTER TABLE `pedido_empenho`
  ADD CONSTRAINT `pedido_empenho_ibfk_1` FOREIGN KEY (`id_pedido`) REFERENCES `pedido` (`id`);

--
-- Limitadores para a tabela `postagens`
--
ALTER TABLE `postagens`
  ADD CONSTRAINT `postagens_ibfk_1` FOREIGN KEY (`tabela`) REFERENCES `paginas_post` (`id`);

--
-- Limitadores para a tabela `processos`
--
ALTER TABLE `processos`
  ADD CONSTRAINT `processos_ibfk_1` FOREIGN KEY (`tipo`) REFERENCES `processos_tipo` (`id`);

--
-- Limitadores para a tabela `saldos_adiantados`
--
ALTER TABLE `saldos_adiantados`
  ADD CONSTRAINT `saldos_adiantados_ibfk_1` FOREIGN KEY (`id_setor`) REFERENCES `setores` (`id`);

--
-- Limitadores para a tabela `saldos_lancamentos`
--
ALTER TABLE `saldos_lancamentos`
  ADD CONSTRAINT `saldos_lancamentos_ibfk_1` FOREIGN KEY (`id_setor`) REFERENCES `setores` (`id`),
  ADD CONSTRAINT `saldos_lancamentos_ibfk_2` FOREIGN KEY (`categoria`) REFERENCES `saldo_categoria` (`id`);

--
-- Limitadores para a tabela `saldos_transferidos`
--
ALTER TABLE `saldos_transferidos`
  ADD CONSTRAINT `saldos_transferidos_ibfk_1` FOREIGN KEY (`id_setor_ori`) REFERENCES `setores` (`id`),
  ADD CONSTRAINT `saldos_transferidos_ibfk_2` FOREIGN KEY (`id_setor_dest`) REFERENCES `setores` (`id`);

--
-- Limitadores para a tabela `saldo_setor`
--
ALTER TABLE `saldo_setor`
  ADD CONSTRAINT `saldo_setor_ibfk_1` FOREIGN KEY (`id_setor`) REFERENCES `setores` (`id`);

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
