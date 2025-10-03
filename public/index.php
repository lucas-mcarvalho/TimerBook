<?php

$protected_actions = ['home'];

require_once __DIR__ . '/../App/controllers/UserController.php';
require_once __DIR__ . '/../App/controllers/AdminController.php';
require_once __DIR__ . '/../App/models/Admin.php';


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
        
        // Processa upload da foto
        $profilePhoto = null;
        if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/uploads/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            
            $extension = pathinfo($_FILES['profile_photo']['name'], PATHINFO_EXTENSION);
            $fileName = uniqid() . '.' . $extension;
            $filePath = $uploadDir . $fileName;
            
            if (move_uploaded_file($_FILES['profile_photo']['tmp_name'], $filePath)) {
                $profilePhoto = $fileName;
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