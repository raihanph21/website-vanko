<?php
session_start();
require 'db_connection.php'; 

if (isset($_GET['id'])) {
    $id_produk = $_GET['id'];

    $sql = "SELECT * FROM produk WHERE id_produk = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_produk);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
    } else {
        echo "Produk tidak ditemukan.";
        exit();
    }
} else {
    echo "ID produk tidak ditemukan.";
    exit();
}

$harga_asli = $row['harga_produk'];
$diskon = $row['diskon_produk']; 
$harga_setelah_diskon = $harga_asli - ($harga_asli * ($diskon / 100));

$deskripsi_produk = $row['deskripsi_produk'];
$deskripsi_items = explode('|', $deskripsi_produk);
?>

<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Vanko Petshop | Detail Produk</title>
    <!-- Link css -->
    <link rel="stylesheet" href="../skripsi/css/styleDetailProduk.css">
    <!-- Link Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <!-- Link Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <!-- Link font montserrat -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat&display=swap" rel="stylesheet">
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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

    <!-- search bar -->
    <div class="container">
        <nav id="search" class="navbar navbar-light bg-light">
            <form id="search-form" class="d-flex">
                <input id="search-input" class="form-control me-2 col" type="search" placeholder="Search" aria-label="Search">
                <button class="btn btn-outline-primary col-2" type="submit" style="margin-right: 10px;">Search</button>
                <a href="keranjang.php" class="btn btn-primary fa fa-shopping-cart col-2"></a>
            </form>
            <div id="search-result" class="dropdown-menu" style="display:none; position: absolute; width:100%;"></div>
        </nav>
    </div>
    <!-- selesai search bar -->

    <div class="product-details">
        <img src="data:image/jpeg;base64,<?php echo base64_encode($row['gambar_produk']); ?>" alt="<?php echo $row['nama_produk']; ?>">
        <h4><?php echo htmlspecialchars($row['nama_produk']); ?></h4>

        <?php if ($diskon > 0): ?>
            <p>Harga: <span style="text-decoration: line-through;">Rp. <?php echo number_format($harga_asli, 2, ',', '.'); ?></span></p>
            <p>Harga Setelah Diskon: Rp. <?php echo number_format($harga_setelah_diskon, 2, ',', '.'); ?></p>
        <?php else: ?>
            <p>Harga: Rp. <?php echo number_format($harga_asli, 2, ',', '.'); ?></p>
        <?php endif; ?>

        <ul>
            <?php foreach ($deskripsi_items as $item): ?>
                <li><?php echo htmlspecialchars($item); ?></li>
            <?php endforeach; ?>
        </ul>

        <p>Jumlah: <?php echo $row['jumlah_produk']; ?></p>
        <p>Diskon: <?php echo $row['diskon_produk']; ?>%</p>

        <div class="add-to-cart">
            <form action="tambahKeKeranjang.php" method="POST">
                <input type="hidden" name="id_produk" value="<?php echo $row['id_produk']; ?>">
                <input type="hidden" name="nama_produk" value="<?php echo htmlspecialchars($row['nama_produk']); ?>">
                <input type="hidden" name="harga_produk" value="<?php echo htmlspecialchars($row['harga_produk']); ?>">
                <input type="hidden" name="jumlah_produk" value="1"> 
                <button type="submit">Tambahkan ke Keranjang</button>
            </form>
        </div>
    </div>

    <!-- footer -->
    <footer>
        <h3 class="fw-bold">Vanko Petshop</h3>
        <h5 class="fw-bold">Tentang kami</h5>
        <p>Lorem ipsum dolor sit, amet consectetur adipisicing elit. Hic ipsum cumque corporis quas tenetur saepe. Assumenda fugit sequi officiis ipsam alias repellendus animi tempore accusantium.</p>
        <h6>Jl. Jae Sumantoro Godean (Pasar Godean)</h6>
        <div class="sosmed">
            <a href="#" class="fa fa-facebook"></a>
            <a href="#" class="fa fa-instagram"></a>
            <a href="#" class="fa fa-whatsapp"></a>
        </div>
    </footer>
    <!-- selesai footer -->

    <script>
    $(document).ready(function() {
        $('#search-input').on('input', function() {
            let searchQuery = $(this).val();
            
            if (searchQuery.length > 0) {
                $.ajax({
                    url: 'search_produk.php',
                    method: 'POST',
                    data: { query: searchQuery },
                    success: function(response) {
                        $('#search-result').html(response);
                        $('#search-result').show();
                    }
                });
            } else {
                $('#search-result').hide();
            }
        });

        $('#search-form').on('submit', function(e) {
            e.preventDefault();
            let searchQuery = $('#search-input').val();
            if (searchQuery.length > 0) {
                window.location.href = 'produk.php?search=' + searchQuery; 
            }
        });

        $(document).on('click', function(e) {
            if (!$(e.target).closest('#search').length) {
                $('#search-result').hide();
            }
        });
    });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcVjKTWBKt9KG" crossorigin="anonymous"></script>
</body>

</html>
