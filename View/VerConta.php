<?php
include "../ScriptLogin.php";
$republicaDAO = new RepublicaDAO($conexao);
$contaDAO = new ContaDAO($conexao);

$idConta = $_REQUEST['id'];
$conta = $contaDAO->getConta($idConta);
$conta->setItens($contaDAO->getContaItem($conta->getId()));

$republica = $conta->getRepublica();
if (!isset($republica)) {
    header('Location: Perfil.php');
}
$integrantes = $republicaDAO->getIntegrantesRepublica($republica->getId());

$arrayJavaScriptId = "";
$arrayJavaScriptNome = "";
foreach ($integrantes as $integrante) {
    $arrayJavaScriptId = $arrayJavaScriptId . $integrante->getUsuario()->getId() . ",";
    $arrayJavaScriptNome = $arrayJavaScriptNome . '"' . $integrante->getUsuario()->getNome() . '",';
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
    Voltar para <a href="Perfil.php"> perfil </a> <br/>

    Valor Total: <?php echo $formato->numeroInterface($conta->getValorTotal()); ?> <br/>
    Data: <?php echo $conta->getData()->format('d/m/Y H:i:s'); ?> <br/>
    Integrantes: <br/>

    <?php
    $precisaReceber = false;
    foreach ($conta->getIntegrantes() as $integrante) {
        echo $integrante->getUsuario()->getNome();
        if ($integrante->isDono()) {
            echo " pagou na conta: " . $formato->numeroInterface($integrante->getValorPagoConta()) . " ";
        }
        if ($integrante->getValorTotalReceber() > 0) {
            echo " precisa receber: " . $formato->numeroInterface($integrante->getValorJaRecebido()) . "/"
            . $formato->numeroInterface($integrante->getValorTotalReceber());
        } else if ($integrante->getValorTotalPagar() > 0) {
            echo " ja pagou: " . $formato->numeroInterface($integrante->getValorJaPagou()) . "/"
            . $formato->numeroInterface($integrante->getValorTotalPagar());
        }
        echo "<br/>";
    }
    if ($conta->getIntegrante($usuario->getId())->precisaReceber()) {
        echo "<button type='button' class='btn btn-primary btn-lg' data-toggle='modal' data-target='#modalPagar'>";
        echo "  Receber";
        echo "</button>";
    }
    ?>



    <form action="ControlesScript/ControleContaScript.php" method="post">
        Nome Conta: <?php echo "<input type='text' value='" . $conta->getNome() . "'  id='nomeConta' name='nomeConta' autocomplete='off' />"; ?> <br/>
        <table width="30%" border="1" id="tabelaProp">
            <th> Nome Dono </th>
            <th> Valor </th>
            <?php
            foreach ($conta->getIntegrantes() as $integrante) {
                if ($integrante->isDono()) {
                    $nome = $integrante->getUsuario()->getNome();
                    $valor = $formato->numeroInterface($integrante->getValorPagoConta());
                    $id = $integrante->getUsuario()->getId();
                    echo "<tr>";
                    echo "  <td>";
                    echo "    <input type='text' value='$nome' size='15' name='nomeProp[]' autocomplete='off'/>";
                    echo "  </td>";

                    echo "  <td>";
                    echo "    <input type='text' value='$valor' size='2' name='valorProp[]' autocomplete='off'/>";
                    echo "  </td>";
                    echo "</tr>";
                    echo "<input type='hidden' value='$id' size='2' name='idProp[]' />";
                }
            }
            ?>
        </table>
        Itens:
        <br/>
        <table  id="tabelaItem" width="100%" class="table">
            <th>Nome Item</th>
            <th>Valor</th>
            <th>Integrantes</th>

            <?php
            $linha = 0;
            foreach ($conta->getItens() as $item) {
                $nome = $item->getNome();
                $valor = $formato->numeroInterface($item->getValor());

                echo "<tr>";
                echo "  <td>";
                echo "    <input type='text' value='$nome' name ='nomeItem[]' size='15' autocomplete='off' />";
                echo "  </td>";

                echo "  <td>";
                echo "    <input type='text' value='$valor' name='valorItem[]' size='2' autocomplete='off'/>";
                echo "  </td>";

                echo "  <td>";
                echo "  <button type='button' onclick='geraConteudoModalDistribuicao(this.parentNode.parentNode.rowIndex-1)' > Adicionar </button>";
                echo "    <ul name='listaDistribuicao[]'>";
                foreach ($item->getDistribuicoes() as $distribuicao) {
                    $nomeUsuario = $distribuicao->getUsuario()->getNome();
                    $idUsuario = $distribuicao->getUsuario()->getId();
                    $valorUsuario = $formato->numeroInterface($distribuicao->getValor());

                    echo "<li>";
                    echo "  $nomeUsuario : <input type='text' value='$valorUsuario' name='valorDistribuicaoItem[]' size='2' autocomplete='off' />";
                    echo "  <input type='hidden' value='$idUsuario' name='idDistribuicaoItem[]' size='2' />";
                    echo "  <button type='button' onclick='removerDistribuicao(this.parentNode)' >remover </button>";
                    echo "</li>";
                }
                echo "    </ul>";
                $totalIntegrantes = count($item->getDistribuicoes());
                echo "<input type='hidden' value='$totalIntegrantes' name='contadorDistribuicao[]' />";
                echo "  </td>";

                echo "</tr>";
                $linha++;
            }
            ?>
        </table>
        <input type="hidden" name="comando" value="alterarConta" />
        <input type="hidden" value="<?php echo $republica->getId(); ?>" name="idRepublica" />
        <input type="hidden" name="idConta" value='<?php echo $conta->getId(); ?>' />
        <input type="hidden" name="data" value='<?php echo $conta->getData()->format('Y-m-d H:i:s'); ?>' />
        <input type="hidden" name="nomeAntigo" value='<?php echo $conta->getNome(); ?>' />
        <center><input type="submit" value="terminar" onclick="return check()" /></center>

    </form> 





    <!-- Modal Pagamento -->
    <div class="modal fade" id="modalPagar" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Pagamento</h4>
                </div>
                <div class="modal-body" >
                    <?php
                    foreach ($conta->getIntegrantes() as $integrante) {
                        if ($integrante->precisaPagar()) {
                            $id = $integrante->getUsuario()->getId();
                            $valor = $formato->numeroInterface($integrante->getValorAPagar());
                            echo "<input type='radio' value='$id;$valor' name ='integrantes[]' /> "
                            . $integrante->getUsuario()->getNome() . "(".$valor.")<br/>";
                        }
                    }
                    ?>

                </div>
                <div class="modal-footer">
                    <form action="ControlesScript/ControleContaScript.php" method="post">
                        Valor: <input type="text" id="valorPago" name="valorPago" />
                        <input type="hidden" id="idPagador" name="idPagador" />
                        <input type="hidden" name="idRecebidor" value ="<?php echo $usuario->getId(); ?>" />
                        <input type="hidden" name="idConta" value ="<?php echo $conta->getId(); ?>" />
                        <input type="hidden" name="comando" value ="usuarioPagando" />
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" onclick="return setInputsPagar();">Save changes </button>
                    </form>

                </div>
            </div>
        </div>
    </div>

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

</html>
