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
document.getElementById("greetingRole").textContent =
  `SELAMAT DATANG DI PAO-POLIBATAM (${userData.role.toUpperCase()})`;

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

// === CRUD PENGUMUMAN ===
let editingIndex = null;
const tableBody = document.getElementById("tableBody");
const modal = new bootstrap.Modal(document.getElementById("modal"));

function loadData() {
  const data = JSON.parse(localStorage.getItem("pengumumanDosen")) || [];
  tableBody.innerHTML = "";
  data.forEach((item, i) => {
    const row = `<tr>
      <td>${i + 1}</td>
      <td>${item.judul}</td>
      <td>${item.deskripsi}</td>
      <td>${item.tanggal}</td>
      <td class="text-center">
        <button class="btn btn-sm btn-outline-primary me-2" onclick="editAnnouncement(${i})">
          <i class="bi bi-pencil"></i>
        </button>
        <button class="btn btn-sm btn-outline-danger" onclick="deleteAnnouncement(${i})">
          <i class="bi bi-trash"></i>
        </button>
      </td>
    </tr>`;
    tableBody.insertAdjacentHTML("beforeend", row);
  });
}

document.getElementById("addBtn").addEventListener("click", () => {
  editingIndex = null;
  document.getElementById("modalTitle").textContent = "Tambah Pengumuman";
  document.getElementById("titleInput").value = "";
  document.getElementById("descInput").value = "";
  document.getElementById("dateInput").value = "";
  modal.show();
});

document.getElementById("saveBtn").addEventListener("click", () => {
  const judul = document.getElementById("titleInput").value.trim();
  const deskripsi = document.getElementById("descInput").value.trim();
  const tanggal = document.getElementById("dateInput").value;
  if (!judul || !deskripsi || !tanggal) return alert("Lengkapi semua field!");

  const data = JSON.parse(localStorage.getItem("pengumumanDosen")) || [];
  const newData = { judul, deskripsi, tanggal };
  if (editingIndex !== null) data[editingIndex] = newData;
  else data.push(newData);

  localStorage.setItem("pengumumanDosen", JSON.stringify(data));
  window.dispatchEvent(new Event("storage"));
  modal.hide();
  loadData();
});

window.editAnnouncement = (i) => {
  const data = JSON.parse(localStorage.getItem("pengumumanDosen")) || [];
  const item = data[i];
  editingIndex = i;
  document.getElementById("modalTitle").textContent = "Edit Pengumuman";
  document.getElementById("titleInput").value = item.judul;
  document.getElementById("descInput").value = item.deskripsi;
  document.getElementById("dateInput").value = item.tanggal;
  modal.show();
};

window.deleteAnnouncement = (i) => {
  if (confirm("Yakin ingin menghapus pengumuman ini?")) {
    const data = JSON.parse(localStorage.getItem("pengumumanDosen")) || [];
    data.splice(i, 1);
    localStorage.setItem("pengumumanDosen", JSON.stringify(data));
    window.dispatchEvent(new Event("storage"));
    loadData();
  }
};

loadData();