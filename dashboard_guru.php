<?php
require 'cek_role.php';
cekRole(['guru', 'pembina', 'admin']);

$koneksi = new mysqli("localhost", "root", "", "db_mood");
if ($koneksi->connect_error) {
    die("Koneksi gagal: " . $koneksi->connect_error);
}

$total_siswa = $koneksi->query("SELECT COUNT(*) as total FROM user WHERE role = 'siswa'")->fetch_assoc()['total'];
$mood_today = $koneksi->query("SELECT COUNT(*) as total FROM mood WHERE tanggal = CURDATE()")->fetch_assoc()['total'];

$where = [];
if (!empty($_GET['tanggal'])) {
    $tanggal = $koneksi->real_escape_string($_GET['tanggal']);
    $where[] = "m.tanggal = '$tanggal'";
}
if (!empty($_GET['nama'])) {
    $nama = $koneksi->real_escape_string($_GET['nama']);
    $where[] = "u.username LIKE '%$nama%'";
}
$where_sql = count($where) > 0 ? 'WHERE ' . implode(' AND ', $where) : '';

$order_sql = "ORDER BY m.tanggal DESC"; // default

if (!empty($_GET['sort'])) {
  switch ($_GET['sort']) {
    case 'nama_asc':
      $order_sql = "ORDER BY u.username ASC";
      break;
    case 'nama_desc':
      $order_sql = "ORDER BY u.username DESC";
      break;
    case 'tanggal_asc':
      $order_sql = "ORDER BY m.tanggal ASC";
      break;
    case 'tanggal_desc':
      $order_sql = "ORDER BY m.tanggal DESC";
      break;
    case 'mood_asc':
      $order_sql = "ORDER BY m.mood ASC";
      break;
    case 'mood_desc':
      $order_sql = "ORDER BY m.mood DESC";
      break;
  }
}


$query = "
  SELECT m.*, u.username 
  FROM mood m 
  JOIN user u ON m.id_user = u.id 
  $where_sql
  $order_sql
";

$result = $koneksi->query($query);

$mood_query = $koneksi->query("
  SELECT mood, COUNT(*) as jumlah 
  FROM mood 
  WHERE tanggal >= CURDATE() - INTERVAL 7 DAY 
  GROUP BY mood
");

$labels = [];
$data = [];
while ($row = $mood_query->fetch_assoc()) {
    $labels[] = ucfirst($row['mood']);
    $data[] = $row['jumlah'];
}
?>
<!DOCTYPE html>
<html lang="id" class="dark">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard Guru</title>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 dark:bg-gray-900 text-gray-800 dark:text-gray-200 min-h-screen p-4 sm:p-6">
  <!-- Toggle Dark Mode -->
  <div class="flex justify-end mb-4">
    <label class="flex items-center cursor-pointer">
      <input type="checkbox" id="darkToggle" class="sr-only">
      <div class="w-10 h-5 bg-gray-300 rounded-full p-1 flex items-center transition duration-300 ease-in-out">
        <div class="w-4 h-4 bg-white rounded-full shadow-md transform transition duration-300 ease-in-out"></div>
      </div>
      <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Dark Mode</span>
    </label>
  </div>

  <div class="max-w-6xl mx-auto bg-white dark:bg-gray-800 p-4 sm:p-6 rounded shadow-lg">
    <!-- Statistik -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
      <div class="bg-blue-500 text-white p-4 rounded text-center shadow-md hover:shadow-lg transition">

        <div class="text-sm">Jumlah Siswa</div>
        <div class="text-2xl font-bold"><?= $total_siswa ?></div>
      </div>
      <div class="bg-green-500 text-white p-4 rounded text-center shadow">
        <div class="text-sm">Mood Hari Ini</div>
        <div class="text-2xl font-bold"><?= $mood_today ?></div>
      </div>
    </div>

    <!-- Filter -->
    <form method="GET" class="mb-6 flex flex-col sm:flex-row flex-wrap gap-4 items-end">
      <div class="w-full sm:w-auto">
        <label class="block font-semibold">Tanggal</label>
        <input type="date" name="tanggal" value="<?= $_GET['tanggal'] ?? '' ?>" class="w-full border rounded px-3 py-2">
      </div>
      <div class="w-full sm:w-auto">
        <label class="block font-semibold">Nama Siswa</label>
        <input type="text" name="nama" placeholder="Cari nama..." value="<?= $_GET['nama'] ?? '' ?>" class="w-full border rounded px-3 py-2">
      </div>
      <div class="flex gap-2">
        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Filter</button>
        <a href="dashboard_guru.php" class="text-sm text-red-500 hover:underline self-center">Reset</a>
      </div>
      <div class="w-full sm:w-auto">
  <label class="block font-semibold">Urutkan Berdasarkan</label>
  <select name="sort" class="w-full border rounded px-3 py-2">
    <option value="">-- Default --</option>
    <option value="nama_asc" <?= ($_GET['sort'] ?? '') == 'nama_asc' ? 'selected' : '' ?>>Nama A-Z</option>
    <option value="nama_desc" <?= ($_GET['sort'] ?? '') == 'nama_desc' ? 'selected' : '' ?>>Nama Z-A</option>
    <option value="tanggal_asc" <?= ($_GET['sort'] ?? '') == 'tanggal_asc' ? 'selected' : '' ?>>Tanggal Terlama</option>
    <option value="tanggal_desc" <?= ($_GET['sort'] ?? '') == 'tanggal_desc' ? 'selected' : '' ?>>Tanggal Terbaru</option>
    <option value="mood_asc" <?= ($_GET['sort'] ?? '') == 'mood_asc' ? 'selected' : '' ?>>Mood A-Z</option>
    <option value="mood_desc" <?= ($_GET['sort'] ?? '') == 'mood_desc' ? 'selected' : '' ?>>Mood Z-A</option>
  </select>
</div>

    </form>

    <!-- Tombol -->
    <div class="mb-6 flex flex-col sm:flex-row flex-wrap gap-4">
      <a href="export_pdf.php?<?= http_build_query($_GET) ?>" target="_blank" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">
        Export PDF
      </a>
      <a href="export_excel.php?<?= http_build_query($_GET) ?>" target="_blank" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
        Export Excel
      </a>
      <a href="tambah_mood_manual.php" class="bg-yellow-500 text-white px-4 py-2 rounded hover:bg-yellow-600">
        + Tambah Mood Manual
      </a>
    </div>

    <!-- Judul & Chart -->
    <h2 class="text-2xl font-bold mb-4">Dashboard Guru / Pembina</h2>
    <h3 class="text-lg font-semibold mb-2">Grafik Mood 7 Hari Terakhir</h3>
    <div class="w-full overflow-x-auto">
      <canvas id="moodChart" class="w-full max-w-full h-auto mb-8"></canvas>
    </div>

    <!-- Tabel -->
    <h3 class="text-lg font-semibold mb-2">Data Mood Siswa</h3>
    <div class="overflow-x-auto max-h-[400px] border rounded">
      <table class="min-w-full bg-white dark:bg-gray-700 border dark:border-gray-600 text-sm">
        <thead class="bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-200 sticky top-0">
          <tr>
            <th class="py-2 px-4 border">Nama Siswa</th>
            <th class="py-2 px-4 border min-w-[120px]">Tanggal</th>
            <th class="py-2 px-4 border">Mood</th>
            <th class="py-2 px-4 border">Keterangan</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($row = $result->fetch_assoc()): ?>
            <tr class="hover:bg-gray-100 dark:hover:bg-gray-600">
              <td class="py-2 px-4 border"><?= $row['username'] ?></td>
              <td class="py-2 px-4 border"><?= $row['tanggal'] ?></td>
              <td class="py-2 px-4 border"><?= ucfirst($row['mood']) ?></td>
              <td class="py-2 px-4 border"><?= $row['keterangan'] ?></td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>

    <!-- Footer -->
    <div class="mt-6 flex flex-col sm:flex-row gap-4">
      <a href="logout.php" class="text-blue-600 hover:underline">Logout</a>
      <a href="ganti_password.php" class="text-blue-500 hover:underline text-sm">Ganti Password</a>
    </div>
  </div>

  <!-- Chart.js Script -->
  <script>
    const ctx = document.getElementById('moodChart').getContext('2d');
    const moodChart = new Chart(ctx, {
      type: 'bar',
      data: {
        labels: <?= json_encode($labels) ?>,
        datasets: [{
          label: 'Jumlah Mood',
          data: <?= json_encode($data) ?>,
          backgroundColor: [
            'rgba(75, 192, 192, 0.6)',
            'rgba(255, 206, 86, 0.6)',
            'rgba(255, 99, 132, 0.6)',
            'rgba(153, 102, 255, 0.6)'
          ],
          borderColor: [
            'rgba(75, 192, 192, 1)',
            'rgba(255, 206, 86, 1)',
            'rgba(255, 99, 132, 1)',
            'rgba(153, 102, 255, 1)'
          ],
          borderWidth: 1
        }]
      },
      options: {
        responsive: true,
        scales: {
          y: {
            beginAtZero: true,
            ticks: { precision: 0 }
          }
        }
      }
    });
  </script>

  <!-- Dark Mode Script -->
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
