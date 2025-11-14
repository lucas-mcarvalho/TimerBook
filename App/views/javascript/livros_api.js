async function cadastrarLivro() {
    const form = document.getElementById("cadastro-livro-form");

    form.addEventListener("submit", async (event) => {
    event.preventDefault();

    const formData = new FormData(form);
    
    console.log(formData); // Debug: Verifica os dados do formulário

    try {
        const res = await fetch("http://localhost:8080/books", {
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
        const res = await fetch(`http://localhost:8080/books/${id}`, {
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



async function listarLivrosUsuario(user_id) {
    try {
        const res = await fetch(`http://localhost:8080/my-books`, {
            method: "GET",
            headers: { "Content-Type": "application/json" },
            credentials: "include"
        });

        if (!res.ok) {
            throw new Error(`Erro na requisição: ${res.status}`);
        }

        const livros = await res.json();
        const divLivros = document.getElementById("book-list");
        divLivros.innerHTML = "";

        console.log(livros); // Debug: Verifica os livros recebidos

        livros.forEach(livro => {
            divLivros.innerHTML += `
                <div class="livro-card" id="livro-${livro.id}">
                    <p class="titulo">${livro.titulo}</p>
                    <p class="autor">${livro.autor}</p>
                    <a class="link_livro" href="${livro.caminho_arquivo}" target="_blank">
                        <img src="${livro.capa_livro}" alt="Capa do livro">
                    </a>
                    <div class="action-buttons">
                        <button id="read-button" class="read-button">Ler</button>
                        <form action="index.php?action=editar_livro" method="POST" style="margin: 0;"> <input type="hidden" name="id_livro" value="${livro.id}">
                            <button class="edit-button">Editar</button>
                        </form>
                        <button class="delete-button" onclick="deletarLivro(${livro.id})">Excluir</button>  
                    </div>
                    </div>
            `;
        });

        const readButtons = divLivros.querySelectorAll(".read-button");
        readButtons.forEach(button => {
            button.addEventListener("click", async (e) => {
                const book_id = e.target.closest('.livro-card').id.split('-')[1];
                const data = await iniciarSessaoLeitura(user_id, book_id);
                if (!data) {
                    alert("Erro ao iniciar a sessão de leitura.");
                    return;
                }
                const leitura_id = data.leitura_id;
                const sessao_id = data.sessao_id;

                window.location.href = `/TimerBook/App/views/html/leitorPdf.php?id=${book_id}&leitura_id=${leitura_id}&sessao_id=${sessao_id}`;
            });
        });
    } catch (error) {
        console.error("Erro ao buscar livros:", error);
    }
}
// rologem da barra de visualisação de livro 
    document.addEventListener('DOMContentLoaded', () => {
    const divLivros = document.getElementById("book-list");

    // Se tiver userId na query string (admin)
    const urlParams = new URLSearchParams(window.location.search);
    const userId = urlParams.get('userId');

    // Define endpoint
    //const endpoint = userId ? `books?user_id=${userId}` : `my-books`;

    //listarLivros(endpoint);

    // Rolagem da barra
    const prevButton = document.getElementById('prev-button');
    const nextButton = document.getElementById('next-button');

    if (prevButton && nextButton) {
        prevButton.addEventListener('click', () => {
            divLivros.scrollBy({ left: -200, behavior: 'smooth' });
        });

        nextButton.addEventListener('click', () => {
            divLivros.scrollBy({ left: 200, behavior: 'smooth' });
        });
    }
});

async function editarLivro(id_livro) {
    const form = document.getElementById("editUserForm");

    form.addEventListener("submit", async (event) => {
        event.preventDefault(); // previne reload

        // Cria o FormData somente **quando o usuário clica em submit**
        const formData = new FormData(form);

        // Debug: verifica os dados preenchidos
        console.log(formData);

        try {
            const res = await fetch(`http://localhost:8080/books/${id_livro}`, {
                method: "POST",
                body: formData,
                credentials: "include"
            });

            if (!res.ok) {
                throw new Error(`Erro na requisição: ${res.status}`);
            }

            const resultado = await res.json();
            console.log("Livro atualizado com sucesso:", resultado);
            alert("Livro atualizado com sucesso!");
        } catch (error) {
            console.error("Erro ao atualizar livro:", error);
            alert("Erro ao atualizar livro. Por favor, tente novamente.");
        }
    });
}

async function listarLivrosAdmin(id) {
    try {
        const res = await fetch(`http://localhost:8080/books/user/${id}`, {
            method: "GET",
            headers: { "Content-Type": "application/json" },
            credentials: "include"
        });

        if (!res.ok) {
            throw new Error(`Erro na requisição: ${res.status}`);
        }

        const livros = await res.json();
        const divLivros = document.getElementById("book-list");
        divLivros.innerHTML = "";

        console.log(livros); // Debug: Verifica os livros recebidos

        livros.forEach(livro => {
            divLivros.innerHTML += `
                <div class="livro-card" id="livro-${livro.id}">
                    <p class="titulo">${livro.titulo}</p>
                    <p class="autor">${livro.autor}</p>
                    <a class="link_livro" href="${livro.caminho_arquivo}" target="_blank">
                        <img src="${livro.capa_livro}" alt="Capa do livro">
                    </a>
                    <div class="action-buttons">
                        <form action="index.php?action=editar_livro" method="POST" style="margin: 0;"> <input type="hidden" name="id_livro" value="${livro.id}">
                            <button class="edit-button">Editar</button>
                        </form>
                        <button class="delete-button" onclick="deletarLivro(${livro.id})">Excluir</button>  
                    </div>
                    </div>
            `;
        });
    } catch (error) {
        console.error("Erro ao buscar livros:", error);
    }
}
// rologem da barra de visualisação de livro 
    document.addEventListener('DOMContentLoaded', () => {
    const divLivros = document.getElementById("book-list");
    const urlParams = new URLSearchParams(window.location.search);
    const userId = urlParams.get('userId');
    const prevButton = document.getElementById('prev-button');
    const nextButton = document.getElementById('next-button');

    if (prevButton && nextButton) {
        prevButton.addEventListener('click', () => {
            divLivros.scrollBy({ left: -200, behavior: 'smooth' });
        });

        nextButton.addEventListener('click', () => {
            divLivros.scrollBy({ left: 200, behavior: 'smooth' });
        });
    }
});

async function editarLivro(id_livro) {
    const form = document.getElementById("editUserForm");

    form.addEventListener("submit", async (event) => {
        event.preventDefault(); // previne reload

        // Cria o FormData somente **quando o usuário clica em submit**
        const formData = new FormData(form);

        // Debug: verifica os dados preenchidos
        console.log(formData);

        try {
            const res = await fetch(`http://localhost:8080/books/${id_livro}`, {
                method: "POST",
                body: formData,
                credentials: "include"
            });

            if (!res.ok) {
                throw new Error(`Erro na requisição: ${res.status}`);
            }

            const resultado = await res.json();
            console.log("Livro atualizado com sucesso:", resultado);
            alert("Livro atualizado com sucesso!");
        } catch (error) {
            console.error("Erro ao atualizar livro:", error);
            alert("Erro ao atualizar livro. Por favor, tente novamente.");
        }
    });
}