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
notifIcon.addEventListener("click", () => notifPopup.classList.toggle("show"));
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

// === LOAD JADWAL KULIAH ===
function loadJadwal() {
  const jadwal = JSON.parse(localStorage.getItem("jadwalKuliah")) || [];
  const tableBody = document.getElementById("tableBody");
  tableBody.innerHTML = "";

  if (jadwal.length === 0) {
    tableBody.innerHTML = `<tr><td colspan="5" class="text-muted text-center">Belum ada jadwal kuliah.</td></tr>`;
    return;
  }

  jadwal.forEach(item => {
    const tanggalObj = new Date(item.tanggal);
    const hari = tanggalObj.toLocaleDateString("id-ID", { weekday: "long" }); // ubah tanggal ke nama hari
    
    const row = `
      <tr>
        <td>${hari}</td>
        <td>${item.mataKuliah}</td>
        <td>${item.ruang}</td>
        <td>${item.waktu}</td>
        <td>${item.dosen}</td>
      </tr>`;
    tableBody.insertAdjacentHTML("beforeend", row);
  });
}
document.addEventListener("DOMContentLoaded", loadJadwal);
window.addEventListener("storage", loadJadwal);