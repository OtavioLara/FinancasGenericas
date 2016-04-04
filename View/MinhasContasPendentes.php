<?php
include "../ScriptLogin.php";
$contaDAO = new ContaDAO($conexao);


$grafico = new GraficoBuilder();
$grafico->adicionaChave("Precisa Receber");
$grafico->adicionaChave("Precisa Pagar");
$contasAPagar = $contaDAO->getContasAPagar($usuario->getId());
$contasAReceber = $contaDAO->getContasAReceber($usuario->getId());

$sugestaoPagar = array();
foreach ($contasAPagar as $conta) {
    $sugestaoConta = $conta->geraSugestaoPagar($usuario->getId());
    foreach ($sugestaoConta as $key => $value) {
        if (isset($sugestaoPagar[$key])) {
            $sugestaoPagar[$key] += $value;
        } else {
            $sugestaoPagar[$key] = $value;
        }
    }
}

$sugestaoReceber = array();
foreach ($contasAReceber as $conta) {
    $sugestaoConta = $conta->geraSugestaoReceber($usuario->getId());
    foreach ($sugestaoConta as $key => $value) {
        if (isset($sugestaoReceber[$key])) {
            $sugestaoReceber[$key] += $value;
        } else {
            $sugestaoReceber[$key] = $value;
        }
    }
}
?>
<html>
    <head>
        <script src="./lib/js/jquery.min.js"></script> 
        <script src="./lib/js/chartphp.js"></script> 
        <link rel="stylesheet" href="./lib/js/chartphp.css"> 
    </head>
    Voltar para <a href="Perfil.php"> perfil </a> <br/>
    <hr>
    <h2>Minhas Contas A Pagar: </h2>
    <ul>
        <?php
        $valorTotal = 0;
        foreach ($contasAPagar as $conta) {
            echo "<li><a href='VerConta.php?id=" . $conta->getId() . "'>" . $conta->getNome() . "</a></li>";
            $usuario_integrante = $conta->getIntegrante($usuario->getId());
            $valorTotal += $usuario_integrante->getValorAPagar();
        }
        echo "Voce estah devendo: R$" . $formato->numeroInterface($valorTotal);
        $grafico->insereValor("Precisa Pagar", $valorTotal);
        echo "<br/>Sugestao: <br/>";
        foreach ($sugestaoPagar as $key => $value) {
            echo "Pagar para $key R$ ".$formato->numeroInterface($value)." .<br/>";
        }
        ?>
    </ul>
    <hr>
    <h2>Minhas Contas A Receber: </h2>
    <ul>
        <?php
        $valorTotal = 0;
        foreach ($contasAReceber as $conta) {
            echo "<li><a href='VerConta.php?id=" . $conta->getId() . "'>" . $conta->getNome() . "</a></li>";
            $usuario_integrante = $conta->getIntegrante($usuario->getId());
            $valorTotal += $usuario_integrante->getValorAReceber();
        }
        echo "Voce precisa receber: R$" . $formato->numeroInterface($valorTotal);
        $grafico->insereValor("Precisa Receber", $valorTotal);
        echo "<br/>Sugestao: <br/>";
        foreach ($sugestaoReceber as $key => $value) {
            echo "Receber de $key R$ ".$formato->numeroInterface($value) .".<br/>";
        }
        ?>
    </ul>
    <?php echo $grafico->geraGrafico("bar", "", "", "", 250, 400) ?> 

</html>