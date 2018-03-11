<?php

/**
 * Class that represents a user.
 *
 * @author João Bolsson (joaovictorbolsson@gmail.com).
 * @since 2017, 20 Ago.
 */
final class User {

    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $nome;

    /**
     * @var string
     */
    private $login;

    /**
     * @var string
     */
    private $email;

    /**
     * @var int
     */
    private $id_sector;

    /**
     * @var int
     */
    private $noticias;

    /**
     * @var int
     */
    private $saldos;

    /**
     * @var int
     */
    private $pedidos;

    /**
     * @var int
     */
    private $recepcao;

    /**
     * User constructor.
     * @param int $id User id
     */
    public function __construct(int $id) {
        $this->id = $id;
        if ($this->id != 0) {
            $this->initUser();
            $this->loadPermissions();
        }
    }

    private function initUser() {
        $query = Query::getInstance()->exe("SELECT nome, login, id_setor, email FROM usuario WHERE id = " . $this->id);
        if ($query->num_rows > 0) {
            $obj = $query->fetch_object();

            $this->nome = $obj->nome;
            $this->login = $obj->login;
            $this->id_sector = $obj->id_setor;
            $this->email = $obj->email;
        }
    }

    private function loadPermissions() {
        $query = Query::getInstance()->exe("SELECT noticias, saldos, pedidos, recepcao FROM usuario_permissoes WHERE id_usuario = " . $this->id);
        if ($query->num_rows > 0) {
            $obj = $query->fetch_object();

            $this->noticias = $obj->noticias;
            $this->saldos = $obj->saldos;
            $this->pedidos = $obj->pedidos;
            $this->recepcao = $obj->recepcao;
        }
    }

    /**
     * Creates a new user or update a existing one.
     *
     * @param string $nome User name.
     * @param string $login User login.
     * @param string $email User email.
     * @param int $setor User sector.
     * @param string $senha Visible password.
     */
    public function createOrUpdate(string $nome, string $login, string $email, int $setor, string $senha) {
        $senha_crp = crypt($senha, SALT);
        $this->nome = $nome;
        $this->login = $login;
        $this->email = $email;
        $this->id_sector = $setor;

        $query = Query::getInstance()->exe("SELECT id FROM usuario WHERE email = '" . $this->email . "' AND ativo = 0;");
        if ($query->num_rows > 0) {
            // usuário já existente, atualiza as informações e reativa
            $this->id = $query->fetch_object()->id;

            Query::getInstance()->exe("UPDATE usuario SET nome = '" . $this->nome . "', login = '" . $this->login . "', senha = '" . $senha_crp . "', id_setor = " . $this->id_sector . ", ativo = 1 WHERE id = " . $this->id);
        } else {
            Query::getInstance()->exe("INSERT INTO usuario VALUES(NULL, '{$this->nome}', '{$this->login}', '{$senha_crp}', {$this->id_sector}, '{$this->email}', 1);");
            $this->id = Query::getInstance()->getInsertId();
        }
    }

    /**
     * Sets or update the user permissions.
     *
     * @param int $noticias Permissions to access SOF posts.
     * @param int $saldos Permissions to access the sectors money.
     * @param int $pedidos Permission to access the requests.
     * @param int $recepcao Permission to access the reception.
     */
    public function setPermissions(int $noticias, int $saldos, int $pedidos, int $recepcao) {
        $this->noticias = $noticias;
        $this->saldos = $saldos;
        $this->pedidos = $pedidos;
        $this->recepcao = $recepcao;

        // garante reativação de usuários
        Query::getInstance()->exe("DELETE FROM usuario_permissoes WHERE id_usuario = " . $this->id);
        Query::getInstance()->exe("INSERT INTO usuario_permissoes VALUES({$this->id}, {$this->noticias}, {$this->saldos}, {$this->pedidos}, {$this->recepcao});");
    }

    /**
     * @return int
     */
    public function getId(): int {
        return $this->id;
    }

    /**
     * Register a in or out of hours control.
     *
     * @param int $log If 1 - in, else - out.
     * @param string $ip User IP address.
     */
    public function pointRegister(int $log, string $ip) {
        if ($log == 1) {
            // entrada
            Query::getInstance()->exe('INSERT INTO usuario_hora VALUES(NULL, ' . $this->id . ", NOW(), NULL, NULL, '" . $ip . "')");
        } else {
            // saída
            $id_last = Busca::getLastRegister();
            Query::getInstance()->exe('UPDATE usuario_hora SET saida = NOW() WHERE id = ' . $id_last);
            // atualiza horas realizadas
            $horas = Util::getTimeDiffHora($id_last);
            Query::getInstance()->exe('UPDATE usuario_hora SET horas = ' . $horas . ' WHERE id = ' . $id_last);
        }
    }

    /**
     * Edit the log informations.
     *
     * @param int $id Log id.
     * @param string $entrada New int
     * @param string $saida New out.
     */
    public function editLog(int $id, string $entrada, string $saida) {
        $dateTimeE = DateTime::createFromFormat('d/m/Y H:i:s', $entrada);
        $in = $dateTimeE->format('Y-m-d H:i:s');

        $dateTimeS = DateTime::createFromFormat('d/m/Y H:i:s', $saida);
        $out = $dateTimeS->format('Y-m-d H:i:s');

        Query::getInstance()->exe("UPDATE usuario_hora SET entrada = '" . $in . "', saida = '" . $out . "' WHERE id = " . $id);

        // atualiza horas realizadas
        $horas = Util::getTimeDiffHora($id);
        Query::getInstance()->exe('UPDATE usuario_hora SET horas = ' . $horas . ' WHERE id = ' . $id);
    }

    /**
     * Disable this user.
     */
    public function disable() {
        Query::getInstance()->exe("UPDATE usuario SET ativo = 0 WHERE id = " . $this->id);
    }

    /**
     * Insert a new attest to this user.
     *
     * @param int $horas Attest hours out.
     * @param string $justificativa Attest justificative.
     * @param string $data Attest date.
     * @return bool If success - true, else - false.
     */
    public function addAttest(int $horas, string $justificativa, string $data): bool {
        $builder = new SQLBuilder(SQLBuilder::$INSERT);
        $builder->setTables(['usuario_atestados']);
        $builder->setValues([NULL, $this->id, $data, $horas, $justificativa]);

        $query = Query::getInstance()->exe($builder->__toString());
        if ($query) {
            return true;
        }
        return false;
    }


}