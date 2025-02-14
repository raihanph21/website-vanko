<?php
require 'db_connection.php';
require_once('../config.php');
require_once('../core/controller.Class.php');

session_start();

$artikelQuery = "SELECT id_artikel, judul_artikel, gambar_artikel, tanggal_publikasi FROM artikel";
$artikelResult = $conn->query($artikelQuery);



$sql = "SELECT *, 
        (harga_produk - (harga_produk * (diskon_produk / 100))) AS harga_setelah_diskon 
        FROM produk 
        WHERE status_produk = 'aktif' 
        ORDER BY harga_setelah_diskon ASC 
        LIMIT 7";
$result = $conn->query($sql);
?>


<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Vanko Petshop</title>
  <!-- Link css -->
  <link rel="stylesheet" href="../skripsi/css/style.css">
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
  <!-- navbar -->
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

  <!-- Jumbotron -->
  <div class="containerJ text-center">
    <div class="jumbotron">
      <h1 class="fw-bold">Vanko Petshop</h1>
      <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Quod quos dolore eveniet voluptatum illum dolorum dolorem? Quidem nisi alias maiores vitae non vel doloribus facere.</p>
    </div>
  </div>

 <!-- produk -->
<h3 class="h1produk fw-bold">Produk Termurah</h3>
<div class="produk-container text-center">
  <?php if ($result->num_rows > 0) : ?>
    <?php while ($row = $result->fetch_assoc()) : ?>
      <div class="produk-thumbnail">
        <a href="detailProduk.php?id=<?php echo $row['id_produk']; ?>">
          <img src="data:image/jpg;base64,<?php echo base64_encode($row['gambar_produk']); ?>" alt="<?php echo htmlspecialchars($row['nama_produk']); ?>" />
          <p class="namaProduk"><?php echo htmlspecialchars($row['nama_produk']); ?></p>
          
          <?php if ($row['diskon_produk'] > 0): ?>
            <p style="text-decoration: line-through; color: red;">Rp<?php echo number_format($row['harga_produk'], 0, ',', '.'); ?></p>
            
            <?php
              $harga_asli = $row['harga_produk'];
              $diskon = $row['diskon_produk']; 
              $harga_diskon = $harga_asli - ($harga_asli * ($diskon / 100));
            ?>
            <p style="font-weight: bold; color: green;">Rp<?php echo number_format($harga_diskon, 0, ',', '.'); ?></p>
          <?php else: ?>
            <p>Harga: Rp<?php echo number_format($row['harga_produk'], 0, ',', '.'); ?></p>
          <?php endif; ?>
        </a>
      </div>
    <?php endwhile; ?>
  <?php else : ?>
    <p>Tidak ada produk.</p>
  <?php endif; ?>
</div>


  <!-- artikel -->
  <h3 class="h1artikel fw-bold">Artikel Terbaru</h3>
  <div class="artikel-container text-center">
    <?php if ($artikelResult->num_rows > 0) : ?>
      <?php while ($row = $artikelResult->fetch_assoc()) : ?>
        <div class="artikel-thumbnail">
          <a href="detailArtikel.php?id=<?php echo $row['id_artikel']; ?>">
            <img src="data:image/jpeg;base64,<?php echo base64_encode($row['gambar_artikel']); ?>" alt="<?php echo htmlspecialchars($row['judul_artikel']); ?>" style="width:200px;height:150px;" />
            <p><?php echo htmlspecialchars($row['judul_artikel']); ?></p>
            <p class="tanggal-artikel"> <?php echo date("d M Y", strtotime($row["tanggal_publikasi"])) ?></p>
          </a>
        </div>
      <?php endwhile; ?>
    <?php else : ?>
      <p>Tidak ada artikel.</p>
    <?php endif; ?>
  </div>

  <!-- footer -->
  <footer>
    <h3 class="fw-bold">Vanko Petshop</h3>
    <h5 class="fw-bold">Tentang kami</h5>
    <p>Lorem ipsum dolor sit, amet consectetur adipisicing elit. Hic ipsum cumque corporis quas tenetur saepe. Assumenda fugit sequi officiis ipsam alias repellendus animi tempore accusantium. </p>
    <h6>Jl. Jae Sumantoro Godean (Pasar Godean)</h6>
    <div class="sosmed">
      <a href="#" class="fa fa-facebook"></a>
      <a href="#" class="fa fa-instagram"></a>
      <a href="#" class="fa fa-whatsapp"></a>
    </div>
  </footer>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</body>

</html>