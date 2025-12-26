/* =========================================================
   DOSEN - PENDAFTAR BEASISWA
   Konsisten endpoint:
   - GET beasiswa:        ../backend/mahasiswa/get_beasiswa.php
   - GET pendaftar:       ../backend/dosen/get_pendaftar.php?beasiswa_id=ID
   - UPDATE status:       ../backend/dosen/update_status.php
   - GET berkas pendaftar ../backend/dosen/get_berkas_pendaftar.php?pendaftar_id=ID
   Status DB: Menunggu | Disetujui | Ditolak
========================================================= */

const API = {
  beasiswa: "../backend/mahasiswa/get_beasiswa.php",
  pendaftar: "../backend/dosen/get_pendaftar.php",
  updateStatus: "../backend/dosen/update_status.php",
  berkas: "../backend/dosen/get_berkas_pendaftar.php",
};

document.addEventListener("DOMContentLoaded", () => {
  loadBeasiswa();
  loadPendaftar(""); // awal: kosong -> tampil "pilih beasiswa"
  const filter = document.getElementById("filterBeasiswa");
  filter?.addEventListener("change", () => loadPendaftar(filter.value));
});

/* ===============================
   LOAD BEASISWA KE DROPDOWN
================================ */
async function loadBeasiswa() {
  const select = document.getElementById("filterBeasiswa");
  if (!select) return;

  select.innerHTML = `<option value="">-- Pilih Beasiswa --</option>`;

  try {
    const res = await fetch(API.beasiswa);
    if (!res.ok) throw new Error("HTTP " + res.status);
    const data = await res.json();

    (Array.isArray(data) ? data : []).forEach((b) => {
      const opt = document.createElement("option");
      opt.value = b.id;
      opt.textContent = `${b.nama} (${b.jenis})`;
      select.appendChild(opt);
    });
  } catch (err) {
    console.error(err);
    alert("Gagal memuat data beasiswa");
  }
}

/* ===============================
   LOAD DATA PENDAFTAR
================================ */
async function loadPendaftar(idBeasiswa) {
  const tbody = document.getElementById("pendaftarTable");
  if (!tbody) return;

  if (!idBeasiswa) {
    tbody.innerHTML = `
      <tr>
        <td colspan="7" class="text-muted">Silakan pilih beasiswa terlebih dahulu.</td>
      </tr>`;
    return;
  }

  tbody.innerHTML = `
    <tr>
      <td colspan="7" class="text-muted">Memuat...</td>
    </tr>`;

  try {
    const url = `${API.pendaftar}?beasiswa_id=${encodeURIComponent(idBeasiswa)}`;
    const res = await fetch(url);
    if (!res.ok) throw new Error("HTTP " + res.status);
    const data = await res.json();

    tbody.innerHTML = "";

    if (!Array.isArray(data) || data.length === 0) {
      tbody.innerHTML = `
        <tr>
          <td colspan="7" class="text-muted">Tidak ada pendaftar untuk beasiswa ini</td>
        </tr>`;
      return;
    }

    data.forEach((mhs, index) => {
      const statusDb = (mhs.status || "Menunggu").trim(); // Menunggu/Disetujui/Ditolak
      tbody.insertAdjacentHTML(
        "beforeend",
        `
        <tr>
          <td>${index + 1}</td>
          <td>${escapeHtml(mhs.nama || "-")}</td>
          <td>${escapeHtml(mhs.nim || "-")}</td>
          <td>${escapeHtml(mhs.prodi || "-")}</td>
          <td>
            <button class="btn btn-info btn-sm" data-action="lihat-berkas" data-id="${mhs.id}">
              Lihat
            </button>
          </td>
          <td>
            <span class="badge ${badgeByStatus(statusDb)}">${escapeHtml(statusDb)}</span>
          </td>
          <td>
            <button class="btn btn-success btn-sm" data-action="set-status" data-id="${mhs.id}" data-status="Disetujui">
              Terima
            </button>
            <button class="btn btn-danger btn-sm" data-action="set-status" data-id="${mhs.id}" data-status="Ditolak">
              Tolak
            </button>
          </td>
        </tr>
        `
      );
    });
  } catch (err) {
    console.error(err);
    tbody.innerHTML = `
      <tr>
        <td colspan="7" class="text-danger">Gagal memuat data pendaftar</td>
      </tr>`;
    alert("Gagal memuat data pendaftar");
  }
}

/* ===============================
   EVENT DELEGATION (klik tombol di tabel)
================================ */
document.addEventListener("click", async (e) => {
  const btnBerkas = e.target.closest("button[data-action='lihat-berkas']");
  if (btnBerkas) {
    const id = btnBerkas.getAttribute("data-id");
    return lihatBerkas(id);
  }

  const btnStatus = e.target.closest("button[data-action='set-status']");
  if (btnStatus) {
    const id = btnStatus.getAttribute("data-id");
    const status = btnStatus.getAttribute("data-status"); // Disetujui/Ditolak
    return ubahStatus(id, status);
  }
});

/* ===============================
   UPDATE STATUS
================================ */
async function ubahStatus(idPendaftar, statusDb) {
  try {
    const res = await fetch(API.updateStatus, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ id: Number(idPendaftar), status: statusDb }),
    });

    const out = await res.json();

    if (!res.ok || out.status !== "success") {
      throw new Error(out.message || "Gagal update status");
    }

    const current = document.getElementById("filterBeasiswa")?.value || "";
    await loadPendaftar(current);
  } catch (err) {
    console.error(err);
    alert("Gagal mengubah status pendaftar");
  }
}

/* ===============================
   LIHAT BERKAS (MODAL)
================================ */
async function lihatBerkas(idPendaftar) {
  try {
    const url = `${API.berkas}?pendaftar_id=${encodeURIComponent(idPendaftar)}`;
    const res = await fetch(url);
    if (!res.ok) throw new Error("HTTP " + res.status);
    const data = await res.json();

    const modalBody = document.querySelector("#modalBerkas .modal-body");
    if (!modalBody) return;

    if (!Array.isArray(data) || data.length === 0) {
      modalBody.innerHTML = "Belum ada berkas yang diunggah.";
    } else {
      modalBody.innerHTML = `
        <ul class="mb-0">
          ${data
            .map((b) => {
              const nama = escapeHtml(b.nama_berkas || "Berkas");
              const url = escapeAttr(b.file_url || "#");
              return `<li>${nama} - <a href="${url}" target="_blank" rel="noopener">Buka</a></li>`;
            })
            .join("")}
        </ul>`;
    }

    const modalEl = document.getElementById("modalBerkas");
    bootstrap.Modal.getOrCreateInstance(modalEl).show();
  } catch (err) {
    console.error(err);
    alert("Gagal memuat berkas pendaftar");
  }
}

/* ===============================
   HELPERS
================================ */
function badgeByStatus(statusDb) {
  const s = String(statusDb || "").toLowerCase();
  if (s.includes("setuju")) return "bg-success";
  if (s.includes("tolak")) return "bg-danger";
  return "bg-secondary";
}

function escapeHtml(str) {
  return String(str)
    .replaceAll("&", "&amp;")
    .replaceAll("<", "&lt;")
    .replaceAll(">", "&gt;")
    .replaceAll('"', "&quot;")
    .replaceAll("'", "&#039;");
}
function escapeAttr(str) {
  return escapeHtml(str).replaceAll("`", "&#096;");
}
