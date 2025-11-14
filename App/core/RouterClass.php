<?php
// Salve isto como App/Router.php

class Router
{
    private $routes = [];

    /**
     * Adiciona uma nova rota à coleção de rotas.
     *
     * @param string $method O método HTTP (GET, POST, PUT, DELETE)
     * @param string $pattern O padrão da URI (ex: /users/{id})
     * @param mixed $handler O 'manipulador' (ex: [UserController::class, 'getById'] ou uma função)
     */
    public function add($method, $pattern, $handler)
    {
        $this->routes[] = [
            'method' => $method,
            'pattern' => $pattern,
            'handler' => $handler
        ];
    }

    /**
     * Encontra a rota correspondente e a executa.
     *
     * @param string $method O método HTTP da requisição atual
     * @param string $uri A URI da requisição atual
     */
    public function dispatch($method, $uri)
    {
        foreach ($this->routes as $route) {
            // 1. Verifica o método HTTP
            if ($route['method'] !== $method) {
                continue;
            }

            // 2. Converte o padrão amigável (ex: /users/{id}) em Regex
            // Isso substitui {id} por (\d+)
            $regex = '#^' . preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(\d+)', $route['pattern']) . '$#';

            // 3. Verifica se a URI bate com o Regex
            if (preg_match($regex, $uri, $matches)) {
                
                // Remove o primeiro item do $matches (que é a string inteira)
                array_shift($matches); 
                
                // Os parâmetros capturados (ex: o 'id')
                $params = $matches; 

                // 4. Executa o Manipulador
                $handler = $route['handler'];

                // Se o manipulador for uma Função anônima (Closure)
                if (is_callable($handler)) {
                    call_user_func_array($handler, $params);
                    return;
                }

                // Se o manipulador for um array [Classe, 'metodo']
                if (is_array($handler)) {
                    $controllerClass = $handler[0];
                    $controllerMethod = $handler[1];
                    
                    $controller = new $controllerClass();
                    call_user_func_array([$controller, $controllerMethod], $params);
                    return;
                }
            }
        }

        // 5. Nenhuma rota encontrada
        http_response_code(404);
        echo json_encode(["error" => "Endpoint não encontrado"]);
    }
}