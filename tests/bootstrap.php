<?php
// bootstrap dos testes
require __DIR__ . '/../vendor/autoload.php';

// Carrega um .env de testes (pode herdar do projeto)
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

// Se houver DB_DSN de testes, sobrescreve as constantes esperadas pelo Database
if (getenv('DB_DSN')) {
    // nada aqui: o arquivo database_config.php será ajustado para usar DB_DSN quando presente
}

// Carrega helpers do app
require __DIR__ . '/../App/core/database_config.php';
