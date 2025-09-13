<?php
require_once __DIR__ . '/../App/controllers/UserController.php';

header('Content-Type: application/json; charset=utf-8');

// Permitir chamadas do front-end (CORS)
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

$method = $_SERVER['REQUEST_METHOD'];
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Ajuste conforme a pasta onde está hospedado
$base = "/PHP/TimerBook/public";
$endpoint = str_replace($base, "", $uri);

$controller = new UserController();

switch ("$method $endpoint") {
    case "POST /register":
        $controller->register();
        break;

    case "GET /users":
        $controller->getAll();
        break;

    default:
        http_response_code(404);
        echo json_encode(["error" => "Endpoint não encontrado"]);
        break;
}
 