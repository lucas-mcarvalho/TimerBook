<?php
require_once __DIR__ . '/../core/database_config.php';

class Book
{
 
    //inserindo o livro
   public static function create($titulo, $autor, $ano_publicacao, $user_id, $caminho_arquivo = null)
{
    try {
        $pdo = Database::connect();

        $stmt = $pdo->prepare(
            "INSERT INTO Books (titulo, autor, ano_publicacao, user_id, caminho_arquivo)
             VALUES (?, ?, ?, ?, ?)"
        );
        $stmt->execute([$titulo, $autor, $ano_publicacao, $user_id, $caminho_arquivo]);

        return [
            "message" => "Livro cadastrado com sucesso",
            "book_id" => $pdo->lastInsertId()
        ];
    } catch (PDOException $e) {
        return ["error" => "Erro no banco: " . $e->getMessage()];
    }
}

// Buscar livro por ID
    public static function findById($id)
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare("SELECT * FROM Books WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
// Buscar livro por título
    public static function findByTitle($titulo)
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare("SELECT * FROM Books WHERE titulo LIKE ?");
        $stmt->execute(["%" . $titulo . "%"]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getAll()
    {
        $pdo = Database::connect();
        $stmt = $pdo->query("SELECT * FROM Books");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
// Deletar livro (Incompleto)
    public static function delete($id)
    {
        try {
            $pdo = Database::connect();
            $stmt = $pdo->prepare("DELETE FROM Books WHERE id = ?");
            $stmt->execute([$id]);
            return ["message" => "Livro excluído com sucesso"];
        } catch (PDOException $e) {
            return ["error" => "Erro no banco: " . $e->getMessage()];
        }
    }

    public static function getByUser($user_id)
{
    $pdo = Database::connect();
    $stmt = $pdo->prepare("SELECT * FROM Books WHERE user_id = ?");
    $stmt->execute([$user_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

}
