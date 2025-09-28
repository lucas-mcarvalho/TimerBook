<?php 
$profilePhoto = $_SESSION['profile_photo'] ?? "uploads/default.png";
?>


<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Timerbook - Início</title>
    <link rel="stylesheet" href="style/home.css">
</head>
<body>

    <header class="main-header">
        <div class="header-logo">
            <a href="index.php?action=home" class="logo-link">
                <img src="uploads/main_logo.png" alt="Logo Timerbook" class="logo-img">
                <h1>TimerBook</h1>
            </a>
        </div>
        <div class="header-profile">
         <img src="<?= htmlspecialchars($profilePhoto) ?>" alt="Foto de Perfil" class="profile-pic">


            <a href="index.php?action=listar_livros" class="profile-button">Meu Perfil</a>
            <a href="index.php?action=sair" class="logout-button">Encerrar Sessão</a>
        </div>
    </header>

    <main class="main-content">
        <section class="slider-container">
            <div class="slider">
                <div class="slides">
                    <input type="radio" name="radio-btn" id="radio1" checked>
                    <input type="radio" name="radio-btn" id="radio2">
                    <input type="radio" name="radio-btn" id="radio3">
                    <input type="radio" name="radio-btn" id="radio4">
                    
                    <div class="slide first">
                        <img src="uploads/boasvindas_carrossel.png" alt="Imagem 1">
                    </div>
                    <div class="slide">
                        <a href="index.php?action=Adicionar_Livro">
                            <img src="uploads/aindanaocadastrou.png" alt="Imagem 2">
                        </a>
                    </div>
                    <div class="slide">
                        <a href="index.php?action=listar_livros">
                            <img src="uploads/acessoperfil.png" alt="Imagem 3">
                        </a>
                    </div>
                    <div class="slide">
                        <a href="index.php?action=forgot_password">
                            <img src="uploads/redefinasuasenha.png" alt="Imagem 4">
                        </a>
                    </div>
                </div>
                
                <div class="manual-navigation">
                    <label for="radio1" class="manual-btn"></label>
                    <label for="radio2" class="manual-btn"></label>
                    <label for="radio3" class="manual-btn"></label>
                    <label for="radio4" class="manual-btn"></label>
                </div>
            </div>
        </section>

        <aside class="ranking-container">
            <h2>Ranking</h2>
            <ol class="ranking-list">
                <li><span>1º</span> Nome do Usuário 1</li>
                <li><span>2º</span> Nome do Usuário 2</li>
                <li><span>3º</span> Nome do Usuário 3</li>
            </ol>
        </aside>
    </main>

    <footer class="main-footer">
        <a href="index.php?action=Adicionar_Livro" class="add-book-button">
            <span class="plus-icon">+</span> Cadastrar Livro
        </a>
    </footer>

    <script>
        let count = 1;
        document.getElementById("radio1").checked = true;
        setInterval(function(){
            nextImage();
        }, 5000);
        function nextImage(){
            count++;
            if(count > 4){
                count = 1;
            }
            document.getElementById("radio" + count).checked = true;
        }
    </script>

</body>
</html>