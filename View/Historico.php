<?php

function datas($contas) {
    if (count($contas) > 0) {
        $primeiraConta = reset($contas);
        $menorData = $primeiraConta->getData();
        $maiorData = $primeiraConta->getData();
        foreach ($contas as $conta) {
            if ($menorData > $conta->getData()) {
                $menorData = $conta->getData();
            } else if ($maiorData < $conta->getData()) {
                $maiorData = $conta->getData();
            }
        }
        return array($menorData, $maiorData);
    }
    return null;
}

include "../ScriptLogin.php";
$contaDAO = new ContaDAO($conexao);
$nomeConta = "";
$dataInicio = "";
$dataFim = "";
$idGrupo = 0;
if (isset($_GET['nomeConta'])) {
    $nomeConta = $_GET['nomeConta'];
    if ($_GET['dataInicio'] != "") {
        $dataInicio = $_GET['dataInicio'] . " 00:00:00";
    }
    if ($_GET['dataFim'] != "") {
        $dataFim = $_GET['dataFim'] . " 23:59:59";
    }
    $idGrupo = $_GET['idGrupo'];
    $contas = $contaDAO->getHistoricoConta($usuario->getId(), $nomeConta, $dataInicio, $dataFim, $idGrupo);

    /* Redefine valores dos campos */
    if ($dataInicio != "") {
        $dataInicio = new DateTime($dataInicio);
        $dataInicio = $dataInicio->format("Y-m-d");
    }
    if ($dataFim != "") {
        $dataFim = new DateTime($dataFim);
        $dataFim = $dataFim->format("Y-m-d");
    }
} else {
    $contas = $contaDAO->getHistoricoConta($usuario->getId(), null, null, null, null, 0, 10);
}

if (count($contas) > 0) {
    $datas = datas($contas);
    $graficoBuilder = new GraficoBuilder();
    $graficoBuilder->criaChavesPorMes($datas[0], $datas[1]);
    foreach ($contas as $conta) {
        $integrante = $conta->getIntegrante($usuario->getId());
        $graficoBuilder->insereValor($conta->getData()->format("m/Y"), $integrante->getValorTotalPagar());
    }
    $codigoGrafico = $graficoBuilder->geraGrafico("graficoGasto");
} else {
    $codigoGrafico = null;
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>

        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="">
        <meta name="author" content="">

        <title>Finanças Genérica - Histórico</title>

        <style>
            .dropdown-menu {
                max-height: 390px;
                overflow-y: scroll;
            }
        </style>
        <!-- Bootstrap Core CSS -->
        <link href="Componentes/bower_components/bootstrap/dist/css/bootstrap.css" rel="stylesheet">

        <!-- MetisMenu CSS -->
        <link href="Componentes/bower_components/metisMenu/dist/metisMenu.min.css" rel="stylesheet">

        <!-- Custom CSS -->
        <link href="Componentes/dist/css/sb-admin-2.css" rel="stylesheet">

        <!-- Custom Fonts -->
        <link href="Componentes/bower_components/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">

        <script src="Componentes/bower_components/jquery/dist/jquery.min.js"></script>

        <!-- Bootstrap Core JavaScript -->
        <script src="Componentes/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>

        <!-- Metis Menu Plugin JavaScript -->
        <script src="Componentes/bower_components/metisMenu/dist/metisMenu.min.js"></script>

        <!-- Custom Theme JavaScript -->
        <script src="Componentes/dist/js/sb-admin-2.js"></script>

        <!-- DataTables -->
        <link href="Componentes/bower_components/datatables-plugins/integration/bootstrap/3/dataTables.bootstrap.css" rel="stylesheet">
        <script src="Componentes/bower_components/datatables/media/js/jquery.dataTables.min.js"></script>
        <script src="Componentes/bower_components/datatables-plugins/integration/bootstrap/3/dataTables.bootstrap.min.js"></script>


        <!-- Morris Charts-->
        <link href="Componentes/bower_components/morrisjs/morris.css" rel="stylesheet">
        <script src="Componentes/bower_components/raphael/raphael-min.js"></script>
        <script src="Componentes/bower_components/morrisjs/morris.min.js"></script>

        <!-- Menu JS -->
        <script src="js/menu.js"></script>
        <script>idUsuario = <?php echo $usuario->getId(); ?>;</script>

        <!-- Script Popup requerimento -->
        <?php include "./RequerimentoPopup.php"; ?>

        <script>
            $(function () {

                $('#dataTableConvite').DataTable({
                    responsive: true
                });

                $("#grupos").change(function () {
                    var idGrupo = $(this).find(":selected").attr("id");
                    idGrupo = idGrupo.replace("grupo", "");
                    $("#idGrupo").val(idGrupo);
                });

<?php
if (isset($codigoGrafico)) {
    echo "Morris.Bar({" . $codigoGrafico . "});";
}
?>

            });
        </script>
    </head>

    <body>
        <?php include "./RequerimentoModal.php"; ?>
        <div id="wrapper">
            <?php include "./menu.php"; ?>

            <!-- Page Content -->
            <div id="page-wrapper">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-lg-12">
                            <h1 class="page-header">Histórico</h1>
                        </div>
                        <!-- /.col-lg-12 -->
                    </div>
                    <!-- /.row -->

                    <form action="Historico.php">
                        <div class="row">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    Pesquisa
                                </div>
                                <div class="panel-body">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label>Nome Conta: </label>
                                            <input type="text" class="form-control" name="nomeConta" value="<?php echo $nomeConta; ?>"/>
                                        </div>

                                        <div class="col-md-3">
                                            <label>Data Início</label>
                                            <input type="date" class="form-control" name="dataInicio" value="<?php echo $dataInicio; ?>"/>
                                        </div>
                                        <div class="col-md-3">
                                            <label>Data Fim:</label>
                                            <input type="date" class="form-control" name="dataFim" value="<?php echo $dataFim; ?>"/>
                                        </div>

                                        <div class="col-md-3">
                                            <label>Grupo: </label>
                                            <select class="form-control" id="grupos">

                                                <option id="grupo0" <?php if ($idGrupo == 0) echo "selected"; ?> >Todos os grupos</option>
                                                <?php
                                                foreach ($grupos as $grupo) {
                                                    $id = $grupo->getId();
                                                    $selected = ($idGrupo == $grupo->getId()) ? "selected" : "";
                                                    echo "<option id='grupo$id' $selected>" . $grupo->getNome() . "</option>";
                                                }
                                                ?>
                                            </select>
                                            <input type="hidden" id="idGrupo" name="idGrupo" value="<?php echo $idGrupo; ?>" />
                                        </div>
                                    </div>
                                    <br/>
                                    <div class="row text-center">
                                        <div class="col-md-4 col-md-offset-4">
                                            <input type="submit" class="btn btn-block btn-success" value="Consultar" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                    <!-- ./ Formulário consultar -->
                    <!-- Tabela -->
                    <div class="row">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                Contas
                            </div>
                            <!-- /.panel-heading -->

                            <div class="panel-body">
                                <div class="dataTable_wrapper">
                                    <table class="table table-striped table-bordered table-hover" id="dataTableConvite">
                                        <thead>
                                            <tr>
                                                <th>Conta</th>
                                                <th>Data</th>
                                                <th>Valor gasto</th>
                                                <th>Grupo</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            foreach ($contas as $conta) {
                                                $integrante = $conta->getIntegrante($usuario->getId());
                                                echo "<tr>";
                                                echo "<td><a href='VerConta.php?idConta=" . $conta->getId() . "'>" . $conta->getNome() . "</a></td>";
                                                echo "<td>" . $conta->getData()->format("d/m/Y H:i:s") . "</td>";
                                                echo "<td>" . $formato->numeroInterface($integrante->getValorTotalPagar()) . "</td>";
                                                $republica = $conta->getRepublica();
                                                if (isset($republica)) {
                                                    echo "<td>" . $conta->getRepublica()->getNome() . "</td>";
                                                } else {
                                                    echo "<td>conta sem grupo</td>";
                                                }
                                                echo "</tr>";
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /.table-responsive -->

                    <!-- Gráfico -->

                    <div class="row">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                Gráfico
                            </div>
                            <!-- /.panel-heading -->  
                            <div class="panel-body">
                                <div id="graficoGasto"></div>
                            </div>
                        </div>
                    </div>
                    <!-- /.Gráfico -->

                </div>
                <!-- /.container-fluid -->
            </div>
            <!-- /#page-wrapper -->
        </div>
        <!-- /#wrapper -->
    </body>
</html>
