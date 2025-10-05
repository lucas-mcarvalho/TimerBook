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
  const file = photoInput.files[0];
  if (file) {
    const reader = new FileReader();
    reader.onload = e => {
      profilePicPreview.src = e.target.result;
    };
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