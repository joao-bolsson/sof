function mostra(row, id_icon) {
	var display = document.getElementById(row).style.display;
	var icon = '';
	if (display == 'block') {
		display = 'none';
		icon = 'keyboard_arrow_down';
	} else {
		display = 'block';
		icon = 'keyboard_arrow_up';
	}
	document.getElementById(row).style.display = display;
	document.getElementById(id_icon).innerHTML = icon;
}

function mostraSolicAdiant() {
	mostra('rowSolicAdi', 'iconSolicAdi');
	iniTableSolicAdiant();
}

function mostraSolicAltPed() {
	mostra('rowAltPed', 'iconSolicAlt');
	iniTableSolicAltPed();
}
// solicitação de alteração de pedidos
function analisaSolicAlt(id_solic, id_pedido, acao) {
	$.post('../php/geral.php', {
		admin: 1,
		form: 'analisaSolicAlt',
		id_solic: id_solic,
		id_pedido: id_pedido,
		acao: acao
	}, function(resposta) {
		// Quando terminada a requisição
		// Se a resposta é um erro
		if (resposta == false) {
			// Exibe o erro na div
			alert(resposta);
			window.location.href = 'index.php';
		}
		// Se resposta for false, ou seja, não ocorreu nenhum erro
		else {
			iniSolicitacoes();
			iniTableSolicAltPed();
		}
	});
}

function abreModal(id_modal) {
	$(id_modal).modal();
}

$('.modal').on('hidden.bs.modal', function(event) {
	$(this).removeClass('fv-modal-stack');
	$('body').data('fv_open_modals', $('body').data('fv_open_modals') - 1);
});

$('.modal').on('shown.bs.modal', function(event) {
	// keep track of the number of open modals
	if (typeof($('body').data('fv_open_modals')) == 'undefined') {
		$('body').data('fv_open_modals', 0);
	}
	// if the z-index of this modal has been set, ignore.
	if ($(this).hasClass('fv-modal-stack')) {
		return;
	}
	$(this).addClass('fv-modal-stack');
	$('body').data('fv_open_modals', $('body').data('fv_open_modals') + 1);
	$(this).css('z-index', 1040 + (10 * $('body').data('fv_open_modals')));
	$('.modal-backdrop').not('.fv-modal-stack')
		.css('z-index', 1039 + (10 * $('body').data('fv_open_modals')));
	$('.modal-backdrop').not('fv-modal-stack')
		.addClass('fv-modal-stack');
});

function avisoSnack(aviso, corpo) {
	$(corpo).snackbar({
		content: aviso,
		show: function() {
			snackbarText++;
		}
	});
}

function iniDataTable(tabela) {
	$(tabela).DataTable({
		destroy: true,
		searching: true,
		language: {
			"decimal": "",
			"emptyTable": "Nenhum dado na tabela",
			"info": "_MAX_ resultados encontrados",
			"infoEmpty": "",
			"infoFiltered": "",
			"infoPostFix": "",
			"thousands": ",",
			"lengthMenu": "Monstrando _MENU_ entradas",
			"loadingRecords": "Carregando...",
			"processing": "Processando...",
			"search": "Pesquisar:",
			"zeroRecords": "Nenhum resultado encontrado",
			"paginate": {
				"first": "Primeiro",
				"last": "Último",
				"next": "Próximo",
				"previous": "Anterior"
			},
			"aria": {
				"sortAscending": ": activate to sort column ascending",
				"sortDescending": ": activate to sort column descending"
			}
		},
		"lengthMenu": [
			[5, 10, 25, 50, -1],
			[5, 10, 25, 50, "All"]
		],
		"scrollY": "400px",
		"scrollCollapse": true,
		"paging": false,
		"scrollX": true
	});
}

function resetSenha() {
	document.getElementById("loaderResetSenha").style.display = 'inline-block';
	var email = document.getElementById('userReset').value;
	$.post('../php/geral.php', {
		form: 'resetSenha',
		email: email
	}, function(resposta) {
		if (resposta) {
			alert("Sua senha foi resetada e enviada para o seu e-mail.");
		} else {
			alert("Ocorreu um erro no servidor. Contate o administrador.");
		}
		$('#esqueceuSenha').modal('hide');
		document.getElementById('formReset').reset();
		document.getElementById("loaderResetSenha").style.display = 'none';
	});
}

function altInfoUser() {
	document.getElementById("loader").style.display = 'inline-block';
	var nome = document.getElementById('nameUser').value;
	var email = document.getElementById('emailUser').value;
	var senhaAtual = document.getElementById('senhaAtualUser').value;
	var novaSenha = document.getElementById('senhaUser').value;
	$.post('../php/geral.php', {
		users: 1,
		form: 'infoUser',
		nome: nome,
		email: email,
		novaSenha: novaSenha,
		senhaAtual: senhaAtual
	}, function(resposta) {
		if (resposta) {
			$('#myInfos').modal('hide');
			document.getElementById('altInfo').reset();
			avisoSnack('Suas informações foram salvas com sucesso!', 'body');
			document.getElementById('nameUser').value = nome;
			document.getElementById('emailUser').value = email;
			document.getElementById('userLogado').innerHTML = nome;
		} else {
			alert("Ocorreu um erro no servidor. Contate o administrador.");
			location.reload();
		}
		document.getElementById("loader").style.display = 'none';
	});
}

function carregaPostsPag(tabela) {
	document.getElementById('loader');
	$.post('../php/busca.php', {
		admin: 1,
		form: 'carregaPostsPag',
		tabela: tabela
	}, function(resposta) {
		$('#tableNoticiasEditar').DataTable().destroy();
		document.getElementById('contNoticiasEditar').innerHTML = resposta;
		iniDataTable('#tableNoticiasEditar');
	});
}

function iniAdminSolicitacoes() {
	iniDataTable('#tableItensPedido');
	iniSolicitacoes();
	iniFreeSaldos();
	iniRecepcao();
	avisoSnack('Carregamento concluído !', 'body');
}

function iniRecepcao() {
	$.post('../php/busca.php', {
		admin: 1,
		form: 'tableRecepcao',
	}, function(resposta) {
		$('#tableRecepcao').DataTable().destroy();
		document.getElementById('conteudoRecepcao').innerHTML = resposta;
		iniDataTable('#tableRecepcao');
	});
}

function iniListProcessos() {
	$.post('../php/busca.php', {
		admin: 1,
		form: 'listProcessos',
	}, function(resposta) {
		$('#tableListProcessos').DataTable().destroy();
		document.getElementById('tbodyListProcessos').innerHTML = resposta;
		iniDataTable('#tableListProcessos');
	});
}

function addProcesso(numProcesso, id) {
	$('#addProcesso').modal();
	document.getElementById("id_processo").value = id;
	if (id === 0) {
		document.getElementById('num_processo').value = numProcesso;
		$('#divNumProc').addClass('control-highlight');
	} else {
		$.post('../php/busca.php', {
			admin: 1,
			form: 'addProcesso',
			id_processo: id
		}, function(resposta) {
			var obj = jQuery.parseJSON(resposta);
			document.getElementById("num_processo").value = obj.num_processo;
			document.getElementById("tipo").value = obj.tipo;
			document.getElementById("estante").value = obj.estante;
			document.getElementById("prateleira").value = obj.prateleira;
			document.getElementById("entrada").value = obj.entrada;
			document.getElementById("saida").value = obj.saida;
			document.getElementById("responsavel").value = obj.responsavel;
			document.getElementById("retorno").value = obj.retorno;
			document.getElementById("obs").value = obj.obs;
			$('#formProcesso .form-group').addClass('control-highlight');
		});
	}
}

function updateProcesso() {
	var campos = ["id_processo", "num_processo", "tipo", "estante", "prateleira", "entrada", "saida", "responsavel", "retorno", "obs"];
	var dados = [];
	for (var i = 0; i < campos.length; i++) {
		dados[i] = document.getElementById(campos[i]).value;
	}
	$.post('../php/geral.php', {
		admin: 1,
		form: 'recepcao',
		dados: dados
	}, function(resposta) {
		if (resposta === "true") {
			iniRecepcao();
			document.getElementById('formProcesso').reset();
			document.getElementById('id_processo').value = 0;
			iniListProcessos();
			$('#addProcesso').modal('hide');
			avisoSnack('Sucesso!');
		} else {
			alert('Ocorreu um erro no servidor. Contate o administrador');
			window.location.href = "sair.php";
		}
	});
}

function iniSolicitacoes() {
	$.post('../php/busca.php', {
		admin: 1,
		form: 'tableItensPedido'
	}, function(resposta) {
		$('#tableSolicitacoes').DataTable().destroy();
		document.getElementById('conteudoSolicitacoes').innerHTML = resposta;
		iniDataTable('#tableSolicitacoes');
	});
}

function formEnvia() {
	var id_pedido = document.getElementById("id_pedido_alt").value;
	var justificativa = document.getElementById("justificativa_alt_ped").value;
	$.post('../php/geral.php', {
		users: 1,
		form: 'alt_pedido',
		id_pedido: id_pedido,
		justificativa: justificativa
	}, function(resposta) {
		// Quando terminada a requisição
		// Se a resposta é um erro
		if (resposta == false) {
			// Exibe o erro na div
			alert(resposta);
			window.location.href = 'index.php';
		}
		// Se resposta for false, ou seja, não ocorreu nenhum erro
		else {
			alert("Sua solicitação será análisada. Caso seja aprovada, seu pedido estará na sessão 'Rascunhos'");
			$('#alt_pedido').modal('hide');
		}
	});
}

function solicAltPed(id_pedido) {
	document.getElementById('id_pedido_alt').value = id_pedido;
	abreModal('#alt_pedido');
	$('#div-lb-high').addClass('control-highlight');
}

function iniFreeSaldos() {
	$.post('../php/busca.php', {
		admin: 1,
		form: 'iniFreeSaldos'
	}, function(resposta) {
		document.getElementById('contFreeSaldos').innerHTML = resposta;
	});
}

function liberaSaldo(id_setor, mes, ano, valor) {
	valor = parseFloat(valor).toFixed(3);
	$.post('../php/geral.php', {
		admin: 1,
		form: 'liberaSaldo',
		id_setor: id_setor,
		mes: mes,
		ano: ano,
		valor: valor
	}, function(resposta) {
		// Quando terminada a requisição
		if (resposta) {
			alert("O saldo do setor acaba de receber R$ " + valor + " referente ao período " + mes + "/" + ano);
			iniFreeSaldos();
		} else {
			alert("Ocorreu um erro no servidor. Contate o administrador.");
			window.location.href = "sair.php";
		}
	});
}
$('#listSolicAltPedidos').on('shown.bs.modal', function(event) {
	if (!$.fn.DataTable.isDataTable('#tableSolicAltPedido')) {
		iniDataTable('#tableSolicAltPedido');
	}
});

function listSolicAltPedidos() {
	$('#listSolicAltPedidos').modal();
	if (!$.fn.DataTable.isDataTable('#tableListProcessos')) {
		$.post('../php/busca.php', {
			users: 1,
			form: 'iniSolicAltPedSetor'
		}, function(resposta) {
			document.getElementById('tbodySolicAltPedido').innerHTML = resposta;
		});
	}
}
$('#listProcessos').on('shown.bs.modal', function(event) {
	if (!$.fn.DataTable.isDataTable('#tableListProcessos')) {
		iniDataTable('#tableListProcessos');
	}
});

function listProcessos(permissao) {
	if (!$.fn.DataTable.isDataTable('#tableListProcessos')) {
		if (permissao == 'users') {
			$.post('../php/busca.php', {
				users: 1,
				form: 'listProcessos'
			}, function(resposta) {
				document.getElementById('tbodyListProcessos').innerHTML = resposta;
			});
		} else if (permissao == 'admin') {
			$.post('../php/busca.php', {
				admin: 1,
				form: 'listProcessos'
			}, function(resposta) {
				document.getElementById('tbodyListProcessos').innerHTML = resposta;
			});
		}
	}
	$('#listProcessos').modal('show');
}

$('#listAdiantamentos').on('shown.bs.modal', function(event) {
	if (!$.fn.DataTable.isDataTable('#tableListAdiantamentos')) {
		iniDataTable('#tableListAdiantamentos');
	}
});

function listAdiantamentos() {
	$('#listAdiantamentos').modal('show');
	if (!$.fn.DataTable.isDataTable('#tableListProcessos')) {
		$.post('../php/busca.php', {
			users: 1,
			form: 'listAdiantamentos'
		}, function(resposta) {
			document.getElementById('tbodyListAdiantamentos').innerHTML = resposta;
		});
	}
}
$('#listPedidos').on('shown.bs.modal', function(event) {
	if (!$.fn.DataTable.isDataTable('#tableListPedidos')) {
		iniDataTable('#tableListPedidos');
	}
});

function listPedidos() {
	$('#listPedidos').modal('show');
	if (!$.fn.DataTable.isDataTable('#tableListPedidos')) {
		$.post('../php/busca.php', {
			users: 1,
			form: 'listPedidos'
		}, function(resposta) {
			document.getElementById('tbodyListPedidos').innerHTML = resposta;
		});
	}
}
$('#listRascunhos').on('shown.bs.modal', function(event) {
	if (!$.fn.DataTable.isDataTable('#tableListRascunhos')) {
		iniDataTable('#tableListRascunhos');
	}
});

function listRascunhos() {
	$('#listRascunhos').modal('show');
	if (!$.fn.DataTable.isDataTable('#tableListRascunhos')) {
		$.post('../php/busca.php', {
			users: 1,
			form: 'listRascunhos'
		}, function(resposta) {
			document.getElementById('tbodyListRascunhos').innerHTML = resposta;
		});
	}
}

function iniPagSolicitacoes() {
	iniDataTable('#tableProcessos');
	avisoSnack('Carregamento concluído!', 'body');
}

function iniTableSolicAdiant() {
	var status = ['stabertos', 'staprovados', 'streprovado'];
	var st = null;
	for (var i = 0; i < status.length; i++) {
		var element = document.getElementById(status[i]);
		if (element.checked) {
			st = element.value;
		}
	}
	$.post('../php/busca.php', {
		admin: 1,
		form: 'tableSolicitacoesAdiantamento',
		status: st
	}, function(resposta) {
		$('#tableSolicitacoesAdiantamento').DataTable().destroy();
		document.getElementById('conteudoSolicitacoesAdiantamento').innerHTML = resposta;
		iniDataTable('#tableSolicitacoesAdiantamento');
	});
}

function iniTableSolicAltPed() {
	var table = 'tableSolicAltPedido';
	var status = ['stAltAbertos', 'stAltAprovados', 'stAltReprovado'];
	var st = null;
	for (var i = 0; i < status.length; i++) {
		var element = document.getElementById(status[i]);
		if (element.checked) {
			st = element.value;
		}
	}
	$.post('../php/busca.php', {
		admin: 1,
		form: 'iniTableSolicAltPed',
		status: st
	}, function(resposta) {
		$('#' + table).DataTable().destroy();
		document.getElementById('contSolicAltPedido').innerHTML = resposta;
		iniDataTable('#' + table);
	});
}

function analisaAdi(id, acao) {
	var aprova;
	if (acao) {
		aprova = confirm("O setor receberá o valor adiantado e terá esse mesmo valor descontado no próximo mês, estando sujeito à ficar com saldo negativo.\n\nDeseja continuar?");
	} else {
		aprova = confirm("A solicitação de adiantamento será reprovada e o saldo do setor não será alterado.\n\nDeseja continuar?");
	}
	if (aprova) {
		$.post('../php/geral.php', {
			admin: 1,
			form: 'aprovaAdi',
			id: id,
			acao: acao
		}, function(resposta) {
			iniTableSolicAdiant();
		});
	}
}

// FUNÇÃO DISPARADA QUANDO O ITEM É CLICADO P/ ADC ---------------------------------
function checkItemPedido(id_item, vl_unitario, qt_saldo) {
	qtd_item = document.getElementById("qtd" + id_item).value;
	if (qtd_item <= 0) {
		document.getElementById("qtd" + id_item).style.border = "0.12em solid red";
		document.getElementById("qtd" + id_item).focus();
	} else {
		//limpando os campos
		document.getElementById("qtd" + id_item).style.border = "none";
		document.getElementById("qtd" + id_item).value = "";
		//verifica se a qtd solicitada está disponível
		if (qtd_item > qt_saldo) {
			avisoSnack('QUANTIDADE INDISPONÍVEL !', 'body');
		} else {
			valor = parseFloat(qtd_item * vl_unitario).toFixed(3);
			total_pedido = document.getElementById('total_hidden').value;
			total_pedido += valor;

			saldo_total = parseFloat(document.getElementById("saldo_total").value);
			if (valor > saldo_total) {
				avisoSnack('SALDO INSUFICIENTE !', 'body');
			} else {
				addItemPedido(id_item, qtd_item, vl_unitario);
			}
		}
	}
}

// ADICIONA ITEM AO PEDIDO ---------------------------------------------------------
function addItemPedido(id_item, qtd, vl_unitario) {

	//valor do pedido
	var valor = qtd * vl_unitario;
	t = document.getElementById('total_hidden').value;
	total = parseFloat(t) + parseFloat(valor);
	document.getElementById('total_hidden').value = parseFloat(total).toFixed(3);
	var tot_str = parseFloat(total).toFixed(3);
	document.getElementById('total').value = "R$ " + tot_str;

	//saldo
	s = document.getElementById('saldo_total').value;
	saldo_total = parseFloat(s) - parseFloat(valor);
	document.getElementById('saldo_total').value = parseFloat(saldo_total).toFixed(3);
	document.getElementById('text_saldo_total').innerHTML = "R$ " + parseFloat(saldo_total).toFixed(3);

	$.post('../php/busca.php', {
		users: 1,
		form: 'addItemPedido',
		id_item: id_item,
		qtd: qtd
	}, function(resposta) {
		conteudoPedido = document.getElementById("conteudoPedido").innerHTML;
		document.getElementById("conteudoPedido").innerHTML = conteudoPedido + resposta;
		avisoSnack('Item Inserido ao Pedido !', 'body');
	});
}

function removeTableRow(id_item, valor) {
	//valor do pedido
	t = document.getElementById('total_hidden').value;
	total = parseFloat(t) - parseFloat(valor);
	document.getElementById('total_hidden').value = parseFloat(total).toFixed(3);
	document.getElementById('total').value = "R$ " + parseFloat(total).toFixed(3);

	//saldo
	s = document.getElementById('saldo_total').value;
	saldo_total = parseFloat(s) + parseFloat(valor);
	document.getElementById('saldo_total').value = parseFloat(saldo_total).toFixed(3);
	document.getElementById('text_saldo_total').innerHTML = "R$ " + parseFloat(saldo_total).toFixed(3);

	var row = document.getElementById("row" + id_item);
	if (row.parentNode) {
		row.parentNode.removeChild(row);
	}
	avisoSnack('Item Removido do Pedido !', 'body');
}
// VISUALIZAR O COMPLEMENTO DO ITEM ------------------------------------------------
function viewCompl(texto) {
	document.getElementById("complementoItem").innerHTML = texto;
	$('#viewCompl').modal();
}
// PESQUISA POR UM PROCESSO CONTENDO ITENS -----------------------------------------
function pesquisarProcesso(busca) {
	if (busca == 0) {
		busca = document.getElementById("searchProcesso").value;
	} else {
		document.getElementById("searchProcesso").value = busca;
		$('#listProcessos').modal('hide');
	}
	len = busca.length;
	if (len < 20) {
		avisoSnack('Digite um processo válido !', 'body');
	} else {
		$.post('../php/busca.php', {
			users: 1,
			form: 'pesquisarProcesso',
			busca: busca
		}, function(resposta) {
			$('#tableProcessos').DataTable().destroy();
			document.getElementById("conteudoProcesso").innerHTML = resposta;
			iniDataTable('#tableProcessos');
			avisoSnack('Busca Realizada com Sucesso !', 'body');
		});
	}
}
// AO CLICAR NO BOTÃO DE EDITAR RASCUNHO SALVO
function editaPedido(id_pedido) {
	$.post('../php/busca.php', {
		users: 1,
		form: 'populaRascunho',
		id_pedido: id_pedido
	}, function(retorno) {
		var obj = jQuery.parseJSON(retorno);

		//valor do pedido
		document.getElementById('total_hidden').value = obj.valor;
		document.getElementById('total').value = "R$ " + obj.valor;

		//saldo
		document.getElementById('saldo_total').value = parseFloat(obj.saldo - obj.valor).toFixed(3);
		document.getElementById('text_saldo_total').innerHTML = "R$ " + parseFloat(obj.saldo - obj.valor).toFixed(3);
	});
	document.getElementById("pedido").value = id_pedido;
	$('#listRascunhos').modal('hide');
	$.post('../php/busca.php', {
		users: 1,
		form: 'editaPedido',
		id_pedido: id_pedido
	}, function(resposta) {
		document.getElementById("conteudoPedido").innerHTML = resposta;
	});
}

// VERIFICA O LOGIN
function login() {
	// Colocamos os valores de cada campo em uma váriavel para facilitar a manipulação
	var user = document.getElementById("user").value;
	var senha = document.getElementById("senha").value;
	// Exibe mensagem de carregamento
	document.getElementById("loader").style.display = 'inline-block';
	$.post('../php/login.php', {
		login: user,
		senha: senha
	}, function(resposta) {
		// Quando terminada a requisição
		// Se a resposta é um erro
		if (resposta == "false") {
			document.getElementById("formLogin").reset();
			document.getElementById("aviso").style.display = 'flex';
		}
		// Se resposta for false, ou seja, não ocorreu nenhum erro
		else {
			//se sucesso, recarrega a página
			window.location.href = '../';
		}
		document.getElementById("loader").style.display = 'none';
	});
}

function aviso() {
	var tile = document.getElementById("aviso");
	if (tile.parentNode) {
		tile.parentNode.removeChild(tile);
	}
	document.getElementById("card").style.marginTop = "-3%";
}

function btnPesquisa() {
	if (document.getElementById("pesquisa").style.display == "" ||
		document.getElementById("pesquisa").style.display == "none") {
		$("#pesquisa").slideDown(200);
	} else {
		$("#pesquisa").slideUp(200);
	}
}

function ver_noticia(id, tabela, slide) {
	$.post('../php/busca.php', {
		form: 'ver_noticia',
		id: id,
		tabela: tabela,
		slide: slide
	}, function(resposta) {
		// Quando terminada a requisição
		window.location.href = 'ver_noticia.php';
	});
}

function addInputsArquivo() {
	qtd = document.getElementById('qtd-arquivos').value;
	$.post('../php/busca.php', {
		form: 'addInputsArquivo',
		qtd: qtd
	}, function(resposta) {
		// Quando terminada a requisição
		tabela_arq = document.getElementById('arquivos').innerHTML;
		document.getElementById('arquivos').innerHTML = tabela_arq + "" + resposta;
		document.getElementById('qtd-arquivos').value = parseInt(qtd) + 1;
	});
}

function pesquisar() {
	busca = document.getElementById("search").value;
	$.post('../php/busca.php', {
		form: 'pesquisar',
		busca: busca
	}, function(resposta) {
		document.getElementById("conteudo").innerHTML = resposta;
	});
}

function imprimir(id_pedido) {
	if (id_pedido == 0) {
		id_pedido = document.getElementById("pedido").value;
	}
	$.post('../php/busca.php', {
		users: 1,
		form: 'imprimirPedido',
		id_pedido: id_pedido
	}, function(resposta) {
		window.open("printPedido.php");
	});
}
// função para recepção para gerar relatórios
function print() {
	var tipo = document.getElementById('type').value;
	$.post('../php/busca.php', {
		admin: 1,
		form: 'relatorioProcessos',
		tipo: tipo
	}, function(resposta) {
		window.open("printRel.php");
	});
}

function analisarPedido(id_pedido, id_setor) {
	document.getElementById('id_setor').value = id_setor;
	$.post('../php/busca.php', {
		admin: 1,
		form: 'infoPedido',
		id_pedido: id_pedido,
		id_setor: id_setor
	}, function(retorno) {
		var obj = jQuery.parseJSON(retorno);
		//valor do pedido
		document.getElementById('total_hidden').value = obj.valor;

		//saldo
		document.getElementById('saldo_total').value = parseFloat(obj.saldo).toFixed(3);
		document.getElementById('text_saldo_total').innerHTML = "R$ " + parseFloat(obj.saldo).toFixed(3);

		//prioridade
		document.getElementById('prioridade').value = obj.prioridade;
		//status
		document.getElementById('st' + obj.status).checked = true;
	});

	document.getElementById("id_pedido").value = id_pedido;

	$.post('../php/busca.php', {
		admin: 1,
		form: 'analisaPedido',
		id_pedido: id_pedido
	}, function(resposta) {
		$('#tableItensPedido').DataTable().destroy();
		document.getElementById("conteudoPedido").innerHTML = resposta;
		iniDataTable('#tableItensPedido');
		avisoSnack('Busca Realizada com Sucesso !', 'body');
	});
}
// cancelar um item
function cancelaItem(id_item) {
	var icone = document.getElementById("icon-cancela-item" + id_item);
	var cancelado = document.getElementById("item_cancelado" + id_item).value;
	// SE O ITEM JÁ ESTÁ CANCELADO, ENTÃO DESCANCELAR
	if (cancelado == "1") {
		icone.innerHTML = "cancel";
		$("#icon-cancela-item" + id_item).removeClass('text-green').addClass('text-red');
		document.getElementById("item_cancelado" + id_item).value = 0;
		document.getElementById("row_item" + id_item).style.backgroundColor = "";
	} else {
		// AVISA O QUE ACONTECERÁ SE O ITEM FOR REMOVIDO
		var cancel = confirm("O item de id = " + id_item + " será desativado no Banco de Dados ao final da análise do pedido atual, e portanto, os setores não poderão mais solicitá-lo. Deseja prosseguir?");
		if (cancel) {
			icone.innerHTML = "check_circle";
			$("#icon-cancela-item" + id_item).removeClass('text-red').addClass('text-green');
			document.getElementById("item_cancelado" + id_item).value = 1;
			document.getElementById("row_item" + id_item).style.backgroundColor = "#ffe6e6";
		}
	}
}
//removendo um input de arquivo para adicionar notícias
function dropTile(id) {
	var tile = document.getElementById(id);
	if (tile.parentNode) {
		tile.parentNode.removeChild(tile);
	}
	qtd = document.getElementById('qtd-arquivos').value;
	document.getElementById('qtd-arquivos').value = parseInt(qtd) - 1;
}

function delArquivo(caminho) {
	del = confirm("Este arquivo será excluído PERMANENTEMENTE do sistema! Estando impossibilitada a sua recuperação.");
	if (del) {
		$.post('../php/geral.php', {
			admin: 1,
			form: 'delArquivo',
			caminhoDel: caminho
		}, function(resposta) {
			alert(resposta);
			window.location.href = "index.php";
		});
	}
}

function editaNoticia(id, tabela, data) {
	document.getElementById("funcao").value = "editar";
	document.getElementById("id_noticia").value = id;
	document.getElementById("tabela").value = tabela;
	document.getElementById("data").value = data;

	$.post('../php/busca.php', {
		admin: 1,
		form: 'editarNoticia',
		id: id
	}, function(resposta) {
		$('#txtnoticia').froalaEditor('destroy');
		document.getElementById("txtnoticia").value = "";
		$('#txtnoticia').froalaEditor({
			language: 'pt_br',
			charCounterCount: false,
			heightMin: 100,
			heightMax: 400,
			// Set the image upload URL.
			imageUploadURL: 'upload_image.php',
			// Set the file upload URL.
			fileUploadURL: 'upload_file.php',
			// Set the image upload URL.
			imageManagerLoadURL: 'load_images.php',
			// Set the image delete URL.
			imageManagerDeleteURL: 'delete_image.php',
		});
		$('#txtnoticia').froalaEditor('html.insert', resposta, true);
		tabela = "op" + document.getElementById("tabela").value;
		document.getElementById(tabela).selected = true;
		$('#listNoticias').modal('hide');
	});
}

function recarregaForm() {
	document.getElementById("funcao").value = "novanoticia";
	document.getElementById("id_noticia").value = 0;
	document.getElementById("tabela").value = 0;
}

function excluirNoticia(id) {
	var confirma = confirm("Essa notícia será desativada do sistema. Deseja continuar?");
	if (confirma) {
		$.post('../php/geral.php', {
			admin: 1,
			form: 'excluirNoticia',
			id: id
		}, function(resposta) {
			if (!resposta) {
				window.location.href = "../";
			} else {
				carregaPostsPag(resposta);
			}
		});
	}
}