// js/admin_edit.js
const API_BASE = "http://localhost/TimerBook/public";

document.addEventListener("DOMContentLoaded", () => {
  const form = document.getElementById("editUserForm");
  const preview = document.getElementById("photoPreview");
  const fileInput = document.getElementById("photo");

  // pega ID da URL (index.php?action=adm_editar&id=3)
  const urlParams = new URLSearchParams(window.location.search);
  const userId = urlParams.get("id");

  if (!userId) {
    alert("ID do usuário não encontrado na URL!");
    return;
  }

  // busca dados do usuário para preencher o form
  async function loadUser() {
    try {
      const res = await fetch(`${API_BASE}/users/${userId}`);
      const data = await res.json();

      if (!res.ok) {
        throw new Error(data.error || data.message || "Erro ao buscar usuário");
      }

      document.getElementById("nome").value = data.nome || "";
      document.getElementById("username").value = data.username || "";
      document.getElementById("email").value = data.email || "";
      document.getElementById("senha").value = "";
      if (preview && data.profile_photo) preview.src = data.profile_photo;
    } catch (err) {
      console.error(err);
      alert("Falha ao carregar usuário: " + err.message);
    }
  }

  // mostra preview da foto
  if (fileInput) {
    fileInput.addEventListener("change", () => {
      const file = fileInput.files[0];
      if (file) {
        const reader = new FileReader();
        reader.onload = e => (preview.src = e.target.result);
        reader.readAsDataURL(file);
      }
    });
  }

  // envia alterações
  if (form) {
    form.addEventListener("submit", async (e) => {
      e.preventDefault();

      const nome = document.getElementById("nome").value.trim();
      const username = document.getElementById("username").value.trim();
      const email = document.getElementById("email").value.trim();
      const senha = document.getElementById("senha").value.trim();
      const photoFile = fileInput?.files[0];

      const useFormData = !!photoFile; // se enviou foto, usa form-data

      let body, headers = {};

      if (useFormData) {
        body = new FormData();
        body.append("nome", nome);
        body.append("username", username);
        body.append("email", email);
        body.append("senha", senha);
        if (photoFile) body.append("photo", photoFile);
      } else {
        headers["Content-Type"] = "application/json";
        body = JSON.stringify({ nome, username, email, senha });
      }

      try {
        const res = await fetch(`${API_BASE}/users/${userId}`, {
          method: "PUT",
          headers,
          body,
        });

        const data = await res.json();

        if (!res.ok) throw new Error(data.error || data.message || "Erro ao atualizar");

        alert("✅ Usuário atualizado com sucesso!");
        window.location.href = "index.php?action=admin"; // volta pra lista
      } catch (err) {
        console.error(err);
        alert("Erro ao salvar alterações: " + err.message);
      }
    });
  }

  loadUser();
});
