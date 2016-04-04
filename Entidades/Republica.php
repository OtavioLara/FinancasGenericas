<?php

class Republica {

    private $id;
    private $nome;
    private $integrantes;

    function __construct($id, $nome, $integrantes) {
        $this->id = $id;
        $this->nome = $nome;
        $this->integrantes = $integrantes;
    }

    public function getId() {
        return $this->id;
    }

    public function getNome() {
        return $this->nome;
    }

    
    public function getIntegrantes(){
        return $this->integrantes;
    }
    
    public function setIntegrantes($integrantes){
        return $this->integrantes = $integrantes;
    }
    
    public function getIntegrante($idUsuario){
        if(isset($this->integrantes[$idUsuario])){
            return $this->integrantes[$idUsuario];
        }
        return null;
    }
}
