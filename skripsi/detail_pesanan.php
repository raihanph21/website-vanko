<?php
session_start();
require 'db_connection.php'; 

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: riwayat_pesanan.php");
    exit();
}

$id_penjualan = $_GET['id'];

$query = "SELECT dp.id_produk, p.nama_produk, dp.jumlah_produk, dp.harga_jual_produk
          FROM detail_penjualan dp
          JOIN produk p ON dp.id_produk = p.id_produk
          WHERE dp.id_penjualan = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id_penjualan);
$stmt->execute();
$result = $stmt->get_result();

$queryPenjualan = "SELECT total_penjualan, status FROM penjualan WHERE id_penjualan = ?";
$stmtPenjualan = $conn->prepare($queryPenjualan);
$stmtPenjualan->bind_param("i", $id_penjualan);
$stmtPenjualan->execute();
$resultPenjualan = $stmtPenjualan->get_result();
$penjualan = $resultPenjualan->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Vanko Petshop | Detail Pesanan</title>
  <!-- Link css -->
  <link rel="stylesheet" href="../skripsi/css/styleDetailPesanan.css">
  <!-- Link Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <!-- Link Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <!-- Link font montserrat -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat&display=swap" rel="stylesheet">
</head>

<body>
<nav class="navbar navbar-expand fixed-top shadow-lg fw-bold">
  <div class="container">
    <a class="navbar-brand" href="#">Vanko Petshop</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse justify-content-end" id="navbarNavAltMarkup">
      <div class="navbar-nav">
        <a class="nav-link" aria-current="page" href="index.php">Beranda</a>
        <a class="nav-link" href="produk.php">Produk</a>
        <a class="nav-link" href="artikel.php">Artikel</a>
        <?php if (isset($_SESSION['nama'])) : ?>
          <div class="nav-item dropdown user-dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button">
              <?php echo htmlspecialchars($_SESSION['nama']); ?>
            </a>
            <ul class="dropdown-menu" aria-labelledby="userDropdown">
              <li><a class="dropdown-item" href="riwayat_pesanan.php">Riwayat Pesanan</a></li>
              <li><hr class="dropdown-divider"></li>
              <li><a class="dropdown-item" href="logout.php">Logout</a></li>
            </ul>
          </div>
        <?php else : ?>
          <a class="nav-link" href="login.php">Login</a>
        <?php endif; ?>
      </div>
    </div>
  </div>
</nav>
    <div class="containerC">
        <h1 class="mt-5 fw-bold">Detail Pesanan</h1>

        <div class="card">
            <div class="card-body">
                <h5 class="card-title">ID Pesanan: <?php echo $id_penjualan; ?></h5>
                <p class="card-text">Total Pembayaran: Rp<?php echo number_format($penjualan['total_penjualan'], 0, ',', '.'); ?></p>
                <p class="card-text">Status Pesanan: <?php echo htmlspecialchars($penjualan['status']); ?></p>
            </div>
        </div>

        <h3 class="mt-4">Produk yang Dipesan:</h3>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th class="table-primary">Nama Produk</th>
                    <th class="table-primary">Jumlah</th>
                    <th id="tHargaP" class="table-primary">Harga</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['nama_produk']); ?></td>
                        <td><?php echo $row['jumlah_produk']; ?></td>
                        <td id="hargaP" >Rp<?php echo number_format($row['harga_jual_produk'], 0, ',', '.'); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    <footer>
    <h3 class="fw-bold">Vanko Petshop</h3>
    <h5 class="fw-bold">Tentang kami</h5>
    <p>Lorem ipsum dolor sit, amet consectetur adipisicing elit.</p>
    <h6>Jl. Jae Sumantoro Godean (Pasar Godean)</h6>
    <div class="sosmed">
      <a href="#" class="fa fa-facebook"></a>
      <a href="#" class="fa fa-instagram"></a>
      <a href="#" class="fa fa-whatsapp"></a>
    </div>
  </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
