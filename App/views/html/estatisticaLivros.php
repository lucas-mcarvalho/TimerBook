<?php
$user_id = $_GET['id'] ?? null;
$user_name = $_GET['user_name'] ?? 'Usuário'; 

if (!$user_id) {
    echo "<h1>Erro: ID do usuário não fornecido.</h1>";
    exit;
}

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="/js/utils.js?v=<?= time() ?>"></script>

    
    <link rel="stylesheet" href="style/admin.css"> 
    <link rel="stylesheet" href="style/estatisticaLivros.css">
    <title>Timer Book - Estatísticas de Livros</title>
</head>
<body>

<header class="header">
    <div class="header-logo ">
        <img src="uploads/TimerbookLogo.svg" alt="Logo TimerBook">
        <h1 class="header-title">TimerBook</h1>
        
    </div>
    
    <div class="header-right">
        <a href="#" onclick="history.back();" class="back-button">Voltar</a>
        <a href="index.php?action=sair" class="logout-button">Sair</a>
    </div>
</header>

<main class="stats-container">
    <h2 class="stats-title">Estatísticas de Livros de <span id="user-name-display"><?= htmlspecialchars($user_name) ?></span></h2>
    
    <p id="loading" class="loading-message">Carregando dados...</p>
    <p id="error-message" class="error-message" style="display:none;"></p>

    <div id="books-list" class="books-list">
        </div>
</main>

<script src="/js/estatisticaLivros.js"></script>

<script>
    window.onload = function() {
        const userId = <?= json_encode($user_id) ?>;
        const userName = <?= json_encode($user_name) ?>;

        if (userId) {
            carregarEstatisticas(userId, userName);
        } else {
            document.getElementById('error-message').textContent = "Erro: ID do usuário não fornecido.";
            document.getElementById('error-message').style.display = 'block';
            document.getElementById('loading').style.display = 'none';
        }
    };
</script>
</body>
</html>