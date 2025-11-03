<?php

require_once __DIR__ . '/TestCase.php';
require_once __DIR__ . '/DatabaseTestHelper.php';
require_once __DIR__ . '/../App/models/Admin.php';

class AdminTest extends TestCase {

    // Testando criar admin
    public function testCreateAdmin()
    {
        $email = 'admtest_create@example.com';
        $password = 'admsecret';
        $nome = 'Admin Teste Create';
        $username = 'admtestcreate';

        $res = self::createTestAdmin(['nome' => $nome, 'username' => $username, 'email' => $email, 'senha' => $password]);
        $this->assertArrayHasKey('admin_id', $res);
        $id = $res['admin_id'];

        // cleanup
        $del = Admin::delete($id);
        $this->assertArrayHasKey('message', $del);
    }

    // Testando se a busca por email funciona
    public function testFindByEmail()
    {
        $email = 'admtest_find@example.com';
        $password = 'admsecret';
        $nome = 'Admin Teste Find';
        $username = 'admtestfind';

        $res = self::createTestAdmin(['nome' => $nome, 'username' => $username, 'email' => $email, 'senha' => $password]);
        $this->assertArrayHasKey('admin_id', $res);
        $id = $res['admin_id'];

        $found = Admin::findByEmail($email);
        $this->assertNotEmpty($found);
        $this->assertEquals($email, $found['email']);
        $this->assertEquals($username, $found['username']);
        $this->assertArrayHasKey('senha', $found);
        $this->assertTrue(password_verify($password, $found['senha']));

        // cleanup
        $del = Admin::delete($id);
        $this->assertArrayHasKey('message', $del);
    }

    // Testando falha ao criar admin com email duplicado 
    public function testDuplicateEmailFails()
    {
        $email = 'admdup@example.com';
        $password = 'pass';
        $username = 'dupadmin';

        $r1 = self::createTestAdmin(['nome' => 'Nome1', 'username' => $username, 'email' => $email, 'senha' => $password]);
        $this->assertArrayHasKey('admin_id', $r1);

        $r2 = self::createTestAdmin(['nome' => 'Nome2', 'username' => 'otheruser', 'email' => $email, 'senha' => 'otherpass']);
        $this->assertArrayHasKey('error', $r2);

        // cleanup
        Admin::delete($r1['admin_id']);
    }

    // Testando getAll e getById
    public function testGetAllAndGetById()
    {
        $email = 'admall@example.com';
        $password = 'passall';
        $username = 'alladmin';

        $res = self::createTestAdmin(['nome' => 'NomeAll', 'username' => $username, 'email' => $email, 'senha' => $password]);
        $this->assertArrayHasKey('admin_id', $res);
        $id = $res['admin_id'];

        $all = Admin::getAll();
        $this->assertIsArray($all);

        $byId = Admin::getById($id);
        $this->assertNotEmpty($byId);
        $this->assertEquals($email, $byId['email']);

        // cleanup
        Admin::delete($id);
    }

    // Testando update
    public function testUpdateAdmin()
    {
        $email = 'admin_update@example.com';
        $password = 'passupdate';
        $username = 'updateadmin';

        $res = self::createTestAdmin(['nome' => 'NomeBefore', 'username' => $username, 'email' => $email, 'senha' => $password]);
        $this->assertArrayHasKey('admin_id', $res);
        $id = $res['admin_id'];

        // update nome and username
        $upd = Admin::update($id, 'NomeAtualizado', 'newusername', 'newemail@example.com', null, false);
        $this->assertArrayHasKey('message', $upd);

        $found = Admin::getById($id);
        $this->assertEquals('NomeAtualizado', $found['nome']);
        $this->assertEquals('newusername', $found['username']);
        $this->assertEquals('newemail@example.com', $found['email']);

        // cleanup
        Admin::delete($id);
    }

    // Testando exclusão de admin inexistente
    public function testDeleteNonexistentReturnsMessage()
    {
        // Deleting non-existente deve retornar message (DELETE SQL não falha)
        $res = Admin::delete(99999);
        $this->assertArrayHasKey('message', $res);
    }

    // Testando exclusão de admin existente
    public function testDeleteAdmin()
    {
        $email = 'adm_delete@example.com';
        $password = 'tobedeleted';
        $res = self::createTestAdmin(['nome' => 'ToDelete', 'username' => 'todelete', 'email' => $email, 'senha' => $password]);
        $this->assertArrayHasKey('admin_id', $res);
        $id = $res['admin_id'];

        $del = Admin::delete($id);
        $this->assertArrayHasKey('message', $del);

        $found = Admin::getById($id);
        $this->assertFalse($found);
    }

    // Testando atualização de senha
    public function testUpdatePassword() {
        $email = 'adm_pwd@example.com';
        $password = 'oldpass';
        $res = self::createTestAdmin(['nome' => 'NomePwd', 'username' => 'userpwd', 'email' => $email, 'senha' => $password]);
        $this->assertArrayHasKey('admin_id', $res);
        $id = $res['admin_id'];

        $before = Admin::getById($id);
        $this->assertTrue(password_verify($password, $before['senha']));

        // Atualiza a senha (ajuste se seu update usa outra assinatura)
        $upd = Admin::update($id, 'NomePwd', 'userpwd', $email, 'newpass123', false);
        $this->assertArrayHasKey('message', $upd);

        $after = Admin::getById($id);
        $this->assertTrue(password_verify('newpass123', $after['senha']));
        $this->assertFalse(password_verify($password, $after['senha']));

        Admin::delete($id);
    }

    // Testando se a senha permanece a mesma quando null
    public function testUpdateKeepsPasswordWhenNull() {
        $email = 'adm_keep@example.com';
        $password = 'keepme';
        $res = self::createTestAdmin(['nome' => 'NomeKeep', 'username' => 'userkeep', 'email' => $email, 'senha' => $password]);
        $this->assertArrayHasKey('admin_id', $res);
        $id = $res['admin_id'];

        $before = Admin::getById($id);
        $hashBefore = $before['senha'];

        // Atualiza sem passar senha (assumindo que passar null não altera a senha)
        $upd = Admin::update($id, 'NomeKeep2', 'userkeep2', $email, null, false);
        $this->assertArrayHasKey('message', $upd);

        $after = Admin::getById($id);
        $this->assertEquals($hashBefore, $after['senha']);

        Admin::delete($id);
    }

    // Testando se a senha é armazenada como hash
    public function testPasswordHashed()
    {
        $nome = 'Hash Test';
        $username = 'hashtest';
        $email = 'hash+' . uniqid() . '@example.com';
        $password = 'original123';
        $res = self::createTestAdmin(['nome' => $nome, 'username' => $username, 'email' => $email, 'senha' => $password]);

        $this->assertArrayHasKey('admin_id', $res);
        $id = $res['admin_id'];

        $found = Admin::findByEmail($email);
        $this->assertNotEmpty($found);
        $this->assertArrayHasKey('senha', $found);
        $this->assertTrue(password_verify($password, $found['senha']));
        $this->assertNotEquals($password, $found['senha']);

        // cleanup
        Admin::delete($id);
    }
}