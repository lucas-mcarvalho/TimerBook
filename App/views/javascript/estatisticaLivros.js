// js/estatisticaLivros.js

(function () {
    const API_BASE = "http://localhost/TimerBook/public";

    function formatarTempo(segundos ) {
        if (segundos === 0 || segundos === null || segundos === undefined || isNaN(segundos)) return "0h 0m";
        const horas = Math.floor(segundos / 3600);
        const minutos = Math.floor((segundos % 3600) / 60);
        
        return `${horas}h ${minutos}m`;
    }

    function formatarData(dataString) {
        if (!dataString || dataString === '0000-00-00 00:00:00' || dataString === '0000-00-00') return "N/A";
        // Tenta formatar para DD/MM/AAAA
        try {
            const date = new Date(dataString);
            // Verifica se a data é válida
            if (isNaN(date.getTime())) return "N/A"; // Corrigido: checar com getTime

            const dia = String(date.getDate()).padStart(2, '0');
            const mes = String(date.getMonth() + 1).padStart(2, '0');
            const ano = date.getFullYear();
            return `${dia}/${mes}/${ano}`;
        } catch (e) {
            return dataString; // Retorna o original se falhar
        }
    }

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

    function renderBookCard(book) {
        const bookItem = document.createElement('div');
        bookItem.className = 'book-item';
        
        const statusClass = book.status ? book.status.toLowerCase().replace(/ /g, '-') : 'indefinido';

        bookItem.innerHTML = `
            <div class="book-cover-col">
                <img src="${book.capa_livro || 'uploads/default_cover.png'}" alt="Capa do Livro: ${book.titulo}" class="book-cover">
            </div>
            <div class="book-details-col">
                <h3 class="book-title">${book.titulo}</h3>
                <p><strong>Autor:</strong> ${book.autor}</p>
                <p><strong>Ano:</strong> ${book.ano_publicacao}</p>
                <p><strong>Status:</strong> <span class="status-badge status-${statusClass}">${book.status || 'N/A'}</span></p>
                <p><strong>Tempo gasto:</strong> ${formatarTempo(book.tempo_gasto)}</p>
                <p><strong>Páginas lidas:</strong> ${book.paginas_lidas}</p>
                <p><strong>Data início:</strong> ${formatarData(book.data_inicio)}</p>
                <p><strong>Data fim:</strong> ${formatarData(book.data_fim)}</p>
            </div>
        `;

        return bookItem;
    }

    async function carregarEstatisticas(userId, userName) {
        const listContainer = document.getElementById("books-list");
        const loading = document.getElementById('loading');
        const errorDiv = document.getElementById('error-message');

        loading.style.display = 'block';
        errorDiv.style.display = 'none';
        
        listContainer.innerHTML = ''; 

        if (!userId) {
            errorDiv.textContent = "Erro: ID do usuário não foi passado corretamente.";
            errorDiv.style.display = 'block';
            loading.style.display = 'none';
            return;
        }

        // Atualiza o nome do usuário no título
        if(userName) {
             document.getElementById('user-name-display').textContent = userName;
        } else {
             // Busca o nome do usuário em paralelo se não foi passado
             fetchUserName(userId);
        }


        try {
            const response = await fetch(`${API_BASE}/reading/statistics/${userId}`, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                }
            });
            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.error || 'Erro desconhecido');
            }

            if (data.length === 0) {
                listContainer.innerHTML = "<p>Nenhum livro encontrado para este usuário.</p>";
            } else {
                listContainer.innerHTML = ''; // Limpa (caso tenha a msg de "nenhum livro")
                data.forEach(book => {
                    listContainer.appendChild(renderBookCard(book));
                });
            }

        } catch (error) {
            console.error("Erro na comunicação com a API:", error);
            errorDiv.textContent = `Falha na comunicação com o servidor: ${error.message}`;
            errorDiv.style.display = 'block';
        } finally {
            loading.style.display = 'none';
        }
    }

    // Expor a função para ser chamada pelo HTML
    window.carregarEstatisticas = carregarEstatisticas;
})();