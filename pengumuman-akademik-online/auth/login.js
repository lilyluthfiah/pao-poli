const BASE = "/pengumuman-akademik";

function showAlert(msg, type = "danger") {
  const box = document.getElementById("alertBox");
  if (!box) return alert(msg);
  box.className = `alert alert-${type}`;
  box.textContent = msg;
  box.classList.remove("d-none");
}

document.addEventListener("DOMContentLoaded", () => {
  const form = document.getElementById("loginForm");
  const btn = document.getElementById("btnLogin");

  form?.addEventListener("submit", async (e) => {
    e.preventDefault();

    btn && (btn.disabled = true);

    try {
      const fd = new FormData(form);

      // optional: kalau kamu punya dropdown role di form
      // pastikan name="role" di select, kalau tidak ada, hapus baris ini
      // const role = form.querySelector("[name=role]")?.value;

      const res = await fetch(`${BASE}/backend/api/auth/login.php`, {
        method: "POST",
        body: fd,
        credentials: "include",
      });

      const text = await res.text();

      let data;
      try {
        data = text ? JSON.parse(text) : null;
      } catch {
        console.error("RAW RESPONSE:", text);
        throw new Error("Server tidak mengembalikan JSON (cek login.php / path).");
      }

      if (!res.ok) {
        throw new Error(data?.message || "Login gagal.");
      }

      if (!data || !data.role) {
        throw new Error("Response login tidak lengkap (tidak ada role).");
      }

      // redirect by role
      if (data.role === "admin") {
        location.href = `${BASE}/app/admin/pages/dashboard.html`;
      } else if (data.role === "mahasiswa") {
        location.href = `${BASE}/app/public/pages/dashboard.html`;
      } else {
        throw new Error(`Role tidak dikenali: ${data.role}`);
      }
    } catch (err) {
      showAlert(err.message);
      console.error(err);
    } finally {
      btn && (btn.disabled = false);
    }
  });
});
