<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Login de Usuário</title>
  <link rel="stylesheet" href="style/login.css">
</head>
<body>
  <div class="login-container">
    <img src="uploads/main_logo.png" alt="Logo" class="logo">
    <form action="" method="" id="loginForm">
      <input type="email" id="email" name="email" required placeholder="E-mail">
      <input type="password" id="password" name="password" required placeholder="Senha">
      <a href="index.php?action=forgot_password" class="forgot-password">Esqueci minha senha</a>
      <button type="button" class="social-login-button">
        Logar com: <img src="uploads/app1_logo.png" alt="Google Logo" class="google-icon">
      </button>
      <div class="register-link">
        <span>Ainda não tem uma conta?</span>
        <a href="index.php?action=register" class="register-now">Cadastre-se</a>
      </div>
      <button type="submit" class="login-button">Entrar</button>
    </form>
  </div>

<script>
    document.getElementById("loginForm").addEventListener("submit", async function(e) {
      e.preventDefault(); // impede reload da página

      const email = document.getElementById("email").value;
      const password = document.getElementById("password").value;

      try {
        const response = await fetch("index.php?action=login", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({ email, password })
        });

        const data = await response.json();

        if (data.success) {
          // Login OK → redireciona
          window.location.href = "home.php";
        } else {
          // Exibe erro
          alert(data.error || "E-mail ou senha inválidos");
        }
      } catch (err) {
        alert("Erro de conexão com o servidor.");
        console.error(err);
      }
    });
  </script>


</body>
</html>