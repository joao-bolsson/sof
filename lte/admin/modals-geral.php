<?php
/**
 * File with general modals. (No need special permissions)
 *
 * @author João Bolsson (joaovictorbolsson@gmail.com).
 * @since 2017, 23 May.
 */

?>

<div aria-hidden="true" class="modal fade" id="loadMoreCustom" role="dialog" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Carregar Pedidos</h4>
            </div>
            <form action="javascript:loadMore();" method="POST">
                <div class="modal-body">
                    <small class="label bg-gray">Carrega todos os pedidos entre Limite 1 e Limite 2.</small>
                    <div class="form-group">
                        <label>Limite 1</label>
                        <input type="number" class="form-control" id="limit1" name="limit1" step="1" min="0"
                               required>
                    </div>
                    <div class="form-group">
                        <label>Limite 2</label>
                        <input type="number" class="form-control" id="limit2" name="limit2" step="1" min="0"
                               required>
                    </div>
                    <small class="label bg-gray">Por motivos de segurança, serão retornados no
                        máximo <?= LIMIT_MAX ?> resultados nesta consulta. ;)
                    </small>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary" type="submit" style="width: 100%;"><i
                                class="fa fa-cloud-download"></i>&nbsp;Carregar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<div aria-hidden="true" class="modal fade" id="listProcessos" role="dialog" tabindex="-1">
    <div class="modal-dialog" style="width: 60%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Processos Atendidos pelo SOF</h4>
            </div>
            <div class="modal-body">
                <table id="tableListProcessos" class="table table-bordered table-striped">
                    <thead>
                    <tr>
                        <th>Número do Processo</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody id="tbodyListProcessos"></tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<div id="viewCompl" class="modal fade" tabindex="-1" role="dialog" data-backdrop="static">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Informação</h4>
            </div>
            <div id="complementoItem" class="modal-body"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal">Fechar</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<div class="modal fade" id="problema" role="dialog">
    <div class="modal-dialog" style="width: 40%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Relatar Problema</h4>
            </div>
            <form action="../../php/geral.php" method="post">
                <input type="hidden" name="users" value="1">
                <input type="hidden" name="form" value="problema">
                <input type="hidden" name="pag" value="lte/">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Assunto</label>
                        <input type="text" class="form-control" placeholder="Assunto" name="assunto" required>
                    </div>
                    <div class="form-group">
                        <label>Descrição</label>
                        <textarea class="form-control" rows="3"
                                  placeholder="Tente descrever o que aconteceu, o que aparece e o que deveria aparecer. Sinta-se a vontade ;)"
                                  name="descr" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary" type="submit" style="width: 100%;"><i class="fa fa-send"></i>&nbsp;Enviar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade" id="myInfos" role="dialog">
    <div class="modal-dialog" style="width: 40%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Informações do Usuário</h4>
            </div>
            <form id="altInfo" action="javascript:altInfoUser();" method="post">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Nome</label>
                        <input class="form-control" id="nameUser" name="nameUser" type="text"
                               value="<?= $_SESSION['nome'] ?>" required>
                    </div>
                    <div class="form-group">
                        <label>E-mail</label>
                        <input class="form-control" id="emailUser" name="emailUser" type="email"
                               value="<?= $_SESSION['email'] ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Senha Atual</label>
                        <input class="form-control" id="senhaAtualUser" name="senhaAtualUser" type="password"
                               required>
                    </div>
                    <div class="form-group">
                        <label>Nova Senha</label>
                        <input class="form-control" id="senhaUser" name="senhaUser" type="password" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary" type="submit" style="width: 100%;"><i class="fa fa-refresh"></i>&nbsp;Atualizar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>