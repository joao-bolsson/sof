<?php
/**
 * @author João Bolsson (joaovictorbolsson@gmail.com).
 * @since 2018, 31 Jan.
 */

$controller = Controller::getInstance();
?>
<nav class="tab-nav ui-tab-tab">
    <ul class="nav nav-list">
        <li class="active">
            <a class="waves-attach" href="index.php"><span class="text-white"><span
                            class="icon">home</span>INÍCIO</span></a>
        </li>
        <li>
            <div class="dropdown dropdown-inline">
                <a class="waves-attach" data-toggle="dropdown"><span class="text-white"><span
                                class="icon">store_mall_directory</span>O SETOR</span><span
                            class="icon margin-left-sm">keyboard_arrow_down</span></a>
                <ul class="dropdown-menu nav">
                    <li>
                        <a class="waves-attach" href="sof.php">SOF</a>
                    </li>
                    <li>
                        <a class="waves-attach" href="recepcao.php">Recepção</a>
                    </li>
                    <li>
                        <a class="waves-attach" href="unidades.php">Unidades</a>
                    </li>
                </ul>
            </div>
        </li>
        <li>
            <div class="dropdown dropdown-inline">
                <a class="waves-attach" data-toggle="dropdown"><span class="text-white"><span
                                class="icon">payments</span>SERVIÇOS SOF</span><span
                            class="icon margin-left-sm">keyboard_arrow_down</span></a>
                <ul class="dropdown-menu nav">
                    <li>
                        <a class="waves-attach" href="../lte/login.php">Solicitações de
                            Empenho</a>
                    </li>
                    <li>
                        <a class="waves-attach" href="consultaspi.php">Solicitações Gerais</a>
                    </li>
                </ul>
            </div>
        </li>
        <li>
            <div class="dropdown dropdown-inline">
                <a class="waves-attach" data-toggle="dropdown"><span class="text-white"><span
                                class="icon">build</span>CONSULTAS</span><span
                            class="icon margin-left-sm">keyboard_arrow_down</span></a>
                <ul class="dropdown-menu nav">
                    <li>
                        <a class="waves-attach" href="consultaspe.php">Público Externo</a>
                    </li>
                </ul>
            </div>
        </li>
        <li>
            <a class="waves-attach waves-light" href="relatorios.php"><span class="text-white"><span
                            class="icon">folder</span>RELATÓRIOS</span></a>
        </li>
        <li>
            <div class="dropdown dropdown-inline">
                <a class="waves-attach" data-toggle="dropdown"><span class="text-white"><span
                                class="icon">near_me</span>LINKS ÚTEIS</span><span
                            class="icon margin-left-sm">keyboard_arrow_down</span></a>
                <ul class="dropdown-menu nav">
                    <li>
                        <a class="waves-attach" href="linksexternos.php">Links Externos</a>
                    </li>
                    <li>
                        <a class="waves-attach" href="linksinternos.php">Links Internos</a>
                    </li>
                    <li>
                        <a class="waves-attach" href="tutoriais.php">POPs e Tutoriais</a>
                    </li>
                </ul>
            </div>
        </li>
        <li>
            <a class="waves-attach waves-light" href="noticia.php"><span class="text-white"><span
                            class="icon">event</span>NOTÍCIAS</span></a>
        </li>
        <li>
            <div class="dropdown dropdown-inline">
                <a class="waves-attach" data-toggle="dropdown"><span class="text-white"><span
                                class="icon">place</span>ENCONTROS</span><span
                            class="icon margin-left-sm">keyboard_arrow_down</span></a>
                <ul class="dropdown-menu nav">
                    <li>
                        <a class="waves-attach" href="boaspraticas.php">Boas Práticas</a>
                    </li>
                    <li>
                        <a class="waves-attach" href="comemoracoes.php">Comemorações</a>
                    </li>
                    <li>
                        <a class="waves-attach" href="dinamicas.php">Dinâmicas de grupos</a>
                    </li>
                    <li>
                        <a class="waves-attach" href="depoimentos.php">Depoimentos</a>
                    </li>
                    <li>
                        <a class="waves-attach" href="acoes_sociais.php">Ações Sociais</a>
                    </li>
                </ul>
            </div>
        </li>
        <li>
            <a class="waves-attach waves-light" href="faleconosco.php"><span class="text-white"><span
                            class="icon">chat</span>CONTATO</span></a>
        </li>
    </ul>
</nav>

