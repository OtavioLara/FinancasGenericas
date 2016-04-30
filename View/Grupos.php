<?php
include "../ScriptLogin.php";
$republicaDAO = new RepublicaDAO($conexao);
$convites = $republicaDAO->getConviteParaUsuario($usuario->getId());
$grupos = $republicaDAO->getRepublicasCompletaPorIdUsuario($usuario->getId());




?>
<!DOCTYPE html>
<html lang="en">
    <head>

        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="">
        <meta name="author" content="">

        <title>Finanças Genérica - Grupos</title>

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

        <!-- Menu JS -->
        <script src="js/menu.js"></script>
        <script>idUsuario = <?php echo $usuario->getId(); ?>;</script>

          <!-- Script Popup requerimento -->
        <?php include "./RequerimentoPopup.php"; ?>
          
        <script>
            $(function () {
                $('#dataTableGrupos').DataTable({
                    responsive: true
                });
                $("#btCadastrarGrupo").click(function () {
                    if ($("#tfNomeGrupo").val() == "") {
                        return false;
                    } else {
                        return true;
                    }

                });

                $("#btAdicionarIntegrante").click(function () {
                    var email = $("#integranteParaAdicionar").val();
                    checkUsuario(email);
                });

                $("#btCancelarIntegrante").click(function () {
                    $("#modalAlerta").html("");
                });


                function checkUsuario(email) {
                    $("#carregandoUsuario").html("Procurando usuário...");
                    $("#modalAlerta").html("");
                    var url = "ScriptsAJAX/scriptExisteUsuario.php?email=" + email;
                    var xmlhttp = new XMLHttpRequest();
                    xmlhttp.onreadystatechange = function () {
                        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                            var texto = xmlhttp.responseText;
                            var usuario = JSON.parse(texto);
                            if (usuario != false) {
                                $("#integranteParaAdicionar").val("");
                                $("#modalAlerta").html("");
                                $('#modalAdicionarIntegrante').modal('hide');
                                adicionaIntegrante(usuario);
                            } else {
                                var divAlerta = "<div class='alert alert-danger'>" +
                                        "O usuário não foi encontrado" +
                                        "</div>";
                                $("#modalAlerta").html(divAlerta);
                            }
                            $("#carregandoUsuario").html("");
                        }
                    }
                    xmlhttp.open("GET", url, true);
                    xmlhttp.send();
                }

                function adicionaIntegrante(integrante) {
                    var inputEmail = "<input type='hidden' name='integrantesNovoGrupo[]' value='" + integrante.id + "'/>";
                    var novaLinha = "<tr>" +
                            "<td>" + integrante.nome + "</td>" +
                            "<td>" + integrante.email + "</td>" +
                            "<td> <button class='btn btn-default' onclick='clickBtRemoverIntegrante(this)'>Remover</button></td>" +
                            inputEmail +
                            "</tr>";
                    $("#integrantesNovoGrupo").append(novaLinha);
                }

            });
            function mostraModalGrupoAviso() {
                $('#modalGrupoAviso').modal('show');
                return false;
            }

            function clickBtRemoverIntegrante(bt) {
                var td = bt.parentNode;
                var tr = td.parentNode;
                var table = tr.parentNode;
                table.removeChild(tr);
            }

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
                            <h1 class="page-header">Grupos</h1>
                        </div>
                        <!-- /.col-lg-12 -->
                    </div>
                    <!-- /.row -->




                    <!-- Convites-->
                    <?php if (count($convites) > 0) { ?>
                        <div class="row">
                            <div class="col-md-6 col-md-offset-3">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        Convites
                                    </div>
                                    <!-- /.panel-heading -->
                                    <div class="panel-body">
                                        <div class="table-responsive">
                                            <table class="table">
                                                <tbody>
                                                    <?php
                                                    foreach ($convites as $convite) {
                                                        $idConvite = $convite->getId();
                                                        echo "<tr>";
                                                        echo "<td width='65%'>" . $convite->getRepublica()->getNome() . "</td>";
                                                        /* Botão aceitar */
                                                        echo "<td>" .
                                                        "<form action='ControlesScript/ControleConviteScript.php' method='post'>" .
                                                        "<input type='hidden' name='resposta' value='true' /> " .
                                                        "<input type='hidden' name='idConvite' value='$idConvite' /> " .
                                                        "<input type='hidden' name='comando' value='responder' /> " .
                                                        "<input type='submit' class='btn btn-success btn-xs' value='Aceitar' />" .
                                                        "</form>" .
                                                        "</td>";
                                                        /* Botão rejeitar */
                                                        echo "<td>" .
                                                        "<form action='ControlesScript/ControleConviteScript.php' method='post'>" .
                                                        "<inpu type='hidden' name='resposta' value='false' /> " .
                                                        "<input type='hidden' name='idConvite' value='$idConvite' /> " .
                                                        "<input type='hidden' name='comando' value='responder' /> " .
                                                        "<input type='submit' class='btn btn-danger btn-xs' value='Rejeitar' /> " .
                                                        "</form>" .
                                                        "</td>";
                                                    }
                                                    ?>
                                                </tbody>
                                            </table>
                                        </div>
                                        <!-- /.table-responsive -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                    <!-- /.Convites -->

                    <!-- Tabela Grupo -->

                    <!-- modal Grupo Aviso -->
                    <div class="modal fade" id="modalGrupoAviso" tabindex="-1" role="dialog">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    Aviso
                                </div>
                                <div class="modal-body ">
                                    Você é o único administrador desse grupo, deixe alguém como administrador para sair.
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Okay</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /. Modal Grupo Aviso -->

                    <div class="row">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                Contas
                            </div>
                            <!-- /.panel-heading -->

                            <div class="panel-body">
                                <div class="dataTable_wrapper">
                                    <table class="table table-striped table-bordered table-hover" id="dataTableGrupos">
                                        <thead>
                                            <tr>
                                                <th width="80%">Grupo</th>
                                                <th>Sair</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $idUsuario = $usuario->getId();
                                            foreach ($grupos as $grupo) {
                                                $onclick = ($grupo->podeSair($idUsuario)) ?  "" : "onclick='return mostraModalGrupoAviso()'" ;
                                                $idGrupo = $grupo->getId();
                                                echo "<tr>";
                                                echo "<td>" . $grupo->getNome() . "</td>";
                                                echo "<td>" .
                                                "<form action='ControlesScript/ControleRepublicaScript.php' method='post'>" .
                                                "<input type='hidden' name='idUsuario' value='$idUsuario'> " .
                                                "<input type='hidden' name='idGrupo' value='$idGrupo'> " .
                                                "<input type='hidden' name='comando' value='sairGrupo'> " .
                                                "<input type='submit' $onclick class='btn btn-danger btn-block' value='Sair' />" .
                                                "</form>" .
                                                "</td>";
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

                    <form action="ControlesScript/ControleRepublicaScript.php" id="formCadastrarGrupo" method="post">
                        <input type="hidden" name="comando" value="inserir" />
                        <input type="hidden" name="idCriador" value="<?php echo $usuario->getId(); ?>" />
                        <div class="row">
                            <div class="">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        Criar Grupo
                                    </div>
                                    <div class="panel-body">
                                        <div class="row">
                                            <div class=" col-md-12 form-horizontal">

                                                <label class="control-label col-md-1" for="nome">Nome:</label>
                                                <div class="col-md-11">
                                                    <input type="text" class="form-control" id="tfNomeGrupo" name="nomeRepublica">
                                                </div>

                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-7">
                                                <table class="table" >
                                                    <thead>
                                                        <tr>
                                                            <th width="50%">Nome</th>
                                                            <th>Email</th>
                                                            <th>Remover</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="integrantesNovoGrupo">
                                                        <tr>
                                                            <td><?php echo $usuario->getNome(); ?></td>
                                                            <td>
                                                                <?php echo $usuario->getEmail(); ?>
                                                                <input type="hidden" name="integrantesNovoGrupo[]" value="<?php echo $usuario->getId(); ?>"/>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <input type="button" class="btn btn-default" data-toggle="modal" data-target="#modalAdicionarIntegrante"  value='Adicionar integrante' />
                                        <center><input type="submit" class="btn btn-default" value="Cadastrar grupo" id="btCadastrarGrupo" form="formCadastrarGrupo"/></center>

                                        <!-- Modal -->
                                        <div class="modal fade" id="modalAdicionarIntegrante" tabindex="-1" role="dialog" 
                                             aria-labelledby="myModalLabel" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <!-- Modal Header -->
                                                    <div class="modal-header">
                                                        <h4 class="modal-title" id="myModalLabel">
                                                            Adicionar integrante
                                                        </h4>
                                                    </div>

                                                    <!-- Modal Body -->
                                                    <div class="modal-body">
                                                        <div id="modalAlerta"></div>
                                                        <div class="form-group">
                                                            <label for="integranteParaAdicionar">Email do usuário:</label>
                                                            <input type="text" class="form-control" id="integranteParaAdicionar" placeholder="Enter email"/>
                                                        </div>
                                                        <div id="carregandoUsuario"></div>
                                                    </div>

                                                    <!-- Modal Footer -->
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-default"
                                                                data-dismiss="modal" id="btCancelarIntegrante">
                                                            Cancelar
                                                        </button>
                                                        <button type="button" class="btn btn-primary" id="btAdicionarIntegrante" onclick="teste()">
                                                            Adicionar
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- .Modal -->
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /.col-md-12 -->
                    </form>


                </div>
                <!-- /.container-fluid -->
            </div>
            <!-- /#page-wrapper -->

        </div>
        <!-- /#wrapper -->

    </body>

</html>
