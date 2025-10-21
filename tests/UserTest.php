<?php

require_once __DIR__ . '/TestCase.php';
require_once __DIR__ . '/DatabaseTestHelper.php';
require_once __DIR__ . '/../App/models/User.php';

class UserTest extends TestCase
{
    public function testCreateAndFindByEmail()
    {
        // Cria usuário
        $email = 'test@example.com';
        $password = 'secret123';
        $nome = 'Teste';
        $username = 'testuser';

        $result = User::create($email, $password, $nome, $username);
        $this->assertArrayHasKey('user_id', $result);

        // Verifica se pode achar por email
        $found = User::findByEmail($email);
        $this->assertNotEmpty($found);
        $this->assertEquals($email, $found['email']);
        $this->assertEquals($username, $found['username']);

        // Limpeza: deleta usuário
        $delete = User::delete($result['user_id']);
        $this->assertArrayHasKey('message', $delete);
    }

    public function testDuplicateEmailFails()
    {
        $email = 'dup@example.com';
        $password = 'secret';
        $username = 'dupuser';

        $res1 = User::create($email, $password, 'Nome', $username);
        $this->assertArrayHasKey('user_id', $res1);

        $res2 = User::create($email, 'other', 'Nome2', 'otheruser');
        $this->assertArrayHasKey('error', $res2);

        // cleanup
        User::delete($res1['user_id']);
    }

    public function testGetAllAndGetByIdAndUpdate()
    {
        $email = 'alltest@example.com';
        $password = 'secret';
        $username = 'alluser';

        $res = User::create($email, $password, 'NomeAll', $username);
        $this->assertArrayHasKey('user_id', $res);
        $id = $res['user_id'];

        $all = User::getAll();
        $this->assertIsArray($all);

        $byId = User::getById($id);
        $this->assertNotEmpty($byId);
        $this->assertEquals($email, $byId['email']);

        // update
        $upd = User::update($id, 'NomeAtualizado', null, null, null, false, null);
        $this->assertArrayHasKey('message', $upd);

        // cleanup
        User::delete($id);
    }

    public function testFindWithBooks()
    {
        // cria usuário e livro relacionado
        $email = 'rel@example.com';
        $res = User::create($email, 'pass', 'RelUser', 'reluser');
        $this->assertArrayHasKey('user_id', $res);
        $uid = $res['user_id'];

        // insere livro diretamente na conexão de teste
        $pdo = DatabaseTestHelper::getPdo();
        $stmt = $pdo->prepare("INSERT INTO Books (titulo, autor, ano_publicacao, user_id) VALUES (?, ?, ?, ?)");
        $stmt->execute(['Livro Teste', 'Autor', 2020, $uid]);

        $result = User::findWithBooks($uid);
        $this->assertNotNull($result);
        $this->assertArrayHasKey('books', $result);
        $this->assertCount(1, $result['books']);

        // cleanup
        User::delete($uid);
    }

    public function testDuplicateUsernameFails()
    {
        $email1 = 'u1@example.com';
        $email2 = 'u2@example.com';
        $password = 'secret';
        $username = 'sameuser';

        $r1 = User::create($email1, $password, 'Nome1', $username);
        $this->assertArrayHasKey('user_id', $r1);

        $r2 = User::create($email2, $password, 'Nome2', $username);
        $this->assertArrayHasKey('error', $r2);

        // cleanup
        User::delete($r1['user_id']);
    }

    public function testPasswordIsHashedAndUpdatePassword()
    {
        $email = 'hash@example.com';
        $password = 'original123';
        $res = User::create($email, $password, 'HashUser', 'hashuser');
        $this->assertArrayHasKey('user_id', $res);
        $id = $res['user_id'];

        $found = User::findByEmail($email);
        $this->assertNotEmpty($found);
        $this->assertArrayHasKey('senha', $found);
        $this->assertTrue(password_verify($password, $found['senha']));

        // atualiza senha
        $upd = User::update($id, null, null, null, 'newpass123', false, null);
        $this->assertArrayHasKey('message', $upd);

        $found2 = User::getById($id);
        $this->assertTrue(password_verify('newpass123', $found2['senha']));

        // cleanup
        User::delete($id);
    }

    public function testFindWithBooksWhenNoBooks()
    {
        $email = 'nobook@example.com';
        $res = User::create($email, 'pass', 'NoBook', 'nobook');
        $this->assertArrayHasKey('user_id', $res);
        $uid = $res['user_id'];

        $result = User::findWithBooks($uid);
        $this->assertNotNull($result);
        $this->assertArrayHasKey('books', $result);
        $this->assertIsArray($result['books']);
        $this->assertCount(0, $result['books']);

        // cleanup
        User::delete($uid);
    }
}
