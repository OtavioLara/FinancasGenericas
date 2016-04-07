<?php

class Notificacao {

    private $id;
    private $usuario;
    private $objeto; //Conta, República ou Requerimento
    private $mensagem;
    private $data;
    private $visualizada;
    private $titulo;

    function __construct($titulo, $usuario, $objeto, $mensagem, $data, $visualizada = false, $id = -1) {
        $this->titulo = $titulo;
        $this->id = $id;
        $this->usuario = $usuario;
        $this->objeto = $objeto;
        $this->mensagem = $mensagem;
        $this->data = $data;
        $this->visualizada = $visualizada;
    }

    public function getTitulo(){
        return $this->titulo;
    }
    
    public function setTitulo($titulo){
        $this->titulo = $titulo;
    }
    
    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getUsuario() {
        return $this->usuario;
    }

    public function setUsuario($usuario) {
        $this->usuario = $usuario;
    }

    public function getObjeto() {
        return $this->objeto;
    }

    public function setObjeto($objeto) {
        $this->objeto = $objeto;
    }

    public function getMensagem() {
        return $this->mensagem;
    }

    public function setMensagem($mensagem) {
        $this->mensagem = $mensagem;
    }

    public function getData() {
        return $this->data;
    }

    public function setData($data) {
        $this->data = $data;
    }

    public function isVisualizada() {
        return $this->visualizada;
    }

    public function setVisualizada($visualizada) {
        $this->visualizada = $visualizada;
    }

    public function getTipoNofitificacao(){
        if($this->objeto instanceof Conta){
            return "Conta";
        }else if($this->objeto instanceof Republica){
            return "Republica";
        }else if($this->objeto instanceof Requerimento){
            return "Requerimento";
        }
        return "";
    }
}

?>
