let pdfGlobal = null;
let paginaAtual = 1;
let globalSessaoId = null;
let globalLeituraId = null;
let globalIdLivro = null;

// Carrega o PDF e inicializa o leitor
async function carregarPdf(livro, sessao_id, leitura_id, ultimaPaginaLida) {
  globalIdLivro = livro.id;
  console.log("Id livro carregar pdf", globalIdLivro);
  globalLeituraId = leitura_id;
  globalSessaoId = sessao_id;
  if(ultimaPaginaLida){
    paginaAtual = ultimaPaginaLida;
  }
  console.log("Página a ser carregada:", paginaAtual);
  try {
    if (!livro.caminho_arquivo) throw new Error("Campo 'caminho_arquivo' ausente no retorno do livro!");
    const pdfUrl = livro.caminho_arquivo;

    pdfGlobal = await carregarDocumentoPdf(pdfUrl);
    renderizarPagina(paginaAtual, sessao_id, leitura_id, globalIdLivro);
  } catch (err) {
    tratarErro(err);
  }
}

async function carregarDocumentoPdf(pdfUrl) {
  const pdf = await pdfjsLib.getDocument(pdfUrl).promise;
  console.log(`✅ PDF carregado: ${pdf.numPages} páginas`);
  return pdf;
}

// Renderiza uma página do PDF no canvas
async function renderizarPagina(numPagina, sessao_id, leitura_id, id_livro) {
  const container = document.getElementById('pdfContainer');
  container.innerHTML = ''; // limpa o container

  const page = await pdfGlobal.getPage(numPagina);
  const scale = 1.2;
  const viewport = page.getViewport({ scale });

  const canvas = document.createElement('canvas');
  const ctx = canvas.getContext('2d');
  canvas.width = viewport.width;
  canvas.height = viewport.height;

  const renderTask = page.render({ canvasContext: ctx, viewport });
  await renderTask.promise;

  // Cria a barra de controle (botões + info)
  const controles = criarBarraDeControles(sessao_id, leitura_id, numPagina, id_livro);

  container.appendChild(canvas);
  container.appendChild(controles);

  atualizarBarraDeControles(); // atualiza os dados (ex: "Página 5 / 100")
  atualizarProgresso();
}

// Cria os elementos da barra de controle (somente uma vez)
function criarBarraDeControles(sessao_id, leitura_id, pagina_atual, id_livro) {
  const div = document.createElement('div');
  div.id = 'barraControles';
  div.style.display = 'flex';
  div.style.justifyContent = 'center';
  div.style.alignItems = 'center';
  div.style.gap = '10px';
  div.style.marginTop = '20px';
  div.style.flexWrap = 'wrap';

  // Texto "Página X / Y"
  const textoPagina = document.createElement('span');
  textoPagina.id = 'textoPagina';
  textoPagina.style.fontSize = '16px';
  textoPagina.style.fontWeight = 'bold';

  // Botões
  const btnVoltar = criarBotao('Voltar', async () => {
    await finalizarSessaoLeitura(sessao_id, leitura_id, pagina_atual, globalIdLivro);
    window.history.back();
  }); 
  div.appendChild(btnVoltar);
 
  const btnAnterior = criarBotao('←', paginaAnterior);
  const btnProxima = criarBotao('→', proximaPagina);

  // Campo de entrada de página
  const inputPagina = document.createElement('input');
  inputPagina.type = 'number';
  inputPagina.id = 'inputPagina';
  inputPagina.min = 1;
  inputPagina.max = pdfGlobal.numPages;
  inputPagina.placeholder = 'Ir...';
  inputPagina.style.width = '60px';
  inputPagina.style.textAlign = 'center';
  inputPagina.style.borderRadius = '5px';
  inputPagina.style.border = '1px solid #534444ff';
  inputPagina.style.padding = '5px';

  // Botão "Ir"
  const btnIr = criarBotao('Ir', () => {
    const valor = parseInt(inputPagina.value);
    if (isNaN(valor) || valor < 1 || valor > pdfGlobal.numPages) {
      alert(`Digite um número entre 1 e ${pdfGlobal.numPages}`);
      return;
    }
    paginaAtual = valor;
    renderizarPagina(paginaAtual, globalSessaoId, globalLeituraId, globalIdLivro);
    atualizarProgresso();
  });

  // Botão "Ver progresso"
  const btnProgresso = criarBotao('Ver progresso', () => mostrarProgresso(paginaAtual, pdfGlobal.numPages));

  // ✅ Botão "Finalizar Leitura" (vermelho)
  const btnFinalizar = document.createElement('button');
  btnFinalizar.textContent = 'Finalizar Leitura';
  btnFinalizar.onclick = async () => {
    await finalizarSessaoLeitura(sessao_id, leitura_id, pagina_atual, globalIdLivro);
    await finalizarLeitura(leitura_id);
    alert('Leitura finalizada!');
    window.history.back();
  };
  btnFinalizar.style.background = '#dc3545';
  btnFinalizar.style.color = 'white';
  btnFinalizar.style.border = 'none';
  btnFinalizar.style.padding = '8px 12px';
  btnFinalizar.style.borderRadius = '8px';
  btnFinalizar.style.cursor = 'pointer';
  btnFinalizar.style.transition = '0.3s';
  btnFinalizar.onmouseover = () => (btnFinalizar.style.background = '#a71d2a');
  btnFinalizar.onmouseout = () => (btnFinalizar.style.background = '#dc3545');

  div.appendChild(textoPagina);
  div.appendChild(btnAnterior);
  div.appendChild(btnProxima);
  div.appendChild(inputPagina);
  div.appendChild(btnIr);
  div.appendChild(btnProgresso);
  div.appendChild(btnFinalizar); // ✅ adicionado no final

  // Barra de progresso
const progressoContainer = document.createElement('div');
progressoContainer.style.width = '80%';
progressoContainer.style.height = '20px';
progressoContainer.style.background = '#ddd';
progressoContainer.style.borderRadius = '10px';
progressoContainer.style.position = 'relative';
progressoContainer.style.marginTop = '10px';
progressoContainer.style.overflow = 'hidden';

const progressoBar = document.createElement('div');
progressoBar.id = 'progressoBar';
progressoBar.style.height = '100%';
progressoBar.style.width = '0%';
progressoBar.style.background = '#4caf50';
progressoBar.style.transition = 'width 0.3s ease';

// Texto da porcentagem no centro
const progressoTexto = document.createElement('div');
progressoTexto.id = 'progressoTexto';
progressoTexto.style.position = 'absolute';
progressoTexto.style.top = '50%';
progressoTexto.style.left = '50%';
progressoTexto.style.transform = 'translate(-50%, -50%)';
progressoTexto.style.fontWeight = 'bold';
progressoTexto.style.color = '#000';
progressoTexto.style.pointerEvents = 'none'; // texto não bloqueia cliques

progressoContainer.appendChild(progressoBar);
progressoContainer.appendChild(progressoTexto);

div.appendChild(progressoContainer);



  return div;
}

// Atualiza o texto da barra quando muda de página
function atualizarBarraDeControles() {
  const texto = document.getElementById('textoPagina');
  if (texto && pdfGlobal) {
    texto.textContent = `Página ${paginaAtual} / ${pdfGlobal.numPages}`;
  }
}

function atualizarProgresso() {
  if (!pdfGlobal) return;

  const barra = document.getElementById('progressoBar');
  const texto = document.getElementById('progressoTexto');
  if (!barra || !texto) return;

  const porcentagem = ((paginaAtual / pdfGlobal.numPages) * 100).toFixed(1);

  barra.style.width = `${porcentagem}%`;
  texto.textContent = `${porcentagem}%`;
}



// Cria botões reutilizáveis
function criarBotao(texto, acao) {
  const btn = document.createElement('button');
  btn.textContent = texto;
  btn.onclick = acao;
  btn.style.background = '#007bff';
  btn.style.color = 'white';
  btn.style.border = 'none';
  btn.style.padding = '8px 12px';
  btn.style.borderRadius = '8px';
  btn.style.cursor = 'pointer';
  btn.style.transition = '0.3s';
  btn.onmouseover = () => (btn.style.background = '#0056b3');
  btn.onmouseout = () => (btn.style.background = '#007bff');
  return btn;
}

// Navegação entre páginas
function proximaPagina() {
  if (paginaAtual < pdfGlobal.numPages) {
    paginaAtual++;
    renderizarPagina(paginaAtual, globalSessaoId, globalLeituraId, globalIdLivro);
  }
  atualizarProgresso();
}

function paginaAnterior() {
  if (paginaAtual > 1) {
    paginaAtual--;
    renderizarPagina(paginaAtual, globalSessaoId, globalLeituraId, globalIdLivro);
  }
  atualizarProgresso();
}

// Progresso de leitura
function mostrarProgresso(paginaAtual, totalPaginas) {
  const porcentagem = ((paginaAtual / totalPaginas) * 100).toFixed(1);
  alert(`Você está na página ${paginaAtual} de ${totalPaginas}.\nProgresso: ${porcentagem}% lido.`);
}

// Tratamento de erro
function tratarErro(err) {
  console.error('❌ Erro ao carregar PDF:', err);
  alert("Erro ao abrir o livro: " + err.message);
}