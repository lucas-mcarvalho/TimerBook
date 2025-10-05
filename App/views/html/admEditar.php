<?php
require_once __DIR__ . '/../../models/Admin.php';
require_once __DIR__ . '/../../models/User.php';
$id = $_GET['id'] ?? null;
$user = null;
if ($id) {
    $user = User::getById($id);
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../public/style/admEditar.css">
    <title>Timer Book</title>
</head>
<body>
    <main class="edit-container">
        <h2 class="title"><?php echo $user ? 'Editar  Usuário' : 'ADICIONAR USER'; ?></h2>
        
        <div>
            <form class="edit-form" action="index.php?action=adm_salvar" method="post" enctype="multipart/form-data">
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($user['id'] ?? ''); ?>">
                <?php if ($user && !empty($user['profile_photo'])): ?>
                <div class="form-group foto-perfil">
                    <label class="foto-perfil" >Foto Atual:</label>
                    <div class="current-photo">
                        <?php
                        // Verifica se é URL do S3 ou caminho local
                        $photoUrl = $user['profile_photo'];
                        if (strpos($photoUrl, 'http') === 0) {
                            // É URL do S3
                            echo '<img src="' . htmlspecialchars($photoUrl) . '" alt="Foto atual" style="max-width: 100px; max-height: 100px; border-radius: 50%;">';
                        } else {
                            // É caminho local (para compatibilidade com fotos antigas)
                            echo '<img src="../public/uploads/' . htmlspecialchars($photoUrl) . '" alt="Foto atual" style="max-width: 100px; max-height: 100px; border-radius: 50%;">';
                        }
                        ?>
                    </div>
                </div>
                <?php endif; ?>
                <div class="form-group">
                    <label for="profile-photo-input"><?php echo $user ? 'Nova Foto de Perfil:' : 'Foto de Perfil:'; ?></label>
                    <input type="file" id="profile-photo-input" name="profile_photo" accept="image/*">
                    <small style="color: #666; font-size: 0.9em;">São aceitos apenas arquivos de imagem (JPG, PNG, GIF)</small>
                </div>
                <div class="form-group">
                    <label for="user-name-input">Nome:</label>
                    <input type="text" id="user-name-input" name="nome" placeholder="Digite o nome completo" value="<?php echo htmlspecialchars($user['nome'] ?? ''); ?>" required>
                </div>
                <div class="form-group">
                    <label for="user-email-input">E-mail:</label>
                    <input type="email" id="user-email-input" name="email" placeholder="Digite o e-mail" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" required>
                </div>
                <div class="form-group">
                    <label for="username-input">Nome de Usuário:</label>
                    <input type="text" id="username-input" name="username" placeholder="Digite o nome de usuário" value="<?php echo htmlspecialchars($user['username'] ?? ''); ?>" required>
                </div>
            
                <div class="form-group">
                    <label for="password-input">Senha:</label>
                    <input type="password" id="password-input" name="senha" placeholder="<?php echo $user ? 'Deixe em branco para manter' : 'Defina a senha'; ?>">
                </div>
                <div class="form-buttons">
                    <button type="submit" id="save-button" class="save-button">Salvar Alterações</button>
                    <a href="index.php?action=admin" id="back-button" class="back-button">Voltar</a>
                </div>
            </form>
        </div>
    </main>

   
   
</body>
</html>
