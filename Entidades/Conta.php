<?php

class Conta {

    private $id;
    private $nome;
    private $valorTotal;
    private $itens; //array de ContaItem
    private $integrantes; //array de integrantes conta
    private $republica;
    private $data;
    private $descricaoAdicional;

    function __construct($nome, $descricaoAdicional, $valorTotal, $data, $integrantes = null, $itens = null, $republica = null, $id = -1) {
        $this->nome = $nome;
        $this->descricaoAdicional = $descricaoAdicional;
        $this->valorTotal = $valorTotal;
        $this->itens = $itens;
        $this->integrantes = $integrantes;
        $this->id = $id;
        $this->republica = $republica;
        $this->data = $data;
    }

    public function getDescricaoAdicional() {
        return $this->descricaoAdicional;
    }

    public function setDescricaoAdicional($descricaoAdicional) {
        $this->descricaoAdicional = $descricaoAdicional;
    }

    public function getData() {
        return $this->data;
    }

    public function setData($data) {
        $this->data = $data;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getRepublica() {
        return $this->republica;
    }

    public function setRepublica($republica) {
        $this->republica = $republica;
    }

    public function getId() {
        return $this->id;
    }

    public function setIntegrantes($integrantes) {
        $this->integrantes = $integrantes;
    }

    public function getIntegrantes() {
        return $this->integrantes;
    }

    public function getIntegrante($idUsuario) {
        if (isset($this->integrantes[$idUsuario])) {
            return $this->integrantes[$idUsuario];
        } else {
            return null;
        }
    }

    public function getNome() {
        return $this->nome;
    }

    public function getValorTotal() {
        return $this->valorTotal;
    }

    public function getItens() {
        return $this->itens;
    }

    public function setItens($itens) {
        $this->itens = $itens;
    }

    public function geraSugestaoPagar($idUsuario) {
        $usuario = $this->getIntegrante($idUsuario);
        $valor = $usuario->getValorAPagar();
        $sugestao = array();
        if (isset($usuario)) {
            foreach ($this->integrantes as $integrante) {
                if ($integrante->precisaReceber()) {
                    if ($integrante->getUsuario()->getId() != $idUsuario) {
                        if ($valor > $integrante->getValorAReceber()) {
                            $valor -= $integrante->getValorAReceber();
                            $sugestao[$integrante->getUsuario()->getNome()] = $integrante->getValorAReceber();
                        } else {
                            $sugestao[$integrante->getUsuario()->getNome()] = $valor;
                            $valor = 0;
                            break;
                        }
                    }
                }
            }
        }
        return $sugestao;
    }

    public function geraSugestaoReceber($idUsuario) {
        $usuario = $this->getIntegrante($idUsuario);
        $valor = $usuario->getValorAReceber();
        $sugestao = array();
        if (isset($usuario)) {
            foreach ($this->integrantes as $integrante) {
                if ($integrante->precisaPagar()) {
                    if ($integrante->getUsuario()->getId() != $idUsuario) {
                        if ($valor > $integrante->getValorAPagar()) {
                            $valor -= $integrante->getValorAPagar();
                            $sugestao[$integrante->getUsuario()->getNome()] = $integrante->getValorAPagar();
                        } else {
                            $sugestao[$integrante->getUsuario()->getNome()] = $valor;
                            $valor = 0;
                            break;
                        }
                    }
                }
            }
        }
        return $sugestao;
    }

}
