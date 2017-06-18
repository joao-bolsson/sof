<?php

/**
 * Class to connect with DB.
 *
 * @author João Bolsson
 * @since 2016, 16 Mar
 */
ini_set('display_errors', true);
error_reporting(E_ALL);

final class Conexao {

    private $mysqli;

    private $server, $user, $password, $database;
    private static $INSTANCE;

    public static function getInstance(): Conexao {
        if (empty(self::$INSTANCE)) {
            self::$INSTANCE = new Conexao();
        }
        return self::$INSTANCE;
    }

    /**
     * Gets a configuration section in config file.
     *
     * @param string $section Section to read.
     */
    private static function getConfig(string $section) {
        $array = parse_ini_file(__DIR__ . "/../config.ini", true);
        return $array[$section];
    }

    private function __construct() {
        $config = self::getConfig('main');

        $this->server = $config["server"];
        $this->user = $config["user"];
        $this->password = $config["pass"];
        $this->database = $config["database"];
    }

    /**
     * Gets an active connection.
     *
     * @access public
     */
    public function getConnection() {
        $this->pingSH();

        return $this->mysqli;
    }

    /**
     * Initialize a connection.
     */
    private function startConnection() {
        $this->mysqli = new MySQLi($this->server, $this->user, $this->password, $this->database);

        /*
         * This is the "official" OO way to do it,
         * BUT $connect_error was broken until PHP 5.2.9 and 5.3.0.
         */
        if ($this->mysqli->connect_error) {
            die('Connect Error (' . $this->mysqli->connect_errno . ') ' . $this->mysqli->connect_error);
        }
    }

    /**
     * Check if connection still alive.
     */
    private function pingSH() {
        if (empty($this->mysqli)) {
            $this->startConnection();
        } else if (!$this->mysqli->ping()) {
            $this->startConnection();
        }
    }

}
