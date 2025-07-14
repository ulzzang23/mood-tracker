<?php
require 'cek_role.php';
cekRole(['admin']);

$koneksi = new mysqli("localhost", "root", "", "db_mood");

$users = $koneksi->query("SELECT * FROM user ORDER BY role, username ASC");

if (isset($_GET['hapus'])) {
  $id = $_GET['hapus'];
  $koneksi->query("DELETE FROM user WHERE id = $id");
  header("Location: kelola_user.php?status=hapus");

  exit;
}
?>

<!DOCTYPE html>
<html lang="id" class="dark">
<head>
  <meta charset="UTF-8">
  <title>Kelola Pengguna</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    .zoom-in {
      animation: zoomIn 0.4s ease;
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
<body class="bg-gray-100 dark:bg-gray-900 text-gray-800 dark:text-gray-100 min-h-screen p-4">

  <!-- Toggle Dark Mode -->
  <div class="flex justify-end mb-4">
    <label class="flex items-center cursor-pointer">
      <input type="checkbox" id="darkToggle" class="sr-only">
      <div class="w-10 h-5 bg-gray-300 dark:bg-gray-700 rounded-full p-1 flex items-center transition duration-300 ease-in-out">
        <div class="w-4 h-4 bg-white dark:bg-gray-100 rounded-full shadow-md transform transition duration-300 ease-in-out"></div>
      </div>
      <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Dark Mode</span>
    </label>
  </div>

  <?php if (isset($_GET['status'])): ?>
  <div class="max-w-5xl mx-auto mb-4">
    <?php if ($_GET['status'] === 'tambah'): ?>
      <div class="bg-green-100 text-green-800 px-4 py-3 rounded shadow text-sm">
        ✅ Pengguna berhasil ditambahkan!
      </div>
    <?php elseif ($_GET['status'] === 'edit'): ?>
      <div class="bg-yellow-100 text-yellow-800 px-4 py-3 rounded shadow text-sm">
        ✏️ Pengguna berhasil diperbarui!
      </div>
    <?php elseif ($_GET['status'] === 'hapus'): ?>
      <div class="bg-red-100 text-red-800 px-4 py-3 rounded shadow text-sm">
        🗑️ Pengguna berhasil dihapus!
      </div>
    <?php endif; ?>
  </div>
<?php endif; ?>


  <div class="max-w-5xl mx-auto bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg zoom-in">
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold text-blue-700 dark:text-blue-400">Kelola Pengguna</h1>
      <a href="admin/tambah.php" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">+ Tambah Pengguna</a>
    </div>

    <div class="overflow-x-auto border rounded-lg dark:border-gray-700">
      <table class="min-w-full text-sm text-left">
        <thead class="bg-blue-100 dark:bg-gray-700 text-blue-700 dark:text-blue-200">
          <tr>
            <th class="py-3 px-4 border dark:border-gray-600">No</th>
            <th class="py-3 px-4 border dark:border-gray-600">Username</th>
            <th class="py-3 px-4 border dark:border-gray-600">Role</th>
            <th class="py-3 px-4 border text-center dark:border-gray-600">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php $no = 1; while ($row = $users->fetch_assoc()): ?>
          <tr class="hover:bg-gray-100 dark:hover:bg-gray-700">
            <td class="py-2 px-4 border dark:border-gray-700"><?= $no++ ?></td>
            <td class="py-2 px-4 border dark:border-gray-700"><?= htmlspecialchars($row['username']) ?></td>
            <td class="py-2 px-4 border dark:border-gray-700 capitalize"><?= $row['role'] ?></td>
            <td class="py-2 px-4 border text-center space-x-2 dark:border-gray-700">
              <a href="admin/edit.php?id=<?= $row['id'] ?>" class="text-yellow-600 hover:underline">Edit</a>
              <a href="kelola_user.php?hapus=<?= $row['id'] ?>" onclick="return confirm('Yakin hapus user ini?')" class="text-red-600 hover:underline">Hapus</a>
            </td>
          </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>

    <div class="mt-6 text-center">
      <a href="admin/index.php" class="text-sm text-blue-500 hover:underline">← Kembali ke Dashboard</a>
    </div>
  </div>

  <!-- Script Toggle Dark Mode -->
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
   <script>
  setTimeout(() => {
    const alertBox = document.querySelector('.max-w-5xl .bg-green-100, .bg-yellow-100, .bg-red-100');
    if (alertBox) alertBox.remove();
  }, 3000);
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
