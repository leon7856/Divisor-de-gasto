<?php
// models/Consumo.php
class Consumo {
    private $conn;
    private $table_name = "consumos";

    public $id;
    public $id_usuario;
    public $id_item;
    public $data_consumo;
    public $valor;
    public $id_pagador;
    public $created_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                 SET id_usuario=:id_usuario, id_item=:id_item, 
                     data_consumo=:data_consumo, valor=:valor, id_pagador=:id_pagador";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(":id_usuario", $this->id_usuario);
        $stmt->bindParam(":id_item", $this->id_item);
        $stmt->bindParam(":data_consumo", $this->data_consumo);
        $stmt->bindParam(":valor", $this->valor);
        $stmt->bindParam(":id_pagador", $this->id_pagador);
        
        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function read() {
        $query = "SELECT c.*, u.nome as nome_usuario, i.nome as nome_item, 
                         p.nome as nome_pagador
                  FROM " . $this->table_name . " c 
                  LEFT JOIN usuarios u ON c.id_usuario = u.id 
                  LEFT JOIN itens i ON c.id_item = i.id 
                  LEFT JOIN usuarios p ON c.id_pagador = p.id 
                  ORDER BY c.data_consumo DESC, c.created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function getByPeriodo($data_inicio, $data_fim) {
        $query = "SELECT c.*, u.nome as nome_usuario, i.nome as nome_item,
                         p.nome as nome_pagador
                  FROM " . $this->table_name . " c 
                  LEFT JOIN usuarios u ON c.id_usuario = u.id 
                  LEFT JOIN itens i ON c.id_item = i.id 
                  LEFT JOIN usuarios p ON c.id_pagador = p.id 
                  WHERE c.data_consumo BETWEEN ? AND ?
                  ORDER BY c.data_consumo ASC, c.id_item ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $data_inicio);
        $stmt->bindParam(2, $data_fim);
        $stmt->execute();
        return $stmt;
    }

    public function getConsumidoresByItem($id_item, $data_consumo, $id_pagador) {
        $query = "SELECT COUNT(*) as total 
                  FROM " . $this->table_name . " 
                  WHERE id_item = ? AND data_consumo = ? AND id_pagador = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id_item);
        $stmt->bindParam(2, $data_consumo);
        $stmt->bindParam(3, $id_pagador);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }

    public function delete($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }
}
?>