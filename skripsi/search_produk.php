<?php
require 'db_connection.php'; 

if (isset($_POST['query'])) {
    $search = $_POST['query'];
    $sql = "SELECT * FROM produk WHERE nama_produk LIKE '%$search%' AND status_produk = 'aktif'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<a class='dropdown-item' href='detailProduk.php?id=" . $row['id_produk'] . "'>";
            echo htmlspecialchars($row['nama_produk']);
            echo "</a>";
        }
    } else {
        echo "<p class='dropdown-item'>Produk tidak ditemukan</p>";
    }
}
?>
