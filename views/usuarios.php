<?php
// views/usuarios.php
?>
<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h4 id="formTitle"><i class="fas fa-user-plus me-2"></i>Cadastrar Pessoa</h4>
            </div>
            <div class="card-body">
                <form method="POST" action="index.php?page=usuarios&action=create" id="formUsuario">
                    <input type="hidden" id="usuario_id" name="id" value="">
                    <div class="mb-3">
                        <label for="nome" class="form-label">Nome</label>
                        <input type="text" class="form-control" id="nome" name="nome" 
                               placeholder="Digite o nome da pessoa" required>
                    </div>
                    <div class="mb-3">
                        <label for="telefone" class="form-label">Telefone</label>
                        <input type="tel" class="form-control" id="telefone" name="telefone" 
                               placeholder="(00) 00000-0000">
                        <small class="text-muted">Opcional - Digite o telefone com DDD</small>
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="pagador" name="pagador">
                        <label class="form-check-label" for="pagador">
                            <i class="fas fa-crown text-warning me-1"></i>
                            É o pagador principal?
                        </label>
                        <div class="form-text">
                            O pagador principal é quem recebe o dinheiro dos outros no final.
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary" id="submitButton">
                        <i class="fas fa-save me-1"></i> Cadastrar Pessoa
                    </button>
                    <button type="button" class="btn btn-secondary" id="cancelButton" style="display: none;" onclick="cancelarEdicao()">
                        <i class="fas fa-times me-1"></i> Cancelar
                    </button>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4><i class="fas fa-users me-2"></i>Pessoas Cadastradas</h4>
                <span class="badge bg-primary"><?php echo count($usuarios); ?> pessoas</span>
            </div>
            <div class="card-body">
                <?php if(empty($usuarios)): ?>
                    <div class="text-center py-4">
                        <i class="fas fa-users fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Nenhuma pessoa cadastrada ainda.</p>
                    </div>
                <?php else: ?>
                    <div class="list-group">
                        <?php foreach($usuarios as $usuario): ?>
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div class="flex-grow-1">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-user-circle text-muted me-2"></i>
                                        <strong><?php echo htmlspecialchars($usuario['nome']); ?></strong>
                                        <?php if($usuario['pagador']): ?>
                                            <span class="badge bg-success ms-2">
                                                <i class="fas fa-crown me-1"></i>Pagador
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                    <?php if(!empty($usuario['telefone'])): ?>
                                        <small class="text-muted d-block">
                                            <i class="fas fa-phone me-1"></i>
                                            <?php echo htmlspecialchars($usuario['telefone']); ?>
                                        </small>
                                    <?php endif; ?>
                                    <small class="text-muted">
                                        <i class="fas fa-calendar me-1"></i>
                                        Cadastrado em: <?php echo date('d/m/Y', strtotime($usuario['created_at'])); ?>
                                    </small>
                                </div>
                                <div class="ms-3 d-flex gap-2">
                                    <button type="button" class="btn btn-sm btn-outline-primary btn-editar" 
                                            data-userid="<?php echo $usuario['id']; ?>"
                                            data-username="<?php echo htmlspecialchars($usuario['nome']); ?>"
                                            data-telefone="<?php echo htmlspecialchars($usuario['telefone'] ?? ''); ?>"
                                            data-pagador="<?php echo $usuario['pagador'] ? '1' : '0'; ?>">
                                        <i class="fas fa-edit me-1"></i> Alterar
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-danger" 
                                            data-bs-toggle="modal" data-bs-target="#deleteModal" 
                                            data-userid="<?php echo $usuario['id']; ?>"
                                            data-username="<?php echo htmlspecialchars($usuario['nome']); ?>"
                                            data-ispagador="<?php echo $usuario['pagador'] ? 'true' : 'false'; ?>">
                                        <i class="fas fa-trash me-1"></i> Excluir
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Confirmação de Exclusão -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-triangle text-warning me-2"></i>
                    Confirmar Exclusão
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Tem certeza que deseja excluir <strong id="userName" class="text-danger"></strong>?</p>
                <div id="pagadorWarning" class="alert alert-warning d-none">
                    <i class="fas fa-crown me-2"></i>
                    <strong>Atenção:</strong> Esta pessoa é um pagador.
                </div>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Importante:</strong> Todos os consumos registrados para esta pessoa também serão excluídos.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i> Cancelar
                </button>
                <form method="POST" action="index.php?page=usuarios&action=delete" id="deleteForm">
                    <input type="hidden" name="id" id="userId">
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-1"></i> Excluir
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Configurar modal de exclusão
    const deleteModal = document.getElementById('deleteModal');
    deleteModal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        const userId = button.getAttribute('data-userid');
        const userName = button.getAttribute('data-username');
        const isPagador = button.getAttribute('data-ispagador') === 'true';
        
        document.getElementById('userName').textContent = userName;
        document.getElementById('userId').value = userId;
        
        // Mostrar aviso se for pagador
        const pagadorWarning = document.getElementById('pagadorWarning');
        if (isPagador) {
            pagadorWarning.classList.remove('d-none');
        } else {
            pagadorWarning.classList.add('d-none');
        }
    });
    
    // Prevenir envio duplo do formulário
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function() {
            const submitButton = this.querySelector('button[type="submit"]');
            if (submitButton) {
                submitButton.disabled = true;
                submitButton.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Processando...';
                
                // Reativar após 5 segundos (caso haja erro)
                setTimeout(() => {
                    submitButton.disabled = false;
                    submitButton.innerHTML = '<i class="fas fa-save me-1"></i> Cadastrar Pessoa';
                }, 5000);
            }
        });
    });
    
    // Validação do formulário de cadastro
    document.getElementById('formUsuario').addEventListener('submit', function(e) {
        const nomeInput = document.getElementById('nome');
        if (nomeInput.value.trim().length < 2) {
            e.preventDefault();
            alert('Por favor, digite um nome válido (mínimo 2 caracteres).');
            nomeInput.focus();
            return false;
        }
    });
    
    // Configurar botões de editar
    const btnEditar = document.querySelectorAll('.btn-editar');
    btnEditar.forEach(btn => {
        btn.addEventListener('click', function() {
            const userId = this.getAttribute('data-userid');
            const userName = this.getAttribute('data-username');
            const userTelefone = this.getAttribute('data-telefone');
            const userPagador = this.getAttribute('data-pagador') === '1';
            
            // Preencher formulário
            document.getElementById('usuario_id').value = userId;
            document.getElementById('nome').value = userName;
            document.getElementById('telefone').value = userTelefone || '';
            document.getElementById('pagador').checked = userPagador;
            
            // Alterar ação do formulário
            document.getElementById('formUsuario').action = 'index.php?page=usuarios&action=update';
            
            // Alterar título e botão
            document.getElementById('formTitle').innerHTML = '<i class="fas fa-user-edit me-2"></i>Alterar Pessoa';
            document.getElementById('submitButton').innerHTML = '<i class="fas fa-save me-1"></i> Salvar Alterações';
            document.getElementById('cancelButton').style.display = 'inline-block';
            
            // Scroll para o formulário
            document.querySelector('.col-md-6').scrollIntoView({ behavior: 'smooth', block: 'start' });
        });
    });
});

// Função para cancelar edição
function cancelarEdicao() {
    // Limpar formulário
    document.getElementById('usuario_id').value = '';
    document.getElementById('nome').value = '';
    document.getElementById('telefone').value = '';
    document.getElementById('pagador').checked = false;
    
    // Restaurar ação do formulário
    document.getElementById('formUsuario').action = 'index.php?page=usuarios&action=create';
    
    // Restaurar título e botão
    document.getElementById('formTitle').innerHTML = '<i class="fas fa-user-plus me-2"></i>Cadastrar Pessoa';
    document.getElementById('submitButton').innerHTML = '<i class="fas fa-save me-1"></i> Cadastrar Pessoa';
    document.getElementById('cancelButton').style.display = 'none';
}
</script>