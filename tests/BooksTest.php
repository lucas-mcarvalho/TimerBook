<?php
require_once __DIR__ . '/TestCase.php';
require_once __DIR__ . '/DatabaseTestHelper.php';
require_once __DIR__ . '/bootstrap.php';

require_once __DIR__ . '/../App/models/Books.php';

class BooksTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        // Fornecendo AWS S3 variáveis de ambiente de teste, se não estiverem definidas
        $_ENV['AWS_DEFAULT_REGION'] = $_ENV['AWS_DEFAULT_REGION'] ?? 'us-east-1';
        $_ENV['AWS_ACCESS_KEY_ID'] = $_ENV['AWS_ACCESS_KEY_ID'] ?? 'test';
        $_ENV['AWS_SECRET_ACCESS_KEY'] = $_ENV['AWS_SECRET_ACCESS_KEY'] ?? 'test';
        $_ENV['S3_BUCKET_NAME'] = $_ENV['S3_BUCKET_NAME'] ?? 'test-bucket';
    }

    // Testando criação de livro
    public function testCreateBook()
    {
        $res = self::createTestBook(['titulo' => 'Livro Teste', 'autor' => 'Autor', 'ano_publicacao' => 2021, 'user_id' => 1]);
        $this->assertIsArray($res);
        $this->assertArrayHasKey('book_id', $res);

        // cleanup
        $del = Book::delete($res['book_id']);
        $this->assertArrayHasKey('message', $del);
    }

    // Testando busca por ID
    public function testFindById()
    {
        $res = self::createTestBook(['titulo' => 'Livro Find', 'autor' => 'Autor Find', 'ano_publicacao' => 2022, 'user_id' => 3]);
        $this->assertArrayHasKey('book_id', $res);

        $id = $res['book_id'];
        $found = Book::findById($id);
        $this->assertIsArray($found);
        $this->assertEquals('Livro Find', $found['titulo']);
        $this->assertEquals('Autor Find', $found['autor']);
        $this->assertEquals(2022, (int)$found['ano_publicacao']);
        $this->assertEquals(3, (int)$found['user_id']);

        // cleanup
        Book::delete($id);
    }

    // Testando se a busca por título funciona
    public function testFindByTitle()
    {
        // Cria dois livros
        self::createTestBook(['titulo' => 'PHP para Testes', 'autor' => 'Autor A', 'ano_publicacao' => 2020, 'user_id' => 2]);
        self::createTestBook(['titulo' => 'Guia de PHP', 'autor' => 'Autor B', 'ano_publicacao' => 2019, 'user_id' => 2]);

        $results = Book::findByTitle('PHP');
        $this->assertIsArray($results);
        $this->assertGreaterThanOrEqual(2, count($results));
    }

    // Testando getAll
    public function testGetAll()
    {
        // Cria entradas desconhecidas
        $r1 = self::createTestBook(['titulo' => 'AllBook1', 'autor' => 'A', 'ano_publicacao' => 2018, 'user_id' => 10]);
        $r2 = self::createTestBook(['titulo' => 'AllBook2', 'autor' => 'B', 'ano_publicacao' => 2017, 'user_id' => 11]);

        $all = Book::getAll();
        $this->assertIsArray($all);
        $this->assertGreaterThanOrEqual(2, count($all));

        // cleanup
        Book::delete($r1['book_id']);
        Book::delete($r2['book_id']);
    }

    // Testando getByUser
    public function testGetByUser()
    {
        $r = self::createTestBook(['titulo' => 'UserBook', 'autor' => 'AutorU', 'ano_publicacao' => 2016, 'user_id' => 20]);
        $this->assertArrayHasKey('book_id', $r);

        $user20 = Book::getByUser(20);
        $this->assertIsArray($user20);
        $this->assertNotEmpty($user20);
        $this->assertEquals(20, (int)$user20[0]['user_id']);

        // cleanup
        Book::delete($r['book_id']);
    }
    
    // Testando update
    public function testUpdateBook()
    {
        $res = Book::create('Para Atualizar', 'AutorX', 2000, 5, null, null);
        $this->assertArrayHasKey('book_id', $res);
        $id = $res['book_id'];

        $update = Book::update($id, 'Atualizado', null, null, null, null);
        $this->assertIsArray($update);
        $this->assertArrayHasKey('message', $update);

        $found = Book::findById($id);
        $this->assertEquals('Atualizado', $found['titulo']);

        // cleanup
        Book::delete($id);
    }

    // Testando delete
    public function testDeleteBook()
    {
        $res = Book::create('Para Deletar', 'AutorDel', 1999, 6, null, null);
        $this->assertArrayHasKey('book_id', $res);
        $id = $res['book_id'];

        $del = Book::delete($id);
        $this->assertIsArray($del);
        $this->assertArrayHasKey('message', $del);

        $notFound = Book::findById($id);
        $this->assertFalse($notFound);
    }
}
