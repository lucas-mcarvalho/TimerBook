<?php
#require_once __DIR__ . '/../App/core/Router.php';

require_once '/../App/controllers/MainController.php';

echo "Teste";

$controller = new MainController();
$controller->index();
