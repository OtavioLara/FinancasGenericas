<?php
class ContaItem {
    private $nome;
    private $valor;
    private $distribuicoes; //array de distruibuicao
    private $id;
    
    function __construct($nome, $valor, $distribuicoes, $id = -1) {
        $this->nome = $nome;
        $this->valor = $valor;
        $this->distribuicoes = $distribuicoes;
        $this->id = $id;
    }
    public function getId(){
        return $this->id;
    }
    public function getNome(){
        return $this->nome;
    }
    public function getValor(){
        return $this->valor;
    }
    
   public function getDistribuicoes(){
       return $this->distribuicoes;
   }
   
   public function setDistribuicoes($distribuicoes){
       $this->distribuicoes = $distribuicoes;
   }
}

