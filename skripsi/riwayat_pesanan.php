<?php
require 'db_connection.php';
require_once('../config.php');
require_once('../core/controller.Class.php');
session_start();

$id_user = $_SESSION['user_id'];

$limit = 10;

$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$pesananQuery = "SELECT id_penjualan, total_penjualan, status, DATE_FORMAT(created_at, '%d-%m-%Y') AS tanggal_pesanan 
                 FROM penjualan 
                 WHERE id_user = ?";

$filters = [];
if (isset($_GET['start_date']) && !empty($_GET['start_date'])) {
    $start_date = $_GET['start_date'];
    $pesananQuery .= " AND created_at >= ?";
    $filters[] = $start_date;
}

if (isset($_GET['end_date']) && !empty($_GET['end_date'])) {
    $end_date = $_GET['end_date'];
    $pesananQuery .= " AND created_at <= ?";
    $filters[] = $end_date;
}

$pesananQuery .= " ORDER BY created_at DESC LIMIT ? OFFSET ?";

$stmt = $conn->prepare($pesananQuery);

if (!empty($filters)) {
    if (count($filters) === 1) {
        $stmt->bind_param("isi", $id_user, $filters[0], $limit, $offset);
    } else {
        $stmt->bind_param("issi", $id_user, $filters[0], $filters[1], $limit, $offset);
    }
} else {
    $stmt->bind_param("iii", $id_user, $limit, $offset);
}

$stmt->execute();
$pesananResult = $stmt->get_result();

$totalQuery = "SELECT COUNT(*) AS total FROM penjualan WHERE id_user = ?";
$totalStmt = $conn->prepare($totalQuery);
$totalStmt->bind_param("i", $id_user);
$totalStmt->execute();
$totalResult = $totalStmt->get_result();
$totalPesanan = $totalResult->fetch_assoc()['total'];

$totalPages = ceil($totalPesanan / $limit);

?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Vanko Petshop | Riwayat Pesanan</title>
  <!-- Link css -->
  <link rel="stylesheet" href="../skripsi/css/styleRiwayatPesanan.css">
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
  <!-- Navbar -->
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
    <h3 class="h3pesanan fw-bold text-center">Riwayat Pesanan</h3>

    <form method="GET" class="row g-3 mb-3">
        <div class="col d-flex flex-column">
            <label for="start_date" class="form-label">Dari Tanggal:</label>
            <input type="date" name="start_date" id="start_date" class="form-control" value="<?php echo isset($_GET['start_date']) ? $_GET['start_date'] : ''; ?>">
        </div>
        <div class="col d-flex flex-column">
            <label for="end_date" class="form-label">Sampai Tanggal:</label>
            <input type="date" name="end_date" id="end_date" class="form-control" value="<?php echo isset($_GET['end_date']) ? $_GET['end_date'] : ''; ?>">
        </div>
        <div class="col mt-5">
            <button id="btnSubmit" type="submit" class="btn btn-primary">Filter</button>
        </div>
    </form>

    <?php if ($pesananResult->num_rows > 0) : ?>
      <table class="table table-striped table-hover mt-4">
        <thead>
          <tr>
            <th>No</th>
            <th>ID Pesanan</th>
            <th>Tanggal Pesanan</th>
            <th id="tHargaP">Total</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          <?php $no = $offset + 1; ?>
          <?php while ($row = $pesananResult->fetch_assoc()) : ?>
            <tr onclick="window.location.href='detail_pesanan.php?id=<?php echo $row['id_penjualan']; ?>'">
              <td><?php echo $no++; ?></td>
              <td><?php echo htmlspecialchars($row['id_penjualan']); ?></td>
              <td><?php echo htmlspecialchars($row['tanggal_pesanan']); ?></td>
              <td id="hargaP">Rp<?php echo number_format($row['total_penjualan'], 0, ',', '.'); ?></td>
              <td><?php echo htmlspecialchars($row['status']); ?></td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>

      <nav id="pagination">
        <ul class="pagination justify-content-center mt-4">
          <?php if ($page > 1): ?>
            <li class="page-item">
              <a class="page-link" href="?page=<?php echo $page - 1; ?>&start_date=<?php echo isset($_GET['start_date']) ? $_GET['start_date'] : ''; ?>&end_date=<?php echo isset($_GET['end_date']) ? $_GET['end_date'] : ''; ?>">Sebelumnya</a>
            </li>
          <?php endif; ?>

          <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">
              <a class="page-link" href="?page=<?php echo $i; ?>&start_date=<?php echo isset($_GET['start_date']) ? $_GET['start_date'] : ''; ?>&end_date=<?php echo isset($_GET['end_date']) ? $_GET['end_date'] : ''; ?>"><?php echo $i; ?></a>
            </li>
          <?php endfor; ?>

          <?php if ($page < $totalPages): ?>
            <li class="page-item">
              <a class="page-link" href="?page=<?php echo $page + 1; ?>&start_date=<?php echo isset($_GET['start_date']) ? $_GET['start_date'] : ''; ?>&end_date=<?php echo isset($_GET['end_date']) ? $_GET['end_date'] : ''; ?>">Selanjutnya</a>
            </li>
          <?php endif; ?>
        </ul>
      </nav>

    <?php else : ?>
      <p class="text-center">Tidak ada riwayat pesanan.</p>
    <?php endif; ?>
</div>


  <!-- Footer -->
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

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</body>
</html>
