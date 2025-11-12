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

        $reading_id     = $input['reading_id'];
        $data_inicio    = $input['data_inicio'] ?? null;
        $data_fim       = $input['data_fim'] ?? null;
        $tempo_sessao   = $input['tempo_sessao'] ?? 0;
        $paginas_lidas  = $input['paginas_lidas'] ?? null;

        try {
            $result = ReadingSession::create($reading_id, $data_inicio, $data_fim, $tempo_sessao, $paginas_lidas);

            if (isset($result['error'])) {
                http_response_code(500);
                echo json_encode($result);
                return;
            }

            http_response_code(201);
            echo json_encode([
                "message" => "Sessão criada com sucesso!",
                "data" => $result
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["error" => "Erro interno ao criar a sessão", "detalhe" => $e->getMessage()]);
        }
    }

    // Buscar todas as sessões
    public function getAllSessions()
    {
        header('Content-Type: application/json');

        try {
            $result = ReadingSession::getAll();

            if (isset($result['error'])) {
                http_response_code(500);
                echo json_encode($result);
                return;
            }

            http_response_code(200);
            echo json_encode($result);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["error" => "Erro ao buscar sessões", "detalhe" => $e->getMessage()]);
        }
    }

    // Atualizar sessão existente
    public function updateSession($id)
    {
        header('Content-Type: application/json');
        $input = json_decode(file_get_contents("php://input"), true);

        if (!$id || !is_numeric($id)) {
            http_response_code(400);
            echo json_encode(["error" => "ID inválido"]);
            return;
        }

        if (!$input) {
            http_response_code(400);
            echo json_encode(["error" => "Nenhum dado enviado para atualização"]);
            return;
        }

        $data_inicio    = $input['data_inicio'] ?? null;
        $data_fim       = $input['data_fim'] ?? null;
        $tempo_sessao   = $input['tempo_sessao'] ?? null;
        $paginas_lidas  = $input['paginas_lidas'] ?? null;

        try {
            $result = ReadingSession::update($id, $data_inicio, $data_fim, $tempo_sessao, $paginas_lidas);

            if (isset($result['error'])) {
                http_response_code(500);
                echo json_encode($result);
                return;
            }

            http_response_code(200);
            echo json_encode([
                "message" => "Sessão atualizada com sucesso!",
                "data" => $result
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["error" => "Erro interno ao atualizar sessão", "detalhe" => $e->getMessage()]);
        }
    }

    // Deletar sessão
    public function deleteSession($id)
    {
        header('Content-Type: application/json');

        if (!$id || !is_numeric($id)) {
            http_response_code(400);
            echo json_encode(["error" => "ID inválido"]);
            return;
        }

        try {
            $result = ReadingSession::delete($id);

            if (isset($result['error'])) {
                http_response_code(500);
                echo json_encode($result);
                return;
            }

            http_response_code(200);
            echo json_encode(["message" => "Sessão deletada com sucesso!"]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["error" => "Erro ao deletar sessão", "detalhe" => $e->getMessage()]);
        }
    }

    // Buscar sessões por livro
    public function getSessionBook($book_id)
    {
        header("Content-Type: application/json");

        if (!$book_id || !is_numeric($book_id)) {
            http_response_code(400);
            echo json_encode(["error" => "book_id é obrigatório e deve ser numérico"]);
            return;
        }

        try {
            $sessoes = ReadingSession::getSessionBook($book_id);

            if (isset($sessoes['error'])) {
                http_response_code(500);
                echo json_encode($sessoes);
                return;
            }

            http_response_code(200);
            echo json_encode($sessoes);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["error" => "Erro ao buscar sessões do livro", "detalhe" => $e->getMessage()]);
        }
    }
}
?>
