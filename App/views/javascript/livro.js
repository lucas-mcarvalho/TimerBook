
async function listarLivros() {
    try {
        const res = await fetch("http://localhost/TimerBook/public/my-books", {
            method: "GET",
            headers: { "Content-Type": "application/json" }
        });
        if (!res.ok) {
            throw new Error(`Erro na requisição: ${res.status}`);
        }
        const livros = await res.json();
        console.log(livros);

        const divLivros = document.getElementById("book-list");

        livros.forEach(livro => {
    divLivros.innerHTML += `
        <div class="livro-card">
            <p class="titulo">${livro.titulo}</p>
            <p class="autor">${livro.autor}</p>
            <a class="link_livro" href="${livro.caminho_arquivo}" target="_blank">
                 <img src="${livro.capa_arquivo}" alt="Capa do livro">
            </a>
        </div>
    `;
});

        
        
    } catch (error) {
        console.error("Erro ao buscar livros:", error);
    }
}

listarLivros(); // chama a função