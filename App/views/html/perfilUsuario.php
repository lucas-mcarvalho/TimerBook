<?php
// Inicia a sessão se ainda não foi iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verifica se o usuário está logado
if (!isset($_SESSION['user_id']) && !isset($_SESSION['id'])) {
    header('Location: index.php?action=login');
    exit();
}

// Pega o ID da sessão
$userId = $_SESSION["user_id"] ?? $_SESSION["id"];

// --- CORREÇÃO AQUI ---
// Em vez de confiar na sessão, buscamos os dados frescos do banco
// Certifique-se de incluir seu arquivo de conexão ou model User se necessário
// require_once '../App/models/User.php'; (Exemplo, ajuste o caminho)

try {
    // Supondo que você tenha um método para buscar por ID
    // Se sua classe for diferente, ajuste aqui (ex: User::find($userId))
    $userData = User::getById($userId); 
    
    if ($userData) {
        // Atualiza as variáveis com o que veio do banco (S3)
        $nome = $userData['nome'];
        $username = $userData['username'];
        $email = $userData['email'];
        $profilePhoto = $userData['profile_photo']; // Aqui virá a URL nova do S3
        
        // Opcional: Atualizar a sessão para as outras páginas
        $_SESSION['profile_photo'] = $profilePhoto;
    } else {
        // Fallback se não achar no banco (usa a sessão antiga)
        $nome = $_SESSION['nome'] ?? '';
        $username = $_SESSION['username'] ?? '';
        $email = $_SESSION['email'] ?? '';
        $profilePhoto = $_SESSION['profile_photo'] ?? null;
    }

} catch (Exception $e) {
    // Se der erro no banco, usa a sessão como fallback
    $nome = $_SESSION['nome'] ?? '';
    $username = $_SESSION['username'] ?? '';
    $email = $_SESSION['email'] ?? '';
    $profilePhoto = $_SESSION['profile_photo'] ?? null;
}

// Lógica de fallback para imagem padrão ou local
if (empty($profilePhoto)) {
    $profilePhoto = "uploads/default.png";
} elseif (strpos($profilePhoto, 'http') === 0) {
    // É URL do S3, mantém como está (AQUI QUE ELE VAI ENTRAR AGORA)
} else {
    // É caminho local antigo
    if (strpos($profilePhoto, 'uploads/') !== 0) {
        $profilePhoto = "uploads/" . $profilePhoto;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="uploads/TimerbookFavicon.png" type="image/png">
    <title>Timerbook - Meu Perfil</title>
    <link rel="stylesheet" href="style/perfilUsuario.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="/TimerBook/App/views/javascript/usuario_api.js?v=<?= time() ?>"></script>
</head>
<body>

    <header class="main-header">
        <div class="header-logo">
            <a href="index.php?action=home" class="logo-link">
                <img src="uploads/TimerbookLogo.svg" alt="Logo Timerbook" class="logo-img">
                <h1>TimerBook</h1>
            </a>
        </div>
        <div class="header-profile">
            <img src="<?= htmlspecialchars($profilePhoto ) ?>" alt="Foto de Perfil" class="profile-pic">
            <a href="index.php?action=perfil_usuario" class="profile-button">Meu Perfil</a>
            <a href="index.php?action=sair" class="logout-button">Encerrar Sessão</a>
        </div>
    </header>

    <main class="main-content">
        <div class="profile-container">
            <!-- Botão de voltar -->
            <a href="index.php?action=home" class="back-button">
                <i class="fas fa-arrow-left"></i>
            </a>

            <div class="profile-content">
                <!-- COLUNA ESQUERDA -->
                <div class="left-column">
                    <!-- Seção da foto de perfil com botões ao lado -->
                    <div class="profile-photo-section">
                        <div class="photo-container">
                            <img src="<?= htmlspecialchars($profilePhoto) ?>" alt="Foto de Perfil" class="profile-photo" id="profilePhoto">
                            <div class="photo-placeholder" id="photoPlaceholder" style="display: none;">
                                <i class="fas fa-times"></i>
                            </div>
                        </div>
                        <div class="photo-buttons">
                            <a href="index.php?action=usuario_editar&id=<?= htmlspecialchars($userId) ?>" class="action-btn edit-profile-btn">Editar Perfil</a>
                        </div>
                    </div>

                    <!-- Seção dos dados do usuário -->
                    <div class="user-data-section">
                        <div class="input-group">
                            <label for="nome">Nome Completo</label>
                            <div class="input-container">
                                <input type="text" id="nome" value="<?= htmlspecialchars($nome) ?>" readonly>
     
                            </div>
                        </div>

                        <div class="input-group">
                            <label for="username">Nome de Usuário</label>
                            <div class="input-container">
                                <input type="text" id="username" value="<?= htmlspecialchars($username) ?>" readonly>
                                  </div>
                        </div>

                        <div class="input-group">
                            <label for="email">E-mail</label>
                            <div class="input-container">
                                <input type="email" id="email" value="<?= htmlspecialchars($email) ?>" readonly>
                                  </div>
                        </div>
                    </div>
                </div>

                <!-- COLUNA DIREITA -->
                <div class="right-column">
                    <!-- Seção dos botões de ação do topo -->
                    <div class="top-action-buttons">
                        <a href="index.php?action=listar_livros" class="action-btn view-books-btn">
                            <i class="fas fa-book"></i>
                            <span>Meus Livros</span>
                        </a>

                        <a href="index.php?action=forgot_password" class="action-btn change-password-btn">
                            <i class="fas fa-key"></i>
                            <span>Alterar Senha</span>
                        </a>

                        <a href="index.php?action=sair" class="action-btn logout-btn">
                            <i class="fas fa-sign-out-alt"></i>
                            <span>Sair</span>
                        </a>
                    </div>

                    <!-- Botão de deletar conta alinhado com o último campo -->
                    <div class="bottom-action-button">
                        <button class="action-btn delete-account-btn" onclick="confirmDeleteAccount(); return false;">
                            <i class="fas fa-trash-alt"></i>
                            <span>Deletar Conta</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Modal de confirmação para deletar conta -->
    <div id="deleteModal" class="modal">
        <div class="modal-content">
            <h3>Confirmar Exclusão</h3>
            <p>Tem certeza que deseja deletar sua conta? Esta ação não pode ser desfeita.</p>
            <div class="modal-buttons">
                <button onclick="closeDeleteModal(); return false;" class="btn-cancel">Cancelar</button>
                <button onclick="deletarContaUsuario(); return false;" class="delete-confirm-btn">Deletar</button>
            </div>
        </div>
    </div>
</body>

<script>
        // Variável global com o ID do usuário
        const currentUserId = <?= json_encode($userId) ?>;

        // Função para mostrar notificações
        function showNotification(message, type = 'success') {
            const notification = document.createElement('div');
            notification.className = `notification notification-${type}`;
            notification.textContent = message;
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.classList.add('show');
            }, 100);
            
            setTimeout(() => {
                notification.classList.remove('show');
                setTimeout(() => notification.remove(), 300);
            }, 3000);
        }

        // Função para confirmar deletar conta
        function confirmDeleteAccount() {
            document.getElementById('deleteModal').style.display = 'block';
        }

        // Função para fechar modal de deletar
        function closeDeleteModal() {
            document.getElementById('deleteModal').style.display = 'none';
        }

        // Função para deletar conta
        async function deletarContaUsuario() {
            deletarConta(currentUserId);
        }

        // Fechar modais ao clicar fora
        window.onclick = function(event) {
            const deleteModal = document.getElementById('deleteModal');
            
            if (event.target == deleteModal) {
                closeDeleteModal();
            }
        }
    </script>
</html>