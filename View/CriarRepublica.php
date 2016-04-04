<?php
include "../ScriptLogin.php";
?>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    </head>
    <form action="ControlesScript/ControleRepublicaScript.php" method="post">
        Nome Republica: <input type="text" name="nome" autocomplete="off" /> <br/>
        <input type="hidden" name="comando" value="inserir" />
        <center><input type="submit" value="Cadastrar" /></center>
    </form>

</html>
