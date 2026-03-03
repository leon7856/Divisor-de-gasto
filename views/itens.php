<?php
// views/itens.php
?>
<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h4><i class="fas fa-tag me-2"></i>Cadastrar Item</h4>
            </div>
            <div class="card-body">
                <form method="POST" action="index.php?page=itens&action=create">
                    <div class="mb-3">
                        <label for="nome" class="form-label">Nome do Item</label>
                        <input type="text" class="form-control" id="nome" name="nome" 
                               placeholder="Ex: Marmita, Sorvete, Refrigerante" required>
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Cadastrar Item
                    </button>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h4><i class="fas fa-list me-2"></i>Itens Cadastrados</h4>
            </div>
            <div class="card-body">
                <?php if(empty($itens)): ?>
                    <p class="text-muted">Nenhum item cadastrado.</p>
                <?php else: ?>
                    <div class="list-group">
                        <?php foreach($itens as $item): ?>
                            <div class="list-group-item d-flex align-items-center justify-content-between">
                                <div class="flex-grow-1">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1"><?php echo htmlspecialchars($item['nome']); ?></h6>
                                        <small class="text-muted">
                                            <?php echo date('d/m/Y', strtotime($item['created_at'])); ?>
                                        </small>
                                    </div>
                                </div>
                                <form method="POST" action="index.php?page=itens&action=delete" class="ms-3"
                                      onsubmit="return confirm('Deseja remover este item?');">
                                    <input type="hidden" name="id" value="<?php echo $item['id']; ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="fas fa-trash me-1"></i> Remover
                                    </button>
                                </form>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>