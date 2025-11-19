<?php
require_once __DIR__ . '/../controllers/RemindController.php';
$controller = new ReminderController();
// Envia lembretes para usuários com 1 dia de inatividade por padrão
$controller->sendReminders(1);


?>