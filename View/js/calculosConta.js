Number.prototype.formatMoney = function (c, d, t) {
    var n = this,
            c = isNaN(c = Math.abs(c)) ? 2 : c,
            d = d == undefined ? "." : d,
            t = t == undefined ? "," : t,
            s = n < 0 ? "-" : "",
            i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "",
            j = (j = i.length) > 3 ? j % 3 : 0;
    return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
};

function numeroControle(numString) {
    if (numString == "") {
        return NaN;
    } else if (!isNaN(numString)) {
        return parseFloat(numString);
    } else {
        numString = numString.replace(",", ".");
        return Number(numString);
    }
}

function numeroInterface(num) {
    if(!isNaN(num)){
        return Number(num).formatMoney(2,',','.');
    }else{
        return num;
    }
    //var numString = "" + num;
    //numString = numString.replace(".", ",");
    //return numString;
}

//16.7645345 => 16.76
function round(valor) {
    var retorno = (valor * 100).toFixed(2);
    var retorno = Math.floor(retorno);
    var retorno = (retorno / 100).toFixed(2);
    return parseFloat(retorno);
}

function geraDistribuicao(valor, totalPessoas, posInicial) {
    var distribuicao = [];
    var divisao = valor / totalPessoas;
    divisao = round(divisao);
    var resto = valor - parseFloat((divisao * totalPessoas).toFixed(2));
    resto = parseFloat(resto.toFixed(2));
    for (var i = 0; i < totalPessoas; i++) {
        distribuicao[i] = divisao;
    }
    var pos = posInicial % totalPessoas;
    while (resto > 0) {
        pos = pos % totalPessoas;
        distribuicao[pos] += 0.01;
        resto -= 0.01;
        distribuicao[pos] = parseFloat(distribuicao[pos].toFixed(2));
        resto = parseFloat(resto.toFixed(2));
        pos++;
    }
    return distribuicao;
}

function geraSugestao(dividas, valor) {
    var distribuicao = [];
    for (var i = 0; i < dividas.length; i++) {
        var valorDivida = parseFloat(dividas[i].value);
        if (valor != 0) {
            if (valor > valorDivida) {
                distribuicao[i] = valorDivida;
                valor -= valorDivida;
                valor = parseFloat(valor.toFixed(2));
            } else {
                distribuicao[i] = valor;
                valor = 0;
            }
        } else {
            distribuicao[i] = 0;
        }
    }
    return distribuicao;
}