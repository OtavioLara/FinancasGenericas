<?php

class Requerimento {

    private $destinatario;
    private $remetente;
    private $valor;
    private $data;
    private $id;
    private $situacao;

    function __construct($destinatario, $remetente, $valor, $data, $situacao, $id = null) {
        $this->destinatario = $destinatario;
        $this->remetente = $remetente;
        $this->valor = $valor;
        $this->data = $data;
        $this->situacao = $situacao;
        $this->id = $id;
    }

    public function getSituacao(){
        return $this->situacao;
    }
    
    public function setSituacao($situacao){
        $this->situacao = $situacao;
    }
    public function getDestinatario() {
        return $this->destinatario;
    }

    public function setDestinatario($destinatario) {
        $this->destinatario = $destinatario;
    }

    public function getRemetente() {
        return $this->remetente;
    }

    public function setRemetente($remetente) {
        $this->remetente = $remetente;
    }

    public function getValor() {
        return $this->valor;
    }

    public function setValor($valor) {
        $this->valor = $valor;
    }

    public function getData() {
        return $this->data;
    }

    public function setData($data) {
        $this->data = $data;
    }

    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

}

?>
