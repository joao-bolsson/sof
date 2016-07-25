<?php include_once 'Conexao.class.php';class _1 extends Conexao{private $_2,$_3;function __construct(){session_start();parent::__construct();$this->_3=parent::getConexao();}public function _4($_5){$_6=date("n");$_7=$this->_3->query("SELECT saldo FROM saldo_setor WHERE id_setor = {$_5} AND mes = {$_6};");$_8=$_7->fetch_object();return $_8->saldo;}public function _9(){$_7=$this->_3->query("SELECT count(id) AS count FROM setores");$_10=$_7->fetch_object();$_11=$_10->_11;$_7->close();return $_11;}public function _12($_13){$_13++;return"
          <div id=\"file-$_13\" class=\"tile\">
            <div class=\"tile-side pull-left\">
              <div class=\"avatar avatar-sm avatar-brand\">
                <span class=\"icon\">backup</span>
              </div>
            </div>
            <div class=\"tile-action tile-action-show\">
              <ul class=\"nav nav-list margin-no pull-right\">
                <li>
                  <a class=\"text-black-sec waves-attach\" href=\"javascript:dropTile('file-$_13');\"><span class=\"icon\">delete</span></a>
                </li>
              </ul>
            </div>
            <div class=\"tile-inner\">
              <input id=\"arq-$_13\" class=\"btn btn-default btn-file\" type=\"file\" name=\"file-$_13\" style=\"text-transform: none !important;\">
            </div>
          </div>
          ";}public function _14(){$_15="";$_7=$this->_3->query("SELECT id, login FROM usuario;");while($_16=$_7->fetch_object()){$_15.="
              <option value=\"{$_16->_18}\">{$_16->login}</option>
            ";}return $_15;}public function _17($_18,$_19){$_15="";$_7=$this->_3->query("SELECT postagem FROM {$_19} WHERE id = {$_18}");$_20=$_7->fetch_object();return html_entity_decode($_20->_23);}public function _21($_5,$_19){$_15="";$_7=$this->_3->query("SELECT postagens.id_postagem, postagens.titulo, postagens.data FROM postagens WHERE postagens.tabela = '{$_19}' AND ativa = 1 ORDER BY data DESC;");$_22=0;while($_23=$_7->fetch_object()){$_24=str_split($_23->_23);$_15.="<tr><td>";$_15.=html_entity_decode($_23->_35);$_25=date('d/m/Y',strtotime($_23->_25));$_15.="<button class=\"btn btn-flat btn-sm\" style=\"text-transform: lowercase !important;font-weight: bold;\" onclick=\"ver_noticia({$_23->id_postagem}, '{$_19}');\">...ver mais</button></td>";$_15.="<td><span style=\"font-weight: bold;\" class=\"pull-right\">$_25</span></td></tr>";}return $_15;}public function _26($_5,$_27){$_28="";if($_27==1){$_28="postagens.data DESC LIMIT 5;";}else{$_28="rand() LIMIT 5;";}$_15="";$_29=array("primeira","segunda","terceira","quarta","quinta");$_30=array("primeiro","segundo","terceiro","quarto","quinto");$_31=$this->_3->query("SELECT postagens.id_postagem, postagens.tabela, postagens.titulo FROM postagens WHERE postagens.ativa = 1 ORDER BY {$_28}");$_32=0;while($_23=$_31->fetch_object()){$_33=$this->_3->query("SELECT postagem, data FROM {$_23->_19} WHERE id = {$_23->id_postagem}")->fetch_object();$_24=str_split($_23->_35);$_34=strlen($_23->_35);$_35="";for($_22=0;$_22<$_34;$_22++){$_35.=$_24[$_22];}$_24=str_split($_33->_23);$_34=strpos($_33->_23,"<img");$_36="../sof_files/logo_blue.png";if($_34!==false){$_34=strpos($_33->_23,"src=\"");$_36="";$_22=$_34+5;while($_24[$_22]!="\""){$_36.=$_24[$_22];$_22++;}}$_37="550";$_34=strpos($_33->_23,"width: ");$_38=strpos($_33->_23,"px;");if($_23->_19!="noticia"||$_23->_18!=8){if($_34!==false){if($_38!==false){for($_22=$_34;$_22<$_38;$_22++){$_37.=$_24[$_22];}}}}$_25=date('d/m/Y',strtotime($_33->_25));$_15.="
              <li id=\"{$_30[$_32]}\" class=\"{$_29[$_32]}-anima\">
                <div class=\"card-img\">
                  <img style=\"width: {$_37}px; height: 275px;\" src=\"$_36\" >
                  <a href=\"../php/busca.php?ver_noticia=1&slide=1&id={$_23->id_postagem}&tabela={$_23->_19}\" class=\"card-img-heading padding\" style=\"font-weight: bold;\">$_35<span class=\"pull-right\">{$_25}</span></a>
                </div>
              </li>
              ";$_32++;}return $_15;}public function _39($_40){$_15="";$_41='../uploads/';$_42=dir($_41);while($_43=$_42->read()){$_44=pathinfo($_41.$_43);$_45="label";if($_44["extension"]=="jpg"||$_44["extension"]=="png"||$_44["extension"]=="jpeg"){$_44="Imagem";$_45.=" label-brand";}else{$_44="Documento";}if($_43!="."&&$_43!=".."&&$_44!="Imagem"){$_15.="
              <tr>
                <td><span class=\"{$_45}\" style=\"font-size: 11pt !important; font-weight: bold;\">{$_44}</span></td>
                <td><a href=\"$_41$_43\">$_43</a></td>
                <td><button class=\"btn btn-flat waves-attach waves-effect\" onclick=\"delArquivo('$_41$_43');\"><span class=\"icon\">delete</span><span style=\"font-weight:bold;\">Excluir</span></button></td>
              </tr>
              ";}}$_42->close();return $_15;}public function _46($_40,$_5){$_15="";$_40=htmlentities($_40);$_40=$this->_3->real_escape_string($_40);$_15="
        <div class=\"card\">
              <div class=\"card-main\">
                <div class=\"card-header card-brand\">
                  <div class=\"card-header-side pull-left\">
                    <p class=\"card-heading\">Publicações</p>
                  </div>
                </div><!--  ./card-header -->
                <div class=\"card-inner margin-top-no\">
                  <div class=\"card-table\">
                    <div class=\"table-responsive\">
                      <table class=\"table\">
                        <thead>
                          <th>Título</th>
                          <th class=\"pull-right\">Data de Publicação</th>
                        </thead>
                        <tbody>
        ";if($_40==""){$_15.="
          <tr>
            <td collspan=\"2\">Digite algo para pesquisar...</td>
            <td></td>
          </tr>
          ";}else{$_7=$this->_3->query("SELECT postagens.* FROM postagens WHERE postagens.titulo LIKE '%{$_40}%' AND postagens.ativa = 1 ORDER BY postagens.data DESC;");if($_7->num_rows==0){$_15.="
          <tr>
            <td collspan=\"2\">Nenhum resultado para '{$_40}'</td>
            <td></td>
          </tr>
          ";}else{while($_23=$_7->fetch_object()){$_35=html_entity_decode($_23->_35);$_25=date('d/m/Y',strtotime($_23->_25));$_15.="
              <tr>
                <td>{$_35}<button class=\"btn btn-flat btn-sm\" style=\"text-transform: lowercase !important;font-weight: bold;\" onclick=\"window.location.href='../php/busca.php?ver_noticia=1&slide=1&id={$_23->id_postagem}&tabela={$_23->_19}'\">...ver mais</button></td>
                <td><span class=\"pull-right\">{$_25}</span></td>
              </tr>
            ";}}$_15.="
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div><!-- ./card-main -->
            </div> <!-- ./card -->
        ";}return $_15;}public function _47(){$_15="
                <nav class=\"tab-nav margin-top-no\">
                  <ul class=\"nav nav-justified\">
          ";$_48=$this->_3->query("SELECT tabela, nome FROM paginas_post;");while($_49=$_48->fetch_object()){$_15.="
                  <li>
                    <a class=\"waves-attach\" data-toggle=\"tab\" href=\"#{$_49->_19}\">{$_49->nome}</a>
                  </li>
              ";}$_48->close();$_15.="
                  </ul>
                </nav>
          ";$_50=$this->_3->query("SELECT tabela FROM paginas_post;");while($_51=$_50->fetch_object()){$_7=$this->_3->query("SELECT * FROM postagens WHERE tabela = '{$_51->_19}' AND ativa = 1 ORDER BY data DESC");$_15.="
                <div class=\"tab-pane fade\" id=\"{$_51->_19}\">
                  <table class=\"table\">
                  <thead>
                    <th>Título</th>
                    <th>Data de Publicação</th>
                    <th>Opções</th>
                  </thead>
                  <tbody>
              ";if($_7->num_rows>0){while($_52=$_7->fetch_object()){$_25=date('d/m/Y',strtotime($_52->_25));$_15.="
                  <tr>
                    <td>{$_52->_35}</td>
                    <td>{$_25}</td>
                    <td>
                    <button class=\"btn btn-default btn-sm\" style=\"text-transform: none !important;font-weight: bold;\" onclick=\"editaNoticia({$_52->id_postagem}, '{$_51->_19}', '{$_52->_25}')\" title=\"Editar\"><span class=\"icon\">create</span></button>
                    <button class=\"btn btn-default btn-sm\" style=\"text-transform: none !important;font-weight: bold;\" onclick=\"excluirNoticia({$_52->id_postagem}, '{$_51->_19}')\" title=\"Excluir\"><span class=\"icon\">delete</span></button>
                    </td>
                  </tr>
                  ";}}else{$_15.="<tr><td collspan=\"3\">Nenhuma publicação</td><td></td><td></td><td></td></tr>";}$_15.="</tbody></table></div>";$_7->close();}$_50->close();return $_15;}public function _53($_18,$_19){$_18=$this->_3->real_escape_string($_18);$_19=$this->_3->real_escape_string($_19);$_15="";$_52=$this->_3->query("SELECT postagem FROM {$_19} WHERE id={$_18}")->fetch_object();return $_52->_23;}public function _54(){$_15="";$_7=$this->_3->query("SELECT tabela, nome FROM paginas_post");while($_55=$_7->fetch_object()){$_15.="
                <option id=\"op{$_55->_19}\" value=\"{$_55->_19}\">{$_55->nome}</option>
              ";}$_7->close();return $_15;}public function _56($_40){$_15="";$_7=$this->_3->query("SELECT id, id_item_processo, nome_fornecedor, cod_reduzido, complemento_item, vl_unitario, qt_contrato FROM itens WHERE num_processo LIKE '%{$_40}%';");while($_57=$_7->fetch_object()){$_57->complemento_item=str_replace("\"","",$_57->complemento_item);$_57->vl_unitario=str_replace(",",".",$_57->vl_unitario);$_57->nome_fornecedor=utf8_encode($_57->nome_fornecedor);$_57->complemento_item=utf8_encode($_57->complemento_item);$_15.="
              <tr>
                <td>
                  <a class=\"modal-close\" href=\"javascript:checkItemPedido({$_57->_18}, '{$_57->vl_unitario}');\"><span class=\"icon\">add<span></a>
                  
                </td>                <td>{$_57->nome_fornecedor}</td>
                <td>{$_57->cod_reduzido}</td>
                <td><input type=\"number\" id=\"qtd{$_57->_18}\"></td>
                <td>
                <a onclick=\"viewCompl('{$_57->complemento_item}');\" class=\"btn btn-flat waves-attach waves-effect\" type=\"button\" title=\"Mais Detalhes\">complemento_item</a>
                </td>
                <td style=\"display: none;\">{$_57->complemento_item}</td>
                <td>{$_57->vl_unitario}</td>
                <td>{$_57->qt_contrato}</td>
                <td><button onclick=\"javascript:void();\" class=\"btn btn-brand waves-attach waves-effect\" type=\"button\" title=\"Mais Detalhes\"><span class=\"icon icon-lg\">more_horiz</span></button></td>
              </tr>
            ";}$_7->close();return $_15;}public function _58($_59,$_13){$_7=$this->_3->query("SELECT id, nome_fornecedor, num_licitacao, cod_reduzido, complemento_item, vl_unitario FROM itens WHERE id = {$_59}");$_57=$_7->fetch_object();$_57->complemento_item=str_replace("\"","",$_57->complemento_item);$_57->vl_unitario=str_replace(",",".",$_57->vl_unitario);$_60=$_13*$_57->vl_unitario;$_15="
            <tr id=\"row{$_59}\">
              <td><a class=\"modal-close\" href=\"javascript:removeTableRow($_59, '$_60');\"><span class=\"icon\">delete</span></a></td>
              <td>{$_57->cod_reduzido}</td>
              <td>
              <button onclick=\"viewCompl('{$_57->complemento_item}');\" class=\"btn btn-flat waves-attach waves-effect\" type=\"button\" title=\"Ver Complemento do Item\">complemento_item</button>
              </td>
              <td>R$ {$_57->vl_unitario}</td>
              <td>{$_57->nome_fornecedor}</td>
              <td>{$_57->num_licitacao}</td>
              <td>{$_13}</td>
              <td>R$ {$_60}</td>
              <td>
                <input type=\"hidden\" name=\"id_item[]\" value=\"{$_59}\">
                <input type=\"hidden\" name=\"qtd[]\" value=\"{$_13}\">
                <input type=\"hidden\" name=\"valor[]\" value=\"{$_60}\">
              </td>
            </tr>
          ";$_7->close();return $_15;}public function _61($_5){$_15="";$_7=$this->_3->query("SELECT id, data_pedido, ref_mes, valor FROM pedido WHERE id_setor = {$_5} AND alteracao = 1 and status = 'Rascunho' AND prioridade = 'rascunho'");while($_62=$_7->fetch_object()){$_25=date('d/m/Y',strtotime($_62->data_pedido));$_15.="
                <tr>
                  <td>{$_62->ref_mes}</td>
                  <td>{$_25}</td>
                  <td>R$ {$_62->_60}</td>
                  <td>
                  <button class=\"btn btn-default btn-sm\" style=\"text-transform: none !important;font-weight: bold;\" onclick=\"editaPedido({$_62->_18});\" title=\"Editar\"><span class=\"icon\">create</span></button>
                  </td>
                </tr>
              ";}$_7->close();return $_15;}public function _63($_64){$_15="";$_7=$this->_3->query("SELECT itens_pedido.id AS id_itens_pedido, itens_pedido.qtd AS qtd_solicitada, itens_pedido.valor, itens.nome_fornecedor, itens.num_licitacao, itens.cod_reduzido, itens.complemento_item, itens.vl_unitario FROM itens_pedido, itens WHERE itens_pedido.id_pedido = 2 AND itens_pedido.id_item = itens.id");while($_57=$_7->fetch_object()){$_59=$_57->id_itens_pedido;$_57->complemento_item=str_replace("\"","",$_57->complemento_item);$_57->vl_unitario=str_replace(",",".",$_57->vl_unitario);$_60=$_57->qtd_solicitada*$_57->vl_unitario;$_15.="
                <tr id=\"row{$_59}\">
                  <td><a class=\"modal-close\" href=\"javascript:removeTableRow($_59, '$_60');\"><span class=\"icon\">delete</span></a></td>
                  <td>{$_57->cod_reduzido}</td>
                  <td>
                  <button onclick=\"viewCompl('{$_57->complemento_item}');\" class=\"btn btn-flat waves-attach waves-effect\" type=\"button\" title=\"Ver Complemento do Item\">complemento_item</button>
                  </td>
                  <td>R$ {$_57->vl_unitario}</td>
                  <td>{$_57->nome_fornecedor}</td>
                  <td>{$_57->num_licitacao}</td>
                  <td>{$_57->qtd_solicitada}</td>
                  <td>R$ {$_60}</td>
                  <td>
                    <input type=\"hidden\" name=\"id_item[]\" value=\"{$_59}\">
                    <input type=\"hidden\" name=\"qtd[]\" value=\"{$_13}\">
                    <input type=\"hidden\" name=\"valor[]\" value=\"{$_60}\">
                  </td>
                </tr>
              ";}$_7->close();return $_15;}public function _65($_64){$_7=$this->_3->query("SELECT saldo_setor.saldo, pedido.ref_mes FROM saldo_setor, pedido WHERE saldo_setor.id_setor = 3 AND saldo_setor.mes = 5 AND pedido.id = {$_64};");$_66=$_7->fetch_object();$_7->close();return json_encode($_66);}} ?>