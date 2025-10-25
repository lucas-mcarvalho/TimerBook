<?php

require_once __DIR__ .'/TestCase.php';
require_once __DIR__ .'/DatabaseTestHelper.php';
require_once __DIR__ .'/../App/models/Admin.php';

class AdminTest extends TestCase {
    public function testCreateAndFindByEmail()
    {
        $email = 'admtest@example.com';
        $password = 'admsecret';
        $nome = 'Admin Teste';
        $username = 'admtest';

        $res = Admin::create($nome, $username, $email, $password);
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

    public function testDuplicateEmailFails()
    {
        $email = 'admdup@example.com';
        $password = 'pass';
        $username = 'dupadmin';

        $r1 = Admin::create('Nome1', $username, $email, $password);
        $this->assertArrayHasKey('admin_id', $r1);

        $r2 = Admin::create('Nome2', 'otheruser', $email, 'otherpass');
        $this->assertArrayHasKey('error', $r2);

        // cleanup
        Admin::delete($r1['admin_id']);
    }

    public function testGetAllAndGetByIdAndUpdate()
    {
        $email = 'admall@example.com';
        $password = 'passall';
        $username = 'alladmin';

        $res = Admin::create('NomeAll', $username, $email, $password);
        $this->assertArrayHasKey('admin_id', $res);
        $id = $res['admin_id'];

        $all = Admin::getAll();
        $this->assertIsArray($all);

        $byId = Admin::getById($id);
        $this->assertNotEmpty($byId);
        $this->assertEquals($email, $byId['email']);

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

    public function testDeleteNonexistentReturnsMessage()
    {
        // Deleting non-existente deve retornar message (DELETE SQL não falha)
        $res = Admin::delete(99999);
        $this->assertArrayHasKey('message', $res);
    }

    public function testUpdatePassword() {
        $email = 'adm_pwd@example.com';
        $password = 'oldpass';
        $res = Admin::create('NomePwd', 'userpwd', $email, $password);
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

    public function testUpdateKeepsPasswordWhenNull() {
        $email = 'adm_keep@example.com';
        $password = 'keepme';
        $res = Admin::create('NomeKeep', 'userkeep', $email, $password);
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

    public function testPasswordHashed()
    {
        $nome = 'Hash Test';
        $username = 'hashtest';
        $email = 'hash+' . uniqid() . '@example.com';
        $password = 'original123';
        $res = Admin::create($nome, $username, $email, $password);

        // usa 'admin_id' (correto) em vez de 'user_id'
        $this->assertArrayHasKey('admin_id', $res);
        $id = $res['admin_id'];

        $found = Admin::findByEmail($email);
        $this->assertNotEmpty($found);
        $this->assertArrayHasKey('senha', $found);
        $this->assertTrue(password_verify($password, $found['senha']));
        $this->assertNotEquals($password, $found['senha']); // opcional: garante que não está salvando em texto

        // cleanup
        Admin::delete($id);
    }
}