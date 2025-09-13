<?php
require_once '../App/controllers/UserController.php';

header('Content-Type: application/json; charset=utf-8');

$method = $_SERVER['REQUEST_METHOD'];
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Remova o prefixo da pasta, se necessário, ex: /public/home.php
$endpoint = str_replace('/PHP/TimerBook/public', '', $uri);

switch ("$method $endpoint") {
    case 'POST /register':
        $controller = new UserController();
        $controller->register();
        break;

    case 'POST /login':
        $controller = new UserController();
     #   $controller->login();
        break;

    case 'GET /users':
        $controller = new UserController();
    #   $controller->index(); // Exemplo: lista usuários
        break;

    default:
        http_response_code(404);
        echo json_encode(["error" => "Endpoint não encontrado"]);
        break;
}

