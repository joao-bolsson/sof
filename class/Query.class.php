<?php

ini_set('display_erros', true);
error_reporting(E_ALL);

spl_autoload_register(function (string $class_name) {
    include_once $class_name . '.class.php';
});

/**
 * Query that implements methods of MySQLi
 *
 * @author João Bolsson (joaovictorbolsson@gmail.com)
 * @since 2017, 05 Mar..
 */
class Query {

    /**
     * Variable usued to executes a query, for example.
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
     * Executes a query in openned connection.
     * @param string $sql A SQL command to execute.
     */
    public function exe(string $sql) {
        $this->openConnection();

        $query = $this->mysqli->query($sql) or exit('Erro: ' . $this->mysqli->error);

        $this->insert_id = $this->mysqli->insert_id;

        $this->mysqli = NULL;
        return $query;
    }

    /**
     * Gets the insert id of previous query executed, maybe NULL.
     * @return int
     */
    public function getInsertId() {
        return $this->insert_id;
    }

}
