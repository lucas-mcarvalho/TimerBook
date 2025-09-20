<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style/recuperar.css">
    <title>recuperar senha</title>
</head>
body>
    
    <img class="logo" src="uploads/main_logo.png" alt="logo">

    <form id="forgotForm">
        <h1>Recuperar Senha</h1>
        <p>Digite o e-mail associado à sua conta, um link será enviado para confirmação:</p>
        
        <label for="email">E-mail:</label>
        <input type="email" id="email" name="email" required>
        
        <button type="submit">Enviar link de recuperação</button>
        <div id="forgotResponse"></div>
    </form>

    <script src="forgot-password.js"></script>
</body>
</html>