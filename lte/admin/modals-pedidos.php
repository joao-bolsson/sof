<?php
/**
 * File with modals about requests.
 *
 * @author João Bolsson (joaovictorbolsson@gmail.com).
 * @since 2017, 23 May.
 */

?>
<div aria-hidden="true" class="modal fade" id="cadEmpenho" role="dialog" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Cadastrar Empenho</h4>
            </div>
            <form id="formEnviaEmpenho">
                <input type="hidden" name="form" value="enviaEmpenho">
                <input type="hidden" name="admin" value="1">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Pedido</label>
                        <input class="form-control" name="id_pedido" type="number"
                               required disabled/>
                    </div>
                    <div class="form-group">
                        <label>Empenho</label>
                        <input class="form-control" name="empenho" required/>
                    </div>
                    <div class="form-group">
                        <label>Data</label>
                        <input class="form-control date" name="data" required/>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary" type="submit" style="width: 100%;"><i
                                class="fa fa-send"></i>Cadastrar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<div aria-hidden="true" class="modal fade" id="cadFontes" role="dialog" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Cadastrar Fontes</h4>
            </div>
            <form id="formEnviaFontes">
                <input type="hidden" name="admin" value="1">
                <input type="hidden" name="form" value="enviaFontes">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Pedido</label>
                        <input class="form-control" name="id_pedido" type="number"
                               required disabled>
                    </div>
                    <div class="form-group">
                        <label>Fonte de Recurso</label>
                        <input class="form-control" name="fonte" required/>
                    </div>
                    <div class="form-group">
                        <label>PTRES</label>
                        <input class="form-control" name="ptres" required/>
                    </div>
                    <div class="form-group">
                        <label>Plano Interno</label>
                        <input class="form-control" name="plano" required/>
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
<?php include_once __DIR__ . "/../report-modals/comum/modal-relLibOrc.php" ?>
<?php include_once __DIR__ . "/../report-modals/comum/modal-relPedidos.php" ?>
<?php include_once __DIR__ . "/../report-modals/sof/relSIAFI.php" ?>
<div class="modal fade" id="importItens" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span>&times;</span>
                </button>
                <h4 class="modal-title">Importar Itens</h4>
            </div>
            <form enctype="multipart/form-data" action="../php/geral.php" method="post">
                <input type="hidden" name="admin" value="1">
                <input type="hidden" name="form" value="importItens">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="file">Arquivo</label>
                        <input type="file" id="file" name="file" required>

                        <p class="help-block">Tamanho máximo: <?= MAX_UPLOAD_SIZE ?> MB</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary" type="submit" style="width: 100%;"><i
                                class="fa fa-cloud-upload"></i>&nbsp;Enviar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade" id="infoItem" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Informações do Item</h4>
            </div>
            <?php include_once __DIR__ . "/../util/formEditRegItem.php"; ?>
        </div>
    </div>
</div>
