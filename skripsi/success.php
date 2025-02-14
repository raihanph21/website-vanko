<?php
session_start();

if (isset($_SESSION['transaction_status']) && $_SESSION['transaction_status'] == 'success') {
    $total_penjualan = $_SESSION['total_penjualan'];
    $id_penjualan = $_SESSION['id_penjualan']; 
    $nama_user = $_SESSION['nama_user'];
} else {
    header('Location: index.php'); 
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaksi Berhasil</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Transaksi Berhasil!</h1>
    <p>Terima kasih, <?php echo $nama_user; ?>, pembayaran Anda telah berhasil.</p>
    <p>ID Pesanan: <?php echo $id_penjualan; ?></p>
    <p>Total Pembayaran: Rp<?php echo number_format($total_penjualan, 0, ',', '.'); ?></p>
    <a href="index.php">Kembali ke Beranda</a>
</body>
</html>
