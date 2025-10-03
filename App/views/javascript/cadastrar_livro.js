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
