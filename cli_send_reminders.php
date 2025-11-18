<?php
// Wrapper CLI para enviar lembretes e gravar logs
chdir(__DIR__);

// Carrega autoload e configurações (dotenv, DB)
require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/App/core/database_config.php';
require __DIR__ . '/App/controllers/RemindController.php';

// Parâmetros: número de dias de inatividade (opcional). Padrão alterado para 1 dia.
$days = 1;
if (isset($argv[1]) && is_numeric($argv[1])) {
    $days = (int)$argv[1];
}

// Executa o envio e captura a saída
ob_start();
$controller = new ReminderController();
$controller->sendReminders($days);
$output = ob_get_clean();

// Garante pasta de logs
$logDir = __DIR__ . '/logs';
if (!is_dir($logDir)) {
    @mkdir($logDir, 0777, true);
}

$logFile = $logDir . '/reminder.log';
$entry = '[' . date('Y-m-d H:i:s') . '] (dias=' . $days . ') ' . preg_replace('/\s+/', ' ', trim($output)) . PHP_EOL;
file_put_contents($logFile, $entry, FILE_APPEND);

// Também imprime no stdout (útil para debug em agendador)
echo $entry;

exit(0);
