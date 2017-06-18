<?php
/**
 *
 * @author João Bolsson (joaovictorbolsson@gmail.com).
 * @since 2017, 25 May.
 */
?>
<!-- Left side column. contains the logo and sidebar -->
<aside class="main-sidebar">

    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">

        <!-- Sidebar user panel (optional) -->
        <div class="user-panel">
            <div class="pull-left image">
                <img src="dist/img/user.png" class="img-circle" alt="User Image">
            </div>
            <div class="pull-left info">
                <p><?= $_SESSION["nome"] ?></p>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <ul class="sidebar-menu">
            <li>
                <a href="javascript:abreModal('#altUser');">
                    <i class="fa fa-user"></i> <span>Alterar Usuário</span>
                </a>
            </li>
            <li class="treeview">
                <a href="#">
                    <i class="fa fa-bar-chart"></i> <span>Relatórios</span>
                </a>
                <ul class="treeview-menu">
                    <li><a href="javascript:abreModal('#relPedidos');"><i class="fa fa-circle-o"></i> Pedidos</a>
                    </li>
                    <li><a href="javascript:abreModal('#relLibOrc');"><i class="fa fa-circle-o"></i> Liberações
                            Orçamentárias</a></li>
                </ul>
            </li>
            <li>
                <a href="javascript:listRascunhos();">
                    <i class="fa fa-pencil"></i> <span>Rascunhos</span>
                </a>
            </li>
            <li>
                <a href="javascript:listPedidos();">
                    <i class="fa fa-file-text"></i> <span>Meus Pedidos</span>
                </a>
            </li>
            <li>
                <a href="javascript:listLancamentos(<?= $_SESSION["id_setor"] ?>);">
                    <i class="fa fa-dollar"></i> <span>Saldos</span>
                </a>
            </li>
            <li>
                <a href="javascript:listAdiantamentos();">
                    <i class="fa fa-plus"></i> <span>Meus Adiantamentos</span>
                </a>
            </li>
            <li>
                <a href="javascript:listSolicAltPedidos();">
                    <i class="fa fa-refresh"></i> <span>Solic Alt Pedidos</span>
                </a>
            </li>
            <li>
                <a href="javascript:listProcessos('users');">
                    <i class="fa fa-tags"></i> <span>Processos</span>
                </a>
            </li>
        </ul>
        <!-- /.sidebar-menu -->
    </section>
    <!-- /.sidebar -->
</aside>