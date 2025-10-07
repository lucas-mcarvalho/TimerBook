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
