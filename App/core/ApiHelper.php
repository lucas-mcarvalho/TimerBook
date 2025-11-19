<?php
// Helper central para APIs: parsing de request, respostas JSON e sessão
class ApiHelper {
    public static function getRequestData(): array {
        $contentType = $_SERVER["CONTENT_TYPE"] ?? '';
        if (stripos($contentType, "application/json") !== false) {
            $data = json_decode(file_get_contents("php://input"), true);
            return is_array($data) ? $data : [];
        }
        return $_POST ?: [];
    }

    public static function json($data, int $status = 200) {
        if (!headers_sent()) {
            header('Content-Type: application/json');
        }
        http_response_code($status);
        echo json_encode($data);
    }

    public static function startSessionIfNeeded() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function setUserSession(array $user) {
        self::startSessionIfNeeded();
        $_SESSION['user_logged_in'] = true;
        $_SESSION['user'] = $user;
        $_SESSION['user_id'] = $user['id'] ?? null;
        $_SESSION['username'] = $user['username'] ?? null;
        $_SESSION['nome'] = $user['nome'] ?? null;
        $_SESSION['email'] = $user['email'] ?? null;
        if (!empty($user['profile_photo'])) {
            $_SESSION['profile_photo'] = ltrim($user['profile_photo'], '/');
        } else {
            $_SESSION['profile_photo'] = null;
        }
    }

    public static function setAdminSession(array $admin) {
        self::startSessionIfNeeded();
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin'] = $admin;
        $_SESSION['admin_id'] = $admin['id'] ?? null;
        $_SESSION['username'] = $admin['username'] ?? null;
        if (!empty($admin['profile_photo'])) {
            $_SESSION['profile_photo'] = ltrim($admin['profile_photo'], '/');
        } else {
            $_SESSION['profile_photo'] = null;
        }
    }
}
