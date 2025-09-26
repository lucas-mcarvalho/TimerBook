document.addEventListener("DOMContentLoaded", () => {
  const form = document.getElementById("loginForm");

  form.addEventListener("submit", async (e) => {
    e.preventDefault(); // impede reload da página

    const email = document.getElementById("email").value;
    const password = document.getElementById("password").value;

    try {
      const res = await fetch("http://localhost:8000/TimerBook/public/login", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ email, password })
      });

      const data = await res.json();

      if (res.ok) {
        // Exemplo: salvar no localStorage
        localStorage.setItem("user", JSON.stringify(data.user));
        // Exemplo: redirecionar para home
        window.location.href = "index.php?action=home";
      }
    } catch (err) {
      console.error("Erro:", err);
      alert("Falha ao conectar com o servidor");
    }
  });
});
