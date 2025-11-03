<?php
$id_livro = $_POST['id_livro'] ?? null;
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../public/style/admEditar.css">
    <script src="/TimerBook/App/views/javascript/livros_api.js"></script>
    

    <title>Timer Book</title>
</head>
<body>

    <main class="edit-container">
        <h2 class="title">Editar Livro</h2>
        
        <div>
            <form class="edit-form" id="editUserForm" enctype="multipart/form-data">
                <input type="hidden" name="id" value="<?php echo $id_livro; ?>">

                <div class="form-group">
                    <label for="photo">Capa:</label>
                    <input type="file" id="photo" name="capa" accept="image/*">
                    <small style="color: #666; font-size: 0.9em;">
                        São aceitos apenas arquivos de imagem (JPG, PNG, GIF)
                    </small>
                    <div style="margin-top: 10px;">
                        <img id="photoPreview" src="" alt="Pré-visualização" style="max-width: 120px; display: none;">
                    </div>
                </div>

                <div class="form-group">
                    <label for="nome">Nome do livro:</label>
                    <input type="text" id="nome-livro" name="titulo" placeholder="Digite o nome do livro" required>
                </div>

                <div class="form-group">
                    <label for="username">Autor:</label>
                    <input type="text" id="nome-autor" name="autor" placeholder="Digite o nome do autor" required>
                </div>

                
                <div class="form-group">
                    <label for="senha">Ano de Publicação:</label>
                    <input type="text" id="ano-publicacao" name="ano_publicacao" placeholder="Digite o ano de publicação"
                    required>
                </div>          
                <div class="form-buttons">
                    <button type="submit" id="save-button" class="save-button">Salvar Alterações</button>
                    <a href="#" onclick="history.back(); return false;" id="back-button" class="back-button">Voltar</a>
                </div>
            </form>
        </div>
    </main>


<script>
    const livroId = "<?php echo $id_livro; ?>";
    editarLivro(livroId);
</script>

</body>
</html>