<?php
/**
 * Modals that will be placed in general sectors view.
 *
 * @author João Bolsson (joaovictorbolsson@gmail.com).
 * @since 2017, 25 May.
 */
?>
<?php include_once __DIR__ . "/../comum/modal-relLibOrc.php" ?>
<?php include_once __DIR__ . "/../comum/modal-relPedidos.php" ?>
<div aria-hidden="true" class="modal fade" id="altUser" role="dialog" tabindex="-1">
    <div class="modal-dialog" style="width: 40%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Alterar Usuário</h4>
            </div>
            <form action="../php/geral.php" method="POST">
                <input type="hidden" name="form" value="altUser"/>
                <input type="hidden" name="users" value="1"/>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Usuário</label>
                        <select id="userA" class="form-control" name="user" required>
                            <?= BuscaLTE::getUsers(true); ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary" type="submit" style="width: 100%;"><i class="fa fa-refresh"></i>&nbsp;Trocar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<div aria-hidden="true" class="modal fade" id="listAdiantamentos" role="dialog" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Solicitações de Adiantamentos</h4>
            </div>
            <div class="modal-body">
                <table class="table table-bordered table-striped" id="tableListAdiantamentos">
                    <thead>
                    <tr>
                        <th>DATA_SOLICITACAO</th>
                        <th>DATA_ANALISE</th>
                        <th>VALOR_ADIANTADO</th>
                        <th>JUSTIFICATIVA</th>
                        <th>STATUS</th>
                    </tr>
                    </thead>
                    <tbody id="tbodyListAdiantamentos"></tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<div aria-hidden="true" class="modal fade" id="adiantamento" role="dialog" tabindex="-1" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Solicitar Adiantamento</h4>
            </div>
            <form action="../php/geral.php" method="POST">
                <input type="hidden" name="form" value="adiantamento"/>
                <input type="hidden" name="users" value="1"/>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Valor</label>
                        <input class="form-control" id="valor_adiantamento" name="valor_adiantamento" type="number"
                               step="0.001" min="0.001" required>
                    </div>
                    <div class="form-group">
                        <label>Justificativa</label>
                        <textarea class="form-control" id="justificativa" name="justificativa" rows="2"
                                  required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary" type="submit" style="width: 100%;"><i class="fa fa-send"></i>&nbsp;Enviar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<div aria-hidden="true" class="modal fade" id="listLancamentos" role="dialog" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Saldos</h4>
            </div>
            <div class="modal-body">
                <table class="table table-bordered table-striped">
                    <tr>
                        <td>Saldo Disponível</td>
                        <td>R$ <?= number_format($saldo_total, 3, ',', '.') ?></td>
                    </tr>
                    <?= $pedidos_em_analise ?>
                </table>
                <small class="label bg-gray">Essa tabela vai exibir no máximo <?= LIMIT_MAX ?> linhas. Você pode
                    gerar relatório das Liberações Orçamentárias do seu Setor ;)
                </small>
                <table id="tableListLancamentos" class="table table-bordered table-striped">
                    <thead>
                    <tr>
                        <th></th>
                        <th>Setor</th>
                        <th>Data</th>
                        <th>Valor</th>
                        <th>Categoria</th>
                        <th>Origem / Destino</th>
                    </tr>
                    </thead>
                    <tbody id="tbodyListLancamentos"></tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary" type="button" onclick="abreModal('#adiantamento');"
                        style="width: 100%;"><i class="fa fa-dollar"></i>&nbsp;Solicitar Adiantamento
                </button>
            </div>
        </div>
    </div>
</div>
<div aria-hidden="true" class="modal fade" id="listSolicAltPedidos" role="dialog" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Solicitações de Alteração de Pedidos</h4>
            </div>
            <div class="modal-body">
                <table id="tableSolicAltPedido" class="table table-bordered table-striped">
                    <thead>
                    <tr>
                        <th>NUM_PEDIDO</th>
                        <th>DATA_SOLICITACAO</th>
                        <th>DATA_ANALISE</th>
                        <th>JUSTIFICATIVA</th>
                        <th>STATUS</th>
                    </tr>
                    </thead>
                    <tbody id="tbodySolicAltPedido"></tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<div aria-hidden="true" class="modal fade" id="alt_pedido" role="dialog" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Solicitar Alteração de Pedido</h4>
            </div>
            <form action="javascript:formEnvia();" method="POST">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Pedido</label>
                        <input class="form-control" id="id_pedido_alt" name="id_pedido_alt" type="number" required
                               disabled>
                    </div>
                    <div class="form-group">
                        <label>Justificativa</label>
                        <textarea class="form-control" id="justificativa_alt_ped" name="justificativa_alt_ped"
                                  rows="2" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary" type="submit" style="width: 100%;"><i class="fa fa-send"></i>&nbsp;Enviar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<div aria-hidden="true" class="modal fade" id="loadMoreCustom" role="dialog" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Carregar Pedidos</h4>
            </div>
            <form action="javascript:loadMoreRequests();" method="POST">
                <div class="modal-body">
                    <small class="label bg-gray">Carrega todos os pedidos entre Limite 1 e Limite 2.</small>
                    <div class="form-group">
                        <label>Limite 1</label>
                        <input type="number" class="form-control" id="limit1" name="limit1" step="1" min="0"
                               required>
                    </div>
                    <div class="form-group">
                        <label>Limite 2</label>
                        <input type="number" class="form-control" id="limit2" name="limit2" step="1" min="0"
                               required>
                    </div>
                    <small class="label bg-gray">Por motivos de segurança, serão retornados no
                        máximo <?= LIMIT_MAX ?> resultados nesta consulta. ;)
                    </small>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary" type="submit" style="width: 100%;"><i
                                class="fa fa-cloud-download"></i>&nbsp;Carregar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<div aria-hidden="true" class="modal fade" id="listPedidos" role="dialog" tabindex="-1">
    <div class="modal-dialog" style="width: 90%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Meus Pedidos</h4>
            </div>
            <div class="modal-body">
                <div id="overlayLoad" class="overlay" style="display: none;">
                    <i class="fa fa-refresh fa-spin"></i>
                </div>
                <div class="margin">
                    <button class="btn btn-primary" type="button" onclick="abreModal('#loadMoreCustom');"
                            data-toggle="tooltip" title="Carregar mais pedidos"><i class="fa fa-cloud-download"></i>&nbsp;Carregar
                    </button>
                </div>
                <small class="label bg-gray">Essa tabela vai exibir no máximo <?= LIMIT_MAX ?> linhas. Você pode
                    gerar relatório dos Pedidos do seu Setor ;)
                </small>
                <table id="tableListPedidos" class="table table-bordered table-striped">
                    <thead>
                    <tr>
                        <th>NUM</th>
                        <th>Data de Envio</th>
                        <th>Prioridade</th>
                        <th>Status</th>
                        <th>SIAFI</th>
                        <th>Valor</th>
                        <th>Fornecedor</th>
                        <th>Opções</th>
                    </tr>
                    </thead>
                    <tbody id="tbodyListPedidos"></tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<div aria-hidden="true" class="modal fade" id="listRascunhos" role="dialog" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Rascunhos</h4>
            </div>
            <div class="modal-body">
                <small class="label bg-gray">Essa tabela vai exibir no máximo <?= LIMIT_MAX ?> linhas. Para exibir
                    alguma linha que não está sendo mostrada, é necessário excluir algum Rascunho. ;)
                </small>
                <table id="tableListRascunhos" class="table table-bordered table-striped">
                    <thead>
                    <tr>
                        <th>Num Pedido</th>
                        <th>Status</th>
                        <th>Última modificação</th>
                        <th>Valor</th>
                        <th>Opções</th>
                    </tr>
                    </thead>
                    <tbody id="tbodyListRascunhos"></tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<div id="viewCompl" class="modal fade" tabindex="-1" role="dialog" data-backdrop="static">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Informação</h4>
            </div>
            <div id="complementoItem" class="modal-body"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal">Fechar</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
