<?php
//Janeiro
class GraficoBuilder {

    private $chave;
    private $chaveOpcional;

    function __construct() {
        $this->chave = array();
        $this->chaveOpcional = array();
    }

    function criarGraficoPorMes($dataInicio, $dataFim) {
        $meses = array("JAN", "FEV", "MAR", "MAI", "ABR", "JUN",
            "JUL", "AGO", "SET", "OUT", "NOV", "DEZ");
        for($i = 0 ; $i < count($meses); $i++){
            $this->adicionaChave($meses[$i]);
            if($i < 10){
                $this->adicionaChaveOptativa("0".$i, $meses[$i]);
            }else{
                $this->adicionaChaveOptativa("$i", $meses[$i]);
            }
        }
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
        if (isset($this->chave[$chave])) {
            $this->chave[$chave] += $valor;
        } elseif (isset($this->chaveOpcional[$chave])) {
            $this->chave[$this->chaveOpcional[$chave]] += $valor;
        }
    }

    function geraGrafico($tipoGrafico, $titulo, $xLabel, $yLabel, $width, $height) {


        
        require_once('lib/inc/chartphp_dist.php');
        $p = new chartphp();
        $data = array();
        $index = 0;
        foreach ($this->chave as $chave => $valor) {
            $data[$index] = array($chave, $valor);
            $index++;
        }
        $p->data = array($data);
        $p->chart_type = $tipoGrafico;
        $p->width = 250;
        $p->height = 400;

        // Common Options 
        $p->title = $titulo;
        $p->xlabel = $xLabel;
        $p->ylabel = $yLabel;
        $p->export = true;
        $p->options["legend"]["show"] = true;
        $p->series_label = array('Q1', 'Q2', 'Q3');
        $p->color = "blue,red";

        return $p->render('c1');
    }

}
?>

