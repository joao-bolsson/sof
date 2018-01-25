<?php
/**
 * Rows that will be placed in general sectors view.
 *
 * @author João Bolsson (joaovictorbolsson@gmail.com).
 * @since 2017, 25 May.
 */

$select_grupo = BuscaLTE::getOptionsGrupos($_SESSION["id_setor"]);
?>
<div class="row">
    <div class="col-xs-12">
        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title">Itens do Processo: <span id="numProc">--------------------</span></h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i
                                class="fa fa-minus"></i>
                    </button>
                </div>
            </div><!-- /.box-header -->
            <input id="searchProcesso" type="hidden">
            <div class="box-body">
                <table class="table table-bordered table-striped" id="tableProcessos">
                    <thead>
                    <tr>
                        <th></th>
                        <th>Fornecedor</th>
                        <th>Cod Reduzido</th>
                        <th>Qt Solicitada</th>
                        <th>Complemento</th>
                        <th style="display: none;"></th>
                        <th>Vl Unitário</th>
                        <th>Qt Saldo</th>
                        <th>Qt Utilizado</th>
                        <th>Vl Saldo</th>
                        <th>Vl Utilizado</th>
                        <th>Qt Contrato</th>
                    </tr>
                    </thead>
                    <tbody id="conteudoProcesso"></tbody>
                </table>
            </div><!-- ./box-body -->
        </div><!-- ./box -->
    </div> <!-- ./col-xs-12 -->
</div>
<div class="row">
    <div class="col-xs-12">
        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title">Pedido | SALDO <span
                            id="text_saldo_total">R$ <?= number_format($saldo_total, 3, ',', '.'); ?></span>
                </h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i
                                class="fa fa-minus"></i>
                    </button>
                </div>
            </div><!-- /.box-header -->
            <form action="../php/geral.php" method="POST">
                <input type="hidden" name="users" value="1">
                <input type="hidden" name="form" value="pedido">
                <input id="pedido" type="hidden" name="pedido" value="0">
                <div class="box-body">
                    <table class="table table-bordered table-striped">
                        <thead>
                        <th></th>
                        <th>NUM_PROCESSO</th>
                        <th>COD_REDUZIDO</th>
                        <th>COMPLEMENTO_ITEM</th>
                        <th>VL_UNITARIO</th>
                        <th>NOME_FORNECEDOR</th>
                        <th>NUM_LICITACAO</th>
                        <th>QT_SOLICITADA</th>
                        <th>VALOR</th>
                        </thead>
                        <tbody id="conteudoPedido"></tbody>
                    </table>
                    <table class="table table-bordered table-striped">
                        <tr>
                            <td>
                                <div class="form-group">
                                    <label>Total</label>
                                    <input class="form-control" id="total" name="total"
                                           style="font-size: 14pt;" type="text" disabled value="R$ 0">
                                </div>
                            </td>
                        </tr>
                        <input id="total_hidden" type="hidden" name="total_hidden" value="0">
                        <input id="saldo_total" type="hidden" name="saldo_total"
                               value="<?= $saldo_total ?>">
                    </table>
                    <div class="form-group">
                        <table class="table table-bordered table-striped">
                            <tr>
                                <?= BuscaLTE::getPrioridades(); ?>
                            </tr>
                        </table>
                    </div>
                    <div class="form-group">
                        <label>Observações</label>
                        <textarea class="form-control" id="obs" name="obs" rows="1" required></textarea>
                    </div>
                    <h2>Licitação</h2>
                    <table class="table table-bordered table-striped">
                        <?= BuscaLTE::getOptionsLicitacao(4); ?>
                    </table>
                    <table class="table table-bordered table-striped">
                        <input id="idLic" type="hidden" name="idLic" value="0">
                        <tr>
                            <td>
                                <div class="form-group">
                                    <label>Número</label>
                                    <input class="form-control" id="infoLic" name="infoLic" required/>
                                </div>
                            </td>
                            <td>
                                <div class="form-group">
                                    <label>UASG</label>
                                    <input class="form-control" id="uasg" name="uasg" disabled/>
                                </div>
                            </td>
                            <td>
                                <div id="divProcOri" class="form-group">
                                    <label>Processo Original</label>
                                    <input class="form-control" id="procOri" name="procOri" disabled/>
                                </div>
                            </td>
                            <td>
                                <div class="form-group">
                                    <input type="radio" name="geraContrato" id="gera" class="minimal"
                                           value="1">
                                    Gera Contrato
                                </div>
                                <div class="form-group">
                                    <input type="radio" name="geraContrato" id="ngera" class="minimal"
                                           value="0">
                                    Não Gera Contrato
                                </div>
                            </td>
                        </tr>
                    </table>
                    <?php if (strlen($select_grupo) > 0): ?>
                        <h2>Grupo</h2>
                        <div class="form-group">
                            <label>Selecione o grupo</label>
                            <select id="grupo" class="form-control select2" name="grupo" required>
                                <?= $select_grupo ?>
                            </select>
                        </div>
                    <?php endif ?>
                    <div class="form-group">
                        <input class="minimal" id="checkPedContr" name="pedidoContrato" type="checkbox">
                        Pedido de Contrato
                    </div>
                    <table class="table table-bordered table-striped">
                        <tr>
                            <?= BuscaLTE::getOptionsContrato(); ?>
                        </tr>
                    </table>
                    <div class="form-group">
                        <label>SIAFI</label>
                        <input class="form-control" id="siafi" name="siafi" type="text">
                    </div>
                </div><!-- ./card-inner -->
                <div class="box-footer">
                    <button id="btnLimpa" class="btn btn-default" type="button" style="width: 49%;"
                            onclick="limpaTelaSolic();"><i class="fa fa-close"></i>&nbsp;Limpar
                    </button>
                    <button class="btn btn-primary" type="submit" style="width: 50%;"><i
                                class="fa fa-send"></i>&nbsp;Enviar Pedido / Salvar Rascunho
                    </button>
                </div>
            </form>
        </div><!-- ./card-main -->
    </div> <!-- ./card -->
</div>