<?php
// views/consumos.php

$pagadorPadraoId = null;
foreach ($usuarios as $usuarioPagador) {
    if (!empty($usuarioPagador['pagador'])) {
        $pagadorPadraoId = $usuarioPagador['id'];
        break;
    }
}
?>
<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h4><i class="fas fa-utensils me-2"></i>Registrar Consumo</h4>
            </div>
            <div class="card-body">
                <form method="POST" action="index.php?page=consumos&action=create" id="formConsumo">
                    <div class="mb-3">
                        <label for="id_item" class="form-label">O que foi Consumido?</label>
                        <select class="form-select" id="id_item" name="id_item" required>
                            <option value="">Selecione o item...</option>
                            <?php foreach($itens as $item): ?>
                                <option value="<?php echo $item['id']; ?>">
                                    <?php echo htmlspecialchars($item['nome']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Quem Consumiu? (Selecione todos)</label>
                        <div class="border p-3 rounded" style="max-height: 200px; overflow-y: auto;">
                            <?php foreach($usuarios as $usuario): ?>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" 
                                           name="id_usuarios[]" 
                                           value="<?php echo $usuario['id']; ?>" 
                                           id="user_<?php echo $usuario['id']; ?>">
                                    <label class="form-check-label" for="user_<?php echo $usuario['id']; ?>">
                                        <?php echo htmlspecialchars($usuario['nome']); ?>
                                        <?php if($usuario['pagador']): ?>
                                            <span class="badge bg-success ms-1">Pagador</span>
                                        <?php endif; ?>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <small class="text-muted">Selecione todas as pessoas que consumiram este item</small>
                    </div>
                    
                    <div class="mb-3">
                        <label for="id_pagador" class="form-label">Quem Pagou?</label>
                        <select class="form-select" id="id_pagador" name="id_pagador" required>
                            <option value="">Selecione o pagador...</option>
                            <?php foreach($usuarios as $usuario): ?>
                                <option value="<?php echo $usuario['id']; ?>"
                                    <?php echo ($pagadorPadraoId !== null && (int)$pagadorPadraoId === (int)$usuario['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($usuario['nome']); ?>
                                    <?php if($usuario['pagador']): ?> (Pagador Principal)<?php endif; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="data_consumo" class="form-label">Data do Consumo</label>
                        <input type="date" class="form-control" id="data_consumo" name="data_consumo" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="valor" class="form-label">Valor Total Gasto (R$)</label>
                        <input type="number" step="0.01" class="form-control" id="valor" name="valor" 
                               placeholder="0.00" required>
                        <small class="text-muted">Este valor será dividido igualmente entre todas as pessoas selecionadas</small>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Registrar Consumo
                    </button>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h4><i class="fas fa-history me-2"></i>Consumos Registrados</h4>
            </div>
            <div class="card-body">
                <?php if(empty($consumos)): ?>
                    <p class="text-muted">Nenhum consumo registrado.</p>
                <?php else: ?>
                    <div class="list-group">
                        <?php foreach($consumos as $consumo): ?>
                            <div class="list-group-item">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="me-3 flex-grow-1">
                                        <div class="d-flex w-100 justify-content-between">
                                            <h6 class="mb-1"><?php echo htmlspecialchars($consumo['nome_usuario']); ?></h6>
                                            <small><?php echo date('d/m/Y', strtotime($consumo['data_consumo'])); ?></small>
                                        </div>
                                        <p class="mb-1">
                                            Consumiu: <strong><?php echo htmlspecialchars($consumo['nome_item']); ?></strong>
                                        </p>
                                        <p class="mb-1">
                                            Valor Individual: <strong>R$ <?php echo number_format($consumo['valor'], 2, ',', '.'); ?></strong>
                                        </p>
                                        <small class="text-muted">
                                            Pago por: <?php echo htmlspecialchars($consumo['nome_pagador']); ?>
                                        </small>
                                    </div>
                                    <form method="POST" action="index.php?page=consumos&action=delete"
                                          onsubmit="return confirm('Deseja remover este consumo?');">
                                        <input type="hidden" name="id" value="<?php echo $consumo['id']; ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="fas fa-trash me-1"></i> Remover
                                        </button>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Validação - pelo menos uma pessoa selecionada
    document.getElementById('formConsumo').addEventListener('submit', function(e) {
        const checkboxes = document.querySelectorAll('input[name="id_usuarios[]"]:checked');
        if (checkboxes.length === 0) {
            e.preventDefault();
            alert('Selecione pelo menos uma pessoa que consumiu!');
            return false;
        }
    });
});
</script>