<?php
// models/Usuario.php
class Usuario {
    private $conn;
    private $table_name = "usuarios";

    public $id;
    public $nome;
    public $telefone;
    public $pagador;
    public $created_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                 SET nome=:nome, telefone=:telefone, pagador=:pagador";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(":nome", $this->nome);
        $stmt->bindParam(":telefone", $this->telefone);
        $stmt->bindParam(":pagador", $this->pagador, PDO::PARAM_BOOL);
        
        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function read() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function getPagador() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE pagador = 1 LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // NOVO MÉTODO: Buscar todos os pagadores
    public function getAllPagadores() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE pagador = 1 ORDER BY nome";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ? LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // NOVO MÉTODO: Excluir usuário
    public function delete($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        
        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // NOVO MÉTODO: Contar quantos pagadores existem
    public function countPagadores() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name . " WHERE pagador = 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }

    // NOVO MÉTODO: Atualizar usuário
    public function update() {
        $query = "UPDATE " . $this->table_name . " 
                 SET nome=:nome, telefone=:telefone, pagador=:pagador 
                 WHERE id=:id";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(":nome", $this->nome);
        $stmt->bindParam(":telefone", $this->telefone);
        $stmt->bindParam(":pagador", $this->pagador, PDO::PARAM_BOOL);
        $stmt->bindParam(":id", $this->id);
        
        if($stmt->execute()) {
            return true;
        }
        return false;
    }
}
?>