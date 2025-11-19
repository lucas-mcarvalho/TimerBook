<?php

require __DIR__ . '/../../vendor/autoload.php'; // carrega o autoload do Composer

// Carrega o .env da raiz do projeto

// Carrega o .env da raiz do projeto
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->load();

// CONEXAO DO BANCO DE DADOS MYSQL
define('DB_HOST', $_ENV['DB_HOST']);
define('DB_USER', $_ENV['DB_USERNAME']);
define('DB_PASS', $_ENV['DB_PASSWORD']);
define('DB_NAME', $_ENV['DB_DATABASE']);
class Database {
    // Para permitir testes, primeiro tentamos usar DB_DSN (ex: sqlite::memory:)
    // Também é possível injetar um PDO via DatabaseTestHelper (ver tests/DatabaseTestHelper.php)
    public static function connect() {
        // Se um PDO de teste foi injetado, use-o
        if (class_exists('DatabaseTestHelper') && DatabaseTestHelper::hasPdo()) {
            return DatabaseTestHelper::getPdo();
        }

        // Se existir DB_DSN nas variáveis, use-o (ex: sqlite::memory: ou sqlite:/path)
        if (!empty($_ENV['DB_DSN'])) {
            try {
                $pdo = new PDO($_ENV['DB_DSN']);
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                return $pdo;
            } catch (PDOException $e) {
                die("Erro de conexão (DB_DSN): " . $e->getMessage());
            }
        }

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