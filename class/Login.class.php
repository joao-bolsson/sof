<?php

/**
 * Classe com as funções para login de usuários
 *
 * @author João Bolsson
 * @since 2016, 16 Mar
 */
ini_set('display_erros', true);
error_reporting(E_ALL);

spl_autoload_register(function (string $class_name) {
    include_once $class_name . '.class.php';
});

final class Login {

    //variáveis usadas para armazenar os campos a serem consultas no banco
    private $tabela, $campoID, $campoNome, $campoLogin, $campoSenha, $campoEmail, $campoSetor;
    //variáveis usadas como objetos da classe Conexao
    private $obj_Busca, $mysqli;
    private static $INSTANCE;

    public static function getInstance(): Login {
        if (empty(self::$INSTANCE)) {
            self::$INSTANCE = new Login();
        }
        return self::$INSTANCE;
    }

    private function __construct($tabela = 'usuario', $campoID = 'id', $campoNome = 'nome', $campoLogin = 'login', $campoSenha = 'senha', $campoSetor = 'id_setor', $campoEmail = 'email') {

        $this->obj_Busca = Busca::getInstance();

        //atribuindo valores as variáveis dos campos
        $this->tabela = $tabela;
        $this->campoID = $campoID;
        $this->campoNome = $campoNome;
        $this->campoLogin = $campoLogin;
        $this->campoSenha = $campoSenha;
        $this->campoSetor = $campoSetor;
        $this->campoEmail = $campoEmail;
    }

    private function openConnection() {
        if (is_null($this->mysqli)) {
            $this->mysqli = Conexao::getInstance()->getConexao();
        }
    }

    /**
     * Seleciona o nome do setor do usuário.
     * @param int $id_setor Id do setor
     * @return string Nome do setor
     */
    private function getNomeSetor(int $id_setor): string {
        $this->openConnection();
        $query = $this->mysqli->query("SELECT setores.nome FROM setores WHERE setores.id = " . $id_setor . " LIMIT 1;") or exit("Erro ao buscar o nome do setor do usuário.");
        $this->mysqli = NULL;
        $obj = $query->fetch_object();
        return $obj->nome;
    }

    /**
     * Função para verificar a validade do login
     * As senhas são criptografas com a função crypt do php
     *
     * @access public
     * @param string login   //  login digitado pelo usuário
     * @param string senha   //  senha digitada pelo usuário
     * @return string
     *
     */
    public function login(string $login, string $senha, $retorno = 0): string {
        $this->openConnection();
        //escapando caracteres especiais para evitar SQL Injections
        $login = $this->mysqli->real_escape_string($login);
        //fazendo a consulta
        $query = $this->mysqli->query("SELECT {$this->campoID}, {$this->campoNome}, {$this->campoSenha}, {$this->campoEmail}, {$this->campoSetor} FROM {$this->tabela}
            WHERE BINARY {$this->campoLogin} = '{$login}'
            LIMIT 1");

        if ($query->num_rows > 0) {
            //colocando o retorno da query em um objeto
            $usuario = $query->fetch_object();
            $senha = $this->mysqli->real_escape_string($senha);

            // verificando a senha com o php e não mysql
            if (crypt($senha, $usuario->{$this->campoSenha}) == $usuario->{$this->campoSenha}) {
                //atribuindo valores à sessão
                $_SESSION[$this->campoID] = $usuario->{$this->campoID};
                $_SESSION[$this->campoNome] = $usuario->{$this->campoNome};
                $_SESSION[$this->campoLogin] = $login;
                $_SESSION[$this->campoEmail] = $usuario->{$this->campoEmail};
                $_SESSION[$this->campoSetor] = $usuario->{$this->campoSetor};
                $_SESSION['nome_setor'] = Login::getNomeSetor($usuario->{$this->campoSetor});

                //definindo os slides para evitar recarregamentos desnecessários
                $_SESSION["slide1"] = $this->obj_Busca->getSlide(1);
                $_SESSION["slide2"] = $this->obj_Busca->getSlide(2);

                $retorno = $usuario->{$this->campoSetor};
            }
        }
        $this->mysqli = NULL;
        return $retorno;
    }

    /**
     * Função para trocar de um usuário para outro durante uma sessão.
     * @param int $id_user Id do usuário futuro.
     */
    public function changeUser(int $id_user) {
        $this->openConnection();
        $query = $this->mysqli->query("SELECT {$this->campoNome}, {$this->campoLogin}, {$this->campoEmail}, {$this->campoSetor} FROM {$this->tabela} WHERE {$this->campoID} = {$id_user} LIMIT 1;") or exit("Erro ao buscar usuário.");
        $this->mysqli = NULL;
        session_unset();
        session_destroy();
        session_start();
        if ($query->num_rows > 0) {
            $usuario = $query->fetch_object();
            $_SESSION[$this->campoID] = $id_user;
            $_SESSION[$this->campoNome] = $usuario->{$this->campoNome};
            $_SESSION[$this->campoLogin] = $usuario->{$this->campoLogin};
            $_SESSION[$this->campoEmail] = $usuario->{$this->campoEmail};
            $_SESSION[$this->campoSetor] = $usuario->{$this->campoSetor};
            $_SESSION['nome_setor'] = Login::getNomeSetor($usuario->{$this->campoSetor});
            if ($usuario->{$this->campoSetor} == 2) {
                $_SESSION["admin"] = true;
            }
        }
    }

}
