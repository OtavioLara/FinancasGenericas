<?php

class ControleRepublica extends Controle {

    function __construct($conexao = null) {
        parent::__construct($conexao);
    }

    function insereRepublica($params) {
        /* Obtem objeto republica */
        $idCriador = $params["idCriador"];
        $idsIntegrantes = $params["integrantesNovoGrupo"];
        $nomeRepublica = $params['nomeRepublica'];
        $republica = new Republica(-1, $nomeRepublica);

        foreach ($idsIntegrantes as $idIntegrante) {
            $usuario = new Usuario($idIntegrante, "", "");
            $integrante = new Integrante($usuario, false);
            $republica->adicionaIntegrante($integrante);
        }

        /* Insere república no banco de dados */
        $republicaDAO = new RepublicaDAO($this->conexao);
        $idRepublica = $republicaDAO->inserirRepublica($republica, $idCriador);

        $republica->setId($idRepublica);

        /* Cria Notificações */
        foreach ($republica->getIntegrantes() as $integrante) {
            $usuario = $integrante->getUsuario();
            if ($usuario->getId() != $idCriador) {
                $mensagem = "Grupo " . $republica->getNome() . " lhe mandou um convite.";
                $this->insereNotificacao("Convite", $usuario, $republica, $mensagem);
            }
        }
    }

    function removeIntegrante($idIntegrante, $idRepublica, $usuarioSaindo = false) {
        $republicaDAO = new RepublicaDAO($this->conexao);
        $republicaDAO->removeIntegrante($idIntegrante, $idRepublica);

        /* Recupera República */
        $republica = $republicaDAO->getRepublicaCompletaPorId($idRepublica);

        if (count($republica->getIntegrantes()) == 0) {
            $republicaDAO->removeRepublica($republica->getId());
        } else {
            /* Recupera Usuário */
            $usuarioDAO = new UsuarioDAO($this->conexao);
            $usuario = $usuarioDAO->getUsuarioPorId($idIntegrante);
            
            $acao = ($usuarioSaindo) ? "saiu" : "foi removido";
            /* Gera notificação */
            $mensagem = "Usuário " . $usuario->getNome() . " $acao do grupo "
                    . $republica->getNome() . ".";
            foreach ($republica->getIntegrantes() as $integrante) {
                $this->insereNotificacao("Republica", $integrante->getUsuario(), $republica, $mensagem);
            }
        }
    }

    function tornaIntegranteAdministrador($idIntegrante, $idRepublica) {
        /* Atualiza usuário para administrador */
        $republicaDAO = new RepublicaDAO($this->conexao);
        $republicaDAO->setIntegranteAdministrador($idIntegrante, $idRepublica, true);

        /* Recupera república */
        $republica = $republicaDAO->getRepublicaCompletaPorId($idRepublica);

        /* Recupera usuário */
        $usuarioDAO = new UsuarioDAO($this->conexao);
        $usuario = $usuarioDAO->getUsuarioPorId($idIntegrante);

        /* Gera notificação para todos da república */
        $mensagem = "O usuário " . $usuario->getNome() . " tornou-se adminstrador na "
                . "república " . $republica->getNome();
        foreach ($republica->getIntegrantes() as $integrante) {
            $this->insereNotificacao("Republica", $integrante->getUsuario(), $republica, $mensagem);
        }
    }

}

?>
