<?php

/**
 * Classe de conexao com o banco
 *
 * @author João Bolsson
 * @since 2016, 16 Mar
 */
ini_set('display_errors', true);
error_reporting(E_ALL);

final class Conexao {

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
     * Função para pegar conexao
     *
     * @access public
     */
    public function getConexao() {
        $mysqli = new MySQLi($this->servidor, $this->usuario, $this->senha, $this->banco, 0, '/var/run/mysqld/mysqld.sock');

        /*
         * This is the "official" OO way to do it,
         * BUT $connect_error was broken until PHP 5.2.9 and 5.3.0.
         */
        if ($mysqli->connect_error) {
            die('Connect Error (' . $mysqli->connect_errno . ') '
                    . $mysqli->connect_error);
        } else {
            return $mysqli;
        }
    }

}
