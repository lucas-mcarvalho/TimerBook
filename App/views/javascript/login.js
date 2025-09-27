
document.addEventListener("DOMContentLoaded", () => {
  const form = document.getElementById("loginForm");

  form.addEventListener("submit", async (e) => {
    e.preventDefault(); // impede reload da página


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

      const res = await fetch("http://localhost:8000/TimerBook/public/login", {

      //FAZ A REQUISICAO PARA A API
      const res = await fetch("http://localhost/TimerBook/public/login", {

        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ email, password })
      });


      const data = await res.json();


        //PEGA A RESPOSTA DA API
      const data = await res.json();

      //SE FOR UM 200 ENTRA NO IF

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
