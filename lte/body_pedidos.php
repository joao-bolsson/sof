<div id="rowAltPed" class="row" style="display: none;">
    <div class="col-xs-12">
        <div class="box">
            <div class="box-header">
                <h3 class="box-title">Solicitações de Alteração de Pedido</h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i
                                class="fa fa-minus"></i>
                    </button>
                </div>
            </div><!-- /.box-header -->
            <div class="box-body">
                <table class="table">
                    <tr>
                        <td>
                            <div class="form-group">
                                <label>
                                    <input id="stAltAbertos" type="radio" name="stAlt" class="minimal"
                                           value="2" checked/>
                                    Abertos
                                </label>
                            </div>
                        </td>
                        <td>
                            <div class="form-group">
                                <label>
                                    <input id="stAltAprovados" type="radio" name="stAlt" class="minimal"
                                           value="1"/>
                                    Aprovados
                                </label>
                            </div>
                        </td>
                        <td>
                            <div class="form-group">
                                <label>
                                    <input id="stAltReprovado" type="radio" name="stAlt" class="minimal"
                                           value="0"/>
                                    Reprovados
                                </label>
                            </div>
                        </td>
                    </tr>
                </table>
                <table id="tableSolicAltPedido" class="table table-bordered table-striped">
                    <thead>
                    <tr>
                        <th>Opções</th>
                        <th>Pedido</th>
                        <th>Setor</th>
                        <th>Data Solic</th>
                        <th>Data Análise</th>
                        <th>Justificativa</th>
                        <th>Status</th>
                    </tr>
                    </thead>
                    <tbody id="contSolicAltPedido"></tbody>
                </table>
            </div><!-- /.box-body -->
        </div><!-- /.box -->
    </div><!-- /.col -->
</div><!-- /.row -->
<div id="rowPedidos" class="row">
    <div class="col-xs-12">
        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title">Pedidos</h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i
                                class="fa fa-minus"></i>
                    </button>
                </div>
            </div><!-- /.box-header -->
            <div class="box-body">
                <div class="margin">
                    <button id="btnPrintCheck" class="btn btn-primary" type="button"
                            onclick="printChecks()" disabled><i class="fa fa-print"></i>&nbsp;Imprimir
                    </button>
                    <button id="btnAprovGeren" class="btn btn-primary" type="button"
                            onclick="aprovGerencia()" disabled><i class="fa fa-check-circle"></i>&nbsp;Aprovado
                        pela Gerência
                    </button>
                    <button class="btn btn-primary" type="button"
                            onclick="abreModal('#loadMoreCustom');" data-toggle="tooltip"
                            title="Carregar mais pedidos"><i class="fa fa-cloud-download"></i>&nbsp;Carregar
                    </button>
                </div>
                <table id="tableSolicitacoes" class="table table-bordered table-striped">
                    <thead>
                    <tr>
                        <th>
                            <div class=form-group>
                                <input type="checkbox" name="checkPedRel" id="checkPedRel" value="1">
                            </div>
                        </th>
                        <th>Opções</th>
                        <th>Pedido</th>
                        <th>Setor</th>
                        <th>Data</th>
                        <th>Prioridade</th>
                        <th>Status</th>
                        <th>Valor</th>
                        <th>Empenho</th>
                        <th>Fornecedor</th>
                    </tr>
                    </thead>
                    <tbody id="conteudoSolicitacoes"></tbody>
                </table>
            </div><!-- /.box-body -->
            <div id="overlayLoad" class="overlay" style="display: none;">
                <i class="fa fa-refresh fa-spin"></i>
            </div>
        </div><!-- /.box -->
    </div><!-- /.col -->
</div><!-- /.row -->
<div id="rowDetPedido" class="row" style="display: none;">
    <div class="col-xs-12">
        <div class="box">
            <div class="box-header">
                <h3 class="box-title">Detalhes do Pedido: <span id="detPedId"></span>&nbsp;SETOR: <span
                            id="nomeSetorDet"></span>&nbsp;SALDO DO SOLICITANTE <span
                            id="text_saldo_total">R$ 0.000</span></h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i
                                class="fa fa-minus"></i>
                    </button>
                </div>
            </div><!-- /.box-header -->
            <form id="formPedido" action="../php/geral.php" method="POST">
                <input type="hidden" name="admin" value="1"/>
                <input id="form" type="hidden" name="form" value="" required/>
                <input id="id_pedido" type="hidden" name="id_pedido" value="0"/>
                <input id="id_setor" type="hidden" name="id_setor" value="0"/>
                <div class="box-body">
                    <div id="divTableItens">
                        <table id="tableItensPedido" class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th>Ferramentas</th>
                                <th>COD_DESPESA</th>
                                <th>DESCRICAO_DESPESA</th>
                                <th>NUM_EXTRATO</th>
                                <th>NUM_CONTRATO</th>
                                <th>NUM_PROCESSO</th>
                                <th>DESCR_MOD_COMPRA</th>
                                <th>NUM_LICITACAO</th>
                                <th>DT_INICIO</th>
                                <th>DT_FIM</th>
                                <th>CGC_FORNECEDOR</th>
                                <th>NOME_FORNECEDOR</th>
                                <th>COD_REDUZIDO</th>
                                <th>SEQ_ITEM_PROCESSO</th>
                                <th>DESCRICAO</th>
                                <th>VL_UNITARIO</th>
                                <th>QT_CONTRATO</th>
                                <th>VL_CONTRATO</th>
                                <th>QT_UTILIZADO</th>
                                <th>VL_UTILIZADO</th>
                                <th>QT_SALDO</th>
                                <th>VL_SALDO</th>
                                <th>QT_SOLICITADA</th>
                                <th>VALOR</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody id="conteudoPedido"></tbody>
                        </table>
                    </div>
                    <div class="form-group">
                        <label>Observações</label>
                        <textarea class="form-control" id="obs" name="obs" rows="2" disabled></textarea>
                    </div>
                    <input id="total_hidden" type="hidden" name="total_hidden" value="0">
                    <input id="saldo_total" type="hidden" name="saldo_total" value="0.000">
                    <input id="prioridade" type="hidden" name="prioridade" value="0">
                    <table class="table table-bordered table-striped">
                        <?= BuscaLTE::getStatus(4) ?>
                    </table>
                    <div class="form-group">
                        <label>Comentário</label>
                        <textarea class="form-control" rows="2" id="comentario"
                                  name="comentario"></textarea>
                    </div>
                    <small class="label bg-gray">Essa opção só irá ser considerada se o pedido for
                        marcado como Reprovado
                    </small>
                    <div class="form-group">
                        <label>
                            <input id="checkExcluir" type="checkbox" class="minimal" name="excluir">
                            Excluir pedido
                        </label>
                    </div>
                </div><!-- ./box-body -->
                <div class="box-footer">
                    <div class="btn-group" style="width: 100%;">
                        <button id="btnLimpa" class="btn btn-default" type="button" style="width: 49%;"
                                onclick="limpaTela();"><i class="fa fa-undo"></i>&nbsp;Limpar / Esconder
                        </button>
                        <button class="btn btn-primary" type="submit" style="width: 50%;"><i
                                    class="fa fa-check"></i>&nbsp;Salvar Alterações
                        </button>
                    </div>
                </div>
            </form>
            <div id="overlayLoadDetPed" class="overlay" style="display: none;">
                <i class="fa fa-refresh fa-spin"></i>
            </div>
        </div><!-- ./box -->
    </div> <!-- ./col-xs-12 -->
</div> <!-- ./row -->
