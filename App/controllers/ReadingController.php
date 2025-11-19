<?php
require_once __DIR__ . '/../models/Reading.php';
require_once __DIR__ . '/../core/database_config.php';
require_once __DIR__ . '/../models/ReadingSession.php';
class ReadingController
{
    // Criar uma nova Reading (POST)
    public function create()
    {
        header("Content-Type: application/json");
        $contentType = $_SERVER["CONTENT_TYPE"] ?? '';

        if (stripos($contentType, "application/json") !== false) {
            $data = json_decode(file_get_contents("php://input"), true);
        } else {
            $data = $_POST;
        }

        $user_id = $data['user_id'] ?? null;
        $book_id = $data['book_id'] ?? null;
        $tempo_gasto = $data['tempo_gasto'] ?? 0;
        $paginas_lidas = $data['paginas_lidas'] ?? 0;
        $status = $data['status'] ?? 'Em andamento';
        $data_inicio = $data['data_inicio'] ?? null;
        $data_fim = $data['data_fim'] ?? null;

        if (!$user_id || !$book_id) {
            http_response_code(400);
            echo json_encode(["error" => "user_id e book_id são obrigatórios"]);
            return;
        }

        $result = Reading::create($user_id, $book_id, $status, $tempo_gasto, $paginas_lidas, $data_inicio, $data_fim);
        echo json_encode($result);
    }

    // Retornar todas as Readings
    public function getAll()
    {
        header("Content-Type: application/json");
        $readings = Reading::getAll();
        echo json_encode($readings);
    }

    // Buscar Reading por ID
    public function getById($id)
    {
        header("Content-Type: application/json");
        $reading = Reading::getById($id);
        if ($reading) {
            echo json_encode($reading);
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Reading não encontrada"]);
        }
    }

    // Atualizar Reading (PUT)
    public function update($id)
    {
        header("Content-Type: application/json");
        $contentType = $_SERVER["CONTENT_TYPE"] ?? '';

        if (stripos($contentType, "application/json") !== false) {
            $data = json_decode(file_get_contents("php://input"), true);
        } else {
            $data = $_POST;
        }

        $status = $data['status'] ?? null;
        $tempo_gasto = $data['tempo_gasto'] ?? null;
        $paginas_lidas = $data['paginas_lidas'] ?? null;
        $data_fim = $data['data_fim'] ?? null;

        $result = Reading::update($id, $status, $tempo_gasto, $paginas_lidas, $data_fim);
        echo json_encode($result);
    }

    // Deletar Reading (DELETE)
    public function delete($id)
    {
        header("Content-Type: application/json");
        $result = Reading::delete($id);
        echo json_encode($result);
    }

    // Buscar Readings de um usuário específico
    public function getStatisticsByUserId($user_id)
    {
        header("Content-Type: application/json");

        if (!$user_id) {
            http_response_code(400);
            echo json_encode(["error" => "user_id é obrigatório"]);
            return;
        }

        $statistics = Reading::getBookStatisticsWithDetails($user_id);

        if (isset($statistics['error'])) {
            http_response_code(500);
            echo json_encode($statistics);
            return;
        }

        if (!empty($statistics)) {
            echo json_encode($statistics);
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Nenhuma estatística encontrada para este usuário"]);
        }
    }

    // Buscar Readings de um usuário específico
    public function getByUser($user_id)
    {
        header("Content-Type: application/json");

        if (!$user_id) {
            http_response_code(400);
            echo json_encode(["error" => "user_id é obrigatório"]);
            return;
        }

        $readings = Reading::getByUser($user_id);

        if (!empty($readings)) {
            echo json_encode($readings);
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Nenhuma Reading encontrada para este usuário"]);
        }
    }

 public function iniciar() {
        $data = json_decode(file_get_contents("php://input"), true);
        

        $user_id = $data['user_id'] ?? null;
        $book_id = $data['book_id'] ?? null;

        if (!$user_id || !$book_id) {
            http_response_code(400); // Bad Request
            echo json_encode(["error" => "user_id e book_id são obrigatórios"]);
            return;
        }

        $leitura_id = Reading::iniciarLeitura($user_id, $book_id);
        $sessao_id = ReadingSession::StartSession($leitura_id);

        echo json_encode([
            "leitura_id" => $leitura_id,
            "sessao_id" => $sessao_id,
            "status" => "sessão iniciada"
        ]);
    }

 public function finalizar() {
    header("Content-Type: application/json");
    $data = json_decode(file_get_contents("php://input"), true);

    if (!$data || !isset($data['sessao_id']) || !isset($data['leitura_id']) || !isset($data['paginas_lidas'])) {
        http_response_code(400);
        echo json_encode([
            "error" => "Payload JSON inválido. Campos 'sessao_id', 'leitura_id' e 'paginas_lidas' são obrigatórios."
        ]);
        return;
    }

    // Encerra apenas a sessão, sem alterar status da leitura
    $resultStop = ReadingSession::StopSession($data['sessao_id'], $data['paginas_lidas']);

    if (is_array($resultStop) && isset($resultStop['error'])) {
        http_response_code(500);
        echo json_encode($resultStop);
        return;
    }

    echo json_encode([
        "status" => "Sessão finalizada com sucesso"
    ]);
}


    public function estatisticas($user_id) {
    

        header("Content-Type: application/json");
        if (!$user_id) {
             http_response_code(400);
             echo json_encode(["error" => "ID do usuário é obrigatório"]);
             return;
        }

     
        $stats = Reading::estatisticasUsuario($user_id);

       
        if (is_array($stats) && isset($stats['error'])) {
             http_response_code(500);
             echo json_encode($stats);
             return;
        }
        
      
        header("Content-Type: application/json");
        echo json_encode($stats);
    }

    public function getAveragePagesByUser($user_id)
{
    header('Content-Type: application/json');

    if (!$user_id) {
        http_response_code(400);
        echo json_encode(["error" => "user_id é obrigatório"]);
        return;
    }

    $result = ReadingSession::getAveragePagesByUser($user_id);

    http_response_code(isset($result['error']) ? 500 : 200);
    echo json_encode($result);
}



public function getReadingTimeStats($user_id = null)
{
    header('Content-Type: application/json');

    // Se vier via rota /sessions/time/{user_id}
    if (isset($_GET['user_id'])) {
        $user_id = $_GET['user_id'];
    }

    $result = ReadingSession::getReadingTimeStats($user_id);

    http_response_code(isset($result['error']) ? 500 : 200);
    echo json_encode($result);
}

public function getBookReading($book_id) {
    header("Content-Type: application/json");

    if (!$book_id) {
        http_response_code(400);
        echo json_encode(["error" => "book_id é obrigatório"]);
        return;
    }

    $leituras = Reading::getReadinBook($book_id);

    if (isset($leituras['error'])) {
        http_response_code(500);
        echo json_encode($leituras);
        return;
    }

    echo json_encode($leituras);

}



public function getSessionBook($book_id) {
    header("Content-Type: application/json");

    if (!$book_id) {
        http_response_code(400);
        echo json_encode(["error" => "book_id é obrigatório"]);
        return;
    }

    $sessoes = ReadingSession::getSessionBook($book_id);

    if (isset($sessoes['error'])) {
        http_response_code(500);
        echo json_encode($sessoes);
        return;
    }

    echo json_encode($sessoes);
}

    public function finalizarLeitura() {
        header("Content-Type: application/json");

        $data = json_decode(file_get_contents("php://input"), true);
        $leitura_id = $data['leitura_id'] ?? null;

        if (!$leitura_id) {
            http_response_code(400);
            echo json_encode(["error" => "leitura_id é obrigatório"]);
            return;
        }

        $result = Reading::finalizarLeitura($leitura_id);

        if (isset($result['error'])) {
            http_response_code(500);
            echo json_encode($result);
            return;
        }

        echo json_encode([
            "mensagem" => "Leitura finalizada com sucesso",
            "resultado" => $result
        ]);
    }

}