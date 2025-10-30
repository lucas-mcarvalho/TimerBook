<?php
require_once __DIR__ . '/../core/database_config.php';
require_once __DIR__ . '/../models/ReadingSession.php';

class ReadingSessionController
{
    // Criar nova sessão
    public function createSession()
    {
        header('Content-Type: application/json');
        $input = json_decode(file_get_contents("php://input"), true);

        // Validação básica
        if (!$input || !isset($input['reading_id'])) {
            http_response_code(400);
            echo json_encode(["error" => "O campo 'reading_id' é obrigatório"]);
            return;
        }

        $reading_id   = $input['reading_id'];
        $data_inicio  = $input['data_inicio'] ?? null;
        $data_fim     = $input['data_fim'] ?? null;
        $tempo_sessao = $input['tempo_sessao'] ?? 0;
        $paginas_lidas = $input['paginas_lidas'] ?? null;

        // Cria a sessão de leitura
        $result = ReadingSession::create($reading_id, $data_inicio, $data_fim, $tempo_sessao, $paginas_lidas);

        // Resposta HTTP
        http_response_code(isset($result['error']) ? 500 : 201);
        echo json_encode($result);
    }

    // Buscar todas as sessões
    public function getAllSessions()
    {
        header('Content-Type: application/json');

        $result = ReadingSession::getAll();

        http_response_code(isset($result['error']) ? 500 : 200);
        echo json_encode($result);
    }

    // Atualizar sessão existente
    public function updateSession($id)
    {
        header('Content-Type: application/json');
        $input = json_decode(file_get_contents("php://input"), true);

        if (!$input) {
            http_response_code(400);
            echo json_encode(["error" => "Nenhum dado enviado para atualização"]);
            return;
        }

        $data_inicio  = $input['data_inicio'] ?? null;
        $data_fim     = $input['data_fim'] ?? null;
        $tempo_sessao = $input['tempo_sessao'] ?? null;
        $paginas_lidas = $input['paginas_lidas'] ?? null;

        $result = ReadingSession::update($id, $data_inicio, $data_fim, $tempo_sessao, $paginas_lidas);

        http_response_code(isset($result['error']) ? 500 : 200);
        echo json_encode($result);
    }

    // Deletar sessão
    public function deleteSession($id)
    {
        header('Content-Type: application/json');

        $result = ReadingSession::delete($id);

        http_response_code(isset($result['error']) ? 500 : 200);
        echo json_encode($result);
    }
}
?>
