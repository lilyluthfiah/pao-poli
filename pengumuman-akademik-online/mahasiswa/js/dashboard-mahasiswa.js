// === LOGIN PROTECTION ===
const usersData = JSON.parse(localStorage.getItem("usersData")) || [];
const username = localStorage.getItem("username");
const role = localStorage.getItem("userRole");

// Ambil data user yang sedang login
const userData = usersData.find(u => u.nama === username && u.role === role);

if (!userData) {
  alert("Silakan login terlebih dahulu!");
  window.location.href = "login.html";
}


// 🔹 Set greeting seperti halaman profile
const greetNama = document.getElementById("greetingNama");
const greetRole = document.getElementById("greetingRole");

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