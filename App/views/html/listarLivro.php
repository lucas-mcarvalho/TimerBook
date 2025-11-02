<?php
// Inicia a sessão se ainda não estiver iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$user_id = $_SESSION['user_id'] ?? null;
$user_name = $_SESSION['username'] ?? 'Usuário'; 

if (!$user_id) {
    echo "<h1>Erro: ID do usuário não fornecido.</h1>";
    exit;
}

$profilePhoto = $_SESSION['profile_photo'] ?? "uploads/default.png";
if ($profilePhoto && strpos($profilePhoto, 'http') === 0) {
} else {
    if ($profilePhoto && strpos($profilePhoto, 'uploads/') !== 0) {
        $profilePhoto = "uploads/" . $profilePhoto;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Timer Book - Meus Livros</title>
    <link rel="stylesheet" href="style/listarLivro.css?v=<?php echo time(); ?>">
    <script src="/TimerBook/App/views/javascript/livros_api.js?v=<?= time() ?>"></script>
</head>
<body>

    <header class="main-header">
        <div class="header-logo">
            <a href="index.php?action=home" class="logo-link">
                <img src="uploads/TimerbookLogo.svg" alt="Logo Timerbook" class="logo-img">
                <h1>TimerBook</h1>
            </a>
        </div>
        <div class="header-profile">
            <img src="<?= htmlspecialchars($profilePhoto) ?>" alt="Foto de Perfil" class="profile-pic">
            <a href="index.php?action=perfil_usuario" class="profile-button">Meu Perfil</a>
            <a href="index.php?action=sair" class="logout-button">Encerrar Sessão</a>
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
        
        <div class="action-buttons-container">
            <a href="index.php?action=Adicionar_Livro" class="add-book-button">
                <span class="plus-icon">+</span> Cadastrar Livro
            </a>
            
            <a href="index.php?action=estatistica_livros&id=<?php echo $user_id ?>&user_name=<?php echo $user_name ?>" class="add-book-button stats-button">
                Ver Estatísticas
            </a>
        </div>
    </main>

    <script>

        listarLivros("my-books");

        const bookList = document.getElementById('book-list');
        const prevButton = document.getElementById('prev-button');
        const nextButton = document.getElementById('next-button');

        nextButton.addEventListener('click', () => {
            bookList.scrollBy({
                left: 220,
                behavior: 'smooth'
            });
        });

        prevButton.addEventListener('click', () => {
            bookList.scrollBy({
                left: -220,
                behavior: 'smooth'
            });
        });
    </script>
    
</body>
</html>