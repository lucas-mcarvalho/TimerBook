<?php
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../core/database_config.php';
require_once __DIR__ . '/../../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;


class UserController
{

    //FUNCAO DE LOGIN
    public function login()
    {
        //VERIFICA O TIPO QUE FOI RECEBIDO
        $contentType = $_SERVER["CONTENT_TYPE"] ?? '';
        //SE FOI JSON
        if (stripos($contentType, "application/json") !== false) {
            $data = json_decode(file_get_contents("php://input"), true);
            $email = $data['email'] ?? null;
            $password = $data['password'] ?? null;
        } else {
            //OU SE FOI FORM DATA , DO TIPO HTML
            $email = $_POST['email'] ?? null;
            $password = $_POST['password'] ?? null;
        }

        //VERIFICA SE OS CAMPOS EMAIL E SENHA FORAM PREENCHIDOS
        if (!$email || !$password) {
            //RETORNA A RESPOSTA HTTP 400
            http_response_code(400);
            //RETORNA NO FORMATO JSON
            echo json_encode(["error" => "E-mail e senha são obrigatórios"]);
            return;
        }
        //CHAMA A FUNCAO DA MODEL PRA BUSCAR EMAIL
        $user = User::findByEmail($email);
        //VERIFICA SE O USUARIO E VALIDO E VERIFICA A SENHA COM O PASSWORD_VERIFY QUE E PARA VERIFICAR SENHAS CRIPTOGRAFADAS
        if ($user && isset($user['senha']) && password_verify($password, $user['senha'])) {
           
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            $_SESSION['user_logged_in'] = true;
            $_SESSION['user'] = $user;

            // Armazena informações do usuário na sessão
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            if (!empty($user['profile_photo'])) {
               $_SESSION['profile_photo'] = ltrim($user['profile_photo'], '/');
          } else {
                   $_SESSION['profile_photo'] = null;
               }
            //RETORNA A MENSAGEM DE SUCESSO E OS DADOS DO USUARIO ,SEM INCLUIR A SENHA
            echo json_encode([
                "success" => true,
                "message" => "Login realizado com sucesso",
                "user" => [
                    "id" => $user['id'],
                    "nome" => $user['nome'],
                    "username" => $user['username'],
                    "email" => $user['email'],
                    "profile_photo" => $user['profile_photo'] ?? null
                ]
            ]);
            //SE DER ERRO RETORNA UMA RESPOSTA HTTP 400
        } else {
            http_response_code(401);
            echo json_encode(["error" => "E-mail ou senha inválidos"]);
        }
    }




    public function register()
    {
        // DETECTA O TIPO DE DADO QUE RECEBEU
        $contentType = $_SERVER["CONTENT_TYPE"] ?? '';

        //PEGA OS DADOS CONFORME O TIPO DE DADO ENVIADO, JSON OU FORM DATA
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

        // Cria usuário chamando a funcao da model create.
        $result = User::create($email, $password, $nome, $username);

        //SE O USUARIO NAO FOI CRIADO, RETORNA O ERRO
        if (isset($result['error'])) {
            echo json_encode($result);
            return;
        }
//PEGA O ID DO USUARIO CRIADO  
        $userId = $result['user_id'];

        //SE O USUARIO ENVIOU UMA FOTO, PROCESSA O UPLOAD·
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
            //
            $file = $_FILES['photo'];
            //CHAMA A FUNCAOO PATHINFO PARA PEGAR A EXTENSAO DO ARQUIVO
            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            //TIPOS DE EXTENSAO PERMITIDOS
            $allowed = ['jpg', 'jpeg', 'png', 'gif'];


            //PEGA A EXTENSAO DO ARQUIVO E VERIFICA SE ESTA DENTRO DO ALLOWED
            if (in_array(strtolower($ext), $allowed)) {
                // Instancia o cliente S3
        $s3Client = new S3Client([
            'version'     => 'latest',
            'region'      => $_ENV['AWS_DEFAULT_REGION'],
            'credentials' => [
                'key'    => $_ENV['AWS_ACCESS_KEY_ID'],
                'secret' => $_ENV['AWS_SECRET_ACCESS_KEY'],
            ]
        ]);

        $bucketName = $_ENV['S3_BUCKET_NAME'];
       
        // Gera nome único para a foto
        $newName = 'profile_photos/' . uniqid() . "." . $ext;

        try {
            // Faz upload para o S3
            $resultS3 = $s3Client->putObject([
                'Bucket'     => $bucketName,
                'Key'        => $newName,
                'SourceFile' => $file['tmp_name'],
                //'ACL'      => 'public-read' // opcional se quiser acesso público
            ]);

            // Monta a URL do S3
            $photoUrl = "https://{$bucketName}.s3.{$_ENV['AWS_DEFAULT_REGION']}.amazonaws.com/{$newName}";

            // Atualiza no banco a URL
            $pdo = Database::connect();
            $stmt = $pdo->prepare("UPDATE `User` SET profile_photo=? WHERE id=?");
            $stmt->execute([$photoUrl, $userId]);

            $result['photo_path'] = $photoUrl;
        } catch (S3Exception $e) {
            $result['photo_error'] = "Erro ao enviar para o S3: " . $e->getMessage();
        }
    } else {
        $result['photo_error'] = "Formato de arquivo não permitido";
    }
      //RETORNA OS DADOS EM JSON
        echo json_encode($result);
}
     
    }

    public function getAll()
    {
        $users = User::getAll();

        if (isset($users['error'])) {
            http_response_code(500);
            echo json_encode($users);
        } else {
            echo json_encode($users);
        }
    }

    public function forgotPassword()
    {
        $contentType = $_SERVER["CONTENT_TYPE"] ?? '';
        if (stripos($contentType, "application/json") !== false) {
            $data = json_decode(file_get_contents("php://input"), true);
            $email = trim($data['email'] ?? '');
        } else {
            $email = trim($_POST['email'] ?? '');
        }
        $pdo = Database::connect();
        $stmt = $pdo->prepare("SELECT id FROM `User` WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            $token = bin2hex(random_bytes(32));
            $stmt = $pdo->prepare("UPDATE `User` SET reset_token = ?, reset_token_expire = DATE_ADD(NOW(), INTERVAL 1 HOUR) WHERE id = ?");
            $stmt->execute([$token, $user['id']]);

            $resetLink = "http://localhost/TimerBook/public/index.php?action=reset_password&token=$token";
            $mail = new PHPMailer(true);

            try {
                $mail->isSMTP();
                $mail->Host = $_ENV['MAIL_HOST'];
                $mail->SMTPAuth = true;
                $mail->Username = $_ENV['MAIL_USERNAME'];;
                $mail->Password = $_ENV['MAIL_PASSWORD'];
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;
                $mail->setFrom($_ENV['MAIL_FROM'], $_ENV['MAIL_FROM_NAME']);
                $mail->addAddress($email);
                $mail->Subject = 'Redefinir Senha';
                $mail->Body = "Clique no link para redefinir sua senha: $resetLink";

                // Força debug do PHPMailer na resposta
                ob_start();
                $mail->SMTPDebug = 2;
                $mail->Debugoutput = function ($str, $level) {
                    echo "Debug: $str\n";
                };
                $enviado = $mail->send();
                $debugOutput = ob_get_clean();

                if ($enviado) {
                    echo json_encode(['success' => true, 'debug' => $debugOutput]);
                } else {
                    echo json_encode(['error' => $mail->ErrorInfo, 'debug' => $debugOutput]);
                }
            } catch (Exception $e) {
                echo json_encode(['error' => $e->getMessage(), 'mail_error' => $mail->ErrorInfo ?? '', 'debug' => $debugOutput ?? '']);
            }
        } else {
            echo json_encode(['error' => 'E-mail não encontrado.']);
        }
    }

    public function resetPassword()
    {
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

    public static function checkLogin() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
            header('Location: /TimerBook/public/index.php?action=login');
            exit;
        }
    }

    public static function logout() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION = [];
        session_destroy();
        header('Location: /TimerBook/public/index.php?action=login');
        exit;
    }
}

