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
                <table id="tableRegisters" class="table table-bordered table-striped">
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
<div class="modal fade" id="atestado" role="dialog" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Cadastrar Atestado</h4>
            </div>
            <form id="formAtestado">
                <input type="hidden" name="admin" value="1"/>
                <input type="hidden" name="form" value="atestado"/>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Usuário</label>
                        <select class="form-control" name="user" required>
                            <?= BuscaLTE::getUsers(false, $_SESSION['id_setor']); ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Dia</label>
                        <input class="form-control date" name="dia" type="text" required>
                    </div>
                    <div class="form-group">
                        <label>Horas</label>
                        <input class="form-control" name="horas" type="number" step="1" min="0" value="4" required>
                    </div>
                    <div class="form-group">
                        <label>Justificativa</label>
                        <textarea class="form-control" name="justificativa" rows="2" maxlength="50"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary" type="submit" style="width: 100%;"><i
                                class="fa fa-refresh"></i>&nbsp;Enviar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
