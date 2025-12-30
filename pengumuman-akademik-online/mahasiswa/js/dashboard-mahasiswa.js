alert("JS dashboard mahasiswa ke-load!");
document.addEventListener("DOMContentLoaded", () => {
  console.log("JS dashboard mahasiswa ke-load!");


  // === LOGIN PROTECTION ===
  const usersData = JSON.parse(localStorage.getItem("usersData")) || [];
  const username = localStorage.getItem("username");
  const role = localStorage.getItem("userRole");
  const userData = usersData.find(u => u.nama === username && u.role === role);

  if (!userData) {
    alert("Silakan login terlebih dahulu!");
    window.location.href = "../auth/login.html";
    return;
  }

  // Greeting
  const greetNama = document.getElementById("greetingNama");
  const greetRole = document.getElementById("greetingRole");
  if (greetNama) greetNama.textContent = `Halo, ${userData.nama}`;
  if (greetRole) greetRole.textContent = `SELAMAT DATANG DI PAO-POLIBATAM (${userData.role.toUpperCase()})`;

  // Elemen
  const notifIcon = document.getElementById("notifIcon");
  const notifPopup = document.getElementById("notifPopup");
  const profileIcon = document.getElementById("profileIcon");
  const profileDropdown = document.getElementById("profileDropdown");
  const logoutBtn = document.getElementById("logoutBtn");


  console.log({ notifIcon, notifPopup, profileIcon, profileDropdown, logoutBtn });

  // Kalau elemen tidak ketemu, berarti path HTML beda / file beda
  if (!notifIcon || !notifPopup || !profileIcon || !profileDropdown || !logoutBtn) {
    alert("❌ Elemen header tidak ketemu. Pastikan file HTML yang dibuka benar dan id sama persis.");
    return;
  }

  // === NOTIFIKASI ===
  notifIcon.addEventListener("click", (e) => {
    e.stopPropagation();
    console.log("🔔 Notif diklik");
    notifPopup.classList.toggle("show");
    profileDropdown.classList.remove("show");
  });

  // === PROFILE ===
  profileIcon.addEventListener("click", (e) => {
    e.stopPropagation();
    console.log("👤 Profile diklik");
    profileDropdown.classList.toggle("show");
    notifPopup.classList.remove("show");
  });

  // jangan nutup saat klik dalam popup
  notifPopup.addEventListener("click", (e) => e.stopPropagation());
  profileDropdown.addEventListener("click", (e) => e.stopPropagation());

  // klik luar tutup semua
  document.addEventListener("click", () => {
    notifPopup.classList.remove("show");
    profileDropdown.classList.remove("show");
  });

  // === LOGOUT ===
  logoutBtn.addEventListener("click", () => {
    console.log("🚪 Logout diklik");
    localStorage.removeItem("isLoggedIn");
    localStorage.removeItem("username");
    localStorage.removeItem("userRole");
    alert("Berhasil logout!");
    window.location.href = "../auth/login.html";
  });
});