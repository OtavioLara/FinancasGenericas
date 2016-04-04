<?php

class ControleRepublica extends Controle {

    function __construct($conexao = null) {
        parent::__construct($conexao);
    }

    function insereRepublica($idCriador, $nomeRepublica){
        /* Insere república no banco de dados */
        $republicaDAO = new RepublicaDAO($this->conexao);
        $republicaDAO->inserirRepublica($idCriador, $nomeRepublica);
    }
    
    function removeIntegrante($idIntegrante, $idRepublica){
        $republicaDAO = new RepublicaDAO($this->conexao);
        $republicaDAO->removeIntegrante($idIntegrante, $idRepublica);
        
        /* Recupera Usuário */
        $usuarioDAO = new UsuarioDAO($this->conexao);
        $usuario = $usuarioDAO->getUsuarioPorId($idIntegrante);
        
        /* Recupera República */
        $republica = $republicaDAO->getRepublicaPorId_Completa($idRepublica);
        
        /*Gera notificação */
        $mensagem = "Usuário ".$usuario->getNome()." foi removido da república "
                . $republica->getNome().".";
        foreach($republica->getIntegrantes() as $integrante){
            $this->insereNotificacao($integrante->getUsuario(), $republica, $mensagem);
        }
    }
    
    function tornaIntegranteAdministrador($idIntegrante, $idRepublica){
        /* Atualiza usuário para administrador */
        $republicaDAO = new RepublicaDAO($this->conexao);
        $republicaDAO->setIntegranteAdministrador($idIntegrante, $idRepublica, true);
        
        /* Recupera república */
        $republica = $republicaDAO->getRepublicaPorId_Completa($idRepublica);
        
        /* Recupera usuário */
        $usuarioDAO = new UsuarioDAO($this->conexao);
        $usuario = $usuarioDAO->getUsuarioPorId($idIntegrante);
        
        /* Gera notificação para todos da república */
        $mensagem = "O usuário ".$usuario->getNome()." tornou-se adminstrador na "
                . "república ".$republica->getNome();
        foreach($republica->getIntegrantes() as $integrante){
            $this->insereNotificacao($integrante->getUsuario(), $republica, $mensagem);
        }
    }
}

?>
