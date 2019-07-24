## CHANGELOG ##

#### v2.4.5 - 24/07/2019 ####
- Organização da interface para AIHS
- Relatório de Receitas Recebidas
- Correção de bug na inserção de observações (<= 300 caracteres)
- Cadastro de tipos para receitas

#### v2.4.4 - 07/07/2019 ####
- Implementação de tabela para visualização, edição e remoção de Receitas Recebidas (AIHS)

#### v2.4.3 - 28/06/2019 ####
- Implementação de formulário Receitas Recebidas (AIHS)
- Correção do relatório SIAFI (com opção: ignorar fontes)

#### v2.4.2 - 02/06/2019 ####
- Inicio de implementação de AIHs (somente formulário inicial)

#### v2.4.1 - 24/02/2019 ####
- Suporte para mais de uma nota por mês
- Organização da estrutura interna do sistema
- Relatório de contrato mostrando todas as notas
- Suporte para exclusão de contratos

#### v2.4.0 - 06/01/2019 ####
- Editar NFs lançadas
- Corrige bug o checkbox 'Reajuste' que não estava operando
- Adiciona grupo para o qual a mensalidade é resignada
- Adiciona filtros para geração de relatórios do contrato (com nota, paga, etc)
- Criação do banco de dados para 2019

#### v2.3.10 - 11/11/2018 ####
- Separa pedidos em vencimento por setor
- Adiciona novos campos (paga e aguarda orçamento) no cadastro de mensalidade de contratos

#### v2.3.9 - 01/09/2018 ####
- Adiciona vigência completa (início e fim) de contratos
- Relatório de contratos
- Ajustes na página inicial de contratos

#### v2.3.8 - 05/07/2018 ####
- Cadastra reajuste de mensalidade (opcional)
- Cadastra CNPJ da empresa

#### v2.3.7 - 17/06/2018 ####
- Adiciona nome do fornecedor nos pedidos em vencimento
- Funcionalidade para gerenciar contratos de empresas

#### v2.3.6 - 22/04/2018 ####
- Imprime lista de pedidos em vencimento com somatório e descrição do relatório
- Mostra somente pedidos em vencimento que estão em análise
- Elimina restrição de usuário na edição de pedidos reprovados e rascunhos
- Modifica importação de itens: apenas faz o upload do arquivo com os dados, fica mais rápido assim

#### v2.3.5 - 01/04/2018 ####
- Mostra os pedidos que irão vencer no mês atual ao invés dos pregões

#### v2.3.4 - 18/03/2018 ####
- Relatório de Processos não devolvidos (recepção)
- Alerta para vencimento de pregões no mês atual

#### v2.3.3 - 11/03/2018 ####
- Corrige bug ao editar empenhos
- Permite empresas acessarem o site para visualizarem arquivos

#### v2.3.2 - 13/02/2018 ####
- Corrige bug ao salvar informações no site
- Permite a alteração de pedidos mesmo após a análise (precisa alterar o status e reprovar o pedido). As informações salvas de empenho e fontes são mantidas. Ao reprovar o pedido basta voltar para o setor que fez o pedido e continuar a edição dos "Rascunhos". Quando o pedido for enviado ao SOF novamente ele estará "Em Análise".
- Adiciona nova página "Ações Sociais"
- Adiciona nova página "Depoimentos"
- Corrige bug ao imprimir determinados pedidos (caracteres inválidos)

#### v2.3.1 - 29/01/2018 ####
- Desvinculação de pedidos com fontes e transferências de saldos com fontes
- Melhoria do relatório de liberações orçamentárias (melhor descrição do relatório e retirada de campos redundantes)
- Realização de pedidos marcados com Plano de trabalho
- Ajustes no layout do relatório de pedidos
- Correção de bug no relatório de pedidos (mostrava pedidos que não eram do Setor selecionado)
- Possibilidade de desfazer liberação orçamentária (disponível somente ao usar o banco principal)

Nota: O banco principal (main) é o banco do ano corrente

#### v2.3.0 - 15/01/2018 ####
- Corrige bug ao editar itens (valor utilizado sempre incorreto)
- Opção "Todas" no campo de prioridades para relatório de pedidos
- Permite edição de itens cancelados e cancelar/descancear itens pela ferramenta de edição
- Corrige bug ao remover pedido sem itens
- Retira limite de linhas de relatório de pedidos e outros
- Finaliza relatório SIAFI com seleção de múltiplas fontes

#### v2.2.10 - 17/12/2017 ####
- Corrige bug de codificação de caracteres ao editar itens
- Corrige bug na remoção dos pedidos
- Cria mecanismo para alternar entre diferentes bancos dentro do site (2017 e 2018)
- Corrige relatório de usuários: mostra apenas os ativos no sistema.

#### v2.2.9 - 27/11/2017 ####
- Permite seleção de mais de um processo ao gerar relatório SIAFI.

#### v2.2.8 - 12/11/2017 ####
- Corrige inconsistência na informação de SIAFI no banco
- Muda formato de datas em itens para facilitar relatórios futuros
- Implementa relatório SIAFI por setor, fonte de recurso, número de processo e vigência

#### v2.2.7 - 04/11/2017 ####
- Adiciona prioridade "Hoje" para realização de pedidos.
- Formatação de relatório de pedidos (retirada de informações desnecessárias)

#### v2.2.6 - 29/10/2017 ####
- Corrige bug que zerava o saldo do sof e atualizava constantemente o saldo dos setores.
- Corrige bug ao reprovar pedido.
- Corrige bug ao mostrar o painel das solicitações (mensagem do DataTables)
- Coloca valor mínimo nas quantidades (de contrato e utilizada) ao editar item (evita conflito de informações)]
- Agrega relatório de fontes com relatório de pedidos.

#### v2.2.5 - 15/10/2017 ####
- Correção de bug ao editar itens
- Melhoria geral na edição e cadastro de itens

#### v2.2.4 - 11/09/2017 ####
- Remove possibilidade de desfazer liberação orçamentária.
- Insere novos objetos para representar liberações orçamentárias e pedido de contrato.
- Coleta de bugs e análise de performance (importação de itens ainda está instável)

#### v2.2.3 - 27/08/2017 ####
- Construção de objetos para representar um pedido, um setor, uma fonte de recurso, etc. Isso deverá ajudar na padronização das funções futuramente e evitar erros de calculo.
- Atualização de valores de pedidos errados e saldos dos setores.

#### v2.2.2 - 12/08/2017 ####
- Melhoria na importação de itens
- Geração de relatório de fontes de recurso

Nota: A importação não é feita mais diretamente no site, ao invés disso, é criado um arquivo com as inserções que devem ser feitas no banco e enviada uma notificação para o e-mail do admin (eu). Dentro de 1 dia ou menos, o arquivo vai ser importado para o site pelo admin. É uma processo mais demorado mas que garante que o site não pare de responder.

#### v2.2.1 - 25/06/2017 ####
- Reativação de usuário quando o e-mail de um usuário que está sendo cadastrado já está no sistema, porém inativo. As antigas informações são substituídas pelas novas cadastradas, inclusive as permissões.
- Cadastro de atestados para os usuários (somente para admin)
- Atestados aparecerem no relatório e as horas abonadas são automaticamente somadas no total de horas mostrados no topo.

#### v2.2.0 - 18/06/2017 ####
- Nova tela de login
- Segrega arquivos JavaScript em outros menores (não havia necessidade de carregar várias funções que nunca deveriam ser usadas em determinadas páginas)
- Implementação de pedidos com vinculação de fonte de recurso.
- Implementação de nova categoria de lançamento de saldo: recolhimento. Pedidos sem fonte de recurso e reprovados voltam para o setor como rascunho e o valor desse pedido, ao invés de retornar para o setor que o fez, volta para o SOF.

Nota 1: todo o valor de saldo disponível para os setores foi recolhido pelo SOF. Assim, os próximos pedidos deverão ter uma fonte anexada.

Nota 2: pedidos que já foram enviados em versões anteriores, sem fonte de recurso, deverão seguir o mesmo fluxo de antes: o SOF cadastra a fonte de recurso e caso o pedido seja reprovado, volta para o setor que o fez e o valor desse pedido retorna ao SOF. Se esse pedido voltar novamente ao SOF, terá uma fonte de recurso anexada pelo setor que o fez.

#### v2.1.16 - 14/05/2017 ####
- Corrige bug para visualizar justificativa nas Solicitações de Alteração de Pedido
- Permite ao SOF desativar um usuário (somente login iara) Obs.: os registros do usuário permanecem, mas ele não poderá mais logar no sistema.
- Começa a versionalizar a estrutura do banco

#### v2.1.15 - 02/05/2017 ####
- Adiciona vigência ao processo
- Justificativas pré-carregadas pelo SOF (saldos)

#### v2.1.14 - 22/04/2017 ####
- Envio de problemas relatados para o e-mail joaovictorbolsson@gmail.com
- Edição completa das informações de um item

#### v2.1.13 - 15/04/2017 ####
- Correção de bug no cadastro de empenhos
- Mostra changelog para o usuário do SOF
- Implementa logs de erros

#### v2.1.12 - 07/04/2017 ####
- Não recarrega toda a página quando um pedido for alterado pela análise do SOF
- Suporte para relatório de pedidos com mais de uma prioridade
- Melhoria de funções e correções pequenas
- Documentação
- Site será acessado somente via https

#### v2.1.11 - 29/03/2017 ####
- Corrige bug no cadastro de empenho de um pedido
- Melhora arquitetura da aplicação

#### v2.1.10 - 19/03/2017 ####
- Implementa testes com PHPUnit
- Corrige bug do somatório no relatório de pedidos selecionados pelo SOF
- Corrige bug do valor total de pedidos (problema na edição de itens)
- Relatório para os setores de todos os pedidos que já tem SIAFI
- Seq item em ordem crescente no pedido impresso
- Exibe o código do grupo no painel Grupo nas solicitações de empenho
- Corrige bug ao desfazer liberação e realizar transferência

#### v2.1.9 - 11/03/2017 ####
- Implementação de classe Query para executar as consultas no banco
- Correção de bugs e aprimoramento de funções
- Exclusão de código duplicado

#### v2.1.8 - 05/03/2017 ####
- Implementação de ponto eletrônico (beta)
- Evolução da arquitetura (padrão singleton na maioria das classes)
- Melhoria de funções e organização de código
- Revisão

#### v2.1.7 - 28/02/2017 ####
- Limita o carregamento de pedidos dos setores em 'Meus Pedidos'
- Corrige erro ao enviar e-mail
- Corrige bug da seleção de pedidos após o carregamento de pedidos na tabela principal do SOF
- Adiciona campo "Descrição da Despesa" na edição de itens
- Retira janela "Relatório" de pedidos que apresentava uma tabela (desuso)
- Complementa o relatório de processos da recepção
- Corrige bug da impressão de pedidos env. ao ord. após a seleção prévia pelo SOF na tabela principal.
- Melhora o cadastro de usuário (permissão de repepção exclui as outras 3 e vice-versa)
- Nova interface para publicação de notícias (altera a página inicial após o login)
- Implementa status de carregameto dentro das janelas (carregamento dos meus pedidos)
- Mostra o número do processo nos itens de um rascunho
- Melhoria de funções e implementação de novas classes para a construção da interface
- Relatório das liberações orçamentárias, disponível para o SOF e para os demais setores.

#### v2.1.6 - 18/02/2017 ####
- Cadastro de itens de RP manualmente
- Atualização da biblioteca de geração de pdf (5.7 -> 6.1.0)
- Usuário genérico da Unidade de Apoio que todos os setores têm acesso
- Percentual no subrelatório de pedidos
- Relatório de Usuários cadastrados
- Relatório de pedidos disponível para os demais setores
- Melhoria no carregamento e atualização da tabela de pedidos do SOF
- - A tabela só irá atualizar o pedido que foi alterado, exceto se o botão "Salvar Alterações" nos detalhes do pedido for clicado
- Os pedidos carregados pelo usuário são mantidos a cada atualização da tabela, exceto no caso acima 
- Subrelatório por grupos dos pedidos (beta).

#### v2.1.5 - 12/02/2017 ####
- Os setores têm acesso aos usuários da Unidade de Apoio
- Nome do fornecedor nos meus pedidos
- Implementa funcionalidade para carregar apenas o necessário na página principal do SOF
- Corrige bug de ícone duplicado de cadastrar empenho
- Suporte para edição de seq_item_processo na edição de itens
- Adiciona plugin PACE para carregamento das páginas lte

#### v2.1.4 - 05/02/2017 ####
- Um usuário pode alterar apenas os pedidos feitos por ele, nos demais apenas pode imprimir
- Nome do usuário no cabeçalho do pedido impresso
- Cancelamento de liberação orçamentária (independente do tempo)
- Mostra o destino / origem nas liberações orçamentárias quando for transferência
- Marcar / desmarcar todos os pedidos da tabela principal da análise
- Permite relatórios com mais de um status selecionado
- Mostra o nome do usuário no pedido impresso
- O sof pode ter acesso à qualquer item para edição de certas colunas
