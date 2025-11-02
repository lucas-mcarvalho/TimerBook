<?php
require_once __DIR__ . '/../core/database_config.php';

// O comentário "//Consertar aqui" foi resolvido.
class ReadingSession
{
    // Criar nova sessão de leitura
    public static function create($reading_id, $data_inicio = null, $data_fim = null ,$tempo_sessao = 0 ,$paginas_lidas = null)
    {
        try {
            $pdo = Database::connect();
            $data_inicio = $data_inicio ?? date('Y-m-d H:i:s');

            $stmt = $pdo->prepare("
                INSERT INTO SessaoLeitura (pk_leitura, data_inicio, data_fim, tempo_sessao,paginas_lidas)
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt->execute([$reading_id, $data_inicio, $data_fim, $tempo_sessao,$paginas_lidas]);

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
                $fields[] = "tempo_sessao = ?";
                $values[] = $tempo_sessao;
            }
            if ($paginas_lidas !== null) {
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
            return ["message" => "Sessão de leitura excluída com sucesso"];
        } catch (PDOException $e) {
            return ["error" => "Erro ao excluir sessão: " . $e->getMessage()];
        }
    }

  // --- CORREÇÃO ABAIXO ---

  /**
   * Inicia uma NOVA sessão de leitura (cronômetro).
   * Ela deve receber o ID da leitura (Reading) principal.
   */
  public static function StartSession($leitura_id)
  {
      try {
          $pdo = Database::connect();
          $stmt = $pdo->prepare("
              INSERT INTO SessaoLeitura (pk_leitura, data_inicio, tempo_sessao)
              VALUES (?, NOW(), 0)
          ");
          $stmt->execute([$leitura_id]);
          return $pdo->lastInsertId(); // Retorna o ID da *nova sessão*
      
      } catch (PDOException $e) {
           return ["error" => "Erro em ReadingSession::StartSession: " . $e->getMessage()];
      }
  }

  /**
   * Para uma sessão de leitura (cronômetro).
   * Ela deve receber o ID da sessão que está parando e as páginas lidas.
   */
  public static function StopSession($sessao_id, $paginas_lidas)
  {
      try {
           $pdo = Database::connect();
           $stmt = $pdo->prepare("
               UPDATE SessaoLeitura
               SET data_fim = NOW(),
                   tempo_sessao = TIMESTAMPDIFF(SECOND, data_inicio, NOW()),
                   paginas_lidas = ?
               WHERE id = ?
           ");
           $stmt->execute([$paginas_lidas, $sessao_id]);
           return true; // Sucesso
      
       } catch (PDOException $e) {
           return ["error" => "Erro em ReadingSession::StopSession: " . $e->getMessage()];
       }
  }

  // --- FIM DA CORREÇÃO ---


    // Média de páginas por usuário
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
                        SUM(s.tempo_sessao) AS tempo_total_segundos,
                        ROUND(AVG(s.tempo_sessao), 2) AS tempo_medio_segundos
                    FROM SessaoLeitura s
                    JOIN Reading l ON s.pk_leitura = l.id
                    WHERE l.pk_usuario = ?
                ");
                $stmt->execute([$user_id]);
            } else {
                $stmt = $pdo->query("
                    SELECT
                        SUM(tempo_sessao) AS tempo_total_segundos,
                        ROUND(AVG(tempo_sessao), 2) AS tempo_medio_segundos
                    FROM SessaoLeitura
                ");
            }

        
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return $result ? $result : [
                "tempo_total_segundos" => 0,
                "tempo_medio_segundos" => 0
            ];
            // --- FIM DA SIMPLIFICAÇÃO ---

        } catch (PDOException $e) {
            return ["error" => "Erro ao calcular tempo de leitura: " . $e->getMessage()];
        }
    }

    private static function formatSeconds($seconds)
    {
        if (!$seconds) return "00:00:00";
        $seconds = (int)$seconds;
        if ($seconds < 0) $seconds = 0;

        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $seconds = $seconds % 60;
        return sprintf("%02d:%02d:%02d", $hours, $minutes, $seconds);
    }




public static function getSessionBook($book_id) {
    try {
        $pdo = Database::connect();

        // Busca todas as sessões ligadas a leituras do livro
        $stmt = $pdo->prepare("
            SELECT 
                s.id AS sessao_id,
                s.pk_leitura,
                s.data_inicio,
                s.data_fim,
                s.tempo_sessao,
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
        return $sessoes ?: []; // retorna vazio se não houver sessões

    } catch (PDOException $e) {
        return ["error" => "Erro em ReadingSession::getSessoesPorLivro: " . $e->getMessage()];
    }
}

  }
?>
