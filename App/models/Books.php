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

    $pdo = Database::connect();

    try {
        // --- Inicia transação ---
        $pdo->beginTransaction();

        // 1️⃣ Busca todas as leituras relacionadas a este livro
        $stmt = $pdo->prepare("SELECT id FROM Reading WHERE livro = ?");
        $stmt->execute([$id]);
        $leituras = $stmt->fetchAll(PDO::FETCH_COLUMN);

        // 2️⃣ Para cada leitura, apaga as sessões ligadas a ela
        if (!empty($leituras)) {
            $placeholders = implode(',', array_fill(0, count($leituras), '?'));
            $stmt = $pdo->prepare("DELETE FROM SessaoLeitura WHERE pk_leitura IN ($placeholders)");
            $stmt->execute($leituras);

            // 3️⃣ Agora apaga as leituras
            $stmt = $pdo->prepare("DELETE FROM Reading WHERE id IN ($placeholders)");
            $stmt->execute($leituras);
        }

        // --- Deleta arquivos do S3 ---
        $s3Client = new S3Client([
            'version'     => 'latest',
            'region'      => $_ENV['AWS_DEFAULT_REGION'],
            'credentials' => [
                'key'    => $_ENV['AWS_ACCESS_KEY_ID'],
                'secret' => $_ENV['AWS_SECRET_ACCESS_KEY'],
            ]
        ]);

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

        if (!empty($objects)) {
            $s3Client->deleteObjects([
                'Bucket' => $_ENV['S3_BUCKET_NAME'],
                'Delete' => ['Objects' => $objects, 'Quiet' => false],
            ]);
        }

        // 4️⃣ Finalmente, apaga o livro
        $stmt = $pdo->prepare("DELETE FROM Books WHERE id = ?");
        $stmt->execute([$id]);

        // --- Finaliza transação ---
        $pdo->commit();

        return ["message" => "Livro, leituras e sessões excluídos com sucesso"];
    } catch (Exception $e) {
        $pdo->rollBack();
        return ["error" => "Falha ao deletar: " . $e->getMessage()];
    }
}



public static function update($id, $titulo = null, $autor = null, $ano_publicacao = null, $novoArquivo = null, $novaCapa = null)
{
    try {
        $pdo = Database::connect();

        $livroExistente = self::findById($id);
        if (!$livroExistente) {
            return ["error" => "Livro não encontrado"];
        }

        $s3Client = new S3Client([
            'version'     => 'latest',
            'region'      => $_ENV['AWS_DEFAULT_REGION'],
            'credentials' => [
                'key'    => $_ENV['AWS_ACCESS_KEY_ID'],
                'secret' => $_ENV['AWS_SECRET_ACCESS_KEY'],
            ]
        ]);

        // Se houver novo arquivo, deleta o antigo no S3
        if ($novoArquivo !== null && !empty($livroExistente['caminho_arquivo'])) {
            $path = parse_url($livroExistente['caminho_arquivo'], PHP_URL_PATH);
            $key = ltrim($path, '/');
            if ($key) {
                $s3Client->deleteObject([
                    'Bucket' => $_ENV['S3_BUCKET_NAME'],
                    'Key'    => $key,
                ]);
            }
        }

        // Se houver nova capa, deleta a antiga no S3
        if ($novaCapa !== null && !empty($livroExistente['capa_livro'])) {
            $path = parse_url($livroExistente['capa_livro'], PHP_URL_PATH);
            $key = ltrim($path, '/');
            if ($key) {
                $s3Client->deleteObject([
                    'Bucket' => $_ENV['S3_BUCKET_NAME'],
                    'Key'    => $key,
                ]);
            }
        }

        // Monta o SQL dinamicamente
        $campos = [];
        $valores = [];

        if ($titulo !== null) {
            $campos[] = "titulo = ?";
            $valores[] = $titulo;
        }

        if ($autor !== null) {
            $campos[] = "autor = ?";
            $valores[] = $autor;
        }

        if ($ano_publicacao !== null) {
            $campos[] = "ano_publicacao = ?";
            $valores[] = $ano_publicacao;
        }

        if ($novoArquivo !== null) {
            $campos[] = "caminho_arquivo = ?";
            $valores[] = $novoArquivo;
        }

        if ($novaCapa !== null) {
            $campos[] = "capa_livro = ?";
            $valores[] = $novaCapa;
        }

        if (empty($campos)) {
            return ["error" => "Nenhum campo para atualizar"];
        }

        $valores[] = $id;
        $sql = "UPDATE Books SET " . implode(", ", $campos) . " WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($valores);

        return ["message" => "Livro atualizado com sucesso"];
    } catch (AwsException $e) {
        return ["error" => "Erro AWS: " . $e->getMessage()];
    } catch (PDOException $e) {
        return ["error" => "Erro no banco: " . $e->getMessage()];
    }
}


}