<?php
if (isset($_GET['idGrupo'])) {
    include "../../scriptRequires.php";
    $republicaDAO = new RepublicaDAO;
    $idGrupo = $_GET['idGrupo'];
    $usuarios = $republicaDAO->getUsuariosRepublica($idGrupo);
    echo json_encode($usuarios);
}
?>
