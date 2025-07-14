<?php
require '../cek_role.php';
cekRole(['admin']);

$koneksi = new mysqli("localhost", "root", "", "db_mood");

// Jika form disubmit (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $id = $_POST['id'];
  $username = $_POST['username'];
  $password = $_POST['password'];
  $role = $_POST['role'];

  // Update data
  if (!empty($password)) {
    $password = password_hash($password, PASSWORD_DEFAULT);
    $koneksi->query("UPDATE user SET username='$username', password='$password', role='$role' WHERE id=$id");
  } else {
    $koneksi->query("UPDATE user SET username='$username', role='$role' WHERE id=$id");
  }

  header("Location: ../kelola_user.php?status=edit");
  exit;
}

// Ambil data user berdasarkan ID dari URL
if (!isset($_GET['id'])) {
  echo "ID tidak ditemukan.";
  exit;
}

$id = $_GET['id'];
$user = $koneksi->query("SELECT * FROM user WHERE id = $id")->fetch_assoc();

if (!$user) {
  echo "Pengguna tidak ditemukan.";
  exit;
}
?>

<!DOCTYPE html>
<html lang="id" class="dark">
<head>
  <meta charset="UTF-8">
  <title>Edit Pengguna</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 dark:bg-gray-900 text-gray-800 dark:text-gray-100 min-h-screen p-6">

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

  <div class="max-w-xl mx-auto bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md">
    <h1 class="text-2xl font-bold mb-4 text-blue-600 dark:text-blue-400">Edit Pengguna</h1>

    <form action="" method="POST" class="space-y-4">
      <input type="hidden" name="id" value="<?= $user['id'] ?>">

      <div>
        <label class="block font-semibold">Username</label>
        <input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>" required class="w-full px-3 py-2 border rounded dark:bg-gray-700">
      </div>

      <div>
        <label class="block font-semibold">Password (Kosongkan jika tidak diubah)</label>
        <input type="password" name="password" placeholder="••••••••" class="w-full px-3 py-2 border rounded dark:bg-gray-700">
      </div>

      <div>
        <label class="block font-semibold">Role</label>
        <select name="role" class="w-full px-3 py-2 border rounded dark:bg-gray-700" required>
          <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
          <option value="guru" <?= $user['role'] === 'guru' ? 'selected' : '' ?>>Guru</option>
          <option value="pembina" <?= $user['role'] === 'pembina' ? 'selected' : '' ?>>Pembina</option>
          <option value="siswa" <?= $user['role'] === 'siswa' ? 'selected' : '' ?>>Siswa</option>
        </select>
      </div>

      <div class="flex justify-between items-center">
        <a href="../kelola_user.php" class="text-sm text-blue-500 hover:underline">← Kembali</a>
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Simpan Perubahan</button>
      </div>
    </form>
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
