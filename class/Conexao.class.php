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

    private $servidor, $usuario, $senha, $banco;
    private static $INSTANCE;

    public static function getInstance(): Conexao {
        if (empty(self::$INSTANCE)) {
            self::$INSTANCE = new Conexao();
        }
        return self::$INSTANCE;
    }

    private function __construct($servidor = "localhost", $usuario = "root", $senha = "j:03984082037@[]ccufsm", $banco = "sof") {

        //definindo variaveis
        $this->servidor = $servidor;
        $this->usuario = $usuario;
        $this->senha = $senha;
        $this->banco = $banco;
    }

    /**
     * Gets an active connection.
     *
     * @access public
     */
    public function getConexao() {
        $this->pingSH();

        return $this->mysqli;
    }

    /**
     * Initialize a connection.
     */
    private function startConnection() {
        $this->mysqli = new MySQLi($this->servidor, $this->usuario, $this->senha, $this->banco);

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
