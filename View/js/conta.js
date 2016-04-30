
function removeLinha(bt) {
    var td = bt.parentNode;
    var tr = td.parentNode;
    var table = tr.parentNode;
    table.removeChild(tr);
}


function somaValoresItens() {
    var soma = 0;
    $("input[name='valorItem[]']").each(function () {
        soma += numeroControle($(this).val());
        soma = parseFloat(soma.toFixed(2));
    });
    return soma;
}

function somaValoresPagosProprietario() {
    var soma = 0;
    $("input[name='valorPagoProprietario[]']").each(function () {
        soma += numeroControle($(this).val());
        soma = parseFloat(soma.toFixed(2));
    });
    return soma;
}

function tfValorAPagarUsuarioItemOnChange(tf) {
    var ul = tf.parentNode.parentNode.parentNode.parentNode;
    var divAlerta = ul.parentNode.querySelectorAll("div[name='divAlerta[]']")[0];
    var tr = ul.parentNode.parentNode;

    var campoValorItem = tr.querySelectorAll("input[name='valorItem[]']")[0];
    var camposDistribuicao = ul.querySelectorAll("input[name='valorPagoUsuarioItem[]']");
    var valorTotal = 0;
    var todosSaoNumeros = true;

    for (var i = 0; i < camposDistribuicao.length; i++) {
        var valor = numeroControle(camposDistribuicao[i].value);
        if (isNaN(valor)) {
            camposDistribuicao[i].style.backgroundColor = "#fc4465";
            todosSaoNumeros = false;
        } else {
            camposDistribuicao[i].style.backgroundColor = "white";
            valorTotal += valor;
            valorTotal = parseFloat(valorTotal.toFixed(2));
        }
    }
    if (todosSaoNumeros && parseFloat(campoValorItem.value) != valorTotal) {
        divAlerta.innerHTML = "Valores da distribuição estão errado";

    } else {
        divAlerta.innerHTML = "";
    }
}

$(function () {
    /* Checkin */
    function mostraModalAlerta(msgs) {
        var msg = "<ul> ";
        for (var i = 0; i < msgs.length; i++) {
            msg += "<li> " + msgs[i] + "</li>";
        }
        msg += "</ul>";
        $("#avisoModal").html(msg);
        $('#modalErroAviso').modal('show');
    }
    $("#btCadastrarConta").click(function () {
        var contErros = 0;
        var msgs = [];

        var nomeConta = $("#nomeConta").val();
        if (nomeConta == "") {
            msgs[contErros] = "Preencha o nome da conta!";
            contErros++;
        }

        if ($("#tabelaProprietarios").children("tr").length == 0 && $("#tabelaItens").children("tr").length == 0) {
            msgs[contErros] = "Insira pelo menos um contribuente.";
            contErros++;
            msgs[contErros] = "Insira pelo menos um item.";
            contErros++;
        } else if ($("#tabelaProprietarios").children("tr").length == 0) {
            msgs[contErros] = "Insira pelo menos um contribuente.";
            contErros++;
        } else if ($("#tabelaItens").children("tr").length == 0) {
            msgs[contErros] = "Insira pelo menos um item.";
            contErros++;
        } else if (somaValoresItens() != somaValoresPagosProprietario()) {
            msgs[contErros] = "Proprietário != Total itens";
            contErros++;
        }

        var nomesItens = $("input[name='nomeItem[]']");
        var valoresItens = $("input[name='valorItem[]']");
        var valoresDistribuicao = $("input[name='valorPagoUsuarioItem[]']");
        var totalIntegrantesItem = $("input[name='totalIntegrantesItem[]']");
        var index = 0;

        for (var i = 0; i < valoresItens.length; i++) {
            var valorItem = numeroControle(valoresItens[i].value);
            var valorTotalDistribuicao = 0;
            for (var j = 0; j < parseInt(totalIntegrantesItem[i].value); j++) {
                valorTotalDistribuicao += numeroControle(valoresDistribuicao[index].value);
                valorTotalDistribuicao = parseFloat(valorTotalDistribuicao.toFixed(2));
                index++;
            }
            if (valorItem != valorTotalDistribuicao) {
                msgs[contErros] = "Verifica os valores das distribuições do item " + nomesItens[i].value;
                contErros++;
            }
        }

        if (contErros > 0) {
            mostraModalAlerta(msgs);
            return false;
        } else {
            return true;
        }
    });

    /* Adição de item */
    $("#btAdicionaItem").click(function () {
        var integrantesSelecionados = $("input:checkbox[name=integrantesItem]:checked");

        var valorItem = $("#valorItem").val();
        var nomeItem = $("#nomeItem").val();
        var contErros = 0;
        var msgs = [];
        if (nomeItem == "") {
            msgs[contErros] = "Preenche o nome do item.";
            contErros++;
        }
        if (isNaN(numeroControle(valorItem))) {
            msgs[contErros] = "Número errado";
            contErros++;
        }
        if (integrantesSelecionados.length == 0) {
            msgs[contErros] = "Selecione algum integrante";
            contErros++;
        }
        if (contErros > 0) {
            mostraModalAlerta(msgs);
            return false;
        }
        var valorDistribuicao = geraDistribuicao(valorItem, integrantesSelecionados.length, 0);
        var distribuicao = [];
        for (var i = 0; i < integrantesSelecionados.length; i++) {
            var valoresCheckbox = integrantesSelecionados[i].value.split(";");
            var objeto = {idUsuario: valoresCheckbox[0],
                nomeUsuario: valoresCheckbox[1],
                emailUsuario: valoresCheckbox[2],
                valorAPagar: valorDistribuicao[i]
            };
            distribuicao[i] = objeto;
        }
        adicionaItem(nomeItem, valorItem, distribuicao);

    });


    function adicionaItem(nomeItem, valorItem, distribuicao) {
        var tdNome = "<td>" + nomeItem + "<input type='hidden' value='" + nomeItem + "' name='nomeItem[]' /></td>";
        var tdValorItem = "<td>" + numeroInterface(valorItem) + "<input type='hidden' value='" + numeroInterface(valorItem) + "' name='valorItem[]' /></td>";
        var tdBotao = "<td><input type='button' value='Remover' class='btn btn-danger' onclick='removeLinha(this)' /></td>";
        var tdDistribuicao = "<td> <div name='divAlerta[]'></div>" +
                "<ul> " +
                "<input type='hidden' name='totalIntegrantesItem[]' class='form-control' value='" + distribuicao.length + "' />";
        for (var i = 0; i < distribuicao.length; i++) {
            tdDistribuicao += "<li>" +
                    "<div class='form-inline'>" +
                    "  <label>" + distribuicao[i].nomeUsuario + ":</label> " +
                    "  <div class='input-group' name='teste' >" +
                    "    <input type='hidden' name='idUsuarioItem[]' class='form-control' value='" + distribuicao[i].idUsuario + "' />" +
                    "    <input type='text' name='valorPagoUsuarioItem[]' oninput='tfValorAPagarUsuarioItemOnChange(this)' class='form-control' size='6' value='" + numeroInterface(distribuicao[i].valorAPagar) + "' />" +
                    "    <span class='input-group-btn'>" +
                    "      <input type='button' class='btn btn-default' value='X' />" +
                    "    </span>" +
                    "  </div>" +
                    "</div>" +
                    "</li>";
        }
        tdDistribuicao += "</ul> </td>";
        var tr = "<tr>" + tdNome + tdValorItem + tdDistribuicao + tdBotao + "</tr>";
        $("#tabelaItens").append(tr);
    }

    /* Adição de proprietário */
    $("#btAdicionaProprietario").click(function () {
        var valorOption = $("#selectProprietario").find(":selected").val().split(";");
        var idUsuario = valorOption[0];
        var nomeUsuario = valorOption[1];
        var emailUsuario = valorOption[2];
        var valorPago = $("#valorPago").val();

        var contErros = 0;
        var msgs = [];
        if (!(idUsuario > 0)) {
            msgs[contErros] = "Selecione um proprietário";
            contErros++;
        }
        if (isNaN(numeroControle(valorPago))) {
            msgs[contErros] = "Número errado";
            contErros++;
        }

        if (contErros > 0) {
            mostraModalAlerta(msgs);
            return false;
        }

        if (idUsuario > 0) {
            var tr = "<tr>" +
                    "   <td>" + nomeUsuario + "</td>" +
                    "    <input type='hidden' name='idUsuarioProprietario[]' value='" + idUsuario + "' />" +
                    "   <td>" + emailUsuario + "</td>" +
                    "   <td> R$ " + numeroInterface(valorPago) +
                    "     <input type='hidden' name='valorPagoProprietario[]' value='" + numeroInterface(valorPago) + "' />" +
                    "   </td>" +
                    "   <td> <input type='button' class='btn btn-danger' value='Remover' onclick='removeLinha(this)' name='btRemoverProprietario' /> </td>" +
                    " </tr>";
            $("#tabelaProprietarios").append(tr);
        }
    });

    /* Seleção de Grupo */
    var isBtSelecionaGrupo = false;

    $("#checkboxSelecionaGrupo").on("change", function () {
        var idGrupo = $(this).find(":selected").attr("id");
        idGrupo = idGrupo.replace("grupo", "");
        if (idGrupo > 0) {
            carregaIntegrantesGrupo(idGrupo);
        } else {
            isBtSelecionaGrupo = false;
        }
    });

    $("#btSelecionaGrupo").click(function () {
        if (isBtSelecionaGrupo) {
            moveIntegrantes($("input:checkbox[name=integrantesFiltro]:checked"));
            var selected = $("#checkboxSelecionaGrupo").find(":selected");
            var idGrupo = selected.attr("id").replace("grupo", "");
            var nomeGrupo = selected.val();
            $("#idGrupo").val(idGrupo);
            $("#nomeGrupo").val(nomeGrupo);
            $('#modalSelecionaGrupo').modal('hide');
        }
    });

    function moveIntegrantes(checkboxsSelecionados) {
        $("#integrantesGrupo0").html("");
        $("#integrantesGrupo1").html("");
        $("#integrantesGrupo2").html("");
        $("#integrantesGrupo3").html("");
        $("#selectProprietario").html("<option value='-1;-1;-1'>Selecione um integrante</option>");
        for (var i = 0; i < checkboxsSelecionados.length; i++) {
            var valorCheckbox = checkboxsSelecionados[i].value;
            var nome = valorCheckbox.split(";")[1];
            var option = "<option value='" + valorCheckbox + "'>" + nome + "</option>";
            var checkbox = "<div class='col-md-12'> " +
                    "<input type='checkbox' name='integrantesItem' value='" + valorCheckbox + "' checked /> " + nome +
                    "</div>";
            $("#integrantesGrupo" + (i % 4)).append(checkbox);
            $("#selectProprietario").append(option);
        }
    }


    function criaDivsFiltroIntegrantes() {
        var divs = "<h4>Selecione os integrantes que participam da conta:</h4> " +
                "<div class='row'> " +
                "<div class='col-md-3' id='integrantesFiltroGrupo0'> " +
                "</div>" +
                "<div class='col-md-3' id='integrantesFiltroGrupo1'> " +
                "</div>" +
                "<div class='col-md-3' id='integrantesFiltroGrupo2'> " +
                "</div>" +
                "<div class='col-md-3' id='integrantesFiltroGrupo3'> " +
                "</div>";
        $("#integrantesFiltroGrupo").html(divs);

    }
    function adicionaIntegrante(idDiv, integrante) {
        var valorCheckbox = integrante.id + ";" + integrante.nome + ";" + integrante.email;
        var html = "<div class='col-md-12'>" +
                "<input type='checkbox' name='integrantesFiltro' value='" + valorCheckbox + "' checked />" + integrante.nome +
                "</div>";
        $("#" + idDiv).append(html);
    }

    function carregaIntegrantesGrupo(idGrupo) {
        $("#integrantesFiltroGrupo").html("Carregando integrantes do grupo...");
        var url = "ScriptsAJAX/scriptIntegrantesGrupo.php?idGrupo=" + idGrupo;
        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function () {
            if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                var texto = xmlhttp.responseText;
                var integrantes = JSON.parse(texto);
                criaDivsFiltroIntegrantes();
                for (var i = 0; i < integrantes.length; i++) {
                    var integrante = JSON.parse(integrantes[i]);
                    var idDiv = "integrantesFiltroGrupo" + (i % 4);
                    adicionaIntegrante(idDiv, integrante);
                }
                isBtSelecionaGrupo = true;
            }
        }
        xmlhttp.open("GET", url, true);
        xmlhttp.send();
    }
});

