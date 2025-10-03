<?php

$protected_actions = ['home'];

require_once __DIR__ . '/../App/controllers/UserController.php';
require_once __DIR__ . '/../App/controllers/AdminController.php';
require_once __DIR__ . '/../App/models/Admin.php';
require_once __DIR__ . '/../vendor/autoload.php';

use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;


$action = $_GET['action'] ?? 'login';


if (in_array($action, $protected_actions)) {
    UserController::checkLogin();
}

switch ($action) {
    case 'home':
        require_once __DIR__ . '/../App/views/html/home.php';
        break;
    case 'sair':
        UserController::logout();
        break;
    case 'register':
        require_once __DIR__ . '/../App/views/html/cadastro.php';
        break;
    case 'login':
        require_once __DIR__ . '/../App/views/html/login.php';
        break;
    case 'forgot_password':
        require_once __DIR__ . '/../App/views/html/recuperar.php';
        break;
    case 'reset_password':
        require_once __DIR__ . '/../App/views/html/redefinir.php';
        break;
    case 'Adicionar_Livro':
        require_once __DIR__ . '/../App/views/html/adicionarLivro.php';
        break;
    case 'admin':
        AdminController::checkLogin();
        require_once __DIR__ . '/../App/views/html/admin.php';
        break;
    case 'adm_editar':
        AdminController::checkLogin();
        require_once __DIR__ . '/../App/views/html/admEditar.php';
        break;
    case 'adm_salvar':
        AdminController::checkLogin();
       
        // Processa upload da foto para S3
        $profilePhoto = null;
        if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['profile_photo'];
            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $allowed = ['jpg', 'jpeg', 'png', 'gif'];

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
                    ]);

                    // Monta a URL do S3
                    $profilePhoto = "https://{$bucketName}.s3.{$_ENV['AWS_DEFAULT_REGION']}.amazonaws.com/{$newName}";
                } catch (S3Exception $e) {
                    error_log("Erro ao fazer upload para S3: " . $e->getMessage());
                    // Continua sem foto se der erro no upload
                }
            }
        }
       
        // Cria ou atualiza usuário via form
        $id = $_POST['id'] ?? null;
        $nome = $_POST['nome'] ?? null;
        $email = $_POST['email'] ?? null;
        $username = $_POST['username'] ?? null;
        $senha = $_POST['senha'] ?? null;

        if ($id && $nome && $email && $username) {
            // Atualizar usuário existente
            User::update($id, $nome, $username, $email, $senha, false, $profilePhoto);
        } elseif (!$id && $nome && $email && $username && $senha) {
            // Criar novo usuário
            User::create($email, $senha, $nome, $username, $profilePhoto);
        }
       
        header('Location: index.php?action=admin');
        exit;
    case 'adm_excluir':
        AdminController::checkLogin();
        $id = $_GET['id'] ?? null;
        if ($id) {
            User::delete($id);
        }
        header('Location: index.php?action=admin');
        exit;
    case 'admin_sair':
        AdminController::logout();
        break;
    case 'admin_login':
        // Opcional: página de login de admin pode reutilizar login padrão por enquanto
        require_once __DIR__ . '/../App/views/html/login.php';
        break;
    case 'listar_livros':
          require_once __DIR__ . '/../App/views/html/listarLivro.php';
          break;
    default:
        echo "Ação não reconhecida.";
        break;
}
