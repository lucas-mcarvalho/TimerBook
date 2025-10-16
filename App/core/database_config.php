<?php

class Database {
    public static function connect() {
        // Dados de conexão local (ajuste conforme seu phpMyAdmin)
        $host = 'localhost';
        $dbname = 'Users';
        $user = 'root';
        $pass = ''; // normalmente vazio no phpMyAdmin local

        try {
            $pdo = new PDO(
                "mysql:host=$host;dbname=$dbname;charset=utf8",
                $user,
                $pass
            );
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $pdo;
        } catch (PDOException $e) {
            die("Erro de conexão: " . $e->getMessage());
        }
    }
}
