SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;


INSERT INTO `contrato_tipo` (`id`, `nome`) VALUES
(1, 'EMPENHO'),
(2, 'REFORCO'),
(3, 'ANULACAO');

INSERT INTO `licitacao_tipo` (`id`, `nome`) VALUES
(1, 'Dispensa de Licitacao'),
(2, 'Inexibilidade de Licitacao'),
(3, 'Adesao'),
(4, 'Compra Compartilhada'),
(5, 'Concorrencia Publica'),
(6, 'RP');

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

INSERT INTO `paginas_post` (`id`, `tabela`, `nome`) VALUES
(1, 'boaspraticas', 'Boas PrÃ¡ticas'),
(2, 'comemoracoes', 'ComemoraÃ§Ãµes'),
(3, 'dinamicas', 'DinÃ¢micas'),
(4, 'noticia', 'NotÃ­cias'),
(5, 'publico_interno', 'PÃºblico Interno'),
(6, 'tutoriais', 'POPs e Tutoriais');

INSERT INTO `postagens` (`id`, `tabela`, `titulo`, `data`, `ativa`, `postagem`) VALUES
(1, 1, 'Kit dos Gestores/Fiscais de Contrato', '2015-09-21', 1, '<h3>Kit dos Gestores/Fiscais de Contrato</h3><hr><p style=\"\"><img class=\"fr-dib fr-draggable\" src=\"../uploads/boaspraticas1.jpg\" data-name=\"boaspraticas1.jpg\" style=\"width: 539px;\"></p><p style=\"\">No m&ecirc;s de setembro de 2015, tivemos a satisfa&ccedil;&atilde;o de compartilhar, por solicita&ccedil;&atilde;o do Analista Administrativo Leonardo Ferreira Viana, do Hospital Universit&aacute;rio Cassiano Ant&ocirc;nio Moraes &ndash; HUCAM, de Vit&oacute;ria &ndash; ES, o Kit dos Gestores/Fiscais de Contratos elaborado pelo SOF.</p><p style=\"\"><br></p>'),
(2, 1, 'Visita do Contador Bruno Martini &ndash; HEUFPEL', '2015-10-22', 1, '<h3>Visita do Contador Bruno Martini &ndash; HEUFPEL</h3><hr><p style=\"\"><img class=\"fr-dib fr-draggable\" src=\"../uploads/82b711138325c1c967dd26f2467969e2eef584f6.jpg\" style=\"width: 443px;\"></p><p style=\"\">Entre os dias 19 e 21 de outubro de 2015, recebemos a visita do Contador Bruno Martini, do Hospital Escola da Universidade Federal de Pelotas, a fim de mapear os processos do SOF com o objetivo de subsidiar o in&iacute;cio das atividades do HEUFPEL enquanto filial da Empresa Brasileira de Servi&ccedil;os Hospitalares &ndash; EBSERH.</p><p style=\"\"><br></p>'),
(3, 1, 'Testandosasas', '2016-03-29', 0, '<h3>Testandosasas</h3><hr><p>asashaus</p>'),
(4, 1, 'AKAKAKA', '2016-04-07', 0, '<h3>AKAKAKA</h3><hr><p>SASKAJSKAJJSKA</p>'),
(5, 1, 'gyygygygygy', '2016-04-08', 0, '<h3>gyygygygygy</h3><hr><p>kkkk</p>'),
(6, 1, 'Publicado o I Caderno de Exec. Or&ccedil;ament. do HUSM', '2015-10-02', 1, '<h3 style=\"\">Publicado o I Caderno de Exec. Or&ccedil;ament. do HUSM</h3><p style=\"\"><img class=\"fr-dib fr-draggable\" src=\"../uploads/9293f3d2ce59193a6c719b982c87325e98f3c5d0.png\" style=\"width: 539px;\"></p><p style=\"\"><br></p><p style=\"\">Em 01 de outubro de 2015, &eacute; enviado &agrave; gr&aacute;fica o primeiro Caderno de Execu&ccedil;&atilde;o Or&ccedil;ament&aacute;ria do Hospital Universit&aacute;rio cuja edi&ccedil;&atilde;o ser&aacute; quadrimestral.</p><p style=\"\"><br></p>'),
(7, 2, 'Confraterniza&ccedil;&atilde;o', '2015-10-16', 1, '<h3 style=\"\">Confraterniza&ccedil;&atilde;o</h3><hr><p style=\"\"><img class=\"fr-dib fr-draggable\" src=\"../uploads/Almoco-no-Etnias.jpg\" data-name=\"Almoco-no-Etnias.jpg\" style=\"width: 539px;\"></p><p style=\"\">Em outubro de 2015, almo&ccedil;o de confraterniza&ccedil;&atilde;o entre colegas e amigos no restaurante Etnias. Um presente carinhoso do colega Paulo Francisco.</p><p style=\"\"><br></p>'),
(8, 2, 'Impress&atilde;o do 1&ordm; Caderno de Execu&ccedil;&atilde;o Or&ccedil;ament&aacute;ria', '2015-10-01', 0, '<h3>Impress&atilde;o do 1&ordm; Caderno de Execu&ccedil;&atilde;o Or&ccedil;ament&aacute;ria</h3><p><br></p><p>Em 01/10/2015, para alegria do SOF, ap&oacute;s nove meses do in&iacute;cio das atividades do setor, o I Caderno de Execu&ccedil;&atilde;o Or&ccedil;ament&aacute;ria do HUSM est&aacute; pronto para ser encaminhado &agrave; gr&aacute;fica.</p><p><img class=\"fr-dib fr-draggable\" src=\"http://localhost:8080/sof/uploads/caderno01.png\" style=\"width: 426px;\"></p>'),
(9, 2, 'Despedida do Antigo Setor de Contabilidade e Or&ccedil;amento do HUSM', '2015-12-07', 1, '<h3>Despedida do Antigo Setor de Contabilidade e Or&ccedil;amento do HUSM</h3><hr><p style=\"\">Ao final de Dezembro de 2014, houve a confraterniza&ccedil;&atilde;o que marcou a despedida do Setor de Contabilidade e Or&ccedil;amento do HUSM. <br><br>A partir desse momento, nascem o Setor de Or&ccedil;amento e Finan&ccedil;as e o Setor de Contabilidade Fiscal e Controladoria do HUSM/EBSERH.</p><p style=\"\"><img class=\"fr-dib fr-draggable\" src=\"../uploads/come01.png\" style=\"width: 474px;\"></p>'),
(10, 2, 'klakslask', '2016-04-07', 0, '<h3>klakslask</h3><hr><p>skalsklakskasa</p>'),
(11, 2, 'klakslask', '2016-04-07', 0, '<h3>klakslask</h3><hr><p>skalsklakskasa</p>'),
(12, 2, 'Comemora&ccedil;&atilde;o Teste', '2016-04-07', 0, '<h3>Comemora&ccedil;&atilde;o Teste</h3><hr><p>olaaaa tudo bein?</p>'),
(13, 2, '', '2016-07-23', 1, '<p style=\"\"><img class=\"fr-dib fr-draggable\" src=\"../uploads/9d343d4678341bc87b12324a528a1247e47b17fe.png\" data-name=\"9d343d4678341bc87b12324a528a1247e47b17fe.png\" style=\"width: 300px;\">testando em 19 de abril</p><p style=\"\">visualizando o teste em 23 de julho</p>'),
(14, 2, 'testando em 19 de abril', '2016-04-19', 0, '<h3><img class=\"fr-dib fr-draggable\" src=\"../uploads/9d343d4678341bc87b12324a528a1247e47b17fe.png\" data-name=\"9d343d4678341bc87b12324a528a1247e47b17fe.png\" style=\"width: 300px;\">testando em 19 de abril</h3>'),
(15, 3, 'Din&acirc;mica da Formiga', '2015-08-21', 1, '<h3 style=\"\">Din&acirc;mica da Formiga</h3><p style=\"\"><br></p><p style=\"\"><img class=\"fr-dib fr-draggable\" src=\"../uploads/din06-min.png\" data-name=\"din06-min.png\" style=\"width: 539px;\"></p><p style=\"\">Em um primeiro momento, o grupo foi reunido em c&iacute;rculo e cada um dos membros foi orientado a passar uma formiguinha imagin&aacute;ria para o colega ao lado.<br><br>Ap&oacute;s a formiguinha ser entregue ao &uacute;ltimo participante do c&iacute;rculo, o grupo recebe a orienta&ccedil;&atilde;o de dar um beijo na posi&ccedil;&atilde;o exata onde colocou a formiguinha no companheiro.<br><br>De uma forma descontra&iacute;da, foi criada a oportunidade para cada um colocar-se no lugar do outro diante da situa&ccedil;&atilde;o de receber a formiga, uma vez que todos tiveram de dar um beijinho no local onde colocaram a formiguinha no colega.<br><br>O objetivo dessa din&acirc;mica foi oportunizar a reflex&atilde;o acerca da empatia, um dos valores do Setor.</p>'),
(16, 3, 'Din&acirc;mica Farm&aacute;cia', '2016-04-07', 0, '<h3>Din&acirc;mica Farm&aacute;cia</h3><hr><p>EAI? BELEZA?</p>'),
(17, 4, 'Parcelas Referentes aos Sub-Repasses da Receita SUS', '2015-11-20', 1, '<h3>Parcelas Referentes aos Sub-Repasses da Receita SUS</h3><hr><p style=\"\"><img class=\"fr-dib fr-draggable\" src=\"../uploads/83127f58e0a63273e69a17940b064aceea939c86.png\" style=\"width: 539px;\"></p><p style=\"\">Memorando Circular, enviado pela EBSERH, informando o parcelamento dos recursos financeiros da produ&ccedil;&atilde;o SUS.</p><p style=\"\">Clique <a class=\"fr-strong\" href=\"../uploads/Sub-repasse-da-receita-SUS.pdf\" target=\"_blank\">aqui</a> para visualizar o documento.</p>'),
(18, 4, 'Prazo de Empenho para as despesas de Fonte de produ&ccedil;&atilde;o SUS', '2015-11-20', 1, '<h3 style=\"\">Prazo de Empenho para as despesas de Fonte de produ&ccedil;&atilde;o SUS</h3><hr><p style=\"\"><img class=\"fr-dib fr-draggable\" src=\"../uploads/e46ff3a74b4652bdf729c87e12419d151af32233.png\" style=\"width: 539px;\"></p><p style=\"\">Memorando Circular, enviado pela EBSERH, informando os prazos limite para execu&ccedil;&atilde;o dos cr&eacute;ditos recebidos por destaque e para as dota&ccedil;&otilde;es do pr&oacute;prio &oacute;rg&atilde;o.</p><p style=\"\"><br></p><p style=\"\"><a class=\"fr-strong\" href=\"../uploads/Prazo-de-empenho-para-as-despesas-de-fonte-de-producao-SUS.pdf\" target=\"_blank\">Memorando 383/SOF</a></p>'),
(19, 4, 'Relat&oacute;rios de Empenhos Abertos Referentes aos dois Primeiros Quadrimestres de 2015', '2015-10-22', 1, '<h3 style=\"\">Relat&oacute;rios de Empenhos Abertos Referentes aos dois Primeiros Quadrimestres de 2015</h3><hr><p style=\"\"><img class=\"fr-dib fr-draggable\" src=\"../uploads/1801165a2e1ca373e87bcba418fdbca554e18e77.png\" style=\"width: 539px;\"></p><p style=\"\">Memorando emitido pelo SOF solicitando o estorno dos saldos de empenhos cujos insumos n&atilde;o foram entregues nos dois primeiros quadrimestres de 2015.</p><p style=\"\"><a class=\"fr-strong\" href=\"../uploads/Relatorios-de-empenhos-abertos-referentes-aos-dois-primeiros-quadrimestres-de-2015.pdf\" target=\"_blank\">Memorando 354/SOF</a></p>'),
(20, 4, 'Anula&ccedil;&atilde;o de Empenhos', '2015-11-24', 1, '<h3>Anula&ccedil;&atilde;o de Empenhos</h3><hr><p style=\"\"><img class=\"fr-dib fr-draggable\" src=\"../uploads/7d134308cad8ab2c1b24de3c97b72a3fda60a52b.png\" style=\"width: 539px;\"></p><p style=\"\">Memorando emitido pelo SOF comunicando anula&ccedil;&atilde;o de empenhos com pend&ecirc;ncias de entrega.</p><p style=\"\"><a class=\"fr-strong\" href=\"../uploads/Anulacao-de-empenhos.pdf\" target=\"_blank\">Memorando Circular 384/SOF</a></p>'),
(21, 4, 'Comunicado aos Fornecedores', '2015-12-03', 1, '<h3 style=\"\">Comunicado aos Fornecedores</h3><p style=\"\">Of&iacute;cio aos Fornecedores sobre a Suspens&atilde;o no Fornecimento de Insumos/Servi&ccedil;os</p><p style=\"\"><a href=\"\" target=\"_blank\"><img class=\"fr-dib fr-draggable\" src=\"../uploads/2df7d20584ffa380cc5e0c211008832b8b1bab4e.png\" style=\"width: 539px;\"></a></p><p style=\"\"><a class=\"fr-strong\" href=\"../uploads/Para-os-Fornecedores.pdf\" target=\"_blank\">Of&iacute;cio/Circular n&ordm; 009/2015-GA/HUSM</a></p>'),
(22, 4, 'P&aacute;scoa Solid&aacute;ria do SOF', '2016-03-28', 1, '<h3 style=\"\"><strong>P&aacute;scoa Solid&aacute;ria do SOF</strong></h3><p style=\"\"><img class=\"fr-dib fr-draggable\" src=\"../uploads/8f1ceca976c6a2f5d24ad317503eae77954dee56.jpg\" style=\"width: 506px;\"></p><p>Representantes do Setor de Or&ccedil;amento e Finan&ccedil;as realizam a entrega de caixas de leite e chocolates para as crian&ccedil;as da Escolinha Esta&ccedil;&atilde;o dos Ventos na quinta-feira que antecede &agrave; P&aacute;scoa.</p><p style=\"\">Para saber como colaborar, clique <a href=\"http://www.estacaodosventos.com.br/\" target=\"_blank\">aqui</a>!</p><p style=\"\"><br></p><p style=\"\"><br></p>'),
(23, 4, 'Natal Solid&aacute;rio do SOF', '2016-03-25', 0, '<h3>Natal Solid&aacute;rio do SOF</h3><hr><p>Equipe do Setor de Or&ccedil;amento e Finan&ccedil;as doa ventiladores para as crian&ccedil;as do Centro de Desenvolvimento Comunit&aacute;rio Esta&ccedil;&atilde;o dos Ventos.&nbsp;</p><p>Para saber como colaborar, clique <a class=\"fr-strong\" href=\"http://www.estacaodosventos.com.br/\" target=\"_blank\"><span style=\"color: rgb(97, 189, 109); font-weight: normal;\">aqui</span></a>.</p><p style=\"\"><img class=\"fr-dib fr-draggable\" src=\"../uploads/2c5e8e9901ed71d4146c5c8d81784f9573d5e0d2.png\" style=\"width: 300px;\"></p>'),
(24, 4, 'Not&iacute;cia Editada', '2016-03-25', 0, '<h3>Not&iacute;cia Editada</h3><hr><p style=\"\">Editando not&iacute;cia...</p>'),
(25, 4, 'Natal Solid&aacute;rio do SOF', '2015-12-26', 1, '<h3 style=\"\">Natal Solid&aacute;rio do SOF</h3><hr><p style=\"\"><img data-name=\"2c5e8e9901ed71d4146c5c8d81784f9573d5e0d2.png\" class=\"fr-dib fr-draggable\" src=\"../uploads/2c5e8e9901ed71d4146c5c8d81784f9573d5e0d2.png\" style=\"width: 300px;\"></p><p style=\"\">Em dezembro de 2015, a equipe do Setor de Or&ccedil;amento e Finan&ccedil;as doa ventiladores para as crian&ccedil;as do Centro de Desenvolvimento Comunit&aacute;rio Esta&ccedil;&atilde;o dos Ventos.&nbsp;</p><p style=\"\">Para saber como voc&ecirc; pode colaborar, clique <a href=\"http://www.estacaodosventos.com.br/\" target=\"_blank\">aqui</a>!</p><p style=\"\"><br></p>'),
(26, 4, 'Editando', '2016-03-31', 0, '<h3>Editando</h3><hr><p style=\"\">alalalala</p>'),
(27, 4, 'Editando essa not&iacute;ciaA', '2016-03-31', 0, '<h3 style=\"\">Editando essa not&iacute;ciaA</h3><hr><p style=\"\">sashaushuashuaus</p>'),
(28, 4, 'oiaaaal', '2016-03-31', 0, '<h3 style=\"\">oiaaaal</h3><hr><p style=\"\">askajska</p>'),
(29, 4, 'Editara', '2016-03-31', 0, '<h3 style=\"\">Editara</h3><hr><p style=\"\">sahsuahushuahsu</p>'),
(30, 4, 'Data Retroativa', '2016-03-06', 0, '<h3 style=\"\">Data Retroativa</h3><hr><p style=\"\">estou voltando no passado shuahsuhua</p>'),
(31, 4, 'aasasasas', '2016-03-07', 0, '<h3 style=\"\">aasasasas</h3><hr>'),
(32, 4, 'Teste Visualiza&ccedil;&atilde;o', '2016-04-08', 0, '<h3 style=\"\">Teste Visualiza&ccedil;&atilde;o</h3><hr><p style=\"\">Esta &eacute; uma publica&ccedil;&atilde;o que deve ser visualizada somente por membros do Setor de Or&ccedil;amento e Finan&ccedil;as do HUSM.</p><p style=\"\">Se voc&ecirc; est&aacute; lendo esta publica&ccedil;&atilde;o e n&atilde;o possui um usu&aacute;rio e senha do Setor, das duas uma:&nbsp;</p><ul><li style=\"\">voc&ecirc; n&atilde;o &eacute; do sof</li><li style=\"\">voc&ecirc; &eacute; do sof e n&atilde;o tem um login, pe&ccedil;a para o administrador do sistema</li></ul><p style=\"\"><br></p>'),
(33, 4, 'Ebserh adquire mais de 1,3 mil computadores para hospitais universit&aacute;rios', '2016-03-26', 0, '<h3 style=\"\">Ebserh adquire mais de 1,3 mil computadores para hospitais universit&aacute;rios</h3><hr><p style=\"\">Investimento &eacute; de R$ 4,68 milh&otilde;es e a entrega ser&aacute; em aproximadamente 30 dias</p><p style=\"text-align: justify;\"><img class=\"fr-draggable fr-dii fr-fil\" src=\"http://andes-ufsc.org.br/wp-content/uploads/2015/10/fotoresize.jpg\" style=\"width: 279px;\"><em><strong>Bras&iacute;lia (DF)</strong></em> &ndash; Com o intuito de renovar o parque tecnol&oacute;gico dos hospitais universit&aacute;rios federais, a Empresa Brasileira de Servi&ccedil;os Hospitalares (Ebserh) adquiriu 1.348 microcomputadores para 32 unidades. O investimento &eacute; de R$ 4,68 milh&otilde;es com recursos do Banco Nacional de Desenvolvimento Econ&ocirc;mico e Social (BNDES).</p><p style=\"text-align: justify;\">&ldquo;A parceria junto ao BNDES &eacute; de suma import&acirc;ncia e visa prover a infraestrutura adequada de TI nos hospitais universit&aacute;rios para a utiliza&ccedil;&atilde;o do sistema Aplicativo de Gest&atilde;o para Hospitais Universit&aacute;rios Federais (AGHU), melhorando assim o funcionamento do hospital e o atendimento &agrave; popula&ccedil;&atilde;o por meio do SUS&rdquo;, ressaltou o presidente da Ebserh, professor Newton Lima.</p><p style=\"text-align: justify;\"><br></p><p style=\"text-align: justify;\">Os computadores j&aacute; v&ecirc;m com sistema operacional instalado e a entrega se dar&aacute; em aproximadamente 30 dias. Essa aquisi&ccedil;&atilde;o comp&otilde;e um conjunto de a&ccedil;&otilde;es que vem sendo desenvolvidas pela estatal por meio da Diretoria de Gest&atilde;o de Processos e Tecnologia da Informa&ccedil;&atilde;o (DGPTI). Desde que assumiu a gest&atilde;o de 37 hospitais universit&aacute;rios, a Ebserh j&aacute; entregou 9.468 computadores.</p><p style=\"text-align: justify;\">De acordo com o Diretor De Gest&atilde;o De Processos e Tecnologia da Informa&ccedil;&atilde;o (DGPTI), Cristiano Cabral, os hospitais alocar&atilde;o os equipamentos dependendo de suas necessidades. &ldquo;Os computadores ser&atilde;o entregues, contribuindo para a moderniza&ccedil;&atilde;o do parque tecnol&oacute;gico dos hospitais universit&aacute;rios federais e ser&atilde;o essenciais para ampliar o atendimento, monitoramento e controle atrav&eacute;s do uso dos sistemas AGHU e SIG da Rede Ebserh&rdquo;, esclareceu.</p><p style=\"text-align: justify;\"><em><strong><u>Outras aquisi&ccedil;&otilde;es</u></strong></em></p><p>Desde o in&iacute;cio de suas atividades em 2012, a Ebserh adquiriu outros produtos na &aacute;rea de Tecnologia da Informa&ccedil;&atilde;o como 1.066 ativos de rede e componentes, 196 servidores, 11 racks e 14 unidades de armazenamento, totalizando investimentos na ordem de R$ 45,9 milh&otilde;es.</p><p>Al&eacute;m disso, foram adquiridos 23 cont&ecirc;ineres datacenters (CDC), tendo sido entregues 15 unidades. 12 CDCs j&aacute; est&atilde;o em pleno funcionamento, atendendo 12 hospitais e duas maternidades. Para estes j&aacute; em opera&ccedil;&atilde;o, incluindo a parte da elabora&ccedil;&atilde;o do projeto, foram investidos mais de R$ 26,4 milh&otilde;es. Este ano, h&aacute; previs&atilde;o de se inaugurar mais nove CDCs e um datacenter indoor (sala cofre), que atender&aacute; a mais 12 unidades hospitalares da rede Ebserh.</p><p>O datacenter &eacute; uma solu&ccedil;&atilde;o modular de r&aacute;pida implanta&ccedil;&atilde;o, longa durabilidade e tolerante a desastres, equipado com nobreaks modulares, gerador e sistema de g&aacute;s para combate a inc&ecirc;ndio. O pr&oacute;ximo cont&ecirc;iner ser&aacute; inaugurado no Hospital Universit&aacute;rio Maria Aparecida Pedrossian da Universidade Federal do Mato Grosso do Sul (Humap-UFMS), em Campo Grande, no dia 10 de mar&ccedil;o.</p><p style=\"\"><em><u><strong>Coordenadoria de Comunica&ccedil;&atilde;o Social da Ebserh</strong></u></em></p>'),
(34, 4, 'asasasasas', '2016-04-07', 0, '<h3>asasasasas</h3><hr><p>sasasas</p>'),
(35, 4, 'shuasuhasuhsauh', '2016-04-08', 0, '<h3 style=\"\">shuasuhasuhsauh</h3><hr><p style=\"\">aalsalsa</p>'),
(36, 4, 'Not&iacute;cia para teste', '2016-04-09', 0, '<h3>Not&iacute;cia para teste</h3><hr><p>uollll</p>'),
(37, 4, '', '2016-04-19', 0, '<p>testando em 19 de abril</p>'),
(38, 4, '', '2016-04-19', 0, '<p>testando em 19 de abril</p>'),
(39, 4, 'Nova Not&iacute;cia', '2016-04-19', 0, '<h3 style=\"\">Nova Not&iacute;cia</h3><hr><p style=\"\">Este &eacute; um exemplo de nova not&iacute;cia.</p><p style=\"\">Se nenhum erro ocorrer, ela ser&aacute; exclu&iacute;da.</p>'),
(40, 4, 'Cadastro Atualizado no SICAF', '2016-05-06', 1, '<h3 style=\"\"><strong>Cadastro Atualizado no SICAF</strong></h3><p style=\"\"><strong><img class=\"fr-dib fr-draggable\" src=\"../uploads/6fd28f42ca45a53a37bc26a6d6fbf74a1d77d3e3.png\" style=\"width: 539px;\"></strong><br></p>'),
(41, 4, 'SOF no Projeto Catalunha', '2015-06-01', 1, '<h3><strong>SOF no Projeto Catalunha</strong></h3><p><img class=\"fr-dib fr-draggable\" src=\"../uploads/3573a3ec1b1efce0be34689527b83b007f584d6d.png\" style=\"width: 539px;\"></p><p>Entre 25 e 29 de maio, em Natal - RN, o SOF participou &nbsp;do &ldquo;<em>Compartilhamento de experi&ecirc;ncias e desenvolvimento de mecanismos e instrumentos para o aprimoramento da gest&atilde;o econ&ocirc;mico-financeira dos Hospitais Universit&aacute;rios Federais (HUF)&rdquo;,&nbsp;</em>uma parceria entre a EBSERH e o Cons&oacute;rcio Hospitalar da Catalunha/Espanha.</p><p>Tamb&eacute;m participaram do encontro os hospitais HUOL-UFRN e o HU-UFMA.&nbsp;</p><p><br></p>'),
(42, 4, 'Curso de Capacita&ccedil;&atilde;o para a Equipe do SOF', '2016-05-10', 1, '<h3 style=\"\"><strong>Curso de Capacita&ccedil;&atilde;o para a Equipe do SOF</strong></h3><p style=\"\"><img class=\"fr-dib fr-draggable\" src=\"../uploads/8aeb2fb67030e254d396b010dc8492e29183f788.jpg\" style=\"width: 539px;\"></p><p style=\"\">No dia 10 de maio ocorreu o I Curso de Capacita&ccedil;&atilde;o para a Equipe do SOF ministrado pela Contadora Greice Eccel Pontelli que atua como T&eacute;cnica em Contabilidade no Setor de Avalia&ccedil;&atilde;o e Controladoria do HUSM.&nbsp;</p><p style=\"\">O treinamento teve como objetivo capacitar a equipe do Setor de Or&ccedil;amento e Finan&ccedil;as no que diz respeito &agrave;s Funcionalidades do Portal de Compras Governamentais.</p><p style=\"\"><img class=\"fr-dib fr-draggable\" src=\"../uploads/d7ddad8e65a37db4cbcefbb3ae85a16ec0025742.jpg\" style=\"width: 539px;\"></p><p style=\"\"><br></p><p style=\"\"><br></p>'),
(43, 6, 'Emiss&atilde;o de GRU', '2015-10-22', 1, '<h3 style=\"\">Emiss&atilde;o de GRU</h3><hr><p style=\"\">Ap&oacute;s o pagamento, enviar c&oacute;pia do comprovante de pagamento e da GRU para o <a href=\"http://localhost:8080/sof/view/faleconosco.php\" target=\"_blank\">e-mail do SOF</a>.</p><p style=\"\"><br></p><p style=\"\"><span class=\"fr-video fr-dvb fr-draggable\" contenteditable=\"false\" draggable=\"true\"><iframe src=\"//www.youtube.com/embed/Hx4bw0CWnFQ\" allowfullscreen=\"\" frameborder=\"0\" height=\"360\" width=\"640\"></iframe></span>\r\n<br></p>'),
(44, 3, 'Nova Not&iacute;cia', '2016-07-23', 0, '<h3 style=\"\">Nova Not&iacute;cia</h3><hr><p style=\"\">Dessa vez, muito mais segura!</p><p style=\"\">Essa not&iacute;cia ser&aacute; usada para testes.</p><p style=\"\"><br></p><p style=\"\">Ela foi editada aqui.</p><p style=\"\"><br></p><p style=\"\">Mudando de p&aacute;gina.</p><p style=\"\"><br></p><p style=\"\">Excluindo.</p>');

INSERT INTO `prioridade` (`id`, `nome`) VALUES
(1, 'Normal'),
(2, 'Preferencial'),
(3, 'Urgente'),
(4, 'Emergencial'),
(5, 'Rascunho');

INSERT INTO `problemas` (`id`, `id_setor`, `assunto`, `descricao`) VALUES
(1, 5, 'Pesquisa de processos.', 'SÃ³ para informar que o espaÃ§o destinado para a pesquisa do processo que nÃ£o funcionava, estÃ¡ funcionando perfeitamente agora. '),
(2, 5, 'Codigos ', 'Boa tarde, mando alguns cÃ³digos que nÃ£o foram encontrados no site no momento de por eles.\r\nALMOXARIFADO \r\n2600875 do pregÃ£o 011/2016 - processo: 23541.000002/2016-52\r\nFARMACIA MEDICAMENTOS \r\nCONNI370FA do pregÃ£o 44/2016 - processo 23541.000113/2016-69\r\n '),
(3, 5, 'Meus Pedidos', 'JoÃ£o, nÃ£o aparece nada no menu Meus Pedidos.... NÃ£o deveriam aparecer os pedidos que enviei? Bju, Iara'),
(4, 5, 'Meus Pedidos', 'Ã‰ porque tu resetou o banco?'),
(5, 2, 'AnÃ¡lise', 'JoÃ£o, qdo clico no lapisinho para analisar o pedido ele nÃ£o abre para eu analisar!!'),
(6, 4, 'Mais problemas :/', 'OlÃ¡, estes sÃ£o alguns processos da far.materias que quando adicionei no site pela primeira vez constavam, e agora quando fui adicionar novamente nÃ£o foi encontrado. \r\n23541.000121/2016-13\r\n23541.000131/2016-41\r\n23541.000376/2015-97'),
(7, 4, 'Arrumar', 'OlÃ¡, se tu puder arrumar na pagina inicial do site, ali onde diz missÃ£o, na ultima frase tem a palavra necessidade, escrita com 3 s haha E hoje durante a tarde tentei entrar pelo login do Almoxarifado e nÃ£o consegui, nÃ£o sei se tu estava arrumando alguma coisa e ficou indisponÃ­vel, ou se houve algum problema.'),
(8, 2, 'Aguarda Siafi ', 'No momento em que sÃ£o colocadas as informaÃ§Ãµes do Siafi (nÃºmero e data), nÃ£o foi possÃ­vel colocar uma data passada. Por exemplo, se o empenho saiu ontem, nÃ£o consigo usar essa data hoje. \r\n ThaÃ­ne Sell'),
(9, 2, 'Fazer relatÃ³rio ', 'Quando muda-se o status, de \"Empenhado\" para \"Enviado ao Ordenador\", nÃ£o Ã© possÃ­vel fazer isso mais de uma vez no mesmo login. Para que se faÃ§a isso, estÃ¡ sendo necessÃ¡rio sair do site e fazer o login novamente. \r\nThaÃ­ne Sell ');

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

INSERT INTO `saldo_categoria` (`id`, `nome`) VALUES
(1, 'normal'),
(2, 'adiantamento'),
(3, 'transferencia'),
(4, 'antecipacao');

INSERT INTO `saldo_setor` (`id`, `id_setor`, `saldo`) VALUES
(1, 2, '0.000');

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

INSERT INTO `setores_grupos` (`id`, `id_setor`, `cod`, `nome`) VALUES
(1, 5, '201', 'COMBUST/LUBRIF. AUTO'),
(2, 5, '202', 'COMB/LUBR. OUTRAS FINAL.'),
(3, 5, '203', 'GÁS ENGARRAFADO MEDICIN.'),
(4, 5, '204', 'GÊNEROS ALIMENTÍCIOS'),
(5, 5, '205', 'MATERIAL ONTOTOLÓGICO'),
(6, 5, '206', 'MAT. EDUC/ESPORTIVO'),
(7, 5, '207', 'MAT.EXPEDIENTE'),
(8, 5, '208', 'MAT. PROCES. DE DADOS'),
(9, 5, '209', 'MAT. ACONDIC./ EMBALAGEM'),
(10, 5, '210', 'MAT. CAMA/MESA/BANHO'),
(11, 5, '211', 'MAT. DE COPA E COZINHA'),
(12, 5, '213', 'MAT. LIMPEZA/HIGIENE'),
(13, 5, '214', 'UNIFORM / TEC / AVIAMENTOS'),
(14, 5, '215', 'MAT. MANUT. BENS IMÓVEIS'),
(15, 5, '216', 'MAT. MANUT. BENS MÓVEIS'),
(16, 5, '217', 'MAT. ELÉTRICO/ELETRÔNICO'),
(17, 5, '218', 'MAT. PROTEÇÃO/SEGURANÇA'),
(18, 5, '219', 'MAT. AUDIO/VÍDEO/FOTO'),
(19, 5, '220', 'MAT. P/ PROD. INDUSTRIAL'),
(20, 5, '221', 'MAT. LABORATORIAL '),
(21, 5, '222', 'GÁS (GLP)'),
(22, 5, '223', 'MAT. HOSPITALAR'),
(23, 5, '224', 'MAT. P/ MANUT. DE VEÍCULOS'),
(24, 5, '225', 'MAT. P/ SINALIZAÇÃO VISUAL OUTROS MAT.'),
(25, 5, '230', 'REC. DIV. EXERC. ANTERIOR'),
(26, 5, '240', 'REEQUILIBRIO ECONÔMICO FINANCEIRO'),
(27, 3, '301', 'ANTINEOPLÁSICOS E ADJUVANTES'),
(28, 3, '302', 'ANESTÉSICOS E CONTROLADOS'),
(29, 3, '303', 'GERAIS NÃO INJETÁVEIS'),
(30, 3, '304', 'GERAIS INJETÁVEIS E CONTRASTES'),
(31, 3, '305', 'ANTIMICROBIANOS'),
(32, 3, '306', 'SOLUÇÕES PARENTERAIS E DIÁLISE'),
(33, 3, '307', 'OUTROS'),
(34, 3, '362', 'NUTRIÇÃO PARENTERAL'),
(35, 3, '330', 'REC. DÍV. EXERC. ANTERIOR'),
(36, 3, '340', 'REEQUILIBRIO ECONÔMICO FINANCEIRO'),
(37, 4, '401', 'MAT. GERAIS, EQUIPOS, EXTENSORES, CONEXÕES'),
(38, 4, '402', 'CURATIVOS GERAIS...'),
(39, 4, '403', 'AGULHAS E CATETERES GERAIS...'),
(40, 4, '404', 'BOLSA, CÂNULAS, DRENOS, SONDAS...'),
(41, 4, '405', 'DIÁLISE PERITONEAL INTERMITENTE DPA E DPAC...'),
(42, 4, '406', 'PRÓTESES GERAIS, ENXERTOS, VÁLVULAS...'),
(43, 4, '407', 'INSTRUMENTOS DE SUTURA MECÂNICA, GRAMPEADORES...'),
(44, 4, '409', 'RECONSTRUÇÃO DE MAMA, IMPLANTES MAMÁRIOS + EXPANSORES DE TECIDO'),
(45, 4, '408', 'MAT. DE CPRE - GASTRO-PNEUMO-URO, PINÇAS, ALÇAS,PAPILÓTOMOS...'),
(46, 4, '410', 'ARTROSCOPIA POR VÍDEO, LÂMINAS DE SHAVER, RADIOFREQÊNCIA...'),
(47, 4, '420', 'ANGIOPLASTIA DE CORONÁRIA..., STENTS, BALÕES...'),
(48, 4, '421', 'ANGIOPLASTIA PERIFÉRICA E ENDOPRÓTESES...STENTS, BALÕES...'),
(49, 4, '422', 'CIRURGIA CARDÍACA, COM C.E.C. (CIRCULAÇÃO EXTRACORPÓREA), MINI CEC...'),
(50, 4, '423', 'MARCA-PASSO (GERADORES DE PULSO ELÉTRICO)...'),
(51, 4, '424', 'CATETERISMO CORONÁRIO E FFR, CATETER DE DIAGNÓSTICO, INTRODUTORES...'),
(52, 4, '425', 'VALVULOPLASTIA, STENTS RECOBERTO E NÃO RECOBERTO...'),
(53, 4, '426', 'EMBOLIZAÇÃO COM MICROPARTÍCULAS DE PVA E MICROMOLAS (COILS)...'),
(54, 4, '461', 'AUTO-TRANSFUSÃO DE HEMODERIVADOS (AUTOLOG)...'),
(55, 4, '462', 'MISTURADOR DE SOLUÇÕES (PINNACLE), EQUIPOS, BOLSAS, NUTRIÇÃO PARENTERAL.'),
(56, 4, '463', 'MONITOR DO NÍVEL DE CONSCIÊNCIA (BIS), SENSORES ADULTO E PED.'),
(57, 4, '464', 'BOMBAS DE INFUSÃO DE PRECISÃO VOLUMÉTRICA, EQUIPOS...'),
(58, 4, '465', 'HEMODIÁLISE + OSMOSE REVERSA PORTÁTIL, FILTROS, LINHAS...'),
(59, 4, '466', 'MONITOR DO DCC (DÉBITO CARDÍACO CONTÍNUO), CATETERDE SWAN-GANZ'),
(60, 4, '467', 'MONITORIZAÇÃO INTRACRANIANA PIC, SENSORES...'),
(61, 4, '430', 'REC. DÍV. EXERC. ANTERIOR'),
(62, 4, '440', 'REEQUILIBRIO ECONÔMICO FINANCEIRO'),
(63, 9, '204N', 'GENEROS ALIMENTICIOS'),
(64, 9, '209N', 'MAT. ACONDIC./EMBALAGEM'),
(65, 9, '210N', 'MAT. CAMA/MESA/BANHO'),
(66, 9, '211N', 'MAT. DE COPA E COZINHA'),
(67, 9, '213N', 'MAT. LIMPEZA/HIGIENE'),
(68, 9, '214N', 'UNIFORM / TEC / AVIAMENTOS'),
(69, 9, '230N', 'REC. DIV. EXERC. ANTERIOR'),
(70, 9, '240N', 'REEQUILIBRIO ECONOMICO FINANCEIRO'),
(71, 6, '215D', 'MAT. MANUT. BENS IMOVEIS'),
(72, 6, '216D', 'MAT. MANUT. BENS MOVEIS'),
(73, 6, '104D', 'AGUA'),
(74, 6, '105D', 'LUZ'),
(75, 6, '111D', 'VIGILLARE'),
(76, 6, '114D', 'SULCLEAN'),
(77, 6, '116D', 'MANUTENCAO DE INFRA-ESTRUTURA (MAO DE OBRA)'),
(78, 6, '117D', 'MANUTENCAO DE EQUIPAMENTO'),
(79, 6, '198D', 'REFORMAS EM INFRAESTRUTURAS'),
(80, 6, '197D', 'MAT. UTILIZADOS NA REFORMA'),
(81, 6, '112D', 'LOCACAO DE EQUIPAMENTO'),
(82, 6, '230D', 'REC. DIV. EXERC. ANTERIOR'),
(83, 6, '240D', 'REEQUILIBRIO ECONOMICO FINANCEIRO'),
(84, 6, '199D', 'EQUIPAMENTO PERMANENTE'),
(85, 6, '200D', 'PERMANENTE - OBRAS'),
(86, 15, '103I', 'TELEFONE FIXO'),
(87, 15, '230I', 'REC. DIV. EXERC. ANTERIOR'),
(88, 15, '240I', 'REEQUILIBRIO ECONOMICO FINANCEIRO'),
(89, 7, '603', 'ORTESES E PROTESES - TRAUMATO'),
(90, 16, '701', 'PROTESES AUDITIVAS - ALMOX MAT. GERAIS'),
(91, 14, '500', 'RADIOTERAPIA'),
(92, 15, '102I', 'TELEFONE MOVEL');

INSERT INTO `sistema` (`ativo`) VALUES
(1);

INSERT INTO `status` (`id`, `nome`) VALUES
(1, 'Rascunho'),
(2, 'Em Analise'),
(3, 'Reprovado'),
(4, 'Aprovado'),
(5, 'Aguarda Orcamento'),
(6, 'Aguarda SIAFI'),
(7, 'Empenhado'),
(8, 'Enviado ao Ordenador'),
(9, 'Enviado ao Fornecedor'),
(10, 'Recebido da Unidade de Aprovacao');

INSERT INTO `usuario` (`id`, `nome`, `login`, `senha`, `id_setor`, `email`) VALUES
(1, 'Joao Bolsson', 'joao', '$1$HaIRlx72$IDbDt1eJGU.jj0wK65NEn/', 2, 'joao@gmail.com'),
(2, 'FarmÃ¡cia de Medicamentos', 'jaja', '$1$WEoaPOvt$Ywfv/12D9tTHNsWY5tyE31', 3, 'joaaa@hotmail.com'),
(3, 'Kkalak', 'iara', '$1$lgF0hIwv$gTSmmfR448sfjwB8WWAii.', 2, 'jaja@gmail.com'),
(4, 'FarmÃ¡cia', 'farmacia', '$1$tyyS5boM$LMpGdgzJOaceC4Kub0ldV/', 3, 'mumu@gmail.com'),
(5, 'Testando', 'teste', '$1$mlWhF1uM$I9VUBrZy4C5GwgY1EZ8Bf1', 2, 'ulala@gmail.com'),
(6, 'Teste ulala', 'ulala', '$1$pGfwx3.B$H0cC7TaOd7/i5w6tRWAiO1', 2, 'joaovictorbolsson@gmail.com'),
(7, 'oiiii', '', '$1$YtJKni.F$bsPZzVMDOmIritxoqprvo1', 2, 'oiia@gmail.com'),
(8, 'OLAM', 'olam', '$1$7fL30IhN$0si6iaDQaEpwDJA7Sj2dR0', 2, 'haha@gmail.com'),
(10, 'Set', 'olamundo', '$1$gTA8XJu8$0Ln8wfEAa6ypsrgYcemN3/', 2, 'joaovictorbolsson@hotmail.com');

INSERT INTO `usuario_permissoes` (`id_usuario`, `noticias`, `saldos`, `pedidos`, `recepcao`) VALUES
(1, 1, 1, 1, 0),
(2, 0, 0, 0, 0),
(3, 1, 1, 1, 0),
(4, 0, 0, 0, 0),
(5, 0, 1, 0, 1),
(6, 0, 1, 1, 0),
(7, 1, 1, 0, 0),
(8, 0, 1, 1, 0),
(10, 1, 0, 1, 0);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
