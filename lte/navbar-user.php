<?php
/**
 * @author João Bolsson (joaovictorbolsson@gmail.com).
 * @since 2017, 17 Dec.
 */
?>
<div class="navbar-custom-menu">
    <ul class="nav navbar-nav">
        <?php if (isset($_SESSION['database']) && $_SESSION['database'] == 'main'): ?>
            <!-- User Account: style can be found in dropdown.less -->
            <li class="dropdown user user-menu">
                <a href="javascript:abreModal('#myInfos');" class="dropdown-toggle">
                    <img src="dist/img/user.png" class="user-image" alt="User Image">
                    <span id="userLogado" class="hidden-xs"><?= $_SESSION["nome"] ?></span>
                </a>
            </li>
        <?php endif; ?>
        <li>
            <a href="../admin/sair.php"><i class="fa fa-power-off"></i></a>
        </li>
    </ul>
</div>
