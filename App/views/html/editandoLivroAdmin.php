<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Livro</title>
    <link rel="stylesheet" href="style/adicionarLivro.css">
</head>
<body>

    <main class="cadastro-container">
        <h2 class="title">EDITAR LIVRO</h2>
        
        <form id="edicao-livro-form" class="cadastro-form" enctype="multipart/form-data">
            
            <div class="form-group">
                <label for="capa_arquivo">Capa do Livro (clique para alterar):</label>
                <label for="capa_arquivo" class="cover-placeholder">
                    <img src="https://placehold.co/200x250" alt="Prévia da capa" id="capaPreview">
                </label>
                <input type="file" id="capa_arquivo" name="capa_arquivo" accept="image/*" class="file-input">
            </div>

            <div class="form-group">
                <label for="caminho_arquivo" class="btn_pdf">Alterar Arquivo (PDF):</label>
                <input type="file" id="caminho_arquivo" name="caminho_arquivo" accept=".pdf" class="file-input01">
            </div>
            
            <div class="form-group">
                <label for="titulo">Nome do Livro:</label>
                <input type="text" id="titulo" name="titulo" value="O Senhor dos Anéis" required>
            </div>

            <div class="form-group">
                <label for="autor">Autor:</label>
                <input type="text" id="autor" name="autor" value="J.R.R. Tolkien" required>
            </div>

            <div class="form-group">
                <label for="ano_publicacao">Ano:</label>
                <input type="number" id="ano_publicacao" name="ano_publicacao" value="1954" required>
            </div>

            <input type="hidden" name="livro_id" value="1">

            <div class="form-buttons">
                <a href=""><button type="submit" class="register-button">Salvar Alterações</button></a>
                <a href=""><button type="button" class="back-button" onclick="window.history.back()">Cancelar</button></a>
            </div>
        </form>
    </main>

</body>
</html>