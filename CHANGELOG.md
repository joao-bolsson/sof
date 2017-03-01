## CHANGELOG ##

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
