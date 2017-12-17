<?php
/**
 * File with utils modals.
 *
 * Used by anyone.
 */
?>
<div aria-hidden="true" class="modal fade" id="changeDB" role="dialog" tabindex="-1">
    <div class="modal-dialog" style="width: 40%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Alterar Banco de Dados</h4>
            </div>
            <form action="../php/geral.php" method="POST">
                <input type="hidden" name="form" value="changeDB"/>
                <input type="hidden" name="admin" value="1"/>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Bancos</label>
                        <select class="form-control" name="db" required>
                            <?php
                            $options = "";
                            $len = count(ARRAY_DATABASES);
                            for ($i = 0; $i < $len; $i++) {
                                $selected = "";
                                if (isset($_SESSION['database']) && $_SESSION['database'] == ARRAY_DATABASES[$i]) {
                                    $selected = "selected";
                                }
                                $options .= "<option value='" . ARRAY_DATABASES[$i] . "' " . $selected . ">" . ARRAY_DATABASES[$i] . "</option>";
                            }
                            echo $options;
                            ?>
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
<div class="modal fade" id="myInfos" role="dialog">
    <div class="modal-dialog" style="width: 40%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Informações do Usuário</h4>
            </div>
            <form id="altInfo">
                <input type="hidden" name="users" value="1">
                <input type="hidden" name="form" value="infoUser">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Nome</label>
                        <input class="form-control" name="nome" type="text"
                               value="<?= $_SESSION['nome'] ?>" required>
                    </div>
                    <div class="form-group">
                        <label>E-mail</label>
                        <input class="form-control" name="email" type="email"
                               value="<?= $_SESSION['email'] ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Senha Atual</label>
                        <input class="form-control" name="senhaAtual" type="password"
                               required>
                    </div>
                    <div class="form-group">
                        <label>Nova Senha</label>
                        <input class="form-control" name="novaSenha" type="password" required>
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
<div aria-hidden="true" class="modal fade" id="listProcessos" role="dialog" tabindex="-1">
    <div class="modal-dialog modal-lg">
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
