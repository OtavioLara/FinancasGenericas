<?php

class Convite {

    private $id;
    private $republica;
    private $usuario;
    private $destinatario;

    function __construct($republica, $usuario, $destinatario, $id = -1) {
        $this->republica = $republica;
        $this->usuario = $usuario;
        $this->destinatario = $destinatario;
        $this->id = $id;
    }

    public function getId(){
        return $this->id;
    }
    
    public function setId($id){
        $this->id = $id;
    }
    
    public function getRepublica() {
        return $this->republica;
    }

    public function getUsuario() {
        return $this->usuario;
    }

    public function getDestinatario() {
        return $this->destinatario;
    }

    public function toString() {
        if ($this->destinatario == 'U') {
            return "A republica ".$this->republica->getNome()." te enviou "
            . "um convite para se juntar a eles, deseja entrar?";
        }else{
            return "O usuario ".$this->usuario->getNome()." enviou "
            . "um convite para se juntar a sua republica, deseja aceitar?";
        }
    }

}
