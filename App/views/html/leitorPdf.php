<?php
 $sessao_id = $_GET['sessao_id'] ?? null;
 $leitura_id = $_GET['leitura_id'] ?? null;
 
 if (session_status() === PHP_SESSION_NONE) {
    session_start();
  }
$userId = $_SESSION["user_id"];
?>


<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <link rel="icon" href="uploads/TimerbookFavicon.png" type="image/png">
  <title>Leitor PDF com Navegação</title>
  <style>
    body {
      background: #222;
      color: white;
      font-family: Arial, sans-serif;
      text-align: center;
    }

    canvas {
      border: 1px solid #555;
      background: white;
      margin-top: 20px;
    }

    button {
      background: #007bff;
      color: white;
      border: none;
      padding: 10px 15px;
      border-radius: 8px;
      cursor: pointer;
      transition: 0.3s;
      margin: 5px;
    }

    button:hover {
      background: #0056b3;
    }
  </style>
</head>
<body>
  <h2>Visualizador de PDF com Botões</h2>
  <div id="pdfContainer"></div>

  <!-- PDF.js CDN -->
  <script src="/js/estatisticaLivros.js?v=<?= time() ?>"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
  <script src="/js/utils.js?v=<?= time() ?>"></script>
  <script src="/js/leitor.js?v=<?= time() ?>"></script>

  <script>
    const urlParams = new URLSearchParams(window.location.search);
    const bookId = urlParams.get('id');
    if (!bookId) {
      alert('ID do livro não informado.');
      throw new Error('ID do livro não informado na query string.');
    }

    const sessao_id = "<?php echo $sessao_id; ?>";
    const leitura_id = "<?php echo $leitura_id; ?>";  
    const user_id = "<?php echo $userId; ?>";

    

    let ultimaPaginaLida = 0;
    //let penultimaPaginaLida = 0;
    async function init(sessao_id, leitura_id) {
      const livro = await buscarLivro(bookId);
      ultimaPaginaLida = await buscarUltimaPagina(user_id, bookId);
      carregarPdf(livro, sessao_id, leitura_id, ultimaPaginaLida);
      console.log("NO lEITOR: Sessão ID:", sessao_id, "Leitura ID:", leitura_id, "Ultima pagina lida: ", ultimaPaginaLida);
    }
    init(sessao_id, leitura_id);
   
  </script>
</body>
</html>
