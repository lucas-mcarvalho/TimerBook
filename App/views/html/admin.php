<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style/admin.css">
    <title>Timer Book</title>

    <script src="/TimerBook/App/views/javascript/admin.js"></script>
</head>
<body>

<header class="header">
    <div class="header-logo">
        <img src="uploads/main_logo.png" alt="Logo TimerBook">
        <span class="header-title">TimerBook</span>
    </div>
    
    <div class="header-right">
        
        <!-- Cria rota para adicionar usuários(via admin)-->
        <a href="" id="add-user-button" class="add-button">Adicionar Usuário +</a>
        
        <!-- Cria rota para saida do admin-->
        <a href="" class="logout-button">sair</a>
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