<?php
/**
 *	Arquivo com as defines utilizadas no sistema
 *
 *	@author João Bolsson
 *
 */
ini_set('display_erros', true);
error_reporting(E_ALL);

/*
CUIDADO!

cuidado ao alterar o nome dos arquivos em view, pois esses nomes estão diretamente
relacionados com o banco na tabela paginas_post, bem como os nomes das tabelas
ver getPostarEm() em class/Busca.class.php e ver_noticia() em ini.js

 */

// criando defines para os LINKS dos arquivos em view/
define('HREF_BOAS_PRATICAS', 'boaspraticas.php');
define('HREF_COMEMORACOES', 'comemoracoes.php');
define('HREF_CONSULTAS_PE', 'consultaspe.php');
define('HREF_DINAMICAS', 'dinamicas.php');
define('HREF_FALE_CONOSCO', 'faleconosco.php');
define('HREF_INDEX', 'index.php');
define('HREF_LINKS_EXTERNOS', 'linksexternos.php');
define('HREF_LINKS_INTERNOS', 'linksinternos.php');
define('HREF_NOTICIAS', 'noticia.php');
define('HREF_RECEPCAO', 'recepcao.php');
define('HREF_REL1', 'rel1.php');
define('HREF_RELATORIOS', 'relatorios.php');
define('HREF_SOF', 'sof.php');
define('HREF_SOLICITACOES', 'solicitacoes.php');
define('HREF_TUTORIAIS', 'tutoriais.php');
define('HREF_UNIDADES', 'unidades.php');
define('HREF_VER_NOTICIA', 'ver_noticia.php');

/* não há necessidade -> alterar as ocorrencias nos arquivos em view/ */
define('HREF_MODAL_LOGIN', "javascript:abreModal('#login');");
define('HREF_MODAL_CONSTRUINDO', "javascript:abreModal('#construindo');");
define('HREF_MODAL_PESQUISA', "javascript:abreModal('#modalPesquisar')");

?>