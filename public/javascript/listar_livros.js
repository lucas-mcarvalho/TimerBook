async function deletarLivro(id) {
    //Pede a confirmação ao usuário para realmente apagar o livro 
    const confirmar = confirm("Tem certeza que deseja excluir este livro?");
    if (!confirmar) return; 

    try {
        //Inicia a requisição HTTP
        const res = await fetch(`http://localhost:8080/login/books/${id}`, {
            method: "DELETE",
            headers: { "Content-Type": "application/json" }
        });
        //Checa se deu algum erro na hora da deleção
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
        const res = await fetch("http://localhost:8080/my-books", {
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

            <a href=""><button class="edit-button" data-id="${livro.id}">Editar</button></a>
        </div>
    `;
});

        
        
    } catch (error) {
        console.error("Erro ao buscar livros:", error);
    }
}


listarLivros(); // chama a função


// rologem da barra de visualisação de livro 
document.addEventListener('DOMContentLoaded', () => {
    const bookList = document.getElementById('book-list');
    const prevButton = document.getElementById('prev-button');
    const nextButton = document.getElementById('next-button');

    // Verifica se os elementos existem para evitar erros
    if (bookList && prevButton && nextButton) {
        // Rola a lista para a esquerda quando o botão anterior é clicado
        prevButton.addEventListener('click', () => {
            bookList.scrollBy({
                left: -200, // Ajuste este valor para a distância de rolagem desejada
                behavior: 'smooth'
            });
        });

        // Rola a lista para a direita quando o botão próximo é clicado
        nextButton.addEventListener('click', () => {
            bookList.scrollBy({
                left: 200, // Ajuste este valor para a distância de rolagem desejada
                behavior: 'smooth'
            });
        });
    }
});