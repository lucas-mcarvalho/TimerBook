async function buscarLivro($id_livro) {
    try {
        const response = await fetch(`http://15.228.40.136/TimerBook/public/books/${$id_livro}`, {
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
        const response = await fetch(`http://15.228.40.136/TimerBook/public/reading/book/${id_livro}/sessions`, {
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
        const response = await fetch(`http://15.228.40.136/TimerBook/public/reading/book/${id_livro}/sessions`, {
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

async function iniciarSessaoLeitura(user_id, book_id) {
    try {
        const res = await fetch(`http://15.228.40.136/TimerBook/public/reading/start`, {
	        method: "POST",
	        headers: { "Content-Type": "application/json" },
	        body: JSON.stringify({ user_id, book_id }),
	        credentials: "include"
	    });
	
	    if (!res.ok) {
            throw new Error(`Erro ao iniciar sessão: ${res.status}`);
	    }
	
        const data = await res.json();
	        return data;
	} catch (error) {
	    console.error("Erro ao iniciar sessão de leitura:", error);
	    return null;
	}
}

async function buscarUltimaPagina(id_user, id_livro) {
    //fix
    try {
        const response = await fetch(`http://15.228.40.136/TimerBook/public/reading/statistics/${id_user}`, {
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
        const response = await fetch(`http://http://15.228.40.136/TimerBook/public/reading/statistics/${id_user}`, {
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

        // A lógica de listarLivrosUsuario usa iniciarSessaoLeitura para obter os IDs.
        // Vamos replicar essa lógica aqui para garantir que os IDs sejam válidos.
        //const data = await iniciarSessaoLeitura(id_user, livro.id);
        
        //Classes css que mudam partes específicas do livro como tamanho da img, fonte etc.
        lastBookSection.innerHTML = `
            <div class="book-card-home">
                <div class="book-info-home">
                    <p class="last-read-text">Última Leitura</p>
                    <h3 class="book-title-home">${livro.titulo}</h3>
                    <p class="book-author-home">${livro.autor}</p>
                </div>
                <img src="${livro.capa_livro || 'uploads/default_cover.jpg'}" 
                     alt="Capa do livro" 
                     class="book-cover-home">
                
                <div class="book-actions-home">
                    <button class="action-button-home read-button-home">Ler</button>
                </div>
            </div>
        `;

        const readButton = lastBookSection.querySelector(".read-button-home");
            readButton.addEventListener("click", async () => {
            console.log("Botão Ler clicado!");
            const data = await iniciarSessaoLeitura(id_user, livro.id);
            const leitura_id = data.leitura_id;
            const sessao_id = data.sessao_id;
            const book_id = livro.id;

            window.location.href = `/TimerBook/App/views/html/leitorPdf.php?id=${book_id}&leitura_id=${leitura_id}&sessao_id=${sessao_id}`;    
});

    } catch (error) {
        console.error("Erro ao renderizar último livro:", error);
        lastBookSection.innerHTML = `<p>Erro ao carregar o último livro.</p>`;
    }
}