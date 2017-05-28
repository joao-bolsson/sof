<?php
/**
 * Contains all the modals used by users with reception permission.
 *
 * @author João Bolsson (joaovictorbolsson@gmail.com)
 * @since 2017, 25 Apr.
 */
?>
<div class="modal fade" id="relatorio" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">Gerar Relatório</h4>
            </div>
            <form id="formRelatorioRecepcao">
                <input type="hidden" name="admin" value="1">
                <input type="hidden" name="form" value="relatorioProcessos">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Tipo</label>
                        <select class="form-control" name="tipo" required>
                            <option value="0">Todos</option>
                            <?= BuscaLTE::getTiposProcessos() ?>
                        </select>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary" type="submit" style="width: 100%;"><i class="fa fa-print"></i>&nbsp;Gerar
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade" id="newTypeProcess" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">Novo Tipo de Processo</h4>
            </div>
            <form action="../php/geral.php" method="post">
                <input type="hidden" name="admin" value="1"/>
                <input type="hidden" name="form" value="newTypeProcess"/>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Nome</label>
                        <input class="form-control" id="newType" name="newType" type="text" required>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary" type="submit" style="width: 100%;"><i class="fa fa-refresh"></i>&nbsp;Cadastrar
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<div aria-hidden="true" class="modal fade" id="addProcesso" role="dialog" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">Processo</h4>
            </div>
            <form id="formProcesso" action="javascript:updateProcesso();" method="post">
                <input id="id_processo" type="hidden" value="0"/>
                <div class="modal-body">
                    <table class="table">
                        <tbody>
                        <tr>
                            <td>
                                <div class="form-group">
                                    <label>Processo</label>
                                    <input class="form-control" id="num_processo" name="num_processo" type="text"
                                           required>
                                </div>
                            </td>
                            <td>
                                <div class="form-group">
                                    <label>Tipo</label>
                                    <select id="tipo" class="form-control" name="tipo" required>
                                        <?= BuscaLTE::getTiposProcessos() ?>
                                    </select>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="form-group">
                                    <label>Estante</label>
                                    <input class="form-control" id="estante" name="estante" type="text" required>
                                </div>
                            </td>
                            <td>
                                <div class="form-group">
                                    <label>Prateleira</label>
                                    <input class="form-control" id="prateleira" name="prateleira" type="text" required>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="form-group">
                                    <label>Entrada (dd/mm/yyyy)</label>
                                    <input class="form-control date" id="entrada" name="entrada" type="text" required
                                           maxlength="10">
                                </div>
                            </td>
                            <td>
                                <div class="form-group">
                                    <label>Saída (dd/mm/yyyy)</label>
                                    <input class="form-control date" id="saida" name="saida" type="text" maxlength="10">
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="form-group">
                                    <label>Responsável</label>
                                    <input class="form-control" id="responsavel" name="responsavel" type="text">
                                </div>
                            </td>
                            <td>
                                <div class="form-group">
                                    <label>Retorno (dd/mm/yyyy)</label>
                                    <input class="form-control date" id="retorno" name="retorno" type="text"
                                           maxlength="10">
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="form-group">
                                    <label>Observação</label>
                                    <textarea class="form-control" id="obs" name="obs" rows="2"></textarea>
                                </div>
                            </td>
                            <td>
                                <div class="form-group">
                                    <label>Vigência (dd/mm/yyyy)</label>
                                    <input class="form-control date" id="vigencia" name="vigencia" type="text"
                                           maxlength="10">
                                </div>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary" type="submit" style="width: 100%;"><i class="fa fa-send"></i>&nbsp;Enviar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
