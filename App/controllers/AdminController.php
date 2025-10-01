<?php
require_once __DIR__ . '/../models/Admin.php';
require_once __DIR__ . '/../core/database_config.php';
require_once __DIR__ . '/../../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

class AdminController
{
    // LOGIN DO ADMIN
    public function login()
    {
        $contentType = $_SERVER["CONTENT_TYPE"] ?? '';

        if (stripos($contentType, "application/json") !== false) {
            $data = json_decode(file_get_contents("php://input"), true);
            $email = $data['email'] ?? null;
            $password = $data['password'] ?? null;
        } else {
            $email = $_POST['email'] ?? null;
            $password = $_POST['password'] ?? null;
        }

         if (!$email || !$password) {
            http_response_code(400);
            echo json_encode(["error" => "E-mail e senha são obrigatórios"]);
            return;
        }

        $admin = Admin::findByEmail($email);

        if ($admin && isset($admin['password'])) {
            session_start();
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin'] = $admin;
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['username'] = $admin['username'];

            if (!empty($admin['profile_photo'])) {
                $_SESSION['profile_photo'] = ltrim($admin['profile_photo'], '/');
            } else {
                $_SESSION['profile_photo'] = null;
            }

            echo json_encode([
                "success" => true,
                "message" => "Login de admin realizado com sucesso",
                "admin" => [
                    "id" => $admin['id'],
                    "nome" => $admin['nome'],
                    "username" => $admin['username'],
                    "email" => $admin['email'],
                    "profile_photo" => $admin['profile_photo'] ?? null
                ]
            ]);
        } else {
            http_response_code(401);
            echo json_encode(["error" => "E-mail ou senha inválidos"]);
        }
    }

    // REGISTRO DE ADMIN
    public function register()
    {
        $contentType = $_SERVER["CONTENT_TYPE"] ?? '';

        if (stripos($contentType, "application/json") !== false) {
            $data = json_decode(file_get_contents("php://input"), true);
            $nome = $data['nome'] ?? null;
            $username = $data['username'] ?? null;
            $email = $data['email'] ?? null;
            $password = $data['password'] ?? null;
        } else {
            $nome = $_POST['nome'] ?? null;
            $username = $_POST['username'] ?? null;
            $email = $_POST['email'] ?? null;
            $password = $_POST['password'] ?? null;
        }

        if (!$email || !$password || !$username) {
            http_response_code(400);
            echo json_encode(["error" => "E-mail, senha e username são obrigatórios"]);
            return;
        }

        $result = Admin::create($nome, $email, $password, $username);

        if (isset($result['error'])) {
            echo json_encode($result);
            return;
        }

        echo json_encode($result);
    }

    // LISTAR TODOS OS ADMINS
    public function getAll()
    {
        $admins = Admin::getAll();

        if (isset($admins['error'])) {
            http_response_code(500);
            echo json_encode($admins);
        } else {
            echo json_encode($admins);
        }
    }

    // EDITAR ADMIN
    public function update()
    {
        $id = $_POST['id'] ?? null;
        $nome = $_POST['nome'] ?? null;
        $email = $_POST['email'] ?? null;
        $username = $_POST['username'] ?? null;
        $senha = $_POST['senha'] ?? null;

        if (!$id || !$nome || !$email || !$username) {
            http_response_code(400);
            echo json_encode(["error" => "Campos obrigatórios não preenchidos"]);
            return;
        }

        $updated = Admin::update($id, $nome, $email, $username, $senha);

        if ($updated) {
            echo json_encode(["success" => true, "message" => "Admin atualizado com sucesso"]);
        } else {
            echo json_encode(["error" => "Erro ao atualizar admin"]);
        }
    }

    // DELETAR ADMIN
    public function delete()
    {
        $id = $_POST['id'] ?? null;

        if (!$id) {
            http_response_code(400);
            echo json_encode(["error" => "ID é obrigatório"]);
            return;
        }

        $deleted = Admin::delete($id);

        if ($deleted) {
            echo json_encode(["success" => true, "message" => "Admin excluído com sucesso"]);
        } else {
            echo json_encode(["error" => "Erro ao excluir admin"]);
        }
    }

    // LOGOUT
    public static function logout()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION = [];
        session_destroy();
        header('Location: /TimerBook/public/index.php?action=admin_login');
        exit;
    }

    // CHECK LOGIN (Protege rotas de admin)
    public static function checkLogin()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
            header('Location: /TimerBook/public/index.php?action=admin_login');
            exit;
        }
    }
}