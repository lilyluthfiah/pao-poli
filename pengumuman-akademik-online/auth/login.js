window.addEventListener("DOMContentLoaded", () => {
  // ====== SET BASE PATH PROJECT (WAJIB) ======
  // Sesuaikan dengan URL project kamu (lihat address bar)
  const BASE = "/pao-poli/pengumuman-akademik-online";

  const form = document.querySelector("#loginForm");
  const usernameEl = document.querySelector("#username");
  const passwordEl = document.querySelector("#password");
  const roleEl = document.querySelector("#role");
  const alertBox = document.querySelector("#alertBox");

  const rememberEl = document.querySelector("#rememberMe");
  const toggleBtn = document.querySelector("#togglePassword");
  const toggleIcon = toggleBtn ? toggleBtn.querySelector("i") : null;

  if (!form || !usernameEl || !passwordEl || !roleEl) return;

  // ====== helper alert ======
  function show(msg, type = "danger") {
    if (!alertBox) return;
    alertBox.textContent = msg;
    alertBox.classList.remove("d-none", "alert-danger", "alert-success", "alert-warning");
    alertBox.classList.add(`alert-${type}`);
  }

  function hide() {
    if (!alertBox) return;
    alertBox.textContent = "";
    alertBox.classList.add("d-none");
    alertBox.classList.remove("alert-danger", "alert-success", "alert-warning");
  }

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
      if (toggleIcon) toggleIcon.className = hidden ? "bi bi-eye-slash" : "bi bi-eye";
      toggleBtn.title = hidden ? "Sembunyikan password" : "Tampilkan password";
      toggleBtn.setAttribute("aria-label", toggleBtn.title);
    });
  }

  // ====== submit login ======
  form.addEventListener("submit", async (e) => {
    e.preventDefault();
    hide();

    const username = usernameEl.value.trim();
    const password = passwordEl.value;
    const role = (roleEl.value || "").trim().toLowerCase(); // admin/mahasiswa

    if (!username || !password || !role) {
      show("Username, password, dan role wajib diisi.", "warning");
      return;
    }

    // remember me: simpan username + role (tanpa password)
    if (rememberEl?.checked) {
      localStorage.setItem("pao_login_remember", JSON.stringify({ username, role }));
    } else {
      localStorage.removeItem("pao_login_remember");
    }

    try {
      const res = await fetch(`${BASE}/backend/api/auth/login.php`, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        credentials: "include",
        body: JSON.stringify({ username, password, role })
      });

      // kalau server balas bukan JSON, biar ketahuan
      const text = await res.text();
      let data;
      try { data = JSON.parse(text); }
      catch { throw new Error("Response bukan JSON: " + text); }

      if (!data.success) {
        show(data.message || "Login gagal.", "danger");
        return;
      }
      if ((data.role || "").toLowerCase() === "admin") {
          window.location.href = `${BASE}/admin/page/dashboard-dosen.html`;
      } else {
        window.location.href = `${BASE}/mahasiswa/pages/dashboard-mahasiswa.html`;
      }
    } catch (err) {
      console.error(err);
      show("Gagal terhubung ke server.", "danger");
    }
  });
});
