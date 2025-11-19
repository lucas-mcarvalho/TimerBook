<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="uploads/TimerbookFavicon.png" type="image/png">
    <link rel="stylesheet" href="style/admin.css">
    <title>Timer Book</title>

    <script src="/js/admin.js?v=<?= time() ?>"></script>
</head>
<body>

<header class="header">
    <div class="header-logo ">
        <img src="uploads/logo.svg" alt="Logo TimerBook">
        <h1 class="header-title">TimerBook</h1>
        
    </div>
    
    <div class="header-right">
        <!-- Cria rota para saida do admin-->
        <a href="index.php?action=sair" class="logout-button">Sair</a>
    </div>
</header>

    <main class="admin-container">
        
    <!-- A função render do admin.js renderiza a tabela de usuários -->
        <div class="user-list-header">
            <h3>Usuários</h3>
        </div>

        <div id="user-list" class="user-list">
            <div class="form-group">
                <label for="search">Pesquisa por nome: </label>
                <input type="text" id="search" placeholder="Pesquisa" oninput="filterList()">
            </div>
        </div>
    </main>

<script>
function filterList() {
  const term = document.getElementById('search').value.toLowerCase();
  document.querySelectorAll('.user-item').forEach(function(row){
    const name = row.getAttribute('data-name').toLowerCase();
    row.style.display = name.includes(term) ? '' : 'none';
  });
}
</script>


<script>listarUsuarios();</script>
</body>
</html>