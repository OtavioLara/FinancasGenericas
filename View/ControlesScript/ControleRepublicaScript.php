<?php

include "../../ScriptLogin.php";
$comando = $_POST['comando'];
$controleRepublica = new ControleRepublica($conexao);

if ($comando == "inserir") {
    $controleRepublica->insereRepublica($_POST);
    header('Location: ../Grupos.php');
} else if ($comando == "removerUsuario") {
    $idUsuario = $_POST['idUsuario'];
    $idGrupo = $_POST['idGrupo'];
    $controleRepublica->removeIntegrante($idUsuario, $idRepublica, false);
    header('Location: ../Grupos.php');
} else if ($comando == "sairGrupo") {
    $idUsuario = $_POST['idUsuario'];
    $idGrupo = $_POST['idGrupo'];
    $controleRepublica->removeIntegrante($idUsuario, $idGrupo, true);
    header('Location: ../Grupos.php');
}
?>