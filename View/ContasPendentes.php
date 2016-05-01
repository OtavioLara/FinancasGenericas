<?php
include "../ScriptLogin.php";
$contaDAO = new ContaDAO($conexao);
$nomeConta = "";
$dataInicio = "";
$dataFim = "";
$idGrupo = 0;
$contasPendentes = $contaDAO->getContasPendentes($usuario->getId(), null, null, null, null);
$sugestaoPagar = array();
$sugestaoReceber = array();
$valorAPagar = 0;
$valorAReceber = 0;
if (count($contasPendentes) > 0) {
    foreach ($contasPendentes as $conta) {
        if ($conta->getIntegrante($usuario->getId())->precisaReceber()) {
            $sugestao = &$sugestaoReceber;
            $sugestaoConta = $conta->geraSugestaoReceber($usuario->getId());
        } else {
            $sugestao = &$sugestaoPagar;
            $sugestaoConta = $conta->geraSugestaoPagar($usuario->getId());
        }
        $valor = 0;
        foreach ($sugestaoConta as $key => $value) {
            if (isset($sugestao[$key])) {
                $sugestao[$key] += $value;
            } else {
                $sugestao[$key] = $value;
            }
            $valor += $value;
        }
        if ($conta->getIntegrante($usuario->getId())->precisaReceber()) {
            $valorAReceber += $valor;
        } else {
            $valorAPagar += $valor;
        }
    }
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

        <title>Finanças Genérica - Contas pendentes</title>

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
                            <h1 class="page-header">Contas Pendentes</h1>
                        </div>
                        <!-- /.col-lg-12 -->
                    </div>
                    <!-- /.row -->

                    <!-- Sugestões -->
                    <div class="row">
                        <div class="col-md-4 col-md-offset-1">
                            <span>Você precisa pagar:</span>
                            <h3><?php echo "R$ " . $formato->numeroInterface($valorAPagar); ?></h3>
                            <?php
                            if (count($sugestaoPagar) > 0) {
                                echo "<span>Sugestão:</span>";
                                echo "<ul>";
                                foreach ($sugestaoPagar as $devedor => $divida) {
                                    echo "<li>Pagar para $devedor R$ " . $formato->numeroInterface($divida) . " .</li>";
                                }
                                echo "</ul>";
                            }
                            ?>
                        </div>
                        <div class="col-md-4 col-md-offset-3">
                            <span>Você precisa receber:</span>
                            <h3><?php echo "R$ " . $formato->numeroInterface($valorAReceber); ?></h3>
                            <?php
                            if (count($sugestaoReceber) > 0) {
                                echo "<span>Sugestão:</span>";
                                echo "<ul>";
                                foreach ($sugestaoReceber as $devedor => $divida) {
                                    echo "<li>Receber de $devedor R$ " . $formato->numeroInterface($divida) . " .</li>";
                                }
                                echo "</ul>";
                            }
                            ?>
                        </div>
                    </div>
                    <!-- ./ Sugestões -->



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
                                                <th>Valor a pagar/receber</th>
                                                <th>Grupo</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            foreach ($contasPendentes as $conta) {
                                                $integrante = $conta->getIntegrante($usuario->getId());
                                                $bgcolor = ($integrante->precisaReceber()) ? "#5CB85C" : "#D9534F";
                                                echo "<tr>";
                                                echo "<td><a href='VerConta.php?idConta=" . $conta->getId() . "'>" . $conta->getNome() . "</a></td>";
                                                echo "<td>" . $conta->getData()->format("d/m/Y H:i:s") . "</td>";
                                                if ($integrante->precisaReceber()) {
                                                    echo "<td> Receber " . $formato->numeroInterface($integrante->getValorAReceber()) . "</td>";
                                                } else {
                                                    echo "<td> Pagar " . $formato->numeroInterface($integrante->getValorAPagar()) . "</td>";
                                                }

                                                $republica = $conta->getRepublica();
                                                if (isset($republica)) {
                                                    echo "<td>" . $conta->getRepublica()->getNome() . "</td>";
                                                } else {
                                                    echo "<td>Conta sem grupo</td>";
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


                </div>
                <!-- /.container-fluid -->
            </div>
            <!-- /#page-wrapper -->
        </div>
        <!-- /#wrapper -->
    </body>
</html>
