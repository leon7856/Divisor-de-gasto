<?php
// views/resumo.php
?>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4><i class="fas fa-chart-bar me-2"></i>Resumo Semanal</h4>
            </div>
            <div class="card-body">
                <form method="GET" class="row g-3 mb-4">
                    <input type="hidden" name="page" value="resumo">
                    <div class="col-md-4">
                        <label for="data_inicio" class="form-label">Data Início</label>
                        <input type="date" class="form-control" id="data_inicio" name="data_inicio" 
                               value="<?php echo $data_inicio ?? ''; ?>" required>
                    </div>
                    <div class="col-md-4">
                        <label for="data_fim" class="form-label">Data Fim</label>
                        <input type="date" class="form-control" id="data_fim" name="data_fim" 
                               value="<?php echo $data_fim ?? ''; ?>" required>
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-chart-pie me-1"></i> Gerar Resumo
                        </button>
                    </div>
                </form>

                <?php if(isset($resumo)): ?>
                    <!-- Cabeçalho do Resumo -->
                    <div class="alert alert-info">
                        <div class="row">
                            <div class="col-md-4">
                                <strong>Período:</strong><br>
                                <?php echo date('d/m/Y', strtotime($resumo['periodo']['inicio'])); ?> a 
                                <?php echo date('d/m/Y', strtotime($resumo['periodo']['fim'])); ?>
                            </div>
                            <div class="col-md-4">
                                <strong>Total Gasto:</strong><br>
                                R$ <?php echo number_format($resumo['totalGasto'], 2, ',', '.'); ?>
                            </div>
                            <div class="col-md-4">
                                <strong>Pagador<?php echo count($resumo['todosPagadores']) > 1 ? 'es' : ''; ?>:</strong><br>
                                <?php 
                                if(!empty($resumo['todosPagadores'])) {
                                    $nomesPagadores = array_map(function($p) { return htmlspecialchars($p['nome']); }, $resumo['todosPagadores']);
                                    echo implode(', ', $nomesPagadores);
                                } else {
                                    echo 'Não definido';
                                }
                                ?>
                            </div>
                        </div>
                    </div>

                    <!-- Divisão de Gastos -->
                    <h5 class="mt-4 mb-3"><i class="fas fa-money-bill-wave me-2"></i>Divisão de Gastos</h5>
                    <div class="row">
                        <?php foreach($resumo['consumoPorPessoa'] as $pessoa): ?>
                            <?php if($pessoa['total'] > 0): ?>
                                <div class="col-md-6 mb-3">
                                    <div class="card <?php echo $pessoa['usuario']['pagador'] ? 'border-success' : ''; ?>">
                                        <div class="card-header d-flex justify-content-between align-items-center">
                                            <strong>
                                                <?php echo htmlspecialchars($pessoa['usuario']['nome']); ?>
                                                <?php if($pessoa['usuario']['pagador']): ?>
                                                    <span class="badge bg-success ms-2">Pagador</span>
                                                <?php endif; ?>
                                            </strong>
                                            <span class="h5 mb-0 <?php 
                                                if($pessoa['usuario']['pagador']) {
                                                    // Para pagadores: calcular saldo líquido
                                                    // (o que ele deve para outros pagadores MENOS o que outros pagadores devem para ele)
                                                    $totalDevidoParaPagadores = 0;
                                                    $totalQuePagadoresDevemParaEle = 0;
                                                    
                                                    foreach($pessoa['dividasPorPagador'] as $dividaPagador) {
                                                        if($dividaPagador['valor'] > 0 && $pessoa['usuario']['id'] != $dividaPagador['pagador']['id']) {
                                                            // Se o pagador é pagador, conta na conta de saldo líquido
                                                            if($dividaPagador['pagador']['pagador']) {
                                                                $totalDevidoParaPagadores += $dividaPagador['valor'];
                                                            }
                                                        }
                                                    }
                                                    
                                                    // Calcular o que outros pagadores devem para esta pessoa
                                                    foreach($resumo['consumoPorPessoa'] as $outraPessoa) {
                                                        if($outraPessoa['usuario']['id'] != $pessoa['usuario']['id'] && $outraPessoa['usuario']['pagador']) {
                                                            foreach($outraPessoa['dividasPorPagador'] as $dividaPagador) {
                                                                if($dividaPagador['valor'] > 0 && $dividaPagador['pagador']['id'] == $pessoa['usuario']['id']) {
                                                                    $totalQuePagadoresDevemParaEle += $dividaPagador['valor'];
                                                                }
                                                            }
                                                        }
                                                    }
                                                    
                                                    // Saldo líquido = o que deve - o que outros devem para ele
                                                    $saldoLiquido = $totalDevidoParaPagadores - $totalQuePagadoresDevemParaEle;
                                                    $valorExibir = max(0, $saldoLiquido); // Não pode ser negativo
                                                    $cor = ($valorExibir > 0) ? 'text-danger' : 'text-success';
                                                } else {
                                                    // Para não-pagadores: calcular apenas o que deve para outras pessoas
                                                    $totalDevidoOutros = 0;
                                                    foreach($pessoa['dividasPorPagador'] as $dividaPagador) {
                                                        if($dividaPagador['valor'] > 0 && $pessoa['usuario']['id'] != $dividaPagador['pagador']['id']) {
                                                            $totalDevidoOutros += $dividaPagador['valor'];
                                                        }
                                                    }
                                                    $valorExibir = $totalDevidoOutros;
                                                    $cor = ($valorExibir > 0) ? 'text-danger' : 'text-success';
                                                }
                                                echo $cor; 
                                            ?>">
                                                R$ <?php 
                                                if($pessoa['usuario']['pagador']) {
                                                    // Para pagadores: calcular saldo líquido
                                                    $totalDevidoParaPagadores = 0;
                                                    $totalQuePagadoresDevemParaEle = 0;
                                                    
                                                    foreach($pessoa['dividasPorPagador'] as $dividaPagador) {
                                                        if($dividaPagador['valor'] > 0 && $pessoa['usuario']['id'] != $dividaPagador['pagador']['id']) {
                                                            if($dividaPagador['pagador']['pagador']) {
                                                                $totalDevidoParaPagadores += $dividaPagador['valor'];
                                                            }
                                                        }
                                                    }
                                                    
                                                    // Calcular o que outros pagadores devem para esta pessoa
                                                    foreach($resumo['consumoPorPessoa'] as $outraPessoa) {
                                                        if($outraPessoa['usuario']['id'] != $pessoa['usuario']['id'] && $outraPessoa['usuario']['pagador']) {
                                                            foreach($outraPessoa['dividasPorPagador'] as $dividaPagador) {
                                                                if($dividaPagador['valor'] > 0 && $dividaPagador['pagador']['id'] == $pessoa['usuario']['id']) {
                                                                    $totalQuePagadoresDevemParaEle += $dividaPagador['valor'];
                                                                }
                                                            }
                                                        }
                                                    }
                                                    
                                                    $saldoLiquido = $totalDevidoParaPagadores - $totalQuePagadoresDevemParaEle;
                                                    $valorExibir = max(0, $saldoLiquido);
                                                    echo number_format($valorExibir, 2, ',', '.'); 
                                                } else {
                                                    // Para não-pagadores: mostrar o que deve
                                                    $totalDevidoOutros = 0;
                                                    foreach($pessoa['dividasPorPagador'] as $dividaPagador) {
                                                        if($dividaPagador['valor'] > 0 && $pessoa['usuario']['id'] != $dividaPagador['pagador']['id']) {
                                                            $totalDevidoOutros += $dividaPagador['valor'];
                                                        }
                                                    }
                                                    echo number_format($totalDevidoOutros, 2, ',', '.'); 
                                                }
                                                ?>
                                            </span>
                                        </div>
                                        <div class="card-body">
                                            <p class="mb-2"><strong>Itens consumidos:</strong></p>
                                            <ul class="list-unstyled">
                                                <?php foreach($pessoa['itens'] as $item): ?>
                                                    <li class="mb-1">
                                                        <i class="fas fa-utensils text-muted me-2"></i>
                                                        <?php echo htmlspecialchars($item['nome']); ?> 
                                                        (<?php echo date('d/m', strtotime($item['data'])); ?>)
                                                        <small class="text-muted">
                                                            - R$ <?php echo number_format($item['valor_individual'], 2, ',', '.'); ?>
                                                        </small>
                                                        <br>
                                                        <small class="text-muted ms-4">
                                                            <i class="fas fa-user me-1"></i>
                                                            Pago por: <?php echo htmlspecialchars($item['pagador']); ?>
                                                        </small>
                                                    </li>
                                                <?php endforeach; ?>
                                            </ul>
                                            
                                            <?php 
                                            // Mostrar dívidas por pagador
                                            $temDividas = false;
                                            foreach($pessoa['dividasPorPagador'] as $dividaPagador):
                                                if($dividaPagador['valor'] > 0 && $pessoa['usuario']['id'] != $dividaPagador['pagador']['id']):
                                                    $temDividas = true;
                                                    break;
                                                endif;
                                            endforeach;
                                            
                                            if($temDividas): ?>
                                                <div class="alert alert-warning mt-2 mb-2 py-2">
                                                    <small><strong>Deve pagar:</strong></small>
                                                    <ul class="list-unstyled mb-0 mt-1">
                                                        <?php foreach($pessoa['dividasPorPagador'] as $dividaPagador): ?>
                                                            <?php if($dividaPagador['valor'] > 0 && $pessoa['usuario']['id'] != $dividaPagador['pagador']['id']): ?>
                                                                <li class="mb-1">
                                                                    <small>
                                                                        <i class="fas fa-hand-holding-usd me-1"></i>
                                                                        Para <strong><?php echo htmlspecialchars($dividaPagador['pagador']['nome']); ?></strong>: 
                                                                        R$ <?php echo number_format($dividaPagador['valor'], 2, ',', '.'); ?>
                                                                    </small>
                                                                </li>
                                                            <?php endif; ?>
                                                        <?php endforeach; ?>
                                                    </ul>
                                                </div>
                                            <?php endif; ?>
                                            
                                            <?php if(!empty($pessoa['usuario']['telefone'])): ?>
                                                <button type="button" 
                                                        class="btn btn-success btn-sm w-100 mt-2 btn-whatsapp" 
                                                        data-nome="<?php echo htmlspecialchars($pessoa['usuario']['nome']); ?>"
                                                        data-telefone="<?php echo htmlspecialchars($pessoa['usuario']['telefone']); ?>"
                                                        data-total="<?php echo $pessoa['total']; ?>"
                                                        data-itens='<?php echo json_encode($pessoa['itens']); ?>'
                                                        data-periodo-inicio="<?php echo htmlspecialchars($resumo['periodo']['inicio']); ?>"
                                                        data-periodo-fim="<?php echo htmlspecialchars($resumo['periodo']['fim']); ?>"
                                                        data-dividas-pagador='<?php echo json_encode($pessoa['dividasPorPagador']); ?>'>
                                                    <i class="fab fa-whatsapp me-1"></i> Enviar Resumo por WhatsApp
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>

                    <!-- Itens Comprados -->
                    <h5 class="mt-4 mb-3"><i class="fas fa-receipt me-2"></i>Consumos Registrados no Período</h5>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Data</th>
                                    <th>Item</th>
                                    <th>Quem Consumiu</th>
                                    <th>Valor</th>
                                    <th>Quem Pagou</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($resumo['consumos'] as $consumo): ?>
                                    <tr>
                                        <td><?php echo date('d/m/Y', strtotime($consumo['data_consumo'])); ?></td>
                                        <td><?php echo htmlspecialchars($consumo['nome_item']); ?></td>
                                        <td><?php echo htmlspecialchars($consumo['nome_usuario']); ?></td>
                                        <td>R$ <?php echo number_format($consumo['valor'], 2, ',', '.'); ?></td>
                                        <td><?php echo htmlspecialchars($consumo['nome_pagador']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php elseif(isset($data_inicio)): ?>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Nenhum dado encontrado para o período selecionado.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>