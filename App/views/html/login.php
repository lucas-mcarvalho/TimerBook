<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Login de Usuário</title>
  <link rel="stylesheet" href="style/login.css">
  <script src="/TimerBook/App/views/javascript/usuario_api.js"></script>

</head>
<body>
  <div class="login-container">
    <img src="uploads/main_logo.png" alt="Logo" class="logo">
    <form action="" method="" id="loginForm">
      <input type="email" id="email" name="email" required placeholder="E-mail">
      <input type="password" id="password" name="password" required placeholder="Senha">
      <a href="index.php?action=forgot_password" class="forgot-password">Esqueci minha senha</a>
      <button type="button" id="googleLoginBtn" class="social-login-button">
        Logar com: <img src="uploads/app1_logo.png" alt="Google Logo" class="google-icon">
      </button>
      <div class="register-link">
        <span>Ainda não tem uma conta?</span>
        <a href="index.php?action=register" class="register-now">Cadastre-se</a>
      </div>
      <button type="submit" class="login-button">Entrar</button>
    </form>
  </div>


<script>loginUsuario();</script>
<script>
  document.getElementById("googleLoginBtn").addEventListener("click", () => {
  window.location.href = "http://localhost/TimerBook/public/google-login";
});
</script>
</body>
</html>