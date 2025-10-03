<!DOCTYPE html>


<!--- Tiago: Na minha máquine tive que trocar os caminhos absolutos(/public) por 
caminhos relativos. Se a estrutura do seu projeto estiver correta não deverá ter
probelmas. Também mudei as ocorrências de "localhost" para locahost:8000-->

<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Cadastro de Usuário</title>
  <link rel="stylesheet" href="style/style.css">
  
</head>
<body>


  <!-- logo -->
  <div class="logo">
    <img src="uploads/main_logo.png" alt="logo">

  </div>

  <!-- cadastro -->
  <form id="registerForm" enctype="multipart/form-data" >
    
    <div class="foto-perfil">
      <label for="photo" class="profile-pic-label" >
        <img src="https://static.vecteezy.com/ti/vetor-gratis/p1/1631580-adicionar-icone-de-foto-com-camera-vetor.jpg" alt="Prévia da foto" id="profilePreview">
      </label>
      <input type="file" id="photo" name="photo" accept="image/*" class="file-input">
      <button type="button" id="resetPhoto" class="reset-btn">Remover foto</button>
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

  <!-- Radio Buttons-->

  <section class="slider">

    <input type="radio" name="radio-btn" id="radio1" checked>
    <input type="radio" name="radio-btn" id="radio2" checked>
    <input type="radio" name="radio-btn" id="radio3">
    <input type="radio" name="radio-btn" id="radio4">

    <!--- ou use /public/uploads se no seu funcionar -->
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
  
  <script src="/TimerBook/App/views/javascript/scripts.js"></script>

</body>
</html>
