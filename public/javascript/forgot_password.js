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