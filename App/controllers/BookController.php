<?php
require_once __DIR__ . '/../models/Books.php';
require_once __DIR__ . '/../core/database_config.php';

require __DIR__ . '/../../vendor/autoload.php';


use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;

// Carrega variáveis de ambiente do .env
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../..');
$dotenv->load();

class BookController // Supondo que isso está dentro de uma classe
{
    public function create()
    {
        $contentType = $_SERVER["CONTENT_TYPE"] ?? '';

        // Recebe os dados do POST ou JSON (esta parte permanece a mesma)
        if (stripos($contentType, "application/json") !== false) {
            $data = json_decode(file_get_contents("php://input"), true);
            $titulo = $data['titulo'] ?? null;
            $autor = $data['autor'] ?? null;
            $ano_publicacao = $data['ano_publicacao'] ?? null;
            $user_id = $data['user_id'] ?? null;
        } else {
            $titulo = $_POST['titulo'] ?? null;
            $autor = $_POST['autor'] ?? null;
            $ano_publicacao = $_POST['ano_publicacao'] ?? null;
            $user_id = $_POST['user_id'] ?? null;
        }

        // Validações básicas (permanecem as mesmas)
        if (!$titulo) {
            http_response_code(400);
            echo json_encode(["error" => "Título é obrigatório"]);
            return;
        }

        if (!$user_id) {
            http_response_code(400);
            echo json_encode(["error" => "Usuário é obrigatório"]);
            return;
        }
        
        $caminho_arquivo_s3 = null; // Variável que guardará a URL do S3

        // --- INÍCIO DA LÓGICA DE UPLOAD PARA O S3 ---
        if (isset($_FILES['caminho_arquivo']) && $_FILES['caminho_arquivo']['error'] === UPLOAD_ERR_OK) {
            
            // 1. Instanciar o cliente S3
            $s3Client = new S3Client([
                'version'     => 'latest',
                'region'      => $_ENV['AWS_DEFAULT_REGION'],
                'credentials' => [
                    'key'    => $_ENV['AWS_ACCESS_KEY_ID'],
                    'secret' => $_ENV['AWS_SECRET_ACCESS_KEY'],
                ]
            ]);

            $bucketName = $_ENV['S3_BUCKET_NAME'];
            
            // 2. Preparar os dados para o upload
            $fileTmpPath = $_FILES['caminho_arquivo']['tmp_name'];
            $fileName = basename($_FILES['caminho_arquivo']['name']);
            
            // Gera um nome de arquivo único para evitar sobreposições
            $fileKey = 'books/' . uniqid() . '-' . $fileName;

            // 3. Fazer o upload do arquivo
          try {
    $resultS3 = $s3Client->putObject([
        'Bucket'     => $bucketName,
        'Key'        => $fileKey, 
        'SourceFile' => $fileTmpPath,
        // 'ACL'        => 'public-read',  <-- LINHA REMOVIDA OU COMENTADA
    ]);

    // 4. Obter a URL do arquivo no S3 para salvar no banco
    // A URL continua sendo a mesma
    $caminho_arquivo_s3 = "https://{$bucketName}.s3.{$_ENV['AWS_DEFAULT_REGION']}.amazonaws.com/{$fileKey}";

} catch (S3Exception $e) {
                http_response_code(500);
                echo json_encode(["error" => "Falha ao fazer upload para o S3: " . $e->getMessage()]);
                return;
            }
        }



         // --- UPLOAD DA CAPA ---
        if (isset($_FILES['capa_arquivo']) && $_FILES['capa_arquivo']['error'] === UPLOAD_ERR_OK) {
            $ext = pathinfo($_FILES['capa_arquivo']['name'], PATHINFO_EXTENSION);
            $allowedImages = ['jpg', 'jpeg', 'png', 'gif'];

            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $_FILES['capa_arquivo']['tmp_name']);
            finfo_close($finfo);

            if (in_array(strtolower($ext), $allowedImages) && str_starts_with($mimeType, 'image/')) {
                $fileTmpPath = $_FILES['capa_arquivo']['tmp_name'];
                $fileName = basename($_FILES['capa_arquivo']['name']);
                $fileKey = 'books/covers/' . uniqid() . '-' . $fileName;

                try {
                    $s3Client->putObject([
                        'Bucket'     => $bucketName,
                        'Key'        => $fileKey,
                        'SourceFile' => $fileTmpPath,
                    ]);
                    $capa_arquivo_s3 = "https://{$bucketName}.s3.{$_ENV['AWS_DEFAULT_REGION']}.amazonaws.com/{$fileKey}";
                } catch (S3Exception $e) {
                    http_response_code(500);
                    echo json_encode(["error" => "Falha no upload da capa: " . $e->getMessage()]);
                    return;
                }
            } else {
                http_response_code(400);
                echo json_encode(["error" => "Formato de capa inválido. Permitido: JPG, JPEG, PNG, GIF."]);
                return;
            }
        }
        // --- FIM DA LÓGICA DE UPLOAD PARA O S3 ---

        // Cria o livro no banco, agora salvando a URL do S3
        $result = Book::create($titulo, $autor, $ano_publicacao, $user_id, $caminho_arquivo_s3,$capa_arquivo_s3);

        echo json_encode($result);
    }


    // Buscar livro por ID
    public function findById($id)
    {
        $result = Book::findById($id);

        if ($result) {
            echo json_encode($result);
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Livro não encontrado"]);
        }
    }


    // Listar todos os livros
    public function getAll()
    {
        $result = Book::getAll();
        echo json_encode($result);
    }

    // Listar livros de um usuário
  

    // Deletar livro
   public function delete($id)
{
    $result = Book::delete($id);

    if (isset($result['error'])) {
        http_response_code(400); // ou 500, dependendo do erro
    } else {
        http_response_code(200);
    }

    echo json_encode($result);
}


    
  
// Listar livros de um usuário , Postman
public function getByUser($user_id)
{
    if (!$user_id) {
        http_response_code(400);
        echo json_encode(["error" => "Usuário é obrigatório"]);
        return;
    }

    $result = Book::getByUser($user_id);

    echo json_encode($result);
}

  // Listar livros de um usuário  pegando id da sessao
public function getByUserFromQuery()
{
    $user_id = $_GET['user_id'] ?? null;

    if (!$user_id) {
        http_response_code(400);
        echo json_encode(["error" => "Informe o ID do usuário"]);
        return;
    }

    $result = Book::getByUser($user_id);

    echo json_encode($result);
}

// Listar livros do usuário autenticado

public function getMyBooks()
{
    session_start();
    if (!isset($_SESSION['user_id'])) {
        http_response_code(401);
        echo json_encode(["error" => "Usuário não autenticado"]);
        return;
    }

    $user_id = $_SESSION['user_id'];
    $result = Book::getByUser($user_id);

    echo json_encode($result);
}

    // Buscar livro por título
public function findByTitle()
{
    $titulo = $_GET['titulo'] ?? '';
    if (!$titulo) {
        http_response_code(400);
        echo json_encode(["error" => "Informe um título para buscar"]);
        return;
    }

    $result = Book::findByTitle($titulo);
    echo json_encode($result);
}


}
