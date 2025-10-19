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

// Foto de perfil
$profilePhoto = $_SESSION['profile_photo'] ?? "uploads/default.png";
// Se a foto é URL do S3, usa diretamente, senão adiciona o caminho local
if ($profilePhoto && strpos($profilePhoto, 'http') === 0) {
    // É URL do S3, mantém como está
} else {
    // É caminho local, adiciona o prefixo uploads/ se necessário
    if ($profilePhoto && strpos($profilePhoto, 'uploads/') !== 0) {
        $profilePhoto = "uploads/" . $profilePhoto;
    }
}

// Dados do usuário da sessão
$nomeCompleto = $_SESSION['nome'] ?? '';
$nomeUsuario = $_SESSION['username'] ?? '';
$email = $_SESSION['email'] ?? '';
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Timerbook - Meu Perfil</title>
    <link rel="stylesheet" href="style/perfilUsuario.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
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
            <img src="<?= htmlspecialchars($profilePhoto) ?>" alt="Foto de Perfil" class="profile-pic">
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
                            <a href="#" class="edit-photo-btn" onclick="editPhoto(); return false;">Editar Foto</a>
                            <a href="#" class="delete-photo-btn" onclick="deletePhoto(); return false;">Excluir Foto</a>
                        </div>
                    </div>

                    <!-- Seção dos dados do usuário -->
                    <div class="user-data-section">
                        <div class="input-group">
                            <label for="nomeCompleto">Nome Completo</label>
                            <div class="input-container">
                                <input type="text" id="nomeCompleto" value="<?= htmlspecialchars($nomeCompleto) ?>" readonly>
                                <a href="#" class="edit-btn" onclick="toggleEdit('nomeCompleto'); return false;">
                                    <i class="fas fa-pencil-alt"></i>
                                </a>
                            </div>
                        </div>

                        <div class="input-group">
                            <label for="nomeUsuario">Nome de Usuário</label>
                            <div class="input-container">
                                <input type="text" id="nomeUsuario" value="<?= htmlspecialchars($nomeUsuario) ?>" readonly>
                                <a href="#" class="edit-btn" onclick="toggleEdit('nomeUsuario'); return false;">
                                    <i class="fas fa-pencil-alt"></i>
                                </a>
                            </div>
                        </div>

                        <div class="input-group">
                            <label for="email">E-mail</label>
                            <div class="input-container">
                                <input type="email" id="email" value="<?= htmlspecialchars($email) ?>" readonly>
                                <a href="#" class="edit-btn" onclick="toggleEdit('email'); return false;">
                                    <i class="fas fa-pencil-alt"></i>
                                </a>
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
                        <a href="#" class="action-btn delete-account-btn" onclick="confirmDeleteAccount(); return false;">
                            <i class="fas fa-trash-alt"></i>
                            <span>Deletar Conta</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Modal para upload de foto -->
    <div id="photoModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closePhotoModal()">&times;</span>
            <h3>Alterar Foto de Perfil</h3>
            <form id="photoForm" enctype="multipart/form-data">
                <input type="file" id="photoInput" accept="image/*" onchange="previewPhoto()">
                <div id="photoPreview"></div>
                <div class="modal-buttons">
                    <a href="#" class="modal-cancel-btn" onclick="closePhotoModal(); return false;">Cancelar</a>
                    <a href="#" class="modal-submit-btn" onclick="document.getElementById('photoForm').dispatchEvent(new Event('submit')); return false;">Salvar</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal de confirmação para deletar conta -->
    <div id="deleteModal" class="modal">
        <div class="modal-content">
            <h3>Confirmar Exclusão</h3>
            <p>Tem certeza que deseja deletar sua conta? Esta ação não pode ser desfeita.</p>
            <div class="modal-buttons">
                <a href="#" onclick="closeDeleteModal(); return false;">Cancelar</a>
                <a href="#" class="delete-confirm-btn" onclick="deleteAccount(); return false;">Deletar</a>
            </div>
        </div>
    </div>

    <script>
        // Função para alternar entre modo de edição e visualização
        function toggleEdit(fieldId) {
            const input = document.getElementById(fieldId);
            const button = input.nextElementSibling;
            const icon = button.querySelector('i');
            
            if (input.readOnly) {
                // Entrar no modo de edição
                input.readOnly = false;
                input.focus();
                input.classList.add('editing');
                icon.className = 'fas fa-check';
                button.classList.add('confirm-btn');
            } else {
                // Sair do modo de edição e salvar
                input.readOnly = true;
                input.classList.remove('editing');
                icon.className = 'fas fa-pencil-alt';
                button.classList.remove('confirm-btn');
                
                // Aqui você pode adicionar a lógica para salvar os dados
                saveUserData(fieldId, input.value);
            }
        }

        // Função para salvar dados do usuário
        function saveUserData(field, value) {
            console.log(`Salvando ${field}: ${value}`);
            
            fetch('public/profile/update', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: 'update_profile',
                    field: field,
                    value: value
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    console.log('Dados salvos com sucesso');
                    showNotification('Perfil atualizado com sucesso!', 'success');
                } else {
                    console.error('Erro ao salvar dados:', data.message);
                    showNotification(data.message || 'Erro ao salvar dados', 'error');
                    // Reverte o valor se houver erro
                    const input = document.getElementById(field);
                    input.value = input.defaultValue;
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                showNotification('Erro ao conectar com o servidor', 'error');
            });
        }
        
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

        // Função para editar foto
        function editPhoto() {
            document.getElementById('photoModal').style.display = 'block';
        }

        // Função para deletar foto
        function deletePhoto() {
            const photoElement = document.getElementById('profilePhoto');
            const placeholder = document.getElementById('photoPlaceholder');
            
            // Implementar chamada para deletar foto do servidor
            fetch('api/profile/photo/delete', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    photoElement.src = data.photo_url;
                    showNotification('Foto deletada com sucesso', 'success');
                } else {
                    showNotification(data.message || 'Erro ao deletar foto', 'error');
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                showNotification('Erro ao conectar com o servidor', 'error');
            });
        }

        // Função para fechar modal de foto
        function closePhotoModal() {
            document.getElementById('photoModal').style.display = 'none';
            document.getElementById('photoPreview').innerHTML = '';
            document.getElementById('photoInput').value = '';
        }

        // Função para preview da foto
        function previewPhoto() {
            const input = document.getElementById('photoInput');
            const preview = document.getElementById('photoPreview');
            
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.innerHTML = `<img src="${e.target.result}" alt="Preview" style="max-width: 200px; max-height: 200px;">`;
                };
                reader.readAsDataURL(input.files[0]);
            }
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
        function deleteAccount() {
            // Implementar chamada para deletar conta
            fetch('api/profile/delete-account', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('Conta deletada com sucesso', 'success');
                    setTimeout(() => {
                        window.location.href = 'index.php?action=login';
                    }, 1500);
                } else {
                    showNotification(data.message || 'Erro ao deletar conta', 'error');
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                showNotification('Erro ao conectar com o servidor', 'error');
            });
        }

        // Manipulador do formulário de foto
        document.getElementById('photoForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData();
            const photoInput = document.getElementById('photoInput');
            
            if (photoInput.files[0]) {
                formData.append('profile_photo', photoInput.files[0]);
                
                fetch('api/profile/photo/upload', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Atualizar a foto na página
                        document.getElementById('profilePhoto').src = data.photo_url + '?t=' + Date.now();
                        document.querySelector('.header-profile .profile-pic').src = data.photo_url + '?t=' + Date.now();
                        closePhotoModal();
                        showNotification('Foto atualizada com sucesso!', 'success');
                    } else {
                        showNotification(data.message || 'Erro ao atualizar foto', 'error');
                    }
                })
                .catch(error => {
                    console.error('Erro:', error);
                    showNotification('Erro ao conectar com o servidor', 'error');
                });
            }
        });

        // Fechar modais ao clicar fora
        window.onclick = function(event) {
            const photoModal = document.getElementById('photoModal');
            const deleteModal = document.getElementById('deleteModal');
            
            if (event.target == photoModal) {
                closePhotoModal();
            }
            if (event.target == deleteModal) {
                closeDeleteModal();
            }
        }
    </script>

</body>
</html>