<?php
require_once __DIR__ . '/../../models/User.php';
$users = User::getAll();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style/admin.css">
    <title>Timer Book</title>
</head>
<body>

<header class="header">
    <div class="header-logo">
        <img src="uploads/main_logo.png" alt="Logo TimerBook">
        <span class="header-title">TimerBook</span>
    </div>
    
    <div class="header-right">
        
        <a href="index.php?action=adm_editar" id="add-user-button" class="add-button">Adicionar Usuário +</a>
        
        <a href="index.php?action=admin_sair" class="logout-button">sair</a>
    </div>
</header>

    <main class="admin-container">
        
        
        <div class="user-list-header">
            <h3>Usuários</h3>
        </div>

        <div id="user-list" class="user-list">
            <div class="form-group">
                <label for="search">Pesquisa por nome: </label>
                <input type="text" id="search" placeholder="Pesquisa" oninput="filterList()">
            </div>

            <?php if (is_array($users) && count($users) > 0): ?>
                <?php foreach ($users as $user): ?>
                    <div class="user-item" data-name="<?php echo htmlspecialchars($user['nome'] ?? $user['username']); ?>">
                        <span><?php echo htmlspecialchars(($user['nome'] ?: $user['username']) . ' (' . $user['email'] . ')'); ?></span>
                        <div class="user-controls">
                            <a href="index.php?action=adm_editar&id=<?php echo urlencode($user['id']); ?>" class="edit-button  btn2">Editar</a>

                            <a href="index.php?action=adm_listarLivros&id=<?php echo urlencode($user['id']);
                            ?>" class="books-button btn3">Gerenciar Livros</a>

                            <a href="index.php?action=adm_excluir&id=<?php echo urlencode($user['id']); ?>" class="delete-button btn" onclick="return confirm('Tem certeza que deseja excluir?')">Excluir</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Nenhum usuário encontrado.</p>
            <?php endif; ?>
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

</body>
</html>