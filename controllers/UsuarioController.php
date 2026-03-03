<?php
// controllers/UsuarioController.php
class UsuarioController {
    private $usuario;

    public function __construct($db) {
        $this->usuario = new Usuario($db);
    }

    public function criarUsuario($dados) {
        $this->usuario->nome = $dados['nome'];
        $this->usuario->telefone = isset($dados['telefone']) && !empty(trim($dados['telefone'])) ? trim($dados['telefone']) : null;
        $this->usuario->pagador = isset($dados['pagador']) ? 1 : 0;
        
        if($this->usuario->create()) {
            return ['success' => true, 'message' => 'Usuário criado com sucesso!'];
        } else {
            return ['success' => false, 'message' => 'Erro ao criar usuário.'];
        }
    }

    public function listarUsuarios() {
        $stmt = $this->usuario->read();
        return $stmt->fetchAll();
    }

    public function getPagador() {
        return $this->usuario->getPagador();
    }

    // NOVO MÉTODO: Buscar todos os pagadores
    public function getAllPagadores() {
        return $this->usuario->getAllPagadores();
    }

    // NOVO MÉTODO: Excluir usuário
    public function excluirUsuario($id) {
        // Verificar se o usuário existe
        $usuario = $this->usuario->getById($id);
        if(!$usuario) {
            return ['success' => false, 'message' => 'Usuário não encontrado.'];
        }
        
        // Não permitir excluir se for o único pagador
        if($usuario['pagador']) {
            $totalPagadores = $this->usuario->countPagadores();
            
            if($totalPagadores <= 1) {
                return [
                    'success' => false, 
                    'message' => 'Não é possível excluir o único pagador do sistema. Cadastre outro pagador primeiro.'
                ];
            }
        }
        
        // Excluir usuário
        if($this->usuario->delete($id)) {
            return [
                'success' => true, 
                'message' => 'Usuário "' . $usuario['nome'] . '" excluído com sucesso!'
            ];
        } else {
            return ['success' => false, 'message' => 'Erro ao excluir usuário.'];
        }
    }

    // NOVO MÉTODO: Buscar usuário por ID
    public function getUsuario($id) {
        return $this->usuario->getById($id);
    }

    // NOVO MÉTODO: Atualizar usuário
    public function atualizarUsuario($id, $dados) {
        // Verificar se o usuário existe
        $usuarioExistente = $this->usuario->getById($id);
        if(!$usuarioExistente) {
            return ['success' => false, 'message' => 'Usuário não encontrado.'];
        }
        
        // Verificar se está tentando remover o único pagador
        if($usuarioExistente['pagador'] && !isset($dados['pagador'])) {
            $totalPagadores = $this->usuario->countPagadores();
            if($totalPagadores <= 1) {
                return [
                    'success' => false, 
                    'message' => 'Não é possível remover o único pagador do sistema. Marque outro pagador primeiro.'
                ];
            }
        }
        
        $this->usuario->id = $id;
        $this->usuario->nome = $dados['nome'];
        $this->usuario->telefone = isset($dados['telefone']) && !empty(trim($dados['telefone'])) ? trim($dados['telefone']) : null;
        $this->usuario->pagador = isset($dados['pagador']) ? 1 : 0;
        
        if($this->usuario->update()) {
            return ['success' => true, 'message' => 'Usuário atualizado com sucesso!'];
        } else {
            return ['success' => false, 'message' => 'Erro ao atualizar usuário.'];
        }
    }
}
?>