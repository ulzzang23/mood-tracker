<?php
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=data_mood.xls");

$koneksi = new mysqli("localhost", "root", "", "db_mood");

// Ambil filter
$where = [];
if (isset($_GET['tanggal']) && $_GET['tanggal'] != '') {
    $tanggal = $koneksi->real_escape_string($_GET['tanggal']);
    $where[] = "m.tanggal = '$tanggal'";
}
if (isset($_GET['nama']) && $_GET['nama'] != '') {
    $nama = $koneksi->real_escape_string($_GET['nama']);
    $where[] = "u.username LIKE '%$nama%'";
}
$where_sql = count($where) > 0 ? 'WHERE ' . implode(' AND ', $where) : '';

$query = "
  SELECT m.*, u.username 
  FROM mood m 
  JOIN user u ON m.id_user = u.id 
  $where_sql
  ORDER BY m.tanggal DESC
";

$result = $koneksi->query($query);

// Tampilkan data sebagai HTML table (dibaca Excel)
echo "<table border='1'>";
echo "<tr><th>Nama Siswa</th><th>Tanggal</th><th>Mood</th><th>Keterangan</th></tr>";

while ($row = $result->fetch_assoc()) {
    echo "<tr>
            <td>{$row['username']}</td>
            <td>{$row['tanggal']}</td>
            <td>{$row['mood']}</td>
            <td>{$row['keterangan']}</td>
          </tr>";
}

echo "</table>";
?>
