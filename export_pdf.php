<?php
require 'dompdf/autoload.inc.php';
use Dompdf\Dompdf;

$koneksi = new mysqli("localhost", "root", "", "db_mood");

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

$html = '<h2>Data Mood Siswa</h2>';
$html .= '<table border="1" cellpadding="6" cellspacing="0" style="width:100%; font-size:12px;">
<tr><th>Nama</th><th>Tanggal</th><th>Mood</th><th>Keterangan</th></tr>';

while ($row = $result->fetch_assoc()) {
    $html .= "<tr>
        <td>{$row['username']}</td>
        <td>{$row['tanggal']}</td>
        <td>{$row['mood']}</td>
        <td>{$row['keterangan']}</td>
    </tr>";
}

$html .= '</table>';

$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream("data_mood.pdf");
