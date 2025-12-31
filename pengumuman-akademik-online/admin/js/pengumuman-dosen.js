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

// ===============================
// PENGUMUMAN CRUD (Tambah, List, Hapus, Edit)
// ===============================
const API_LIST   = `${BASE}/backend/api/admin/beasiswa/pengumuman/list.php`;
const API_ADD    = `${BASE}/backend/api/admin/beasiswa/pengumuman/tambah.php`;
const API_DELETE = `${BASE}/backend/api/admin/beasiswa/pengumuman/hapus.php`;
const API_UPDATE = `${BASE}/backend/api/admin/beasiswa/pengumuman/update.php`;

const addBtn = document.getElementById("addBtn");
const tableBody = document.getElementById("tableBody");

// Modal elements (pastikan modalnya ada di HTML)
const modalEl = document.getElementById("modalPengumuman");
const form = document.getElementById("formPengumuman");
const alertBox = document.getElementById("alertPengumuman");

const modalTitle = document.getElementById("modalTitle");
const hiddenId = document.getElementById("pengumumanId");
const judulEl = document.getElementById("judul");
const deskripsiEl = document.getElementById("deskripsi");

const modal = modalEl ? new bootstrap.Modal(modalEl) : null;

function escapeHtml(str) {
  return String(str)
    .replaceAll("&", "&amp;")
    .replaceAll("<", "&lt;")
    .replaceAll(">", "&gt;")
    .replaceAll('"', "&quot;")
    .replaceAll("'", "&#039;");
}

function showAlert(msg, type = "danger") {
  if (!alertBox) return;
  alertBox.className = `alert alert-${type}`;
  alertBox.textContent = msg;
  alertBox.classList.remove("d-none");
}

function hideAlert() {
  alertBox?.classList.add("d-none");
}

function resetFormToAdd() {
  hideAlert();
  if (modalTitle) modalTitle.textContent = "Tambah Pengumuman";
  if (hiddenId) hiddenId.value = "";
  form?.reset();
}

async function loadPengumuman() {
  if (!tableBody) return;
  tableBody.innerHTML = `<tr><td colspan="5" class="text-center text-muted">Memuat...</td></tr>`;

  try {
    const res = await fetch(API_LIST, { credentials: "include" });
    const json = await res.json();

    if (!res.ok || !json.success) throw new Error(json.message || "Gagal memuat data.");

    const rows = json.data || [];
    if (!rows.length) {
      tableBody.innerHTML = `<tr><td colspan="5" class="text-center text-muted">Belum ada pengumuman.</td></tr>`;
      return;
    }
   
  tableBody.innerHTML = rows.map(r => `
  <tr>
    <td>${escapeHtml(r.id)}</td>
    <td>${escapeHtml(r.judul)}</td>
    <td>${escapeHtml(r.deskripsi)}</td>
    <td>${escapeHtml(r.tanggal || "-")}</td>
    <td class="text-center">
      <button class="btn btn-sm btn-warning me-1" data-action="edit"
        data-id="${escapeHtml(r.id)}"
        data-judul="${escapeHtml(r.judul)}"
        data-deskripsi="${escapeHtml(r.deskripsi)}">
        <i class="bi bi-pencil"></i>
      </button>
      <button class="btn btn-sm btn-danger" data-action="delete" data-id="${escapeHtml(r.id)}">
        <i class="bi bi-trash"></i>
      </button>
    </td>
  </tr>
`).join("");


  } catch (e) {
    tableBody.innerHTML = `<tr><td colspan="5" class="text-center text-danger">${escapeHtml(e.message)}</td></tr>`;
  }
}

// klik tombol tambah -> buka modal
addBtn?.addEventListener("click", () => {
  if (!modal) {
    alert("Modal belum ada. Tempel kode modal #modalPengumuman ke HTML dulu.");
    return;
  }
  resetFormToAdd();
  modal.show();
});

// submit form -> tambah / update
form?.addEventListener("submit", async (e) => {
  e.preventDefault();
  hideAlert();

  const payload = {
    id: hiddenId?.value ? Number(hiddenId.value) : null,
    judul: judulEl?.value.trim(),
    deskripsi: deskripsiEl?.value.trim(),
  };

  if (!payload.judul || !payload.deskripsi) {
    showAlert("Judul dan deskripsi wajib diisi.", "warning");
    return;
  }

  const endpoint = payload.id ? API_UPDATE : API_ADD;

  try {
    const res = await fetch(endpoint, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      credentials: "include",
      body: JSON.stringify(payload),
    });

    const json = await res.json();
    if (!res.ok || !json.success) throw new Error(json.message || "Gagal menyimpan.");

    modal?.hide();
    await loadPengumuman();
  } catch (err) {
    showAlert(err.message || "Terjadi error", "danger");
  }
});

// klik edit/hapus dari tabel
tableBody?.addEventListener("click", async (e) => {
  const btn = e.target.closest("button[data-action]");
  if (!btn) return;

  const action = btn.dataset.action;

  if (action === "edit") {
    if (!modal) return;
    hideAlert();
    modalTitle.textContent = "Edit Pengumuman";
    hiddenId.value = btn.dataset.id || "";
    judulEl.value = btn.dataset.judul || "";
    deskripsiEl.value = btn.dataset.deskripsi || "";
    modal.show();
    return;
  }

  if (action === "delete") {
    const id = Number(btn.dataset.id || 0);
    if (!id) return;

    if (!confirm(`Hapus pengumuman ID ${id}?`)) return;

    try {
      const res = await fetch(API_DELETE, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        credentials: "include",
        body: JSON.stringify({ id }),
      });

      const json = await res.json();
      if (!res.ok || !json.success) throw new Error(json.message || "Gagal menghapus.");

      await loadPengumuman();
    } catch (err) {
      alert(err.message || "Terjadi error");
    }
  }
});

// load tabel saat halaman dibuka
document.addEventListener("DOMContentLoaded", loadPengumuman);

