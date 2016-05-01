<?php
session_start();
if (!isset($_SESSION['logado']) || $_SESSION['logado'] == false) {
    session_destroy();
    header('Location: View/Login.php');
} else {
    header('Location: View/Index.php');
}
?>