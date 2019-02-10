<?php
/**
 * File with form to edit an item.
 *
 * @author João Bolsson (joaovictorbolsson@gmail.com)
 * @since 2017, 05 Jun.
 */
?>
<form id="formEditRegItem">
    <input id="id" type="hidden" name="id" value="0"/>
    <input type="hidden" name="form" value="formEditRegItem"/>
    <input type="hidden" name="admin" value="1"/>
    <div class="modal-body">
        <table class="table">
            <tr>
                <td colspan="3">
                    <div class="form-group">
                        <label>
                            <input id="checkCancel" type="checkbox" name="cancelado">
                            Cancelado
                        </label>
                    </div>
                </td>
            </tr>
            <tr>
                <td colspan="3">
                    <div class="form-group">
                        <label>Complemento do Item</label>
                        <textarea class="form-control" id="complemento_item" name="complemento_item" required
                                  rows="5"></textarea>
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="form-group">
                        <label>Código Despesa</label>
                        <input class="form-control" id="cod_despesa" name="cod_despesa" type="text"
                               maxlength="15" required>
                    </div>
                </td>
                <td>
                    <div class="form-group">
                        <label>Código Reduzido</label>
                        <input class="form-control" id="cod_reduzido" name="cod_reduzido" type="text"
                               maxlength="20" required>
                    </div>
                </td>
                <td>
                    <div class="form-group">
                        <label>Ano Orçamento</label>
                        <input type="number" id="ano_orcamento" name="ano_orcamento" step="1" min="2000"
                               class="form-control">
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="form-group">
                        <label>Valor Unitário</label>
                        <input class="form-control" id="vl_unitario" name="vl_unitario" type="number"
                               step="0.0001" required>
                    </div>
                </td>
                <td>
                    <div class="form-group">
                        <label>Quantidade Contrato</label>
                        <input class="form-control" id="qt_contrato" name="qt_contrato" type="number"
                               required>
                    </div>
                </td>
                <td>
                    <div class="form-group">
                        <label>Valor Contrato</label>
                        <input class="form-control" id="vl_contrato" name="vl_contrato" type="number"
                               step="0.001" required>
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="form-group">
                        <label>Quantidade Utilizada</label>
                        <input class="form-control" id="qt_utilizado" name="qt_utilizado" type="number"
                               required>
                    </div>
                </td>
                <td>
                    <div class="form-group">
                        <label>Valor Utilizado</label>
                        <input class="form-control" id="vl_utilizado" name="vl_utilizado" type="number"
                               step="0.001" required>
                    </div>
                </td>
                <td>
                    <div class="form-group">
                        <label>Quantidade Saldo</label>
                        <input class="form-control" id="qt_saldo" name="qt_saldo" type="number" required>
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="form-group">
                        <label>Valor Saldo</label>
                        <input class="form-control" id="vl_saldo" name="vl_saldo" type="number"
                               step="0.001" required>
                    </div>
                </td>
                <td>
                    <div class="form-group">
                        <label>Descrição Despesa</label>
                        <input class="form-control" id="descr_despesa" name="descr_despesa" type="text"
                               maxlength="100" required>
                    </div>
                </td>
                <td>
                    <div class="form-group">
                        <label>Seq Item Processo</label>
                        <input class="form-control" id="seq_item_processo" name="seq_item_processo"
                               type="text" maxlength="20" required>
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="form-group">
                        <label>Id Item Processo</label>
                        <input type="number" id="id_item_processo" name="id_item_processo" step="1"
                               class="form-control">
                    </div>
                </td>
                <td>
                    <div class="form-group">
                        <label>Id Item Contrato</label>
                        <input type="number" id="id_item_contrato" name="id_item_contrato" step="1"
                               class="form-control">
                    </div>
                </td>
                <td>
                    <div class="form-group">
                        <label>Descrição Tipo Doc</label>
                        <input type="text" id="descr_tipo_doc" name="descr_tipo_doc" maxlength="80"
                               class="form-control">
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="form-group">
                        <label>Número do Contrato</label>
                        <input type="text" id="num_contrato" name="num_contrato" maxlength="15"
                               class="form-control">
                    </div>
                </td>
                <td>
                    <div class="form-group">
                        <label>Número do Processo</label>
                        <input type="text" id="num_processo" name="num_processo" maxlength="20"
                               class="form-control">
                    </div>
                </td>
                <td>
                    <div class="form-group">
                        <label>Descrição Mod Compra</label>
                        <input type="text" id="descr_mod_compra" name="descr_mod_compra" maxlength="50"
                               class="form-control">
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="form-group">
                        <label>Número da Licitação</label>
                        <input type="text" id="num_licitacao" name="num_licitacao" maxlength="15"
                               class="form-control">
                    </div>
                </td>
                <td>
                    <div class="form-group">
                        <label>Data Início</label>
                        <input type="text" id="dt_inicio" name="dt_inicio" maxlength="15"
                               class="form-control date">
                    </div>
                </td>
                <td>
                    <div class="form-group">
                        <label>Data Fim</label>
                        <input type="text" id="dt_fim" name="dt_fim" maxlength="15"
                               class="form-control date">
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="form-group">
                        <label>Data Geração</label>
                        <input type="text" id="dt_geracao" name="dt_geracao" maxlength="15"
                               class="form-control date">
                    </div>
                </td>
                <td>
                    <div class="form-group">
                        <label>CGC Fornecedor</label>
                        <input type="text" id="cgc_fornecedor" name="cgc_fornecedor" maxlength="20"
                               class="form-control">
                    </div>
                </td>
                <td>
                    <div class="form-group">
                        <label>Nome do Fornecedor</label>
                        <input type="text" id="nome_fornecedor" name="nome_fornecedor" maxlength="50"
                               class="form-control">
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="form-group">
                        <label>Nome da Unidade</label>
                        <input type="text" id="nome_unidade" name="nome_unidade" maxlength="100"
                               class="form-control">
                    </div>
                </td>
                <td>
                    <div class="form-group">
                        <label>Código Estruturado</label>
                        <input type="text" id="cod_estruturado" name="cod_estruturado" maxlength="20"
                               class="form-control">
                    </div>
                </td>
                <td>
                    <div class="form-group">
                        <label>Número do Extrato</label>
                        <input type="text" id="num_extrato" name="num_extrato" maxlength="20"
                               class="form-control">
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="form-group">
                        <label>Descrição</label>
                        <input type="text" id="descricao" name="descricao" maxlength="200"
                               class="form-control">
                    </div>
                </td>
                <td>
                    <div class="form-group">
                        <label>Id Extrato Contrato</label>
                        <input type="number" id="id_extrato_contr" name="id_extrato_contr" step="1"
                               class="form-control">
                    </div>
                </td>
                <td>
                    <div class="form-group">
                        <label>Id Unidade</label>
                        <input type="number" id="id_unidade" name="id_unidade" step="1" class="form-control">
                    </div>
                </td>
            </tr>
        </table>
    </div>
    <div class="modal-footer">
        <button class="btn btn-primary" type="submit" style="width: 100%;"><i class="fa fa-refresh"></i>&nbsp;Atualizar
            / Cadastrar
        </button>
    </div>
</form>
