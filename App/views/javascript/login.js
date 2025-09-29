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
      //FAZ A REQUISICAO PARA A API
      const res = await fetch("http://localhost/TimerBook/public/login", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ email, password })
      });

      //PEGA A RESPOSTA DA API
      const data = await res.json();

      //SE FOR UM 200 ENTRA NO IF
      if (res.ok) {
        // Salvar usuário no localStorage
        localStorage.setItem("user", JSON.stringify(data.user));
        // Redirecionar para home
        window.location.href = "index.php?action=home";
      } else {
        // Se deu erro (ex: 400 ou 401), mostra mensagem vinda da API
        alert(data.error || "Erro desconhecido");
      }
    } catch (err) {
      console.error("Erro:", err); // <-- corrigido aqui
      alert("Falha ao conectar com o servidor");
    }
  });
});
