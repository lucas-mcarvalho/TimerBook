<?php

require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../core/ApiHelper.php';

require __DIR__ . '/../../vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;

use PHPMailer\PHPMailer\Exception;


class AuthenticationController {
         public function login()
    {
        // Gera dados de request (JSON ou form-data)
        $data = ApiHelper::getRequestData();
        $email = $data['email'] ?? null;
        $password = $data['password'] ?? null;

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
            
            // centraliza configuração de sessão
            ApiHelper::setUserSession($user);
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

            $resetLink = "/?action=reset_password&token=$token";
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
        // Detecta o tipo de conteúdo recebido
        $contentType = $_SERVER["CONTENT_TYPE"] ?? '';
        
        // Pega os dados conforme o tipo enviado (JSON ou form data)
        if (stripos($contentType, "application/json") !== false) {
            $data = json_decode(file_get_contents("php://input"), true);
            $token = $data['token'] ?? '';
            $novaSenha = $data['nova_senha'] ?? '';
            $confirmaSenha = $data['confirma_senha'] ?? '';
        } else {
            $token = $_POST['token'] ?? '';
            $novaSenha = $_POST['nova_senha'] ?? '';
            $confirmaSenha = $_POST['confirma_senha'] ?? '';
        }

        // Validação dos campos obrigatórios
        if (!$token || !$novaSenha || !$confirmaSenha) {
            http_response_code(400);
            echo json_encode(["error" => "Preencha todos os campos."]);
            return;
        }

        // Verifica se as senhas coincidem
        if ($novaSenha !== $confirmaSenha) {
            http_response_code(400);
            echo json_encode(["error" => "As senhas não coincidem."]);
            return;
        }

        try {
            $pdo = Database::connect();

            // Busca o usuário pelo token e verifica no banco se o token ainda é válido (evita problemas de fuso/hora no PHP)
            $stmt = $pdo->prepare("SELECT id FROM `User` WHERE reset_token = ? AND reset_token_expire > NOW()");
            $stmt->execute([$token]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user) {
                // Se não encontrou com validade, verificar se o token existe (então é expirado) ou é inválido
                $stmt2 = $pdo->prepare("SELECT id FROM `User` WHERE reset_token = ?");
                $stmt2->execute([$token]);
                $exists = $stmt2->fetch(PDO::FETCH_ASSOC);

                if ($exists) {
                    http_response_code(400);
                    echo json_encode(["error" => "Token expirado. Solicite uma nova redefinição."]); 
                    return;
                } else {
                    http_response_code(400);
                    echo json_encode(["error" => "Token inválido."]); 
                    return;
                }
            }

            // Atualiza a senha e limpa o token
            $hash = password_hash($novaSenha, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE `User` SET senha = ?, reset_token = NULL, reset_token_expire = NULL WHERE id = ?");
            $stmt->execute([$hash, $user['id']]);

            // Se a requisição foi JSON (API), retorna JSON. Caso contrário (form HTML), redireciona para a página de redefinir que já mostra a mensagem de sucesso.
            if (stripos($contentType, "application/json") !== false) {
                echo json_encode(["success" => true, "message" => "Senha redefinida com sucesso!"]);
            } else {
                header('Location: /?action=reset_password&success=1');
                exit;
            }

        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["error" => "Erro ao redefinir senha: " . $e->getMessage()]);
        }
    }

    public static function checkLogin() {
        ApiHelper::startSessionIfNeeded();
        if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
            header('Location: /?action=login');
            exit;
        }
    }

    public static function logout() {
        ApiHelper::startSessionIfNeeded();
        $_SESSION = [];
        session_destroy();
        header('Location: /?action=login');
        exit;
    }
}
