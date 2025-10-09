<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style/editarLivroAdmin.css">
    <title>Admin - Gerenciar Livros</title>
</head>
<body>

    <header class="main-header">
        <div class="header-logo">
            <a href="index.php?action=home" class="logo-link">
                <img src="uploads/main_logo.png" alt="Logo Timerbook" class="logo-img">
                <h1>TimerBook</h1>
            </a>
        </div>
        <div class="header-buttons">
            <button class="nav1-button" onclick="window.history.back()">Voltar</button> 
            <button class="nav-button" onclick="window.location.href='index.php?action=home'">Tela Principal</button> 
        </div>
    </header>

    <main class="admin-container">
        <h2 class="title">GERENCIAR LIVROS DO USUÁRIO</h2>
        
        <table class="books-table">
            <thead>
                <tr>
                    <th>Capa</th>
                    <th>Título</th>
                    <th>Autor</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                
                <tr>
                    <td><img src="https://placehold.co/50x75" alt="Capa" class="book-cover"></td>
                    <td>O Senhor dos Anéis</td>
                    <td>J.R.R. Tolkien</td>
                    <td class="actions-cell">
                        <a href=""><button class="edit-btn">Editar</button></a>
                        <a href=""><button class="delete-btn">Excluir</button></a>
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="add-button-container">
            <a href="index.php?action=Adicionar_Livro" class="add-book-btn">
                + Adicionar Novo Livro
            </a>
        </div>
    </main>

</body>
</html>