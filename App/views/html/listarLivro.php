<?php 
$id = $_SESSION['id'] ?? "uploads/default.png";
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Timer Book</title>
    <link rel="stylesheet" href="style/listarLivro.css?v=<?php echo time(); ?>">

    <script src="/TimerBook/App/views/javascript/listar_livros.js"></script>

    
</head>
<body>

<header class="navbar">
    <div class="nav-buttons">
        <!-- Botão voltar para a página anterior no histórico -->
        <button class="nav-button" onclick="window.history.back()"><</button> 
        
        <!-- Botão para ir para a página inicial -->
        <button class="nav-button" onclick="window.location.href='index.php?action=home'">&#8962;</button> 
    </div>
</header>

<main class="books-container">
    <h2 class="title">MEUS LIVROS</h2>
    
    <div id="book-list" class="book-list"></div>
    
    <!-- Botão para cadastrar novo livro -->
    <button id="add-button" class="add-button" onclick="window.location.href='index.php?action=Adicionar_Livro'">
        <span>+</span> CADASTRAR NOVO LIVRO
    </button>
</main>



</body>
</html>
