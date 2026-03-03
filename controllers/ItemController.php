<?php
// controllers/ItemController.php
class ItemController {
    private $item;

    public function __construct($db) {
        $this->item = new Item($db);
    }

    public function criarItem($dados) {
        $this->item->nome = $dados['nome'];
        
        if($this->item->create()) {
            return ['success' => true, 'message' => 'Item criado com sucesso!'];
        } else {
            return ['success' => false, 'message' => 'Erro ao criar item.'];
        }
    }

    public function listarItens() {
        $stmt = $this->item->read();
        return $stmt->fetchAll();
    }

    public function excluirItem($id) {
        if ($this->item->delete($id)) {
            return ['success' => true, 'message' => 'Item removido com sucesso!'];
        }

        return ['success' => false, 'message' => 'Erro ao remover o item.'];
    }
}
?>