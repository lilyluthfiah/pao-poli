// ===============================
// UTIL
// ===============================
function escapeHtml(s) {
  if (!s) return "";
  return String(s).replace(/[&<>"']/g, m => ({
    "&":"&amp;","<":"&lt;",">":"&gt;","\"":"&quot;","'":"&#39;"
  }[m]));
}

let beasiswaList = [];

// ===============================
// LOAD BEASISWA
// ===============================
function loadBeasiswa() {
  fetch("api/get_beasiswa.php")
    .then(res => res.json())
    .then(data => {
      beasiswaList = data;

      const select = document.getElementById("pilihBeasiswa");
      select.innerHTML = '<option value="">-- Pilih Beasiswa --</option>';

      data.forEach(b => {
        const opt = document.createElement("option");
        opt.value = b.id;
        opt.textContent = `${b.nama} — ${b.jenis}`;
        select.appendChild(opt);
      });
    });
}

// ===============================
// LOAD PENGAJUAN
// ===============================
function loadPengajuan() {
  fetch("api/get_pengajuan.php")
    .then(res => res.json())
    .then(data => {
      const tbody = document.getElementById("bodyPengajuan");
      tbody.innerHTML = "";

      data.forEach((p, i) => {
        const tr = document.createElement("tr");
        tr.innerHTML = `
          <td>${i + 1}</td>
          <td>${p.created_at}</td>
          <td>${escapeHtml(p.nama_beasiswa)}</td>
          <td>${escapeHtml(p.jenis)}</td>
          <td>${escapeHtml(p.rekening)}</td>
          <td>${p.jumlah_berkas} berkas</td>
          <td>
            <span class="badge bg-${
              p.status === "Diterima" ? "success" :
              p.status === "Ditolak" ? "danger" : "warning"
            }">${p.status}</span>
          </td>
          <td>
            <button class="btn btn-sm btn-info me-1" data-view="${p.id}">
              <i class="bi bi-eye"></i>
            </button>
            <button class="btn btn-sm btn-danger" data-del="${p.id}">
              <i class="bi bi-trash"></i>
            </button>
          </td>
        `;
        tbody.appendChild(tr);
      });

      if ($.fn.DataTable.isDataTable("#tabelPengajuan")) {
        $("#tabelPengajuan").DataTable().destroy();
      }

      $("#tabelPengajuan").DataTable({
        paging: false,
        searching: false,
        info: false
      });
    });
}

// ===============================
// RENDER BERKAS
// ===============================
function renderBerkas(beasiswaId) {
  const area = document.getElementById("dynamicBerkasArea");
  area.innerHTML = "";

  const b = beasiswaList.find(x => x.id == beasiswaId);
  if (!b) return;

  b.berkas.forEach((bk, i) => {
    area.innerHTML += `
      <div class="mb-2 p-2 border rounded">
        <label class="form-label">
          ${bk.nama} ${bk.wajib ? "<span class='text-danger'>*</span>" : ""}
        </label>
        <input type="file"
               name="berkas[${i}]"
               class="form-control"
               ${bk.wajib ? "required" : ""}>
        <input type="hidden" name="nama_berkas[${i}]" value="${bk.nama}">
      </div>
    `;
  });
}

// ===============================
// DOM READY
// ===============================
document.addEventListener("DOMContentLoaded", () => {
  loadBeasiswa();
  loadPengajuan();

  const modal = new bootstrap.Modal(document.getElementById("modalDaftar"));

  document.getElementById("btnAjukan").onclick = () => {
    document.getElementById("formDaftar").reset();
    document.getElementById("dynamicBerkasArea").innerHTML = "";
    modal.show();
  };

  document.getElementById("pilihBeasiswa").onchange = function () {
    renderBerkas(this.value);
  };

  // ===============================
  // SUBMIT PENGAJUAN
  // ===============================
  document.getElementById("formDaftar").onsubmit = e => {
    e.preventDefault();

    const formData = new FormData(e.target);

    fetch("api/submit_pengajuan.php", {
      method: "POST",
      body: formData
    })
    .then(res => res.json())
    .then(res => {
      if (res.status === "success") {
        alert("Pengajuan berhasil!");
        modal.hide();
        loadPengajuan();
      } else {
        alert(res.message);
      }
    });
  };

  // ===============================
  // DELETE
  // ===============================
  bodyPengajuan.onclick = e => {
    const del = e.target.closest("[data-del]");
    if (!del) return;

    if (!confirm("Hapus pengajuan ini?")) return;

    fetch("api/delete_pengajuan.php", {
      method: "POST",
      headers: {"Content-Type": "application/x-www-form-urlencoded"},
      body: "id=" + del.dataset.del
    })
    .then(res => res.json())
    .then(() => loadPengajuan());
  };
});
