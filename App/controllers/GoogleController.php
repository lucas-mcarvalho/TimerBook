<?php

use Google\Client as GoogleClient;
use Google\Service\Oauth2;

class GoogleController
{
 
 public function googleLogin()
    {
        $client = new GoogleClient();
        $client->setClientId($_ENV['GOOGLE_CLIENT_ID']);
        $client->setClientSecret($_ENV['GOOGLE_CLIENT_SECRET']);
        $client->setRedirectUri('http://localhost/TimerBook/public/google-callback');
        $client->addScope('email');
        $client->addScope('profile');

        // Redireciona o usuário para o login do Google
        $authUrl = $client->createAuthUrl();
        header('Location: ' . filter_var($authUrl, FILTER_SANITIZE_URL));
        exit;
    }

    public function googleCallback()
    {
        $client = new GoogleClient();
        $client->setClientId($_ENV['GOOGLE_CLIENT_ID']);
        $client->setClientSecret($_ENV['GOOGLE_CLIENT_SECRET']);
        $client->setRedirectUri('http://localhost/TimerBook/public/google-callback');

        if (!isset($_GET['code'])) {
            echo json_encode(['error' => 'Código de autenticação ausente']);
            return;
        }

        // Troca o "code" pelo token
        $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);

        if (isset($token['error'])) {
            echo json_encode(['error' => $token['error_description']]);
            return;
        }

        $client->setAccessToken($token['access_token']);
        $google_service = new Oauth2($client);
        $google_user = $google_service->userinfo->get();

        // Dados do Google
        $email = $google_user->email;
        $nome = $google_user->name;
        $profilePhoto = $google_user->picture;
        $username = explode('@', $email)[0]; // simples username padrão

        // Verifica se o usuário já existe
        $user = User::findByEmail($email);
        if (!$user) {
            // Cria usuário automaticamente
            $result = User::create($email, bin2hex(random_bytes(8)), $nome, $username, $profilePhoto);
            if (isset($result['error'])) {
                echo json_encode($result);
                return;
            }
            $user = User::getById($result['user_id']);
        }

        // Inicia sessão
        session_start();
        $_SESSION['user_logged_in'] = true;
        $_SESSION['user'] = $user;
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['nome'] = $user['nome'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['username'] = $user['username'];
            if (!empty($user['profile_photo'])) {
               $_SESSION['profile_photo'] = ltrim($user['profile_photo'], '/');
          } else {
                   $_SESSION['profile_photo'] = null;
               }

        header('Location: /TimerBook/public/index.php?action=home');
        exit;
    }
}