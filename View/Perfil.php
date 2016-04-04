<?php
include "../ScriptLogin.php";
$republicaDAO = new RepublicaDAO($conexao);
$contaDAO = new ContaDAO($conexao);
$notificacaoDAO = new NotificacaoDAO($conexao);

$republicas = $republicaDAO->getRepublicasPorIdUsuario_Incompleto($usuario->getId());
$convites = $republicaDAO->getConviteParaUsuario($usuario->getId());
$requerimentos = $contaDAO->getRequerimentosDestinatario($usuario->getId(), 'L');
$notificacoes = $notificacaoDAO->getNotificacoes($usuario->getId(), 0, 10);
?>
<a href="../../BD/ContaDAO.php"></a>

<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <script>
            limiteInicio = 0;
            limiteFim = 10;
            idUsuario = <?php echo $usuario->getId() . ";"; ?>
            function carregaNotificacoes() {
                var div = document.getElementById("notificacoes");
                var divCarregando = document.getElementById("carregandoNotificacoes");
                divCarregando.innerHTML = "<img src='Imagens/loading.gif' />";
                var url = "ScriptsAJAX/scriptNotificacoes.php?idUsuario=" + idUsuario +
                        "&limiteInicio=" + limiteInicio + "&limiteFim=" + limiteFim;
                limiteInicio += 10;
                limiteFim += 10;
                var xmlhttp = new XMLHttpRequest();
                xmlhttp.onreadystatechange = function () {
                    if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                        divCarregando.innerHTML = "";
                        div.innerHTML += xmlhttp.responseText;
                    }
                }
                xmlhttp.open("GET", url, true);
                xmlhttp.send();

            }
        </script>
    </head>
    <div>
        
        <input type="button" onclick="carregaNotificacoes()" value="notificações" />
        <div id="notificacoes">
            <div id="carregandoNotificacoes"></div>
        </div>
        <?php
        if (count($requerimentos) > 0) {
            echo "Requerimentos de pagamento: <br/>";
            foreach ($requerimentos as $requerimento) {
                echo "Requerimento de : " . $requerimento->getRemetente()->getNome()
                . " valor: R$ " . $requerimento->getValor() . " ";
                echo "<a href='Pagamento.php?email=" . $requerimento->getRemetente()->getEmail()
                . "&pagamento=on&viaRequerimento=true&valorAPagar=" . $requerimento->getValor()
                . "&idRequerimento=" . $requerimento->getId() . "'> Receber </a>|";
                echo "<a href='ControlesScript/ControleContaScript.php?idRequerimento=" . $requerimento->getId()
                . "&comando=requerimentoRejeitado' > Rejeitar </a>";
                echo "<br/>";
            }
        }
        if (count($convites) > 0) {
            echo "Convites: <br/>";
            foreach ($convites as $convite) {
                $idRepublica = $convite->getRepublica()->getId();
                $idUsuario = $convite->getUsuario()->getId();
                echo $convite->toString();
                echo " <a href='ControlesScript/ControleConviteScript.php?comando=responder"
                . "&resposta=1&idConvite=" . $convite->getId() . "'> sim </a>|";
                echo " <a href='ControlesScript/ControleConviteScript.php?comando=responder"
                . "&resposta=0&idConvite=" . $convite->getId() . "'> não </a> <br/>";
                echo "<br/>";
            }
        }
        ?>
        Nome: <?php echo $usuario->getNome(); ?> <br/>
        Email: <?php echo $usuario->getEmail(); ?> <br/>
        <a href="MinhasContasPendentes.php">Minhas Contas</a><br/>
        <a href="HistoricoConta.php">Consultar histórico</a><br/>
        <a href="Pagamento.php">Atualizar dívidas</a><br/>
        <a href='CriarRepublica.php'> criar republica </a> <br/>
        Minhas Republicas:<br/>
        <?php
        foreach ($republicas as $republica) {
            echo "<a href='MinhaRepublica.php?id=" . $republica->getId() . "'>" .
            $republica->getNome() . "</a> <br/>";
        }
        ?>

        <a href="../abort.php"> deslogar </a>
    </div>
</html>