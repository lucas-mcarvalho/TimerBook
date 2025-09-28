<?php
require_once __DIR__ . '/../models/Books.php';
require_once __DIR__ . '/../core/database_config.php';



class BookController
{
    // Criar um novo livro```php
public function create()
{
    $contentType = $_SERVER["CONTENT_TYPE"] ?? '';

    // Recebe os dados do POST ou JSON
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

    // Validações básicas
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

    // Upload do arquivo do livro
    $caminho_arquivo = null;
    if(isset($_FILES['caminho_arquivo']) && $_FILES['caminho_arquivo']['error'] === UPLOAD_ERR_OK){
        $uploadDir = "books/"; // pasta onde vai salvar
        if(!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

        $filename = basename($_FILES['caminho_arquivo']['name']);
        $targetPath = $uploadDir . $filename;

        if(move_uploaded_file($_FILES['caminho_arquivo']['tmp_name'], $targetPath)){
            $caminho_arquivo = $targetPath;
        }
    }

    // Cria o livro no banco
    $result = Book::create($titulo, $autor, $ano_publicacao, $user_id, $caminho_arquivo);

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
        echo json_encode($result);
    }
    // Listar livros de um usuário
// Listar livros de um usuário
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




    
}
