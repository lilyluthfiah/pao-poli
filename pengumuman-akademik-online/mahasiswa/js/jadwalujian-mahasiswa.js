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

// === LOAD JADWAL UJIAN ===
function loadJadwal() {
  const jadwal = JSON.parse(localStorage.getItem("jadwalUjian")) || [];
  const tableBody = document.getElementById("tableBody");
  tableBody.innerHTML = "";

  if (jadwal.length === 0) {
    tableBody.innerHTML =
      `<tr><td colspan="5" class="text-muted text-center">Belum ada jadwal ujian.</td></tr>`;
    return;
  }

  jadwal.forEach(item => {
    const row = `
      <tr>
        <td>${item.tanggal}</td>
        <td>${item.mataKuliah}</td>
        <td>${item.ruang}</td>
        <td>${item.waktu}</td>
        <td>${item.dosen}</td>
      </tr>`;
    tableBody.insertAdjacentHTML("beforeend", row);
  });
}

// === AUTO UPDATE ===
document.addEventListener("DOMContentLoaded", loadJadwal);
window.addEventListener("storage", loadJadwal);