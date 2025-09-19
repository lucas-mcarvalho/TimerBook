<?php
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../core/database_config.php';
require_once __DIR__ . '/../../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class UserController {

    public function register() {
        // Usa $_POST (multipart ou x-www-form-urlencoded)
        $nome = $_POST['nome'] ?? null;
        $username = $_POST['username'] ?? null;
        $email = $_POST['email'] ?? null;
        $password = $_POST['password'] ?? null;

        if (!$email || !$password || !$username) {
            http_response_code(400);
            echo json_encode(["error" => "E-mail, senha e username são obrigatórios"]);
            return;
        }

        // Cria usuário
        $result = User::create($email, $password, $nome, $username);

        if (isset($result['error'])) {
            echo json_encode($result);
            return;
        }

        $userId = $result['user_id'];

        // 🔹 Se houver foto, processa upload
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['photo'];
            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $allowed = ['jpg', 'jpeg', 'png', 'gif'];

            if (in_array(strtolower($ext), $allowed)) {
                $newName = uniqid() . "." . $ext;
                $target = __DIR__ . '/../../public/uploads/' . $newName;

                if (move_uploaded_file($file['tmp_name'], $target)) {
                    $pdo = Database::connect();
                    $stmt = $pdo->prepare("UPDATE `User` SET profile_photo=? WHERE id=?");
                    $stmt->execute(['/uploads/' . $newName, $userId]);

                    $result['photo_path'] = '/uploads/' . $newName;
                } else {
                    $result['photo_error'] = "Erro ao salvar a foto";
                }
            } else {
                $result['photo_error'] = "Formato de arquivo não permitido";
            }
        }

        echo json_encode($result);
    }

    public function getAll() {
        $users = User::getAll();

        if (isset($users['error'])) {
            http_response_code(500);
            echo json_encode($users);
        } else {
            echo json_encode($users);
        }
    }

    public function forgotPassword() {
        $email = $_POST['email'] ?? '';
        $pdo = Database::connect();
        $stmt = $pdo->prepare("SELECT id FROM `User` WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            $token = bin2hex(random_bytes(32));
            $stmt = $pdo->prepare("UPDATE `User` SET reset_token = ?, reset_token_expire = DATE_ADD(NOW(), INTERVAL 1 HOUR) WHERE id = ?");
            $stmt->execute([$token, $user['id']]);

            $resetLink = "http://localhost:8000/TimerBook/public/reset-password?token=$token";
            $mail = new PHPMailer(true);

            try {
                $mail->SMTPDebug = 2;
                $mail->Debugoutput = 'html';
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com'; 
                $mail->SMTPAuth = true;
                $mail->Username = 'timerbook.app@gmail.com';
                $mail->Password = 'scrflqdpzcbuctbs';
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;
                $mail->setFrom('timerbook.app@gmail.com', 'TimerBook');
                $mail->addAddress($email);
                $mail->Subject = 'Redefinir Senha';
                $mail->Body = "Clique no link para redefinir sua senha: $resetLink";
                $mail->send();
                echo json_encode(['success' => true]);
            } catch (Exception $e) {
                echo json_encode(['error' => $e->getMessage()]);
            }
        } else {
            echo json_encode(['error' => 'E-mail não encontrado.']);
        }
    }

    public function resetPassword() {
        $token = $_POST['token'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $pdo = Database::connect();
        $stmt = $pdo->prepare("SELECT id FROM `User` WHERE reset_token = ? AND reset_token_expire > NOW()");
        $stmt->execute([$token]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            $hash = password_hash($newPassword, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE `User` SET senha = ?, reset_token = NULL, reset_token_expire = NULL WHERE id = ?");
            $stmt->execute([$hash, $user['id']]);
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['error' => 'Token inválido ou expirado.']);
        }
    }
}