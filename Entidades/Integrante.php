<?php

class Integrante {
    
    private $usuario;
    private $administrador;
    
    function __construct($usuario, $administrador) {
        $this->usuario = $usuario;
        $this->administrador = $administrador;
    }
    
    public function getUsuario(){
        return $this->usuario;
    }
    
    public function isAdministrador(){
        return $this->administrador;
    }
    
    public function toString(){
        if($this->administrador){
            return $this->usuario->getNome()." (administrador)";
        }else{
            return $this->usuario->getNome();
        }
    }
}
