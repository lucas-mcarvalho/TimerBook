// js/estatisticaLivros.js


async function iniciarSessaoLeitura(user_id, book_id) {
    
    try {
        const response = await fetch("http://localhost/TimerBook/public/reading/start", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                // Dados necessários para iniciar a sessão de leitura
                user_id: user_id, // Exemplo de ID do livro
                book_id: book_id // Exemplo de ID do livro
            })
        });
        const data = await response.json();

        const leitura_id = data.leitura_id;
        const sessao_id = data.sessao_id;

        if (response.ok) {
            console.log("Sessão de leitura iniciada com sucesso:", leitura_id, sessao_id);
            return {leitura_id, sessao_id};
        } else {
            console.error("Erro ao iniciar a sessão de leitura:", data.error);
        }   
    }     catch (error) {
        console.error("Erro na comunicação com a API:", error);
    }   
}

async function finalizarSessaoLeitura(sessao_id, leitura_id, paginas_lidas, id_livro) {
    const ultimaPaginaLida = await buscarUltimaPagina(user_id);
    if(ultimaPaginaLida){
        console.log("Ultima Página Lida", ultimaPaginaLida);
        if(paginas_lidas > ultimaPaginaLida){
            let pg_ant = paginas_lidas;
            paginas_lidas = paginas_lidas - ultimaPaginaLida;
            console.log("O usuário estava na página ", ultimaPaginaLida, " E avançou até ", pg_ant, "Portanto ele leu ", paginas_lidas);
        }
        else{
            paginas_lidas = 0;
        }
    }


    try {
        const response = await fetch("http://localhost/TimerBook/public/reading/finish", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                // Dados necessários para finalizar a sessão de leitura
                leitura_id: leitura_id, 
                sessao_id: sessao_id,
                paginas_lidas: paginas_lidas,
            })
        });
        const data = await response.json();

        if (response.ok) {
            console.log("Sessão de leitura finalizada com sucesso:", data);
        } else {
            console.error("Erro ao finalizar a sessão de leitura:", data.error);
        }   
    }catch (error) {
        console.error("Erro na comunicação com a API:", error);
    }
}


(async function () {
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
        console.log(book);
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
    function renderEstatisticasGerais(stats) {
        const statsDisplay = document.getElementById('general-stats-display');
        statsDisplay.innerHTML = ''; // Limpa o conteúdo

        if (!stats) {
            statsDisplay.innerHTML = "<p>Nenhum dado estatístico geral encontrado.</p>";
            return;
        }

        // Estrutura de exibição dos dados (pode ser ajustada via CSS)
        statsDisplay.innerHTML = `
            <div class="stat-card">
                <h4>Livros Totais</h4>
                <p class="stat-value">${stats.total_livros || 0}</p>
            </div>
            <div class="stat-card">
                <h4>Tempo Total de Leitura</h4>
                <p class="stat-value">${formatarTempo(stats.tempo_total)}</p>
            </div>
            <div class="stat-card">
                <h4>Total de Páginas Lidas</h4>
                <p class="stat-value">${stats.paginas_total || 0}</p>
            </div>

            <div class="stat-card">
                <h4>Média de Páginas Lidas por Livro</h4>
                <p class="stat-value">${stats.media_paginas_por_livro || 0}</p>
            </div>
        `;
    }
    async function estatisticasGeraisUsuario(user_id){
        const generalLoading = document.getElementById('general-stats-loading');
        const generalError = document.getElementById('general-stats-error');
        
        generalLoading.style.display = 'block';
        generalError.style.display = 'none';

        try {
            console.log("Buscando estatísticas gerais para o usuário ID:", user_id);
            const response = await fetch(`http://localhost/TimerBook/public/reading/totals/${user_id}`, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                }
            });
            const data = await response.json();

            if (response.ok) {
                console.log("Estatísticas gerais obtidas com sucesso:", data);
                // CHAMA A FUNÇÃO DE RENDERIZAÇÃO
                renderEstatisticasGerais(data); 
                return data;
            } else {
                const errorMessage = data.error || "Erro desconhecido ao obter estatísticas gerais.";
                console.error("Erro ao obter as estatísticas gerais:", errorMessage);
                generalError.textContent = `Erro ao carregar dados: ${errorMessage}`;
                generalError.style.display = 'block';
            }   
        } catch (error) {
            console.error("Erro na comunicação com a API:", error);
            generalError.textContent = `Falha na comunicação com o servidor: ${error.message}`;
            generalError.style.display = 'block';
        } finally {
            generalLoading.style.display = 'none';
        }
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
    window.estatisticasGeraisUsuario = estatisticasGeraisUsuario;
})();


/***
async function estatisticasGeraisUsuario(user_id){
    try {
        console.log("Buscando estatísticas gerais para o usuário ID:", user_id);
        const response = await fetch(`http://localhost/TimerBook/public/reading/totals/${user_id}`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
            }
        });
        const data = await response.json();

        if (response.ok) {
            console.log("Estatísticas gerais obtidas com sucesso:", data);
            return data;
        } else {
            console.error("Erro ao obter as estatísticas gerais:", data.error);
        }   
    } catch (error) {
        console.error("Erro na comunicação com a API:", error);
    }
}

***/