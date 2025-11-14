<?php

// 1. INCLUDES
require_once '../App/controllers/UserController.php';
require_once '../App/controllers/BookController.php';
require_once '../App/controllers/AdminController.php';
require_once '../App/controllers/AuthenticationController.php';
require_once '../App/controllers/GoogleController.php';
require_once '../App/controllers/ReadingController.php';
require_once '../App/controllers/ReadingSessionController.php';
require_once '../App/controllers/RemindController.php';

// Inclui nossa nova classe de Roteador
require_once '../App/core/RouterClass.php';

// 2. HEADERS (Mesma lógica sua)
header('Content-Type: application/json; charset=utf-8');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

// 3. PREPARAÇÃO DA URI (Mesma lógica sua)
$method = $_SERVER['REQUEST_METHOD'];
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$endpoint = str_replace('/TimerBook/public', '', $uri);

if (strpos($endpoint, '/api.php') !== false) {
    $action = $_GET['action'] ?? $_POST['action'] ?? '';
    if ($action) {
        $endpoint = "/$action";
    }
}

// 4. DEFINIÇÃO DAS ROTAS (Aqui está a mágica!)
$router = new Router();

// --- User Routes ---
$router->add('POST', '/register', [UserController::class, 'register']);
$router->add('GET', '/users', [UserController::class, 'getAll']);
$router->add('DELETE', '/users/{id}', [UserController::class, 'delete']);
$router->add('GET', '/users/{id}', [UserController::class, 'getById']);
$router->add('POST', '/users/{id}', [UserController::class, 'update']); // Nota: Isso parece um update, considere usar 'PUT'

// --- Authentication ---
$router->add('POST', '/login', [AuthenticationController::class, 'login']);
$router->add('POST', '/forgot-password', [AuthenticationController::class, 'forgotPassword']);
$router->add('POST', '/reset-password', [AuthenticationController::class, 'resetPassword']);

// --- Book Routes ---
$router->add('POST', '/books', [BookController::class, 'create']);
$router->add('GET', '/books/search', [BookController::class, 'findByTitle']);
$router->add('GET', '/my-books', [BookController::class, 'getMyBooks']);
$router->add('DELETE', '/books/{id}', [BookController::class, 'delete']);
$router->add('GET', '/books/{id}', [BookController::class, 'findById']);
$router->add('POST', '/books/{id}', [BookController::class, 'update']);
$router->add('GET', '/books/user/{user_id}', [BookController::class, 'getByUser']);

// Rota especial GET /books que tinha lógica condicional
$router->add('GET', '/books', function() {
    $controller = new BookController();
    if (isset($_GET['user_id'])) {
        $controller->getByUserFromQuery();
    } else {
        $controller->getAll();
    }
});

// --- Admin Routes ---
$router->add('POST', '/admin', [AdminController::class, 'login']); // Você tinha duplicado, mantive a primeira
$router->add('GET', '/admin', [AdminController::class, 'getAll']);
$router->add('GET', '/admin/{id}', [AdminController::class, 'getById']);
$router->add('POST', '/admin/login', [AdminController::class, 'login']);
$router->add('POST', '/admin/register', [AdminController::class, 'register']);
$router->add('POST', '/admin/update', [AdminController::class, 'update']);
$router->add('POST', '/admin/delete', [AdminController::class, 'delete']);
$router->add('GET', '/admin/me', [AdminController::class, 'me']);
$router->add('GET', '/admin/logout', [AdminController::class, 'logout']);

// --- Google Login ---
$router->add('GET', '/google-login', [GoogleController::class, 'googleLogin']);
$router->add('GET', '/google-callback', [GoogleController::class, 'googleCallback']);

// --- Reading Routes ---
$router->add('POST', '/reading', [ReadingController::class, 'create']);
$router->add('GET', '/reading', [ReadingController::class, 'getAll']);
$router->add('GET', '/reading/{id}', [ReadingController::class, 'getById']);
$router->add('GET', '/reading/user/{id}', [ReadingController::class, 'getByUser']);
$router->add('PUT', '/reading/{id}', [ReadingController::class, 'update']);
$router->add('DELETE', '/reading/{id}', [ReadingController::class, 'delete']);
$router->add('GET', '/reading/statistics/{id}', [ReadingController::class, 'getStatisticsByUserId']);
$router->add('GET', '/reading/average-pages/{id}', [ReadingController::class, 'getAveragePagesByUser']);
$router->add('GET', '/reading/time/{id}', [ReadingController::class, 'getReadingTimeStats']);
$router->add('POST', '/reading/start', [ReadingController::class, 'iniciar']);
$router->add('POST', '/reading/finish', [ReadingController::class, 'finalizar']);
$router->add('POST', '/reading/finish-read', [ReadingController::class, 'finalizarLeitura']);
$router->add('GET', '/reading/totals/{id}', [ReadingController::class, 'estatisticas']);
$router->add('GET', '/reading/book/{id}', [ReadingController::class, 'getBookReading']);

// --- Reading Session ---
$router->add('POST', '/reading-session', [ReadingSessionController::class, 'createSession']);
$router->add('GET', '/reading-session', [ReadingSessionController::class, 'getAllSessions']);
$router->add('PUT', '/reading-session/{id}', [ReadingSessionController::class, 'updateSession']);
$router->add('DELETE', '/reading-session/{id}', [ReadingSessionController::class, 'deleteSession']);
$router->add('GET', '/reading/book/{id}/sessions', [ReadingSessionController::class, 'getSessionBook']);

// --- Reminders ---
$router->add('GET', '/reminders/send', [ReminderController::class, 'sendReminders']); // O nome do controller estava errado no seu 'require'

// 5. EXECUÇÃO DA ROTA (Substitui todo o switch)
$router->dispatch($method, $endpoint);