async function deletarLivro(id) {
    const confirmar = confirm("Tem certeza que deseja excluir este livro?");
    if (!confirmar) return; 

    try {
        const res = await fetch(`http://localhost/TimerBook/public/books/${id}`, {
            method: "DELETE",
            headers: { "Content-Type": "application/json" }
        });

        if (!res.ok) {
            throw new Error(`Erro ao deletar: ${res.status}`);
        }

        // Se a API retornou sucesso, remove o livro do DOM
        const livroCard = document.getElementById(`livro-${id}`);
        if (livroCard) {
            livroCard.remove();
        }

        alert("Livro excluído com sucesso!");
    } catch (error) {
        console.error(error);
        alert("Ocorreu um erro ao tentar excluir o livro.");
    }
}



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
        <div class="livro-card" id="livro-${livro.id}">
            <p class="titulo">${livro.titulo}</p>
            <p class="autor">${livro.autor}</p>
            <a class="link_livro" href="${livro.caminho_arquivo}" target="_blank">
                 <img src="${livro.capa_livro}" alt="Capa do livro">
            </a>
            <button class="delete-button" onclick="deletarLivro(${livro.id})">Excluir</button>
        </div>
    `;
});

        
        
    } catch (error) {
        console.error("Erro ao buscar livros:", error);
    }
}


listarLivros(); // chama a função