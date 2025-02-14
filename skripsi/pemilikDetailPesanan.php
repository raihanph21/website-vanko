<?php
session_start();

require_once 'db_connection.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'pemilik') {
    header('Location: login.php');
    exit();
}

if (!isset($_GET['id_penjualan'])) {
    die('ID Penjualan tidak ditemukan.');
}
$id_penjualan = $_GET['id_penjualan'];

$query = $conn->prepare('
    SELECT p.nama_produk, dp.jumlah_produk, dp.harga_jual_produk, dp.total_penjualan, dp.status, pen.alamat_pengiriman
    FROM detail_penjualan dp
    JOIN produk p ON dp.id_produk = p.id_produk
    JOIN penjualan pen ON dp.id_penjualan = pen.id_penjualan
    WHERE dp.id_penjualan = ?
');
$query->bind_param('i', $id_penjualan);
$query->execute();
$result = $query->get_result();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pemilik Page | Detail Pesanan</title>
    <!-- link css -->
    <link rel="stylesheet" type="text/css" href="../skripsi/css/stylePemilikDetailPesanan.css">

    <!-- link font awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- link bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">

    <!-- Link font montserrat -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat&display=swap" rel="stylesheet">
</head>

<body>
    <div class="row">
        <div class="col-3" id="sticky-sidebar">
            <div class="sticky-top">
                <div class="nav flex-column">
                    <img src="../skripsi/assests/img/150x150.png" class="rounded-circle"></img>
                    <li href="#_" class="text-center"><?= htmlspecialchars($_SESSION['nama']); ?></li>
                    <li href="#_" class="nav-link text-center"><a href="pemilikPage.php">Stok</a></li>
                    <li href="#_" class="nav-link text-center"><a href="pemilikLaporan.php">Laporan Keuangan</a></li>
                    <li href="#_" class="nav-link text-center"><a href="pemilikPesanan.php">Pesanan</a></li>
                    <li href="#_" class="nav-link text-center"><a href="logout.php">Logout</a></li>
                </div>
            </div>
        </div>
        <div class="pesanan col-9">
            <h1 class='fw-bold'>Detail Pesanan</h1>

            <div class="invoice-box">
                <div class="invoice-header">
                    <p>ID Penjualan: <?= htmlspecialchars($id_penjualan); ?></p>
                </div>

                <div class="customer-info">
                    <?php
                    if ($result->num_rows > 0) {
                        $row = $result->fetch_assoc();
                        echo '<p><strong>Alamat Pengiriman:</strong> ' . htmlspecialchars($row['alamat_pengiriman']) . '</p>';
                    } else {
                        echo '<p>Detail pesanan tidak ditemukan.</p>';
                    }
                    ?>
                </div>

                <div class="product-list">
                    <h3>Detail Produk</h3>
                    <hr>
                    <?php
                    $result->data_seek(0);
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo '<div class="product-item">';
                            echo '<p><strong>Nama Produk:</strong> ' . htmlspecialchars($row['nama_produk']) . '</p>';
                            echo '<p><strong>Jumlah:</strong> ' . htmlspecialchars($row['jumlah_produk']) . '</p>';
                            echo "<p><strong>Harga Jual:</strong> Rp" . number_format($row['harga_jual_produk'], 0, ',', '.') . "</p>";
                            echo "<p><strong>Total:</strong> Rp" . number_format($row['total_penjualan'], 0, ',', '.') . "</p>";
                            echo '<p><strong>Status Pembayaran:</strong> ' . htmlspecialchars($row['status']) . '</p>';
                            echo '<hr>';
                            echo '</div>';
                        }
                    } else {
                        echo '<p>Detail produk tidak ditemukan.</p>';
                    }
                    ?>
                </div>
            </div>

        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</body>

</html>
