let pdfGlobal = null;
let paginaAtual = 1;
let globalSessaoId = null;
let globalLeituraId = null;

// Carrega o PDF e inicializa o leitor

/***
 Para que o leitor seja capaz de ir na última página lida, é necessário carregar os
 dados da última sessão do usuário naquela determinada. É preciso uma função capaz
 de retornar os dados da útima sessão de uma leitua, dado o ID da leitura.
 
 retornarUltimaSessaoLeitura(leitura_id) -> { sessao_id, pagina_atual }
 ***/

async function carregarPdf(livro, sessao_id, leitura_id) {
  globalLeituraId = leitura_id;
  globalSessaoId = sessao_id;
  try {
    if (!livro.caminho_arquivo) throw new Error("Campo 'caminho_arquivo' ausente no retorno do livro!");
    const pdfUrl = livro.caminho_arquivo;

    pdfGlobal = await carregarDocumentoPdf(pdfUrl);
    renderizarPagina(paginaAtual, sessao_id, leitura_id);
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
async function renderizarPagina(numPagina, sessao_id, leitura_id) {
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
  const controles = criarBarraDeControles(sessao_id, leitura_id, numPagina);

  container.appendChild(canvas);
  container.appendChild(controles);

  atualizarBarraDeControles(); // atualiza os dados (ex: "Página 5 / 100")
}

// Cria os elementos da barra de controle (somente uma vez)
function criarBarraDeControles(sessao_id, leitura_id, pagina_atual) {
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
    console.log(sessao_id, leitura_id, pagina_atual);
    await finalizarSessaoLeitura(sessao_id, leitura_id, pagina_atual);
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
    renderizarPagina(paginaAtual, globalSessaoId, globalLeituraId);
  });

  // Botão "Ver progresso"
  const btnProgresso = criarBotao('Ver progresso', () => mostrarProgresso(paginaAtual, pdfGlobal.numPages));

  div.appendChild(textoPagina);
  div.appendChild(btnAnterior);
  div.appendChild(btnProxima);
  div.appendChild(inputPagina);
  div.appendChild(btnIr);
  div.appendChild(btnProgresso);

  return div;
}

// Atualiza o texto da barra quando muda de página
function atualizarBarraDeControles() {
  const texto = document.getElementById('textoPagina');
  if (texto && pdfGlobal) {
    texto.textContent = `Página ${paginaAtual} / ${pdfGlobal.numPages}`;
  }
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
    renderizarPagina(paginaAtual, globalSessaoId, globalLeituraId);
  }
}

function paginaAnterior() {
  if (paginaAtual > 1) {
    paginaAtual--;
    renderizarPagina(paginaAtual, globalSessaoId, globalLeituraId);
  }
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