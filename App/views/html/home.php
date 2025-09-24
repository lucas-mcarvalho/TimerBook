    <?php
require_once __DIR__ . '/../../../App/controllers/UserController.php';
UserController::checkLogin();
?>
    
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Home</title>
    </head>
    <body>
        
    </body>
    </html>
        <form action="index.php?action=sair">
        <button>Sair.</button>

        </form>    
    <?php
    var_dump($_SESSION);
    ?>
