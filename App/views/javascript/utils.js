async function buscarLivro($id_livro) {
    try {
        const response = await fetch(`http://localhost/TimerBook/public/books/${$id_livro}`, {
            method: "GET",
            credentials: "include"
        });
        
        if (!response.ok) {
            throw new Error("Erro ao buscar o livro.");
        }
        const livro = await response.json();
        console.log("Livro buscado:", livro); // Debug: Verifica o caminho do arquivo
        return livro;
    } catch (error) {
        console.error("Erro na função buscarLivro:", error);
        throw error;
    }
}

async function buscarUltimaSessao(id_livro){
    try {
        const response = await fetch(`http://localhost/TimerBook/public/reading/book/${id_livro}/sessions`, {
            method: "GET",
            credentials: "include"
        });
        
        if (!response.ok) {
            throw new Error("Erro ao buscar a última sessão.");
        }
        const sessoes = await response.json();
        const ultimaSessao = sessoes.length != 0 ? sessoes[1] : null;
        console.log("Última sessão buscada:", ultimaSessao); // Debug: Verifica o caminho do arquivo
        return ultimaSessao;
    } catch (error) {
        console.error("Erro na função buscarUltimaSessao:", error);
        throw error;
    }
} 

async function buscarSessao(id_livro, index) {
    try {
        const response = await fetch(`http://localhost/TimerBook/public/reading/book/${id_livro}/sessions`, {
            method: "GET",
            credentials: "include"
        });
        
        if (!response.ok) {
            throw new Error("Erro ao buscar a última sessão.");
        }
        const sessoes = await response.json();
        const ultimaSessao = sessoes.length != 0 ? sessoes[index] : null;
        console.log("Última sessão buscada:", ultimaSessao); // Debug: Verifica o caminho do arquivo
        return ultimaSessao;
    } catch (error) {
        console.error("Erro na função buscarUltimaSessao:", error);
        throw error;
    }
}

async function buscarUltimaPagina(id_user, id_livro) {
    try {
        const response = await fetch(`http://localhost/TimerBook/public/reading/statistics/${id_user}`, {
            method: "GET",
            credentials: "include"
        });
        
        if (!response.ok) {
            throw new Error("Erro ao buscar estatisticas do usuário.");
        }

        const stats = await response.json();
        let book;
        for(let b of stats){
            if(b.id == id_livro){
                book = b;
            }
        }
        console.log("status do livro ALSKNAS: ", stats)
        if(book.paginas_lidas != null){
            return book.paginas_lidas;
        }
        return 0;
        
    } catch (error) {
        console.error("Erro na função buscarUltimaPagina:", error);
        throw error;
    }
}



async function buscarEstatisticas(id_user) {
    try {
        const response = await fetch(`http://localhost/TimerBook/public/reading/statistics/${id_user}`, {
            method: "GET",
            credentials: "include"
        });
        
        if (!response.ok) {
            return;
        }
        const stats = await response.json();
        return stats;
    } catch (error) {
        console.error("Erro em Buscar Estatisticas:", error);
        throw error;
    } 
}

async function buscarUltimoLivro(id_user) {
    try {
        const book_stats = await buscarEstatisticas(id_user);
        if (!book_stats || book_stats.length === 0) {
            console.warn("Nenhuma leitura encontrada para o usuário.");
            return null;
        }

        let ultimaSessaoGlobal = null;
        let idLivroUltimo = null;

        for (let stat of book_stats) {
            if (!stat) continue;
            if(stat.data_inicio == null) continue;
            // Busca a última sessão do livro (buscarSessao já faz isso)
            const ultimaSessao = await buscarSessao(stat.id, 0);

            if (!ultimaSessao || !ultimaSessao.data_fim) continue;

            const dataAtual = new Date(ultimaSessao.data_fim);
            const dataUltima = ultimaSessaoGlobal ? new Date(ultimaSessaoGlobal.data_fim) : null;

            // Atualiza se essa for a sessão mais recente
            if (!dataUltima || dataAtual > dataUltima) {
                ultimaSessaoGlobal = ultimaSessao;
                idLivroUltimo = stat.id;
            }
        }

        if (!ultimaSessaoGlobal) {
            console.warn("Nenhuma sessão válida encontrada.");
            return null;
        }


        const livro = await buscarLivro(idLivroUltimo);
        console.log("📖 Último livro lido:", livro);

        return livro;

    } catch (error) {
        console.error("Erro em buscarUltimoLivro:", error);
        throw error;
    }
}

async function mostrarUltimoLivro(id_user) {
    const lastBookSection = document.querySelector(".last-book");
    lastBookSection.innerHTML = ""; // limpa antes de renderizar algo novo

    try {
        const livro = await buscarUltimoLivro(id_user);

        if (!livro) {
            lastBookSection.innerHTML = `<p>Nenhum livro encontrado recentemente.</p>`;
            return;
        }

        //Classes css que mudam partes específicas do livro como tamanho da img, fonte etc.
        lastBookSection.innerHTML = `
            <div class="book-card">
                <img src="${livro.capa_livro || 'uploads/default_cover.jpg'}" 
                     alt="Capa do livro" 
                     class="book-cover">

                <div class="book-info">
                    <h3>${livro.titulo}</h3>
                    <p><strong>Autor:</strong> ${livro.autor}</p>
                    <p><strong>Gênero:</strong> ${livro.genero || 'Desconhecido'}</p>
                </div>
            </div>
        `;

    } catch (error) {
        console.error("Erro ao renderizar último livro:", error);
        lastBookSection.innerHTML = `<p>Erro ao carregar o último livro.</p>`;
    }
}


