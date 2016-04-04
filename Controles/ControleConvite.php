<?php

class ControleConvite extends Controle{

    function __construct($conexao = null) {
        parent::__construct($conexao);
    }
    
    
    function insereConvite($idRepublica, $emailConvidado, $destinatario){
        /* Recupera usuário */
        $usuarioDAO = new UsuarioDAO($this->conexao);
        $usuario = $usuarioDAO->getUsuarioPorEmail($emailConvidado);
        $idConvidado = $usuario->getId();
        
        /* Insere Convite */
        $republicaDAO = new RepublicaDAO($this->conexao);
        $republicaDAO->inserirConvite($idRepublica, $idConvidado, $destinatario);
        
        /* Recupera república */
        $republica = $republicaDAO->getRepublicaPorId_Incompleto($idRepublica);
        
        /* Cria notificação */
        $mensagem = "República ".$republica->getNome()." lhe mandou um convite.";
        $this->insereNotificacao($usuario, $republica, $mensagem);
    }
    
    function aceitaConvite($idConvite){
        /* Recupera convite */
        $republicaDAO = new RepublicaDAO($this->conexao);
        $convite = $republicaDAO->getConvitePorId($idConvite);
        $republica = $convite->getRepublica();
        
        /* Remove convite e insere o convidado na república*/
        $republicaDAO->inserirIntegrante($convite->getRepublica()->getId(), $convite->getUsuario()->getId());
        $republicaDAO->removeConviteDeUsuario($convite->getRepublica()->getId(), $convite->getUsuario()->getId());

        /* Cria notificação */
        $mensagem = "Usuário ".$convite->getUsuario()->getNome()." adicionado na república ". $republica->getNome().".";
        $republica->setIntegrantes($republicaDAO->getIntegrantesRepublica($republica->getId()));
        foreach($republica->getIntegrantes() as $integrante){
            $this->insereNotificacao($integrante->getUsuario(), $republica, $mensagem);
        }
        
    }
    
    function rejeitaConvite($idConvite){        
         /* Recupera convite */
        $republicaDAO = new RepublicaDAO($this->conexao);
        $convite = $republicaDAO->getConvitePorId($idConvite);
        
        /* Remove convite */
        $republicaDAO->removeConviteDeUsuario($convite->getRepublica()->getId(), $convite->getUsuario()->getId());
        
    }
}

?>