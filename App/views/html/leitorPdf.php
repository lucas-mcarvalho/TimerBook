<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Leitor PDF com Progresso</title>
  <style>
    body {
      background: #222;
      color: white;
      font-family: Arial, sans-serif;
      text-align: center;
    }

    .page-container {
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 20px;
    }

    canvas {
      border: 1px solid #555;
      background: white;
      margin-right: 10px;
    }

    button {
      background: #007bff;
      color: white;
      border: none;
      padding: 10px 15px;
      border-radius: 8px;
      cursor: pointer;
      transition: 0.3s;
    }

    button:hover {
      background: #0056b3;
    }
  </style>

  
</head>
<body>
  <h2>Visualizador de PDF com PDF.js e Progresso</h2>
  <div id="pdfContainer"></div>

  <!-- PDF.js CDN -->
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

    async function init() {
      const livro = await buscarLivro(bookId);
      carregarPdf(livro);
    }
    init();
    
    

  </script>
</body>
</html>
