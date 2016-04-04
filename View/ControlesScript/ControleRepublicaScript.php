<?php

include "../../ScriptLogin.php";
$comando = $_POST['comando'];
$controleRepublica = new ControleRepublica($conexao);

if ($comando == "inserir") {
    $nomeRepublica = $_POST['nome'];
    $controleRepublica->insereRepublica($usuario->getId(), $nomeRepublica);
    header('Location: ../Perfil.php');
}
?>