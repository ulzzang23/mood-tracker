<?php
session_start();
if (!isset($_SESSION['user']) || ($_SESSION['user']['role'] != 'pembina' && $_SESSION['user']['role'] != 'guru')) {
    echo "Akses ditolak!";
    exit;
}

$koneksi = new mysqli("localhost", "root", "", "db_mood");
if ($koneksi->connect_error) {
    die("Koneksi gagal: " . $koneksi->connect_error);
}

// Ambil data siswa untuk dropdown
$siswa_result = $koneksi->query("SELECT * FROM user WHERE role = 'siswa'");

$pesan = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_user = $_POST['id_user'];
    $tanggal = $_POST['tanggal'];
    $mood = $_POST['mood'];
    $keterangan = $_POST['keterangan'];

    // Cek apakah mood untuk tanggal itu sudah ada
    $cek = $koneksi->query("SELECT * FROM mood WHERE id_user = $id_user AND tanggal = '$tanggal'");
    if ($cek->num_rows > 0) {
        $pesan = "❗ Mood untuk tanggal ini sudah ada.";
    } else {
        $koneksi->query("INSERT INTO mood (id_user, tanggal, mood, keterangan) VALUES ($id_user, '$tanggal', '$mood', '$keterangan')");
        $pesan = "✅ Mood berhasil ditambahkan.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Tambah Mood Manual</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
  <div class="bg-white p-8 rounded shadow max-w-md w-full">
    <h2 class="text-xl font-bold mb-4">Tambah Data Mood Siswa</h2>

    <?php if ($pesan): ?>
      <div class="mb-4 text-center font-semibold <?= strpos($pesan, '✅') !== false ? 'text-green-600' : 'text-red-600' ?>">
        <?= $pesan ?>
      </div>
    <?php endif; ?>

    <form method="POST" class="space-y-4">
      <div>
        <label class="block font-medium mb-1">Nama Siswa</label>
        <select name="id_user" required class="w-full border px-3 py-2 rounded">
          <option value="">-- Pilih Siswa --</option>
          <?php while ($siswa = $siswa_result->fetch_assoc()): ?>
            <option value="<?= $siswa['id'] ?>"><?= $siswa['username'] ?></option>
          <?php endwhile; ?>
        </select>
      </div>

      <div>
        <label class="block font-medium mb-1">Tanggal</label>
        <input type="date" name="tanggal" required class="w-full border px-3 py-2 rounded">
      </div>

      <div>
        <label class="block font-medium mb-1">Mood</label>
        <select name="mood" required class="w-full border px-3 py-2 rounded">
          <option value="">-- Pilih Mood --</option>
          <option value="senang">😊 Senang</option>
          <option value="sedih">😢 Sedih</option>
          <option value="marah">😡 Marah</option>
          <option value="biasa aja">😐 Biasa Aja</option>
        </select>
      </div>

      <div>
        <label class="block font-medium mb-1">Keterangan</label>
        <textarea name="keterangan" rows="3" class="w-full border px-3 py-2 rounded" placeholder="Opsional..."></textarea>
      </div>

      <button type="submit" class="w-full bg-blue-500 text-white py-2 rounded hover:bg-blue-600">Simpan Mood</button>
    </form>

    <div class="text-center mt-4">
      <a href="dashboard_guru.php" class="text-blue-600 hover:underline">← Kembali ke Dashboard</a>
    </div>
  </div>
</body>
</html>
