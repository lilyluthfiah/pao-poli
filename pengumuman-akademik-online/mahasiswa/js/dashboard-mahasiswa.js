document.addEventListener("DOMContentLoaded", async () => {
  // CEK SESSION BACKEND (PHP)
  let me;
  try {
    const res = await fetch("../../backend/api/auth/me.php", {
      credentials: "include",
    });
    me = await res.json();
    if (!res.ok || !me.loggedIn) throw new Error("UNAUTH");
  } catch (e) {
    alert("Silakan login terlebih dahulu!");
    window.location.href = "../../auth/login.html"; // sesuaikan lokasi login kamu
    return;
  }

  // KUNCI ROLE MAHASISWA (read-only)
  if ((me.user?.role || "").toLowerCase() !== "mahasiswa") {
    alert("Akses ditolak (bukan mahasiswa).");
    window.location.href = "../../admin/page/dashboard-dosen.html"; // sesuaikan
    return;
  }

  // Greeting
  const greetNama = document.getElementById("greetingNama");
  const greetRole = document.getElementById("greetingRole");
  if (greetNama) greetNama.textContent = `Halo, ${me.user.username}`;
  if (greetRole)
    greetRole.textContent = `SELAMAT DATANG DI PAO-POLIBATAM (${String(
      me.user.role
    ).toUpperCase()})`;

  // Elemen header
  const notifIcon = document.getElementById("notifIcon");
  const notifPopup = document.getElementById("notifPopup");
  const profileIcon = document.getElementById("profileIcon");
  const profileDropdown = document.getElementById("profileDropdown");
  const logoutBtn = document.querySelector(".logout-btn");

  function closeAll() {
    notifPopup?.classList.remove("show");
    profileDropdown?.classList.remove("show");
  }

  notifIcon?.addEventListener("click", (e) => {
    e.stopPropagation();
    notifPopup?.classList.toggle("show");
    profileDropdown?.classList.remove("show");
  });

  profileIcon?.addEventListener("click", (e) => {
    e.stopPropagation();
    profileDropdown?.classList.toggle("show");
    notifPopup?.classList.remove("show");
  });

  notifPopup?.addEventListener("click", (e) => e.stopPropagation());
  profileDropdown?.addEventListener("click", (e) => e.stopPropagation());

  document.addEventListener("click", closeAll);

  logoutBtn?.addEventListener("click", () => {
    window.location.href = "../../backend/api/auth/logout.php"; // sesuaikan kalau beda
  });
});
