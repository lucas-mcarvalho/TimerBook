<?php
session_start();
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
        
        <!-- Formulário sem action/method -->
        <form id="cadastro-livro-form" class="cadastro-form" enctype="multipart/form-data">
            
            <!-- Upload da capa -->
            <div class="form-group">
                <label for="cover">Capa do Livro:</label>
                <input type="file" id="capa_arquivo" name="capa_arquivo" accept="image/*" required>
            </div>

            <!-- Upload do arquivo do livro -->
            <div class="form-group">
                <label for="book-file">Arquivo do Livro (PDF):</label>
                <input type="file" id="caminho_arquivo" name="caminho_arquivo" accept=".pdf" required>
            </div>
            
            <!-- Nome do livro -->
            <div class="form-group">
                <label for="book-name-input">Nome do Livro:</label>
                <input type="text" id="titulo" name="titulo" placeholder="Digite o nome do livro" required>
            </div>

            <!-- Autor -->
            <div class="form-group">
                <label for="book-author-input">Autor:</label>
                <input type="text" id="autor" name="autor" placeholder="Digite o autor do livro" required>
            </div>

            <!-- Ano -->
            <div class="form-group">
                <label for="book-year-input">Ano:</label>
                <input type="number" id="ano_publicacao" name="ano_publicacao" placeholder="Digite o ano de publicação" required>
            </div>

            <!-- User id aqui no front não é melhor prática -->
            <input type="hidden" name="user_id" value="<?php echo $_SESSION['user_id']; ?>">

            <!-- Botões -->
            <div class="form-buttons">
                
                <button type="submit" id="register-button" class="register-button">Cadastrar</button>
                
                <button type="button" id="back-button" class="back-button" onclick="window.location.href='index.php?action=home'">Voltar</button>
            </div>
        </form>
    </main>

    <script src="/TimerBook/App/views/javascript/cadastrar_livro.js"></script>
</body>
</html>
