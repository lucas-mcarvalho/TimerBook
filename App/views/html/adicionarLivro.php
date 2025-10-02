<?php 
// Pega o ID do usuário logado, ou null se não estiver logado
$userId = $_SESSION['user_id'] ?? null;
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Livro</title>
    <link rel="stylesheet" href="style/adicionarLivro.css">
</head>
<body>

<main class="cadastro-container">
    <h2 class="title">CADASTRAR NOVO LIVRO</h2>

    <!-- Formulário para JS -->
    <form id="bookForm" class="cadastro-form" enctype="multipart/form-data">

        <!-- Upload da capa -->
        <div class="form-group">
            <label for="cover">Capa do Livro:</label>
            <input type="file" id="cover" name="capa_arquivo" accept="image/*" required>
        </div>

        <!-- Upload do arquivo do livro (PDF) -->
        <div class="form-group">
            <label for="book-file">Arquivo do Livro (PDF):</label>
            <input type="file" id="book-file" name="caminho_arquivo" accept=".pdf" required>
        </div>

        <!-- Nome do livro -->
        <div class="form-group">
            <label for="book-name-input">Nome do Livro:</label>
            <input type="text" id="book-name-input" name="titulo" placeholder="Digite o nome do livro" required>
        </div>

        <!-- Autor -->
        <div class="form-group">
            <label for="book-author-input">Autor:</label>
            <input type="text" id="book-author-input" name="autor" placeholder="Digite o autor do livro" required>
        </div>

        <!-- Ano -->
        <div class="form-group">
            <label for="book-year-input">Ano:</label>
            <input type="number" id="book-year-input" name="ano_publicacao" placeholder="Digite o ano" required>
        </div>

        <!-- user_id oculto (substitua pelo ID do usuário logado) -->
        <input type="hidden" name="user_id" value="<?= htmlspecialchars($user_id) ?>">

        <!-- Botões -->
        <div class="form-buttons">
            <button type="submit" class="register-button">Cadastrar</button>
            <button type="button" class="back-button" onclick="window.location.href='index.php?action=home'">Voltar</button>
        </div>

        <!-- Div de feedback -->
        <div id="feedback"></div>

    </form>
</main>

</body>
</html>
