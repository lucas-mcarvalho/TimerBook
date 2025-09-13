<?php
// Configuração do banco de dados MySQL
define('DB_HOST', 'localhost'); // IP ou hostname
define('DB_USER', 'root');     // Usuário do MySQL
define('DB_NAME', 'Users');         // Nome do banco de dados
define('DB_PASS', '');  // Senha do MySQL


class Database {
    public static function connect() {
        try {
            $pdo = new PDO(
                "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8",
                DB_USER,
                DB_PASS
            );
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $pdo;
        } catch (PDOException $e) {
            die("Erro de conexão: " . $e->getMessage());
        }
    }
}