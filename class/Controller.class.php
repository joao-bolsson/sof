<?php

/**
 * Controller of active session in application.
 *
 * @author João Bolsson (joaovictorbolsson@gmail.com)
 * @since 2017, 26 Feb.
 */
class Controller {

    /**
     * @var Controller
     */
    private static $INSTANCE;

    /**
     * @return Controller The controller of this session.
     */
    public static function getInstance(): Controller {
        if (empty(self::$INSTANCE)) {
            self::$INSTANCE = new Controller();
        }
        return self::$INSTANCE;
    }

    /**
     * Default construct.
     */
    private function __construct() {
        if (!isset($_SESSION['id_setor'])) {
            $_SESSION['id_setor'] = 0;
        }
    }

    /**
     * @return string The buttons group in the page's top.
     */
    public final function buttonsRight(): string {
        $return = '';

        if ($_SESSION['id_setor'] == 2) {
            $return .= "
                <li>
                    <a class=\"btn btn-flat waves-attach waves-light\" href=\"../lte/\"><span class=\"text-white\"><span class=\"icon\">power_settings_new</span>ADMIN</span></a>
                </li>";
        } else {
            $return .= "
                <li class=\"active\">
                    <a class=\"btn btn-flat waves-attach waves-light\" href=\"../lte/login.php\"><span class=\"text-white\"><span class=\"icon\">power_settings_new</span>LOGIN</span></a>
                </li>";
        }

        $return .= ($_SESSION['id_setor'] != 0) ? "<li>
                            <a class=\"btn btn-flat waves-attach waves-light\" href=\"../admin/sair.php\"><span class=\"text-white\"><span class=\"icon\">undo</span>SAIR</span></a>
                        </li>" : '';

        return $return;
    }

    /**
     * @return string Footer to docs to ordenator.
     */
    public static final function footerOrdenator(): string {
        return "<br><br><br>
        <h5 class=\"ass\" style=\"text-align: center\">
        _______________________________________________<br>
        AUTORIZADO PELO ORDENADOR DE DESPESAS
        </h5><br><br>
        <h4 style=\"text-align: center\" class=\"ass\">Santa Maria, ___ de ___________________ de _____.</h4>";
    }

}
