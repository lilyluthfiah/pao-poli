document.addEventListener("DOMContentLoaded", function () {

  document.getElementById("MasukForm").addEventListener("submit", function (e) {

    const username = document.getElementById("username").value.trim();
    const password = document.getElementById("password").value.trim();
    const role = document.getElementById("role").value;

    if (!role || role === "Pilih") {
      alert("Silakan pilih role terlebih dahulu (Mahasiswa atau Dosen).");
      e.preventDefault();
      return;
    }

    if (username === "" || password === "") {
      alert("Nama dan password wajib diisi!");
      e.preventDefault();
      return;
    }

    // ✅ jangan preventDefault kalau semua valid
    // form akan POST ke php/login.php
  });

});