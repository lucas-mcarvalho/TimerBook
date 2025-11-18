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



    public function testStarReading()
{
    $userId = self::createTestUser(null, ['nome' => 'UserIni', 'username' => 'iniuser', 'email' => 'ini_' . uniqid() . '@example.com']);
    $book = self::createTestBook(['titulo' => 'Livro Iniciar', 'autor' => 'Autor I', 'user_id' => $userId]);
    $this->assertArrayHasKey('book_id', $book);

    // Cria nova leitura
    $leituraId = Reading::iniciarLeitura($userId, $book['book_id']);
    $this->assertNotEmpty($leituraId);

    // Tenta iniciar novamente o mesmo livro — deve retornar o mesmo id
    $mesmoId = Reading::iniciarLeitura($userId, $book['book_id']);
    $this->assertEquals($leituraId, $mesmoId);

    // cleanup
    Reading::delete($leituraId);
    Book::delete($book['book_id']);
}


public function testEndReading()
{
    $userId = self::createTestUser(null, ['nome' => 'UserFin', 'username' => 'finuser', 'email' => 'fin_' . uniqid() . '@example.com']);
    $book = self::createTestBook(['titulo' => 'Livro Finalizar', 'autor' => 'Autor F', 'user_id' => $userId]);
    $reading = Reading::create($userId, $book['book_id'], 'Em andamento', 0, 0, null, null);

    // Cria sessões simuladas para esta leitura
    $pdo = self::getPdo();
    $ins = $pdo->prepare('INSERT INTO SessaoLeitura (pk_leitura, data_inicio, data_fim, tempo_sessao, paginas_lidas) VALUES (?, ?, ?, ?, ?)');
    $ins->execute([$reading['reading_id'], date('Y-m-d H:i:s', strtotime('-30 minutes')), date('Y-m-d H:i:s'), 1800, 20]);

    // Chama finalizarLeitura
    $rows = Reading::finalizarLeitura($reading['reading_id']);
    $this->assertGreaterThanOrEqual(1, $rows);

    // Confirma atualização
    $found = Reading::getById($reading['reading_id']);
    $this->assertEquals('Finalizada', $found['status']);

    // cleanup
    Reading::delete($reading['reading_id']);
    Book::delete($book['book_id']);
}


public function testStatistcsUser()
{
    $userId = self::createTestUser(null, ['nome' => 'UserStats', 'username' => 'statsuser', 'email' => 'stats_' . uniqid() . '@example.com']);
    $b1 = self::createTestBook(['titulo' => 'Livro 1', 'user_id' => $userId]);
    $b2 = self::createTestBook(['titulo' => 'Livro 2', 'user_id' => $userId]);

    Reading::create($userId, $b1['book_id'], 'Finalizada', 1200, 50, null, date('Y-m-d H:i:s'));
    Reading::create($userId, $b2['book_id'], 'Finalizada', 600, 30, null, date('Y-m-d H:i:s'));

    $stats = Reading::estatisticasUsuario($userId);
    $this->assertIsArray($stats);
    $this->assertArrayHasKey('total_livros', $stats);
    $this->assertGreaterThanOrEqual(2, (int)$stats['total_livros']);
    $this->assertGreaterThan(0, (float)$stats['tempo_total']);

    // cleanup
    $pdo = self::getPdo();
    $pdo->exec("DELETE FROM Reading WHERE pk_usuario = " . (int)$userId);
}


public function testGetReadinBook()
{
    $userId = self::createTestUser(null, ['nome' => 'UserGB', 'username' => 'gbuser', 'email' => 'gb_' . uniqid() . '@example.com']);
    $book = self::createTestBook(['titulo' => 'Livro GB', 'user_id' => $userId]);
    $reading = Reading::create($userId, $book['book_id'], 'Em andamento', 0, 0);

    $leituras = Reading::getReadinBook($book['book_id']);
    $this->assertIsArray($leituras);
    $this->assertNotEmpty($leituras);
    $this->assertEquals($book['book_id'], $leituras[0]['livro']);

    // cleanup
    Reading::delete($reading['reading_id']);
    Book::delete($book['book_id']);
}


public function testGetSessionBook()
{
    $userId = self::createTestUser(null, ['nome' => 'UserSB', 'username' => 'sbuser', 'email' => 'sb_' . uniqid() . '@example.com']);
    $book = self::createTestBook(['titulo' => 'Livro SB', 'user_id' => $userId]);
    $reading = Reading::create($userId, $book['book_id']);

    // Cria duas sessões associadas à leitura
    $pdo = self::getPdo();
    $pdo->prepare("INSERT INTO SessaoLeitura (pk_leitura, data_inicio, data_fim, tempo_sessao, paginas_lidas) VALUES (?, ?, ?, ?, ?)")
        ->execute([$reading['reading_id'], date('Y-m-d H:i:s', strtotime('-1 hour')), date('Y-m-d H:i:s'), 3600, 10]);
    $pdo->prepare("INSERT INTO SessaoLeitura (pk_leitura, data_inicio, data_fim, tempo_sessao, paginas_lidas) VALUES (?, ?, ?, ?, ?)")
        ->execute([$reading['reading_id'], date('Y-m-d H:i:s', strtotime('-2 hours')), date('Y-m-d H:i:s', strtotime('-1 hour')), 3600, 5]);

    $sessoes = Reading::getSessionBook($book['book_id']);
    $this->assertIsArray($sessoes);
    $this->assertGreaterThanOrEqual(2, count($sessoes));
    $this->assertArrayHasKey('sessao_id', $sessoes[0]);

    // cleanup
    $pdo->exec("DELETE FROM SessaoLeitura WHERE pk_leitura = " . (int)$reading['reading_id']);
    Reading::delete($reading['reading_id']);
    Book::delete($book['book_id']);
}

public function testGetBookStatisticsWithDetails()
{
    // Cria um usuário e dois livros
    $userId = self::createTestUser(null, ['nome' => 'UserStatsDetail', 'username' => 'stats_detail', 'email' => 'stats_detail_' . uniqid() . '@example.com']);
    $book1 = self::createTestBook(['titulo' => 'Livro A', 'autor' => 'Autor X', 'user_id' => $userId]);
    $book2 = self::createTestBook(['titulo' => 'Livro B', 'autor' => 'Autor Y', 'user_id' => $userId]);

    $book1_id = $book1['book_id'];
    $book2_id = $book2['book_id'];

    // Cria leituras para os livros
    // Livro A: Em andamento, 120 minutos, 50 páginas
    $r1 = Reading::create($userId, $book1_id, 'Em andamento', 120, 50, date('Y-m-d H:i:s', strtotime('-1 day')), null);
    // Livro B: Finalizada, 60 minutos, 30 páginas
    $r2 = Reading::create($userId, $book2_id, 'Finalizada', 60, 30, date('Y-m-d H:i:s', strtotime('-2 days')), date('Y-m-d H:i:s', strtotime('-1 day')));

    // Executar o método a ser testado
    $result = Reading::getBookStatisticsWithDetails($userId);

    // Deve retornar um array com 2 entradas (2 livros diferentes)
    $this->assertIsArray($result);
    $this->assertCount(2, $result);

    // Organizar o retorno por ID para facilitar o teste
    $stats = [];
    foreach ($result as $row) {
         $stats[$row["id"]] = $row;
    }

    // LIVRO A (Em andamento)
    $this->assertArrayHasKey($book1_id, $stats);
    $this->assertEquals("Livro A", $stats[$book1_id]["titulo"]);
    $this->assertEquals("Em andamento", $stats[$book1_id]["status"]);
    $this->assertEquals(120, (int)$stats[$book1_id]["tempo_gasto"]);
    $this->assertEquals(50, (int)$stats[$book1_id]["paginas_lidas"]);

    // LIVRO B (Finalizada)
    $this->assertArrayHasKey($book2_id, $stats);
    $this->assertEquals("Livro B", $stats[$book2_id]["titulo"]);
    $this->assertEquals("Finalizada", $stats[$book2_id]["status"]);
    $this->assertEquals(60, (int)$stats[$book2_id]["tempo_gasto"]);
    $this->assertEquals(30, (int)$stats[$book2_id]["paginas_lidas"]);

    // Cleanup
    Reading::delete($r1['reading_id']);
    Reading::delete($r2['reading_id']);
    Book::delete($book1_id);
    Book::delete($book2_id);
}

public function testGetAllReadings()
{
    // Cria um usuário e um livro para associar à leitura
    $userId = self::createTestUser(null, ['nome' => 'UserGetAll', 'username' => 'user_getall', 'email' => 'getall_' . uniqid() . '@example.com']);
    $bookTitle = 'Livro GetAll';
    $book = self::createTestBook(['titulo' => $bookTitle, 'autor' => 'Autor GA', 'ano_publicacao' => 2022, 'user_id' => $userId]);
    $this->assertArrayHasKey('book_id', $book);
    $bookId = $book['book_id'];

    // Cria uma leitura
    $r = Reading::create($userId, $bookId, 'Em andamento', 0, 0, null, null);
    $this->assertArrayHasKey('reading_id', $r);
    $readingId = $r['reading_id'];

    // Chama o método getAll
    $allReadings = Reading::getAll();

    // Asserts
    $this->assertIsArray($allReadings);
    $this->assertNotEmpty($allReadings);

    // Verifica se a leitura criada está presente no resultado
    $found = false;
    foreach ($allReadings as $reading) {
        if ((int)$reading['id'] === (int)$readingId) {
            $found = true;
            $this->assertEquals($bookTitle, $reading['livro_titulo']);
            $this->assertEquals($userId, (int)$reading['pk_usuario']);
            break;
        }
    }
    $this->assertTrue($found, "A leitura criada com ID {$readingId} não foi encontrada no resultado de getAll().");

    // Cleanup
    Reading::delete($readingId);
    Book::delete($bookId);
}

}
