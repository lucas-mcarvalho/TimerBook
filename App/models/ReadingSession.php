<?php
require_once __DIR__ . '/../core/database_config.php';

class ReadingSession
{
    // Criar nova sessão de leitura
    public static function create($reading_id, $data_inicio = null, $data_fim = null ,$tempo_sessao = 0 ,$paginas_lidas = null)
    {
        try {
            $pdo = Database::connect();
            $data_inicio = $data_inicio ?? date('Y-m-d H:i:s');

            $stmt = $pdo->prepare("
                INSERT INTO SessaoLeitura (pk_leitura, data_inicio, data_fim, tempo_sessao,paginas_lida)
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt->execute([$reading_id, $data_inicio, $data_fim, $tempo_sessao,$paginas_lidas]);

            return [
                "message" => "Sessão de leitura criada com sucesso",
                "session_id" => $pdo->lastInsertId()
            ];
        } catch (PDOException $e) {
            return ["error" => "Erro ao criar sessão de leitura: " . $e->getMessage()];
        }
    }

    // Buscar todas as sessões
    public static function getAll()
    {
        try {
            $pdo = Database::connect();
            $stmt = $pdo->query("
                SELECT s.*, r.id AS reading_id, r.pk_usuario
                FROM SessaoLeitura s
                JOIN Reading r ON s.pk_leitura = r.id
                ORDER BY s.data_inicio DESC
            ");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return ["error" => "Erro ao buscar sessões: " . $e->getMessage()];
        }
    }

    // Atualizar sessão de leitura
    public static function update($id, $data_inicio = null, $data_fim = null, $tempo_sessao = null, $paginas_lidas = null)
    {
        try {
            $pdo = Database::connect();

            $fields = [];
            $values = [];

            if ($data_inicio !== null) {
                $fields[] = "data_inicio = ?";
                $values[] = $data_inicio;
            }
            if ($data_fim !== null) {
                $fields[] = "data_fim = ?";
                $values[] = $data_fim;
            }
            if ($tempo_sessao !== null) {
                $fields[] = "tempo_sessao = ?";
                $values[] = $tempo_sessao;
            }
            if ($paginas_lidas !== null) {
                $fields[] = "paginas_lida = ?";
                $values[] = $paginas_lidas;
            }
            if (empty($fields)) {
                return ["error" => "Nenhum campo informado para atualização"];
            }

            $values[] = $id;

            $sql = "UPDATE SessaoLeitura SET " . implode(", ", $fields) . " WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($values);

            return ["message" => "Sessão de leitura atualizada com sucesso"];
        } catch (PDOException $e) {
            return ["error" => "Erro ao atualizar sessão: " . $e->getMessage()];
        }
    }

    // Deletar sessão
    public static function delete($id)
    {
        try {
            $pdo = Database::connect();
            $stmt = $pdo->prepare("DELETE FROM SessaoLeitura WHERE id = ?");
            $stmt->execute([$id]);
            return ["message" => "Sessão de leitura excluída com sucesso"];
        } catch (PDOException $e) {
            return ["error" => "Erro ao excluir sessão: " . $e->getMessage()];
        }
    }
}
?>