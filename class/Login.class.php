<?php
/**
 * Classe com as funções para login de usuários
 *
 * @author João Bolsson
 * @since Version 1.0
 */
ini_set('display_erros', true);
error_reporting(E_ALL);

//incluindo a classe de conexao
include_once 'Conexao.class.php';
include_once 'Busca.class.php';

class Login extends Conexao {

	//variáveis usadas para armazenar os campos a serem consultas no banco
	private $tabela, $campoID, $campoNome, $campoLogin, $campoSenha, $campoSetor;
	//variáveis usadas como objetos da classe Conexao
	private $obj_Conexao, $mysqli;
	private $obj_Busca;
	function __construct($tabela = 'usuario', $campoID = 'id', $campoNome = 'nome', $campoLogin = 'login', $campoSenha = 'senha', $campoSetor = 'id_setor') {

		$this->obj_Busca = new Busca();
		//chama o método contrutor da classe Conexao
		parent::__construct();
		//atribuindo valor à variável para consultas no banco
		$this->mysqli = parent::getConexao();

		//atribuindo valores as variáveis dos campos
		$this->tabela = $tabela;
		$this->campoID = $campoID;
		$this->campoNome = $campoNome;
		$this->campoLogin = $campoLogin;
		$this->campoSenha = $campoSenha;
		$this->campoSetor = $campoSetor;
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
	public function login($login, $senha, $retorno = 0) {
		//escapando caracteres especiais para evitar SQL Injections
		$login = $this->mysqli->real_escape_string($login);
		//fazendo a consulta
		$query = $this->mysqli->query("SELECT {$this->campoID}, {$this->campoNome}, {$this->campoSenha}, {$this->campoSetor} FROM {$this->tabela}
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
				$_SESSION[$this->campoSetor] = $usuario->{$this->campoSetor};

				//definindo os slides para evitar recarregamentos desnecessários
				$_SESSION["slide1"] = $this->obj_Busca->getSlide(1);
				$_SESSION["slide2"] = $this->obj_Busca->getSlide(2);

				$retorno = $usuario->{$this->campoSetor};
			}
		}
		//fechando a conexao
		$query->close();
		return $retorno;
	}
}

?>
