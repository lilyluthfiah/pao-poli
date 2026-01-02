console.log("beasiswa-mahasiswa.js LOADED ✅");

const BASE = "/pao-poli/pengumuman-akademik-online";
const API  = `${BASE}/backend/api/mahasiswa/beasiswa`;

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

async function fetchJson(url, opts = {}) {
  const res = await fetch(url, { credentials: "include", ...opts });
  const data = await res.json().catch(() => ({}));

  // kalau backend pakai success false tapi HTTP 200
  if (data && data.success === false) {
    throw new Error(data.message || "Request gagal");
  }

  if (!res.ok) {
    throw new Error(data.message || `HTTP ${res.status}`);
  }
  return data;
}

function setBeasiswaAvailability(isAvailable) {
  const submitBtn = document.querySelector('#formDaftar button[type="submit"]');
  if (submitBtn) submitBtn.disabled = !isAvailable;

  const select = document.getElementById("pilihBeasiswa");
  if (select) select.disabled = !isAvailable;
}

// ===============================
// STEP DETAIL
// ===============================
function toggleStepDetail(show) {
  const step = document.getElementById("stepDetail");
  if (!step) return;
  step.classList.toggle("d-none", !show);
}

// ===============================
// PREVIEW
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
    console.warn("Elemen preview beasiswa tidak lengkap (cek ID di HTML).");
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
// RENDER BERKAS
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
  } 

  // upload bebas
  const freeBox = document.createElement("div");
  freeBox.className = "mb-2";
  freeBox.innerHTML = `
    <label class="form-label">Upload Berkas </label>
    <input type="file" class="form-control" id="berkasUpload" name="berkas[]" multiple>
    <div class="form-text">Upload file sesuai persyaratan beasiswa.</div>
  `;
  area.appendChild(freeBox);
}

// ===============================
// LOAD BEASISWA
// ===============================
async function loadBeasiswa() {
  const select = document.getElementById("pilihBeasiswa");
  if (!select) return;

  try {
    const json = await fetchJson(`${API}/list.php`);
    beasiswaList = json.data || [];

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
  } catch (err) {
    console.error("ERROR loadBeasiswa:", err);
    beasiswaList = [];
    setBeasiswaAvailability(false);
    select.innerHTML = `<option value="">-- Gagal memuat beasiswa --</option>`;
  }
}

// ===============================
// LOAD PENGAJUAN
// ===============================
async function loadPengajuan() {
  try {
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
        <td>${escapeHtml(p.tanggal_daftar || "-")}</td>
        <td>${escapeHtml(p.nama_beasiswa || "-")}</td>
        <td>${escapeHtml(p.jenis || "-")}</td>
        <td>${escapeHtml(p.rekening || "-")}</td>
        <td>${escapeHtml(String(p.jumlah_berkas || 0))} berkas</td>
        <td><span class="badge bg-${badge}">${escapeHtml(p.status || "-")}</span></td>
        <td>-</td>
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
  } catch (err) {
    console.error("ERROR loadPengajuan:", err);
  }
}

// ===============================
// DOM READY
// ===============================
document.addEventListener("DOMContentLoaded", async () => {
  await loadBeasiswa();
  await loadPengajuan();

  toggleStepDetail(false);
  showPreview(null);
  renderBerkasRules([]);

  // klik ajukan -> kalau belum ada beasiswa, alert
  const btnAjukan = document.querySelector('[data-bs-target="#modalDaftar"]');
  if (btnAjukan) {
    btnAjukan.addEventListener("click", (e) => {
      if (!beasiswaList.length) {
        e.preventDefault();
        e.stopPropagation();
        e.stopImmediatePropagation();
        alert("Beasiswa belum tersedia.");
        return false;
      }
    }, true);
  }

  // pilih beasiswa -> tampilkan form detail
  const select = document.getElementById("pilihBeasiswa");
  if (select) {
    select.addEventListener("change", () => {
      const id = select.value;

      showPreview(null);
      renderBerkasRules([]);
      selectedBerkasRules = [];

      if (!id) {
        toggleStepDetail(false);
        return;
      }

      toggleStepDetail(true);

      const b = beasiswaList.find((x) => String(x.id) === String(id));
      showPreview(b);
    });
  }

  // reset modal
  const modalDaftarEl = document.getElementById("modalDaftar");
  const form = document.getElementById("formDaftar");

  if (modalDaftarEl) {
    modalDaftarEl.addEventListener("show.bs.modal", () => {
      form?.reset();
      toggleStepDetail(false);
      showPreview(null);
      renderBerkasRules([]);
      if (select) select.value = "";
    });
  }

  // submit pengajuan
  if (form) {
    form.addEventListener("submit", async (e) => {
      e.preventDefault();

      const beasiswaId = document.getElementById("pilihBeasiswa")?.value;
      if (!beasiswaId) return alert("Pilih beasiswa terlebih dahulu.");

      // WAJIB: pastikan input rekening punya name="rekening"
      const rekeningVal = document.getElementById("rekening")?.value?.trim();
      if (!rekeningVal) return alert("Nomor rekening wajib diisi.");

      const fd = new FormData(form);
      fd.append("beasiswa_id", beasiswaId);

      try {
        const res = await fetchJson(`${API}/submit_pengajuan.php`, {
          method: "POST",
          body: fd,
        });

        alert(res.message || "Pengajuan berhasil.");
        bootstrap.Modal.getInstance(modalDaftarEl)?.hide();

        await loadPengajuan();
        form.reset();
        toggleStepDetail(false);
        showPreview(null);
        renderBerkasRules([]);
      } catch (err) {
        console.error("ERROR submit:", err);
        alert(err.message || "Gagal submit pengajuan.");
      }
    });
  }
});
