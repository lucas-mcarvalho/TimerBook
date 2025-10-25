<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Timer Book</title>
    <link rel="stylesheet" href="style/listarLivro.css?v=<?php echo time(); ?>">
    <script src="/TimerBook/App/views/javascript/livros_api.js?v=<?= time() ?>"></script>

    
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

<main class="books-container">
    <h2 class="title">MEUS LIVROS</h2>
    
    <div class="carousel-container">
        <button id="prev-button" class="carousel-button prev-button">
            <
        </button>
        
        <div id="book-list" class="book-list carousel-list"></div>
        
        <button id="next-button" class="carousel-button next-button">
            >
        </button>
    </div>
    
    <button id="add-button" class="add-button" onclick="window.location.href='index.php?action=Adicionar_Livro'">
        <span class="plus-icon btn_cad">+</span> Cadastrar Livro
    </button>
</main>

<script>listarLivros("my-books");</script>

</body>
</html>
