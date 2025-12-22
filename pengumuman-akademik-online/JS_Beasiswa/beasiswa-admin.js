console.log("✅ beasiswa-admin.js loaded");

const ENDPOINT_SIMPAN = "../backend/dosen/simpan_beasiswa.php";

function escapeHtml(str) {
  return String(str)
    .replaceAll("&", "&amp;")
    .replaceAll("<", "&lt;")
    .replaceAll(">", "&gt;")
    .replaceAll('"', "&quot;")
    .replaceAll("'", "&#039;");
}

document.addEventListener("DOMContentLoaded", () => {
  const modalEl = document.getElementById("beasiswaModal");
  const modal = modalEl ? bootstrap.Modal.getOrCreateInstance(modalEl) : null;

  // ===== TAB NAV =====
  const tabs = Array.from(document.querySelectorAll("#beasiswaTabs button[data-bs-toggle='tab']"));
  const prevTabBtn = document.getElementById("prevTabBtn");
  const nextTabBtn = document.getElementById("nextTabBtn");

  function getActiveIndex() {
    return tabs.findIndex(t => t.classList.contains("active"));
  }
  function showTab(index) {
    if (index < 0 || index >= tabs.length) return;
    new bootstrap.Tab(tabs[index]).show();
  }
  function updateNavButtons() {
    const i = getActiveIndex();
    if (prevTabBtn) prevTabBtn.disabled = i <= 0;
    if (nextTabBtn) nextTabBtn.disabled = i >= tabs.length - 1;
  }

  prevTabBtn?.addEventListener("click", () => showTab(getActiveIndex() - 1));
  nextTabBtn?.addEventListener("click", () => showTab(getActiveIndex() + 1));
  tabs.forEach(t => t.addEventListener("shown.bs.tab", updateNavButtons));

  modalEl?.addEventListener("shown.bs.modal", () => {
    showTab(0);
    updateNavButtons();
  });

  updateNavButtons();

  // ===== CLICK DEBUGGER (biar kita tau klik sampai atau ketutup) =====
  document.addEventListener("click", (e) => {
    if (e.target.closest("#addPersyaratanBtn")) console.log("✅ KLIK: addPersyaratanBtn");
    if (e.target.closest("#addBerkasBtn")) console.log("✅ KLIK: addBerkasBtn");
    if (e.target.closest("#prevTabBtn")) console.log("✅ KLIK: prevTabBtn");
    if (e.target.closest("#nextTabBtn")) console.log("✅ KLIK: nextTabBtn");
    if (e.target.closest("#saveBtn")) console.log("✅ KLIK: saveBtn");
  });

  // ===== EVENT DELEGATION: PERSYARATAN =====
  document.addEventListener("click", (e) => {
    const btn = e.target.closest("#addPersyaratanBtn");
    if (!btn) return;

    const persyaratanInput = document.getElementById("persyaratanInput");
    const persyaratanList = document.getElementById("persyaratanList");
    if (!persyaratanInput || !persyaratanList) {
      console.error("❌ persyaratanInput/persyaratanList tidak ditemukan");
      return;
    }

    const text = persyaratanInput.value.trim();
    if (!text) return;

    const exists = Array.from(persyaratanList.querySelectorAll(".badge[data-value]"))
      .some(b => (b.getAttribute("data-value") || "").toLowerCase() === text.toLowerCase());
    if (exists) {
      persyaratanInput.value = "";
      return;
    }

    const badge = document.createElement("span");
    badge.className = "badge bg-primary text-wrap";
    badge.setAttribute("data-value", text);
    badge.style.padding = "8px 10px";
    badge.innerHTML = `${escapeHtml(text)}
      <button type="button" class="btn-close btn-close-white btn-sm ms-2" aria-label="Close"></button>`;
    persyaratanList.appendChild(badge);

    persyaratanInput.value = "";
    persyaratanInput.focus();
  });

  // hapus badge persyaratan
  document.addEventListener("click", (e) => {
    const closeBtn = e.target.closest("#persyaratanList .btn-close");
    if (!closeBtn) return;
    closeBtn.closest(".badge")?.remove();
  });

  // ===== EVENT DELEGATION: BERKAS =====
  document.addEventListener("click", (e) => {
    const btn = e.target.closest("#addBerkasBtn");
    if (!btn) return;

    const berkasNama = document.getElementById("berkasNama");
    const berkasTipe = document.getElementById("berkasTipe");
    const berkasSize = document.getElementById("berkasSize");
    const berkasWajib = document.getElementById("berkasWajib");
    const tableBerkasBody = document.querySelector("#tableBerkas tbody");

    if (!berkasNama || !berkasTipe || !berkasSize || !berkasWajib || !tableBerkasBody) {
      console.error("❌ elemen berkas tidak ditemukan");
      return;
    }

    const nama = berkasNama.value.trim();
    const tipe = berkasTipe.value.trim();
    const maxRaw = berkasSize.value.trim();
    const wajib = berkasWajib.value.trim();

    if (!nama) return;

    const maxNum = maxRaw === "" ? null : Number(maxRaw);
    if (maxNum !== null && (!Number.isFinite(maxNum) || maxNum <= 0)) {
      alert("Max MB harus angka > 0 (atau kosongkan).");
      return;
    }

    const exists = Array.from(tableBerkasBody.querySelectorAll("tr"))
      .some(tr => (tr.getAttribute("data-nama") || "").toLowerCase() === nama.toLowerCase());
    if (exists) {
      berkasNama.value = "";
      return;
    }

    const tr = document.createElement("tr");
    tr.setAttribute("data-nama", nama);
    tr.setAttribute("data-tipe", tipe);
    tr.setAttribute("data-maxmb", maxNum === null ? "" : String(Math.trunc(maxNum)));
    tr.setAttribute("data-status", wajib);

    tr.innerHTML = `
      <td>${escapeHtml(nama)}</td>
      <td>${escapeHtml(tipe)}</td>
      <td>${maxNum === null ? "-" : escapeHtml(String(Math.trunc(maxNum)))}</td>
      <td>${wajib === "wajib" ? "Wajib" : "Opsional"}</td>
      <td><button type="button" class="btn btn-sm btn-outline-danger" data-action="hapus-berkas">Hapus</button></td>
    `;

    tableBerkasBody.appendChild(tr);

    berkasNama.value = "";
    berkasSize.value = "";
    berkasWajib.value = "wajib";
    berkasNama.focus();
  });

  // hapus row berkas
  document.addEventListener("click", (e) => {
    const btn = e.target.closest("button[data-action='hapus-berkas']");
    if (!btn) return;
    btn.closest("tr")?.remove();
  });

  // ===== SIMPAN =====
  document.getElementById("saveBtn")?.addEventListener("click", async () => {
    const persyaratanList = document.getElementById("persyaratanList");
    const tableBerkasBody = document.querySelector("#tableBerkas tbody");

    const data = {
      nama: document.getElementById("namaBeasiswa").value.trim(),
      jenis: document.getElementById("jenisBeasiswa").value.trim(),
      penyelenggara: document.getElementById("penyelenggara").value.trim(),
      deskripsi: document.getElementById("deskripsi").value.trim(),
      tanggal_mulai: document.getElementById("tanggalMulai").value,
      tanggal_akhir: document.getElementById("tanggalAkhir").value,
      tanggal_seleksi: document.getElementById("tanggalSeleksi").value || null,
      tanggal_pengumuman: document.getElementById("tanggalPengumuman").value || null,
      min_ipk: document.getElementById("minIPK").value ? Number(document.getElementById("minIPK").value) : null,
      min_semester: document.getElementById("minSemester").value ? Number(document.getElementById("minSemester").value) : null,
      allowed_prodi: document.getElementById("allowedProdi").value.trim() || null,
      persyaratan: [],
      berkas: []
    };

    if (!data.nama || !data.jenis || !data.penyelenggara || !data.tanggal_mulai || !data.tanggal_akhir) {
      alert("Nama, Jenis, Penyelenggara, Tanggal Mulai, dan Tanggal Akhir wajib diisi.");
      return;
    }

    // persyaratan
    if (persyaratanList) {
      data.persyaratan = Array.from(persyaratanList.querySelectorAll(".badge[data-value]"))
        .map(b => (b.getAttribute("data-value") || "").trim())
        .filter(Boolean);
    }

    // berkas
    if (tableBerkasBody) {
      data.berkas = Array.from(tableBerkasBody.querySelectorAll("tr")).map(tr => {
        const nama = tr.getAttribute("data-nama") || "";
        const tipe = tr.getAttribute("data-tipe") || "pdf";
        const maxRaw = tr.getAttribute("data-maxmb") || "";
        const max = maxRaw === "" ? null : parseInt(maxRaw, 10);
        const wajib = tr.getAttribute("data-status") || "wajib";
        return { nama, tipe, max: Number.isFinite(max) ? max : null, wajib };
      });
    }

    try {
      const res = await fetch(ENDPOINT_SIMPAN, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(data)
      });
      const out = await res.json();
      alert(out.message || "Selesai");

      if (out.status === "success") {
        modal?.hide();
        // reset simple
        modalEl.querySelectorAll("input, textarea, select").forEach(el => el.value = "");
        document.getElementById("persyaratanList").innerHTML = "";
        document.querySelector("#tableBerkas tbody").innerHTML = "";
        showTab(0);
        updateNavButtons();
      }
    } catch (err) {
      console.error(err);
      alert("Gagal menyimpan data");
    }
  });
});
