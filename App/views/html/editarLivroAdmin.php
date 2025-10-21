<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style/editarLivroAdmin.css">
    <title>Admin - Gerenciar Livros</title>
</head>
    
<body>

    <header class="main-header">
        <div class="header-logo">
            <a href="index.php?action=home" class="logo-link">
                <img src="uploads/logo.svg" alt="Logo Timerbook" class="logo-img">
                <h1>TimerBook</h1>
            </a>
        </div>
        <div class="header-buttons">
            <button class="nav1-button" onclick="window.history.back()">Voltar</button> 
            <button class="nav-button" onclick="window.location.href='index.php?action=home'">Tela Principal</button> 
        </div>
    </header>

    <main class="admin-container">
        <h2 class="title">GERENCIAR LIVROS DO USUÁRIO</h2>
        
        <table class="books-table">
            <thead>
                <tr>
                    <th>Capa</th>
                    <th>Título</th>
                    <th>Autor</th>
                    <th>ano</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
        
            </tbody>
        </table>

        <div class="add-button-container">
            <a href="index.php?action=Adicionar_Livro" class="add-book-btn">
                + Adicionar Novo Livro
            </a>
        </div>
    </main>
<Script>
async function listarLivrosAdmin() {
  const urlParams = new URLSearchParams(window.location.search);
  const userId = urlParams.get("userId");
  const tabela = document.querySelector(".books-table tbody");

  if (!userId) {
    tabela.innerHTML = `<tr><td colspan="4">❌ ID do usuário não informado.</td></tr>`;
    return;
  }

  try {
    const res = await fetch(`http://localhost/TimerBook/public/books?user_id=${userId}`);
    const data = await res.json();

    if (!res.ok) throw new Error(data.error || "Erro ao buscar livros.");

    if (!Array.isArray(data) || data.length === 0) {
      tabela.innerHTML = `<tr><td colspan="4">Nenhum livro encontrado.</td></tr>`;
      return;
    }

    tabela.innerHTML = data.map(livro => `
      <tr>
        <td><img src="${livro.capa_arquivo || '/TimerBook/public/uploads/placeholder.png'}" width="60"></td>


        <td>${livro.titulo || '(Sem título)'}</td>
        <td>${livro.autor || '(Desconhecido)'}</td>
        <td>${livro.ano_publicacao || '(Desconhecido)'}</td>
        <td>
          <button onclick="editarLivro(${livro.id})">Editar</button>
          <button onclick="deletarLivro(${livro.id})">Excluir</button>
        </td>
      </tr>
    `).join("");
  } catch (err) {
    console.error(err);
    tabela.innerHTML = `<tr><td colspan="4">Erro ao carregar livros: ${err.message}</td></tr>`;
  }
}

async function deletarLivro(id) {
  if (!confirm("Tem certeza que deseja excluir este livro?")) return;

  try {
    const res = await fetch(`http://localhost/TimerBook/public/books/${id}`, { method: "DELETE" });
    const data = await res.json();

    if (!res.ok) throw new Error(data.error || "Erro ao excluir livro.");

    alert("Livro excluído com sucesso!");
    listarLivrosAdmin();
  } catch (err) {
    alert("Falha ao excluir livro: " + err.message);
  }
}

async function editarLivro(id) {
  const titulo = prompt("Novo título do livro:");
  const autor = prompt("Novo autor:");
  const ano = prompt("Novo ano de publicação:");

  if (!titulo || !autor || !ano) {
    alert("Todos os campos são obrigatórios!");
    return;
  }

  try {
    const res = await fetch(`http://localhost/TimerBook/public/books/${id}`, {
      method: "PUT",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ titulo, autor, ano_publicacao: ano }),
    });
    const data = await res.json();

    if (!res.ok) throw new Error(data.error || "Erro ao atualizar livro.");

    alert("Livro atualizado com sucesso!");
    listarLivrosAdmin();
  } catch (err) {
    alert("Falha ao editar livro: " + err.message);
  }
}

listarLivrosAdmin();


</Script>


</body>
</html>