<?php
require_once __DIR__ . '/../core/database_config.php';

use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;

class Book
{
 
    //inserindo o livro
   public static function create($titulo, $autor, $ano_publicacao, $user_id, $caminho_arquivo = null,$capa_livro = null)
{
    try {
        $pdo = Database::connect();

        $stmt = $pdo->prepare(
            "INSERT INTO Books (titulo, autor, ano_publicacao, user_id, caminho_arquivo,capa_livro)
             VALUES (?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([$titulo, $autor, $ano_publicacao, $user_id, $caminho_arquivo, $capa_livro]);

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
  /*  public static function delete($id)
    {
        try {
            $pdo = Database::connect();
            $stmt = $pdo->prepare("DELETE FROM Books WHERE id = ?");
            $stmt->execute([$id]);
            return ["message" => "Livro excluído com sucesso"];
        } catch (PDOException $e) {
            return ["error" => "Erro no banco: " . $e->getMessage()];
        }
    }*/

    public static function getByUser($user_id)
{
    $pdo = Database::connect();
    $stmt = $pdo->prepare("SELECT * FROM Books WHERE user_id = ?");
    $stmt->execute([$user_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

public static function delete($id)
    {
        // 1. Buscar o livro
        $book = self::findById($id);
        if (!$book) {
            return ["error" => "Livro não encontrado"];
        }

        // 2. Se existir arquivo no S3, tenta excluir
        if (!empty($book['caminho_arquivo'])) {
            try {
                $fileUrl = $book['caminho_arquivo'];
                $urlParts = parse_url($fileUrl);
                $fileKey = ltrim($urlParts['path'], '/');

                $s3Client = new S3Client([
                    'version'     => 'latest',
                    'region'      => $_ENV['AWS_DEFAULT_REGION'],
                    'credentials' => [
                        'key'    => $_ENV['AWS_ACCESS_KEY_ID'],
                        'secret' => $_ENV['AWS_SECRET_ACCESS_KEY'],
                    ]
                ]);

                $s3Client->deleteObject([
                    'Bucket' => $_ENV['S3_BUCKET_NAME'],
                    'Key'    => $fileKey,
                ]);

            } catch (S3Exception $e) {
                return [
                    "error" => "Falha ao deletar arquivo no S3",
                    "aws_error_message" => $e->getAwsErrorMessage(),
                    "aws_error_code" => $e->getAwsErrorCode(),
                ];
            }
        }

        // 3. Agora deleta do banco
        try {
            $pdo = Database::connect();
            $stmt = $pdo->prepare("DELETE FROM Books WHERE id = ?");
            $stmt->execute([$id]);
            return ["message" => "Livro excluído com sucesso"];
        } catch (PDOException $e) {
            return ["error" => "Erro no banco: " . $e->getMessage()];
        }
    }

}
