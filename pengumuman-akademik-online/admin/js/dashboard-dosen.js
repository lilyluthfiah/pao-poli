// ===============================
// AMBIL USER DARI SESSION (me.php) + GREETING
// ===============================
const BASE = "/pao-poli/pengumuman-akademik-online";

async function initGreetingFromSession() {
  try {
    const res = await fetch(`${BASE}/backend/api/auth/me.php`, {
      credentials: "include", // WAJIB: agar cookie session kebawa
    });

    const json = await res.json();
    if (!res.ok) throw new Error(json.message || "Unauthorized");

    const username = json.user.username;
    const role = json.user.role;


    // Greeting
    document.getElementById("greetingNama").textContent = `Halo, ${username}`;
    document.getElementById("greetingRole").textContent =
      `SELAMAT DATANG DI PAO-POLIBATAM`;

    // (Opsional) kalau dashboard ini khusus admin/dosen, aktifkan cek akses:
    // if (role !== "admin" && role !== "dosen") {
    //   alert("Akses ditolak!");
    //   window.location.href = `${BASE}/mahasiswa/pages/dashboard-mahasiswa.html`;
    // }

  } catch (err) {
    alert("Silakan login terlebih dahulu!");
    window.location.href = `${BASE}/auth/login.html`;
  }
}

document.addEventListener("DOMContentLoaded", initGreetingFromSession);

// ===============================
// LOGOUT (SESSION)
// ===============================
document.querySelector(".logout-btn")?.addEventListener("click", async () => {
  try {
    await fetch(`${BASE}/backend/api/auth/logout.php`, {
      method: "POST",
      credentials: "include",
    });
  } catch (e) {}

  alert("Berhasil logout!");
  window.location.href = `${BASE}/auth/login.html`;
});

// ===============================
// NOTIFIKASI (tetap pakai localStorage)
// ===============================
const notifPopup = document.getElementById("notifPopup");
const notifIcon = document.getElementById("notifIcon");
const notifPopupContent = document.getElementById("notifPopupContent");

notifIcon?.addEventListener("click", () => {
  notifPopup?.classList.toggle("show");
  const notifText = localStorage.getItem("latestNotification") || "";

  if (!notifPopupContent) return;

  notifPopupContent.innerHTML = `
    <textarea id="notifInput" class="form-control mb-2" rows="3">${notifText}</textarea>
    <button class="btn btn-sm btn-primary w-100" id="saveNotif">Simpan</button>
  `;

  document.getElementById("saveNotif").onclick = () => {
    localStorage.setItem(
      "latestNotification",
      document.getElementById("notifInput").value
    );
    alert("Notifikasi disimpan!");
  };
});

document.addEventListener("click", (e) => {
  if (!notifPopup || !notifIcon) return;
  if (!notifPopup.contains(e.target) && !notifIcon.contains(e.target)) {
    notifPopup.classList.remove("show");
  }
});

// ===============================
// PROFILE DROPDOWN
// ===============================
const profileIcon = document.getElementById("profileIcon");
const profileDropdown = document.getElementById("profileDropdown");

profileIcon?.addEventListener("click", () => profileDropdown?.classList.toggle("show"));

document.addEventListener("click", (e) => {
  if (!profileDropdown || !profileIcon) return;
  if (!profileDropdown.contains(e.target) && !profileIcon.contains(e.target)) {
    profileDropdown.classList.remove("show");
  }
});
