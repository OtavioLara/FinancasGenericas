<?php
include "../ScriptLogin.php";
if(isset($_GET['dataInicio'])){
    $contaDAO = new ContaDAO($conexao);
    $infoData = date_parse_from_format("d/m/Y", $_GET['dataInicio']);
    $dataInicio = $infoData["year"]."-".$infoData["month"]."-".$infoData["day"]." 00:00:00";
    $infoData = date_parse_from_format("d/m/Y", $_GET['dataFim']);
    $dataFim = $infoData["year"]."-".$infoData["month"]."-".$infoData["day"]." 23:59:59";
    $contas = $contaDAO->getHistoricoConta($dataInicio, $dataFim);
}
?>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    </head>
    <a href="Perfil.php">Voltar para o perfil</a><br/>
    <form action="HistoricoConta.php" method="get">
        Data Inicio: <input type="text" name="dataInicio" />
        Data Fim: <input type="text" name="dataFim" />
        <br/>
        <input type="submit" value="pesquisar" />
    </form>

    <?php
    if(isset($contas)){
        echo "<ul>";
        foreach($contas as $conta){
            echo "<li>";
            echo "<a href='VerConta.php?id=".$conta->getId()."'>".$conta->getNome()."</a>";
            echo "</li>";
        }
        echo "</ul>";
    }
    ?>
</html>