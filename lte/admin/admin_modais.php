<?php
/**
 * File with admin modals.
 *
 * @author João Bolsson (joaovictorbolsson@gmail.com).
 * @since 2017, 23 May.
 */

?>
<?php if ($_SESSION['login'] == 'joao' || $_SESSION['login'] == 'iara'): ?>
    <div aria-hidden="true" class="modal fade" id="altUser" role="dialog" tabindex="-1">
        <div class="modal-dialog" style="width: 40%;">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Alterar Usuário</h4>
                </div>
                <form action="../php/geral.php" method="POST">
                    <input type="hidden" name="form" value="altUser"/>
                    <input type="hidden" name="users" value="1"/>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Usuário</label>
                            <select class="form-control" name="user" required>
                                <?= BuscaLTE::getUsers(); ?>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary" type="submit" style="width: 100%;"><i
                                    class="fa fa-refresh"></i>&nbsp;Trocar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div aria-hidden="true" class="modal fade" id="cadUser" role="dialog" tabindex="-1">
        <div class="modal-dialog" style="width: 40%;">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Adicionar Usuário</h4>
                </div>
                <form action="../php/geral.php" method="POST">
                    <input type="hidden" name="form" value="addUser"/>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Nome</label>
                            <input type="text" class="form-control" name="nome" placeholder="Nome" maxlength="40"
                                   required>
                        </div>
                        <div class="form-group">
                            <label>Login</label>
                            <input type="text" class="form-control" name="login" placeholder="Login" maxlength="30"
                                   required>
                        </div>
                        <div class="form-group">
                            <label>E-mail</label>
                            <input type="email" class="form-control" name="email" placeholder="E-mail"
                                   maxlength="40" required>
                        </div>
                        <div class="form-group">
                            <label>Setor</label>
                            <select class="form-control" name="setor" required>
                                <?= BuscaLTE::getOptionsSetores(); ?>
                            </select>
                        </div>
                        <?= BuscaLTE::getCheckPermissoes(); ?>
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
    <div aria-hidden="true" class="modal fade" id="manageUsers" role="dialog" tabindex="-1">
        <div class="modal-dialog" style="width: 40%;">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Desativar Usuário</h4>
                </div>
                <form id="formDesativarUsuario" action="../php/geral.php" method="POST">
                    <input type="hidden" name="form" value="desativaUser"/>
                    <input type="hidden" name="admin" value="1"/>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Usuário</label>
                            <select id="usersToDisable" class="form-control" name="user" required>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-danger" type="submit" style="width: 100%;"><i
                                    class="fa fa-close"></i>&nbsp;Desativar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php endif; ?>
<?php if ($_SESSION['login'] == 'joao'): ?>
    <div aria-hidden="true" class="modal fade" id="listProblemas" role="dialog" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Problemas Relatados</h4>
                </div>
                <div class="modal-body">
                    <table id="tableListProblemas" class="table table-bordered table-striped">
                        <thead>
                        <tr>
                            <th>Setor</th>
                            <th>Assunto</th>
                            <th>Opções</th>
                        </tr>
                        </thead>
                        <tbody id="tbodyListProblemas"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>