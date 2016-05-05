<?php

include "../../ScriptLogin.php";
$comando = $_REQUEST['comando'];
$controleConvite = new ControleConvite($conexao);
if ($comando == "inserir") {
    $destinatario = $_POST['destinatario'];
    $email = $_POST['email'];
    $idRepublica = $_POST['idRepublica'];
    $controleConvite->insereConvite($idRepublica, $email, $destinatario);
    header('Location: ../MinhaRepublica.php?id=' . $idRepublica);
} else if ($comando == "aceitaConvite") {
    $idConvite = $_POST["idConvite"];
    $controleConvite->aceitaConvite($idConvite);
    header('Location: ../Grupos.php');
} else if ($comando == "rejeitaConvite") {
    $idConvite = $_POST["idConvite"];
    $controleConvite->rejeitaConvite($idConvite);
    header('Location: ../Grupos.php');
}
?>

