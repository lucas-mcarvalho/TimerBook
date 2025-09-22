<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/TimerBook/public/style/redefinir.css">
    <title>Redefinir senha</title>
</head>
<body>
    <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
        <div id="successBox" style="
            margin: 40px auto 20px auto;
            max-width: 400px;
            background: #e6ffe6;
            border: 2px solid #4CAF50;
            color: #256029;
            padding: 20px;
            border-radius: 8px;
            font-size: 1.1em;
            text-align: center;
            box-shadow: 0 2px 8px #0001;
        ">
            Senha redefinida com sucesso!<br>
            Você será redirecionado para o login em instantes.
        </div>
        <script>
            setTimeout(function() {
                window.location.href = '/TimerBook/public/index.php?action=login';
            }, 3000);
        </script>
    <?php else: ?>
        <img class="logo" src="/TimerBook/public/uploads/main_logo.png" alt="logo">

        <form action="/TimerBook/public/salvar_nova_senha.php" method="post">
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