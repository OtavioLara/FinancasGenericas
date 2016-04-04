<?php
include "../ScriptLogin.php";
$republicaDAO = new RepublicaDAO($conexao);
$contaDAO = new ContaDAO($conexao);

$republica = $republicaDAO->getRepublicaPorId_Incompleto($_GET['id']);
if (!isset($republica)) {
    header('Location: Perfil.php');
}
$integrantes = $republicaDAO->getIntegrantesRepublica($republica->getId());
$arrayJavaScriptId = "";
$arrayJavaScriptNome = "";
foreach ($integrantes as $integrante) {
    $arrayJavaScriptId .= $integrante->getUsuario()->getId() . ",";
    $arrayJavaScriptNome .= '"' . $integrante->getUsuario()->getNome() . '",';
}
$arrayJavaScriptId = substr($arrayJavaScriptId, 0, -1);
$arrayJavaScriptNome = substr($arrayJavaScriptNome, 0, -1);
?>

<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title></title>
        <link rel="stylesheet" href="css/bootstrap.min.css">
        <script src="js/jquery.min.js"></script>
        <script src="js/jquery-ui.min.js"></script>
        <script src="js/bootstrap.min.js"></script>
        <script src="js/calculosConta.js"></script>
        <script src="js/interfaceConta.js"></script>

        <script>
            var todosIntegrantesNome = [<?php echo $arrayJavaScriptNome; ?>];
            var todosIntegrantesId = [<?php echo $arrayJavaScriptId; ?>];
        </script>

    </head>
    <form action="ControlesScript/ControleContaScript.php" method="post">
        NomeConta: <input type="text" id="nomeConta" name="nomeConta" />
        <h2>Quem pagou a conta?</h2>
        Nome: <input type="text" id="nomeProp" onclick="showModalProp()" autocomplete='off'/> Valor: <input type="text" id="valorProp" size="2" autocomplete='off'/>
        <input type="hidden" id="idProp" />
        <button type="button" onclick="adicionaProprietario()"> Adicionar </button>
        <br/><br/>
        <table id="tabelaProp" border="1" width="30%">
            <th>Nome</th>
            <th>Valor</th>
            <th>Remover</th>
        </table>
        <hr>
        <h2>Lista de Itens da Conta</h2>
        Nome: <input type="text" id="nomeItem"  autocomplete='off'/>
        Valor: <input type="text" id="valorItem"  autocomplete='off'/>
        <button type="button" onclick="adicionarItem()" > adicionar </button>
        <button type="button" data-target="#modalFiltro"  data-toggle="modal" > Filtrar integrantes </button>
        <div id="distribuicao">
            <?php
            foreach ($integrantes as $integrante) {
                $nomeUsuario = $integrante->getUsuario()->getNome();
                $idUsuario = $integrante->getUsuario()->getId();
                echo "<input type='checkbox' value='$idUsuario;$nomeUsuario' name='distribuicao[]' checked> "
                . $nomeUsuario . " ";
            }
            ?>
        </div>
        <table id="tabelaItem" border="1" width="60%">
            <th>Nome</th>
            <th>Valor</th>
            <th>Distribuicao</th>
            <th>Remover</th>

        </table>
        <input type="hidden" value="<?php echo $republica->getId(); ?>" name="idRepublica" />
        <input type="hidden" value="inserir" name="comando" />
        <input type="submit" onclick="return check();" value="Cadastrar" />

    </form>

    <!-- Modal Distribuicao -->
    <div class="modal fade" id="modalDistribuicao" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Filtre os integrantes</h4>
                </div>
                <div class="modal-body" id="integrantesASerAdicionado">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" data-dismiss="modal" onclick="modalDistribuicaoClick()">Save changes</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Filtro -->
    <div class="modal fade" id="modalFiltro" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Filtre os integrantes</h4>
                </div>
                <div class="modal-body">
                    <?php
                    foreach ($integrantes as $integrante) {
                        $nomeUsuario = $integrante->getUsuario()->getNome();
                        $idUsuario = $integrante->getUsuario()->getId();
                        echo "<input type='checkbox' value='$idUsuario;$nomeUsuario' name='integrantes'> "
                        . $nomeUsuario . "<br/>";
                    }
                    ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" data-dismiss="modal" onclick="filtrar()">Save changes</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Proprietário -->
    <div class="modal fade" id="modalProp" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Selecione o proprietario da conta</h4>
                </div>
                <div class="modal-body">
                    <?php
                    foreach ($integrantes as $integrante) {
                        $nomeUsuario = $integrante->getUsuario()->getNome();
                        $idUsuario = $integrante->getUsuario()->getId();
                        echo "<input type='radio' value='$idUsuario;$nomeUsuario' name='integrantes'> "
                        . $nomeUsuario . "<br/>";
                    }
                    ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" data-dismiss="modal" onclick="setProprietario()">Save changes</button>
                </div>
            </div>
        </div>
    </div>
</html>

