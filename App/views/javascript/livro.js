// Aguarda o conteúdo da página carregar completamente
document.addEventListener('DOMContentLoaded', () => {
    
    // Seleciona o formulário e a div de feedback
    const bookForm = document.getElementById('bookForm');
    const feedbackDiv = document.getElementById('feedback');
    
    // Adiciona um "ouvinte" para o evento de envio do formulário
    bookForm.addEventListener('submit', async (event) => {
        // 1. Previne o comportamento padrão do formulário (que é recarregar a página)
        event.preventDefault();
        
        // 2. Cria um objeto FormData a partir do formulário.
        // Isso coleta todos os campos e o arquivo automaticamente.
        const formData = new FormData(bookForm);

        // Limpa mensagens antigas
        feedbackDiv.innerHTML = '';
        feedbackDiv.className = '';

        try {
            // 3. Envia os dados para o seu endpoint PHP usando fetch
            const response = await fetch('http://localhost/TimerBook/public/books', { // <-- SUBSTITUA PELO CAMINHO CORRETO DO SEU ENDPOINT
                method: 'POST',
                body: formData, // O corpo da requisição é o nosso FormData
                // IMPORTANTE: Não defina o cabeçalho 'Content-Type'. 
                // O navegador fará isso automaticamente com o boundary correto para multipart/form-data.
            });
            
            // 4. Pega a resposta do servidor em formato JSON
            const result = await response.json();
            
            // 5. Verifica se a resposta foi bem-sucedida (status 2xx)
            if (response.ok) {
                feedbackDiv.className = 'success';
                feedbackDiv.textContent = 'Livro cadastrado com sucesso!';
                // Opcional: mostrar a URL retornada ou redirecionar
                console.log('Dados retornados:', result); 
                bookForm.reset(); // Limpa o formulário
            } else {
                // Se o servidor retornou um erro (status 400, 500, etc.)
                feedbackDiv.className = 'error';
                feedbackDiv.textContent = `Erro: ${result.error || 'Ocorreu um problema.'}`;
            }

        } catch (error) {
            // 6. Captura erros de rede ou falhas na comunicação
            feedbackDiv.className = 'error';
            feedbackDiv.textContent = 'Erro de conexão. Não foi possível enviar os dados.';
            console.error('Erro no fetch:', error);
        }
    });
});