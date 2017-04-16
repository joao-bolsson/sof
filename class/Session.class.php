<?php

ini_set('display_errors', true);
error_reporting(E_ALL);

spl_autoload_register(function (string $class_name) {
    include_once $class_name . '.class.php';
});

/**
 * Class that represents a session.
 *
 * @author João Bolsson (joaovictorbolsson@gmail.com)
 * @since 2017, 28 Mar.
 */
class Session {

    private static $INSTANCE;

    public static function getInstance(): Session {
        if (empty(self::$INSTANCE)) {
            self::$INSTANCE = new Session();
        }
        return self::$INSTANCE;
    }

    /**
     * Default constructor.
     */
    private function __construct() {
        session_start();
        Logger::info("Initializing a session object: " . session_id());
    }

    /**
     * The same likes $_SESSION[$key] = $value
     * @param string $key
     * @param mixed $value
     */
    public function set(string $key, $value) {
        $_SESSION[$key] = $value;
    }

    /**
     * Returns the value in $_SESSION[$key].
     *
     * @param string $key Key in $_SESSION variable.
     * @return mixed
     */
    public function get(string $key) {
        if (isset($_SESSION[$key])) {
            return $_SESSION[$key];
        }
        return NULL;
    }

    public function destroy() {
        Logger::info("Destroying the session object: " . session_id());
        Logger::info("Active login: " . $this->get('login'));
        // Initialize the session.
        // If you are using session_name("something"), don't forget it now!
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        // Unset all of the session variables.
        $_SESSION = array();

        // If it's desired to kill the session, also delete the session cookie.
        // Note: This will destroy the session, and not just the session data!
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }

        // Finally, destroy the session.
        session_destroy();
    }

}