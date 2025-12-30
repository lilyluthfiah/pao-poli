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

// === CRUD JADWAL KULIAH ===
const tableBody = document.getElementById("tableBody");
const modal = new bootstrap.Modal(document.getElementById("scheduleModal"));
const saveBtn = document.getElementById("saveBtn");
const addScheduleBtn = document.getElementById("addScheduleBtn");
let editingIndex = null;

function loadJadwal() {
  const jadwal = JSON.parse(localStorage.getItem("jadwalKuliah")) || [];
  tableBody.innerHTML = "";
  jadwal.forEach((item, index) => {
    const row = `
      <tr>
        <td>${item.tanggal}</td>
        <td>${item.mataKuliah}</td>
        <td>${item.ruang}</td>
        <td>${item.waktu}</td>
        <td>${item.dosen}</td>
        <td>
          <button class="btn btn-sm btn-warning me-2" onclick="editJadwal(${index})"><i class="bi bi-pencil"></i></button>
          <button class="btn btn-sm btn-danger" onclick="hapusJadwal(${index})"><i class="bi bi-trash"></i></button>
        </td>
      </tr>`;
    tableBody.insertAdjacentHTML("beforeend", row);
  });
}

addScheduleBtn.addEventListener("click", () => {
  editingIndex = null;
  document.getElementById("modalTitle").textContent = "Tambah Jadwal";
  document.getElementById("tanggal").value = "";
  document.getElementById("mataKuliah").value = "";
  document.getElementById("ruang").value = "";
  document.getElementById("waktu").value = "";
  document.getElementById("dosen").value = "";
  modal.show();
});

saveBtn.addEventListener("click", () => {
  const jadwal = JSON.parse(localStorage.getItem("jadwalKuliah")) || [];
  const newData = {
    tanggal: document.getElementById("tanggal").value,
    mataKuliah: document.getElementById("mataKuliah").value,
    ruang: document.getElementById("ruang").value,
    waktu: document.getElementById("waktu").value,
    dosen: document.getElementById("dosen").value
  };
  if (editingIndex === null) jadwal.push(newData);
  else jadwal[editingIndex] = newData;
  localStorage.setItem("jadwalKuliah", JSON.stringify(jadwal));
  loadJadwal();
  modal.hide();
});

window.editJadwal = (index) => {
  const jadwal = JSON.parse(localStorage.getItem("jadwalKuliah")) || [];
  const data = jadwal[index];
  editingIndex = index;
  document.getElementById("modalTitle").textContent = "Edit Jadwal";
  document.getElementById("tanggal").value = data.tanggal;
  document.getElementById("mataKuliah").value = data.mataKuliah;
  document.getElementById("ruang").value = data.ruang;
  document.getElementById("waktu").value = data.waktu;
  document.getElementById("dosen").value = data.dosen;
  modal.show();
};

window.hapusJadwal = (index) => {
  if (confirm("Yakin ingin menghapus jadwal ini?")) {
    const jadwal = JSON.parse(localStorage.getItem("jadwalKuliah")) || [];
    jadwal.splice(index, 1);
    localStorage.setItem("jadwalKuliah", JSON.stringify(jadwal));
    window.dispatchEvent(new Event("storage"));
    loadJadwal();
  }
};

loadJadwal();