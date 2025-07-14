<?php
session_start();
$koneksi = new mysqli("localhost", "root", "", "db_mood");

$pesan = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $username = $_POST['username'];
  $password = $_POST['password'];

  $cek = $koneksi->query("SELECT * FROM user WHERE username = '$username'");
  if ($cek->num_rows > 0) {
    $user = $cek->fetch_assoc();
    $login = $password == $user['password'];

    if ($login) {
      $_SESSION['user'] = $user;
      if ($user['role'] == 'admin') {
        header("Location: admin/index.php");
      } elseif ($user['role'] == 'guru' || $user['role'] == 'pembina') {
        header("Location: dashboard_guru.php");
      } elseif ($user['role'] == 'siswa') {
        header("Location: dashboard_siswa.php");
      }
      exit;
    } else {
      $pesan = "❗ Password salah!";
    }
  } else {
    $pesan = "❗ Username tidak ditemukan!";
  }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login - Mood Tracker</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <!-- Animate.css -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
</head>
<body class="bg-gradient-to-br from-blue-100 to-blue-300 min-h-screen flex items-center justify-center">

 <div class="w-full max-w-md bg-white p-6 rounded-lg shadow-lg animate__animated animate__zoomIn">


    <h2 class="text-2xl font-bold text-center text-blue-700 mb-4">Login Mood Tracker</h2>

    <?php if ($pesan): ?>
      <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-2 rounded mb-4">
        <?= $pesan ?>
      </div>
    <?php endif; ?>

    <form method="POST" class="space-y-4">
      <div>
        <label class="block text-gray-700 font-medium">Username</label>
        <input type="text" name="username" required class="w-full border rounded px-3 py-2 focus:outline-none focus:ring focus:border-blue-400" placeholder="Masukkan username">
      </div>
      <div>
        <label class="block text-gray-700 font-medium">Password</label>
        <input type="password" name="password" id="password" required class="w-full border rounded px-3 py-2 focus:outline-none focus:ring focus:border-blue-400" placeholder="Masukkan password">

        <!-- Show password -->
        <div class="flex items-center mt-1">
          <input type="checkbox" id="showPassword" class="mr-2">
          <label for="showPassword" class="text-sm text-gray-600">Tampilkan Password</label>
        </div>
      </div>
      <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700 transition">Login</button>
    </form>

    <!-- Lupa password -->
    <div class="text-center mt-2">
      <a href="lupa_password.php" class="text-sm text-blue-600 hover:underline">Lupa Password?</a>
    </div>

    <p class="text-center text-sm text-gray-500 mt-4">
      © <?= date('Y') ?> Mood Tracker • All Rights Reserved
    </p>
  </div>

  <script>
    const showPass = document.getElementById('showPassword');
    const passInput = document.getElementById('password');
    showPass.addEventListener('change', () => {
      passInput.type = showPass.checked ? 'text' : 'password';
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
