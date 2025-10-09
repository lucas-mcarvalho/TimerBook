<?php
require_once __DIR__ . '/../core/database_config.php';

class Admin {
    public static function getAll() {
        $pdo = Database::connect();
        $stmt = $pdo->query("SELECT * FROM Admin");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getById($id) {
        $pdo = Database::connect();
        $stmt = $pdo->prepare("SELECT * FROM Admin WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function findByEmail($email) {
        try {
            $pdo = Database::connect();
            // A coluna correta para a senha na tabela é 'senha'
            $stmt = $pdo->prepare("SELECT id, nome, username, email, senha, profile_photo 
                                   FROM Admin 
                                   WHERE email = ?");
            $stmt->execute([$email]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return ["error" => "Erro no banco: " . $e->getMessage()];
        }
    }

    public static function create($nome, $username, $email, $senha) {
        try {
            $pdo = Database::connect();

            // Verifica se email já existe
            $stmt = $pdo->prepare("SELECT id FROM Admin WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                return ["error" => "E-mail já cadastrado"];
            }

            // Verifica se username já existe
            $stmt = $pdo->prepare("SELECT id FROM Admin WHERE username = ?");
            $stmt->execute([$username]);
            if ($stmt->fetch()) {
                return ["error" => "Username já está em uso"];
            }

            $hash = password_hash($senha, PASSWORD_DEFAULT);

            $stmt = $pdo->prepare(
                "INSERT INTO Admin (nome, username, email, senha) VALUES (?, ?, ?, ?)"
            );
            $stmt->execute([$nome, $username, $email, $hash]);

            return [
                "message" => "Admin cadastrado com sucesso",
                "admin_id" => $pdo->lastInsertId()
            ];

        } catch (PDOException $e) {
            return ["error" => "Erro no banco: " . $e->getMessage()];
        }
    }

    public static function delete($id) {
        try {
            $pdo = Database::connect();
            $stmt = $pdo->prepare("DELETE FROM Admin WHERE id = ?");
            $stmt->execute([$id]);
            return ["message" => "Admin deletado com sucesso"];
        } catch (PDOException $e) {
            return ["error" => "Erro no banco: " . $e->getMessage()];
        }
    }

    public static function update($id, $nome = null, $username = null, $email = null, $senha = null, $isAlreadyHashed = false) {
        try {
            $pdo = Database::connect();

            // Verifica se o admin existe
            $stmt = $pdo->prepare("SELECT * FROM Admin WHERE id = ?");
            $stmt->execute([$id]);
            $admin = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$admin) {
                return ["error" => "Admin não encontrado"];
            }

            // Verifica se email já existe para outro admin
            if ($email && $email !== $admin['email']) {
                $stmt = $pdo->prepare("SELECT id FROM Admin WHERE email = ? AND id != ?");
                $stmt->execute([$email, $id]);
                if ($stmt->fetch()) {
                    return ["error" => "E-mail já cadastrado"];
                }
            }

            // Verifica se username já existe para outro admin
            if ($username && $username !== $admin['username']) {
                $stmt = $pdo->prepare("SELECT id FROM Admin WHERE username = ? AND id != ?");
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
            if ($senha !== null) {
                $fields[] = "senha = ?";
                $values[] = $isAlreadyHashed ? $senha : password_hash($senha, PASSWORD_DEFAULT);
            }

            if (empty($fields)) {
                return ["error" => "Nenhum campo para atualizar"];
            }

            $values[] = $id; // Para a cláusula WHERE

            $sql = "UPDATE Admin SET " . implode(", ", $fields) . " WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($values);

            return ["message" => "Admin atualizado com sucesso"];

        } catch (PDOException $e) {
            return ["error" => "Erro no banco: " . $e->getMessage()];
        }   
    }

public static function getUserBooks($userId) {
    try {
        if (empty($userId)) {
            return ["error" => "ID do usuário é obrigatório"];
        }
        // reutiliza a função do model Book
        $books = Book::getByUser($userId);
        if (is_array($books) && isset($books['error'])) {
            return $books;
        }
        return $books;
    } catch (PDOException $e) {
        return ["error" => "Erro no banco: " . $e->getMessage()];
    } catch (Exception $e) {
        return ["error" => "Erro: " . $e->getMessage()];
    }
}
}