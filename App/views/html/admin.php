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
    <h2 class="title">PAINEL DE ADMIN</h2>
    <a href="index.php?action=sair" class="logout-button">Sair</a>
</header>

    <main class="admin-container">
        <div class="user-actions">
            <button id="add-user-button" class="add-button">Adicionar Usuário</button>
        </div>
        
        <div class="user-list-header">
            <h3>Usuários</h3>
        </div>

        <div id="user-list" class="user-list">
             <div class="form-group">
                <label for="search">Pesquisa por nome: </label>
                <input type="text" id="search" placeholder="Pesquisa">
            </div>

            <div class="user-item">
                <span>Nome do Usuário</span>
              <div class="user-controls">
    <a href="index.php?action=adm_editar&id=1" class="edit-button">Editar</a>
    <a href="index.php?action=adm_excluir&id=1" class="delete-button">Excluir</a>
    </div>
    <!--        </div>
            
            <div class="user-item">
                <span>Outro Usuário</span>
                <div class="user-controls">
                    <button class="edit-button">Editar</button>
                    <button class="delete-button">Excluir</button>
                </div>
            </div> -->
        </div>
    </main>

</body>
</html>