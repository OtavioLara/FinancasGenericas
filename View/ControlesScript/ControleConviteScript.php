<form action="../MinhaRepublica.php"
<?php

include "../../ScriptLogin.php";
$comando = $_REQUEST['comando'];
$controleConvite = new ControleConvite($conexao);


if ($comando == "inserir") {
    $destinatario = $_POST['destinatario'];
    $email = $_POST['email'];
    $idRepublica = $_POST['idRepublica'];
    $controleConvite->insereConvite($idRepublica, $email, $destinatario);
    header('Location: ../MinhaRepublica.php?id='.$idRepublica);
} elseif ($comando == "responder") {
    $resposta = $_GET['resposta'];
    $idConvite = $_GET['idConvite'];
    if ($resposta) {
        $controleConvite->aceitaConvite($idConvite);
    } else {
        $controleConvite->rejeitaConvite($idConvite);
    }
    header('Location: ../MinhaRepublica.php?id='.$idRepublica);
}
?>

