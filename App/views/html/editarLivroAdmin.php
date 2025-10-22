<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style/editarLivroAdmin.css">
    <link rel="stylesheet" href="style/listarLivro.css">
    <title>Admin - Gerenciar Livros</title>
    <script src="/TimerBook/App/views/javascript/livros_api.js"></script>
</head>
    
<body>

    <header class="main-header">
        <div class="header-logo">
            <a href="index.php?action=home" class="logo-link">
                <img src="uploads/logo.svg" alt="Logo Timerbook" class="logo-img">
                <h1>TimerBook</h1>
            </a>
        </div>
        <div class="header-buttons">
            <button class="nav1-button" onclick="window.history.back()">Voltar</button> 
            <button class="nav-button" onclick="window.location.href='index.php?action=home'">Tela Principal</button> 
        </div>
    </header>

    
    <!--- Código copiado exatamente como na visão do usuário em 
    listarLivro.php. Resta configurar a edição de livros-->
   <main class="books-container">
    <h2 class="title">Livros do Usuário</h2>
    
    <div class="carousel-container">
        <button id="prev-button" class="carousel-button prev-button">
            <
        </button>
        
        <div id="book-list" class="book-list carousel-list"></div>
        
        <button id="next-button" class="carousel-button next-button">
            >
        </button>
    </div>
</main>
   
    <script>listarLivros();</script>


</body>
</html>