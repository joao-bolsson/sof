<div class="modal fade" id="registerAdmin" role="dialog" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Ferramenta Administrativa</h4>
            </div>
            <div class="modal-body">
                <small class="label bg-gray">Essa tabela exibe os últimos <?= LIMIT_LOGS ?> registros no sistema
                    ;)
                </small>
                <div id="overlayLoadAdmin" class="overlay" style="display: none;">
                    <i class="fa fa-refresh fa-spin"></i>
                </div>
                <table class="table table-bordered table-striped">
                    <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Entrada</th>
                        <th>Saída</th>
                        <th>Opções</th>
                    </tr>
                    </thead>
                    <tbody id="tbodyAdminTool"></tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="editLog" role="dialog" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Edição</h4>
            </div>
            <form action="../php/geral.php" method="post">
                <input type="hidden" name="admin" value="1"/>
                <input type="hidden" name="form" value="editLog"/>
                <input id="idLog" type="hidden" name="idLog" value="0"/>
                <div class="modal-body">
                    <small class="label bg-gray"><i class="fa fa-exclamation-triangle"></i> IMPORTANTE: Altere
                        apenas as unidades, mantenha o formato ;)
                    </small>
                    <div class="form-group">
                        <label>Nome</label>
                        <input class="form-control" id="nomeEdit" name="nome" type="text" value="" disabled
                               required>
                    </div>
                    <div class="form-group">
                        <label>Entrada</label>
                        <input class="form-control" id="entradaEdit" name="entrada" type="text" value=""
                               required>
                    </div>
                    <div class="form-group">
                        <label>Saída</label>
                        <input class="form-control" id="saidaEdit" name="saida" type="text" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary" type="submit" style="width: 100%;"><i
                                class="fa fa-refresh"></i>&nbsp;Atualizar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
