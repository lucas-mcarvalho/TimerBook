<?php
require_once __DIR__ . '/../core/database_config.php';

class SessaoLeitura 
{
    private $conn;
    private $table = 'SessaoLeitura';
    public $id;
    public $pk_leitura;
    public $data_inicio;
    public $data_fim;
    public $tempo_sessao;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Criar nova sessão
    public function criar() {
        $query = "INSERT INTO " . $this->table . " (pk_leitura, data_inicio, data_fim, tempo_sessao) 
                  VALUES (:pk_leitura, :data_inicio, :data_fim, :tempo_sessao)";
        
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':pk_leitura', $this->pk_leitura);
        $stmt->bindParam(':data_inicio', $this->data_inicio);
        $stmt->bindParam(':data_fim', $this->data_fim);
        $stmt->bindParam(':tempo_sessao', $this->tempo_sessao);

        return $stmt->execute();
    }

    // Listar sessões por leitura
    public function listarPorLeitura($pk_leitura) {
        $query = "SELECT * FROM " . $this->table . " WHERE pk_leitura = :pk_leitura ORDER BY data_inicio DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':pk_leitura', $pk_leitura);
        $stmt->execute();
        return $stmt;
    }

    // Deletar sessão
    public function deletar($id) {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}
?>
