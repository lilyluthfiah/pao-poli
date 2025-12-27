// === GREETING dari SESSION (bukan localStorage) ===
const nama = document.body.dataset.nama;
const role = document.body.dataset.role;

document.getElementById("greetingNama").textContent = `Halo, ${nama}`;
document.getElementById("greetingRole").textContent =
  `SELAMAT DATANG DI PAO-POLIBATAM (${role.toUpperCase()})`;

// === LOGOUT (session) ===
document.querySelector(".logout-btn").addEventListener("click", () => {
  alert("Berhasil logout!");
  window.location.href = "../php/logout.php";
});

// === NOTIFIKASI (tetap pakai localStorage, bukan login) ===
const notifPopup = document.getElementById("notifPopup");
const notifIcon = document.getElementById("notifIcon");
const notifPopupContent = document.getElementById("notifPopupContent");

notifIcon.addEventListener("click", () => {
  notifPopup.classList.toggle("show");
  const notifText = localStorage.getItem("latestNotification") || "Belum ada notifikasi.";
  notifPopupContent.innerHTML = `
    <div class="mb-2">
      <textarea id="notifInput" class="form-control form-control-sm" rows="3">${notifText}</textarea>
    </div>
    <button class="btn btn-sm btn-primary w-100" id="saveNotif">Simpan Notifikasi</button>
  `;
  document.getElementById("saveNotif").addEventListener("click", () => {
    const newNotif = document.getElementById("notifInput").value.trim();
    if (newNotif) {
      localStorage.setItem("latestNotification", newNotif);
      alert("Notifikasi berhasil diperbarui!");
    } else {
      alert("Teks notifikasi tidak boleh kosong!");
    }
  });
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