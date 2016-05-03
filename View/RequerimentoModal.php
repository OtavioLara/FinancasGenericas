<!-- Modal -->
<?php
if (isset($requerimento)) {
    ?>
    <!-- Modal Requerimento -->
    <div class="modal fade" id="modalRequerimento" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <!-- Modal Header -->
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">
                        Solicitação de pagamento
                    </h4>
                </div>

                <!-- Modal Body -->
                <div class="modal-body">
                    <div class="col-md-12">
                        <font size="3"><?php echo $pagante->getNome() . " te pagou R$ " . $formato->numeroInterface($requerimento->getValor()) . " ?"; ?></font>
                    </div>
                    <div class="">
                        <div class='row'>
                            <div class='col-md-3 col-md-offset-6'>
                                <input type='button' class='btn btn-success btn-block' value='Confirmar' data-toggle="collapse" data-target="#divAtualizacao" />
                            </div>
                            <div class='col-md-3'>
                                <form action='ControlesScript/ControleContaScript.php' method='post'>
                                    <input type='submit' class='btn btn-danger btn-block' value='Não' />
                                    <input type='hidden' name='idRequerimento' value='<?php echo $requerimento->getId(); ?>' />
                                    <input type='hidden' name='comando' value='requerimentoRejeitado' />
                                </form>
                            </div>
                        </div>
                        <div class='collapse' id='divAtualizacao'>
                            <hr/>
                            <div class='row'>
                                <div class='col-md-12'>
                                    <h4>Valor a atualizar: R$ <?php echo $formato->numeroInterface($requerimento->getValor()); ?></h4>
                                </div>
                            </div>
                            <!-- Form de pagamento -->
                            <form action="ControlesScript/ControleContaScript.php" method="post">
                                <div class='row'>
                                    <div class='col-md-12'>
                                        Contas pendentes com <?php echo $pagante->getNome() ?> : 
                                        <ul class="list-group">
                                            <?php
                                            $valorRequerimento = $requerimento->getValor();
                                            foreach ($contasDividas as $contaDivida) {
                                                $dividaConta = $contaDivida->getDivida($pagante->getId(), $usuario->getId());
                                                if ($dividaConta > $valorRequerimento) {
                                                    $valorAPagarDivida = $valorRequerimento;    
                                                } else {
                                                    $valorAPagarDivida = $dividaConta;
                                                }
                                                $valorRequerimento -= $valorAPagarDivida;
                                                echo "<li class='list-group-item'>";
                                                echo $contaDivida->getNome() . " (dívida: R$ " . $dividaConta . "): <input type='text' name='pagamento[]' value='" . $valorAPagarDivida . "' readonly />";
                                                echo "<input type='hidden' name='idConta[]' value='" . $contaDivida->getId() . "' />";
                                                echo "</li>";
                                            }
                                            echo "<input type='hidden' name='idUsuarioPagando' value='" . $pagante->getId() . "' />";
                                            echo "<input type='hidden' name='idUsuarioRecebendo' value='" . $usuario->getId() . "' />";
                                            echo "<input type='hidden' name='idRequerimento' value='" . $requerimento->getId() . "' />";
                                            echo "<input type='hidden' name='comando' value='usuarioPagandoMuitasContas' />";
                                            ?>
                                        </ul>

                                        <div class='row'>
                                            <div class='col-md-3 col-md-offset-9'>
                                                <input type='submit' class='btn btn-success btn-block' value='Efetivar' />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                            <!-- ./Form de pagamento -->
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
    <!-- ./Modal -->
<?php } ?>
