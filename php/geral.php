<?php

/**
 *    Todos os formulários de registro e funções que precisem registrar informações no banco
 *    devem ser mandadas para este arquivo
 *
 *
 *    existem algumas variáveis controladoras, tal como 'admin', 'form' e 'user'
 *
 *    se a variável admin existir, então a ação foi feita por um usuário do SOF e deve ser
 *    autenticada com isset($_SESSION["id_setor"]) && $_SESSION["id_setor"] == 2
 *
 *    form controla o que fazer quando este arquivo for chamado
 *
 *    user chama funções que podem ser feitas por todos os setores (inclusive o SOF)
 *
 * @author João Bolsson
 * @since Version 1.0
 *
 */
ini_set('display_errors', true);
error_reporting(E_ALL);

session_start();

require_once '../defines.php';

spl_autoload_register(function (string $class_name) {
    include_once '../class/' . $class_name . '.class.php';
});

if (Busca::isActive()) {
    $obj_Util = Util::getInstance();
    $obj_Login = Login::getInstance();

    $admin = filter_input(INPUT_POST, "admin");
    $users = filter_input(INPUT_POST, "users");

    $form = '';

    $filter = filter_input(INPUT_POST, 'form');
    if (!empty($filter)) {
        $form = $filter;
    }

    if (!is_null($admin) && isset($_SESSION["id_setor"]) && ($_SESSION["id_setor"] == 2 || $_SESSION["id_setor"] == 12)) {
        switch ($form) {

            case 'remContract':
                $contrato = filter_input(INPUT_POST, 'id');
                $rem = Geral::remContract($contrato);
                if ($rem) {
                    echo "ok";
                } else {
                    echo "error";
                }
                break;

            case 'formMensalidade':
                $id = filter_input(INPUT_POST, 'id');
                $contrato = filter_input(INPUT_POST, 'contrato');
                $grupo = filter_input(INPUT_POST, 'grupo');
                $ano = filter_input(INPUT_POST, 'ano');
                $mes = filter_input(INPUT_POST, 'mes');
                $valor = filter_input(INPUT_POST, 'valor');
                $nota = !empty(filter_input(INPUT_POST, 'nota'));
                $reajuste = filter_input(INPUT_POST, 'valorReajuste');

                $aguardaOrc = !empty(filter_input(INPUT_POST, 'checkAgOrc'));
                $paga = !empty(filter_input(INPUT_POST, 'checkPaga'));

                if (empty($reajuste)) {
                    $reajuste = floatval(0);
                }

                $cad = Geral::cadMensalidade($id, $contrato, $ano, $mes, $grupo, $valor, $nota, $reajuste, $aguardaOrc, $paga);
                echo $cad;
                break;

            case 'formEmpresa':
                $nome = filter_input(INPUT_POST, 'nome');
                $cnpj = filter_input(INPUT_POST, 'cnpj');
                $contratos = filter_input(INPUT_POST, 'contratos', FILTER_DEFAULT, FILTER_FORCE_ARRAY);
                $grupos = filter_input(INPUT_POST, 'grupos', FILTER_DEFAULT, FILTER_FORCE_ARRAY);

                $cad = Geral::cadEmpresa($nome, $cnpj, $contratos, $grupos);
                echo $cad;
                break;

            case 'formContr':
                $id = filter_input(INPUT_POST, 'id');
                $numero = filter_input(INPUT_POST, 'numero');
                $dt_inicio = filter_input(INPUT_POST, 'dt_inicio');
                $dt_fim = filter_input(INPUT_POST, 'dt_fim');
                $teto = floatval(filter_input(INPUT_POST, 'teto'));
                $mensalidade = floatval(filter_input(INPUT_POST, 'mensalidade'));

                $dt_inicio = Util::dateFormat($dt_inicio);
                $dt_fim = Util::dateFormat($dt_fim);

                $cad = Geral::cadContract($id, $numero, $teto, $dt_inicio, $dt_fim, $mensalidade);
                echo $cad;
                break;
            case 'changeDB':
                $db = filter_input(INPUT_POST, 'db');
                if (in_array($db, ARRAY_DATABASES)) {
                    Conexao::getInstance()->changeDatabase($db);
                    header("Location: ../lte/");
                } else {
                    echo "Banco inválido!";
                }
                break;

            case 'atestado':
                $id_user = filter_input(INPUT_POST, 'user');
                $horas = filter_input(INPUT_POST, 'horas');
                $justificativa = filter_input(INPUT_POST, 'justificativa');
                $dia = filter_input(INPUT_POST, 'dia');

                $data = Util::dateFormat($dia);

                $user = new User($id_user);
                echo $user->addAttest($horas, $justificativa, $data);
                break;

            case 'cadFontesTransf':
                $setores = filter_input(INPUT_POST, 'setores', FILTER_DEFAULT, FILTER_FORCE_ARRAY);
                $fonte = filter_input(INPUT_POST, 'fonte');
                $ptres = filter_input(INPUT_POST, 'ptres');
                $plano = filter_input(INPUT_POST, 'plano');

                MoneySource::newSourceToSectors($setores, $fonte, $ptres, $plano);
                header("Location: ../lte/");
                break;

            case 'desativaUser':
                $id_user = filter_input(INPUT_POST, 'user');
                if ($id_user !== NULL) {
                    $user = new User($id_user);
                    $user->disable();
                } else {
                    echo "fail";
                }
                break;

            case 'regJustify':
                $just = filter_input(INPUT_POST, 'justificativa');

                Util::recordJustify($just);
                header("Location: ../lte/");
                break;

            case 'editLog':
                $id = filter_input(INPUT_POST, 'idLog');
                $entrada = filter_input(INPUT_POST, 'entrada');
                $saida = filter_input(INPUT_POST, 'saida');

                $user = new User($_SESSION['id']);
                $user->editLog($id, $entrada, $saida);
                header('Location: ../lte/hora.php');
                break;

            case 'pointRegister':
                $log = filter_input(INPUT_POST, 'log');
                $ip = filter_input(INPUT_SERVER, 'REMOTE_ADDR');
                $user = new User($_SESSION['id']);

                $user->pointRegister($log, $ip);
                break;

            case 'formEditRegItem':
                $array_names = ARRAY_ITEM_FIELDS;

                $data = [];

                for ($i = 0; $i < count($array_names); $i++) {
                    $name = $array_names[$i];
                    $input = filter_input(INPUT_POST, $name);
                    if ($input !== NULL) {
                        $data[$name] = $input;
                        if ($name == 'cancelado') {
                            $data[$name] = true;
                        }
                    } else if ($name == 'cancelado') {
                        $data[$name] = false;
                    }
                }

                $item = new Item($data['id']);
                $item->setIdItemProcesso($data['id_item_processo']);
                $item->setIdItemContrato($data['id_item_contrato']);
                $item->setCodDespesa($data['cod_despesa']);
                $item->setDescrDespesa($data['descr_despesa']);
                $item->setDescrTipoDoc($data['descr_tipo_doc']);
                $item->setNumContrato($data['num_contrato']);
                $item->setNumProcesso($data['num_processo']);
                $item->setDescrModCompra($data['descr_mod_compra']);
                $item->setNumLicitacao($data['num_licitacao']);
                $item->setDtInicio($data['dt_inicio']);
                $item->setDtFim($data['dt_fim']);
                $item->setDtGeracao($data['dt_geracao']);
                $item->setCgcFornecedor($data['cgc_fornecedor']);
                $item->setNomeFornecedor($data['nome_fornecedor']);
                $item->setNumExtrato($data['num_extrato']);
                $item->setCodEstruturado($data['cod_estruturado']);
                $item->setNomeUnidade($data['nome_unidade']);
                $item->setCodReduzido($data['cod_reduzido']);
                $item->setComplementoItem($data['complemento_item']);
                $item->setDescricao($data['descricao']);
                $item->setIdExtratoContr($data['id_extrato_contr']);
                $item->setQtContrato($data['qt_contrato']);
                $item->setVlContrato($data['vl_contrato']);
                $item->setQtUtilizado($data['qt_utilizado']);
                $item->setVlUtilizado($data['vl_utilizado']);
                $item->setQtSaldo($data['qt_saldo']);
                $item->setVlSaldo($data['vl_saldo']);
                $item->setIdUnidade($data['id_unidade']);
                $item->setAnoOrcamento($data['ano_orcamento']);
                $item->setCancelado($data['cancelado']);
                if ($data['id'] == Item::$NEW_ITEM) {
                    $item->setChave($data['num_processo'] . '#' . $data['cod_reduzido'] . '#' . $data['seq_item_processo']);
                }
                $item->setSeqItemProcesso($data['seq_item_processo']);
                /**
                 * This value need to be setted lastly
                 */
                $item->setVlUnitario($data['vl_unitario']);

                unset($data);
                $success = $item->update();
                if (!$success) {
                    echo "fail";
                }
                break;

            case 'undoFreeMoney':
                $id_lancamento = filter_input(INPUT_POST, 'id_lancamento');
                if (!empty($id_lancamento)) {
                    $freeMoney = new FreeMoney($id_lancamento);
                    $freeMoney->undo();
                }
                break;

            case 'aprovaGeren':
                $pedidos = filter_input(INPUT_POST, 'pedidos', FILTER_DEFAULT, FILTER_FORCE_ARRAY);
                Request::aprovaGerencia($pedidos);
                break;

            case 'enviaForn':
                $id = filter_input(INPUT_POST, 'id_pedido');
                if (empty($id)) {
                    break;
                }
                $pedido = new Request($id);
                $pedido->setStatus(9);
                break;

            case 'enviaOrdenador':
                $id_pedido = filter_input(INPUT_POST, 'id_pedido');
                if (empty($id_pedido)) {
                    break;
                }
                $pedido = new Request($id_pedido);
                $pedido->setStatus(8);
                echo true;
                break;

            case 'enviaFontes':
                $id_pedido = filter_input(INPUT_POST, 'id_pedido');
                $fonte = filter_input(INPUT_POST, 'fonte');
                $ptres = filter_input(INPUT_POST, 'ptres');
                $plano = filter_input(INPUT_POST, 'plano');
                echo Geral::cadastraFontes($id_pedido, $fonte, $ptres, $plano);
                break;

            case 'enviaEmpenho':
                $id_pedido = filter_input(INPUT_POST, 'id_pedido');
                $empenho = filter_input(INPUT_POST, 'empenho');
                $data = Util::dateFormat(filter_input(INPUT_POST, 'data'));

                $request = new Request($id_pedido);
                echo $cadastra = $request->setSIAFI($empenho, $data);
                break;

            case 'transfereSaldo':
                $ori = filter_input(INPUT_POST, 'ori');
                $dest = filter_input(INPUT_POST, 'dest');
                $val = filter_input(INPUT_POST, 'valor');
                $just = filter_input(INPUT_POST, 'just');

                $sector = new Sector($dest);
                $sof = new Sector(2);

                $valor = floatval($val);

                $transfere = $sof->transferMoneyTo($sector, $valor, $just);

                if ($transfere) {
                    echo "success";
                } else {
                    echo "fail";
                }
                break;

            case 'newTypeProcess':
                $tipo = filter_input(INPUT_POST, 'newType');
                if (empty($tipo)) {
                    header("Location: ../lte/");
                    break;
                }
                $cadastra = Geral::newTypeProcess($tipo);
                if (!$cadastra) {
                    // remove all session variables
                    session_unset();
                    // destroy the session
                    session_destroy();
                }
                header("Location: ../lte/");
                break;

            case 'recepcao':
                // fields names in form
                $fields = ["id_processo", "num_processo", "tipo", "estante", "prateleira", "entrada", "saida", "responsavel", "retorno", "obs", "vigencia"];

                $len = count($fields);

                $data = [];
                for ($i = 0; $i < $len; $i++) {
                    $data[$i] = filter_input(INPUT_POST, $fields[$i]);
                }

                $update = Geral::updateProcesso($data);

                if ($update) {
                    echo "true";
                } else {
                    echo "false";
                }

                break;

            case 'importItens':
                // Tamanho máximo do arquivo (em Bytes)
                $maxFileSize = 1024;
                for ($j = 1; $j < MAX_UPLOAD_SIZE; $j++) {
                    $maxFileSize *= 1024;
                }
                $_UP['tamanho'] = $maxFileSize;
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
                }
                // Caso script chegue a esse ponto, não houve erro com o upload e o PHP pode continuar
                // Faz a verificação da extensão do arquivo
                $file_name = $_FILES["file"]['name'];
                $tmp = explode('.', $file_name);
                $file_extension = end($tmp);
                $extensao = strtolower($file_extension);
                if (array_search($extensao, $_UP['extensoes']) === false) {
                    exit("Por favor, envie arquivos com as segues extensões: .tsv");
                }
                // Faz a verificação do tamanho do arquivo
                if ($_UP['tamanho'] < $_FILES["file"]['size']) {
                    exit("O arquivo enviado é muito grande, envie arquivos de até " . MAX_UPLOAD_SIZE . "MB.");
                }
                $nome_final = $_FILES["file"]['name'];
                // O arquivo passou em todas as verificações, hora de tentar movê-lo para a pasta
                // Primeiro verifica se deve trocar o nome do arquivo
                if ($_UP['renomeia'] == true) {
                    // Cria um nome baseado no UNIX TIMESTAMP atual e com extensão .tsv
                    $nome_final = md5(time()) . '.tsv';
                }
                if (isset($_SESSION['database'])) {
                    $nome_final .= "-" . $_SESSION['database'];
                }
                $dir = "../toImport/";
                move_uploaded_file($_FILES["file"]['tmp_name'], $dir . $nome_final);
                //                $dados = $obj_Util->readFile($_FILES["file"]['tmp_name']);
                //                unset($_FILES["file"]);
                //                if (count($dados) < 1) {
                //                    exit("Empty file!");
                //                }
                // prepara a importação dos itens (insert)
                //                $array_sql = $obj_Util->prepareImport($dados);
                //                unset($dados);
                //                $len = count($array_sql);


                //                $sqlFileName = $dir . $nome_final . ".sql";
                //                for ($i = 0; $i < $len; $i++) {
                //                    $query = $array_sql[$i];
                //                    file_put_contents($sqlFileName, $query, FILE_APPEND);
                //                }
                //                unset($array_sql);

                header("Location: ../lte/");
                break;

            case 'analisaSolicAlt':
                $id_solic = filter_input(INPUT_POST, 'id_solic');
                $id_pedido = filter_input(INPUT_POST, 'id_pedido');
                $acao = filter_input(INPUT_POST, 'acao');

                $analisa = Geral::analisaSolicAlt($id_solic, $id_pedido, $acao);
                echo $analisa;
                break;

            case 'altStatus':
                $id_pedido = filter_input(INPUT_POST, 'id_pedido');
                $comentario = filter_input(INPUT_POST, 'comentario');
                $status = filter_input(INPUT_POST, 'fase');

                $pedido = new Request($id_pedido);

                $pedido->manage($status, []);
                $pedido->addComment($comentario);

                $analisado = true;
                $excluir = filter_input(INPUT_POST, 'excluir');
                if (!empty($excluir) && $status == 3) {
                    Request::delete($id_pedido);
                }
                if ($analisado) {
                    header("Location: ../lte/");
                } else {
                    echo "Ocorreu algum erro no servidor. Contate o administrador.";
                }
                break;

            case 'gerenciaPedido':
                $id_pedido = filter_input(INPUT_POST, 'id_pedido');
                $item_cancelado = filter_input(INPUT_POST, 'item_cancelado', FILTER_DEFAULT, FILTER_FORCE_ARRAY);
                $fase = filter_input(INPUT_POST, 'fase');
                $prioridade = filter_input(INPUT_POST, 'prioridade');
                $comentario = filter_input(INPUT_POST, 'comentario');

                // never must happen (requests without items)
                if (is_null($item_cancelado)) {
                    $item_cancelado = [];
                }
                $pedido = new Request($id_pedido);
                $pedido->manage($fase, $item_cancelado);
                $pedido->addComment($comentario);

                $excluir = filter_input(INPUT_POST, 'excluir');
                if (!empty($excluir) && $fase == 3) {
                    Request::delete($id_pedido);
                }
                break;

            case 'liberaSaldo':
                $id_setor = filter_input(INPUT_POST, 'id_setor');
                $valor = filter_input(INPUT_POST, 'valor');

                $sector = new Sector($id_setor);
                $vl = floatval($valor);
                $oldMoney = $sector->getMoney();
                $sector->setMoney($oldMoney + $vl);

                if ($sector->getId() != 2) {
                    $sof = new Sector(2);
                    $oldSOFMoney = $sof->getMoney();
                    $sof->setMoney($oldSOFMoney - $vl);
                }

                $hoje = date('Y-m-d');
                Query::getInstance()->exe("INSERT INTO saldos_lancamentos VALUES(NULL, {$sector->getId()}, '{$hoje}', '{$vl}', 1);");
                break;

            case 'aprovaAdi':
                $id = filter_input(INPUT_POST, 'id');
                $acao = filter_input(INPUT_POST, 'acao');
                $aprova = Geral::analisaAdi($id, $acao);
                if (!$aprova) {
                    echo "Ocorreu um erro no servidor. Contate o administrador";
                }
                break;

            case 'alterSenha':
                $id_user = filter_input(INPUT_POST, 'id_user');
                $input_senha = filter_input(INPUT_POST, 'senha');
                //encritpando a senha
                $senha = crypt($input_senha, SALT);
                //alterando no banco
                $update = Geral::updateSenha($id_user, $senha);
                if ($update) {
                    echo true;
                } else {
                    echo false;
                }
                break;

            case 'novanoticia':
                $data = filter_input(INPUT_POST, 'data');
                $postagem = filter_input(INPUT_POST, 'postagem');
                $pag = filter_input(INPUT_POST, 'pag');
                $funcao = filter_input(INPUT_POST, 'funcao');

                //verificando se o usuário está publicando ou editando notícia
                if ($funcao == "novanoticia") {
                    $id_noticia = Geral::setPost($data, $postagem, $pag);

                    if ($id_noticia != 0) {
                        header("Location: ../lte/posts.php");
                    } else {
                        echo "Ocorreu um erro no servidor. Contate o administrador.";
                    }
                } else {
                    $id = filter_input(INPUT_POST, 'id_noticia');

                    $editar = Geral::editPost($data, $id, $postagem, $pag);
                    if ($editar) {
                        header("Location: ../lte/posts.php");
                    } else {
                        echo "Ocorreu um erro no servidor. Contate o administrador.";
                    }
                }
                break;

            case 'delArquivo':
                $file = filter_input(INPUT_POST, 'caminhoDel');
                $unlink = unlink($file);
                if ($unlink == TRUE) {
                    echo "O arquivo foi excluído com sucesso! A seguir, esta página será recarregada";
                } else {
                    echo "Falha ao excluir arquivo: " . $file;
                }
                break;

            case 'excluirNoticia':
                $id = filter_input(INPUT_POST, 'id');
                echo Geral::excluirNoticia($id);
                break;

            default:
                break;
        }
    } else if ($users !== NULL && isset($_SESSION["id_setor"]) && $_SESSION["id_setor"] != 0) {
        switch ($form) {

            case 'cadFatAprov':
                $id = filter_input(INPUT_POST, 'id');
                $data = filter_input(INPUT_POST, 'data');
                $competencia = filter_input(INPUT_POST, 'mes');
                $producao = filter_input(INPUT_POST, 'producao');
                $financiamento = filter_input(INPUT_POST, 'financiamento');
                $complexidade = filter_input(INPUT_POST, 'complexidade');
                $valor = filter_input(INPUT_POST, 'valor');

                $insertId = Geral::cadFaturamento($id, $data, $competencia, $producao, $financiamento, $complexidade, $valor);

                // contratualizacao
                $contr = filter_input(INPUT_POST, 'contr');
                $vigencia_ini = filter_input(INPUT_POST, 'vigencia_ini');
                $vigencia_fim = filter_input(INPUT_POST, 'vigencia_fim');
                $aditivo_ini = filter_input(INPUT_POST, 'aditivo_ini');
                $aditivo_fim = filter_input(INPUT_POST, 'aditivo_fim');
                $prefix = filter_input(INPUT_POST, 'prefix');
                $valor = filter_input(INPUT_POST, 'valorContr');

                Geral::cadContratualizacao($insertId, $contr, $vigencia_ini, $vigencia_fim, $aditivo_ini, $aditivo_fim, $prefix, $valor);

                echo "ok";
                break;

            case 'cadTipoReceitaRec':
                $tipo = filter_input(INPUT_POST, 'tipo');
                Geral::cadReceitaTipo($tipo);
                header("Location: ../lte/aihs.php");
                break;

            case 'removeReceita':
                $id = filter_input(INPUT_POST, 'id');

                Geral::removeReceita($id);
                echo "ok";
                break;

            case 'removeAIHS':
                $id = filter_input(INPUT_POST, 'id');

                Geral::removeAIHS($id);
                echo "ok";
                break;

            case 'cadAIHS':
                $id = filter_input(INPUT_POST, 'id');
                $data = filter_input(INPUT_POST, 'data');
                $mes = filter_input(INPUT_POST, 'mes');
                $qtd = filter_input(INPUT_POST, 'qtd');
                $valor = filter_input(INPUT_POST, 'valor');
                $tipo = filter_input(INPUT_POST, 'tipo');
                $grupo = filter_input(INPUT_POST, 'grupo');
                $descricao = filter_input(INPUT_POST, 'descricao');

                // can be empty if disabled
                if (empty($grupo)) {
                    $grupo = "";
                }
                if (empty($descricao)) {
                    $descricao = "";
                }

                $bool = Geral::cadAIHS($id, $data, $mes, $qtd, $valor, $tipo, $grupo, $descricao);
                if ($bool) {
                    echo "Registro inserido com sucesso.";
                } else {
                    echo "O registro não pode ser inserido. Não inserimos registros repetidos (com o mesmo tipo e a mesma competência). Caso não seja esse o caso, contate o administrador.";
                }
                break;

            case 'cadReceita':
                $id = filter_input(INPUT_POST, 'id');
                $tipo = filter_input(INPUT_POST, 'tipo');
                $mes = filter_input(INPUT_POST, 'mes');
                $data = filter_input(INPUT_POST, 'data');
                $valor = filter_input(INPUT_POST, 'valor');
                $pf = filter_input(INPUT_POST, 'pf');
                $obs = filter_input(INPUT_POST, 'obs');

                $bool = Geral::cadReceita($id, $tipo, $mes, $data, $valor, $pf, $obs);
                if ($bool) {
                    echo "Registro inserido com sucesso.";
                } else {
                    echo "O registro não pode ser inserido. Não inserimos registros repetidos (com o mesmo tipo e a mesma competência). Caso não seja esse o caso, contate o administrador.";
                }
                break;

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
                Geral::insereProblema($id_setor, $assunto, $descricao);

                $from = $obj_Util->mail->Username;
                $nome_from = utf8_decode("Setor de Orçamento e Finanças do HUSM");
                $altBody = "Olá! Temos um problema: " . $assunto;

                $obj_Util->preparaEmail($from, $nome_from, "joaovictorbolsson@gmail.com", "João", "Problema relatado", $altBody, $descricao);

                //send the message, check for errors
                if (!$obj_Util->mail->send()) {
                    Logger::error("Erro ao enviar problema por e-mail");
                }
                header('Location: ../' . $pag);
                break;

            case 'deletePedido':
                $id_pedido = filter_input(INPUT_POST, 'id_pedido');
                Request::delete($id_pedido);
                break;

            // redefinindo informações do usuário
            case 'infoUser':
                $id_user = $_SESSION["id"];
                $nome = filter_input(INPUT_POST, 'nome');
                $email = filter_input(INPUT_POST, 'email');
                $novaSenha = filter_input(INPUT_POST, 'novaSenha');
                $senhaAtual = filter_input(INPUT_POST, 'senhaAtual');

                $redefini = Geral::altInfoUser($id_user, $nome, $email, $novaSenha, $senhaAtual);
                echo $redefini;
                break;

            // solicitação para alterar um pedido

            case 'alt_pedido':
                $id_pedido = filter_input(INPUT_POST, 'id_pedido');
                $justificativa = filter_input(INPUT_POST, 'justificativa');
                $id_setor = $_SESSION["id_setor"];

                $solicita = Geral::solicAltPedido($id_pedido, $id_setor, $justificativa);
                echo $solicita;
                break;

            case 'adiantamento':
                $valor = filter_input(INPUT_POST, 'valor_adiantamento');
                $justificativa = filter_input(INPUT_POST, 'justificativa');
                $id_setor = $_SESSION["id_setor"];

                $envia = Geral::solicitaAdiantamento($id_setor, $valor, $justificativa);
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

                if (empty($id_item)) {
                    exit("Erro ao ler os itens do pedido.");
                }
                $total_pedido = filter_input(INPUT_POST, 'total_hidden');
                // evita pedido zerado
                if (empty($total_pedido)) {
                    exit("Pedido zerado. Esse pedido não será inserido no sistema. Volte e recarregue a página.");
                }

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

                $procSei = filter_input(INPUT_POST, 'procSei');
                $pedSei = filter_input(INPUT_POST, 'pedSei');

                // $pedido pode ser 0, será sobrescrito depois
                $request = new Request($pedido);
                if (!$pedido_existe) {
                    $request->insertNewRequest($id_user, $id_setor, $id_item, $qtd_solicitada, $prioridade, $obs, $pedido_contrato, $procSei, $pedSei);
                } else {
                    $request->editRequest($id_item, $qtd_solicitada, $prioridade, $obs, $pedido_contrato, $procSei, $pedSei);
                }

                $pedido = $request->getId();

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

                $licitacao = new Licitacao($idLic, $numero, $uasg, $procOri, $tipo, $geraContrato);

                $request->setLicitacao($licitacao);

                $grupo = filter_input(INPUT_POST, 'grupo');
                if (!empty($grupo)) {
                    $group = new SectorGroup($grupo);
                    $request->setGroup($group);
                }

                // pedido de contrato
                $tipo_cont = filter_input(INPUT_POST, 'tipoCont');
                if (empty($tipo_cont)) {
                    exit("Pedido inserido. Erro ao registrar uma das 3 opções.");
                }
                $siafi = "";
                if ($tipo_cont == 2 || $tipo_cont == 3) {
                    // se for reforço ou anulação, precisa ter o SIAFI
                    $siafi = filter_input(INPUT_POST, 'siafi');
                    if (empty($siafi)) {
                        exit("Pedido inserido. Erro ao ler o SIAFI.");
                    }
                }
                $request->setContract($tipo_cont, $siafi);

                $checkPlanoTrabalho = filter_input(INPUT_POST, 'checkPlanoTrabalho');
                if (!empty($checkPlanoTrabalho)) {
                    $plan = filter_input(INPUT_POST, 'planoTrabalho');

                    $request->setWorkPlan($plan);
                }

                header("Location: ../lte/solicitacoes.php");
                break;
            default:
                break;
        }
    } else {
        switch ($form) {

            case 'importItensTest':
                $data = filter_input(INPUT_POST, 'array_sql', FILTER_DEFAULT, FILTER_FORCE_ARRAY);

                Logger::info("================\nImporting itens (test code) array size: " . count($data));
                $i = 0;
                foreach ($data as $sql) {
                    Logger::info("Line: " . $i++);
                    Query::getInstance()->exe($sql);
                }
                Logger::info("Finish\n=================");
                echo "finish";
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
                    $obj_Util = Util::getInstance();
                }
                $senha = Util::criaSenha();

                $user = new User(0);
                $user->createOrUpdate($nome, $login, $email, $setor, $senha);

                $noticias = 0;
                $saldos = 0;
                $pedidos = 0;
                $recepcao = 0;

                if ($setor == 2) {
                    $noticias = !is_null(filter_input(INPUT_POST, 'noticias'));
                    $saldos = !is_null(filter_input(INPUT_POST, 'saldos'));
                    $pedidos = !is_null(filter_input(INPUT_POST, 'pedidos'));
                    $recepcao = !is_null(filter_input(INPUT_POST, 'recepcao'));
                    $aihs = !is_null(filter_input(INPUT_POST, 'aihs'));

                    if ($recepcao) {
                        $noticias = 0;
                        $saldos = 0;
                        $pedidos = 0;
                    }

                    if ($noticias || $saldos || $pedidos) {
                        $recepcao = 0;
                    }
                }

                $user->setPermissions($noticias, $saldos, $pedidos, $recepcao, $aihs);

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

            // resetando senha

            case 'resetSenha':
                $email = '';
                $input = filter_input(INPUT_POST, 'email');
                if (!empty($filter)) {
                    $email = $input;
                }
                // gera uma senha aleatória (sem criptografia)
                $senha = Util::criaSenha();

                $reset = Geral::resetSenha($email, $senha);

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
