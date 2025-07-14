<?php
require '../cek_role.php';
cekRole(['admin']);
header("Location: ../kelola_user.php?status=tambah");
exit;

?>

<!DOCTYPE html>
<html lang="id" class="dark">
<head>
  <meta charset="UTF-8">
  <title>Tambah Pengguna</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    .zoom-in {
      animation: zoomIn 0.3s ease-out;
    }
    @keyframes zoomIn {
      0% {
        transform: scale(0.95);
        opacity: 0;
      }
      100% {
        transform: scale(1);
        opacity: 1;
      }
    }
  </style>
</head>
<body class="bg-gray-100 dark:bg-gray-900 text-gray-800 dark:text-gray-100 min-h-screen p-6">

  <!-- Toggle Dark Mode -->
  <div class="flex justify-end mb-4">
    <label class="flex items-center cursor-pointer">
      <input type="checkbox" id="darkToggle" class="sr-only">
      <div class="w-10 h-5 bg-gray-300 dark:bg-gray-700 rounded-full p-1 flex items-center transition">
        <div class="w-4 h-4 bg-white dark:bg-gray-100 rounded-full shadow-md transform transition"></div>
      </div>
      <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Dark Mode</span>
    </label>
  </div>

  <div class="max-w-lg mx-auto bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg zoom-in">
    <h2 class="text-2xl font-bold mb-6 text-blue-600 dark:text-blue-400">Tambah Pengguna</h2>

    <form method="POST" action="proses_tambah.php" class="space-y-4">
      <div>
        <label for="username" class="block font-semibold mb-1">Username</label>
        <input type="text" name="username" id="username" required class="w-full px-3 py-2 border rounded bg-gray-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:outline-none focus:ring focus:border-blue-500">
      </div>
      <div>
        <label for="password" class="block font-semibold mb-1">Password</label>
        <input type="password" name="password" id="password" required class="w-full px-3 py-2 border rounded bg-gray-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:outline-none focus:ring focus:border-blue-500">
      </div>
      <div>
        <label for="role" class="block font-semibold mb-1">Role</label>
        <select name="role" id="role" required class="w-full px-3 py-2 border rounded bg-gray-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
          <option value="">-- Pilih Role --</option>
          <option value="admin">Admin</option>
          <option value="guru">Guru</option>
          <option value="pembina">Pembina</option>
          <option value="siswa">Siswa</option>
        </select>
      </div>
      <div class="flex justify-between items-center mt-6">
        <a href="../kelola_user.php" class="text-sm text-blue-500 hover:underline">← Kembali</a>
        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded transition">Simpan</button>
      </div>
    </form>
  </div>

  <!-- Script Dark Mode -->
  <script>
    if (localStorage.getItem('darkMode') === 'true') {
      document.documentElement.classList.add('dark');
      document.getElementById('darkToggle').checked = true;
    }

    document.getElementById('darkToggle').addEventListener('change', function () {
      if (this.checked) {
        document.documentElement.classList.add('dark');
        localStorage.setItem('darkMode', 'true');
      } else {
        document.documentElement.classList.remove('dark');
        localStorage.setItem('darkMode', 'false');
      }
    });
  </script>
 <audio id="bg-audio" autoplay loop>

  <source src="assets/audio/backsound.mp3" type="audio/mpeg">
  Browser tidak mendukung audio.
</audio>
<!-- Tombol Play Musik -->
<button onclick="document.getElementById('bg-audio').play()" class="fixed top-4 right-4 bg-blue-600 text-white px-3 py-1 rounded shadow hover:bg-blue-700 transition text-sm z-50">
  🎵 Play Musik
</button>
<script>
  const audio = document.getElementById("bg-audio");
  audio.volume = 0.1;

  // Simpan waktu sebelumnya
  const savedTime = localStorage.getItem("audioTime");
  const savedState = localStorage.getItem("audioPlaying");

  if (savedTime !== null) {
    audio.currentTime = parseFloat(savedTime);
  }

  document.addEventListener("click", function () {
    if (savedState === "true") {
      audio.play().catch(() => {});
    }
  }, { once: true });

  // Simpan progress
  setInterval(() => {
    localStorage.setItem("audioTime", audio.currentTime);
    localStorage.setItem("audioPlaying", !audio.paused);
  }, 1000);
</script>
</body>
</html>
