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

    // Adiciona um "escutador" que aguarda o evento de "submit" (envio) do formulário.
    form.addEventListener("submit", async event => {
        event.preventDefault();
        const formData = new FormData(form);

        try {
            const res = await fetch("http://15.228.179.50/TimerBook/public/register", {
                method: "POST",
                body: formData
            } );

            const response = await res.json();

            if (response.error) {
                msg.innerText = response.error;
                msg.style.color = "red";
            } else {
                msg.innerText = response.message || "Cadastro realizado com sucesso!";
                msg.style.color = "green";
                form.reset();

                // Redireciona para a tela de login após 3 segundos
                setTimeout(() => {
                    window.location.href = "index.php?action=login";
                }, 3000); 
            }
        } catch (err) {
            msg.innerText = "Erro ao conectar com a API.";
            msg.style.color = "red";
            console.error("Erro na requisição fetch:", err);
        }
    });

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
            const adminRes = await fetch("http://15.228.179.50/TimerBook/public/admin/login", {
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
            const userRes = await fetch("http://15.228.179.50/TimerBook/public/login", {
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
                const res = await fetch("http://15.228.179.50/TimerBook/public/forgot-password", {
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

async function deletarConta(currentUserId) {
    try {
        closeDeleteModal();
        const response = await fetch(`/TimerBook/public/users/${currentUserId}`, {
            method: 'DELETE',
            headers: {
                    'Content-Type': 'application/json',
                }
            });
            
        const data = await response.json();
        if (response.ok) {
            showNotification('Conta deletada com sucesso', 'success');
            setTimeout(() => {
                window.location.href = 'index.php?action=login';
            }, 1500);
        } else {
            showNotification(data.error || data.message || 'Erro ao deletar conta', 'error');
        }
    } catch (error) {
        console.error('Erro:', error);
        showNotification('Erro ao conectar com o servidor', 'error');
    }
}