<?php
require 'cek_role.php';
cekRole(['siswa', 'admin']);

$koneksi = new mysqli("localhost", "root", "", "db_mood");
if ($koneksi->connect_error) {
    die("Koneksi gagal: " . $koneksi->connect_error);
}

$pesan = "";
$id_user = $_SESSION['user']['id'];
$tanggal = date('Y-m-d');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $mood = $_POST['mood'];
    $keterangan = $_POST['keterangan'];

    $cek = $koneksi->query("SELECT * FROM mood WHERE id_user = $id_user AND tanggal = '$tanggal'");
    if ($cek->num_rows > 0) {
        $pesan = "❗ Kamu sudah mengisi mood hari ini.";
    } else {
        $koneksi->query("INSERT INTO mood (id_user, tanggal, mood, keterangan) VALUES ($id_user, '$tanggal', '$mood', '$keterangan')");
        $pesan = "✅ Mood berhasil disimpan!";
    }
}
// Data untuk grafik
$mood_chart = $koneksi->query("
  SELECT tanggal, mood FROM mood 
  WHERE id_user = $id_user AND tanggal >= CURDATE() - INTERVAL 6 DAY
  ORDER BY tanggal ASC
");

$labels = [];
$data_mood = [];

$mood_hari = [];

while ($row = $mood_chart->fetch_assoc()) {
    $tgl = $row['tanggal'];
    $mood = strtolower($row['mood']);

    if (!isset($mood_hari[$tgl])) {
        $mood_hari[$tgl] = [
            'senang' => 0,
            'sedih' => 0,
            'marah' => 0,
            'biasa aja' => 0,
        ];
    }
    $mood_hari[$tgl][$mood]++;
}

// Konversi ke array JS
foreach ($mood_hari as $tanggal => $moods) {
    $labels[] = $tanggal;
    foreach (['senang', 'sedih', 'marah', 'biasa aja'] as $m) {
        $data_mood[$m][] = $moods[$m];
    }
}


$riwayat = $koneksi->query("SELECT * FROM mood WHERE id_user = $id_user ORDER BY tanggal DESC");
?>
<?php
// Ambil data mood siswa yang login
$mood_pie = $koneksi->query("
  SELECT mood, COUNT(*) as total 
  FROM mood 
  WHERE id_user = $id_user 
  GROUP BY mood
");

$label_pie = [];
$data_pie = [];
while ($row = $mood_pie->fetch_assoc()) {
    $label_pie[] = ucfirst($row['mood']);
    $data_pie[] = $row['total'];
}

?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Dashboard Siswa</title>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    .fade-in {
      animation: fadeIn 0.6s ease-in-out;
    }
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(10px); }
      to { opacity: 1; transform: translateY(0); }
    }
  </style>
</head>
<body class="bg-blue-50 min-h-screen text-gray-800">

  <div class="w-full max-w-4xl mx-auto p-4 sm:p-6 mt-8 bg-white rounded-lg shadow-lg fade-in">

    <!-- Header -->
    <div class="text-center mb-6">
      <h2 class="text-2xl sm:text-3xl font-bold text-blue-700">Halo, <?= $_SESSION['user']['username'] ?> 👋</h2>
      <p class="text-gray-600 mt-1 text-sm sm:text-base">Selamat datang di Dashboard Mood Kamu</p>
    </div>

    <!-- Alert -->
    <?php if ($pesan): ?>
      <div class="mb-4 text-center font-medium text-sm sm:text-base <?= strpos($pesan, '✅') !== false ? 'text-green-600' : 'text-red-600' ?>">
        <?= $pesan ?>
      </div>
    <?php endif; ?>

    <!-- Form Input Mood -->
    <form method="POST" class="grid gap-4 mb-6">
      <div>
        <label class="block font-medium text-gray-700">Mood Hari Ini</label>
        <select name="mood" required class="w-full border border-gray-300 rounded px-3 py-2 mt-1 focus:outline-none focus:ring-2 focus:ring-blue-400">
          <option value="">-- Pilih Mood --</option>
          <option value="senang">😊 Senang</option>
          <option value="sedih">😢 Sedih</option>
          <option value="marah">😡 Marah</option>
          <option value="biasa aja">😐 Biasa Aja</option>
        </select>
      </div>
      <div>
        <label class="block font-medium text-gray-700">Keterangan</label>
        <textarea name="keterangan" rows="3" class="w-full border border-gray-300 rounded px-3 py-2 mt-1 focus:outline-none focus:ring-2 focus:ring-blue-400" placeholder="Opsional..."></textarea>
      </div>
      <button type="submit" class="bg-blue-600 text-white py-2 rounded hover:bg-blue-700 transition w-full">Kirim Mood</button>
    </form>

    <!-- Grafik Pie -->
    <h3 class="text-lg sm:text-xl font-semibold mt-6 mb-2 text-blue-700 text-center sm:text-left">Statistik Mood Kamu</h3>
    <div class="w-full max-w-xs sm:max-w-md mx-auto sm:mx-0">
      <canvas id="moodPieChart" class="mb-6"></canvas>
    </div>

    <!-- Riwayat Table -->
    <h3 class="text-lg sm:text-xl font-semibold text-blue-700 mb-3">Riwayat Mood Kamu</h3>
    <div class="overflow-x-auto max-h-[300px] border rounded text-sm">
      <table class="min-w-full">
        <thead class="bg-blue-100 text-blue-800 sticky top-0">
          <tr>
            <th class="px-4 py-2 text-left">Tanggal</th>
            <th class="px-4 py-2 text-left">Mood</th>
            <th class="px-4 py-2 text-left">Keterangan</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($row = $riwayat->fetch_assoc()): ?>
          <tr class="hover:bg-gray-100">
            <td class="px-4 py-2"><?= $row['tanggal'] ?></td>
            <td class="px-4 py-2"><?= ucfirst($row['mood']) ?></td>
            <td class="px-4 py-2"><?= $row['keterangan'] ?></td>
          </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>

    <!-- Footer -->
    <div class="text-center mt-6 space-x-4 text-sm">
      <a href="logout.php" class="text-blue-600 hover:underline">Logout</a>
      <a href="ganti_password.php" class="text-blue-500 hover:underline">Ganti Password</a>
    </div>
  </div>

  <script>
    const ctxPie = document.getElementById('moodPieChart').getContext('2d');
    new Chart(ctxPie, {
      type: 'pie',
      data: {
        labels: <?= json_encode($label_pie) ?>,
        datasets: [{
          data: <?= json_encode($data_pie) ?>,
          backgroundColor: ['#3B82F6', '#EF4444', '#F59E0B', '#6B7280'],
          borderWidth: 1
        }]
      },
      options: {
        responsive: true,
        plugins: {
          legend: { position: 'bottom' }
        }
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





