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

    public static function delete($id) {
        try {
            $pdo = Database::connect();
            $stmt = $pdo->prepare("DELETE FROM User WHERE id = ?");
            $stmt->execute([$id]);
            return ["message" => "Usuário deletado com sucesso"];
        } catch (PDOException $e) {
            return ["error" => "Erro no banco: " . $e->getMessage()];
        }
    }

    public static function update($id, $nome = null, $username = null, $email = null, $senha = null, $isAlreadyHashed = false, $profilePhoto = null) {
        try {
            $pdo = Database::connect();

            // Verifica se o usuário existe
            $stmt = $pdo->prepare("SELECT * FROM User WHERE id = ?");
            $stmt->execute([$id]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$user) {
                return ["error" => "Usuário não encontrado"];
            }

            // Verifica se email já existe para outro usuário
            if ($email && $email !== $user['email']) {
                $stmt = $pdo->prepare("SELECT id FROM User WHERE email = ? AND id != ?");
                $stmt->execute([$email, $id]);
                if ($stmt->fetch()) {
                    return ["error" => "E-mail já cadastrado"];
                }
            }

            // Verifica se username já existe para outro user
            if ($username && $username !== $user['username']) {
                $stmt = $pdo->prepare("SELECT id FROM User WHERE username = ? AND id != ?");
                $stmt->execute([$username, $id]);
                if ($stmt->fetch()) {
                    return ["error" => "Username já está em uso"];
                }
            }

            // Atualiza os campos fornecidos
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
            // Atualiza senha somente se fornecida (não nula e não vazia)
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

            $values[] = $id; // Para a cláusula WHERE

            $sql = "UPDATE User SET " . implode(", ", $fields) . " WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($values);

            return ["message" => "Usuário atualizado com sucesso"];

        } catch (PDOException $e) {
            return ["error" => "Erro no banco: " . $e->getMessage()];
        }   
    }
}
