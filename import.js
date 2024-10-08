const id_item_processo = 0;
const id_item_contrato = 1;
const cod_despesa = 2;
const descr_despesa = 3;
const descr_tipo_doc = 4;
const num_contrato = 5;
const num_processo = 6;
const descr_mod_compra = 7;
const num_licitacao = 8;
const dt_inicio = 9;
const dt_fim = 10;
const dt_geracao = 11;
const cgc_fornecedor = 12;
const nome_fornecedor = 13;
const num_extrato = 14;
const cod_estruturado = 15;
const nome_unidade = 16;
const cod_reduzido = 17;
const complemento_item = 18;
const descricao = 19;
const id_extrato_contr = 20;
const vl_unitario = 21;
const qt_contrato = 22;
const vl_contrato = 23;
const qt_utilizado = 24;
const vl_utilizado = 25;
const qt_saldo = 26;
const vl_saldo = 27;
const id_unidade = 28;
const ano_orcamento = 29;
const seq_item_processo = 32;

const CSVsaida = document.getElementById('CSVsaida');

const leitorDeCSV = new FileReader();

window.onload = function init() {
    leitorDeCSV.onload = leCSV;
}

function pegaCSV(inputFile) {
    CSVsaida.innerHTML += "<p>Novo arquivo selecionado</p>";
    const file = inputFile.files[0];
    leitorDeCSV.readAsText(file);
}

function mysql_real_escape_string(str) {
    return str.replace(/[\0\x08\x09\x1a\n\r"'\\\%]/g, function (char) {
        switch (char) {
            case "\0":
                return "\\0";
            case "\x08":
                return "\\b";
            case "\x09":
                return "\\t";
            case "\x1a":
                return "\\z";
            case "\n":
                return "\\n";
            case "\r":
                return "\\r";
            case "\"":
            case "'":
            case "\\":
            case "%":
                return "\\" + char; // prepends a backslash to backslash, percent,
                                    // and double/single quotes
        }
    });
}

function dateFormat(data) {
    const array_data = data.split('/');
    if (array_data.length === 3) {
        return array_data[2] + '-' + array_data[1] + '-' + array_data[0];
    }
    return data;
}

function prepareImport(data) {
    const array_sql = [];
    const insert = 'INSERT IGNORE INTO itens VALUES';
    let values = "";
    let row = 1, i = 0;
    const len = data.length;

    let lines = 0;
    for (let a = 0; a < len; a++) {
        row++;
        const dtInicio = dateFormat(data[a][dt_inicio]);
        const dtFim = dateFormat(data[a][dt_fim]);
        const dtGeracao = dateFormat(data[a][dt_geracao]);

        // chave
        let chave = data[a][num_processo] + '#' + data[a][cod_reduzido] + '#' + data[a][seq_item_processo];

        values += "\n(NULL, " + data[a][id_item_processo] + ", " + data[a][id_item_contrato] + ", \"" + data[a][cod_despesa] + "\", \"" + data[a][descr_despesa] + "\", \"" + data[a][descr_tipo_doc] + "\", \"" + data[a][num_contrato] + "\", \"" + data[a][num_processo] + "\", \"" + data[a][descr_mod_compra] + "\", \"" + data[a][num_licitacao] + "\", \"" + dtInicio + "\", \"" + dtFim + "\", \"" + dtGeracao + "\", \"" + data[a][cgc_fornecedor] + "\", \"" + data[a][nome_fornecedor] + "\", \"" + data[a][num_extrato] + "\", \"" + data[a][cod_estruturado] + "\", \"" + data[a][nome_unidade] + "\", \"" + data[a][cod_reduzido] + "\", \"" + data[a][complemento_item] + "\", \"" + data[a][descricao] + "\", \"" + data[a][id_extrato_contr] + "\", \"" + data[a][vl_unitario] + "\", " + data[a][qt_contrato] + ", \"" + data[a][vl_contrato] + "\", " + data[a][qt_utilizado] + ", \"" + data[a][vl_utilizado] + "\", " + data[a][qt_saldo] + ", \"" + data[a][vl_saldo] + "\", \"" + data[a][id_unidade] + "\", \"" + data[a][ano_orcamento] + "\", 0, \"" + chave + "\", \"" + data[a][seq_item_processo] + "\")";

        lines++;

        if (row === 70) {
            array_sql[i] = insert + values + ';';
            values = "";
            i++;
            row = 1;
        } else if (a !== len - 1) {
            values += ", ";
        }
    }
    console.log('importing lines: ' + lines);
    array_sql[i] = insert + values + ';';
    return array_sql;
}

function send(data) {
    CSVsaida.innerHTML += "<p>Enviando. AGUARDE...</p>";
    $.post('php/geral.php', {
        form: 'importItensTest',
        array_sql: data
    }).done(function (resposta) {
        CSVsaida.innerHTML += "Processo finalizado!";
        alert("Resposta do servidor: " + resposta);
    });
}

function leCSV(evt) {
    CSVsaida.innerHTML += "<p>Lendo arquivo...</p>"
    const fileArr = evt.target.result.split('\n');

    CSVsaida.innerHTML += "<p>Linhas encontradas: " + fileArr.length + "</p>";
    let array = [];
    CSVsaida.innerHTML += "<p>Formatando...</p>";
    for (let i = 1; i < fileArr.length; i++) {
        const fileLine = fileArr[i].split('	');
        for (let j = 0; j < fileLine.length; j++) {
            fileLine[j] = fileLine[j].replace("\"", "'");
            fileLine[j] = mysql_real_escape_string(fileLine[j]);
        }
        array[i - 1] = fileLine;
    }

    CSVsaida.innerHTML += "<p>Preparando importação...</p>";
    const array_sql = prepareImport(array);
    CSVsaida.innerHTML += "<p>Preparação concluída</p>";
    send(array_sql);
}