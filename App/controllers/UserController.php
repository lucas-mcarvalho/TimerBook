<?php
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../core/database_config.php';

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
}
