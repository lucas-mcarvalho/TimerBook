<?php
require_once __DIR__ . '/../core/database_config.php';

use Aws\S3\S3Client;
use Aws\Exception\AwsException;


class User {
    public static function findByEmail($email) {
    try {
        $pdo = Database::connect();
        $stmt = $pdo->prepare("SELECT id, nome, username, email, senha, profile_photo 
                               FROM User 
                               WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return ["error" => "Erro no banco: " . $e->getMessage()];
    }
}

    public static function getById($id) {
        $pdo = Database::connect();
        $stmt = $pdo->prepare("SELECT * FROM User WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    public static function create($email, $password, $nome = null, $username = null, $profilePhoto = null) {
    try {
        $pdo = Database::connect();

        // Verifica se email já existe
        $stmt = $pdo->prepare("SELECT id FROM User WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            return ["error" => "E-mail já cadastrado"];
        }

        // Verifica se username já existe
        $stmt = $pdo->prepare("SELECT id FROM User WHERE username = ?");
        $stmt->execute([$username]);
        if ($stmt->fetch()) {
            return ["error" => "Username já está em uso"];
        }

        $hash = password_hash($password, PASSWORD_DEFAULT);

        // Agora já inclui a foto no INSERT
        $stmt = $pdo->prepare(
            "INSERT INTO User (nome, username, email, senha, profile_photo) VALUES (?, ?, ?, ?, ?)"
        );
        $stmt->execute([$nome ?? '', $username, $email, $hash, $profilePhoto]);

        return [
            "message" => "Usuário cadastrado com sucesso",
            "user_id" => $pdo->lastInsertId()
        ];

    } catch (PDOException $e) {
        return ["error" => "Erro no banco: " . $e->getMessage()];
    }
}

    public static function getAll() {
        $pdo = Database::connect();
        $stmt = $pdo->query("SELECT id, nome, username, email, profile_photo FROM User");
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $users;
    }

    public static function findWithBooks($id) {
    $pdo = Database::connect();
    $sql = "SELECT
                u.id as user_id,
                u.nome as user_name,
                u.email as user_email,
                b.id as book_id,
                b.titulo as book_title,
                b.autor as book_author
            FROM
                User u
            LEFT JOIN
                Books b ON u.id = b.user_id
            WHERE
                u.id = ?";

    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($results)) {
            return null;
        }

        $user_data = [
            'id' => $results[0]['user_id'],
            'nome' => $results[0]['user_name'],
            'email' => $results[0]['user_email'],
            'books' => []
        ];

        foreach ($results as $row) {
            if ($row['book_id'] !== null) {
                $user_data['books'][] = [
                    'id' => $row['book_id'],
                    'titulo' => $row['book_title'],
                    'autor' => $row['book_author']
                ];
            }
        }

        return $user_data;

    } catch (PDOException $e) {
        return null;
    }
}       public static function delete($id)
    {
        // 1. Primeiro, busca os dados do usuário para obter o caminho da foto
        $user = self::getById($id);
        if (!$user) {
            return ["error" => "Usuário não encontrado"];
        }

        // 2. Verifica se o usuário tem uma foto de perfil para deletar no S3
        if (!empty($user['profile_photo'])) {
            // Inicializa o cliente S3
            $s3Client = new S3Client([
                'version'     => 'latest',
                'region'      => $_ENV['AWS_DEFAULT_REGION'],
                'credentials' => [
                    'key'    => $_ENV['AWS_ACCESS_KEY_ID'],
                    'secret' => $_ENV['AWS_SECRET_ACCESS_KEY'],
                ]
            ]);

            try {
                // Extrai a 'key' (caminho do arquivo no bucket) da URL completa
                $path = parse_url($user['profile_photo'], PHP_URL_PATH);
                $key = ltrim($path, '/');

                // Deleta o objeto (a foto) do bucket S3
                $s3Client->deleteObject([
                    'Bucket' => $_ENV['S3_BUCKET_NAME'],
                    'Key'    => $key,
                ]);

            } catch (AwsException $e) {
                // Se falhar a exclusão no S3, retorna o erro e não deleta do banco
                return [
                    "error" => "Falha ao deletar a foto de perfil no S3",
                    "aws_message" => $e->getMessage()
                ];
            }
        }

        // 3. Se a exclusão no S3 deu certo (ou não havia foto), deleta o usuário do banco
        try {
            $pdo = Database::connect();
            $stmt = $pdo->prepare("DELETE FROM User WHERE id = ?");
            $stmt->execute([$id]);
            return ["message" => "Usuário e foto de perfil excluídos com sucesso"];
        } catch (PDOException $e) {
            // Este erro ocorreria se, por exemplo, houvesse uma restrição de chave estrangeira
            return ["error" => "Erro no banco ao deletar usuário: " . $e->getMessage()];
        }
    }

  public static function update($id, $nome = null, $username = null, $email = null, $senha = null, $isAlreadyHashed = false, $profilePhoto = null)
{
    try {
        $pdo = Database::connect();

        $stmt = $pdo->prepare("SELECT * FROM User WHERE id = ?");
        $stmt->execute([$id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$user) {
            return ["error" => "Usuário não encontrado"];
        }

        // Configura o S3
        $s3Client = new S3Client([
            'version'     => 'latest',
            'region'      => $_ENV['AWS_DEFAULT_REGION'],
            'credentials' => [
                'key'    => $_ENV['AWS_ACCESS_KEY_ID'],
                'secret' => $_ENV['AWS_SECRET_ACCESS_KEY'],
            ]
        ]);

        // Se tiver nova foto, apaga a antiga no S3
        if ($profilePhoto !== null && !empty($user['profile_photo'])) {
            $path = parse_url($user['profile_photo'], PHP_URL_PATH);
            $key = ltrim($path, '/');
            if ($key) {
                $s3Client->deleteObject([
                    'Bucket' => $_ENV['S3_BUCKET_NAME'],
                    'Key'    => $key,
                ]);
            }
        }

        // Campos dinâmicos
        $fields = [];
        $values = [];

        if ($nome !== null) {
            $fields[] = "nome = ?";
            $values[] = $nome;
        }
        if ($username !== null) {
            $fields[] = "username = ?";
            $values[] = $username;
        }
        if ($email !== null) {
            $fields[] = "email = ?";
            $values[] = $email;
        }
        if ($senha !== null && $senha !== '') {
            $fields[] = "senha = ?";
            $values[] = $isAlreadyHashed ? $senha : password_hash($senha, PASSWORD_DEFAULT);
        }
        if ($profilePhoto !== null) {
            $fields[] = "profile_photo = ?";
            $values[] = $profilePhoto;
        }

        if (empty($fields)) {
            return ["error" => "Nenhum campo para atualizar"];
        }

        $values[] = $id;
        $sql = "UPDATE User SET " . implode(", ", $fields) . " WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($values);

        return ["message" => "Usuário atualizado com sucesso"];
    } catch (AwsException $e) {
        return ["error" => "Erro AWS: " . $e->getMessage()];
    } catch (PDOException $e) {
        return ["error" => "Erro no banco: " . $e->getMessage()];
    }
}

}
