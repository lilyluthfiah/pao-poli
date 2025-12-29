window.addEventListener("DOMContentLoaded", () => {
  const form = document.querySelector("#loginForm");
  const usernameEl = document.querySelector("#username");
  const passwordEl = document.querySelector("#password");
  const roleEl = document.querySelector("#role");
  const alertBox = document.querySelector("#alertBox");

  const rememberEl = document.querySelector("#rememberMe");
  const toggleBtn = document.querySelector("#togglePassword");
  const toggleIcon = toggleBtn?.querySelector("i");

  if (!form || !usernameEl || !passwordEl || !roleEl) return;

  // ====== load remember me ======
  const saved = localStorage.getItem("pao_login_remember");
  if (saved) {
    try {
      const obj = JSON.parse(saved);
      if (obj.username) usernameEl.value = obj.username;
      if (obj.role) roleEl.value = obj.role; // admin/mahasiswa
      if (rememberEl) rememberEl.checked = true;
    } catch {}
  }

  // ====== toggle password ======
  if (toggleBtn && passwordEl) {
    toggleBtn.addEventListener("click", () => {
      const hidden = passwordEl.type === "password";
      passwordEl.type = hidden ? "text" : "password";
      if (toggleIcon) {
        toggleIcon.className = hidden ? "bi bi-eye-slash" : "bi bi-eye";
      }
      toggleBtn.title = hidden ? "Sembunyikan password" : "Tampilkan password";
      toggleBtn.setAttribute("aria-label", toggleBtn.title);
    });
  }

  function show(msg) {
    if (!alertBox) return;
    alertBox.textContent = msg;
    alertBox.classList.remove("d-none");
    alertBox.classList.add("alert-danger");
  }

  function hide() {
    if (!alertBox) return;
    alertBox.textContent = "";
    alertBox.classList.add("d-none");
    alertBox.classList.remove("alert-danger");
  }

  form.addEventListener("submit", async (e) => {
    e.preventDefault();
    hide();

    const username = usernameEl.value.trim();
    const password = passwordEl.value;
    const role = (roleEl.value || "").trim().toLowerCase();

    if (!username || !password || !role) {
      show("Username, password, dan role wajib diisi.");
      return;
    }

    // ====== save remember me (username+role saja, tidak simpan password) ======
    if (rememberEl?.checked) {
      localStorage.setItem("pao_login_remember", JSON.stringify({ username, role }));
    } else {
      localStorage.removeItem("pao_login_remember");
    }

    try {
      const res = await fetch("../backend/api/auth/login.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        credentials: "include",
        body: JSON.stringify({ username, password, role })
      });

      const data = await res.json();

      if (!data.success) {
        show(data.message || "Login gagal.");
        return;
      }

      if ((data.role || "").toLowerCase() === "admin") {
        window.location.href = "../admin/dashboard.php";
      } else {
        window.location.href = "../mahasiswa/dashboard.php";
      }
    } catch (err) {
      console.error(err);
      show("Gagal terhubung ke server.");
    }
  });
});
