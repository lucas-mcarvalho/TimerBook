<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style/admin.css">
    <title>Timer Book</title>
</head>
<body>
    <main class="edit-container">

        <h2 class="title">EDITAR USUÁRIO</h2>
        
        <form class="edit-form">

            <div class="form-group">
                <label for="user-name-input">Nome:</label>
                <input type="text" id="user-name-input" placeholder="Digite o nome completo">
            </div>

            <div class="form-group">
                <label for="user-email-input">E-mail:</label>
                <input type="email" id="user-email-input" placeholder="Digite o e-mail">
            </div>

            <div class="form-group">
                <label for="username-input">Nome de Usuário:</label>
                <input type="text" id="username-input" placeholder="Digite o nome de usuário">
            </div>
            
            <div class="form-group">
                <label for="password-input">Senha:</label>
                <input type="password" id="password-input" placeholder="Digite a nova senha">
            </div>

            <div class="form-buttons">
                <button type="submit" id="save-button" class="save-button">Salvar Alterações</button>
                <button type="button" id="back-button" class="back-button">Voltar</button>
            </div>
        </form>
    </main>

    
</body>
</html>