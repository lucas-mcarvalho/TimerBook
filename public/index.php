<?php

$action = $_GET['action'] ?? 'login';

switch ($action) {
    case 'home':
        require_once __DIR__ . '/../App/views/html/home.php';
        break;

    case 'sair':
        require_once __DIR__ . '/../App/views/html/login.php';
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
    default:
        echo "Ação não reconhecida.";
        break;
}