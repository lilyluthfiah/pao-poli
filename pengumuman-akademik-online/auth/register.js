window.addEventListener("DOMContentLoaded", () => {
  const form = document.querySelector("#registerForm");
  const usernameEl = document.querySelector("#username");
  const passwordEl = document.querySelector("#password");
  const password2El = document.querySelector("#password2");
  const roleEl = document.querySelector("#role");
  const alertBox = document.querySelector("#alertBox");

  const toggle1 = document.querySelector("#togglePassword");
  const toggle2 = document.querySelector("#togglePassword2");

  if (!form || !usernameEl || !passwordEl || !password2El || !roleEl) return;

  function show(msg, type = "danger") {
    if (!alertBox) return;
    alertBox.textContent = msg;
    alertBox.classList.remove("d-none");
    alertBox.classList.remove("alert-danger", "alert-success");
    alertBox.classList.add(type === "success" ? "alert-success" : "alert-danger");
  }

  function hide() {
    if (!alertBox) return;
    alertBox.textContent = "";
    alertBox.classList.add("d-none");
    alertBox.classList.remove("alert-danger", "alert-success");
  }

  function togglePw(btn, input) {
    if (!btn || !input) return;
    const icon = btn.querySelector("i");
    btn.addEventListener("click", () => {
      const hidden = input.type === "password";
      input.type = hidden ? "text" : "password";
      if (icon) icon.className = hidden ? "bi bi-eye-slash" : "bi bi-eye";
    });
  }

  togglePw(toggle1, passwordEl);
  togglePw(toggle2, password2El);

  form.addEventListener("submit", async (e) => {
    e.preventDefault();
    hide();

    const username = usernameEl.value.trim();
    const password = passwordEl.value;
    const password2 = password2El.value;
    const role = (roleEl.value || "").trim().toLowerCase();

    if (!username || !password || !password2 || !role) {
      show("Semua field wajib diisi.");
      return;
    }

    if (username.length < 3) {
      show("Username minimal 3 karakter.");
      return;
    }

    if (password.length < 6) {
      show("Password minimal 6 karakter.");
      return;
    }

    if (password !== password2) {
      show("Konfirmasi password tidak sama.");
      return;
    }

    try {
      const res = await fetch("../backend/api/auth/register.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ username, password, role })
      });

      const data = await res.json();

      if (!data.success) {
        show(data.message || "Registrasi gagal.");
        return;
      }

      show(data.message || "Registrasi berhasil. Silakan login.", "success");
      setTimeout(() => {
        window.location.href = "./login.html";
      }, 900);
    } catch (err) {
      console.error(err);
      show("Gagal terhubung ke server.");
    }
  });
});
