const form = document.querySelector('#registerForm');
const msg = document.getElementById("msg");

form.addEventListener('submit', async event => {
  event.preventDefault();
  const formData = new FormData(form);
  
  // A resposta do fetch é armazenada em 'res'
  try{
  const res = await fetch("http://localhost/TIMERBOOK/public/register", {
    method: 'POST',
    body: formData
  });

  const response = await res.json(); 
  msg.innerText = response.message || result.error;
  msg.style.color = response.message ? "green" : "red";

  if (response.message) form.reset();
} catch(err){
    msg.innerText = "Erro ao conectar com a API. ";
    msg.style.color = "red";
}
  
});