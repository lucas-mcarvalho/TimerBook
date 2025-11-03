<?php
require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/DatabaseTestHelper.php';
require_once __DIR__ . '/TestCase.php';

require_once __DIR__ . '/../App/models/Reading.php';
require_once __DIR__ . '/../App/models/Books.php';

class ReadingTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
    }

    public function testCreateReading()
    {
        // Cria um usuário e um livro para associar à leitura
        $userId = self::createTestUser(null, ['nome' => 'User50', 'username' => 'user50', 'email' => 'user50_' . uniqid() . '@example.com']);
        $book = self::createTestBook(['titulo' => 'Livro para Reading', 'autor' => 'Autor R', 'ano_publicacao' => 2021, 'user_id' => $userId]);
        $this->assertArrayHasKey('book_id', $book);

        $bookId = $book['book_id'];

        $res = Reading::create($userId, $bookId, 'Em andamento', 10, 5, null, null);
        $this->assertIsArray($res);
        $this->assertArrayHasKey('reading_id', $res);

        // cleanup
        Reading::delete($res['reading_id']);
        Book::delete($bookId);
    }

    // Testando getById

    public function testGetById()
    {
        // Cria usuário e livro para teste
        $userId = self::createTestUser(null, ['nome' => 'User60', 'username' => 'user60', 'email' => 'user60_' . uniqid() . '@example.com']);
        $book = self::createTestBook(['titulo' => 'Livro GetById', 'autor' => 'Autor G', 'ano_publicacao' => 2019, 'user_id' => $userId]);
        $this->assertArrayHasKey('book_id', $book);
        $bookId = $book['book_id'];

        $r = Reading::create($userId, $bookId, 'Em andamento', 0, 0, null, null);
        $this->assertArrayHasKey('reading_id', $r);
        $id = $r['reading_id'];

        $found = Reading::getById($id);
        $this->assertIsArray($found);
        $this->assertEquals($userId, (int)$found['pk_usuario']);
        $this->assertEquals($bookId, (int)$found['livro']);

        // cleanup
        Reading::delete($id);
        Book::delete($bookId);
    }

    // Testando update
    public function testUpdateReading()
    {
        // Cria usuário e livro para teste de update
        $userId = self::createTestUser(null, ['nome' => 'User61', 'username' => 'user61', 'email' => 'user61_' . uniqid() . '@example.com']);

        $book = self::createTestBook(['titulo' => 'Livro Update', 'autor' => 'Autor U', 'ano_publicacao' => 2020, 'user_id' => $userId]);
        $this->assertArrayHasKey('book_id', $book);
        $bookId = $book['book_id'];

        $r = Reading::create($userId, $bookId, 'Em andamento', 0, 0, null, null);
        $this->assertArrayHasKey('reading_id', $r);
        $id = $r['reading_id'];

        $upd = Reading::update($id, 'Concluído', 120, 200, date('Y-m-d H:i:s'));
        $this->assertIsArray($upd);
        $this->assertArrayHasKey('message', $upd);

        $found2 = Reading::getById($id);
        $this->assertEquals('Concluído', $found2['status']);

        // cleanup
        Reading::delete($id);
        Book::delete($bookId);
    }

    // Testando getByUser e estatísticas detalhadas
    public function testGetByUserAndStatistics()
    {
        $userId = self::createTestUser(null, ['nome' => 'User77', 'username' => 'user77', 'email' => 'user77_' . uniqid() . '@example.com']);
        $b1 = self::createTestBook(['titulo' => 'Estat1', 'autor' => 'A', 'ano_publicacao' => 2000, 'user_id' => $userId]);
        $b2 = self::createTestBook(['titulo' => 'Estat2', 'autor' => 'B', 'ano_publicacao' => 2001, 'user_id' => $userId]);

        $this->assertArrayHasKey('book_id', $b1);
        $this->assertArrayHasKey('book_id', $b2);

        $r1 = Reading::create($userId, $b1['book_id'], 'Em andamento', 5, 10, null, null);
        $r2 = Reading::create($userId, $b2['book_id'], 'Concluído', 200, 300, null, date('Y-m-d H:i:s'));

        $byUser = Reading::getByUser($userId);
        $this->assertIsArray($byUser);
        $this->assertNotEmpty($byUser);

        $stats = Reading::getBookStatisticsWithDetails($userId);
        $this->assertIsArray($stats);
        $this->assertGreaterThanOrEqual(2, count($stats));

        // cleanup
        Reading::delete($r1['reading_id']);
        Reading::delete($r2['reading_id']);
        Book::delete($b1['book_id']);
        Book::delete($b2['book_id']);
    }

    // Testando delete
    public function testDeleteReading()
    {
        $userId = self::createTestUser(null, ['nome' => 'User88', 'username' => 'user88', 'email' => 'user88_' . uniqid() . '@example.com']);
        $book = self::createTestBook(['titulo' => 'Livro DeleteReading', 'autor' => 'Autor D', 'ano_publicacao' => 2015, 'user_id' => $userId]);
        $this->assertArrayHasKey('book_id', $book);

        $r = Reading::create($userId, $book['book_id'], 'Em andamento', 0, 0, null, null);
        $this->assertArrayHasKey('reading_id', $r);
        $id = $r['reading_id'];

        $del = Reading::delete($id);
        $this->assertIsArray($del);
        $this->assertArrayHasKey('message', $del);

        $notFound = Reading::getById($id);
        $this->assertFalse($notFound);

        // cleanup
        Book::delete($book['book_id']);
    }
}
