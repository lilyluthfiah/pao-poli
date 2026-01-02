console.log("beasiswa-mahasiswa.js LOADED ✅");

const BASE = "/pao-poli/pengumuman-akademik-online";
const API = `${BASE}/backend/api/mahasiswa/beasiswa`;

// ===============================
// UTIL
// ===============================
function escapeHtml(s) {
  if (s === null || s === undefined) return "";
  return String(s).replace(/[&<>"']/g, (m) => ({
    "&": "&amp;",
    "<": "&lt;",
    ">": "&gt;",
    '"': "&quot;",
    "'": "&#39;",
  }[m]));
}

function fmtDate(d) {
  if (!d) return "-";
  return d;
}

// 🔥 fetch JSON yang TEGAS: kalau bukan JSON, lempar error
async function fetchJson(url, opts = {}) {
  const res = await fetch(url, { credentials: "include", ...opts });
  const text = await res.text();

  let data;
  try {
    data = JSON.parse(text);
  } catch (e) {
    console.error("❌ Response bukan JSON:", url, "\n", text);
    throw new Error("Response server bukan JSON (cek error PHP / warning).");
  }

  if (!res.ok) {
    throw new Error(data.message || `HTTP ${res.status}`);
  }
  return data;
}

function setBeasiswaAvailability(isAvailable) {
  // tombol ajukan jangan di-disable (karena mau pakai alert saat klik)
  const submitBtn = document.querySelector('#formDaftar button[type="submit"]');
  if (submitBtn) submitBtn.disabled = !isAvailable;

  const select = document.getElementById("pilihBeasiswa");
  if (select) select.disabled = !isAvailable;
}

// ===============================
// SHOW/HIDE STEP DETAIL
// ===============================
function toggleStepDetail(show) {
  const step = document.getElementById("stepDetail");
  if (!step) return;
  step.classList.toggle("d-none", !show);
}

// ===============================
// PREVIEW BEASISWA
// ===============================
function showPreview(beasiswa) {
  const wrap = document.getElementById("beasiswaPreview");
  const pvJudul = document.getElementById("pvJudul");
  const pvJenis = document.getElementById("pvJenis");
  const pvPenyelenggara = document.getElementById("pvPenyelenggara");
  const pvPeriode = document.getElementById("pvPeriode");
  const pvDeskripsi = document.getElementById("pvDeskripsi");
  const pvPdfLink = document.getElementById("pvPdfLink");

  if (!wrap || !pvJudul || !pvJenis || !pvPenyelenggara || !pvPeriode || !pvDeskripsi || !pvPdfLink) {
    console.warn("Elemen preview beasiswa tidak lengkap.");
    return;
  }

  if (!beasiswa) {
    wrap.classList.add("d-none");
    pvPdfLink.classList.add("d-none");
    pvPdfLink.removeAttribute("href");
    return;
  }

  pvJudul.textContent = beasiswa.nama || "-";
  pvJenis.textContent = beasiswa.jenis || "-";
  pvPenyelenggara.textContent = beasiswa.penyelenggara || "-";
  pvPeriode.textContent = `${fmtDate(beasiswa.tanggal_mulai)} s/d ${fmtDate(beasiswa.tanggal_akhir)}`;
  pvDeskripsi.textContent = beasiswa.deskripsi || "-";

  if (beasiswa.pdf_path) {
    pvPdfLink.classList.remove("d-none");
    pvPdfLink.href = (`${BASE}/${beasiswa.pdf_path}`).replaceAll("//", "/");
  } else {
    pvPdfLink.classList.add("d-none");
    pvPdfLink.removeAttribute("href");
  }

  wrap.classList.remove("d-none");
}

// ===============================
// STATE
// ===============================
let beasiswaList = [];
let selectedBerkasRules = [];

// ===============================
// RENDER SYARAT BERKAS + UPLOAD BEBAS
// ===============================
function renderBerkasRules(rules) {
  const area = document.getElementById("dynamicBerkasArea");
  const info = document.getElementById("infoJumlahBerkas");
  if (!area || !info) return;

  area.innerHTML = "";

  if (rules && rules.length) {
    info.innerHTML = `<div class="alert alert-light border mb-2">
      Syarat berkas: <b>${rules.length}</b> item (wajib/opsional).
    </div>`;
  } else {
    info.innerHTML = `<div class="alert alert-light border mb-2">
      Tidak ada syarat berkas khusus. Kamu tetap bisa upload berkas bebas.
    </div>`;
  }

  (rules || []).forEach((bk) => {
    const wajib = String(bk.wajib || "").toLowerCase() === "wajib";
    const accept =
      bk.tipe_file === "pdf" ? "application/pdf" :
      bk.tipe_file === "jpg" ? "image/jpeg" :
      bk.tipe_file === "png" ? "image/png" : "";

    const key = String(bk.id);

    const box = document.createElement("div");
    box.className = "mb-2 p-2 border rounded";
    box.innerHTML = `
      <label class="form-label">
        ${escapeHtml(bk.nama_berkas)}
        ${wajib ? "<span class='text-danger'>*</span>" : "<span class='text-muted'>(opsional)</span>"}
      </label>
      <input
        type="file"
        class="form-control"
        name="berkas_rule[${escapeHtml(key)}]"
        ${accept ? `accept="${accept}"` : ""}
        ${wajib ? "required" : ""}
      >
      <input type="hidden" name="nama_berkas_rule[${escapeHtml(key)}]" value="${escapeHtml(bk.nama_berkas)}">
    `;
    area.appendChild(box);
  });

  // upload bebas
  const freeBox = document.createElement("div");
  freeBox.className = "mb-2";
  freeBox.innerHTML = `
    <label class="form-label">Upload Berkas (boleh lebih dari 1)</label>
    <input type="file" class="form-control" id="berkasUpload" name="berkas[]" multiple>
    <div class="form-text">Kamu bisa pilih banyak file sekaligus.</div>
  `;
  area.appendChild(freeBox);
}

// ===============================
// LOAD BEASISWA
// ===============================
async function loadBeasiswa() {
  console.log("LOAD BEASISWA =>", `${API}/list.php`);

  const json = await fetchJson(`${API}/list.php`);
  console.log("RESP BEASISWA =>", json);

  beasiswaList = json.data || [];

  const select = document.getElementById("pilihBeasiswa");
  if (!select) return;

  select.innerHTML = `<option value="">-- Pilih Beasiswa --</option>`;

  if (!beasiswaList.length) {
    select.innerHTML = `<option value="">-- Beasiswa belum tersedia --</option>`;
    setBeasiswaAvailability(false);
    return;
  }

  setBeasiswaAvailability(true);

  beasiswaList.forEach((b) => {
    const opt = document.createElement("option");
    opt.value = b.id;
    opt.textContent = `${b.nama} — ${b.jenis}`;
    select.appendChild(opt);
  });

  // ✅ kalau cuma 1 beasiswa, auto pilih + trigger change
  if (beasiswaList.length === 1) {
    select.value = String(beasiswaList[0].id);
    select.dispatchEvent(new Event("change"));
  }
}

// ===============================
// LOAD PENGAJUAN
// ===============================
async function loadPengajuan() {
  const json = await fetchJson(`${API}/list_pengajuan.php`);
  const data = json.data || [];

  const tbody = document.getElementById("bodyPengajuan");
  if (!tbody) return;
  tbody.innerHTML = "";

  data.forEach((p, i) => {
    const badge =
      p.status === "DITERIMA" ? "success" :
      p.status === "DITOLAK" ? "danger" : "warning";

    const tr = document.createElement("tr");
    tr.innerHTML = `
      <td>${i + 1}</td>
      <td>${escapeHtml(p.tanggal_daftar || p.created_at || "-")}</td>
      <td>${escapeHtml(p.nama_beasiswa || "-")}</td>
      <td>${escapeHtml(p.jenis || "-")}</td>
      <td>${escapeHtml(p.rekening || "-")}</td>
      <td>${escapeHtml(String(p.jumlah_berkas || 0))} berkas</td>
      <td><span class="badge bg-${badge}">${escapeHtml(p.status || "-")}</span></td>
      <td>
        <button class="btn btn-sm btn-info me-1" data-view="${p.id}" title="Detail">
          <i class="bi bi-eye"></i>
        </button>
        <button class="btn btn-sm btn-danger" data-del="${p.id}" title="Hapus">
          <i class="bi bi-trash"></i>
        </button>
      </td>
    `;
    tbody.appendChild(tr);
  });

  if (window.$ && $.fn.DataTable) {
    if ($.fn.DataTable.isDataTable("#tabelPengajuan")) {
      $("#tabelPengajuan").DataTable().destroy();
    }
    $("#tabelPengajuan").DataTable({
      paging: false,
      searching: false,
      info: false,
    });
  }
}

// ===============================
// DOM READY
// ===============================
document.addEventListener("DOMContentLoaded", async () => {
  // awal: sembunyikan detail
  toggleStepDetail(false);

  try {
    await loadBeasiswa();
  } catch (e) {
    console.error("❌ loadBeasiswa error:", e);
    beasiswaList = [];
    setBeasiswaAvailability(false);
  }

  try {
    await loadPengajuan();
  } catch (e) {
    console.error("❌ loadPengajuan error:", e);
  }

  // ✅ ALERT HANYA SAAT KLIK AJUKAN BEASISWA
  const btnAjukan = document.querySelector('[data-bs-target="#modalDaftar"]');
  if (btnAjukan) {
    btnAjukan.addEventListener("click", (e) => {
      if (!beasiswaList.length) {
        e.preventDefault();
        e.stopPropagation();
        alert("Beasiswa belum tersedia.");
        return false;
      }
    }, true);
  }

  // pilih beasiswa => tampilkan stepDetail + preview + rules
  const select = document.getElementById("pilihBeasiswa");
  if (select) {
    select.addEventListener("change", async function () {
      const id = this.value;
      console.log("SELECT CHANGE =>", id);

      showPreview(null);
      renderBerkasRules([]);

      if (!id) {
        toggleStepDetail(false);
        return;
      }

      toggleStepDetail(true);

      const b = beasiswaList.find((x) => String(x.id) === String(id));
      showPreview(b);

      try {
        const json = await fetchJson(`${API}/detail.php?id=${encodeURIComponent(id)}`);
        selectedBerkasRules = json.data?.berkas || [];
        renderBerkasRules(selectedBerkasRules);
      } catch (err) {
        console.error("ERROR detail beasiswa:", err);
        renderBerkasRules([]);
      }
    });
  }

  // reset saat modal dibuka
  const modalEl = document.getElementById("modalDaftar");
  if (modalEl) {
    modalEl.addEventListener("show.bs.modal", () => {
      document.getElementById("formDaftar")?.reset();
      toggleStepDetail(false);
      showPreview(null);
      renderBerkasRules([]);
    });
  }
});
