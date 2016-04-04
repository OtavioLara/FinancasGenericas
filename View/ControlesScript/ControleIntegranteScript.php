<?php

include "../../ScriptLogin.php";
$controleRepublica = new ControleRepublica($conexao);
$comando = $_REQUEST['comando'];
$idUsuario = $_REQUEST['idUsuario'];
$idRepublica = $_REQUEST['idRepublica'];


if ($comando == "alterar") {
    $administrador = $_REQUEST['administrador'];
    $controleRepublica->tornaIntegranteAdministrador($idUsuario, $idRepublica, $administrador);
} elseif ($comando == "remover") {
    $controleRepublica->removeIntegrante($idUsuario, $idRepublica);
}

header('Location: ../MinhaRepublica.php?id='.$idRepublica);
?>