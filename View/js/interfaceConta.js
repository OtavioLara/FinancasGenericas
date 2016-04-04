//Proprietário
function showModalProp() {
    $('#modalProp').modal("show");
}

function setProprietario() {
    var div = document.getElementById("modalProp");
    var radioSelected = div.querySelectorAll("input[name='integrantes']:checked");
    var values = radioSelected[0].value.split(";");
    document.getElementById("idProp").value = values[0];
    document.getElementById("nomeProp").value = values[1];
}
function removeLinha(botao) {
    var linhaTabela = botao.parentNode.parentNode;
    var tabela = linhaTabela.parentNode;
    tabela.removeChild(linhaTabela);
}
function adicionaProprietario() {

    /* Resgata campos */
    var inputNome = document.getElementById("nomeProp");
    var inputValor = document.getElementById("valorProp");
    var inputId = document.getElementById("idProp");
    var tabela = document.getElementById("tabelaProp");

    /* Cria campos necessários */
    var tfValor = "<input type='text' value='" + inputValor.value + "' name='valorProp[]' autocomplete='off' />";
    var tfId = "<input type='hidden' value='" + inputId.value + "' name='idProp[]' />";
    var btRemover = "<button type='button' onclick='removeLinha(this)'>remover</button>";
    
    /* Insere campos criados na tabela */
    var row = tabela.insertRow(1);
    var cell1 = row.insertCell(0);
    var cell2 = row.insertCell(1);
    var cell3 = row.insertCell(2);

    cell1.innerHTML = inputNome.value + tfId;
    cell2.innerHTML = tfValor;
    cell3.innerHTML = btRemover;

    /* Reseta campos */
    inputNome.value = "";
    inputValor.value = "";
    inputId.value = "";
}

//Itens da conta
function filtrar() {
    var div = document.getElementById("modalFiltro");
    var checkboxSelected = div.querySelectorAll("input[name='integrantes']:checked");
    var inputs = "";

    for (var i = 0; i < checkboxSelected.length; i++) {
        var values = checkboxSelected[i].value.split(";");
        var nome = values[1];

        inputs += "<input type='checkbox' name='distribuicao[]' "
                + "value='" + checkboxSelected[i].value + "' checked>"
                + nome;
    }
    document.getElementById("distribuicao").innerHTML = inputs;
}


function adicionarItem() {
    /* Resgata valores dos campos */
    var inputNome = document.getElementById("nomeItem");
    var inputValor = document.getElementById("valorItem");
    var inputIntegrantes = document.querySelectorAll("input[name='distribuicao[]']:checked");
    var tabela = document.getElementById("tabelaItem");

    /* Cria campos necessários para adicionar na tabela */
    var tfNome = "<input type='text' value='" + inputNome.value + "' name='nomeItem[]' autocomplete='off' />";
    var tfValor = "<input type='text' value='" + inputValor.value + "' name='valorItem[]' autocomplete='off' />";
    var btRemover = "<button type='button' onclick='removeLinha(this)' > Remover </button>";
    var btAdicionar = "<button type='button' onclick='geraConteudoModalDistribuicao(this.parentNode.parentNode.rowIndex-1)' > Adicionar </button>";
    var tfContador = "<input type='hidden' value='" + inputIntegrantes.length + "' name='contadorDistribuicao[]' />";
    var lista = "<ul name='listaDistribuicao[]'>";

    var valorItem = numeroControle(inputValor.value);
    var indexItem = document.getElementById("tabelaItem").rows.length - 1;
    var distribuicao = geraDistribuicao(valorItem, inputIntegrantes.length, indexItem);
    for (var i = 0; i < inputIntegrantes.length; i++) {
        var values = inputIntegrantes[i].value.split(";");
        var idIntegrante = values[0];
        var nomeIntegrante = values[1];
        var valorDistribuicao = numeroInterface(distribuicao[i]);

        lista += "<li>";
        lista += nomeIntegrante + ":<input type='text' value='" + valorDistribuicao + "' name='valorDistribuicaoItem[]' autocomplete='off' />";
        lista += "<button type='button' onclick='removerDistribuicao(this.parentNode)' >remover </button>";
        lista += "<input type='hidden' value='" + idIntegrante + "' name='idDistribuicaoItem[]' />";
        lista += "</li>";
    }
    lista += "</ul>";

    /* Insere campos criados na tabela */
    var row = tabela.insertRow(1);
    var cell1 = row.insertCell(0);
    var cell2 = row.insertCell(1);
    var cell3 = row.insertCell(2);
    var cell4 = row.insertCell(3);

    cell1.innerHTML = tfNome;
    cell2.innerHTML = tfValor;
    cell3.innerHTML = btAdicionar + lista + tfContador;
    cell4.innerHTML = btRemover;

}

//Distribuicao
function geraConteudoModalDistribuicao(linhaItem) {

    /* Resgata campos */
    var div = document.getElementById("integrantesASerAdicionado");
    var listaDistribuicao = document.getElementsByName("listaDistribuicao[]");
    var lista = listaDistribuicao[linhaItem];
    var inputIds = lista.querySelectorAll("input[name='idDistribuicaoItem[]']");
    var idsIntegrantes = [];
    for (var i = 0; i < inputIds.length; i++) {
        idsIntegrantes[i] = parseInt(inputIds[i].value);
    }
    /* Veririca quais integrantes não estão na lista */
    var listaCheckbox = "";
    for (var i = 0; i < todosIntegrantesId.length; i++) {
        if (idsIntegrantes.indexOf(todosIntegrantesId[i]) < 0) {
            var checkbox = "<input type='checkbox' value='" + todosIntegrantesId[i] + ";"
                    + todosIntegrantesNome[i] + "' name='integrantes' />" + todosIntegrantesNome[i] + "<br/>";
            listaCheckbox += checkbox;
        }
    }

    if (listaCheckbox != "") {
        var tfLinhaItem = "<input type='hidden' value='"+linhaItem+"' id='linhaItem' />";
        div.innerHTML = tfLinhaItem + listaCheckbox;
        $('#modalDistribuicao').modal("show");
    }
}
function modalDistribuicaoClick() {
    /* Resgata valores */
    var div = document.getElementById("modalDistribuicao");
    var checkSelected = div.querySelectorAll("input[name='integrantes']:checked");
    var linhaItem = parseInt(document.getElementById("linhaItem").value);
    
    /* Adiciona todos os checkbox selecionados na distribuição */
    for (var i = 0; i < checkSelected.length; i++) {
        var values = checkSelected[i].value.split(";");
        var idIntegrante = values[0];
        var nomeIntegrante = values[1];
        adicionarDistribuicao(linhaItem, idIntegrante, nomeIntegrante);
    }
}
function adicionarDistribuicao(linhaItem, idIntegrante, nomeIntegrante) {
    /* Resgata valores dos campos */
    var listaDistribuicao = document.getElementsByName("listaDistribuicao[]");
    var lista = listaDistribuicao[linhaItem];
    var li = document.createElement("li");
    var inputContador = document.getElementsByName("contadorDistribuicao[]");

    /* Cria campos necessários */
    var tfIdIntegrante = "<input type='text' value='" + idIntegrante + "' name='idDistribuicaoItem[]' />";
    var tfDistribuicao = "<input type='text' value='0' name='valorDistribuicaoItem[]' autocomplete='off' />";
    var btRemove = "<button type='button' onclick='removerDistribuicao(this.parentNode)' >remover </button>";
    
    /* Adiciona campos */
    li.innerHTML = nomeIntegrante + tfDistribuicao + btRemove + tfIdIntegrante;
    lista.appendChild(li);
    inputContador[linhaItem].value = parseInt(inputContador[linhaItem].value) + 1;
}

function removerDistribuicao(li) {
    var linhaItem = li.parentNode.parentNode.parentNode.rowIndex - 1;
    li.parentNode.removeChild(li);
    var contador = document.getElementsByName("contadorDistribuicao[]");
    contador[linhaItem].value = parseInt(contador[linhaItem].value) - 1;
}

//Verificacao
function somaValores(inputs) {
    var somatorio = 0;
    for (var i = 0; i < inputs.length; i++) {
        var valor = numeroControle(inputs[i].value)
        somatorio += parseFloat(valor);
        somatorio = parseFloat(somatorio.toFixed(2));
    }
    return somatorio;
}

function check() {

    var valorProp = document.getElementsByName("valorProp[]");
    var inputValorItem = document.getElementsByName("valorItem[]");
    var listaDistribuicao = document.getElementsByName("listaDistribuicao[]");
    var erro = false;
    var valorTotal = 0;

    
    if (document.getElementById("nomeConta").value == '') {
        alert('O campo "nome conta" está em branco.');
        erro = true;
    }
    
    if (document.getElementById("tabelaProp").rows.length == 1) {
        alert('Adiciona algum proprietário(dono) à conta.');
        erro = true;
    }
    
    for (var i = 0; i < inputValorItem.length; i++) {
        var valorDistribuicaoItem = listaDistribuicao[i].querySelectorAll("input[name='valorDistribuicaoItem[]']");
        var valorTotalDistribuicao = 0;
        for (var j = 0; j < valorDistribuicaoItem.length; j++) {
            var valor = numeroControle(valorDistribuicaoItem[j].value);
            valorTotalDistribuicao += valor;
            valorTotalDistribuicao = parseFloat(valorTotalDistribuicao.toFixed(2));
        }
        var color;
        var valorItem = numeroControle(inputValorItem[i].value);
        if (valorTotalDistribuicao != valorItem) {
            color = "red";
            erro = true;
        } else {
            color = "white";
        }
        for (var j = 0; j < valorDistribuicaoItem.length; j++) {
            valorDistribuicaoItem[j].style.backgroundColor = color;
        }
        valorTotal += numeroControle(inputValorItem[i].value);
        valorTotal = parseFloat(valorTotal.toFixed(2));
    }
    
    var valorPropTotal = somaValores(valorProp);
    if (valorTotal != valorPropTotal) {
        alert('Valor Prop: ' + valorPropTotal + " Valor Total Itens: " + valorTotal);
        erro = true;
    }
    
    return !erro;

}


function setInputsPagar() {
    /* Resgata valores */
    var div = document.getElementById("modalPagar");
    var radioSelected = div.querySelectorAll("input[name='integrantes[]']:checked");
    var inputIdPagador = document.getElementById("idPagador");
    var inputValorPago = document.getElementById("valorPago");
    
    var values = radioSelected[0].value.split(";");
    var idPagador = values[0];
    var valorDivida = numeroControle(values[1]);
    var valorPago = numeroControle(inputValorPago.value);
    inputIdPagador.value = values[0];
    
    if (valorPago >= valorDivida) {
        inputValorPago.value = numeroInterface(valorDivida);
    }
    return true;

}
