<?php
require_once 'db_connection.php';

$query = $_GET['query'];
$sql = "SELECT * FROM produk WHERE nama_produk LIKE '%$query%'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $statusClass = $row['status_produk'] == 'aktif' ? 'dot-green' : 'dot-red';
        echo "<tr>";
        echo "<td>{$row['id_produk']}</td>";
        echo "<td>{$row['nama_produk']}</td>";
        echo "<td>{$row['harga_produk']}</td>";
        echo "<td>{$row['jumlah_produk']}</td>";
        echo "<td>{$row['diskon_produk']}</td>";
        echo "<td><span class='status-dot $statusClass'></span> " . $row['status_produk'] . "</td>";
        echo '<td><a href="pemilikEditProduk.php?id=' . $row['id_produk'] . '">Edit</a></td>';
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='7'>Tidak ada produk ditemukan.</td></tr>";
}
?>
