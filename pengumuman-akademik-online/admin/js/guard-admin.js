const BASE = "/pao-poli/pengumuman-akademik-online";

(async function () {
  // tunggu DOM siap
  if (document.readyState === "loading") {
    await new Promise((r) =>
      document.addEventListener("DOMContentLoaded", r, { once: true })
    );
  }

  try {
    const res = await fetch(`${BASE}/backend/api/auth/me.php`, {
      credentials: "include",
      headers: { Accept: "application/json" },
    });

    const json = await res.json();

    // ====== SESUAIKAN FORMAT RESPONSE me.php ======
    // me.php kamu pakai helpers: success([...]) dan error(...)
    // Dari kode me.php kamu: success({ loggedIn: true, user: {...} })
    // Jadi ceknya pakai json.loggedIn, BUKAN json.success
    if (!res.ok || !json.loggedIn) {
      window.location.href = `${BASE}/login.php`; // sesuaikan bila login kamu beda
      return;
    }

    const username = json.user?.username || "Admin";
    const role = String(json.user?.role || "").toLowerCase();

    if (role !== "admin") {
      window.location.href = `${BASE}/login.php`;
      return;
    }

    // set greeting
    const namaEl = document.getElementById("greetingNama");
    const roleEl = document.getElementById("greetingRole");

    if (namaEl) namaEl.textContent = `Halo, ${username}`;
    if (roleEl) roleEl.textContent = `SELAMAT DATANG DI PAO-POLIBATAM`;

  } catch (e) {
    // kalau fetch gagal / session gak kebaca, langsung lempar ke login
    window.location.href = `${BASE}/login.php`;
  }
})();
