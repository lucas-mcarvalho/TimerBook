const form = document.querySelector('#registerForm');
const msg = document.getElementById("msg");

form.addEventListener('submit', async event => {
  event.preventDefault();

  const formData = new FormData(form); // mantém multipart

  try {
    const res = await fetch("http://localhost/TimerBook/public/register", {
      method: "POST",
      body: formData // não colocar headers Content-Type, o browser define
    });

    const response = await res.json();

    msg.innerText = response.message || response.error;
    msg.style.color = response.message ? "green" : "red";

    if (response.message) {
      form.reset();
    }
  } catch (err) {
    msg.innerText = "Erro ao conectar com a API.";
    msg.style.color = "red";
  }
});
