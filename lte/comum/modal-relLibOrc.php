<?php
/**
 * @author João Bolsson (joaovictorbolsson@gmail.com).
 * @since 2017, 03 Nov.
 */
?>
<div aria-hidden="true" class="modal fade" id="relLibOrc" role="dialog" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Relatório de Liberações Orçamentárias</h4>
            </div>
            <form action="../admin/printRelatorio.php" method="post" target="_blank">
                <input type="hidden" name="tipo" value="liberacoes"/>
                <input type="hidden" name="relatorio" value="1"/>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Setor</label>
                        <select class="form-control" name="setor" required>
                            <?php
                            if ($_SESSION['id_setor'] != 2) {
                                echo "<option value='" . $_SESSION['id_setor'] . "'>" . ARRAY_SETORES[$_SESSION['id_setor']] . "</option>";
                            } else {
                                echo "<option value=\"0\">Todos</option>" . BuscaLTE::getOptionsSetores();
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Categoria</label>
                        <select class="form-control select2" name="categoria[]" multiple="multiple"
                                data-placeholder="Selecione" required>
                            <?= BuscaLTE::getOptionsCategoria(); ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Data Início</label>
                        <div class="input-group">
                            <div class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                            </div>
                            <input type="text" class="form-control date" name="dataI" required
                                   data-inputmask="'alias': 'dd/mm/yyyy'" data-mask>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Data Fim</label>
                        <div class="input-group">
                            <div class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                            </div>
                            <input type="text" class="form-control date" name="dataF" required
                                   data-inputmask="'alias': 'dd/mm/yyyy'" data-mask>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary" type="submit" style="width: 100%;"><i
                                class="fa fa-refresh"></i>&nbsp;Gerar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
