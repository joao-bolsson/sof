<?php
/**
 * File that contains the aside menu bar.
 *
 * @author João Bolsson (joaovictorbolsson@gmail.com).
 * @since 2017, 23 May.
 */

$permissao = BuscaLTE::getPermissoes($_SESSION["id"]);
?>
<!-- Left side column. contains the logo and sidebar -->
<aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
        <!-- Sidebar user panel -->
        <div class="user-panel">
            <div class="pull-left image">
                <img src="dist/img/user.png" class="img-circle" alt="User Image">
            </div>
            <div class="pull-left info">
                <p id="userLogadop"><?= $_SESSION["nome"] ?></p>
            </div>
        </div>
        <!-- sidebar menu: : style can be found in sidebar.less -->
        <ul class="sidebar-menu">
            <li>
                <a href="hora.php">
                    <i class="fa fa-clock-o"></i> <span>Ponto Eletrônico</span>
                </a>
            </li>
            <?php if ($permissao->noticias): ?>
                <li>
                    <a href="posts.php">
                        <i class="fa fa-newspaper-o"></i> <span>Postar</span>
                    </a>
                </li>
            <?php endif; ?>
            <?php if ($permissao->pedidos): ?>
                <li>
                    <a href="editmode.php">
                        <i class="fa fa-database"></i> <span>Cadastrar / Editar Itens</span>
                    </a>
                </li>
            <?php endif; ?>
            <?php if ($permissao->saldos): ?>
                <li>
                    <a href="javascript:mostraSolicAdiant();">
                        <i class="fa fa-credit-card"></i> <span>Solic Adiantamento</span>
                        <?php if ($count->solic_adi > 0): ?>
                            <span class="pull-right-container">
                                            <small class="label pull-right bg-blue"><?= $count->solic_adi; ?></small>
                                        </span>
                        <?php endif; ?>
                    </a>
                </li>
            <?php endif; ?>
            <?php if ($permissao->pedidos): ?>
                <li>
                    <a href="javascript:mostraSolicAltPed();">
                        <i class="fa fa-cog"></i> <span>Solic Alt Pedidos</span>
                        <?php if ($count->solic_alt > 0): ?>
                            <span class="pull-right-container">
                                            <small class="label pull-right bg-red"><?= $count->solic_alt; ?></small>
                                        </span>
                        <?php endif; ?>
                    </a>
                </li>
                <li>
                    <a href="javascript:mostraPed();">
                        <i class="fa fa-tags"></i> <span>Pedidos</span>
                        <?php if ($count->solic_ped > 0): ?>
                            <span class="pull-right-container">
                                            <small class="label pull-right bg-blue"><?= $count->solic_ped; ?></small>
                                        </span>
                        <?php endif; ?>
                    </a>
                </li>
            <?php endif; ?>
            <?php if ($_SESSION['login'] == 'joao' || $_SESSION['login'] == 'iara'): ?>
                <li class="treeview">
                    <a href="#">
                        <i class="fa fa-users"></i> <span>Usuários</span>
                    </a>
                    <ul class="treeview-menu">
                        <li>
                            <a href="javascript:abreModal('#altUser');">
                                <i class="fa fa-user"></i> <span>Alterar Usuário</span>
                            </a>
                        </li>
                        <li>
                            <a href="javascript:abreModal('#cadUser');">
                                <i class="fa fa-user-plus"></i> <span>Adicionar Usuário</span>
                            </a>
                        </li>
                        <li>
                            <a href="javascript:abreModal('#manageUsers');">
                                <i class="fa fa-user-times"></i> <span>Desativar Usuário</span>
                            </a>
                        </li>
                    </ul>
                </li>
            <?php endif; ?>
            <?php if ($_SESSION['login'] == 'joao'): ?>
                <li>
                    <a href="javascript:listProblemas();">
                        <i class="fa fa-warning"></i> <span>Problemas</span>
                    </a>
                </li>
            <?php endif; ?>
            <li>
                <a href="javascript:abreModal('#problema');">
                    <i class="fa fa-comments"></i> <span>Relatar Problema</span>
                </a>
            </li>
            <?php if ($permissao->saldos): ?>
                <li>
                    <a href="javascript:abreModal('#freeSaldos');">
                        <i class="fa fa-send"></i> <span>Liberar Saldo</span>
                    </a>
                </li>
                <li>
                    <a href="javascript:listLancamentos();">
                        <i class="fa fa-dollar"></i> <span>Liberações Orçamentárias</span>
                    </a>
                </li>
                <li>
                    <a href="javascript:abreModal('#transferencia');">
                        <i class="fa fa-arrows-h"></i> <span>Transferências</span>
                    </a>
                </li>
            <?php endif; ?>
            <?php if ($permissao->pedidos): ?>
                <li>
                    <a href="javascript:abreModal('#importItens');">
                        <i class="fa fa-cloud-upload"></i> <span>Importar Itens</span>
                    </a>
                </li>
                <li class="treeview">
                    <a href="#">
                        <i class="fa fa-file-text"></i> <span>Relatórios</span>
                        <span class="pull-right-container">
                            <small class="label pull-right bg-blue">Novo</small>
                        </span>
                    </a>
                    <ul class="treeview-menu">
                        <li><a href="javascript:abreModal('#relPedidos');"><i class="fa fa-circle-o"></i>Pedidos</a>
                        </li>
                        <li><a href="javascript:relListUsers();"><i class="fa fa-circle-o"></i> Usuários</a></li>
                        <li><a href="javascript:abreModal('#relLibOrc');"><i class="fa fa-circle-o"></i> Liberações
                                Orçamentárias</a></li>
                        <li><a href="javascript:abreModal('#relFontes');"><i class="fa fa-circle-o"></i>Fontes</a>
                        </li>
                    </ul>
                </li>
            <?php endif; ?>
            <li>
                <a href="changelog.php">
                    <i class="fa fa-code-fork"></i> <span>Changelog</span>
                </a>
            </li>
            <?php if ($permissao->recepcao): ?>
                <li>
                    <a href="javascript:listProcessos('admin');">
                        <i class="fa fa-archive"></i> <span>Processos</span>
                    </a>
                </li>
                <li>
                    <a href="javascript:abreModal('#newTypeProcess');">
                        <i class="fa fa-plus"></i> <span>Novo Tipo</span>
                    </a>
                </li>
                <li>
                    <a href="javascript:abreModal('#relatorio');">
                        <i class="fa fa-pie-chart"></i> <span>Relatório</span>
                    </a>
                </li>
            <?php endif; ?>
        </ul>
    </section>
    <!-- /.sidebar -->
</aside>