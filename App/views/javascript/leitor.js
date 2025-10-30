async function carregarPdf(livro) {
  try {
    if (!livro.caminho_arquivo) {
      throw new Error("Campo 'caminho_arquivo' ausente no retorno do livro!");
    }

    const pdfUrl = livro.caminho_arquivo;
    console.log(`📘 Carregando PDF do livro "${livro.titulo}"...`);

    const pdf = await carregarDocumentoPdf(pdfUrl);
    await renderizarPdf(pdf);

  } catch (err) {
    tratarErro(err);
  }
}

// --- Funções auxiliares ---

async function carregarDocumentoPdf(pdfUrl) {
  const pdf = await pdfjsLib.getDocument(pdfUrl).promise;
  console.log(`✅ PDF carregado: ${pdf.numPages} páginas`);
  return pdf;
}

async function renderizarPdf(pdf) {
  const container = document.getElementById('pdfContainer');
  container.innerHTML = ''; // limpa o container antes de renderizar

  for (let pagina = 1; pagina <= pdf.numPages; pagina++) {
    const page = await pdf.getPage(pagina);
    const elementoPagina = criarElementoPagina(page, pagina, pdf.numPages);
    container.appendChild(elementoPagina);
  }
}

function criarElementoPagina(page, numero, total) {
  const scale = 1.2;
  const viewport = page.getViewport({ scale });

  // cria container da página
  const div = document.createElement('div');
  div.classList.add('page-container');

  // cria canvas e renderiza a página nele
  const canvas = document.createElement('canvas');
  const ctx = canvas.getContext('2d');
  canvas.width = viewport.width;
  canvas.height = viewport.height;
  page.render({ canvasContext: ctx, viewport });

  // cria botão de progresso
  const botao = document.createElement('button');
  botao.textContent = 'Ver progresso';
  botao.onclick = () => mostrarProgresso(numero, total);

  div.appendChild(canvas);
  div.appendChild(botao);
  return div;
}

function mostrarProgresso(paginaAtual, totalPaginas) {
  const porcentagem = ((paginaAtual / totalPaginas) * 100).toFixed(1);
  alert(`Você está na página ${paginaAtual} de ${totalPaginas}.\nProgresso: ${porcentagem}% lido.`);
}

function tratarErro(err) {
  console.error('❌ Erro ao carregar PDF:', err);
  alert("Erro ao abrir o livro: " + err.message);
}
