<?php
session_start();

if (isset($_SESSION['transaction_status']) && $_SESSION['transaction_status'] == 'error') {
    $error_message = $_SESSION['error_message']; 
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
    <title>Transaksi Gagal</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Transaksi Gagal</h1>
    <p>Maaf, transaksi Anda tidak dapat diselesaikan.</p>
    <p>Pesan Error: <?php echo $error_message; ?></p>
    <a href="index.php">Kembali ke Beranda</a>
    <a href="checkout.php">Coba Lagi</a>
</body>
</html>
