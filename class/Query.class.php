<?php

ini_set('display_errors', true);
error_reporting(E_ALL);

spl_autoload_register(function (string $class_name) {
    include_once $class_name . '.class.php';
});

/**
 * Query that implements methods of MySQLi
 *
 * @author João Bolsson (joaovictorbolsson@gmail.com)
 * @since 2017, 05 Mar.
 */
class Query {

    /**
     * Variable used to executes a query, for example.
     * @var MySQLi
     */
    private $mysqli;

    /**
     * Insert id of INSERT INTO result of Query.
     * @var int
     */
    private $insert_id;

    /**
     * Store the only instance of this class to all the application.
     * @var Query
     */
    private static $INSTANCE;

    public static function getInstance(): Query {
        if (empty(self::$INSTANCE)) {
            self::$INSTANCE = new Query();
        }
        return self::$INSTANCE;
    }

    private function __construct() {
        // empty
    }

    private function openConnection() {
        if (empty($this->mysqli)) {
            $this->mysqli = Conexao::getInstance()->getConexao();
        }
    }

    /**
     * Executes a query in open connection.'
     * @param string $sql A SQL command to execute.
     * @return mixed If success will return a mysqli_result object, else - FALSE.
     */
    public function exe(string $sql) {
        $this->openConnection();

        $query = $this->mysqli->query($sql);

        if (!$query) {
            Logger::error("Query executing error. Error: " . $this->mysqli->error . " SQL: " . $sql . "");
            exit('Error: ' . $this->mysqli->error);
        }
        $this->insert_id = $this->mysqli->insert_id;

        $this->mysqli = NULL;
        return $query;
    }

    /**
     * Uses mysqli::real_escape_string to escape a string.
     * @link http://php.net/manual/pt_BR/mysqli.real-escape-string.php
     * @param string $string String to escape.
     * @return string A escaped string.
     */
    public function real_escape_string(string $string): string {
        $this->openConnection();
        $escaped = $this->mysqli->real_escape_string($string);
        $this->mysqli = NULL;

        return $escaped;
    }

    /**
     * Gets the insert id of previous query executed, maybe NULL.
     * @return int
     */
    public function getInsertId() {
        return $this->insert_id;
    }

}
