document.addEventListener("DOMContentLoaded", function () {
  document.getElementById("MasukForm").addEventListener("submit", async function (e) {
    e.preventDefault();

    const username = document.getElementById("username").value.trim();
    const password = document.getElementById("password").value.trim();
    const role = document.getElementById("role").value;

    if (!role || role === "Pilih") {
      alert("Silakan pilih role terlebih dahulu (Mahasiswa atau Dosen).");
      return;
    }
    if (username === "" || password === "") {
      alert("Nama dan password wajib diisi!");
      return;
    }

    try {
      const formData = new FormData();
      formData.append("username", username);
      formData.append("password", password);
      formData.append("role", role);
      formData.append("login", "1");

      // ✅ dari auth/login.html ke backend/auth/login.php
      const res = await fetch("../backend/auth/login.php", {
        method: "POST",
        body: formData
      });

      const data = await res.json();

      if (!res.ok) {
        alert(data.message || "Login gagal");
        return;
      }

      // ✅ simpan untuk halaman HTML yang masih pakai localStorage
      localStorage.setItem("isLoggedIn", "true");
      localStorage.setItem("username", data.username);
      localStorage.setItem("userRole", data.role);

      // ✅ redirect sesuai role
      if (data.role === "dosen") {
        window.location.href = "../dashboard-dosen.php"; // sesuaikan kalau dashboard ada di folder lain
      } else {
        window.location.href = "../dashboard-mahasiswa.html"; // sesuaikan juga
      }

    } catch (err) {
      console.error(err);
      alert("Terjadi error. Cek Console (F12) dan Network.");
    }
  });
});