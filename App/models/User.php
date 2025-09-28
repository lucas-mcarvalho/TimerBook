<?php
require_once '../App/core/database_config.php';

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


    public static function create($email, $password, $nome = null, $username = null) {
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

            $stmt = $pdo->prepare(
                "INSERT INTO User (nome, username, email, senha) VALUES (?, ?, ?, ?)"
            );
            $stmt->execute([$nome ?? '', $username, $email, $hash]);

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
    $pdo = Database::connect(); // Usando o método centralizado que criamos

    // CORREÇÃO APLICADA AQUI: "FROM users u"
    $sql = "SELECT
                u.id as user_id,
                u.nome as user_name,
                u.email as user_email,
                b.id as book_id,
                b.titulo as book_title,
                b.autor as book_author
            FROM
                users u -- Nome da tabela corrigido para minúsculo e plural
            LEFT JOIN
                books b ON u.id = b.user_id
            WHERE
                u.id = ?";

    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($results)) {
            return null;
        }

        // O resto do seu código para estruturar os dados está perfeito!
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
        // Para depuração, você pode temporariamente logar o erro:
        // error_log($e->getMessage());
        return null;
    }
}
}
