<?php
/**
 * File with contents about balances.
 *
 * @author João Bolsson (joaovictorbolsson@gmail.com).
 * @since 2017, 23 May.
 */

?>
<div id="rowSolicAdi" class="row" style="display: none;">
    <div class="col-xs-12">
        <div class="box">
            <div class="box-header">
                <h3 class="box-title">Solicitações de Adiantamento</h3>
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
                                    <input id="stabertos" type="radio" name="stadi" class="minimal"
                                           value="2" checked/>
                                    Abertos
                                </label>
                            </div>
                        </td>
                        <td>
                            <div class="form-group">
                                <label>
                                    <input id="staprovados" type="radio" name="stadi" class="minimal"
                                           value="1"/>
                                    Aprovados
                                </label>
                            </div>
                        </td>
                        <td>
                            <div class="form-group">
                                <label>
                                    <input id="streprovado" type="radio" name="stadi" class="minimal"
                                           value="0"/>
                                    Reprovados
                                </label>
                            </div>
                        </td>
                    </tr>
                </table>
                <table id="tableSolicitacoesAdiantamento" class="table table-bordered table-striped">
                    <thead>
                    <tr>
                        <th>Opções</th>
                        <th>Setor</th>
                        <th>Data Solic</th>
                        <th>Data Análise</th>
                        <th>Valor Adiantado</th>
                        <th>Justificativa</th>
                        <th>Status</th>
                    </tr>
                    </thead>
                    <tbody id="conteudoSolicitacoesAdiantamento"></tbody>
                </table>
            </div><!-- /.box-body -->
        </div><!-- /.box -->
    </div><!-- /.col -->
</div><!-- /.row -->
