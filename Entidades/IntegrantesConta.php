<?php

class IntegrantesConta {

    private $usuario;
    private $valorPagoConta;
    private $valorTotalReceber;
    private $valorJaRecebido;
    private $valorTotalPagar;
    private $valorJaPagou;

    function __construct($usuario, $valorPagoConta, $valorTotalReceber, $valorJaRecebido, $valorTotalPagar, $valorJaPagou) {
        $this->usuario = $usuario;
        $this->valorPagoConta = $valorPagoConta;
        $this->valorTotalReceber = $valorTotalReceber;
        $this->valorTotalPagar = $valorTotalPagar;
        $this->valorJaPagou = $valorJaPagou;
        $this->valorJaRecebido = $valorJaRecebido;
    }

    public function getUsuario() {
        return $this->usuario;
    }

    public function isDono() {
        return $this->valorPagoConta > 0;
    }
    
    public function precisaReceber(){
        return $this->valorTotalReceber > $this->valorJaRecebido;
    }
    public function getValorAReceber(){
        return $this->valorTotalReceber - $this->valorJaRecebido;
    }
    
    public function precisaPagar(){
        return $this->valorTotalPagar > $this->valorJaPagou;
    }
    public function getValorAPagar(){
        return $this->valorTotalPagar - $this->valorJaPagou;
    }
   
    public function getValorJaRecebido(){
        return $this->valorJaRecebido;
    }
    
    public function getValorPagoConta() {
        return $this->valorPagoConta;
    }   

    public function getValorTotalReceber() {
        return $this->valorTotalReceber;
    }

    public function getValorTotalPagar() {
        return $this->valorTotalPagar;
    }

    public function incrementarValorTotal($incremento) {
        $this->valorTotalPagar += $incremento;
    }

    public function getValorJaPagou() {
        return $this->valorJaPagou;
    }
    
    public function setValores($valorPagoConta) {
        $this->valorPagoConta = $valorPagoConta;
        if ($valorPagoConta >= $this->valorTotalPagar) {
            $this->valorTotalReceber = $valorPagoConta - $this->valorTotalPagar;
            $this->valorJaPagou = $this->valorTotalPagar;
        } else {
            $this->valorTotalReceber = 0;
            $this->valorJaPagou = $valorPagoConta;
        }
    }

}
