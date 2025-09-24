// Seleciona os elementos do HTML com os quais vamos interagir.
const form = document.querySelector('#registerForm');
const msg = document.getElementById("msg");


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