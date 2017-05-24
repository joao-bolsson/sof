<div aria-hidden="true" class="modal fade" id="transferencia" role="dialog" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Nova Transferência</h4>
            </div>
            <form id="formTransferencia" action="../php/geral.php" method="POST">
                <input type="hidden" name="admin" value="1">
                <input type="hidden" name="form" value="transfereSaldo">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Setor Origem</label>
                        <select class="form-control" name="ori" required
                                onchange="getSaldoOri();">
                            <?= BuscaLTE::getOptionsSetores(); ?>
                        </select>
                        <p id="saldoDispOri" style="font-weight: bold;"></p>
                    </div>
                    <div class="form-group">
                        <label>Setor Destino</label>
                        <select class="form-control" name="dest" required>
                            <?= BuscaLTE::getOptionsSetores(); ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Valor</label>
                        <input class="form-control" name="valor" type="number" step="0.001"
                               required min="0.001">
                    </div>
                    <div class="form-group">
                        <label>Justificativa</label>
                        <select class="form-control select2" name="just"
                                data-placeholder="Selecione o motivo da transferência" required>
                            <?= BuscaLTE::getJustifies(); ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <button class="btn btn-primary" type="button" onclick="abreModal('#regJustify')"><i
                                    class="fa fa-plus"></i> Cadastrar Justificativa
                        </button>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary" type="submit" style="width: 100%;"><i
                                class="fa fa-refresh"></i>&nbsp;Liberar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade" id="regJustify" role="dialog">
    <div class="modal-dialog" style="width: 40%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Cadastrar Justificativa</h4>
            </div>
            <form action="../php/geral.php" method="post">
                <input type="hidden" name="admin" value="1">
                <input type="hidden" name="form" value="regJustify">
                <div class="modal-body">
                    <small class="label bg-gray"><i class="fa fa-exclamation-circle "></i> A página será
                        recarregada em seguida
                    </small>
                    <div class="form-group">
                        <label>Justificativa</label>
                        <textarea class="form-control" rows="2" name="justificativa" maxlength="40"
                                  placeholder="Digite a justificativa. Máx. 40 caracteres" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary" type="submit" style="width: 100%;"><i
                                class="fa fa-send"></i>&nbsp;Cadastrar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<div aria-hidden="true" class="modal fade" id="freeSaldos" role="dialog" tabindex="-1">
    <div class="modal-dialog" style="width: 40%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Liberar Saldo</h4>
            </div>
            <div class="modal-body">
                <form action="javascript:liberaSaldo();" method="post">
                    <div class="modal-inner">
                        <div class="form-group">
                            <label>Setor</label>
                            <select id="setor" class="form-control" name="setor" required>
                                <?= BuscaLTE::getOptionsSetores(); ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Valor</label>
                            <input id="valorFree" type="number" class="form-control" placeholder="Valor"
                                   name="valor" step="0.001" required min="0.001">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary" type="submit" style="width: 100%;"><i
                                    class="fa fa-refresh"></i>&nbsp;Liberar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<div aria-hidden="true" class="modal fade" id="listLancamentos" role="dialog" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Liberações Orçamentárias</h4>
            </div>
            <div class="modal-body">
                <div class="form-group form-group-label">
                    <label>Setor</label>
                    <select id="selectSetor" class="form-control" onchange="changeSetor();">
                        <option value="-1">Nenhum</option>
                        <option value="0">Todos</option>
                        <?= BuscaLTE::getOptionsSetores(); ?>
                    </select>
                </div>
                <table class="table table-bordered table-striped">
                    <tbody id="rowDataSaldo">
                    <tr>
                        <td>Dados do setor selecionado</td>
                        <td id="totOut">Total de Saídas: R$ 0.000</td>
                        <td id="totIn">Total de Entradas: R$ 0.000</td>
                    </tr>
                    </tbody>
                </table>
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
        </div>
    </div>
</div>