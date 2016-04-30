<?php
include "../ScriptLogin.php";
$republicaDAO = new RepublicaDAO($conexao);
$republica = $republicaDAO->getRepublicaCompletaPorId($_GET['id']);
if (!isset($republica)) {
    header('Location: Perfil.php');
}
$administrador = $republicaDAO->isAdministrador($usuario->getId(), $republica->getId());
?>

<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    </head>
    <div>
        Voltar para <a href="Index.php"> perfil </a> <br/>
        <a href="CadastroConta.php?id=<?php echo $republica->getId() ?>">cadastrar conta</a> <br/>
        Nome Republica: <?php echo $republica->getNome(); ?> <br/>
        Integrantes:
        <ul>
            <?php
            $integranteUsuario = $republica->getIntegrante($usuario->getId());
            foreach ($republica->getIntegrantes() as $integrante) {
                echo "<li>";
                echo $integrante->toString();
                if ($integranteUsuario->isAdministrador() && $integrante->getUsuario()->getId() != $usuario->getId()) {
                    echo " <a href='ControlesScript/ControleIntegranteScript.php?comando=alterar"
                    . "&administrador=1&idUsuario=" . $integrante->getUsuario()->getId()
                    . "&idRepublica=" . $republica->getId() . "'> tornar administrador </a>";
                    echo "|<a href='ControlesScript/ControleIntegranteScript.php?comando=remover"
                    . "&idUsuario=" . $integrante->getUsuario()->getId() . "&idRepublica=" . $republica->getId()
                    . "'> remover integrante </a>";
                }
                echo "</li>";
            }
            ?>
        </ul>
        <?php
        if ($administrador) {
            ?>
            <hr>
            Enviar convite de republica : <br/>
            <form action="ControlesScript/ControleConviteScript.php" method="post">
                Email: <input type="text" name="email" />
                <input type="hidden" name="destinatario" value="U" />
                <input type="hidden" name="idRepublica" value="<?php echo $republica->getId(); ?>" />
                <input type="hidden" name="comando" value="inserir" />
                <input type="submit" value="enviar" />

            </form>
        <?php } ?>
    </div>
</html>

