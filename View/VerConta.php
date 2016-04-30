<?php
include "../ScriptLogin.php";
if (isset($_GET['idConta'])) {
    $idConta = $_GET['idConta'];
    $contaDAO = new ContaDAO($conexao);
    $conta = $contaDAO->getContaCompletaPorIdConta($idConta);
    if (isset($conta) && $conta->possuiIntegrante($usuario->getId())) {
        $nomeConta = $conta->getNome();
        $itens = $conta->getItens();
        $integrantes = $conta->getIntegrantes();
        $republica = $conta->getRepublica();
        if (isset($republica)) {
            $nomeGrupo = $conta->getRepublica()->getNome();
        } else {
            $nomeGrupo = "Sem grupo";
            $idGrupo = 0;
            $integrantesGrupo = array();
        }
        $dataAtual = $conta->getData()->format("Y-m-d");
        $descricaoAdicional = $conta->getDescricaoAdicional();
        $dataAlerta = $conta->getDataAlerta();
        if (isset($dataAlerta)) {
            $dataAlerta = $dataAlerta->format("d/m/Y");
        } else {
            $dataAlerta = "Sem data alerta";
        }
    } else {
        header('Location: Index.php');
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

        <title>Finanças Genérica - Ver conta</title>

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

        <!-- Contas -->
        <script src='js/calculosConta.js'></script>

        <script>
            var idConta = <?php echo $conta->getId(); ?>;
            function verificaValorAPagar(valorMax) {
                var valor = $("#valorAAtualizar").val();
                valor = numeroControle(valor);
                if (!isNaN(valor)) {
                    if (valor > valorMax) {
                        var valor = $("#valorAAtualizar").val(valorMax);
                    }
                    return true;
                } else {
                    alert('número incorreto');
                    return false;
                }
            }
            $(function () {
                $("#selecionaDevedor").change(function () {
                    var valorOption = $(this).find(":selected").val().split(";");
                    var idDevedor = valorOption[0];
                    var nomeDevedor = valorOption[1];
                    var valorMax = valorOption[2];
                    if (idDevedor > 0) {
                        var html = "<p> <font size='4'> Valor máximo que " + nomeDevedor + " pode te pagar: R$ " + numeroInterface(valorMax) + "</font></p>" +
                                "<div class='row'> <form action='ControlesScript/ControleContaScript.php' method='post'>" +
                                "  <input type='hidden' name='comando' value='usuarioPagando' />" +
                                "  <input type='hidden' name='idConta' value='" + idConta + "' />" +
                                "  <input type='hidden' name='idRecebidor' value='" + idUsuario + "' />" +
                                "  <div class='col-md-3'>" +
                                "    <label>Valor a atualizar:</label>" +
                                "    <input type='text' name='valorAAtualizar' id='valorAAtualizar' class='form-control' />" +
                                "    <input type='hidden' name='idPagador' value='" + idDevedor + "' />" +
                                "  </div>" +
                                "  <div class='col-md-2'>" +
                                "    <label>&nbsp</label>" +
                                "    <input type='submit' class='btn btn-success' value='Atualizar' onclick='return verificaValorAPagar(" + valorMax + ");' />" +
                                "  </div>" +
                                "</div> </form>";
                        $("#divDevedor").html(html);
                    } else {
                        $("#divDevedor").html("");
                    }

                });
            });
        </script>
    </head>

    <body>
        <div id="wrapper">

            <?php include "./menu.php"; ?>

            <!-- Page Content -->
            <div id="page-wrapper">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-lg-12">
                            <h1 class="page-header">Detalhes conta</h1>
                        </div>
                        <!-- /.col-lg-12 -->
                    </div>
                    <!-- /.row -->

                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4 class="panel-title">
                                Pagamento
                            </h4>
                        </div>
                        <div class="panel-body">
                            <?php
                            echo "<ul>";
                            $contaFechada = true;
                            foreach ($conta->getIntegrantes() as $integrante) {
                                echo "<li><p><font size='4'> " . $integrante->getUsuario()->getNome();
                                if ($integrante->isDono()) {
                                    echo " contribuiu na conta em R$ " . $formato->numeroInterface($integrante->getValorPagoConta()) . " e ";
                                }

                                if ($integrante->getValorTotalReceber() > 0) {
                                    echo " precisa receber: R$ " . $formato->numeroInterface($integrante->getValorAReceber()) . " de R$ " . $formato->numeroInterface($integrante->getValorTotalReceber());
                                    $contaFechada = $contaFechada && ($integrante->getValorAReceber() > 0);
                                } else {
                                    echo " precisa pagar: R$ " . $formato->numeroInterface($integrante->getValorAPagar()) . " de R$ " . $formato->numeroInterface($integrante->getValorTotalPagar());
                                    $contaFechada = $contaFechada && ($integrante->getValorAPagar() > 0);
                                }
                                echo "</p></font></li>";
                            }
                            echo "</ul>";
                            ?>
                            <?php
                            if ($conta->getIntegrante($usuario->getId())->precisaReceber()) {
                                ?>
                                <div class='text-right'>
                                    <input type='button' value='Atualizar Conta' class='btn btn-success' data-toggle="modal" data-target="#modalAtualizarConta" />    
                                </div>
                                <?php
                            }
                            ?>
                        </div>
                    </div>

                    <!-- Modal Pagamento -->
                    <div class="modal fade" id="modalAtualizarConta" tabindex="-1" role="dialog">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    Atualizar conta
                                </div>
                                <div class="modal-body ">
                                    <div class='form-group'>
                                        <label>Selecione um devedor: </label>
                                        <select class='form-control' id='selecionaDevedor'>
                                            <option value='-1;-1;-1'>Selecione um devedor</option>
                                            <?php
                                            $integranteUsuario = $conta->getIntegrante($usuario->getId());
                                            foreach ($conta->getIntegrantes() as $integrante) {
                                                if ($integrante->precisaPagar()) {
                                                    if ($integranteUsuario->getValorAReceber() > $integrante->getValorAPagar()) {
                                                        $valorMax = $integrante->getValorAPagar();
                                                    } else {
                                                        $valorMax = $integranteUsuario->getValorAReceber();
                                                    }
                                                    $valorOption = $integrante->getUsuario()->getId() . ";" . $integrante->getUsuario()->getNome() . ";" . $valorMax;
                                                    $nome = $integrante->getUsuario()->getNome();
                                                    echo "<option value='$valorOption'>" . $nome . "</option>";
                                                }
                                            }
                                            ?>
                                        </select>
                                        <hr/>
                                        <div id='divDevedor'>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Sair</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- ./ Modal Pagamento -->

                    <!-- Informações da conta -->
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4 class="panel-title">
                                <a data-toggle="collapse" data-parent="#accordion" href="#collapseThree">Informações da conta</a>
                            </h4>
                        </div>
                        <div class="panel-body">
                            <div class="row form-group">
                                <div class="col-md-12 " >
                                    <label class="control-label" for="nomeConta">Nome conta: </label>
                                    <span id='nomeConta'><?php echo $conta->getNome(); ?></span>
                                </div>
                            </div>
                            <div class="row form-group">
                                <div class="col-md-3">
                                    <label>Data: </label>
                                    <span><?php echo $conta->getData()->format("d/m/Y"); ?></span>
                                </div>
                                <div class="col-md-3">
                                    <label>Data alerta:</label>
                                    <span><?php echo $dataAlerta; ?> <span/>
                                </div>
                                <div class="col-md-6">
                                    <label>Grupo:</label>
                                    <span><?php echo $nomeGrupo; ?></span>
                                </div>
                            </div>
                            <div class="row form-group">
                                <div class="col-md-12">
                                    <label>Informações adicionais:</label>
                                    <textarea class="form-control" rows="5" id="comment" name="descricaoAdicional" readonly=""><?php echo $descricaoAdicional; ?></textarea>
                                </div>
                            </div>
                        </div>
                    </div>




                    <!-- ./Informações da conta -->

                    <!-- Proprietários -->
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4 class="panel-title">
                                <a data-toggle="collapse" data-parent="#accordion" href="#collapseThree">Proprietário</a>
                            </h4>
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="form-group">
                                    <div class="col-md-12">
                                        <table class="table" >
                                            <thead>
                                                <tr>
                                                    <th width="30%">Nome</th>
                                                    <th>Email</th>
                                                    <th width="30%">Valor</th>
                                                </tr>
                                            </thead>
                                            <tbody id='tabelaProprietarios'>
                                                <?php
                                                foreach ($integrantes as $integrante) {
                                                    if ($integrante->isDono()) {
                                                        echo "<tr>" .
                                                        "   <td>" . $integrante->getUsuario()->getNome() . "</td>" .
                                                        "    <input type='hidden' name='idUsuarioProprietario[]' value='" . $integrante->getUsuario()->getId() . "' />" .
                                                        "   <td>" . $integrante->getUsuario()->getEmail() . "</td>" .
                                                        "   <td> R$ " . $formato->numeroInterface($integrante->getValorPagoConta()) .
                                                        "     <input type='hidden' name='valorPagoProprietario[]' value='" . $formato->numeroInterface($integrante->getValorPagoConta()) . "' />" .
                                                        "   </td>" .
                                                        " </tr>";
                                                    }
                                                }
                                                ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- ./ Proprietários -->

                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4 class="panel-title">
                                <a data-toggle="collapse" data-parent="#accordion" href="#collapseThree">Itens da conta</a>
                            </h4>
                        </div>
                        <div class="panel-body">
                            <!-- Itens da conta -->

                            <div class="row">
                                <div class="col-md-12">
                                    <table class="table" >
                                        <thead>
                                            <tr>
                                                <th width="30%">Nome</th>
                                                <th width="20%">Valor</th>
                                                <th width="50%">Distribuição</th>
                                            </tr>
                                        </thead>
                                        
                                        <tbody id="tabelaItens">
                                            <?php
                                            if (count($itens) > 0) {
                                                foreach ($itens as $item) {
                                                    echo "<tr>";
                                                    echo "<td>" . $item->getNome() . "<input type='hidden' value='" . $item->getNome() . "' name='nomeItem[]' /></td>";
                                                    echo "<td> R$ " . $formato->numeroInterface($item->getValor()) . "<input type='hidden' value='" . $formato->numeroInterface($item->getValor()) . "' name='valorItem[]' /></td>";
                                                    echo "<td>" .
                                                    "<ul> ";
                                                    foreach ($item->getDistribuicoes() as $distribuicao) {
                                                        echo "<li>";
                                                        echo "  <label>" . $distribuicao->getUsuario()->getNome() . ":</label> R$ " .
                                                        $formato->numeroInterface($distribuicao->getValor());
                                                        echo "</li>";
                                                    }
                                                    echo "</ul> </td>";
                                                    echo "</tr>";
                                                }
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- ./Itens da conta -->

                    <!-- modal Erro -->
                    <div class="modal fade" id="modalErroAviso" tabindex="-1" role="dialog">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    Erro
                                </div>
                                <div class="modal-body" id='avisoModal'>

                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Okay</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /. Modal Erro -->
                    <?php //if ($contaFechada) { ?>
                        <form action='CadastroConta.php'>
                            <input type='hidden' value='<?php echo $idConta; ?>' name='idConta' />
                            <center><input type="submit" class="btn btn-default" value="Refazer conta" id='btCadastrarConta' /></center>
                        </form>
                    <?php //} ?>
                    <!-- /.container-fluid -->
                </div>
                <!-- /#page-wrapper -->
            </div>
            <!-- /#wrapper -->
    </body>
</html>
