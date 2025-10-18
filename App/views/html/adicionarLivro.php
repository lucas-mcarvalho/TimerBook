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

    <script src="/TimerBook/App/views/javascript/livros_api.js"></script>

</head>
<body>

    <main class="cadastro-container">
        <h2 class="title">CADASTRAR NOVO LIVRO</h2>
        
        <form id="cadastro-livro-form" class="cadastro-form" enctype="multipart/form-data">
            
            <!-- Upload da capa -->
            <div class="form-group">
            <label for="capa_arquivo">Capa do Livro:</label>
            <label for="capa_arquivo" class="cover-placeholder">
                <img src="https://us.123rf.com/450wm/oliviart/oliviart2004/oliviart200400338/144688847-cone-mais-isolado-no-fundo-branco-adicionar-ícone-mais-sinal-de-adição-cone-medical-plus.jpg?ver=6" alt="Prévia da foto" id="capaPreview">
            </label>
                <input type="file" id="capa_arquivo" name="capa_arquivo" accept="image/*" required class="file-input">
            </div>

            <!-- Upload do arquivo do livro -->
            <div class="form-group">
                <label for="caminho_arquivo" class="btn_pdf">Arquivo do Livro (PDF):</label>
                
                <input type="file" id="caminho_arquivo" name="caminho_arquivo" accept=".pdf" required class="file-input01">
            </div>
            
            <!-- Nome do livro -->
            <div class="form-group">
                <label for="titulo">Nome do Livro:</label>
                <input type="text" id="titulo" name="titulo" placeholder="Digite o nome do livro" required>
            </div>

            <!-- Autor -->
            <div class="form-group">
                <label for="autor">Autor:</label>
                <input type="text" id="autor" name="autor" placeholder="Digite o autor do livro" required>
            </div>

            <!-- Ano -->
            <div class="form-group">
                <label for="ano_publicacao">Ano:</label>
                <input type="number" id="ano_publicacao" name="ano_publicacao" placeholder="Digite o ano de publicação" required>
            </div>

            <input type="hidden" name="user_id" value="<?php echo $_SESSION['user_id']; ?>">

            <!-- Botões -->
            <div class="form-buttons">
                <button type="submit" id="register-button" class="register-button">Cadastrar</button>
                <button type="button" id="back-button" class="back-button" onclick="window.location.href='index.php?action=home'">Voltar</button>
            </div>
        </form>
    </main>

    <script>cadastrarLivro()</script>
</body>
</html>
