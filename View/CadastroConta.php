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
            $idGrupo = $conta->getRepublica()->getId();
            $republicaDAO = new RepublicaDAO($conexao);
            $integrantesGrupo = $republicaDAO->getUsuariosRepublica($conta->getRepublica()->getId());
        } else {
            $nomeGrupo = "Sem grupo";
            $idGrupo = 0;
            $integrantesGrupo = array();
        }
        $dataAtual = $conta->getData()->format("Y-m-d");
        $descricaoAdicional = $conta->getDescricaoAdicional();
        $dataAlerta = $conta->getDataAlerta();
        if (isset($dataAlerta)) {
            $dataAlerta = "value='" . $dataAlerta->format("Y-m-d") . "'";
        } else {
            $dataAlerta = "";
        }
        $comando = "alterarConta";
    }else{
        header('Location: Index.php');
    }
} else {
    $idConta = -1;
    $nomeConta = "";
    $itens = array();
    $integrantes = array();
    $integrantesGrupo = array();
    $nomeGrupo = "Selecione um grupo";
    $idGrupo = -1;
    $dataAtual = new DateTime();
    $dataAtual = $dataAtual->format("Y-m-d");
    $descricaoAdicional = "";
    $dataAlerta = "";
    $comando = "inserir";
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

        <title>Finanças Genérica - Cadastro conta</title>

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

        <!-- Conta JS -->
        <script src='js/calculosConta.js'></script>
        <script src="js/conta.js"></script>

        <script>
            var idGrupo = <?php echo $idConta; ?>;
            if (idGrupo > 0) {
                carregaIntegrantesGrupo(idGrupo);
            }
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
                            <h1 class="page-header">Cadastro conta</h1>
                        </div>
                        <!-- /.col-lg-12 -->
                    </div>
                    <!-- /.row -->

                    <form action="ControlesScript/ControleContaScript.php" method="post">
                        <input type='hidden' value='<?php echo $comando; ?>' name='comando' />
                        <input type='hidden' value='<?php echo $idConta; ?>' name='idConta' />
                        <input type='hidden' value="<?php echo $nomeConta; ?>" name='nomeAntigoConta' />
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
                                        <input type="text" class="form-control" id="nomeConta" name="nomeConta" value="<?php echo $nomeConta; ?>" />
                                    </div>
                                </div>
                                <div class="row form-group">
                                    <div class="col-md-3">
                                        <label>Data: </label>
                                        <input type="date" class="form-control" name="dataConta" value="<?php echo $dataAtual; ?>"/>
                                    </div>
                                    <div class="col-md-3">
                                        <label>Data alerta:</label>
                                        <input type="date" class="form-control" name="dataAlerta" <?php echo $dataAlerta; ?> />
                                    </div>
                                    <div class="col-md-6">
                                        <label>Grupo:</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" id="nomeGrupo" value="<?php echo $nomeGrupo; ?>" <?php if ($idGrupo < 0) { ?>data-toggle="modal" data-target="#modalSelecionaGrupo" <?php } ?> readonly>
                                            <input type="hidden" id="idGrupo" name="idGrupo" value='<?php echo $idGrupo; ?>'/>
                                            <span class="input-group-btn">
                                                <button class="btn btn-default" type="button"  data-toggle="modal" data-target="#modalSelecionaGrupo" <?php if ($idGrupo >= 0) echo "disabled"; ?>>Selecionar grupo</button>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row form-group">
                                    <div class="col-md-12">
                                        <label>Informações adicionais:</label>
                                        <textarea class="form-control" rows="5" id="comment" name="descricaoAdicional"><?php echo $descricaoAdicional; ?></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <!-- Modal Grupos -->
                        <div class="modal fade" id="modalSelecionaGrupo" tabindex="-1" role="dialog" 
                             aria-labelledby="myModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <!-- Modal Header -->
                                    <div class="modal-header">
                                        <h4 class="modal-title" id="myModalLabel">
                                            Meus grupos
                                        </h4>
                                    </div>

                                    <!-- Modal Body -->
                                    <div class="modal-body">
                                        <div class="form-group">
                                            <label for="integranteParaAdicionar">Grupo:</label>
                                            <select class="form-control" id="checkboxSelecionaGrupo">
                                                <option id="grupo0">Selecione um grupo</option>
                                                <?php
                                                foreach ($gruposMenu as $grupo) {
                                                    $idGrupo = $grupo->getId();
                                                    echo "<option id='grupo$idGrupo' >" . $grupo->getNome() . "</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <div id="integrantesFiltroGrupo">
                                        </div>
                                    </div>

                                    <!-- Modal Footer -->
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default"
                                                data-dismiss="modal" id="btCancelaSelecionaGrupo">
                                            Cancelar
                                        </button>
                                        <button type="button" class="btn btn-primary" id="btSelecionaGrupo">
                                            Selecionar
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- ./Modal Grupos -->

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


                                        <div class="col-md-4">
                                            <label>Integrante: </label>
                                            <select class="form-control" id='selectProprietario'>
                                                <option value='-1;-1;-1'>Selecione um integrante</option>
                                                <?php
                                                foreach ($integrantesGrupo as $usuario) {
                                                    $value = $usuario->getId() . ";" . $usuario->getNome() . ";" . $usuario->getEmail();
                                                    echo "<option value='$value'>" . $usuario->getNome() . "</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <label>Valor: </label>
                                            <input type="text" id='valorPago' class="form-control" placeholder="R$">
                                        </div>
                                        <div class="col-md-4">
                                            <label for="btAdicionaProprietario">&nbsp;</label>
                                            <input type="button" class="btn btn-success form-control" id="btAdicionaProprietario" value="Adicionar" />
                                        </div>


                                        <div class="col-md-12">
                                            <table class="table" >
                                                <thead>
                                                    <tr>
                                                        <th width="30%">Nome</th>
                                                        <th>Email</th>
                                                        <th width="30%">Valor</th>
                                                        <th>Remover</th>
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
                                                            "   <td> <input type='button' class='btn btn-danger' value='Remover' onclick='removeLinha(this)' name='btRemoverProprietario' /> </td>" .
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
                                        <div class="form-group">
                                            <div class="col-md-4">
                                                <label>Nome item: </label>
                                                <input type="text" class="form-control" id="nomeItem">
                                            </div>
                                            <div class="col-md-2">
                                                <label>Valor: </label>
                                                <input type="text" class="form-control" placeholder="R$" id="valorItem">
                                            </div>
                                            <div class="col-md-4">
                                                <label for="btAdicionaProprietario">&nbsp;</label>
                                                <input type="button" class="btn btn-success form-control" id="btAdicionaItem" value="Adicionar" />
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Integrantes Distribuição -->
                                <br/>
                                <div class="row">
                                    <div class="col-md-2">
                                        <div class='form-group' id="integrantesGrupo0">
                                            <?php
                                            $index = 0;
                                            foreach ($integrantesGrupo as $usuario) {
                                                $value = $usuario->getId() . ";" . $usuario->getNome() . ";" . $usuario->getEmail();
                                                if ($index % 4 == 0) {
                                                    echo "<div class='col-md-12'> " .
                                                    "<input type='checkbox' name='integrantesItem' value='" . $value . "' checked /> " . $usuario->getNome() .
                                                    "</div>";
                                                }
                                                $index++;
                                            }
                                            ?>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class='form-group' id="integrantesGrupo1">
                                            <?php
                                            $index = 0;
                                            foreach ($integrantesGrupo as $usuario) {
                                                $value = $usuario->getId() . ";" . $usuario->getNome() . ";" . $usuario->getEmail();
                                                if ($index % 4 == 1) {
                                                    echo "<div class='col-md-12'> " .
                                                    "<input type='checkbox' name='integrantesItem' value='" . $value . "' checked /> " . $usuario->getNome() .
                                                    "</div>";
                                                }
                                                $index++;
                                            }
                                            ?>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class='form-group' id="integrantesGrupo2">
                                            <?php
                                            $index = 0;
                                            foreach ($integrantesGrupo as $usuario) {
                                                $value = $usuario->getId() . ";" . $usuario->getNome() . ";" . $usuario->getEmail();
                                                if ($index % 4 == 2) {
                                                    echo "<div class='col-md-12'> " .
                                                    "<input type='checkbox' name='integrantesItem' value='" . $value . "' checked /> " . $usuario->getNome() .
                                                    "</div>";
                                                }
                                                $index++;
                                            }
                                            ?>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class='form-group' id="integrantesGrupo3">
                                            <?php
                                            $index = 0;
                                            foreach ($integrantesGrupo as $usuario) {
                                                $value = $usuario->getId() . ";" . $usuario->getNome() . ";" . $usuario->getEmail();
                                                if ($index % 4 == 3) {
                                                    echo "<div class='col-md-12'> " .
                                                    "<input type='checkbox' name='integrantesItem' value='" . $value . "' checked /> " . $usuario->getNome() .
                                                    "</div>";
                                                }
                                                $index++;
                                            }
                                            ?>
                                        </div>
                                    </div>
                                </div>
                                <!-- ./Integrantes Distribuição -->

                                <div class="row">
                                    <div class="col-md-12">
                                        <table class="table" >
                                            <thead>
                                                <tr>
                                                    <th width="30%">Nome</th>
                                                    <th width="10%">Valor</th>
                                                    <th width="50%">Distribuição</th>
                                                    <th>Remover</th>
                                                </tr>
                                            </thead>
                                            <tbody id="tabelaItens">
                                                <?php
                                                if (count($itens) > 0) {
                                                    foreach ($itens as $item) {
                                                        echo "<tr>";
                                                        echo "<td>" . $item->getNome() . "<input type='hidden' value='" . $item->getNome() . "' name='nomeItem[]' /></td>";
                                                        echo "<td>" . $formato->numeroInterface($item->getValor()) . "<input type='hidden' value='" . $formato->numeroInterface($item->getValor()) . "' name='valorItem[]' /></td>";
                                                        echo "<td> <div name='divAlerta[]'></div>" .
                                                        "<ul> " .
                                                        "<input  type='hidden' name='totalIntegrantesItem[]' class='form-control' value='" . count($item->getDistribuicoes()) . "' />";
                                                        foreach ($item->getDistribuicoes() as $distribuicao) {
                                                            echo "<li>" .
                                                            "<div class='form-inline'>" .
                                                            "  <label>" . $distribuicao->getUsuario()->getNome() . ":</label> " .
                                                            "  <div class='input-group' name='teste' >" .
                                                            "    <input type='hidden' name='idUsuarioItem[]' class='form-control' value='" . $distribuicao->getUsuario()->getId() . "' />" .
                                                            "    <input type='text' name='valorPagoUsuarioItem[]' oninput='tfValorAPagarUsuarioItemOnChange(this)' class='form-control' size='6' value='" . $formato->numeroInterface($distribuicao->getValor()) . "' />" .
                                                            "    <span class='input-group-btn'>" .
                                                            "      <input type='button' class='btn btn-default' value='X' />" .
                                                            "    </span>" .
                                                            "  </div>" .
                                                            "</div>" .
                                                            "</li>";
                                                        }
                                                        echo "</ul> </td>";
                                                        echo "<td><input type='button' value='Remover' class='btn btn-danger' onclick='removeLinha(this)' /></td>";
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

                        <?php $valorBt = ($idConta == -1 ) ? "cadastrar" : "Refazer"; ?>
                        <center><input type="submit" class="btn btn-default" value="<?php echo $valorBt ?>" id='btCadastrarConta' /></center>
                    </form>
                    <!-- /.container-fluid -->
                </div>
                <!-- /#page-wrapper -->
            </div>
            <!-- /#wrapper -->
    </body>
</html>
