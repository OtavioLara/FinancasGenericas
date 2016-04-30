<?php

//Janeiro
class GraficoBuilder {

    private $chave;
    private $chaveOpcional;

    function __construct() {
        $this->chave = array();
        $this->chaveOpcional = array();
    }

    function adicionaChave($chave) {
        $this->chave[$chave] = 0;
    }

    function adicionaChaveOptativa($chaveAlternativa, $referencia) {
        if (isset($this->chave[$referencia])) {
            $this->chaveOpcional[$chaveAlternativa] = $referencia;
        }
    }

    function insereValor($chave, $valor) {
        //echo "adicionando $valor em $chave <br/>";
        if (isset($this->chave[$chave])) {
            $this->chave[$chave] += $valor;
        } elseif (isset($this->chaveOpcional[$chave])) {
            $this->chave[$this->chaveOpcional[$chave]] += $valor;
        }
    }

    function criaChavesPorMes($dataInicio, $dataFim) {
        $meses = array("JAN", "FEV", "MAR", "ABR", "MAI", "JUN",
            "JUL", "AGO", "SET", "OUT", "NOV", "DEZ");

        $limiteInicio = $dataInicio->format("m") + 0;
        for ($ano = $dataInicio->format("Y"); $ano <= $dataFim->format("Y"); $ano++) {
            if ($ano == $dataFim->format("Y")) {
                $limiteFim = $dataFim->format("m");
            } else {
                $limiteFim = 12;
            }
            for ($mes = $limiteInicio; $mes <= $limiteFim; $mes++) {
                //echo "chave: " . $meses[$mes - 1] . "/$ano" . " criada <br/>";
                $this->adicionaChave($meses[$mes - 1] . "/$ano");
                if($mes < 10){
                    //echo "chave optativa: 0" . $mes . "/" . $ano . " criada <br/>";
                    $this->adicionaChaveOptativa("0".$mes . "/" . $ano, $meses[$mes - 1] . "/$ano");
                }else{
                    //echo "chave optativa: " . $mes . "/" . $ano . " criada <br/>";
                    $this->adicionaChaveOptativa($mes . "/" . $ano, $meses[$mes - 1] . "/$ano");
                }
                
            }
            $limiteInicio = 1;
        }
    }

    function geraGrafico($elemento) {
        $code = "element: '$elemento'," .
                "data: [";
        $index = 0;
        foreach ($this->chave as $mes => $gasto) {
            $code .= "{mes: '$mes', gasto: $gasto}";
            if ($index != count($this->chave) - 1) {
                $code .= ",";
            }
            $index++;
        }
        $code .= "], xkey: 'mes', ykeys: ['gasto'], labels: ['Gasto']";
        return $code;
    }

}
?>

