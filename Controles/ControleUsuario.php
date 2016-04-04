<?php


class ControleUsuario extends Controle{
    
    function __construct($conexao = null) {
        parent::__construct($conexao);
    }
    
    function insereUsuario($nomeUsuario, $email, $senha){
        /* Cria objeto usuário */
        $usuario = new Usuario(-1, $nomeUsuario, $email);
        
        /* Insere usuário no banco de dados */
        $usuarioDAO = new UsuarioDAO($this->conexao);
        $usuarioDAO->inserirUsuario($usuario, $senha);
        
    }
    
}
