<?php

require_once '../App/controllers/UserController.php';
require_once '../App/controllers/BookController.php';
require_once '../App/controllers/AdminController.php';
require_once '../App/controllers/AuthenticationController.php';
require_once '../App/controllers/GoogleController.php';
require_once '../App/controllers/ReadingController.php';
require_once '../App/controllers/ReadingSessionController.php';
require_once '../App/controllers/RemindController.php';



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
// Se for uma chamada para api.php, usa parâmetros para determinar a ação

if (strpos($endpoint, '/api.php') !== false) {
    $action = $_GET['action'] ?? $_POST['action'] ?? '';
    if ($action) {

        $endpoint = "/$action";
    }

}
switch ("$method $endpoint") {
    case 'POST /register':
        $controller = new UserController();
        $controller->register();
        break;
    case 'GET /users':
       $controller = new UserController();
       $controller->getAll(); // Exemplo: lista usuários
        break;
     // Deletar usuário  DELETE /users/{id}
    case (preg_match('#^DELETE /users/(\d+)$#', "$method $endpoint", $matches) ? true : false):
        $controller = new UserController();
        $userId = $matches[1];
       $controller->delete($userId); 
        break;


    // Buscar usuário por ID → GET /users/{id}
     case (preg_match('#^GET /users/(\d+)$#', "$method $endpoint", $matches) ? true : false):
        $controller = new UserController();
        $userId = (int)$matches[1];
        $controller->getById($userId);
        break;

    // Atualizar usuário PUT /users/{id}
    case (preg_match('#^POST /users/(\d+)$#', "$method $endpoint", $matches) ? true : false):
        $controller = new UserController();
        $userId = (int)$matches[1];
        $controller->update($userId);
        break;
    //AUTHENTICATION
    case 'POST /login':
        $controller = new AuthenticationController();
        $controller->login();
        break;
    case 'POST /forgot-password':
        $controller = new AuthenticationController();
        $controller->forgotPassword();
        break;
    case 'POST /reset-password':
        $controller = new AuthenticationController();
        $controller->resetPassword();
        break;
    // ---------------- BOOK ROUTES ----------------

        //INSERCAO DE LIVROS NO BANCO DE DADOS

    case 'POST /books':
        $controller = new BookController();
        $controller->create();
        break;
    //BUSCAR LIVROS POR TITULO
    case 'GET /books/search':
    $controller = new BookController();
    $controller->findByTitle();
    break;
    //BUSCAR TODOS OS LIVROS DO BANCO DE DADOS·
    case 'GET /books':
        $controller = new BookController();
        if (isset($_GET['user_id'])) {
        $controller->getByUserFromQuery(); // método que retorna livros do usuário
        } else {
        $controller->getAll(); // retorna todos os livros
}
        break;
    //DELETAR O LIVRO PELO ID.
    case (preg_match('#^DELETE /books/(\d+)$#', "$method $endpoint", $matches) ? true : false):
    $bookId = $matches[1];
    $controller = new BookController();
    $controller->delete($bookId);
    break;
    //BUSCAR LIVROS DO USUARIO AUTENTICADO

    case 'GET /my-books':
    $controller = new BookController();
    $controller->getMyBooks();
    break;

    // Buscar livro por ID → GET /books/{id}
    case (preg_match('#^GET /books/(\d+)$#', "$method $endpoint", $matches) ? true : false):
    $bookId = (int)$matches[1];
    $controller = new BookController();
    $controller->findById($bookId);
    break;
    //UPDATE DO LIVRO
    case (preg_match('#^POST /books/(\d+)$#', "$method $endpoint", $matches) ? true : false):
    $bookId = (int)$matches[1];
    $controller = new BookController();
    $controller->update($bookId);
    break;

    //ADMIN 
    case 'POST /admin':
        $controller = new AdminController();
        $controller->login();
        break;
   case 'GET /admin':
        $controller = new AdminController();
        $controller->getAll();
        break;
    // GET /admin/{id}
    case (preg_match('#^GET /admin/(\d+)$#', "$method $endpoint", $matches) ? true : false):
        $id = (int)$matches[1];
        $controller = new AdminController();
        $controller->getById($id);
        break;
    
    // GET /books/user/{user_id}
    case (preg_match('#^GET /books/user/(\d+)$#', "$method $endpoint", $matches) ? true : false):
        $user_id = (int)$matches[1];
        $controller = new BookController();
        $controller->getByUser($user_id);
        break;

    case 'POST /admin/login':
        $controller = new AdminController();
        $controller->login();
        break;

    case 'POST /admin/register':
        $controller = new AdminController();
        $controller->register();
        break;
    case 'POST /admin/update':
        $controller = new AdminController();
        $controller->update();
        break;
    case 'POST /admin/delete':
        $controller = new AdminController();
        $controller->delete();
        break;
    // Sessão atual

    case 'GET /admin/me':
        $controller = new AdminController();
        $controller->me();
        break;

    case 'GET /admin/logout':
        $controller = new AdminController();
        $controller->logout();
        break;

      // ---------------- GOOGLE LOGIN ----------------
    case 'GET /google-login':
        $controller = new GoogleController();
        $controller->googleLogin();

        break;
    case 'GET /google-callback':
        $controller = new GoogleController();
        $controller->googleCallback();
        break;

   // ---------------- LEITURAS ----------------
    case 'POST /reading': // Criar nova leitura
        $controller = new ReadingController();
        $controller->create();
        break;

    case 'GET /reading': // Buscar todas as leituras
        $controller = new ReadingController();
        $controller->getAll();
        break;

    case (preg_match('#^GET /reading/(\d+)$#', "$method $endpoint", $matches) ? true : false):
        $controller = new ReadingController();
        $controller->getById((int)$matches[1]);
        break;

    case (preg_match('#^GET /reading/user/(\d+)$#', "$method $endpoint", $matches) ? true : false):
        $controller = new ReadingController();
        $controller->getByUser((int)$matches[1]);
        break;

    case (preg_match('#^PUT /reading/(\d+)$#', "$method $endpoint", $matches) ? true : false):
        $controller = new ReadingController();
        $controller->update((int)$matches[1]);
        break;

    case (preg_match('#^DELETE /reading/(\d+)$#', "$method $endpoint", $matches) ? true : false):
        $controller = new ReadingController();
        $controller->delete((int)$matches[1]);
        break;

    // Estatísticas gerais de leitura por usuário
    case (preg_match('#^GET /reading/statistics/(\d+)$#', "$method $endpoint", $matches) ? true : false):
        $controller = new ReadingController();
        $controller->getStatisticsByUserId((int)$matches[1]);
        break;

    // ---------------- SESSÕES DE LEITURA ----------------
    case 'POST /reading-session': // Criar sessão
        $controller = new ReadingSessionController();
        $controller->createSession();
        break;

    case 'GET /reading-session': // Todas as sessões
        $controller = new ReadingSessionController();
        $controller->getAllSessions();
        break;

    
    case (preg_match('#^PUT /reading-session/(\d+)$#', "$method $endpoint", $matches) ? true : false):
        $controller = new ReadingSessionController();
        $controller->updateSession((int)$matches[1]);
        break;

    case (preg_match('#^DELETE /reading-session/(\d+)$#', "$method $endpoint", $matches) ? true : false):
        $controller = new ReadingSessionController();
        $controller->deleteSession((int)$matches[1]);
        break;


        // Estatísticas gerais de leitura por usuário
case (preg_match('#^GET /reading/statistics/(\d+)$#', "$method $endpoint", $matches) ? true : false):
    $controller = new ReadingController();
    $controller->getStatisticsByUserId((int)$matches[1]);
    break;

// Média de páginas lidas por usuário
case (preg_match('#^GET /reading/average-pages/(\d+)$#', "$method $endpoint", $matches) ? true : false):
    $controller = new ReadingController();
    $controller->getAveragePagesByUser((int)$matches[1]);
    break;

// Tempo total de leitura por usuário
case (preg_match('#^GET /reading/time/(\d+)$#', "$method $endpoint", $matches) ? true : false):
    $controller = new ReadingController();
    $controller->getReadingTimeStats((int)$matches[1]);
    break;

// Iniciar leitura + sessão
case 'POST /reading/start':
    $controller = new ReadingController();
    $controller->iniciar();
    break;

// Finalizar sessão
case 'POST /reading/finish':
    $controller = new ReadingController();
    $controller->finalizar();
    break;

case (preg_match('#^POST /reading/finish-read$#', "$method $endpoint") ? true : false):
    $controller = new ReadingController();
    $controller->finalizarLeitura();
    break;

// Estatísticas do usuário logado (via sessão)
case (preg_match('#^GET /reading/totals/(\d+)$#', "$method $endpoint", $matches) ? true : false):
    $controller = new ReadingController();
    $controller->estatisticas((int)$matches[1]); // Passa o ID para a função
    break;


// Tempo total de leitura por usuário
case (preg_match('#^GET /reading/book/(\d+)$#', "$method $endpoint", $matches) ? true : false):
    $controller = new ReadingController();
    $controller->getBookReading((int)$matches[1]);
    break;


    // Sessões de leitura por livro
case (preg_match('#^GET /reading/book/(\d+)/sessions$#', "$method $endpoint", $matches) ? true : false):
    $controller = new ReadingSessionController();
    $controller->getSessionBook((int)$matches[1]);
    break;


// ---------------- LEMBRETES DE LEITURA ----------------
case 'GET /reminders/send':
    $controller = new ReminderController();
    $controller->sendReminders(); // 3 dias
    break;


default:
    http_response_code(404);
    echo json_encode(["error" => "Endpoint não encontrado"]);
    break; 
}
