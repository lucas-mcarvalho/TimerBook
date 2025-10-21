async function cadastrarLivro() {

    const form = document.getElementById("cadastro-livro-form");

    form.addEventListener("submit", async (event) => {
    event.preventDefault();

    const formData = new FormData(form);
    
    console.log(formData); // Debug: Verifica os dados do formulário

    try {
        const res = await fetch("http://localhost/TimerBook/public/books", {
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
        const res = await fetch(`http://localhost/TimerBook/public/books/${id}`, {
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



async function listarLivros(endpoint) {
    try {
        const res = await fetch(`http://localhost/TimerBook/public/${endpoint}`, {
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
    const divLivros = document.getElementById("book-list");

    // Se tiver userId na query string (admin)
    const urlParams = new URLSearchParams(window.location.search);
    const userId = urlParams.get('userId');

    // Define endpoint
    const endpoint = userId ? `books?user_id=${userId}` : `my-books`;

    listarLivros(endpoint);

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

    // js/livros_api.js
(async function () {
  const API_BASE = "http://localhost/TimerBook/public";

  // ==========================
  // LISTAR LIVROS DO USUÁRIO
  // ==========================
async function listarLivrosAdmin() {
  const urlParams = new URLSearchParams(window.location.search);
  const userId = urlParams.get("userId");
  const tabela = document.querySelector(".books-table tbody");

  if (!userId) {
    tabela.innerHTML = `<tr><td colspan="4">❌ ID do usuário não informado na URL.</td></tr>`;
    return;
  }

  try {
    const res = await fetch(`http://localhost/TimerBook/public/books?user_id=${userId}`, {
      cache: "no-store"
    });
    const data = await res.json();

    if (!res.ok) throw new Error(data.error || "Erro ao buscar livros.");

    if (!Array.isArray(data) || data.length === 0) {
      tabela.innerHTML = `<tr><td colspan="4">Nenhum livro encontrado para este usuário.</td></tr>`;
      return;
    }

    tabela.innerHTML = data.map(livro => `
      <tr>
        <td><img src="${livro.capa_arquivo || 'uploads/placeholder.png'}" alt="Capa" width="60"></td>
        <td>${livro.titulo || '(Sem título)'}</td>
        <td>${livro.autor || '(Desconhecido)'}</td>
        <td>

          <button onclick="editarLivro(${livro.id})">Editar</button>
          <button onclick="deletarLivro(${livro.id})">Excluir</button>
        </td>
      </tr>
    `).join("");
  } catch (err) {
    console.error("Erro ao listar livros:", err);
    tabela.innerHTML = `<tr><td colspan="4"> Falha ao carregar livros: ${err.message}</td></tr>`;
  }
}


  // ==========================
  // EXCLUIR LIVRO
  // ==========================
  async function deletarLivro(id) {
    if (!confirm("Tem certeza que deseja excluir este livro?")) return;

    try {
      const res = await fetch(`${API_BASE}/livros/${id}`, { method: "DELETE" });
      if (!res.ok) throw new Error("Erro ao deletar livro");
      alert("Livro excluído com sucesso!");
      listarLivrosAdmin(); // recarrega a lista
    } catch (err) {
      alert("Erro ao excluir livro: " + err.message);
    }
  }

  function escapeHtml(str) {
    return String(str)
      .replace(/&/g, "&amp;")
      .replace(/"/g, "&quot;")
      .replace(/'/g, "&#39;")
      .replace(/</g, "&lt;")
      .replace(/>/g, "&gt;");
  }

  // Exposição global
  window.listarLivrosAdmin = listarLivrosAdmin;
})();

});
