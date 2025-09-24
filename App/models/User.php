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
}
