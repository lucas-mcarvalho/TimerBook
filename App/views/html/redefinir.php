<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="uploads/TimerbookFavicon.png" type="image/png">
    <link rel="stylesheet" href="/TimerBook/public/style/redefinir.css">
    <title>Redefinir senha</title>
</head>
<body>

    <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
        
        <div id="successBox">
            
            <img class="logo" src="uploads/logo.svg" alt="logo">

            Senha redefinida com sucesso!<br>
            Você será redirecionado para o login em instantes.
        </div>
        
        <script>
            setTimeout(function() {
                window.location.href = '/TimerBook/public/index.php?action=login';
            }, 3000);
        </script>

    <?php else: ?>
        
        <form id="resetForm" action="/TimerBook/public/api.php?action=reset-password" method="post">
          
          <img class="logo" src="/TimerBook/public/uploads/main_logo.png" alt="logo">

          <h1>Redefinir Senha</h1>
          <input type="hidden" name="token" value="<?php echo htmlspecialchars($_GET['token'] ?? ''); ?>">
          <label for="nova_senha">Nova Senha:</label>
          <input type="password" id="nova_senha" name="nova_senha" required>
          <label for="confirma_senha">Confirmar Nova Senha:</label>
          <input type="password" id="confirma_senha" name="confirma_senha" required>
          <button type="submit">Redefinir Senha</button>
        </form>
        
    <?php endif; ?>
</body>
</html>