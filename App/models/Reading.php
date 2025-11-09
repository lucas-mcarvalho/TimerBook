<?php
require_once __DIR__ . '/../core/database_config.php';

class Reading
{
    //  Cria um novo registro de Reading
    public static function create($user_id, $book_id, $status = 'Em andamento', $tempo_gasto = 0, $paginas_lidas = 0, $data_inicio = null, $data_fim = null)
    {
        try {
            $pdo = Database::connect();

            $data_inicio = $data_inicio ?? date('Y-m-d H:i:s');

            $stmt = $pdo->prepare("
                INSERT INTO Reading (pk_usuario, livro, status, tempo_gasto, paginas_lidas, data_inicio, data_fim)
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([$user_id, $book_id, $status, $tempo_gasto, $paginas_lidas, $data_inicio, $data_fim]);

            return [
                "message" => "Reading criada com sucesso",
                "reading_id" => $pdo->lastInsertId()
            ];
        } catch (PDOException $e) {
            return ["error" => "Erro ao criar Reading: " . $e->getMessage()];
        }
    }

    // Retorna todas as Readings
    public static function getAll()
    {
        try {
            $pdo = Database::connect();
            $stmt = $pdo->query("
                SELECT r.*, u.nome AS usuario_nome, b.titulo AS livro_titulo
                FROM Reading r
                JOIN User u ON r.pk_usuario = u.id
                JOIN Books b ON r.livro = b.id
            ");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return ["error" => "Erro ao buscar Readings: " . $e->getMessage()];
        }
    }

    //  Busca Reading por ID
    public static function getById($id)
    {
        try {
            $pdo = Database::connect();
            $stmt = $pdo->prepare("
                SELECT r.*, u.nome AS usuario_nome, b.titulo AS livro_titulo
                FROM Reading r
                JOIN User u ON r.pk_usuario = u.id
                JOIN Books b ON r.livro = b.id
                WHERE r.id = ?
            ");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return ["error" => "Erro ao buscar Reading: " . $e->getMessage()];
        }
    }

    //  Atualiza o progresso de uma Reading
    public static function update($id, $status = null, $tempo_gasto = null, $paginas_lidas = null, $data_fim = null)
    {
        try {
            $pdo = Database::connect();

            $fields = [];
            $values = [];

            if ($status !== null) {
                $fields[] = "status = ?";
                $values[] = $status;
            }
            if ($tempo_gasto !== null) {
                $fields[] = "tempo_gasto = ?";
                $values[] = $tempo_gasto;
            }
            if ($paginas_lidas !== null) {
                $fields[] = "paginas_lidas = ?";
                $values[] = $paginas_lidas;
            }
            if ($data_fim !== null) {
                $fields[] = "data_fim = ?";
                $values[] = $data_fim;
            }

            if (empty($fields)) {
                return ["error" => "Nenhum campo informado para atualização"];
            }

            $values[] = $id;

            $sql = "UPDATE Reading SET " . implode(", ", $fields) . " WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($values);

            return ["message" => "Reading atualizada com sucesso"];
        } catch (PDOException $e) {
            return ["error" => "Erro ao atualizar Reading: " . $e->getMessage()];
        }
    }

    // Deleta uma Reading
    public static function delete($id)
    {
        try {
            $pdo = Database::connect();
            $stmt = $pdo->prepare("DELETE FROM Reading WHERE id = ?");
            $stmt->execute([$id]);
            return ["message" => "Reading excluída com sucesso"];
        } catch (PDOException $e) {
            return ["error" => "Erro ao excluir Reading: " . $e->getMessage()];
        }
    }

    //  Busca os livros do usuário com as estatísticas de leitura
    public static function getBookStatisticsWithDetails($user_id)
    {
        try {
            $pdo = Database::connect();
            // A consulta começa na tabela Books (b) e usa LEFT JOIN para incluir dados da Reading (r)
            // A condição de JOIN é feita pelo ID do livro (b.id) e pelo campo 'livro' da tabela Reading (r.livro)
            // A filtragem pelo usuário deve ser feita na tabela Books (b.user_id)
            $stmt = $pdo->prepare("
                SELECT
                    b.titulo,
                    b.autor,
                    b.ano_publicacao,
                    b.capa_livro,
                    COALESCE(r.status, 'Não iniciado') AS status,
                    COALESCE(r.tempo_gasto, 0) AS tempo_gasto,
                    COALESCE(r.paginas_lidas, 0) AS paginas_lidas,
                    r.data_inicio,
                    r.data_fim
                FROM Books b
                LEFT JOIN Reading r ON b.id = r.livro AND r.pk_usuario = b.user_id
                WHERE b.user_id = ?
            ");
            $stmt->execute([$user_id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return ["error" => "Erro ao buscar estatísticas de livros: " . $e->getMessage()];
        }
    }

    //  Busca todas as Readings de um usuário específico
    public static function getByUser($user_id)
    {
        try {
            $pdo = Database::connect();
            $stmt = $pdo->prepare("
                SELECT r.*, b.titulo AS livro_titulo, b.autor AS livro_autor
                FROM Reading r
                JOIN Books b ON r.livro = b.id
                WHERE r.pk_usuario = ?
            ");
            $stmt->execute([$user_id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return ["error" => "Erro ao buscar Readings do usuário: " . $e->getMessage()];
        }
    }

     public static function iniciarLeitura($user_id, $book_id) {
        $pdo = Database::connect();

        // Verifica se já existe leitura para este livro e usuário
        $stmt = $pdo->prepare("SELECT id, status FROM Reading WHERE pk_usuario = ? AND livro = ?");
        $stmt->execute([$user_id, $book_id]);
        $leitura = $stmt->fetch();

        if ($leitura) {
            // Se a leitura estiver finalizada, reabrir
            if ($leitura['status'] === 'Finalizada') {
                $stmt = $pdo->prepare("
                    UPDATE Reading
                    SET `status` = 'em andamento', data_fim = NULL
                    WHERE id = ?
                ");
                $stmt->execute([$leitura['id']]);
            }

            return $leitura['id']; 
        }

        // Cria nova leitura
        $stmt = $pdo->prepare("
            INSERT INTO Reading (pk_usuario, livro, `status`, data_inicio) 
            VALUES (?, ?, 'em andamento', NOW())
        ");
        $stmt->execute([$user_id, $book_id]);
        return $pdo->lastInsertId();
    }

    
   public static function finalizarLeitura($leitura_id) {
        try { 
            $pdo = Database::connect();

            // 1. Soma todos os tempos das sessões e páginas
            $stmt = $pdo->prepare("
                SELECT SUM(tempo_sessao) AS tempo_total, SUM(paginas_lidas) AS paginas_total
                FROM SessaoLeitura WHERE pk_leitura = ?
            "); 
            $stmt->execute([$leitura_id]);
            $resumo = $stmt->fetch();
            
           
            $tempo_total = $resumo['tempo_total'] ?? 0;
            $paginas_total = $resumo['paginas_total'] ?? 0;

          
            $stmt = $pdo->prepare("
                UPDATE Reading 
                SET tempo_gasto = ?, 
                    paginas_lidas = ?, 
                    data_fim = NOW(), 
                    status = 'Finalizada'
                WHERE id = ?
            ");
            
            $stmt->execute([$tempo_total, $paginas_total, $leitura_id]);

            
            return $stmt->rowCount(); 
        
        } catch (PDOException $e) {
            return ["error" => "Erro em Reading::finalizarLeitura: " . $e->getMessage()];
        }
    }

  public static function estatisticasUsuario($user_id) {
        try {
            $pdo = Database::connect();

            $stmt = $pdo->prepare("
                SELECT 
                    COUNT(*) AS total_livros,
                    SUM(tempo_gasto) AS tempo_total,
                    SUM(paginas_lidas) AS paginas_total,
                    ROUND(AVG(paginas_lidas), 2) AS media_paginas_por_livro
                FROM Reading 
                WHERE pk_usuario = ?
            ");
            $stmt->execute([$user_id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            return ["error" => "Erro ao buscar estatísticas do usuário: " . $e->getMessage()];
        }
    }


    //RETORNA A LEITURA ASSOCIADA AO LIVRO DO USUARIO
public static function getReadinBook($book_id) {
    try {
        $pdo = Database::connect();

        $stmt = $pdo->prepare("
            SELECT id, pk_usuario, livro, status, data_inicio, data_fim
            FROM Reading
            WHERE livro = ?
            ORDER BY data_inicio DESC
        ");
        $stmt->execute([$book_id]);

        $leituras = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $leituras ?: []; // retorna lista vazia se não houver leituras

    } catch (PDOException $e) {
        return ["error" => "Erro em Reading::getLeiturasPorLivro: " . $e->getMessage()];
    }
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