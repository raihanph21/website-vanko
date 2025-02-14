<?php
session_start();

if (isset($_SESSION['transaction_status']) && $_SESSION['transaction_status'] == 'unfinished') {
    $total_penjualan = $_SESSION['total_penjualan']; 
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
    <title>Transaksi Belum Selesai</title>
</head>
<body>
    <h1>Transaksi Belum Selesai</h1>
    <p>Anda belum menyelesaikan pembayaran sebesar Rp<?php echo number_format($total_penjualan, 0, ',', '.'); ?>.</p>
    <a href="keranjang.php">Kembali ke Keranjang</a> atau
    <a href="checkout.php">Coba Lagi</a>
</body>
</html>
