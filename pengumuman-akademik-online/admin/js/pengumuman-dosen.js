// ===============================
// AMBIL USER DARI SESSION (me.php) + GREETING
// ===============================
const BASE = "/pao-poli/pengumuman-akademik-online";

async function initGreetingFromSession() {
  try {
    const res = await fetch(`${BASE}/backend/api/auth/me.php`, {
      credentials: "include",
    });

    const json = await res.json();
    if (!res.ok) throw new Error(json.message || "Unauthorized");

    document.getElementById("greetingNama").textContent = `Halo, ${json.user.username}`;
    document.getElementById("greetingRole").textContent = `SELAMAT DATANG DI PAO-POLIBATAM`;
  } catch (err) {
    alert("Silakan login terlebih dahulu!");
    window.location.href = `${BASE}/auth/login.html`;
  }
}

document.addEventListener("DOMContentLoaded", initGreetingFromSession);

// ===============================
// LOGOUT
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
// ENDPOINT API PENGUMUMAN
// ===============================
const API_LIST   = `${BASE}/backend/api/admin/beasiswa/pengumuman/list.php`;
const API_ADD    = `${BASE}/backend/api/admin/beasiswa/pengumuman/tambah.php`;
const API_DELETE = `${BASE}/backend/api/admin/beasiswa/pengumuman/hapus.php`;
const API_UPDATE = `${BASE}/backend/api/admin/beasiswa/pengumuman/update.php`;

// ===============================
// ELEMENT
// ===============================
const addBtn = document.getElementById("addBtn");
const tableBody = document.getElementById("tableBody");

const modalEl = document.getElementById("modalPengumuman");
const form = document.getElementById("formPengumuman");
const alertBox = document.getElementById("alertPengumuman");

const modalTitle = document.getElementById("modalTitle");
const hiddenId = document.getElementById("pengumumanId");
const judulEl = document.getElementById("judul");
const deskripsiEl = document.getElementById("deskripsi");

// ===============================
// HELPER
// ===============================
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
  modalTitle.textContent = "Tambah Pengumuman";
  hiddenId.value = "";
  form.reset();
}

// ===============================
// LOAD DATA PENGUMUMAN
// ===============================
async function loadPengumuman() {
  if (!tableBody) return;

  tableBody.innerHTML = `<tr><td colspan="5" class="text-center text-muted">Memuat...</td></tr>`;

  try {
    const res = await fetch(API_LIST, { credentials: "include" });
    const json = await res.json();

    if (!res.ok || !json.success) {
      throw new Error(json.message || "Gagal memuat data");
    }

    const rows = json.data || [];
    if (!rows.length) {
      tableBody.innerHTML = `<tr><td colspan="5" class="text-center text-muted">Belum ada pengumuman</td></tr>`;
      return;
    }

    tableBody.innerHTML = rows.map((r) => `
      <tr>
        <td>${escapeHtml(r.id)}</td>
        <td>${escapeHtml(r.judul)}</td>
        <td>${escapeHtml(r.deskripsi)}</td>
        <td>${escapeHtml(r.tanggal || "-")}</td>
        <td class="text-center">
          <button class="btn btn-sm btn-warning me-1"
            data-action="edit"
            data-id="${escapeHtml(r.id)}"
            data-judul="${escapeHtml(r.judul)}"
            data-deskripsi="${escapeHtml(r.deskripsi)}">
            <i class="bi bi-pencil"></i>
          </button>
          <button class="btn btn-sm btn-danger"
            data-action="delete"
            data-id="${escapeHtml(r.id)}">
            <i class="bi bi-trash"></i>
          </button>
        </td>
      </tr>
    `).join("");

  } catch (err) {
    tableBody.innerHTML = `<tr><td colspan="5" class="text-center text-danger">${escapeHtml(err.message)}</td></tr>`;
  }
}

// ===============================
// BUTTON TAMBAH
// ===============================
addBtn?.addEventListener("click", () => {
  resetFormToAdd();
});

// ===============================
// SUBMIT FORM (TAMBAH / EDIT)
// ===============================
form?.addEventListener("submit", async (e) => {
  e.preventDefault();
  hideAlert();

  const payload = {
    id: hiddenId.value ? Number(hiddenId.value) : null,
    judul: judulEl.value.trim(),
    deskripsi: deskripsiEl.value.trim(),
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

    // baca response (aman walau bukan json)
    const text = await res.text();
    let json;
    try {
      json = JSON.parse(text);
    } catch {
      console.log("RESPON RAW:", text);
      throw new Error("Response bukan JSON (cek tambah.php / update.php).");
    }

    console.log("RESPON SIMPAN:", json);

    if (!res.ok || !json.success) {
      throw new Error(json.message || "Gagal menyimpan.");
    }

    bootstrap.Modal.getOrCreateInstance(modalEl).hide();
    await loadPengumuman();

  } catch (err) {
    showAlert(err.message || "Terjadi error");
  }
});

// ===============================
// EDIT & DELETE
// ===============================
tableBody?.addEventListener("click", async (e) => {
  const btn = e.target.closest("button[data-action]");
  if (!btn) return;

  const action = btn.dataset.action;

  if (action === "edit") {
    modalTitle.textContent = "Edit Pengumuman";
    hiddenId.value = btn.dataset.id || "";
    judulEl.value = btn.dataset.judul || "";
    deskripsiEl.value = btn.dataset.deskripsi || "";
    bootstrap.Modal.getOrCreateInstance(modalEl).show();
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
      if (!res.ok || !json.success) throw new Error(json.message || "Gagal menghapus");

      await loadPengumuman();
    } catch (err) {
      alert(err.message || "Terjadi error");
    }
  }
});

// ===============================
// LOAD DATA SAAT HALAMAN DIBUKA
// ===============================
document.addEventListener("DOMContentLoaded", () => {
  loadPengumuman();
});
