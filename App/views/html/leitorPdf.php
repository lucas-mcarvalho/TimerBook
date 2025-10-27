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
    // ID do livro que você quer abrir (exemplo: 97)
    const bookId = 97;

    // Endpoint da sua API
    const apiUrl = `http://localhost/TimerBook/public/books/${bookId}`;

    // Busca o livro na API
    fetch(apiUrl)
      .then(response => response.json())
      .then(data => {
        if (!data.caminho_arquivo) {
          throw new Error("Campo 'caminho_arquivo' não encontrado no retorno da API!");
        }

        const pdfUrl = data.caminho_arquivo;
        console.log("📘 PDF URL:", pdfUrl);

        // Agora carrega o PDF do S3
        return pdfjsLib.getDocument(pdfUrl).promise;
      })
      .then(pdf => {
        console.log('PDF carregado:', pdf.numPages, 'páginas');
        const pdfContainer = document.getElementById('pdfContainer');

        for (let pageNum = 1; pageNum <= pdf.numPages; pageNum++) {
          pdf.getPage(pageNum).then(page => {
            const scale = 1.2;
            const viewport = page.getViewport({ scale });

            const pageDiv = document.createElement('div');
            pageDiv.classList.add('page-container');

            const canvas = document.createElement('canvas');
            const context = canvas.getContext('2d');
            canvas.height = viewport.height;
            canvas.width = viewport.width;

            const button = document.createElement('button');
            button.textContent = 'Ver progresso';
            button.onclick = () => {
              const porcentagem = ((pageNum / pdf.numPages) * 100).toFixed(1);
              alert(`Você está na página ${pageNum} de ${pdf.numPages}.\nProgresso: ${porcentagem}% lido.`);
            };

            pageDiv.appendChild(canvas);
            pageDiv.appendChild(button);
            pdfContainer.appendChild(pageDiv);

            const renderContext = { canvasContext: context, viewport: viewport };
            page.render(renderContext);
          });
        }
      })
      .catch(err => {
        console.error('Erro ao carregar PDF:', err);
        alert("Erro ao abrir o livro: " + err.message);
      });
  </script>
</body>
</html>
