<?php
$protected_actions = ['home'];
require_once __DIR__ . '/../App/controllers/UserController.php';
require_once __DIR__ . '/../App/controllers/AdminController.php';
require_once __DIR__ . '/../App/controllers/AuthenticationController.php';
require_once __DIR__ . '/../vendor/autoload.php';

$action = $_GET['action'] ?? 'login';

if (in_array($action, $protected_actions)) {
    AuthenticationController::checkLogin();
}

switch ($action) {
    case 'home':
        require_once __DIR__ . '/../App/views/html/home.php';
        break;
    case 'sair':
        AuthenticationController::logout();
        break;
    case 'register':
        require_once __DIR__ . '/../App/views/html/cadastro.php';
        break;
    case 'login':
        require_once __DIR__ . '/../App/views/html/login.php';
        break;
    case 'perfil_usuario':
        require_once __DIR__ . '/../App/views/html/perfilUsuario.php';
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
    case 'listar_livros':
          require_once __DIR__ . '/../App/views/html/listarLivro.php';
          break;

    case 'admin':
          require_once __DIR__ . '/../App/views/html/admin.php';
          break;

    case 'adm_editar':
        require_once __DIR__ . '/../App/views/html/admEditar.php';
        break;
   
    case 'editar_livro':
        require_once __DIR__ . '/../App/views/html/Editar_Livro.php';
        break;

    case 'adm_ver_livros':
        require_once __DIR__ . '/../App/views/html/ListarLivrosAdmin.php';
        break;
  
    case 'usuario_editar':
        require_once __DIR__ . '/../App/views/html/UsuarioEditarPerfil.php';
        break;

    case 'estatistica_livros':
        require_once __DIR__ . '/../App/views/html/estatisticaLivros.php';
        break;    
    default:
        echo "Ação não reconhecida.";
        break;
}
?>