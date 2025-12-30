const isLoggedIn = localStorage.getItem("isLoggedIn") === "true";
const username = localStorage.getItem("username");
const role = localStorage.getItem("userRole");

if (!isLoggedIn || !username || !role) {
  alert("Silakan login terlebih dahulu!");
  window.location.href = "/auth/login.html";
}

if (role !== "dosen") {
  alert("Akses ditolak!");
  window.location.href = "/mahasiswa/pages/dashboard-mahasiswa.html";
}

// ===============================
// GREETING
// ===============================
document.getElementById("greetingNama").textContent = `Halo, ${username}`;
document.getElementById("greetingRole").textContent =
  `SELAMAT DATANG DI PAO-POLIBATAM (${role.toUpperCase()})`;

// ===============================
// LOGOUT
// ===============================
document.querySelector(".logout-btn").addEventListener("click", () => {
  localStorage.removeItem("isLoggedIn");
  localStorage.removeItem("username");
  localStorage.removeItem("userRole");
  alert("Berhasil logout!");
  window.location.href = "../auth/login.html";
});

// ===============================
// NOTIFIKASI
// ===============================
const notifPopup = document.getElementById("notifPopup");
const notifIcon = document.getElementById("notifIcon");
const notifPopupContent = document.getElementById("notifPopupContent");

notifIcon.addEventListener("click", () => {
  notifPopup.classList.toggle("show");
  const notifText = localStorage.getItem("latestNotification") || "";
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

document.addEventListener("click", e => {
  if (!notifPopup.contains(e.target) && !notifIcon.contains(e.target)) {
    notifPopup.classList.remove("show");
  }
});

// ===============================
// PROFILE DROPDOWN
// ===============================
const profileIcon = document.getElementById("profileIcon");
const profileDropdown = document.getElementById("profileDropdown");

profileIcon.onclick = () => profileDropdown.classList.toggle("show");

document.addEventListener("click", e => {
  if (!profileDropdown.contains(e.target) && !profileIcon.contains(e.target)) {
    profileDropdown.classList.remove("show");
  }
});