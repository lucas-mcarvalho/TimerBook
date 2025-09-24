<?php
require_once '../App/controllers/UserController.php';


header('Content-Type: application/json; charset=utf-8');

$method = $_SERVER['REQUEST_METHOD'];
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);


// Libera CORS para permitir acesso do front-end
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Responde requisições OPTIONS automaticamente (necessário para navegadores)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

// Remove o prefixo da pasta

$endpoint = str_replace('/TimerBook/public', '', $uri);


switch ("$method $endpoint") {
    case 'POST /register':
        $controller = new UserController();
        $controller->register();
        break;

    case 'POST /login':
        $controller = new UserController();
        $controller->login();
        break;

    case 'GET /users':
        $controller = new UserController();
       $controller->getAll(); // Exemplo: lista usuários
        break;

    case 'POST /forgot-password':
        $controller = new UserController();
        $controller->forgotPassword();
        break;

    case 'POST /reset-password':
        $controller = new UserController();
        $controller->resetPassword();
        break;
    
    default:
        http_response_code(404);
        echo json_encode(["error" => "Endpoint não encontrado"]);
        break;
}

