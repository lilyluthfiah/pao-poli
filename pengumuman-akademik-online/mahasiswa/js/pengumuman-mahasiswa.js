// === LOGIN PROTECTION ===
const usersData = JSON.parse(localStorage.getItem("usersData")) || [];
const username = localStorage.getItem("username");
const role = localStorage.getItem("userRole");
const userData = usersData.find(u => u.nama === username && u.role === role);

if (!userData) {
  alert("Silakan login terlebih dahulu!");
  window.location.href = "login.html";
}

// === GREETING ===
document.getElementById("greetingNama").textContent = `Halo, ${userData.nama}`;
document.getElementById("greetingRole").textContent = `SELAMAT DATANG DI PAO-POLIBATAM (${userData.role.toUpperCase()})`;

// === LOGOUT ===
document.querySelector(".logout-btn").addEventListener("click", () => {
  localStorage.removeItem("isLoggedIn");
  localStorage.removeItem("username");
  localStorage.removeItem("userRole");
  alert("Berhasil logout!");
  window.location.href = "login.html";
});

// === NOTIFIKASI ===
const notifPopup = document.getElementById("notifPopup");
const notifIcon = document.getElementById("notifIcon");
const notifPopupContent = document.getElementById("notifPopupContent");

notifIcon.addEventListener("click", () => {
  notifPopup.classList.toggle("show");
  const notifText = localStorage.getItem("latestNotification") || "Belum ada notifikasi.";
  notifPopupContent.innerHTML = `<p class="small text-muted">${notifText}</p>`;
});

document.addEventListener("click", (e) => {
  if (!notifPopup.contains(e.target) && !notifIcon.contains(e.target))
    notifPopup.classList.remove("show");
});

// === PROFILE DROPDOWN ===
const profileIcon = document.getElementById("profileIcon");
const profileDropdown = document.getElementById("profileDropdown");
profileIcon.addEventListener("click", () => profileDropdown.classList.toggle("show"));
document.addEventListener("click", (e) => {
  if (!profileDropdown.contains(e.target) && !profileIcon.contains(e.target))
    profileDropdown.classList.remove("show");
});

// === LOAD PENGUMUMAN DARI DOSEN ===
document.addEventListener("DOMContentLoaded", loadPengumuman);
window.addEventListener("storage", loadPengumuman);

function loadPengumuman() {
  const data = JSON.parse(localStorage.getItem("pengumumanDosen")) || [];
  const tableBody = document.getElementById("tableBody");

  if (data.length === 0) {
    tableBody.innerHTML = `<tr><td colspan="4" class="text-center text-muted">Belum ada pengumuman.</td></tr>`;
    return;
  }

  tableBody.innerHTML = "";
  data.forEach((item, i) => {
    const row = `
      <tr>
        <td>${i + 1}</td>
        <td>${item.judul}</td>
        <td>${item.deskripsi}</td>
        <td>${item.tanggal}</td>
      </tr>`;
    tableBody.insertAdjacentHTML("beforeend", row);
  });
}