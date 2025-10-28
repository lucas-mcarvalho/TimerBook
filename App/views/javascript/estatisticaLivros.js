// js/estatisticaLivros.js

(function () {
    // A variável USER_ID e API_BASE são definidas em estatisticaLivros.php

    function formatarTempo(segundos) {
        if (segundos === 0) return "0 segundos";
        const horas = Math.floor(segundos / 3600);
        const minutos = Math.floor((segundos % 3600) / 60);
        const segs = segundos % 60;
        
        let tempoFormatado = [];
        if (horas > 0) tempoFormatado.push(`${horas}h`);
        if (minutos > 0) tempoFormatado.push(`${minutos}m`);
        if (segs > 0 || tempoFormatado.length === 0) tempoFormatado.push(`${segs}s`);

        return tempoFormatado.join(" ");
    }

    function formatarData(dataString) {
        if (!dataString || dataString === '0000-00-00 00:00:00' || dataString === '0000-00-00') return "N/A";
        // Tenta formatar para DD/MM/AAAA
        try {
            const date = new Date(dataString);
            // Verifica se a data é válida
            if (isNaN(date)) return dataString;

            const dia = String(date.getDate()).padStart(2, '0');
            const mes = String(date.getMonth() + 1).padStart(2, '0');
            const ano = date.getFullYear();
            return `${dia}/${mes}/${ano}`;
        } catch (e) {
            return dataString;
        }
    }

    function renderBookCard(book) {
        const bookCard = document.createElement('div');
        bookCard.className = 'book-card';

        const statusClass = book.status.replace(/\s/g, '-'); // "Em andamento" -> "Em-andamento"

        bookCard.innerHTML = `
            <div class="book-content">
                <div class="book-cover-column">
                    <img src="${book.capa_livro || 'placeholder_capa.png'}" alt="Capa do Livro" class="book-cover">
                </div>
                <div class="book-info-column">
                    <h3>${book.titulo}</h3>
                    <p><strong>Autor:</strong> ${book.autor || 'N/A'}</p>
                    <p><strong>Ano:</strong> ${book.ano_publicacao || 'N/A'}</p>
                    <p><strong>Status:</strong> <span class="status-badge status-${statusClass}">${book.status}</span></p>
                    <p><strong>Tempo Gasto:</strong> ${formatarTempo(book.tempo_gasto)}</p>
                    <p><strong>Páginas Lidas:</strong> ${book.paginas_lidas}</p>
                    <p><strong>Início:</strong> ${formatarData(book.data_inicio)}</p>
                    <p><strong>Fim:</strong> ${formatarData(book.data_fim)}</p>
                </div>
            </div>
        `;

        return bookCard;
    }

    async function carregarEstatisticas() {
        const loading = document.getElementById('loading');
        const errorDiv = document.getElementById('error-message');
        const container = document.getElementById('books-container');

        loading.style.display = 'block';
        errorDiv.style.display = 'none';
        container.innerHTML = '';

        if (!window.USER_ID) {
            errorDiv.textContent = "Erro: ID do usuário não foi passado corretamente.";
            errorDiv.style.display = 'block';
            loading.style.display = 'none';
            return;
        }

        try {
            // CORRIGIDO: Usando a rota via api.php para garantir o roteamento correto
            const url = `${API_BASE}/api.php?action=book/statistics&user_id=${window.USER_ID}`;
            const response = await fetch(url);
            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.error || "Falha ao carregar dados do servidor.");
            }

            if (data.length === 0) {
                container.innerHTML = '<p class="loading-message">Este usuário ainda não possui livros cadastrados.</p>';
            } else {
                data.forEach(book => {
                    container.appendChild(renderBookCard(book));
                });
            }

        } catch (error) {
            console.error("Erro ao carregar estatísticas:", error);
            errorDiv.textContent = `Erro ao carregar estatísticas: ${error.message}`;
            errorDiv.style.display = 'block';
        } finally {
            loading.style.display = 'none';
        }
    }

    // Expor a função para ser chamada pelo HTML
    window.carregarEstatisticas = carregarEstatisticas;
})();