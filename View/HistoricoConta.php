<?php
include "../ScriptLogin.php";
if (isset($_GET['dataInicio'])) {
    $contaDAO = new ContaDAO($conexao);
    $infoDataInicio = date_parse_from_format("d/m/Y", $_GET['dataInicio']);
    $dataInicio = $infoDataInicio["year"] . "-" . $infoDataInicio["month"] . "-" . $infoDataInicio["day"] . " 00:00:00";
    $infoDataFim = date_parse_from_format("d/m/Y", $_GET['dataFim']);
    $dataFim = $infoDataFim["year"] . "-" . $infoDataFim["month"] . "-" . $infoDataFim["day"] . " 23:59:59";
    $contas = $contaDAO->getHistoricoConta($dataInicio, $dataFim);
    $graficoBuilder = new GraficoBuilder();
    $graficoBuilder->criaChavesPorMes($infoDataInicio, $infoDataFim);
}
?>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <script src="./lib/js/jquery.min.js"></script> 
        <script src="./lib/js/chartphp.js"></script> 
        <link rel="stylesheet" href="./lib/js/chartphp.css"> 
    </head>
    <a href="Perfil.php">Voltar para o perfil</a><br/>
    <form action="HistoricoConta.php" method="get">
        Data Inicio: <input type="text" name="dataInicio" />
        Data Fim: <input type="text" name="dataFim" />
        <br/>
        <input type="submit" value="pesquisar" />
    </form>

    <?php
    if (isset($contas)) {
        echo "<ul>";
        foreach ($contas as $conta) {
            $graficoBuilder->insereValor($conta->getData()->format('m/Y'), $conta->getValorTotal());
            $valorConta = $formato->numeroInterface($conta->getValorTotal());
            echo "<li>";
            echo "<a href='VerConta.php?id=" . $conta->getId() . "'>" . $conta->getNome() . " (R$ $valorConta)</a>";
            echo "</li>";
        }
        echo "</ul>";
    }
    ?>
    <?php echo $graficoBuilder->geraGrafico("bar", "", "", "", 250, 400) ?> 
</html>