<?php
require_once __DIR__ . '/../core/database_config.php';

class Reading
{
    // 🔹 Cria um novo registro de Reading
    public static function create($user_id, $book_id, $status = 'Em andamento', $tempo_gasto = 0, $paginas_lidas = 0, $data_inicio = null, $data_fim = null)
    {
        try {
            $pdo = Database::connect();

            $data_inicio = $data_inicio ?? date('Y-m-d H:i:s');

            $stmt = $pdo->prepare("
                INSERT INTO Reading (pk_usuario, livro, status, tempo_gasto, paginas_lidas, data_inicio, data_fim)
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([$user_id, $book_id, $status, $tempo_gasto, $paginas_lidas, $data_inicio, $data_fim]);

            return [
                "message" => "Reading criada com sucesso",
                "reading_id" => $pdo->lastInsertId()
            ];
        } catch (PDOException $e) {
            return ["error" => "Erro ao criar Reading: " . $e->getMessage()];
        }
    }

    // 🔹 Retorna todas as Readings
    public static function getAll()
    {
        try {
            $pdo = Database::connect();
            $stmt = $pdo->query("
                SELECT r.*, u.nome AS usuario_nome, b.titulo AS livro_titulo
                FROM Reading r
                JOIN User u ON r.pk_usuario = u.id
                JOIN Books b ON r.livro = b.id
            ");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return ["error" => "Erro ao buscar Readings: " . $e->getMessage()];
        }
    }

    // 🔹 Busca Reading por ID
    public static function getById($id)
    {
        try {
            $pdo = Database::connect();
            $stmt = $pdo->prepare("
                SELECT r.*, u.nome AS usuario_nome, b.titulo AS livro_titulo
                FROM Reading r
                JOIN User u ON r.pk_usuario = u.id
                JOIN Books b ON r.livro = b.id
                WHERE r.id = ?
            ");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return ["error" => "Erro ao buscar Reading: " . $e->getMessage()];
        }
    }

    // 🔹 Atualiza o progresso de uma Reading
    public static function update($id, $status = null, $tempo_gasto = null, $paginas_lidas = null, $data_fim = null)
    {
        try {
            $pdo = Database::connect();

            $fields = [];
            $values = [];

            if ($status !== null) {
                $fields[] = "status = ?";
                $values[] = $status;
            }
            if ($tempo_gasto !== null) {
                $fields[] = "tempo_gasto = ?";
                $values[] = $tempo_gasto;
            }
            if ($paginas_lidas !== null) {
                $fields[] = "paginas_lidas = ?";
                $values[] = $paginas_lidas;
            }
            if ($data_fim !== null) {
                $fields[] = "data_fim = ?";
                $values[] = $data_fim;
            }

            if (empty($fields)) {
                return ["error" => "Nenhum campo informado para atualização"];
            }

            $values[] = $id;

            $sql = "UPDATE Reading SET " . implode(", ", $fields) . " WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($values);

            return ["message" => "Reading atualizada com sucesso"];
        } catch (PDOException $e) {
            return ["error" => "Erro ao atualizar Reading: " . $e->getMessage()];
        }
    }

    // 🔹 Deleta uma Reading
    public static function delete($id)
    {
        try {
            $pdo = Database::connect();
            $stmt = $pdo->prepare("DELETE FROM Reading WHERE id = ?");
            $stmt->execute([$id]);
            return ["message" => "Reading excluída com sucesso"];
        } catch (PDOException $e) {
            return ["error" => "Erro ao excluir Reading: " . $e->getMessage()];
        }
    }

    // 🔹 Busca todas as Readings de um usuário específico
    public static function getByUser($user_id)
    {
        try {
            $pdo = Database::connect();
            $stmt = $pdo->prepare("
                SELECT r.*, b.titulo AS livro_titulo, b.autor AS livro_autor
                FROM Reading r
                JOIN Books b ON r.livro = b.id
                WHERE r.pk_usuario = ?
            ");
            $stmt->execute([$user_id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return ["error" => "Erro ao buscar Readings do usuário: " . $e->getMessage()];
        }
    }
}
