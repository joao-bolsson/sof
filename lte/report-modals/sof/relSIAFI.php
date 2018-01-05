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
                <h4 class="modal-title">Relatório SIAFI</h4>
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
                        <select id="selectFonteSIAFI" class="form-control select2" name="fonte[]" multiple="multiple"
                                data-placeholder="Selecione" required>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Processo</label>
                        <select class="form-control select2" multiple="multiple" data-placeholder="Selecione"
                                name="num_processo[]" required>
                            <option value="Todos">Todos</option>
                            <?= BuscaLTE::getOptionsProcessos(); ?>
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
