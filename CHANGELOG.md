## CHANGELOG ##

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
