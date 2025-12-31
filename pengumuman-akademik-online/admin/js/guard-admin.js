alert("GUARD ADMIN KELOAD ✅");

const BASE = "/pao-poli/pengumuman-akademik-online";

(async function () {
  // pastikan DOM sudah siap (kalau defer sudah dipakai, ini makin aman)
  if (document.readyState === "loading") {
    await new Promise((r) => document.addEventListener("DOMContentLoaded", r, { once: true }));
  }

  try {
    const res = await fetch(`${BASE}/backend/api/auth/me.php`, { credentials: "include" });
    const json = await res.json();

    console.log("GUARD me.php:", res.status, json);

    if (!res.ok || !json.success) throw new Error("unauthorized");

    const username = json.user?.username || "";
    const role = (json.user?.role || "").toLowerCase();

    if (role !== "admin") {
      window.location.href = `${BASE}/auth/login.html`;
      return;
    }

    const namaEl = document.getElementById("greetingNama");
    const roleEl = document.getElementById("greetingRole");

    console.log("EL:", namaEl, roleEl);

    if (namaEl) namaEl.textContent = `Halo, ${username}`;
    if (roleEl) roleEl.textContent = `SELAMAT DATANG DI PAO-POLIBATAM` ;

  } catch (e) {
    console.error("GUARD ERROR:", e);
    window.location.href = `${BASE}/auth/login.html`;
  }
})();
