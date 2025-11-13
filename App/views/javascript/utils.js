async function buscarLivro($id_livro) {
    try {
        const response = await fetch(`http://15.228.179.50/TimerBook/public/books/${$id_livro}`, {
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
        const response = await fetch(`http://15.228.179.50/TimerBook/public/reading/book/${id_livro}/sessions`, {
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
        const response = await fetch(`http://15.228.179.50/TimerBook/public/reading/book/${id_livro}/sessions`, {
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

async function buscarUltimaPagina(id_user) {
    try {
        const response = await fetch(`http://15.228.179.50/TimerBook/public/reading/statistics/${id_user}`, {
            method: "GET",
            credentials: "include"
        });
        
        if (!response.ok) {
            throw new Error("Erro ao buscar estatisticas do usuário.");
        }
        const stats = await response.json();
        if(stats[0].paginas_lidas != null){
            return stats[0].paginas_lidas;
        }
        return stats[1].paginas_lidas;
        
    } catch (error) {
        console.error("Erro na função buscarUltimaPagina:", error);
        throw error;
    }
}