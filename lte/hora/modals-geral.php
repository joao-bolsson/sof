<div class="modal fade" id="relRegister" role="dialog" tabindex="-1">
    <div class="modal-dialog" style="width: 40%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Relatório</h4>
            </div>
            <form action="../admin/printRelatorio.php" method="post" target="_blank">
                <input type="hidden" name="relatorio" value="1"/>
                <input type="hidden" name="tipo" value="hora"/>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Usuário</label>
                        <select class="form-control" name="user" required>
                            <?= BuscaLTE::getUsers(false, $_SESSION['id_setor']); ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Período:</label>
                        <div class="input-group">
                            <div class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                            </div>
                            <input type="text" class="form-control pull-right" id="reservation" name="periodo"
                                   required>
                        </div>
                        <!-- /.input group -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary" type="submit" style="width: 100%;"><i class="fa fa-print"></i>&nbsp;Gerar
                        Relatório
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
