<?php
session_start();
if (!isset($_SESSION['user'])) {
    echo "Akses ditolak!";
    exit;
}

$koneksi = new mysqli("localhost", "root", "", "db_mood");
if ($koneksi->connect_error) {
    die("Koneksi gagal: " . $koneksi->connect_error);
}

$pesan = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_SESSION['user']['id'];
    $old = $_POST['old_password'];
    $new = $_POST['new_password'];
    $confirm = $_POST['confirm_password'];

    // Ambil data password sekarang
    $cek = $koneksi->query("SELECT password FROM user WHERE id = $id");
    $data = $cek->fetch_assoc();

    if ($data['password'] !== $old) {
        $pesan = "❌ Password lama salah.";
    } elseif ($new !== $confirm) {
        $pesan = "❌ Konfirmasi password tidak cocok.";
    } else {
        $koneksi->query("UPDATE user SET password = '$new' WHERE id = $id");
        $pesan = "✅ Password berhasil diubah!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Ganti Password</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
  <div class="bg-white p-8 rounded shadow max-w-md w-full">
    <h2 class="text-xl font-bold mb-4">Ganti Password</h2>

    <?php if ($pesan): ?>
      <div class="mb-4 text-center font-semibold <?= strpos($pesan, '✅') !== false ? 'text-green-600' : 'text-red-600' ?>">
        <?= $pesan ?>
      </div>
    <?php endif; ?>

    <form method="POST" class="space-y-4">
      <div>
        <label class="block mb-1 font-medium">Password Lama</label>
        <input type="password" name="old_password" required class="w-full border px-3 py-2 rounded">
      </div>
      <div>
        <label class="block mb-1 font-medium">Password Baru</label>
        <input type="password" name="new_password" required class="w-full border px-3 py-2 rounded">
      </div>
      <div>
        <label class="block mb-1 font-medium">Konfirmasi Password Baru</label>
        <input type="password" name="confirm_password" required class="w-full border px-3 py-2 rounded">
      </div>
      <button type="submit" class="w-full bg-blue-500 text-white py-2 rounded hover:bg-blue-600">Simpan Perubahan</button>
    </form>

    <div class="text-center mt-4">
      <a href="<?= $_SESSION['user']['role'] == 'siswa' ? 'dashboard_siswa.php' : 'dashboard_guru.php' ?>" class="text-blue-600 hover:underline">Kembali ke Dashboard</a>
    </div>
  </div>
</body>
</html>
