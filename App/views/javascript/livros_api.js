async function cadastrarLivro() {

    const form = document.getElementById("cadastro-livro-form");

    form.addEventListener("submit", async (event) => {
    event.preventDefault();

    const formData = new FormData(form);
    
    console.log(formData); // Debug: Verifica os dados do formulário

    try {
        const res = await fetch("http://15.228.179.50/TimerBook/public/books", {
            method: "POST",
            body: formData,
            credentials: "include" // <- envia cookies de sessão
        });

        if (!res.ok) {
            throw new Error(`Erro na requisição: ${res.status}`);
        }

        const resultado = await res.json();
        console.log("Livro cadastrado com sucesso:", resultado);
        alert("Livro cadastrado com sucesso!");
        form.reset(); // limpa o formulário
        } catch (error) {
            console.error("Erro ao cadastrar livro:", error);
            alert("Erro ao cadastrar livro. Por favor, tente novamente.");
        }
    });

    const photoInput = document.getElementById('capa_arquivo');
    const profilePicPreview = document.getElementById('capaPreview');

    // Salva a imagem padrão
    const defaultImage = profilePicPreview.src;

    // Preview da imagem escolhida
    photoInput.addEventListener('change', () => {
    //Pega o primeiro arquivo selecionado   
    const file = photoInput.files[0];
    //Faz a verificação se o arquivo existe   
    if (file) {
        //Cria um objeto FileReader que ler o conteúdo do arquivo    
        const reader = new FileReader();
        //A foto recebe o conteúdo do reader    
        reader.onload = e => {
        profilePicPreview.src = e.target.result;
    };
    //Inicia a leitura e transforma em uma DataURL    
        reader.readAsDataURL(file);
    } else {
    profilePicPreview.src = defaultImage;
    }   
    }); 
}

async function deletarLivro(id) {
    //Pede a confirmação ao usuário para realmente apagar o livro 
    const confirmar = confirm("Tem certeza que deseja excluir este livro?");
    if (!confirmar) return; 

    try {
        //Inicia a requisição HTTP
        const res = await fetch(`http://15.228.179.50/TimerBook/public/books/${id}`, {
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
        const res = await fetch("http://15.228.179.50/TimerBook/public/my-books", {
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


//listarLivros(); // chama a função


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