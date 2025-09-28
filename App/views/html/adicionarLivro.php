<?php
session_start(); // obrigatório
if(!isset($_SESSION['user_id'])){
    // redirecionar para login ou exibir erro
    header("Location: index.php?action=login");
    exit;
}
?>

<form id="book-form" class="cadastro-form" enctype="multipart/form-data">
    <!-- Upload do arquivo do livro -->
    <div class="form-group">
        <label for="book-file-input">Arquivo do Livro (PDF, ePub, etc.):</label>
        <input type="file" id="book-file-input" name="caminho_arquivo" accept=".pdf,.epub" required>
    </div>

    <!-- Nome do livro -->
    <div class="form-group">
        <label for="book-name-input">Nome do Livro:</label>
        <input type="text" id="book-name-input" name="titulo" placeholder="Digite o nome do livro" required>
    </div>

    <!-- Autor -->
    <div class="form-group">
        <label for="book-author-input">Autor:</label>
        <input type="text" id="book-author-input" name="autor" placeholder="Digite o autor" required>
    </div>

    <!-- Ano -->
    <div class="form-group">
        <label for="book-year-input">Ano de publicação:</label>
        <input type="text" id="book-year-input" name="ano_publicacao" placeholder="Digite o ano de publicação" required>
    </div>

    <!-- Usuário logado -->
    <input type="hidden" name="user_id" value="<?= $_SESSION['user_id'] ?>">

    <div class="form-buttons">
        <button type="submit">Cadastrar Livro</button>
    </div>

    <div id="response" class="response-message"></div>

</form>
