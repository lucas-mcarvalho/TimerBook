<?php
require_once __DIR__ . '/../core/database_config.php';

class ReadingSession
{
    // Criar nova sessão de leitura
    public static function create($reading_id, $data_inicio = null, $data_fim = null ,$tempo_sessao = 0 ,$paginas_lidas = null)
    {
        try {
            $pdo = Database::connect();

            // Verifica se leitura existe
            $check = $pdo->prepare("SELECT id FROM Reading WHERE id = ?");
            $check->execute([$reading_id]);
            if ($check->rowCount() === 0) {
                return ["error" => "Leitura informada não existe."];
            }

            $data_inicio = $data_inicio ?? date('Y-m-d H:i:s');

            $stmt = $pdo->prepare("
                INSERT INTO SessaoLeitura (pk_leitura, data_inicio, data_fim, tempo_sessao, paginas_lidas)
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt->execute([$reading_id, $data_inicio, $data_fim, $tempo_sessao, $paginas_lidas]);

            return [
                "message" => "Sessão de leitura criada com sucesso",
                "session_id" => $pdo->lastInsertId()
            ];
        } catch (PDOException $e) {
            return ["error" => "Erro ao criar sessão de leitura: " . $e->getMessage()];
        }
    }

    // Buscar todas as sessões
    public static function getAll()
    {
        try {
            $pdo = Database::connect();
            $stmt = $pdo->query("
                SELECT s.*, r.id AS reading_id, r.pk_usuario
                FROM SessaoLeitura s
                JOIN Reading r ON s.pk_leitura = r.id
                ORDER BY s.data_inicio DESC
            ");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return ["error" => "Erro ao buscar sessões: " . $e->getMessage()];
        }
    }

    // Atualizar sessão de leitura
    public static function update($id, $data_inicio = null, $data_fim = null, $tempo_sessao = null, $paginas_lidas = null)
    {
        try {
            $pdo = Database::connect();

            $fields = [];
            $values = [];

            if ($data_inicio !== null) {
                $fields[] = "data_inicio = ?";
                $values[] = $data_inicio;
            }
            if ($data_fim !== null) {
                $fields[] = "data_fim = ?";
                $values[] = $data_fim;
            }
            if ($tempo_sessao !== null) {
                if (!is_numeric($tempo_sessao) || $tempo_sessao < 0) {
                    return ["error" => "O campo tempo_sessao deve ser um número positivo"];
                }
                $fields[] = "tempo_sessao = ?";
                $values[] = $tempo_sessao;
            }
            if ($paginas_lidas !== null) {
                if (!is_numeric($paginas_lidas) || $paginas_lidas < 0) {
                    return ["error" => "O campo paginas_lidas deve ser um número positivo"];
                }
                $fields[] = "paginas_lidas = ?";
                $values[] = $paginas_lidas;
            }
            if (empty($fields)) {
                return ["error" => "Nenhum campo informado para atualização"];
            }

            $values[] = $id;

            $sql = "UPDATE SessaoLeitura SET " . implode(", ", $fields) . " WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($values);

            if ($stmt->rowCount() === 0) {
                return ["error" => "Nenhum registro foi atualizado. Verifique o ID."];
            }

            return ["message" => "Sessão de leitura atualizada com sucesso"];
        } catch (PDOException $e) {
            return ["error" => "Erro ao atualizar sessão: " . $e->getMessage()];
        }
    }

    // Deletar sessão
    public static function delete($id)
    {
        try {
            $pdo = Database::connect();
            $stmt = $pdo->prepare("DELETE FROM SessaoLeitura WHERE id = ?");
            $stmt->execute([$id]);

            if ($stmt->rowCount() === 0) {
                return ["error" => "Sessão não encontrada para exclusão."];
            }

            return ["message" => "Sessão de leitura excluída com sucesso"];
        } catch (PDOException $e) {
            return ["error" => "Erro ao excluir sessão: " . $e->getMessage()];
        }
    }

    // Iniciar nova sessão
    public static function StartSession($leitura_id)
    {
        try {
            $pdo = Database::connect();

            // Verifica se leitura existe
            $check = $pdo->prepare("SELECT id FROM Reading WHERE id = ?");
            $check->execute([$leitura_id]);
            if ($check->rowCount() === 0) {
                return ["error" => "Leitura informada não existe."];
            }

            $stmt = $pdo->prepare("
                INSERT INTO SessaoLeitura (pk_leitura, data_inicio, tempo_sessao)
                VALUES (?, NOW(), 0)
            ");
            $stmt->execute([$leitura_id]);
            return $pdo->lastInsertId();

        } catch (PDOException $e) {
            return ["error" => "Erro em ReadingSession::StartSession: " . $e->getMessage()];
        }
    }

    public static function StopSession($sessao_id, $paginas_lidas)
    {
        try {
            $pdo = Database::connect();
            $pdo->beginTransaction();

            $check = $pdo->prepare("SELECT pk_leitura, data_inicio FROM SessaoLeitura WHERE id = ? FOR UPDATE");
            $check->execute([$sessao_id]);
            $sessao = $check->fetch(PDO::FETCH_ASSOC);

            if (!$sessao) {
                $pdo->rollBack();
                return ["error" => "Sessão de leitura não encontrada."];
            }

            $leitura_id = $sessao['pk_leitura'];

            $stmt = $pdo->prepare("
                UPDATE SessaoLeitura
                SET 
                    data_fim = NOW(),
                    tempo_sessao = GREATEST(TIMESTAMPDIFF(SECOND, data_inicio, NOW()), 0),
                    paginas_lidas = ?
                WHERE id = ?
            ");
            $stmt->execute([$paginas_lidas, $sessao_id]);

            if ($stmt->rowCount() === 0) {
                $pdo->rollBack();
                return ["error" => "Falha ao atualizar a sessão. Verifique o ID."];
            }

            $getTempo = $pdo->prepare("SELECT tempo_sessao FROM SessaoLeitura WHERE id = ?");
            $getTempo->execute([$sessao_id]);
            $tempoSessao = (int)$getTempo->fetchColumn();

            $updateReadingTempo = $pdo->prepare("
                UPDATE Reading
                SET tempo_gasto = COALESCE(tempo_gasto, 0) + ?
                WHERE id = ?
            ");
            $updateReadingTempo->execute([$tempoSessao, $leitura_id]);

            $updateReadingPaginas = $pdo->prepare("
                UPDATE Reading
                SET paginas_lidas = COALESCE(paginas_lidas, 0) + ?
                WHERE id = ?
            ");
            $updateReadingPaginas->execute([$paginas_lidas, $leitura_id]);

            $pdo->commit();

            $get = $pdo->prepare("
                SELECT s.*, r.paginas_lidas AS total_paginas_lidas, r.tempo_gasto AS total_tempo_gasto, r.status
                FROM SessaoLeitura s
                JOIN Reading r ON s.pk_leitura = r.id
                WHERE s.id = ?
            ");
            $get->execute([$sessao_id]);
            $dados = $get->fetch(PDO::FETCH_ASSOC);

            return ["message" => "Sessão encerrada com sucesso", "dados" => $dados];

        } catch (PDOException $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            return ["error" => "Erro em ReadingSession::StopSession: " . $e->getMessage()];
        }
    }

    public static function getAveragePagesByUser($user_id)
    {
        try {
            $pdo = Database::connect();
            $stmt = $pdo->prepare("
                SELECT ROUND(AVG(s.paginas_lidas), 2) AS media_paginas
                FROM SessaoLeitura s
                JOIN Reading l ON s.pk_leitura = l.id
                WHERE l.pk_usuario = ? AND s.paginas_lidas IS NOT NULL
            ");
            $stmt->execute([$user_id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return ["error" => "Erro ao calcular média de páginas por usuário: " . $e->getMessage()];
        }
    }

    public static function getReadingTimeStats($user_id = null)
    {
        try {
            $pdo = Database::connect();

            if ($user_id) {
                $stmt = $pdo->prepare("
                    SELECT
                        COALESCE(SUM(s.tempo_sessao), 0) AS tempo_total_segundos,
                        ROUND(COALESCE(AVG(s.tempo_sessao), 0), 2) AS tempo_medio_segundos
                    FROM SessaoLeitura s
                    JOIN Reading l ON s.pk_leitura = l.id
                    WHERE l.pk_usuario = ?
                ");
                $stmt->execute([$user_id]);
            } else {
                $stmt = $pdo->query("
                    SELECT
                        COALESCE(SUM(tempo_sessao), 0) AS tempo_total_segundos,
                        ROUND(COALESCE(AVG(tempo_sessao), 0), 2) AS tempo_medio_segundos
                    FROM SessaoLeitura
                ");
            }

            return $stmt->fetch(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            return ["error" => "Erro ao calcular tempo de leitura: " . $e->getMessage()];
        }
    }

    public static function getSessionBook($book_id)
    {
        try {
            $pdo = Database::connect();

            $stmt = $pdo->prepare("
                SELECT 
                    s.id AS sessao_id,
                    s.pk_leitura,
                    s.data_inicio,
                    s.data_fim,
                    s.tempo_sessao,
                    s.paginas_lidas,
                    r.pk_usuario,
                    r.status,
                    r.data_inicio AS leitura_inicio,
                    r.data_fim AS leitura_fim
                FROM SessaoLeitura s
                INNER JOIN Reading r ON s.pk_leitura = r.id
                WHERE r.livro = ?
                ORDER BY s.data_inicio DESC
            ");
            $stmt->execute([$book_id]);

            $sessoes = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $sessoes ?: [];

        } catch (PDOException $e) {
            return ["error" => "Erro em ReadingSession::getSessoesPorLivro: " . $e->getMessage()];
        }
    }



public static function getInactiveUsers($dias_inatividade = 3)
{
    try {
        $pdo = Database::connect();

        $sql = "
           SELECT 
    u.id AS user_id,
    u.nome,
    u.email,

    MAX(CASE WHEN s.data_fim IS NOT NULL THEN s.data_fim END) AS ultima_sessao,

    DATEDIFF(
        NOW(),
        MAX(CASE WHEN s.data_fim IS NOT NULL THEN s.data_fim END)
    ) AS dias_inativo

FROM User u
LEFT JOIN Reading r ON r.pk_usuario = u.id
LEFT JOIN SessaoLeitura s ON s.pk_leitura = r.id
GROUP BY u.id
HAVING dias_inativo >= :dias
    OR ultima_sessao IS NULL;
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':dias', $dias_inatividade, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);

    } catch (PDOException $e) {
        return ["error" => "Erro em ReadingSession::getInactiveUsers: " . $e->getMessage()];
    }
}
}
?>
