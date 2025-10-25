<?php
require_once __DIR__ . '/../models/Reading.php';
require_once __DIR__ . '/../core/database_config.php';

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
}
