<?php
/**
 * @author João Bolsson (joaovictorbolsson@gmail.com).
 * @since 2017, 10 Nov.
 */
?>
<div aria-hidden="true" class="modal fade" id="relSIAFI" role="dialog" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Relatório SIAFI (em construção)</h4>
            </div>
            <form action="../admin/printRelatorio.php" method="post" target="_blank">
                <input type="hidden" name="tipo" value="siafi"/>
                <input type="hidden" name="relatorio" value="1"/>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Setor</label>
                        <select id="selectSetorRelSIAFI" class="form-control" name="setor" required>
                            <?= BuscaLTE::getOptionsSetores(); ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Fonte</label>
                        <select id="selectFonteSIAFI" class="form-control" name="fonte" required>
                        </select>
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
