// js/admin.js
const API_BASE = "http://localhost/TimerBook/public"; // ajuste se necessário (case-sensitive)

// ao carregar a página
document.addEventListener("DOMContentLoaded", () => {
  const userList = document.getElementById("user-list");

  // garante que exista um container para os resultados (não sobrescreve o form de busca)
  let resultsContainer = document.getElementById("usersContainer");
  if (!resultsContainer) {
    resultsContainer = document.createElement("div");
    resultsContainer.id = "usersContainer";
    resultsContainer.className = "users-container";
    userList.appendChild(resultsContainer);
  }

  // busca usuários do backend
  async function fetchUsers() {
    resultsContainer.innerHTML = `<p>Carregando usuários...</p>`;
    try {
      const res = await fetch(`${API_BASE}/users`, { cache: "no-store" });
      const text = await res.text(); // pega o texto pra evitar erro se o backend retornar algo inesperado
      let data;
      try {
        data = JSON.parse(text);
      } catch (e) {
        throw new Error("Resposta do servidor inválida: " + text);
      }

      if (!res.ok) {
        const msg = data && (data.error || data.message) ? (data.error || data.message) : "Erro ao buscar usuários";
        throw new Error(msg);
      }

      // aceita tanto um array direto quanto { users: [...] }
      const users = Array.isArray(data) ? data : (Array.isArray(data.users) ? data.users : []);

      renderUsers(users);
    } catch (err) {
      console.error("Erro ao buscar usuários:", err);
      resultsContainer.innerHTML = `<p class="error">⚠️ Falha ao carregar usuários: ${err.message}</p>`;
    }
  }

  // renderiza a lista de usuários
  function renderUsers(users) {
    if (!users || users.length === 0) {
      resultsContainer.innerHTML = `<p>Nenhum usuário encontrado.</p>`;
      return;
    }

    resultsContainer.innerHTML = ""; // limpa

    users.forEach((user) => {
      const userItem = document.createElement("div");
      userItem.className = "user-item";
      userItem.setAttribute("data-name", (user.nome || "").toLowerCase());

      // monta a imagem (se existir) e os dados
      const photoHtml = user.profile_photo
        ? `<img src="${user.profile_photo}" alt="${user.nome}" class="user-photo" width="48" height="48">`
        : `<div class="user-photo placeholder" style="width:48px;height:48px;border-radius:4px;background:#eee;display:inline-block;"></div>`;

      userItem.innerHTML = `
        <div class="user-row" style="display:flex;align-items:center;gap:12px;justify-content:space-between;padding:8px;border-bottom:1px solid #eee;">
          <div style="display:flex;align-items:center;gap:12px;">
            ${photoHtml}
            <div>
              <div><strong>${escapeHtml(user.nome || "(sem nome)")}</strong></div>
              <div style="font-size:0.9em;color:#666;">${escapeHtml(user.email || "")}</div>
            </div>
          </div>

          <div class="user-actions" style="display:flex;gap:8px;">
            <button class="edit-btn" data-id="${user.id}">Editar</button>
            <button class="delete-btn" data-id="${user.id}">Excluir</button>
          </div>
        </div>
      `;

      resultsContainer.appendChild(userItem);
    });

    // adiciona listeners (delegation para botões)
    resultsContainer.querySelectorAll(".delete-btn").forEach(btn => {
      btn.onclick = () => deleteUser(btn.getAttribute("data-id"));
    });
    resultsContainer.querySelectorAll(".edit-btn").forEach(btn => {
      btn.onclick = () => editUser(btn.getAttribute("data-id"));
    });
  }

  // função para deletar usuário
  async function deleteUser(id) {
    if (!confirm("Tem certeza que deseja excluir este usuário?")) return;
    try {
      const res = await fetch(`${API_BASE}/users/${id}`, { method: "DELETE" });
      const data = await res.json();
      if (!res.ok) {
        throw new Error(data.error || data.message || "Erro ao deletar usuário");
      }
      alert(data.message || "Usuário excluído com sucesso!");
      fetchUsers();
    } catch (err) {
      console.error("Erro ao deletar usuário:", err);
      alert("Erro ao excluir: " + (err.message || "verifique o console"));
    }
  }

  // redireciona para página de edição (usa sua rota existente)
  function editUser(id) {
    window.location.href = `index.php?action=adm_editar&id=${encodeURIComponent(id)}`;
  }

  // filtro (usa o input #search que já está no HTML)
  const searchInput = document.getElementById("search");
  if (searchInput) {
    searchInput.addEventListener("input", () => {
      const term = searchInput.value.trim().toLowerCase();
      document.querySelectorAll("#usersContainer .user-item").forEach(row => {
        const name = row.getAttribute("data-name") || "";
        row.style.display = name.includes(term) ? "" : "none";
      });
    });
  }

  // função simples para escapar HTML (segurança básica ao injetar strings)
  function escapeHtml(str) {
    return String(str)
      .replace(/&/g, "&amp;")
      .replace(/"/g, "&quot;")
      .replace(/'/g, "&#39;")
      .replace(/</g, "&lt;")
      .replace(/>/g, "&gt;");
  }

  // inicia carregamento
  fetchUsers();
});
