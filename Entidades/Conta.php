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
    private $dataAlerta;

    function __construct($nome, $dataAlerta, $descricaoAdicional, $valorTotal, $data, $integrantes = null, $itens = null, $republica = null, $id = -1) {
        $this->nome = $nome;
        $this->dataAlerta = $dataAlerta;
        $this->descricaoAdicional = $descricaoAdicional;
        $this->valorTotal = $valorTotal;
        $this->itens = $itens;
        $this->integrantes = $integrantes;
        $this->id = $id;
        $this->republica = $republica;
        $this->data = $data;
        if (!isset($integrantes)) {
            $this->integrantes = array();
        }
        if (!isset($itens)) {
            $this->itens = array();
        }
    }

    public function getContaItem($idContaItem) {
        if (isset($this->itens[$idContaItem])) {
            return $this->itens[$idContaItem];
        }
        return null;
    }

    public function possuiIntegrante($idUsuario) {
        return isset($this->integrantes[$idUsuario]);
    }

    public function adicionaIntegrante(IntegrantesConta $integrante) {
        $this->integrantes[$integrante->getUsuario()->getId()] = $integrante;
    }

    public function possuiItem($idContaItem) {
        return isset($this->itens[$idContaItem]);
    }

    public function adicionaItem(ContaItem $contaItem) {
        $this->itens[$contaItem->getId()] = $contaItem;
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

    public function getDataAlerta() {
        return $this->dataAlerta;
    }

    public function setDataAlerta($dataAlerta) {
        $this->dataAlerta = $dataAlerta;
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

    public function imprimeContaTeste() {
        $republica = $this->getRepublica();
        $nomeRepublica = (isset($republica)) ? $republica->getNome() : "";
        echo "Nome: " . $this->getNome() . "<br/>";
        echo "Grupo: " . $nomeRepublica . "<br/>";
        echo "Valor total: " . $this->getValorTotal() . "<br/>";
        echo "Data: " . $this->getData()->format("d/m/Y") . "<br/>";
        $dataAlerta = $this->getDataAlerta();
        if (isset($dataAlerta)) {
            echo "DataAlerta: " . $this->getDataAlerta()->format("d/m/Y") . "<br/>";
        } else {
            echo "DataAlerta: <br/>";
        }
        echo "Integrantes: <br/>";
        foreach ($this->getIntegrantes() as $integrante) {
            echo $integrante->getUsuario()->getId();
            if ($integrante->isDono()) {
                echo " pagou na conta: " . $integrante->getValorPagoConta() . " ";
            }
            if ($integrante->getValorTotalReceber() > 0) {
                echo " precisa receber: " . $integrante->getValorJaRecebido() . "/"
                . $integrante->getValorTotalReceber() . "<br/>";
            } else if ($integrante->getValorTotalPagar() > 0) {
                echo " ja pagou: " . $integrante->getValorJaPagou() . "/"
                . $integrante->getValorTotalPagar() . "<br/>";
            }
            echo "<br/>";
        }

        echo "Itens: <br/>";
        foreach ($this->getItens() as $item) {
            echo $item->getNome() . " " . $item->getValor() . "<br/>";
            echo "Distribuição: <br/>";
            foreach ($item->getDistribuicoes() as $distribuicao) {
                echo $distribuicao->getUsuario()->getId() . " " . $distribuicao->getValor() . "<br/>";
            }
        }
    }

}
