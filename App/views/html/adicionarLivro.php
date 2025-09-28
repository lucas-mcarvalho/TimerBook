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
        
        <form class="cadastro-form">
            <div class="cover-upload">
                <div class="cover-placeholder">Capa do Livro</div>
                <button type="button" class="upload-button">Selecionar Imagem</button>
            </div>
            
            <div class="form-group">
                <label for="book-name-input">Nome do Livro:</label>
                <input type="text" id="book-name-input" placeholder="Digite o nome do livro">
            </div>

            <div class="form-group">
                <label for="book-year-input">Ano:</label>
                <input type="text" id="book-year-input" placeholder="Digite o ano de publicação">
            </div>

            <div class="form-buttons">
                <button type="submit" id="register-button" class="register-button">Cadastrar</button>
                <button type="button" id="back-button" class="back-button" onclick="window.location.href='index.php?action=home'">Voltar</button>
            </div>
        </form>
    </main>

</body>
</html>