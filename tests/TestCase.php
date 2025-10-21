<?php
use PHPUnit\Framework\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    protected static $pdo;

    public static function setUpBeforeClass(): void
    {
        // Cria um DB SQLite na memória ou em arquivo para testes
        $dsn = getenv('DB_DSN') ?: 'sqlite::memory:';
        self::$pdo = new PDO($dsn);
        self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Cria tabela User mínima necessária para testes
        $sql = "CREATE TABLE IF NOT EXISTS User (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            nome TEXT,
            username TEXT,
            email TEXT UNIQUE,
            senha TEXT,
            profile_photo TEXT
        );";
        self::$pdo->exec($sql);

        // Cria tabela Books mínima necessária para testes de relacionamento
        $sqlBooks = "CREATE TABLE IF NOT EXISTS Books (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            titulo TEXT,
            autor TEXT,
            ano_publicacao INTEGER,
            user_id INTEGER,
            caminho_arquivo TEXT,
            capa_livro TEXT
        );";
        self::$pdo->exec($sqlBooks);

        // Substitui a conexão usada pela classe Database
        // A Database::connect usará DB_DSN quando estiver setado no .env; como fallback
        // nós injetamos via PDO estático em DatabaseTestHelper
        DatabaseTestHelper::setPdo(self::$pdo);
    }

    public static function tearDownAfterClass(): void
    {
        self::$pdo = null;
    }
}
