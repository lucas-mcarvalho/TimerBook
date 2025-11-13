<?php
require_once __DIR__ . '/../controllers/RemindController.php';
$controller = new ReminderController();
$controller->sendReminders();
