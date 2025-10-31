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

        // Cria tabela Admin mínima necessária para testes das funcionalidades de admin
        $sqlAdmin = "CREATE TABLE IF NOT EXISTS Admin (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            nome TEXT,
            username TEXT,
            email TEXT UNIQUE,
            senha TEXT,
            profile_photo TEXT
        );";
        self::$pdo->exec($sqlAdmin);

        // Cria tabela Reading conforme o modelo utilizado pela aplicação
        $sqlReading = "CREATE TABLE IF NOT EXISTS Reading (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            pk_usuario INTEGER,
            livro INTEGER,
            status TEXT,
            tempo_gasto INTEGER,
            paginas_lidas INTEGER,
            data_inicio TEXT,
            data_fim TEXT
        );";
        self::$pdo->exec($sqlReading);

        // Cria tabela SessaoLeitura (ReadingSession) usada pelo modelo ReadingSession
        $sqlSessao = "CREATE TABLE IF NOT EXISTS SessaoLeitura (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            pk_leitura INTEGER,
            data_inicio TEXT,
            data_fim TEXT,
            tempo_sessao INTEGER,
            paginas_lidas INTEGER
        );";
        self::$pdo->exec($sqlSessao);

        // Definições de ambiente de teste para serviços externos (ex: S3)
        $_ENV['AWS_DEFAULT_REGION'] = $_ENV['AWS_DEFAULT_REGION'] ?? 'us-east-1';
        $_ENV['AWS_ACCESS_KEY_ID'] = $_ENV['AWS_ACCESS_KEY_ID'] ?? 'test';
        $_ENV['AWS_SECRET_ACCESS_KEY'] = $_ENV['AWS_SECRET_ACCESS_KEY'] ?? 'test';
        $_ENV['S3_BUCKET_NAME'] = $_ENV['S3_BUCKET_NAME'] ?? 'test-bucket';

        // Normaliza timezone e reporting para consistência nos testes
        date_default_timezone_set('UTC');
        error_reporting(E_ALL);

        // Substitui a conexão usada pela classe Database
        // A Database::connect usará DB_DSN quando estiver setado no .env; como fallback
        // nós injetamos via PDO estático em DatabaseTestHelper
        DatabaseTestHelper::setPdo(self::$pdo);
    }

    public static function tearDownAfterClass(): void
    {
        self::$pdo = null;
    }

    // Inicia uma transação antes de cada teste para isolamento
    protected function setUp(): void
    {
        if (self::$pdo && !self::$pdo->inTransaction()) {
            self::$pdo->beginTransaction();
        }
    }

    // Reverte a transação após cada teste
    protected function tearDown(): void
    {
        if (self::$pdo && self::$pdo->inTransaction()) {
            self::$pdo->rollBack();
        }
    }

    // Retorna o PDO de teste
    public static function getPdo(): ?PDO
    {
        return self::$pdo;
    }

    // Helper: cria usuário diretamente via PDO (evita dependência de modelo)
    protected static function createTestUser(int $id = null, array $attrs = []): int
    {
        $nome = $attrs['nome'] ?? 'Test User';
        $username = $attrs['username'] ?? 'testuser' . uniqid();
        $email = $attrs['email'] ?? $username . '@example.com';
        $senha = $attrs['senha'] ?? 'pass';

        if ($id !== null) {
            $stmt = self::$pdo->prepare("INSERT OR REPLACE INTO User (id, nome, username, email, senha, profile_photo) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$id, $nome, $username, $email, password_hash($senha, PASSWORD_DEFAULT), null]);
            return $id;
        }

        $stmt = self::$pdo->prepare("INSERT INTO User (nome, username, email, senha, profile_photo) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$nome, $username, $email, password_hash($senha, PASSWORD_DEFAULT), null]);
        return (int) self::$pdo->lastInsertId();
    }

    // Helper: cria livro de teste. Usa Book::create quando disponível, senão insere direto
    protected static function createTestBook(array $attrs = []): array
    {
        $titulo = $attrs['titulo'] ?? 'Test Book ' . uniqid();
        $autor = $attrs['autor'] ?? 'Author';
        $ano = $attrs['ano_publicacao'] ?? 2000;
        $user_id = $attrs['user_id'] ?? 1;
        $caminho = $attrs['caminho_arquivo'] ?? null;
        $capa = $attrs['capa_livro'] ?? null;

        if (class_exists('Book')) {
            return Book::create($titulo, $autor, $ano, $user_id, $caminho, $capa);
        }

        $stmt = self::$pdo->prepare("INSERT INTO Books (titulo, autor, ano_publicacao, user_id, caminho_arquivo, capa_livro) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$titulo, $autor, $ano, $user_id, $caminho, $capa]);
        return [
            'message' => 'Livro cadastrado com sucesso',
            'book_id' => (int) self::$pdo->lastInsertId()
        ];
    }

    // Helper: cria reading via modelo quando disponível ou via PDO
    protected static function createTestReading(int $user_id, int $book_id, array $opts = []): array
    {
        $status = $opts['status'] ?? 'Em andamento';
        $tempo = $opts['tempo_gasto'] ?? 0;
        $paginas = $opts['paginas_lidas'] ?? 0;
        $data_inicio = $opts['data_inicio'] ?? date('Y-m-d H:i:s');
        $data_fim = $opts['data_fim'] ?? null;

        if (class_exists('Reading')) {
            return Reading::create($user_id, $book_id, $status, $tempo, $paginas, $data_inicio, $data_fim);
        }

        $stmt = self::$pdo->prepare("INSERT INTO Reading (pk_usuario, livro, status, tempo_gasto, paginas_lidas, data_inicio, data_fim) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$user_id, $book_id, $status, $tempo, $paginas, $data_inicio, $data_fim]);
        return [
            'message' => 'Reading criada com sucesso',
            'reading_id' => (int) self::$pdo->lastInsertId()
        ];
    }

    // Helper: cria admin via modelo quando disponível
    protected static function createTestAdmin(array $attrs = []): array
    {
        $nome = $attrs['nome'] ?? 'Admin Test';
        $username = $attrs['username'] ?? 'adm' . uniqid();
        $email = $attrs['email'] ?? $username . '@example.com';
        $senha = $attrs['senha'] ?? 'pass';

        if (class_exists('Admin')) {
            return Admin::create($nome, $username, $email, $senha);
        }

        $stmt = self::$pdo->prepare("INSERT INTO Admin (nome, username, email, senha, profile_photo) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$nome, $username, $email, password_hash($senha, PASSWORD_DEFAULT), null]);
        return [
            'message' => 'Admin criado',
            'admin_id' => (int) self::$pdo->lastInsertId()
        ];
    }

    // Helper: limpa tabelas
    protected static function truncateTables(array $tables): void
    {
        foreach ($tables as $t) {
            self::$pdo->exec("DELETE FROM " . $t . ";");
            self::$pdo->exec("DELETE FROM sqlite_sequence WHERE name='" . $t . "';");
        }
    }
}
