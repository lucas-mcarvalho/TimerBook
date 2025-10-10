<?php

require_once __DIR__ . '/../App/controllers/AuthenticationController.php';

// --- DEFINIÇÃO CENTRALIZADA DAS ROTAS DE VISUALIZAÇÃO ---
// 'nome-da-acao' => ['handler_type' => 'caminho/do/arquivo' ou [Controller::class, 'metodo'], 'protected' => true/false]
$viewRoutes = [
    'home' => [
        'type' => 'view',
        'handler' => '../App/views/html/home.php',
        'protected' => true
    ],
    'login' => [
        'type' => 'view',
        'handler' => '../App/views/html/login.php',
        'protected' => false
    ],
    'register' => [
        'type' => 'view',
        'handler' => '../App/views/html/cadastro.php',
        'protected' => false
    ],
    'forgot_password' => [
        'type' => 'view',
        'handler' => '../App/views/html/recuperar.php',
        'protected' => false
    ],
    'reset_password' => [
        'type' => 'view',
        'handler' => '../App/views/html/redefinir.php',
        'protected' => false
    ],
    'Adicionar_Livro' => [
        'type' => 'view',
        'handler' => '../App/views/html/adicionarLivro.php',
        'protected' => true // Assumindo que esta é uma área protegida
    ],
    'listar_livros' => [
        'type' => 'view',
        'handler' => '../App/views/html/listarLivro.php',
        'protected' => true // Assumindo que esta é uma área protegida
    ],
    'admin' => [
        'type' => 'view',
        'handler' => '../App/views/html/admin.php',
        'protected' => false // Assumindo que esta é uma área protegida
    ],
    'adm_editar' => [
        'type' => 'view',
        'handler' => '../App/views/html/admEditar.php',
        'protected' => false // Assumindo que esta é uma área protegida
    ],
    'sair' => [
        'type' => 'controller',
        'handler' => [AuthenticationController::class, 'logout'],
        'protected' => false // O logout em si não precisa de proteção prévia
    ],
];

// --- LÓGICA DO ROTEADOR DE VISUALIZAÇÃO ---

// Obtém a ação da URL, com 'login' como padrão se nenhuma for fornecida
$action = $_GET['action'] ?? 'login';

// Verifica se a ação solicitada existe no nosso array de rotas
if (isset($viewRoutes[$action])) {
    $route = $viewRoutes[$action];

    // Passo 1: Verificar se a rota é protegida
    if ($route['protected']) {
        // Se for, executa a verificação de login do controller
        AuthenticationController::checkLogin();
    }

    // Passo 2: Executar o manipulador (handler) da rota
    if ($route['type'] === 'view') {
        // Se for do tipo 'view', simplesmente inclui o arquivo PHP/HTML
        require_once __DIR__ . '/' . $route['handler'];
    } elseif ($route['type'] === 'controller') {
        // Se for do tipo 'controller', instancia a classe e chama o método
        $controllerClass = $route['handler'][0];
        $controllerMethod = $route['handler'][1];
        $controller = new $controllerClass();
        $controller->$controllerMethod();
    }

} else {
    // Se a ação não for encontrada no array, exibe uma mensagem de erro
    http_response_code(404);
    echo "Ação não reconhecida ou página não encontrada.";
}

?>
