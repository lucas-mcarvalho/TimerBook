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