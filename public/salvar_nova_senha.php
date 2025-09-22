<?php
require_once __DIR__ . '/../App/core/database_config.php';

date_default_timezone_set('America/Sao_Paulo');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['token'] ?? '';
    $novaSenha = $_POST['nova_senha'] ?? '';
    $confirmaSenha = $_POST['confirma_senha'] ?? '';

    if (!$token || !$novaSenha || !$confirmaSenha) {
        echo "Preencha todos os campos.";
        exit;
    }

    if ($novaSenha !== $confirmaSenha) {
        echo "As senhas não coincidem.";
        exit;
    }

    try {
        $pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt = $pdo->prepare("SELECT id, reset_token_expire FROM User WHERE reset_token = ?");
        $stmt->execute([$token]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            echo "Token inválido.";
            exit;
        }

        if (strtotime($user['reset_token_expire']) < time()) {
            echo "Token expirado. Solicite uma nova redefinição.";
            exit;
        }

        $hash = password_hash($novaSenha, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE User SET senha = ?, reset_token = NULL, reset_token_expire = NULL WHERE id = ?");
        $stmt->execute([$hash, $user['id']]);

        // Redireciona para a página de redefinição com mensagem de sucesso
        header("Location: /TimerBook/public/index.php?action=reset_password&success=1");
        exit;
    } catch (PDOException $e) {
        echo "Erro ao redefinir senha: " . $e->getMessage();
    }
} else {
    echo "Requisição inválida.";
}