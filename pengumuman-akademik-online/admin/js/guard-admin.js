const BASE = "/pao-poli/pengumuman-akademik-online";

(async () => {
  try {
    const res = await fetch(`${BASE}/backend/api/auth/me.php`, {
      credentials: "include"
    });

    if (!res.ok) throw new Error("unauthorized");
    const data = await res.json();

    if (!data.success) throw new Error("unauthorized");

    // role dari backend: admin / user
    if ((data.user?.role || "").toLowerCase() !== "admin") {
      window.location.href = `${BASE}/auth/login.html`;
    }
  } catch (e) {
    window.location.href = `${BASE}/auth/login.html`;
  }
})();
