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
