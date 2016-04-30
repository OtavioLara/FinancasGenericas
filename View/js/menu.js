/* Globais */
var idUsuario;
var carregandoNotificacoes = false;
var limiteInicio = 0;
var limiteFim = 6;

$(function () {
    $("#liNotificacoes").on("show.bs.dropdown", function () {
        if (!carregandoNotificacoes) {
            $('#notificacoes').html("");
            carregaNotificacoes();
        }
    });

    $('#notificacoes').on('scroll', function () {
        if ($(this).scrollTop() + $(this).innerHeight() >= $(this)[0].scrollHeight) {
            if (!carregandoNotificacoes) {
                carregaNotificacoes();
            }
        }
    });

    /* Funções para Notificação */
    function mostraBarraDeCarregarNotificacoes() {
        var html = "<li class='divider' id='liLoadingDivider'></li>" +
                "<li id='imgLoadingNotificacoes' class='text-center'>" +
                "<img src='Imagens/ajax_loading.gif' />'" +
                "</li>";
        $("#notificacoes").append(html);
    }

    function retiraBarraDeCarregarNotificacoes() {
        $("#liLoadingDivider").remove();
        $("#imgLoadingNotificacoes").remove();
    }
    function adicionaNotificacao(notificacao) {
        var novaLinha = "<li>" +
                "<a href='#'>" +
                "<div>" +
                "<strong>" + notificacao.titulo + "</strong>" +
                "<span class='pull-right text-muted'>" +
                "<em>" + notificacao.data + "</em>" +
                "</span>" +
                "</div>" +
                "<div>" + notificacao.mensagem + "</div>" +
                "</a>" +
                "</li>" +
                "<li class='divider'></li>";

        $("#notificacoes").append(novaLinha);
    }

    function atualizaQuantidadeNotificacoesNaoVisualizadas() {
        var url = "ScriptsAJAX/scriptTotalNotificacoesNaoVisualizadas.php?idUsuario=" + idUsuario;
        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function () {
            if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                $("#qtdNotificacoes").html(xmlhttp.responseText);
            }
        }
        xmlhttp.open("GET", url, true);
        xmlhttp.send();
    }
    function carregaNotificacoes() {
        carregandoNotificacoes = true;
        mostraBarraDeCarregarNotificacoes();
        var url = "ScriptsAJAX/scriptNotificacoes.php?idUsuario=" + idUsuario +
                "&limiteInicio=" + limiteInicio + "&limiteFim=" + limiteFim;
        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function () {
            if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                var texto = xmlhttp.responseText;
                var notificacoes = JSON.parse(texto);
                for (var i = 0; i < notificacoes.length; i++) {
                    var notificacao = JSON.parse(notificacoes[i]);
                    adicionaNotificacao(notificacao);
                }
                atualizaQuantidadeNotificacoesNaoVisualizadas();
                retiraBarraDeCarregarNotificacoes();
                carregandoNotificacoes = false;
            }
        }
        xmlhttp.open("GET", url, true);
        xmlhttp.send();
    }

});



