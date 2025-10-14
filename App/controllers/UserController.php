<?php
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../core/database_config.php';
require_once __DIR__ . '/../../vendor/autoload.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;


class UserController
{
    public function register()
    {
        // DETECTA O TIPO DE DADO QUE RECEBEU
        $contentType = $_SERVER["CONTENT_TYPE"] ?? '';

        //PEGA OS DADOS CONFORME O TIPO DE DADO ENVIADO, JSON OU FORM DATA
        if (stripos($contentType, "application/json") !== false) {
            $data = json_decode(file_get_contents("php://input"), true);
            $nome = $data['nome'] ?? null;
            $username = $data['username'] ?? null;
            $email = $data['email'] ?? null;
            $password = $data['password'] ?? null;
        } else {
            $nome = $_POST['nome'] ?? null;
            $username = $_POST['username'] ?? null;
            $email = $_POST['email'] ?? null;
            $password = $_POST['password'] ?? null;
        }
        if (!$email || !$password || !$username) {
            http_response_code(400);
            echo json_encode(["error" => "E-mail, senha e username são obrigatórios"]);
            return;
        }

        // Cria usuário chamando a funcao da model create.
        $result = User::create($email, $password, $nome, $username);

        //SE O USUARIO NAO FOI CRIADO, RETORNA O ERRO
        if (isset($result['error'])) {
            echo json_encode($result);
            return;
        }
//PEGA O ID DO USUARIO CRIADO   
        $userId = $result['user_id'];

        //SE O USUARIO ENVIOU UMA FOTO, PROCESSA O UPLOAD·
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
            //
            $file = $_FILES['photo'];
            //CHAMA A FUNCAOO PATHINFO PARA PEGAR A EXTENSAO DO ARQUIVO
            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            //TIPOS DE EXTENSAO PERMITIDOS
            $allowed = ['jpg', 'jpeg', 'png', 'gif'];


            //PEGA A EXTENSAO DO ARQUIVO E VERIFICA SE ESTA DENTRO DO ALLOWED
            if (in_array(strtolower($ext), $allowed)) {
                // Instancia o cliente S3
        $s3Client = new S3Client([
            'version'     => 'latest',
            'region'      => $_ENV['AWS_DEFAULT_REGION'],
            'credentials' => [
                'key'    => $_ENV['AWS_ACCESS_KEY_ID'],
                'secret' => $_ENV['AWS_SECRET_ACCESS_KEY'],
            ]
        ]);

        $bucketName = $_ENV['S3_BUCKET_NAME'];
        
        // Gera nome único para a foto
        $newName = 'profile_photos/' . uniqid() . "." . $ext;

        try {
            // Faz upload para o S3
            $resultS3 = $s3Client->putObject([
                'Bucket'     => $bucketName,
                'Key'        => $newName,
                'SourceFile' => $file['tmp_name'],
                //'ACL'      => 'public-read' // opcional se quiser acesso público
            ]);

            // Monta a URL do S3
            $photoUrl = "https://{$bucketName}.s3.{$_ENV['AWS_DEFAULT_REGION']}.amazonaws.com/{$newName}";

            // Atualiza no banco a URL
            $pdo = Database::connect();
            $stmt = $pdo->prepare("UPDATE `User` SET profile_photo=? WHERE id=?");
            $stmt->execute([$photoUrl, $userId]);

            $result['photo_path'] = $photoUrl;
        } catch (S3Exception $e) {
            $result['photo_error'] = "Erro ao enviar para o S3: " . $e->getMessage();
        }
    } else {
        $result['photo_error'] = "Formato de arquivo não permitido";
    }
      //RETORNA OS DADOS EM JSON
        echo json_encode($result);
}
      
    }

    public function getAll()
    {
        $users = User::getAll();

        if (isset($users['error'])) {
            http_response_code(500);
            echo json_encode($users);
        } else {
            echo json_encode($users);
        }
    }

     public function getById($id)
    {
        $user = User::getById($id);
        if (!$user) {
            http_response_code(404);
            echo json_encode(["error" => "Usuário não encontrado"]);
        } else {
            echo json_encode($user);
        }
    }

    public function findWithBooks($id)
    {
        $result = User::findWithBooks($id);
        if (!$result) {
            http_response_code(404);
            echo json_encode(["error" => "Usuário não encontrado ou sem livros"]);
        } else {
            echo json_encode($result);
        }
}
  public function update($id)
{
    header("Content-Type: application/json");

    $contentType = $_SERVER["CONTENT_TYPE"] ?? '';

    if (stripos($contentType, "application/json") !== false) {
        $data = json_decode(file_get_contents("php://input"), true);
        $nome = $data['nome'] ?? null;
        $username = $data['username'] ?? null;
        $email = $data['email'] ?? null;
        $senha = $data['senha'] ?? null;
    } else {
        $nome = $_POST['nome'] ?? null;
        $username = $_POST['username'] ?? null;
        $email = $_POST['email'] ?? null;
        $senha = $_POST['senha'] ?? null;
    }

    $profilePhoto = null;

    // Upload da nova foto (se enviada)
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['photo'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($ext, $allowed)) {
            try {
                $s3Client = new S3Client([
                    'version' => 'latest',
                    'region' => $_ENV['AWS_DEFAULT_REGION'],
                    'credentials' => [
                        'key' => $_ENV['AWS_ACCESS_KEY_ID'],
                        'secret' => $_ENV['AWS_SECRET_ACCESS_KEY'],
                    ]
                ]);

                $bucketName = $_ENV['S3_BUCKET_NAME'];
                $newName = 'profile_photos/' . uniqid() . "." . $ext;

                $s3Client->putObject([
                    'Bucket' => $bucketName,
                    'Key' => $newName,
                    'SourceFile' => $file['tmp_name'],
                    'ACL' => 'public-read' // opcional, garante acesso público
                ]);

                $profilePhoto = "https://{$bucketName}.s3.{$_ENV['AWS_DEFAULT_REGION']}.amazonaws.com/{$newName}";

            } catch (S3Exception $e) {
                echo json_encode(["error" => "Erro ao enviar imagem: " . $e->getMessage()]);
                return;
            }
        } else {
            echo json_encode(["error" => "Formato de arquivo inválido"]);
            return;
        }
    }

    // Chama o model (que já lida com a exclusão da antiga)
    $result = User::update($id, $nome, $username, $email, $senha, false, $profilePhoto);

    echo json_encode($result);
}


       public function delete($id)
    {
        $result = User::delete($id);
        echo json_encode($result);
    }

}