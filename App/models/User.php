<?php
require_once '../App/core/database_config.php';

class User {
    public static function create($email, $password, $nome = null) {
        try {
            $pdo = Database::connect();

            // Verifica se já existe
            $stmt = $pdo->prepare("SELECT id FROM User WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                return ["error" => "Usuário já existe"];
            }

            $hash = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $pdo->prepare("INSERT INTO User (nome, email, senha) VALUES (?, ?, ?)");
            $stmt->execute([$nome ?? '', $email, $hash]);

            return ["message"=>"Usuário cadastrado com sucesso", "user_id"=>$pdo->lastInsertId()];

        } catch (PDOException $e) {
            return ["error" => "Erro no banco: " . $e->getMessage()];
        }
    }

      public static function getAll() {
            $pdo = Database::connect();
             $stmt = $pdo->query("SELECT id, nome, email, profile_photo FROM User");
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $users;
    }

}




