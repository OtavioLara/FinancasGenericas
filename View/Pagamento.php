<?php
include "../ScriptLogin.php";
if (isset($_REQUEST['email'])) {
    $contaDAO = new ContaDAO($conexao);
    $usuarioDAO = new UsuarioDAO($conexao);
    $requerimento = false;
    $pagamento = false;
    if (isset($_REQUEST['requerimento'])) {
        $requerimento = true;
    } else if (isset($_REQUEST['pagamento'])) {
        $pagamento = true;
    }
    $usuarioInformado = $usuarioDAO->getUsuarioPorEmail($_REQUEST['email']);
    if ($requerimento) {
        $contas = $contaDAO->getDividas($usuarioInformado->getId(), $usuario->getId());
    } else if ($pagamento) {
        $contas = $contaDAO->getDividas($usuario->getId(), $usuarioInformado->getId());
    }
    $scriptJavaScript = "";
    $inputValorAPagar = "";
    $readOnly = "";
    $idRequerimento = "-1";

    if (isset($_REQUEST['viaRequerimento']) && $_REQUEST['viaRequerimento']) {
        $viaRequerimento = true;
        $readOnly = "readonly";
        $scriptJavaScript = "chamaGeraSugestao();";
        $inputValorAPagar = $_REQUEST['valorAPagar'];
        $idRequerimento = $_REQUEST['idRequerimento'];
    }
}
?>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <script src="js/calculosConta.js"></script>
        <script>
            function chamaGeraSugestao() {
                var dividas = document.getElementsByName("divida[]");
                var valor = document.getElementById("valorAPagar").value;
                var valoresDividas = [];
                for (var i = 0; i < dividas.length; i++) {
                    valoresDividas[i] = dividas.value;
                }
                var distribuicao = geraSugestao(dividas, valor);
                var pagar = document.getElementsByName("pagar[]");
                for (var i = 0; i < pagar.length; i++) {
                    pagar[i].value = numeroInterface(distribuicao[i]);
                }
            }
        </script>
    </head>
    <body onload="<?php echo $scriptJavaScript; ?>">
        <a href="Perfil.php"> perfil </a> <br/>
        <form action="Pagamento.php" method="post">
            Informe o email : <input type="text" name="email" />
            <input type="checkbox" name="requerimento" /> Requerimento de pagamento
            <input type="checkbox" name="pagamento"  /> Atualizar pagamento
            <input type="submit" value="enviar"  />
        </form>
        <?php
        if (isset($pagamento) && $pagamento) {
            echo "Devedor: " . $usuarioInformado->getNome() . "<br/>";
            echo "<form method='post' action='ControlesScript/ControleContaScript.php'/>";
            echo "<input type='text' id='valorAPagar' size='3' value='$inputValorAPagar' $readOnly /> ";
            echo "<input type='button' value='gerar' onclick='chamaGeraSugestao()' /> <br/>";
            $dividaTotal = 0;
            foreach ($contas as $conta) {
                $valorAPagar = $conta->getIntegrante($usuarioInformado->getId())->getValorAPagar();
                echo "<strong>" . $conta->getNome() . "</strong>";
                echo "-Valor: <input type='text' name='pagar[]' size='3' value='0' $readOnly /> / " . $formato->numeroInterface($valorAPagar) . "<br/>";
                $dividaTotal += $conta->getIntegrante($usuarioInformado->getId())->getValorAPagar();
                echo "<input type='hidden' name='divida[]' value='$valorAPagar' />";
                echo "<input type='hidden' name='idConta[]' value='" . $conta->getId() . "' />";
            }
            echo "Dívida total: ".$formato->numeroInterface($dividaTotal)."<br/>";
            echo "<input type='hidden' name='idUsuarioPagando' value='" . $usuarioInformado->getId() . "' />";
            echo "<input type='hidden' name='idUsuarioRecebendo' value='" . $usuario->getId() . "' />";
            echo "<input type='hidden' name='idRequerimento' value='$idRequerimento' />";
            echo "<input type='hidden' name='comando' value='usuarioPagandoMuitasContas' />";
            echo "<input type='submit' value='atualizar' />";
        } else if (isset($requerimento) && $requerimento) {
            echo "Contas que está devendo para: " . $usuarioInformado->getNome() . "<br/>";
            $valorTotal = 0;
            foreach ($contas as $conta) {
                $valorAPagar = $conta->getIntegrante($usuario->getId())->getValorAPagar();
                echo "<strong>" . $conta->getNome() . "</strong>. Valor que está devendo: " . $formato->numeroInterface($valorAPagar);
                echo "<br/>";
                $valorTotal += $valorAPagar;
            }
            echo "Total: R$ " . $formato->numeroInterface($valorTotal) . "<br/>";
            echo "<form method='post' action='ControlesScript/ControleContaScript.php'/>";
            echo "<input type='text' name='valorRequerimento' />";
            echo "<input type='hidden' name='idDestinatario' value='" . $usuarioInformado->getId() . "' /> ";
            echo "<input type='hidden' name='idRemetente' value='" . $usuario->getId() . "' /> ";
            echo "<input type='hidden' name='comando' value='usuarioRequerimento' />";
            echo "<input type='submit' value='enviar requerimento' /> ";

            echo "</form>";
        }
        ?>
    </body>
</html>