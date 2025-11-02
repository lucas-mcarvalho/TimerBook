<?php
 $sessao_id = $_GET['sessao_id'] ?? null;
 $leitura_id = $_GET['leitura_id'] ?? null;
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
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
  <script src="/TimerBook/App/views/javascript/estatisticaLivros.js?v=<?= time() ?>"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
  <script src="/TimerBook/App/views/javascript/utils.js?v=<?= time() ?>"></script>
  <script src="/TimerBook/App/views/javascript/leitor.js?v=<?= time() ?>"></script>

  <script>
    const urlParams = new URLSearchParams(window.location.search);
    const bookId = urlParams.get('id');
    if (!bookId) {
      alert('ID do livro não informado.');
      throw new Error('ID do livro não informado na query string.');
    }

    const sessao_id = "<?php echo $sessao_id; ?>";
    const leitura_id = "<?php echo $leitura_id; ?>";  

    async function init(sessao_id, leitura_id) {
      const livro = await buscarLivro(bookId);
      carregarPdf(livro, sessao_id, leitura_id);
    }
    init(sessao_id, leitura_id);
    console.log("NO lEITOR: Sessão ID:", sessao_id, "Leitura ID:", leitura_id);
  </script>
</body>
</html>
