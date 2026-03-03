<?php
// controllers/ConsumoController.php
class ConsumoController {
    private $consumo;

    public function __construct($db) {
        $this->consumo = new Consumo($db);
    }

    public function registrarConsumo($dados) {
        // Agora recebe array de usuários
        $id_usuarios = $dados['id_usuarios'];
        $id_item = $dados['id_item'];
        $data_consumo = $dados['data_consumo'];
        $valor_total = $dados['valor'];
        $id_pagador = $dados['id_pagador'];
        
        // Calcular valor individual
        $total_pessoas = count($id_usuarios);
        $valor_individual = $valor_total / $total_pessoas;
        
        // Registrar consumo para CADA pessoa
        $sucessos = 0;
        foreach($id_usuarios as $id_usuario) {
            $this->consumo->id_usuario = $id_usuario;
            $this->consumo->id_item = $id_item;
            $this->consumo->data_consumo = $data_consumo;
            $this->consumo->valor = $valor_individual; // Valor JÁ dividido
            $this->consumo->id_pagador = $id_pagador;
            
            if($this->consumo->create()) {
                $sucessos++;
            }
        }
        
        if($sucessos == $total_pessoas) {
            return [
                'success' => true, 
                'message' => "Consumo registrado para $sucessos pessoa(s)! Valor dividido: R$ " . number_format($valor_individual, 2, ',', '.') . " cada"
            ];
        } else {
            return [
                'success' => false, 
                'message' => "Erro: $sucessos de $total_pessoas registros foram salvos."
            ];
        }
    }

    public function listarConsumos() {
        $stmt = $this->consumo->read();
        return $stmt->fetchAll();
    }

    public function getConsumosPorPeriodo($data_inicio, $data_fim) {
        $stmt = $this->consumo->getByPeriodo($data_inicio, $data_fim);
        return $stmt->fetchAll();
    }

    public function excluirConsumo($id) {
        if ($this->consumo->delete($id)) {
            return ['success' => true, 'message' => 'Consumo removido com sucesso!'];
        }

        return ['success' => false, 'message' => 'Erro ao remover consumo.'];
    }
}
?>