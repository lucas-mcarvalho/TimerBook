<?php
$user_id = $_GET['id'] ?? null;
$user_name = $_GET['user_name'] ?? 'Usuário'; 

if (!$user_id) {
    echo "<h1>Erro: ID do usuário não fornecido.</h1>";
    exit;
}

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Caminhos corrigidos para funcionar via index.php -->
    <link rel="stylesheet" href="style/admin.css"> 
    <link rel="stylesheet" href="style/estatisticaLivros.css">
    <title>Timer Book - Estatísticas de Livros</title>
</head>
<body>

<header class="header">
    <div class="header-logo ">
        <!-- O caminho da imagem deve ser corrigido para funcionar via index.php -->
        <img src="uploads/logo.svg" alt="Logo TimerBook">
        <h1 class="header-title">TimerBook</h1>
        
    </div>
    
    <div class="header-right">
        <!-- Volta para a tela de administração -->
        <a href="#" onclick="history.back();" class="back-button">Voltar</a>
        <!-- Rota para saida do admin -->
        <a href="index.php?action=sair" class="logout-button">sair</a>
    </div>
</header>

<main class="stats-container">
    <h2 class="stats-title">Estatísticas de Livros de <span id="user-name-display"><?= htmlspecialchars($user_name) ?></span></h2>
    
    <div id="books-list" class="books-list">
        <!-- Conteúdo será carregado via JavaScript -->
        <p>Carregando dados...</p>
    </div>
</main>

<script>
    const API_BASE = "http://localhost/TimerBook/public";
    const userId = <?= json_encode($user_id) ?>;

    async function fetchUserName(id) {
        try {
            const response = await fetch(`${API_BASE}/users/${id}`);
            const data = await response.json();
            if (response.ok && data.nome) {
                document.getElementById('user-name-display').textContent = data.nome;
            }
        } catch (error) {
            console.error("Erro ao buscar nome do usuário:", error);
        }
    }

    async function loadBookStatistics() {
        const listContainer = document.getElementById("books-list");
        if (!userId) {
            listContainer.innerHTML = "<p class='error'>ID do usuário não encontrado.</p>";
            return;
        }

        // Busca o nome do usuário em paralelo
        fetchUserName(userId);

        try {
            const response = await fetch(`${API_BASE}/reading/statistics/${userId}`, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                }
            });
            const data = await response.json();

            if (!response.ok) {
                listContainer.innerHTML = `<p class='error'>Erro ao carregar estatísticas: ${data.error || 'Erro desconhecido'}</p>`;
                return;
            }

            if (data.length === 0) {
                listContainer.innerHTML = "<p>Nenhum livro encontrado para este usuário.</p>";
                return;
            }

            listContainer.innerHTML = '';

            data.forEach(book => {
                const bookItem = document.createElement('div');
                bookItem.className = 'book-item';
                
                // Formatação de data e tempo
                const formatDate = (dateString) => dateString ? new Date(dateString).toLocaleDateString('pt-BR') : 'N/A';
                const formatTime = (seconds) => {
                    if (seconds === null || seconds === undefined || isNaN(seconds)) return '0h 0m';
                    const h = Math.floor(seconds / 3600);
                    const m = Math.floor((seconds % 3600) / 60);
                    return `${h}h ${m}m`;
                };

                bookItem.innerHTML = `
                    <div class="book-cover-col">
                        <img src="${book.capa_livro || 'uploads/default_cover.png'}" alt="Capa do Livro: ${book.titulo}" class="book-cover">
                    </div>
                    <div class="book-details-col">
                        <h3 class="book-title">${book.titulo}</h3>
                        <p><strong>Autor:</strong> ${book.autor}</p>
                        <p><strong>Ano:</strong> ${book.ano_publicacao}</p>
                        <p><strong>Status:</strong> <span class="status-badge status-${book.status.toLowerCase().replace(/ /g, '-')}">${book.status}</span></p>
                        <p><strong>Tempo gasto:</strong> ${formatTime(book.tempo_gasto)}</p>
                        <p><strong>Páginas lidas:</strong> ${book.paginas_lidas}</p>
                        <p><strong>Data início:</strong> ${formatDate(book.data_inicio)}</p>
                        <p><strong>Data fim:</strong> ${formatDate(book.data_fim)}</p>
                    </div>
                `;
                listContainer.appendChild(bookItem);
            });

        } catch (error) {
            console.error("Erro na comunicação com a API:", error);
            listContainer.innerHTML = `<p class='error'>Falha na comunicação com o servidor: ${error.message}</p>`;
        }
    }

    // Chama a função ao carregar a página
    window.onload = loadBookStatistics;
</script>
</body>
</html>