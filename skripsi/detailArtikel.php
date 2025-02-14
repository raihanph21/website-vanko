<?php
require 'db_connection.php';
session_start();
$id_artikel = $_GET['id'];

$sql = "SELECT judul_artikel, gambar_artikel, konten_artikel, tanggal_publikasi FROM artikel WHERE id_artikel = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_artikel);
$stmt->execute();
$stmt->bind_result($judul_artikel, $gambar_artikel, $konten_artikel, $tanggal_publikasi);
$stmt->fetch();
$stmt->close();
?>

<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Vanko Petshop | Detail Artikel</title>
    <!-- Link css -->
    <link rel="stylesheet" href="../skripsi/css/styleDetailArtikel.css">
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
    <!-- selesai navbar -->
    <div class="containerA text-center">
        <h1 class="text-center"><?php echo $judul_artikel; ?></h1>
        <p class="text-center"><?php echo $tanggal_publikasi; ?></p>
        <img src="data:image/jpeg;base64,<?php echo base64_encode($gambar_artikel); ?>" alt="<?php echo $judul_artikel; ?>" style="width:400px;height:300px;" />
        <div id="konten"><?php echo nl2br($konten_artikel); ?></div>
        <a href="artikel.php" class="btn btn-primary">Kembali</a>
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
    <!-- selesai footer -->

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</body>

</html>
<?php
$conn->close();
?>