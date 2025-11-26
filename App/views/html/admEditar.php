<?php session_start(); ?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="uploads/TimerbookFavicon.png" type="image/png">
    <link rel="stylesheet" href="style/admEditar.css">
    <script src="/js/admin.js"></script>

    <title>Timer Book</title>
</head>
<body>

    <main class="edit-container">
        <h2 class="title">Editar Usuário</h2>
        
        <div>
            <form class="edit-form" id="editUserForm" enctype="multipart/form-data">
                <input type="hidden" id="user-id" name="id">

                <div class="form-group">
                    <label for="photo">Foto de Perfil:</label>
                    <input type="file" id="photo" name="profile_photo" accept="image/*">
                    <small style="color: #666; font-size: 0.9em;">
                        São aceitos apenas arquivos de imagem (JPG, PNG, GIF)
                    </small>
                    <div style="margin-top: 10px;">
                        <img id="photoPreview" src="" alt="Pré-visualização" style="max-width: 120px; display: none;">
                    </div>
                </div>

                <div class="form-group">
                    <label for="nome">Nome:</label>
                    <input type="text" id="nome" name="nome" placeholder="Digite o nome completo" required>
                </div>

                <div class="form-group">
                    <label for="email">E-mail:</label>
                    <input type="email" id="email" name="email" placeholder="Digite o e-mail" required>
                </div>

                <div class="form-group">
                    <label for="username">Nome de Usuário:</label>
                    <input type="text" id="username" name="username" placeholder="Digite o nome de usuário" required>
                </div>

                <div class="form-group">
                    <label for="senha">Senha:</label>
                    <input type="password" id="senha" name="senha" placeholder="Deixe em branco para manter">
                </div>

                <div class="form-buttons">
                    <button type="submit" id="save-button" class="save-button">Salvar Alterações</button>
                    <a href="index.php?action=admin" 
                     id="back-button" 
                     class="back-button">
                    Voltar
                    </a>
                </div>
            </form>
        </div>
    </main>


<script>editarUsuario();</script>

</body>
</html>