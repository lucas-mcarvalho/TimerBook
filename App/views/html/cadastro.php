<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Cadastro de Usuário</title>
  <link rel="stylesheet" href="style/style.css"> 
  <script src="/TimerBook/App/views/javascript/usuario_api.js"></script>
</head>
<body>

  <div class="logo">
    <img src="uploads/logo.svg" alt="logo">
  </div>

  <div class="main-content">

    <form id="registerForm" enctype="multipart/form-data" >
      
      <div class="foto-perfil">
        <label for="photo" class="profile-pic-label" >
          <img src="https://static.vecteezy.com/ti/vetor-gratis/p1/1631580-adicionar-icone-de-foto-com-camera-vetor.jpg" alt="Prévia da foto" id="profilePreview">
        </label>
        <input type="file" id="photo" name="photo" accept="image/*" class="file-input">
        <button type="button" id="resetPhoto" class="reset-btn">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path fill="currentColor" d="M135.2 17.7L128 32H32C14.3 32 0 46.3 0 64S14.3 96 32 96H416c17.7 0 32-14.3 32-32s-14.3-32-32-32H320l-7.2-14.7C307.4 6.8 296.3 0 284.2 0H163.8c-12.1 0-23.2 6.8-28.6 17.7zM416 128H32L53.2 467c1.6 25.3 22.6 45 47.9 45H346.9c25.3 0 46.3-19.7 47.9-45L416 128z"/></svg>
      </button>
      </div>


      <h2>Cadastro</h2>
      <div>
        <label for="nome">Nome completo:</label>
        <input type="text" id="nome" name="nome" required placeholder="ex: Nome Sobrenome">
      </div>
      <div>
        <label for="username">Nome de usuário:</label>
        <input type="text" id="username" name="username" required placeholder="ex: teste">
      </div>
      <div>
        <label for="email">E-mail:</label>
        <input type="email" id="email" name="email" required placeholder="ex: nome@gmail.com">
      </div>
      <div>
        <label for="password">Senha:</label>
        <input type="password" id="password" name="password" required placeholder="********">
      </div>

      <button type="submit">Cadastrar</button>

      <div class="msg" id="msg"></div>
    </form>

    <section class="slider">

      <input type="radio" name="radio-btn" id="radio1" checked>
      <input type="radio" name="radio-btn" id="radio2">
      <input type="radio" name="radio-btn" id="radio3">
      <input type="radio" name="radio-btn" id="radio4">

      <div class="slides">
        <div class="slide first">
          <img src="uploads/img1.jpeg" alt="img1">
        </div>
        <div class="slide">
          <img src="uploads/img2.jpg" alt="img2">
        </div>
        <div class="slide">
          <img src="uploads/img3.jpg" alt="img3">
        </div>
        <div class="slide">
          <img src="uploads/img4.jpg" alt="img4">
        </div>
      </div>

      <div class="navigation-auto">
        <div class="auto-btn1"></div>
        <div class="auto-btn2"></div>
        <div class="auto-btn3"></div>
        <div class="auto-btn4"></div>
      </div>

      <div class="manual-navigation">
        <label for="radio1" class="manual-btn"></label>
        <label for="radio2" class="manual-btn"></label>
        <label for="radio3" class="manual-btn"></label>
        <label for="radio4" class="manual-btn"></label>
      </div>
    </section>

  </div> <script>cadastrarUsuario();</script>

</body>
</html>