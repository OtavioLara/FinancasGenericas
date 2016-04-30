<?php
if (isset($_GET['email'])) {
    include "../../scriptRequires.php";
    $email = $_GET['email'];
    $usuarioDAO = new UsuarioDAO();
    $usuario = $usuarioDAO->getUsuarioPorEmail($email);
    if(isset($usuario)){
        echo $usuario->jsonSerialize();
    }else{
        echo "false";
    }
} else {
    echo "false";
}
?>




