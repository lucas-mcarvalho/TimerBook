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

  <script>
    // Caminho do seu arquivo PDF (deixe na mesma pasta deste HTML para testes apenas)
    const url = 'https://imgusrs.s3.sa-east-1.amazonaws.com/books/68e3c59d0251f-SprintES.PDF';

    // Configurações do PDF.js
    pdfjsLib.getDocument(url).promise.then(pdf => {
      console.log('PDF carregado:', pdf.numPages, 'páginas');
      const pdfContainer = document.getElementById('pdfContainer');

      // Para cada página, renderiza e adiciona um botão
      for (let pageNum = 1; pageNum <= pdf.numPages; pageNum++) {
        pdf.getPage(pageNum).then(page => {
          const scale = 1.2;
          const viewport = page.getViewport({ scale });

          // Cria o container de página
          const pageDiv = document.createElement('div');
          pageDiv.classList.add('page-container');

          // Cria o canvas para desenhar a página
          const canvas = document.createElement('canvas');
          const context = canvas.getContext('2d');
          canvas.height = viewport.height;
          canvas.width = viewport.width;

          // Cria o botão de progresso
          const button = document.createElement('button');
          button.textContent = 'Ver progresso';
          button.onclick = () => {
            const porcentagem = ((pageNum / pdf.numPages) * 100).toFixed(1);
            alert(`Você está na página ${pageNum} de ${pdf.numPages}.\nProgresso: ${porcentagem}% lido.`);
          };

          // Adiciona o canvas e o botão na tela
          pageDiv.appendChild(canvas);
          pageDiv.appendChild(button);
          pdfContainer.appendChild(pageDiv);

          // Renderiza a página no canvas
          const renderContext = { canvasContext: context, viewport: viewport };
          page.render(renderContext);
        });
      }
    }).catch(err => {
      console.error('Erro ao carregar PDF:', err);
    });
  </script>
</body>
</html>