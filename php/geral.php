<?php

/**
 * 	Todos os formulários de registro e funções que precisem registrar informações no banco
 * 	devem ser mandadas para este arquivo
 *
 *
 * 	existem algumas variáveis controladoras, tal como 'admin', 'form' e 'user'
 *
 * 	se a variável admin existir, então a ação foi feita por um usuário do SOF e deve ser
 * 	autenticada com isset($_SESSION["id_setor"]) && $_SESSION["id_setor"] == 2
 *
 * 	form controla o que fazer quando este arquivo for chamado
 *
 * 	user chama funções que podem ser feitas por todos os setores (inclusive o SOF)
 *
 * 	@author João Bolsson
 * 	@since Version 1.0
 *
 */
ini_set('display_erros', true);
error_reporting(E_ALL);

session_start();

require_once '../defines.php';

include_once '../class/Geral.class.php';
include_once '../class/Busca.class.php';
include_once '../class/Util.class.php';
include_once '../class/Login.class.php';

$obj_Busca = new Busca();

if ($obj_Busca->isActive()) {

    $obj_Geral = new Geral();
    $obj_Util = new Util();
    $obj_Login = new Login();

    $admin = filter_input(INPUT_POST, "admin");
    $users = filter_input(INPUT_POST, "users");

    $form = '';

    $filter = filter_input(INPUT_POST, 'form');
    if (!empty($filter)) {
        $form = $filter;
    }

    if (!is_null($admin) && isset($_SESSION["id_setor"]) && ($_SESSION["id_setor"] == 2 || $_SESSION["id_setor"] == 12)) {
        switch ($form) {

            case 'undoFreeMoney':
                $id_lancamento = filter_input(INPUT_POST, 'id_lancamento');
                if (!empty($id_lancamento)) {
                    $obj_Geral->undoFreeMoney($id_lancamento);
                }
                break;

            case 'aprovaGeren':
                $pedidos = filter_input(INPUT_POST, 'pedidos', FILTER_DEFAULT, FILTER_FORCE_ARRAY);
                echo $obj_Geral->aprovaGerencia($pedidos);
                break;

            case 'addUser':
                $nome = filter_input(INPUT_POST, 'nome');
                $login = filter_input(INPUT_POST, 'login');
                $email = filter_input(INPUT_POST, 'email');
                $setor = filter_input(INPUT_POST, 'setor');
                // teste para variavel indefinida
                if (empty($nome) || empty($login) || empty($email) || empty($setor)) {
                    echo "Erro: variáveis indefinidas ou vazias.";
                    break;
                }

                if (is_null($obj_Util)) {
                    $obj_Util = new Util();
                }
                $senha = $obj_Util->criaSenha();

                $id_user = $obj_Geral->cadUser($nome, $login, $email, $setor, $senha);

                $noticias = 0;
                $saldos = 0;
                $pedidos = 0;
                $recepcao = 0;

                if ($setor == 2) {
                    $noticias = !is_null(filter_input(INPUT_POST, 'noticias'));
                    $saldos = !is_null(filter_input(INPUT_POST, 'saldos'));
                    $pedidos = !is_null(filter_input(INPUT_POST, 'pedidos'));
                    $recepcao = !is_null(filter_input(INPUT_POST, 'recepcao'));
                }

                $obj_Geral->cadPermissao($id_user, $noticias, $saldos, $pedidos, $recepcao);

                $from = $obj_Util->mail->Username;
                $nome_from = utf8_decode("Setor de Orçamento e Finanças do HUSM");
                $assunto = "Cadastro SOFHUSM";
                $altBody = "Olá! Você foi cadastrado(a) no sistema do SOFHUSM.";
                $body = "Seu login: " . $login . "
                        <br>Sua senha: <strong>" . $senha . "</strong> (negrito)";
                $body .= utf8_decode("
			<br>
			<br> Não responda à esse e-mail.
			<br>
			<br>Caso tenha problemas, contate orcamentofinancashusm@gmail.com
			<br>
			<br>Atenciosamente,
			<br>equipe do SOF.");

                $obj_Util->preparaEmail($from, $nome_from, $email, "Usuário", $assunto, $altBody, $body);

                //send the message, check for errors
                if ($obj_Util->mail->send()) {
                    header("Location: ../lte/");
                } else {
                    echo "Usuário e permissões cadastradas. Erro ao enviar o e-mail com a senha : " . $senha;
                }

                break;

            case 'enviaForn':
                $id = filter_input(INPUT_POST, 'id_pedido');
                if (empty($id)) {
                    break;
                }
                $obj_Geral->enviaFornecedor($id);
                break;

            case 'enviaOrdenador':
                $id_pedido = filter_input(INPUT_POST, 'id_pedido');
                if (empty($id_pedido)) {
                    break;
                }
                echo $obj_Geral->enviaOrdenador($id_pedido);
                break;
            case 'enviaFontes':
                $id_pedido = filter_input(INPUT_POST, 'id_pedido');
                $fonte = filter_input(INPUT_POST, 'fonte');
                $ptres = filter_input(INPUT_POST, 'ptres');
                $plano = filter_input(INPUT_POST, 'plano');
                echo $obj_Geral->cadastraFontes($id_pedido, $fonte, $ptres, $plano);
                break;
            case 'resetSystem':
                if ($_SESSION['login'] == 'joao') {
                    $obj_Geral->resetSystem();
                }
                break;
            // comment.
            case 'editItem':
                $fields = filter_input(INPUT_POST, 'fields', FILTER_DEFAULT, FILTER_FORCE_ARRAY);
                $dados = filter_input(INPUT_POST, 'dados', FILTER_DEFAULT, FILTER_FORCE_ARRAY);
                $array_dados = [];

                for ($i = 0; $i < count($dados); $i++) {
                    $array_dados[$fields[$i]] = $dados[$i];
                }

                $obj_dados = (object) $array_dados;
                unset($fields);
                unset($dados);
                unset($array_dados);
                $success = $obj_Geral->editItem($obj_dados);
                if ($success) {
                    $obj_Geral->editItemFactory($obj_dados);
                    echo 1;
                } else {
                    echo 0;
                }

                break;
            // comment.

            case 'enviaEmpenho':
                $id_pedido = filter_input(INPUT_POST, 'id_pedido');
                $empenho = filter_input(INPUT_POST, 'empenho');
                $data = $obj_Util->dateFormat(filter_input(INPUT_POST, 'data'));
                echo $cadastra = $obj_Geral->cadastraEmpenho($id_pedido, $empenho, $data);
                break;
            // comentário
            case 'transfereSaldo':
                $ori = filter_input(INPUT_POST, 'ori');
                $dest = filter_input(INPUT_POST, 'dest');
                $valor = filter_input(INPUT_POST, 'valor');
                $just = filter_input(INPUT_POST, 'just');
                echo $transfere = $obj_Geral->transfereSaldo($ori, $dest, $valor, $just);

                break;
            // comentário

            case 'newTypeProcess':
                $tipo = filter_input(INPUT_POST, 'newType');
                if (empty($tipo)) {
                    header("Location: ../lte/");
                    break;
                }
                $cadastra = $obj_Geral->newTypeProcess($tipo);
                if (!$cadastra) {
                    // remove all session variables
                    session_unset();
                    // destroy the session
                    session_destroy();
                }
                header("Location: ../lte/");
                break;
            // comentário

            case 'recepcao':
                // array com os dados
                $dados = filter_input(INPUT_POST, 'dados', FILTER_DEFAULT, FILTER_FORCE_ARRAY);
                $update = $obj_Geral->updateProcesso($dados);

                if ($update) {
                    echo "true";
                } else {
                    echo "false";
                }

                break;

            // comentário

            case 'importItens':
                // Tamanho máximo do arquivo (em Bytes)
                $_UP['tamanho'] = 1024 * 1024 * 1024; // 3MB
                // Array com as extensões permitidas
                $_UP['extensoes'] = array('tsv');
                // Renomeia o arquivo? (Se true, o arquivo será salvo como .jpg e um nome único)
                $_UP['renomeia'] = false;
                // Array com os tipos de erros de upload do PHP
                $_UP['erros'][0] = 'Não houve erro';
                $_UP['erros'][1] = 'O arquivo no upload é maior do que o limite do PHP';
                $_UP['erros'][2] = 'O arquivo ultrapassa o limite de tamanho especifiado no HTML';
                $_UP['erros'][3] = 'O upload do arquivo foi feito parcialmente';
                $_UP['erros'][4] = 'Não foi feito o upload do arquivo';
                //fazendo o upload
                // Verifica se houve algum erro com o upload. Se sim, exibe a mensagem do erro
                if ($_FILES["file"]["error"] != 0) {
                    die("Não foi possível fazer o upload, erro:" . $_UP['erros'][$_FILES["file"]['error']]);
                    exit; // Para a execução do script
                }
                // Caso script chegue a esse ponto, não houve erro com o upload e o PHP pode continuar
                // Faz a verificação da extensão do arquivo
                $file_name = $_FILES["file"]['name'];
                $tmp = explode('.', $file_name);
                $file_extension = end($tmp);
                $extensao = strtolower($file_extension);
                if (array_search($extensao, $_UP['extensoes']) === false) {
                    echo "Por favor, envie arquivos com as segues extensões: .tsv";
                    exit;
                }
                // Faz a verificação do tamanho do arquivo
                if ($_UP['tamanho'] < $_FILES["file"]['size']) {
                    echo "O arquivo enviado é muito grande, envie arquivos de até 3Mb.";
                    exit;
                }
                $nome_final = $_FILES["file"]['name'];
                // O arquivo passou em todas as verificações, hora de tentar movê-lo para a pasta
                // Primeiro verifica se deve trocar o nome do arquivo
                if ($_UP['renomeia'] == true) {
                    // Cria um nome baseado no UNIX TIMESTAMP atual e com extensão .tsv
                    $nome_final = md5(time()) . '.tsv';
                }
                $dados = $obj_Util->readFile($_FILES["file"]['tmp_name']);
                unset($_FILES["file"]);
                if (count($dados) < 1) {
                    exit;
                }
                // prepara a importação dos itens (insert)
                $array_sql = $obj_Util->prepareImport($dados);
                unset($dados);
                $insert = $obj_Geral->importaItens($array_sql);
                unset($array_sql);
                if ($insert) {
                    header("Location: ../lte/");
                } else {
                    exit("Ocorreu um erro ao importar os itens. Contate o administrador.");
                }
                break;

            // comentário

            case 'analisaSolicAlt':
                $id_solic = filter_input(INPUT_POST, 'id_solic');
                $id_pedido = filter_input(INPUT_POST, 'id_pedido');
                $acao = filter_input(INPUT_POST, 'acao');

                $analisa = $obj_Geral->analisaSolicAlt($id_solic, $id_pedido, $acao);
                echo $analisa;
                break;
            // comment.

            case 'altStatus':
                $id_pedido = filter_input(INPUT_POST, 'id_pedido');
                $id_setor = filter_input(INPUT_POST, 'id_setor');
                $comentario = filter_input(INPUT_POST, 'comentario');
                $status = filter_input(INPUT_POST, 'fase');
                $analisado = $obj_Geral->altStatus($id_pedido, $id_setor, $comentario, $status);
                $excluir = filter_input(INPUT_POST, 'excluir');
                if (!empty($excluir) && $status == 3) {
                    $obj_Geral->deletePedido($id_pedido);
                }
                if ($analisado) {
                    header("Location: ../lte/");
                } else {
                    echo "Ocorreu algum erro no servidor. Contate o administrador.";
                }
                break;
            // comentário

            case 'gerenciaPedido':
                $saldo_setor = filter_input(INPUT_POST, 'saldo_total');
                //id do pedido
                $id_pedido = filter_input(INPUT_POST, 'id_pedido');
                $total_pedido = filter_input(INPUT_POST, 'total_hidden');
                $id_item = filter_input(INPUT_POST, 'id_item', FILTER_DEFAULT, FILTER_FORCE_ARRAY);
                // id dos itens cancelados
                $item_cancelado = filter_input(INPUT_POST, 'item_cancelado', FILTER_DEFAULT, FILTER_FORCE_ARRAY);

                $qtd_solicitada = filter_input(INPUT_POST, 'qtd_solicitada', FILTER_DEFAULT, FILTER_FORCE_ARRAY);
                $qt_saldo = filter_input(INPUT_POST, 'qt_saldo', FILTER_DEFAULT, FILTER_FORCE_ARRAY);
                $qt_utilizado = filter_input(INPUT_POST, 'qt_utilizado', FILTER_DEFAULT, FILTER_FORCE_ARRAY);
                $vl_saldo = filter_input(INPUT_POST, 'vl_saldo', FILTER_DEFAULT, FILTER_FORCE_ARRAY);
                $vl_utilizado = filter_input(INPUT_POST, 'vl_utilizado', FILTER_DEFAULT, FILTER_FORCE_ARRAY);
                $valor_item = filter_input(INPUT_POST, 'valor_item', FILTER_DEFAULT, FILTER_FORCE_ARRAY);

                $fase = filter_input(INPUT_POST, 'fase');
                $prioridade = filter_input(INPUT_POST, 'prioridade');
                if ($fase == 'rascunho') {
                    $prioridade = $fase;
                    $fase = 'Rascunho';
                }

                $comentario = filter_input(INPUT_POST, 'comentario');

                $analisado = $obj_Geral->pedidoAnalisado($id_pedido, $fase, $prioridade, $id_item, $item_cancelado, $qtd_solicitada, $qt_saldo, $qt_utilizado, $vl_saldo, $vl_utilizado, $valor_item, $saldo_setor, $total_pedido, $comentario);

                $excluir = filter_input(INPUT_POST, 'excluir');
                if (!empty($excluir) && $fase == 3) {
                    $obj_Geral->deletePedido($id_pedido);
                }
                if ($analisado) {
                    header("Location: ../lte/");
                } else {
                    echo "Ocorreu algum erro no servidor. Contate o administrador.";
                }
                break;

            // comentário

            case 'liberaSaldo':
                $id_setor = filter_input(INPUT_POST, 'id_setor');
                $valor = filter_input(INPUT_POST, 'valor');

                $saldo_atual = $obj_Busca->getSaldo($id_setor);

                $libera = $obj_Geral->liberaSaldo($id_setor, $valor, $saldo_atual);

                if ($libera) {
                    echo true;
                } else {
                    echo false;
                }

                break;

            // comentário

            case 'aprovaAdi':
                $id = filter_input(INPUT_POST, 'id');
                $acao = filter_input(INPUT_POST, 'acao');
                $aprova = $obj_Geral->analisaAdi($id, $acao);
                if (!$aprova) {
                    echo "Ocorreu um erro no servidor. Contate o administrador";
                }
                break;

            // comentário

            case 'alterSenha':
                $id_user = filter_input(INPUT_POST, 'id_user');
                $input_senha = filter_input(INPUT_POST, 'senha');
                //encritpando a senha
                $senha = crypt($input_senha, SALT);
                //alterando no banco
                $update = $obj_Geral->updateSenha($id_user, $senha);
                if ($update) {
                    echo true;
                } else {
                    echo false;
                }
                break;

            // comentário

            case 'novanoticia':
                $data = filter_input(INPUT_POST, 'data');
                $postagem = filter_input(INPUT_POST, 'postagem');
                $pag = filter_input(INPUT_POST, 'pag');
                $funcao = filter_input(INPUT_POST, 'funcao');

                //verificando se o usuário está publicando ou editando notícia
                if ($funcao == "novanoticia") {
                    $id_noticia = $obj_Geral->setPost($data, $postagem, $pag);

                    if ($id_noticia != 0) {
                        header("Location: ../admin/");
                    } else {
                        echo "Ocorreu um erro no servidor. Contate o administrador.";
                    }
                } else {
                    $id = filter_input(INPUT_POST, 'id_noticia');

                    $editar = $obj_Geral->editPost($data, $id, $postagem, $pag);
                    if ($editar) {
                        header("Location: ../admin/");
                    } else {
                        echo "Ocorreu um erro no servidor. Contate o administrador.";
                    }
                }
                break;

            // comentário

            case 'delArquivo':
                $file = filter_input(INPUT_POST, 'caminhoDel');
                unlink($file);
                echo "O arquivo foi excluído com sucesso! A seguir, esta página será recarregada";
                break;

            // comentário

            case 'excluirNoticia':
                $id = filter_input(INPUT_POST, 'id');
                echo $obj_Geral->excluirNoticia($id);
                break;

            default:
                break;
        }
    } else if ($users !== NULL && isset($_SESSION["id_setor"]) && $_SESSION["id_setor"] != 0) {
        switch ($form) {

            case 'altUser':
                $user = filter_input(INPUT_POST, 'user');
                if (!is_null($user)) {
                    $obj_Login->changeUser($user);
                }
                header("Location: ../");
                break;

            case 'problema':
                $assunto = filter_input(INPUT_POST, 'assunto');
                $descricao = filter_input(INPUT_POST, 'descr');
                $id_setor = $_SESSION['id_setor'];
                $pag = filter_input(INPUT_POST, 'pag'); // like lte/solicitacoes.php
                $obj_Geral->insereProblema($id_setor, $assunto, $descricao);
                header('Location: ../' . $pag);
                break;

            case 'deletePedido':
                $id_pedido = filter_input(INPUT_POST, 'id_pedido');
                echo $delete = $obj_Geral->deletePedido($id_pedido);
                break;
            // redefinindo informações do usuário
            case 'infoUser':
                $id_user = $_SESSION["id"];
                $nome = filter_input(INPUT_POST, 'nome');
                $email = filter_input(INPUT_POST, 'email');
                $novaSenha = filter_input(INPUT_POST, 'novaSenha');
                $senhaAtual = filter_input(INPUT_POST, 'senhaAtual');

                $redefini = $obj_Geral->altInfoUser($id_user, $nome, $email, $novaSenha, $senhaAtual);
                echo $redefini;
                break;

            // solicitação para alterar um pedido

            case 'alt_pedido':
                $id_pedido = filter_input(INPUT_POST, 'id_pedido');
                $justificativa = filter_input(INPUT_POST, 'justificativa');
                $id_setor = $_SESSION["id_setor"];

                $solicita = $obj_Geral->solicAltPedido($id_pedido, $id_setor, $justificativa);
                echo $solicita;
                break;
            case 'adiantamento':
                $valor = filter_input(INPUT_POST, 'valor_adiantamento');
                $justificativa = filter_input(INPUT_POST, 'justificativa');
                $id_setor = $_SESSION["id_setor"];

                $envia = $obj_Geral->solicitaAdiantamento($id_setor, $valor, $justificativa);
                if ($envia) {
                    header("Location: ../lte/solicitacoes.php");
                } else {
                    echo "Ocorreu algum erro no servidor. Contate o administrador";
                }
                break;
            case 'pedido':
                $id_user = $_SESSION['id'];
                $id_setor = $_SESSION["id_setor"];
                // dados do formulário
                $id_item = filter_input(INPUT_POST, 'id_item', FILTER_DEFAULT, FILTER_FORCE_ARRAY);
                $qtd_solicitada = filter_input(INPUT_POST, 'qtd_solicitada', FILTER_DEFAULT, FILTER_FORCE_ARRAY);
                $qtd_disponivel = filter_input(INPUT_POST, 'qtd_disponivel', FILTER_DEFAULT, FILTER_FORCE_ARRAY);
                $qtd_contrato = filter_input(INPUT_POST, 'qtd_contrato', FILTER_DEFAULT, FILTER_FORCE_ARRAY);
                $qtd_utilizado = filter_input(INPUT_POST, 'qtd_utilizado', FILTER_DEFAULT, FILTER_FORCE_ARRAY);
                $vl_saldo = filter_input(INPUT_POST, 'vl_saldo', FILTER_DEFAULT, FILTER_FORCE_ARRAY);
                $vl_contrato = filter_input(INPUT_POST, 'vl_contrato', FILTER_DEFAULT, FILTER_FORCE_ARRAY);
                $vl_utilizado = filter_input(INPUT_POST, 'vl_utilizado', FILTER_DEFAULT, FILTER_FORCE_ARRAY);
                $valor = filter_input(INPUT_POST, 'valor', FILTER_DEFAULT, FILTER_FORCE_ARRAY);

                if (empty($id_item)) {
                    exit("Erro ao ler os itens do pedido.");
                }
                $total_pedido = filter_input(INPUT_POST, 'total_hidden');
                // evita pedido zerado
                if (empty($total_pedido)) {
                    exit("Pedido zerado. Esse pedido não será inserido no sistema. Volte e recarregue a página.");
                }
                $saldo_total = filter_input(INPUT_POST, 'saldo_total');
                $prioridade = filter_input(INPUT_POST, 'st');
                $obs = filter_input(INPUT_POST, 'obs');

                $pedido = filter_input(INPUT_POST, 'pedido');
                if (is_null($pedido)) {
                    exit("Erro ao enviar o pedido. ID NULL.");
                }

                $pedido_existe = ($pedido != 0);

                $pedido_contrato = filter_input(INPUT_POST, 'pedidoContrato');
                if (empty($pedido_contrato)) {
                    $pedido_contrato = 0;
                } else {
                    $pedido_contrato = 1;
                }

                $obj_Geral->insertPedido($id_user, $id_setor, $id_item, $qtd_solicitada, $qtd_disponivel, $qtd_contrato, $qtd_utilizado, $vl_saldo, $vl_contrato, $vl_utilizado, $valor, $total_pedido, $saldo_total, $prioridade, $obs, $pedido, $pedido_contrato);

                // licitação
                $idLic = filter_input(INPUT_POST, 'idLic');
                $numero = filter_input(INPUT_POST, 'infoLic');
                $uasg = filter_input(INPUT_POST, 'uasg');
                $procOri = filter_input(INPUT_POST, 'procOri');

                if (empty($uasg) && empty($procOri)) {
                    $uasg = $procOri = "";
                }
                $tipo = filter_input(INPUT_POST, 'tipoLic');
                $geraContrato = filter_input(INPUT_POST, 'geraContrato');
                if (is_null($geraContrato)) {
                    $geraContrato = 0;
                }

                $obj_Geral->insertLicitacao($numero, $uasg, $procOri, $tipo, $pedido, $idLic, $geraContrato);

                $grupo = filter_input(INPUT_POST, 'grupo');
                if (!empty($grupo)) {
                    $obj_Geral->insertGrupoPedido($pedido, $grupo, $pedido_existe);
                }

                // pedido de contrato
                $tipo_cont = filter_input(INPUT_POST, 'tipoCont');
                if (empty($tipo_cont)) {
                    exit("Pedido inserido. Erro ao registrar uma das 3 opções.");
                }
                $siafi = "";
                if ($tipo_cont == 3) {
                    // se for reforço ou anulação, precisa ter o SIAFI
                    $siafi = filter_input(INPUT_POST, 'siafi');
                    if (empty($siafi)) {
                        exit("Pedido inserido. Erro ao ler o SIAFI.");
                    }
                }
                $obj_Geral->insertPedContr($pedido, $tipo_cont, $siafi);

                header("Location: ../lte/solicitacoes.php");
                break;
            default:
                break;
        }
    } else {
        switch ($form) {

            // resetando senha

            case 'resetSenha':
                $email = '';
                $input = filter_input(INPUT_POST, 'email');
                if (!empty($filter)) {
                    $email = $input;
                }
                // gera uma senha aleatória (sem criptografia)
                $senha = $obj_Util->criaSenha();

                $reset = $obj_Geral->resetSenha($email, $senha);

                if ($reset == "Sucesso") {
                    $from = $obj_Util->mail->Username;
                    $nome_from = utf8_decode("Setor de Orçamento e Finanças do HUSM");
                    $assunto = "Reset Senha";
                    $altBody = "Sua senha resetada";
                    $body = "Sua nova senha:<strong>{$senha}</strong>";
                    $body .= utf8_decode("
			<br>
			<br> Não responda à esse e-mail.
			<br>
			<br>Caso tenha problemas, contate orcamentofinancashusm@gmail.com
			<br>
			<br>Atenciosamente,
			<br>equipe do SOF.
			");

                    $obj_Util->preparaEmail($from, $nome_from, $email, "Usuário", $assunto, $altBody, $body);

                    //send the message, check for errors
                    if ($obj_Util->mail->send()) {
                        echo true;
                    } else {
                        echo false;
                    }
                } else {
                    echo false;
                }
                break;

            // formulário para contato

            case 'faleconosco':
                $nome = filter_input(INPUT_POST, 'nome');
                $nome_from = utf8_decode($nome);
                $from = filter_input(INPUT_POST, 'email');
                $para = "orcamentofinancashusm@gmail.com";
                $nome_para = "Setor de Orçamento e Finanças do HUSM";
                $assunto = utf8_decode(filter_input(INPUT_POST, 'assunto'));
                $mensagem = utf8_decode(filter_input(INPUT_POST, 'mensagem'));

                $altBody = 'SOF Fale Conosco';
                $body = $mensagem;

                $obj_Util->preparaEmail($from, $nome_from, $para, $nome_para, $assunto, $altBody, $body);

                $qtd_arquivos = filter_input(INPUT_POST, 'qtd-arquivos');
                /* =========== ANEXANDO OS ARQUIVOS ========  */
                if ($qtd_arquivos != 0) {
                    // Tamanho máximo do arquivo (em Bytes)
                    $_UP['tamanho'] = 1024 * 1024 * 1024 * 1024 * 1024 * 1024 * 1024 * 1024 * 1024 * 1024 * 1024; //
                    // Array com as extensões permitidas
                    $_UP['extensoes'] = array('pdf', 'docx', 'odt', 'jpg', 'jpeg', 'png');
                    // Renomeia o arquivo? (Se true, o arquivo será salvo como .jpg e um nome único)
                    $_UP['renomeia'] = false;
                    // Array com os tipos de erros de upload do PHP
                    $_UP['erros'][0] = 'Não houve erro';
                    $_UP['erros'][1] = 'O arquivo no upload é maior do que o limite do PHP';
                    $_UP['erros'][2] = 'O arquivo ultrapassa o limite de tamanho especifiado no HTML';
                    $_UP['erros'][3] = 'O upload do arquivo foi feito parcialmente';
                    $_UP['erros'][4] = 'Não foi feito o upload do arquivo';
                    //fazendo o upload de todos os arquivos inseridos
                    for ($i = 1; $i <= $qtd_arquivos; $i++) {
                        // Verifica se houve algum erro com o upload. Se sim, exibe a mensagem do erro
                        if ($_FILES["file-$i"]["error"] != 0) {
                            die("Não foi possível fazer o upload, erro:" . $_UP['erros'][$_FILES["file-$i"]['error']]);
                            exit; // Para a execução do script
                        }
                        // Caso script chegue a esse ponto, não houve erro com o upload e o PHP pode continuar
                        // Faz a verificação da extensão do arquivo
                        $extensao = strtolower(end(explode('.', $_FILES["file-$i"]['name'])));
                        if (array_search($extensao, $_UP['extensoes']) === false) {
                            echo "Por favor, envie arquivos com as segues extensões: pdf, docx ou odt";
                            exit;
                        }
                        // Faz a verificação do tamanho do arquivo
                        if ($_UP['tamanho'] < $_FILES["file-$i"]['size']) {
                            echo "O arquivo enviado é muito grande, envie arquivos de até XMb.";
                            exit;
                        }
                        $nome_final = $_FILES["file-$i"]['name'];
                        // O arquivo passou em todas as verificações, hora de tentar movê-lo para a pasta
                        // Primeiro verifica se deve trocar o nome do arquivo
                        if ($_UP['renomeia'] == true) {
                            // Cria um nome baseado no UNIX TIMESTAMP atual e com extensão .jpg
                            $nome_final = md5(time()) . '.pdf';
                        }

                        $obj_Util->mail->addAttachment($_FILES["file-$i"]['tmp_name'], $_FILES["file-$i"]['name']);
                    }
                }
                //send the message, check for errors
                if (!$obj_Util->mail->send()) {
                    echo "Mailer Error: " . $obj_Util->mail->ErrorInfo;
                } else {
                    $_SESSION["email_sucesso"] = 1;
                    header("Location: ../view/faleconosco.php");
                }
                break;

            default:
                // remove all session variables
                session_unset();
                // destroy the session
                session_destroy();
                break;
        }
    }
} else {
    // remove all session variables
    session_unset();
    // destroy the session
    session_destroy();
    echo "Estamos realizando uma manutenção no momento. Tente fazer o login novamente dentro de 10min ;)";
}
