async function cadastrarUsuario() {
    // Seleciona os elementos do HTML com os quais vamos interagir.
    const form = document.querySelector('#registerForm');
    const msg = document.getElementById("msg");

    const photoInput = document.getElementById('photo');
    const profilePicPreview = document.getElementById('profilePreview');
    const resetBtn = document.getElementById('resetPhoto');

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

    // Resetar manualmente
    resetBtn.addEventListener('click', () => {
    photoInput.value = '';              // limpa seleção do input
    profilePicPreview.src = defaultImage; // volta para imagem padrão
    });


    // Adiciona um "escutador" que aguarda o evento de 'submit' (envio) do formulário.
    form.addEventListener('submit', async event => {

    event.preventDefault();
    // Cria um objeto FormData, que captura todos os dados dos campos do formulário
    const formData = new FormData(form);
    
    
    try{
    // Inicia a requisição para a API usando fetch e aguarda a resposta.
    const res = await fetch("http://localhost/TimerBook/public/register", {
        method: 'POST',
        body: formData
    });

    // Converte a resposta da API (que vem em formato JSON) para um objeto JavaScript.
    const response = await res.json(); 


    msg.innerText = response.message || response.error;
    msg.style.color = response.message ? "green" : "red";

    // Se a requisição foi bem-sucedida (indicado pela presença de 'response.message'), limpa o formulário.
    if (response.message){
        form.reset();
    }
    } catch(err){
        // Se ocorrer um erro na comunicação com a API (ex: servidor offline), este bloco é executado.
        msg.innerText = "Erro ao conectar com a API. ";
        msg.style.color = "red";
    }
    });

    /* teste  */

    document.addEventListener('DOMContentLoaded', () => {

        const radioButtons = document.querySelectorAll('input[name="radio-btn"]');
        let currentSlide = 0;

        function nextSlide() {
            currentSlide++;
            if (currentSlide > radioButtons.length - 1) {
                currentSlide = 0;
            }
            radioButtons[currentSlide].checked = true;
        }

        // Passa a imagem a cada 4 segundos
        setInterval(nextSlide, 4000);

    });
}

async function loginUsuario() {
    //SERVE PARA NAO DEIXAR A PAGINA RECARREGAR AO SUBMETER O FORMULARIO
    document.addEventListener("DOMContentLoaded", () => {
    const form = document.getElementById("loginForm");

    //ADICIONA OUVINTE DE EVENTO NO FORMULARIO
    form.addEventListener("submit", async (e) => {
        e.preventDefault(); // impede reload da página

        //PEGA OS VALORES DOS CAMPOS EMAIL E PASSWORD DO HTML
        const email = document.getElementById("email").value;
        const password = document.getElementById("password").value;

        try {
            // 1) Tenta login como ADMIN
            const adminRes = await fetch("http://localhost/TimerBook/public/admin/login", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ email, password })
            });

            if (adminRes.ok) {
                const adminData = await adminRes.json();
                // opcional: armazenar admin no localStorage
                localStorage.setItem("admin", JSON.stringify(adminData.admin));
                // Redireciona para o painel do admin
                window.location.href = "index.php?action=admin";
                return;
            }

            // 2) Se não for admin (ex.: 401), tenta login como USUÁRIO comum
            const userRes = await fetch("http://localhost/TimerBook/public/login", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ email, password })
            });

            const data = await userRes.json();
            if (userRes.ok) {
                localStorage.setItem("user", JSON.stringify(data.user));
                window.location.href = "index.php?action=home";
            } else {
                alert(data.error || "E-mail ou senha inválidos");
            }
            } catch (err) {
            console.error("Erro:", err);
            alert("Falha ao conectar com o servidor");
            }
        });
    });
}


async function redefinirSenha(){
    
    document.addEventListener("DOMContentLoaded", () => {
        const form = document.getElementById("forgotForm");
        const responseDiv = document.getElementById("forgotResponse");

        form.addEventListener("submit", async (e) => {
            e.preventDefault();
            responseDiv.innerText = "Enviando...";

            const email = document.getElementById("email").value;

            try {
                const res = await fetch("http://localhost/TimerBook/public/forgot-password", {
                    method: "POST",
                    headers: { "Content-Type": "application/x-www-form-urlencoded" },
                    body: "email=" + encodeURIComponent(email)
                });
                const data = await res.json();
                if (data.success) {
                    responseDiv.innerText = "Se o e-mail existir, um link foi enviado!";
                    responseDiv.style.color = "green";
                } else if (data.error) {
                    responseDiv.innerText = data.error;
                    responseDiv.style.color = "red";
                } else {
                    responseDiv.innerText = "Erro inesperado.";
                    responseDiv.style.color = "red";
                }
            } catch (err) {
                responseDiv.innerText = "Erro ao conectar com o servidor.";
                responseDiv.style.color = "red";
            }
        });
    });
}