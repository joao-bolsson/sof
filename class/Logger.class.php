<?php

/**
 * Class to log informations, warnings and errors.
 *
 * @author João Bolsson (joaovictorbolsson@gmail.com)
 * @since 2017, 28 Mar.
 */
class Logger {

    private static $INFO_PATH = __DIR__ . "/../log/info.log";
    private static $ERROR_PATH = __DIR__ . "/../log/error.log";

    /**
     * Default constructor.
     */
    private function __construct() {
        // empty
    }

    /**
     * Puts an information in log file.
     *
     * @param string $info Information to log
     */
    public static function info(string $info) {
        error_log('[' . date("d/m/Y H:i:s ") . "] " . $info . "\n", 3, self::$INFO_PATH);
    }

    /**
     * Puts an error log message in log file.
     * @param string $error Error message to log.
     */
    public static function error(string $error) {
        error_log('[' . date("d/m/Y H:i:s ") . "] " . $error . "\n", 3, self::$ERROR_PATH);
    }

}