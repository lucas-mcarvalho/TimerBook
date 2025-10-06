<?php
require_once __DIR__ . '/../core/database_config.php';

use Aws\S3\S3Client;
use Aws\Exception\AwsException;

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
    $book = self::findById($id);
    if (!$book) {
        return ["error" => "Livro não encontrado"];
    }

    // cria cliente S3 uma vez
    $s3Client = new S3Client([
        'version'     => 'latest',
        'region'      => $_ENV['AWS_DEFAULT_REGION'],
        'credentials' => [
            'key'    => $_ENV['AWS_ACCESS_KEY_ID'],
            'secret' => $_ENV['AWS_SECRET_ACCESS_KEY'],
        ]
    ]);

    // monta lista de objetos a deletar (evita duplicatas)
    $objects = [];

    if (!empty($book['caminho_arquivo'])) {
        $path = parse_url($book['caminho_arquivo'], PHP_URL_PATH);
        $key = ltrim($path, '/');
        if ($key !== '') $objects[] = ['Key' => $key];
    }

    if (!empty($book['capa_livro'])) {
        $path = parse_url($book['capa_livro'], PHP_URL_PATH);
        $key = ltrim($path, '/');
        if ($key !== '' && !in_array($key, array_column($objects, 'Key'))) {
            $objects[] = ['Key' => $key];
        }
    }

    // se houver objetos, deleta em lote
    if (!empty($objects)) {
        try {
            $s3Client->deleteObjects([
                'Bucket' => $_ENV['S3_BUCKET_NAME'],
                'Delete' => [
                    'Objects' => $objects,
                    'Quiet' => false
                ],
            ]);
        } catch (AwsException $e) {
            // retorna erro e não remove do banco
            return [
                "error" => "Falha ao deletar arquivos no S3",
                "aws_message" => $e->getMessage()
            ];
        }
    }

    // finalmente deleta do banco
    try {
        $pdo = Database::connect();
        $stmt = $pdo->prepare("DELETE FROM Books WHERE id = ?");
        $stmt->execute([$id]);
        return ["message" => "Livro e arquivos excluídos com sucesso"];
    } catch (PDOException $e) {
        return ["error" => "Erro no banco: " . $e->getMessage()];
    }
}
}
