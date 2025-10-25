// js/admin.js
(function () {
  const API_BASE = "http://localhost/TimerBook/public";

  // ==========================
  // LISTAGEM DE USUÁRIOS
  // ==========================
  async function listarUsuarios() {
    const userList = document.getElementById("user-list");
    if (!userList) return;

    const resultsContainer = ensureResultsContainer(userList);

    try {
      resultsContainer.innerHTML = `<p>Carregando usuários...</p>`;
      const res = await fetch(`${API_BASE}/users`, { cache: "no-store" });
      const text = await res.text();

      let data;
      try {
        data = JSON.parse(text);
      } catch (e) {
        throw new Error("Resposta do servidor inválida: " + text);
      }

      if (!res.ok) throw new Error(data?.error || data?.message || "Erro ao buscar usuários");

      const users = Array.isArray(data) ? data : Array.isArray(data.users) ? data.users : [];
      renderUsers(resultsContainer, users);
    } catch (err) {
      console.error("Erro ao buscar usuários:", err);
      resultsContainer.innerHTML = `<p class="error">⚠️ Falha ao carregar usuários: ${err.message}</p>`;
    }
  }

  function ensureResultsContainer(userList) {
    let resultsContainer = document.getElementById("usersContainer");
    if (!resultsContainer) {
      resultsContainer = document.createElement("div");
      resultsContainer.id = "usersContainer";
      resultsContainer.className = "users-container";
      userList.appendChild(resultsContainer);
    }
    return resultsContainer;
  }

  function renderUsers(container, users) {
    container.innerHTML = "";

    if (!users || users.length === 0) {
      container.innerHTML = `<p>Nenhum usuário encontrado.</p>`;
      return;
    }

    users.forEach((user) => {
      const userItem = createUserItem(user);
      container.appendChild(userItem);
    });

    attachUserActionListeners(container);
    setupSearchFilter(container); // Filtro configurado após renderização
  }

  function createUserItem(user) {
    const userItem = document.createElement("div");
    userItem.className = "user-item";
    userItem.setAttribute("data-name", (user.nome || "").toLowerCase());

    const photoHtml = user.profile_photo
      ? `<img src="${user.profile_photo}" alt="${user.nome}" class="user-photo" width="48" height="48">`
      : `<div class="user-photo placeholder" style="width:48px;height:48px;border-radius:4px;background:#eee;display:inline-block;"></div>`;

    userItem.innerHTML = `
      
      <div style="display:flex;gap:12px;">
        ${photoHtml}
        <div>
          <div><strong>${escapeHtml(user.nome || "(sem nome)")}</strong></div>
          <div style="font-size:0.9em;color:#666;">${escapeHtml(user.email || "")}</div>
        </div>
      </div>
      <div class="user-actions" style="display:flex;gap:8px;">
        <button class="edit-btn" data-id="${user.id}">Editar</button>
        <button class="delete-btn" data-id="${user.id}">Excluir</button>
        <button class="edit-livro-btn" data-id="${user.id}">ver livros</button>
      </div>
      
    `;
    return userItem;
  }

// ... dentro do seu arquivo admin.js ...

  function attachUserActionListeners(container) {
    container.querySelectorAll(".delete-btn").forEach((btn) => {
      btn.onclick = () => deletarUsuario(btn.getAttribute("data-id"));
    });
    
    container.querySelectorAll(".edit-btn").forEach((btn) => {
      btn.onclick = () => {
        const id = btn.getAttribute("data-id");
        if (!id) {
          alert("ID do usuário não encontrado!");
          return;
        }
        // redireciona para a página de edição DO USUÁRIO
        //window.location.href = `index.php?action=adm_editar&id=${encodeURIComponent(id)}`;
      };
    });

    
    // Faz o botão "Editar Livro" redirecionar para a página (não entrará na release)
    
    container.querySelectorAll(".edit-livro-btn").forEach((btn) => {
      btn.onclick = () => {
        const id = btn.getAttribute("data-id"); // Pega o ID do *usuário*
        if (!id) {
          alert("ID do usuário não encontrado!");
          return;
        }
        // Redireciona para a sua página de gerenciar livros, passando o ID do usuário
        // (Ajuste 'adm_livros' se o nome da sua 'action' for outro)
        window.location.href = `index.php?action=adm_ver_livros&userId=${encodeURIComponent(id)}`;
      };
    });
  }

  // ... resto do seu admin.js ...
 async function deletarUsuario(id) {
  if (!confirm("Tem certeza que deseja excluir este usuário?")) return;

  try {
    const res = await fetch(`${API_BASE}/users/${id}`, { method: "DELETE" });

    if (!res.ok) {
      // tenta pegar mensagem do backend, se existir
      let errorMsg = "";
      try {
        errorMsg = await res.text();
      } catch {}
      throw new Error(errorMsg || "Erro ao deletar usuário");
    }

    alert("Usuário excluído com sucesso!");
    listarUsuarios();
  } catch (err) {
    console.error("Erro ao deletar usuário:", err);
    alert("Erro ao excluir: " + (err.message || "verifique o console"));
  }
}


  // ==========================
  // EDIÇÃO DE USUÁRIO
  // ==========================
  async function editarUsuario() {
    const urlParams = new URLSearchParams(window.location.search);
    const userId = urlParams.get("id");

    if (!userId) {
      alert("ID do usuário não encontrado na URL!");
      return;
    }

    const form = document.getElementById("editUserForm");
    const preview = document.getElementById("photoPreview");
    const fileInput = document.getElementById("photo");

    // Carrega dados do usuário
    try {
      const res = await fetch(`${API_BASE}/users/${userId}`);
      const data = await res.json();
      if (!res.ok) throw new Error(data.error || data.message || "Erro ao buscar usuário");

      document.getElementById("nome").value = data.nome || "";
      document.getElementById("username").value = data.username || "";
      document.getElementById("email").value = data.email || "";
      document.getElementById("senha").value = "";
      if (preview && data.profile_photo) preview.src = data.profile_photo;
    } catch (err) {
      console.error(err);
      alert("Falha ao carregar usuário: " + err.message);
    }

  
    // Preview da foto
    if (fileInput) {
      fileInput.addEventListener("change", () => {
        const file = fileInput.files[0];
        if (file) {
          const reader = new FileReader();
          reader.onload = (e) => (preview.src = e.target.result);
          reader.readAsDataURL(file);
        }
      });
    }

    // Submissão do formulário
    if (form) {
      form.addEventListener("submit", async (e) => {
        e.preventDefault();

        const nome = document.getElementById("nome").value.trim();
        const username = document.getElementById("username").value.trim();
        const email = document.getElementById("email").value.trim();
        const senha = document.getElementById("senha").value.trim();
        const photoFile = fileInput?.files[0];

        const useFormData = !!photoFile;
        let body, headers = {};

        if (useFormData) {
          body = new FormData();
          body.append("_method", "PUT"); 
          body.append("nome", nome);
          body.append("username", username);
          body.append("email", email);
          body.append("senha", senha);
          body.append("photo", photoFile);
        } else {
          headers["Content-Type"] = "application/json";
          body = JSON.stringify({ nome, username, email, senha });
        }

        try {
          const res = await fetch(`${API_BASE}/users/${userId}`, {
            method: "POST",
            headers,
            body,
          });

          const data = await res.json();
          if (!res.ok) throw new Error(data.error || data.message || "Erro ao atualizar");

          alert("✅ Usuário atualizado com sucesso!");
          //window.location.href = "index.php?action=admin";
        } catch (err) {
          console.error(err);
          alert("Erro ao salvar alterações: " + err.message);
        }
      });
    }
  }

  // ==========================
  // FILTRO DE BUSCA
  // ==========================
  function setupSearchFilter(container) {
    const searchInput = document.getElementById("search");
    if (!searchInput || !container) return;

    searchInput.addEventListener("input", () => {
      const term = searchInput.value.trim().toLowerCase();
      container.querySelectorAll(".user-item").forEach((row) => {
        const name = row.getAttribute("data-name") || "";
        row.style.display = name.includes(term) ? "" : "none";
      });
    });
  }

  function escapeHtml(str) {
    return String(str)
      .replace(/&/g, "&amp;")
      .replace(/"/g, "&quot;")
      .replace(/'/g, "&#39;")
      .replace(/</g, "&lt;")
      .replace(/>/g, "&gt;");
  }

  // ==========================
  // EXPOSIÇÃO GLOBAL
  // ==========================
  window.listarUsuarios = listarUsuarios;
  window.deletarUsuario = deletarUsuario;
  window.editarUsuario = editarUsuario;
})();