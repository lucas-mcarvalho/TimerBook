<?php
require_once '../App/controllers/UserController.php';
require_once '../App/controllers/BookController.php';


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

    default:
        http_response_code(404);
        echo json_encode(["error" => "Endpoint não encontrado"]);
        break;
}

