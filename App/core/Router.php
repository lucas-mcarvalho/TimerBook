<?php
// Inclui todos os controllers necessários no início
require_once '../App/controllers/UserController.php';
require_once '../App/controllers/BookController.php';
require_once '../App/controllers/AdminController.php';
require_once '../App/controllers/AuthenticationController.php';
require_once '../App/controllers/GoogleController.php';

// --- CONFIGURAÇÃO INICIAL ---
header('Content-Type: application/json; charset=utf-8');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Responde requisições OPTIONS (pre-flight) automaticamente
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

// Obtém o método e a URI da requisição
$method = $_SERVER['REQUEST_METHOD'];
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$endpoint = str_replace('/TimerBook/public', '', $uri);

// --- DEFINIÇÃO CENTRALIZADA DAS ROTAS ---
// Formato: 'METODO /padrão/da/rota' => [ClasseDoController::class, 'metodoDoController']
$routes = [
    // --- Rotas de Usuário ---
    'POST /register' => [UserController::class, 'register'],
    'GET /users' => [UserController::class, 'getAll'],
    'GET /users/(\d+)' => [UserController::class, 'getById'],
    'GET /users/(\d+)/books' => [UserController::class, 'findWithBooks'],
    'PUT /users/(\d+)' => [UserController::class, 'update'],
    'DELETE /users/(\d+)' => [UserController::class, 'delete'],

    // --- Rotas de Autenticação ---
    'POST /login' => [AuthenticationController::class, 'login'],
    'POST /forgot-password' => [AuthenticationController::class, 'forgotPassword'],
    'POST /reset-password' => [AuthenticationController::class, 'resetPassword'],

    // --- Rotas de Livros (Books) ---
    'POST /books' => [BookController::class, 'create'],
    'GET /books/search' => [BookController::class, 'findByTitle'],
    'GET /my-books' => [BookController::class, 'getMyBooks'],
    'DELETE /books/(\d+)' => [BookController::class, 'delete'],
    
    // Rota GET /books com lógica condicional
    'GET /books' => function() {
        $controller = new BookController();
        if (isset($_GET['user_id'])) {
            $controller->getByUserFromQuery();
        } else {
            $controller->getAll();
        }
    },

    // --- Rotas de Admin ---
    'GET /admin' => [AdminController::class, 'getAll'],
    'GET /admin/me' => [AdminController::class, 'me'],
    'GET /admin/(\d+)' => [AdminController::class, 'getById'],
    'POST /admin/login' => [AdminController::class, 'login'],
    'POST /admin/register' => [AdminController::class, 'register'],
    'POST /admin/update' => [AdminController::class, 'update'], // Note: estas rotas de admin parecem não ter ID
    'POST /admin/delete' => [AdminController::class, 'delete'],

    // --- Rotas de Login com Google ---
    'GET /google-login' => [GoogleController::class, 'googleLogin'],
    'GET /google-callback' => [GoogleController::class, 'googleCallback'],
];


// --- LÓGICA DO ROTEADOR ---
$routeFound = false;
foreach ($routes as $pattern => $handler) {
    // Separa o método HTTP do padrão da URI
    list($routeMethod, $routePattern) = explode(' ', $pattern, 2);

    // Cria a expressão regular final para a verificação
    $regex = '#^' . $routePattern . '$#';

    // Verifica se o método e o padrão da rota correspondem à requisição atual
    if ($method === $routeMethod && preg_match($regex, $endpoint, $matches)) {
        $routeFound = true;
        
        // Remove o primeiro elemento de $matches, que é a string completa correspondente
        array_shift($matches);

        // Se o handler for uma função anônima (closure), apenas a executa
        if (is_callable($handler)) {
            $handler();
        } else {
            // Se for um array [controller, method]
            $controllerClass = $handler[0];
            $controllerMethod = $handler[1];

            // Instancia o controller
            $controller = new $controllerClass();

            // Chama o método do controller, passando os parâmetros capturados (como o ID)
            call_user_func_array([$controller, $controllerMethod], $matches);
        }
        
        // Interrompe o loop pois a rota foi encontrada
        break; 
    }
}

// Se nenhuma rota foi encontrada após o loop, retorna erro 404
if (!$routeFound) {
    http_response_code(404);
    echo json_encode(["error" => "Endpoint não encontrado"]);
}
