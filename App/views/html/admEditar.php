<?php
require_once __DIR__ . '/../../models/Admin.php';
$id = $_GET['id'] ?? null;
$admin = null;
if ($id) {
    $admin = Admin::getById($id);
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style/admin.css">
    <title>Timer Book</title>
</head>
<body>
    <main class="edit-container">

        <h2 class="title"><?php echo $admin ? 'EDITAR ADMIN' : 'ADICIONAR ADMIN'; ?></h2>
        
        <form class="edit-form" action="index.php?action=adm_salvar" method="post">
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($admin['id'] ?? ''); ?>">

            <div class="form-group">
                <label for="user-name-input">Nome:</label>
                <input type="text" id="user-name-input" name="nome" placeholder="Digite o nome completo" value="<?php echo htmlspecialchars($admin['nome'] ?? ''); ?>" required>
            </div>

            <div class="form-group">
                <label for="user-email-input">E-mail:</label>
                <input type="email" id="user-email-input" name="email" placeholder="Digite o e-mail" value="<?php echo htmlspecialchars($admin['email'] ?? ''); ?>" required>
            </div>

            <div class="form-group">
                <label for="username-input">Nome de Usuário:</label>
                <input type="text" id="username-input" name="username" placeholder="Digite o nome de usuário" value="<?php echo htmlspecialchars($admin['username'] ?? ''); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="password-input">Senha:</label>
                <input type="password" id="password-input" name="senha" placeholder="<?php echo $admin ? 'Deixe em branco para manter' : 'Defina a senha'; ?>">
            </div>

            <div class="form-buttons">
                <button type="submit" id="save-button" class="save-button">Salvar Alterações</button>
                <a href="index.php?action=admin" id="back-button" class="back-button">Voltar</a>
            </div>
        </form>
    </main>

    
    
</body>
</html>