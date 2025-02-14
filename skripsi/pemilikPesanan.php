<?php
session_start();
require_once 'db_connection.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'pemilik') {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : '';
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : '';
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10; 
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$show_failed = isset($_GET['show_failed']) ? true : false;

$base_query = "
    SELECT penjualan.id_penjualan, penjualan.total_penjualan, penjualan.status, penjualan.created_at, penjualan.nomor_hp, COALESCE(user.nama, penjualan.nama) as nama 
    FROM penjualan 
    LEFT JOIN user ON penjualan.id_user = user.id_user
    WHERE 1=1
";

$params = [];
$types = '';

if (!empty($start_date) && !empty($end_date)) {
    $base_query .= " AND DATE(penjualan.created_at) BETWEEN ? AND ? ";
    $params[] = $start_date;
    $params[] = $end_date;
    $types .= 'ss';
}

$count_query = "
    SELECT COUNT(*) AS total_records 
    FROM penjualan 
    LEFT JOIN user ON penjualan.id_user = user.id_user 
    WHERE 1=1
";
$count_params = [];
$count_types = '';

if (!empty($start_date) && !empty($end_date)) {
    $count_query .= " AND DATE(penjualan.created_at) BETWEEN ? AND ? ";
    $count_params[] = $start_date;
    $count_params[] = $end_date;
    $count_types .= 'ss';
}

if ($show_failed) {
    $count_query .= " AND (penjualan.status = 'gagal' OR penjualan.status = 'pending') ";
} else {
    $count_query .= " AND (penjualan.status = 'success' OR penjualan.status = 'lunas') ";
}

$count_stmt = $conn->prepare($count_query);

if (!empty($count_types)) {
    $count_stmt->bind_param($count_types, ...$count_params);
}

$count_stmt->execute();
$count_result = $count_stmt->get_result();
$total_records = $count_result->fetch_assoc()['total_records'];

$total_pages = ceil($total_records / $limit);

if ($show_failed) {
    $base_query .= " AND (penjualan.status = 'gagal' OR penjualan.status = 'pending') ";
} else {
    $base_query .=" AND (penjualan.status = 'success' OR penjualan.status = 'lunas') ";
}

$base_query .= " ORDER BY penjualan.created_at DESC LIMIT ? OFFSET ?";

$params[] = $limit;
$params[] = $offset;
$types .= 'ii';

$stmt = $conn->prepare($base_query);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();
?>

<html lang="en">
<head>
<meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pemilik Page | Pesanan</title>
    <link rel="stylesheet" type="text/css" href="../skripsi/css/stylePemilikPesanan.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
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
                    <li href="#_" class="nav-link text-center active"><a href="pemilikPesanan.php">Pesanan</a></li>
                    <li href="#_" class="nav-link text-center"><a href="logout.php">Logout</a></li>
                </div>
            </div>
        </div>
        <div class="col-9">
            <h1 class="text-start fw-bold mt-3 mb-3">Pesanan</h1>

            <!-- Filter Form -->
            <form method="GET" action="pemilikPesanan.php">
                <div class="row">
                    <div class="col">
                        <label for="start_date">Dari Tanggal:</label>
                        <input type="date" id="start_date" name="start_date" value="<?= htmlspecialchars($start_date); ?>">
                    </div>
                    <div class="col">
                        <label for="end_date">Sampai Tanggal:</label>
                        <input type="date" id="end_date" name="end_date" value="<?= htmlspecialchars($end_date); ?>">
                    </div>
                    <div class="col">
                        <label for="limit">Tampilkan:</label>
                        <select id="limit" name="limit">
                            <option value="10" <?= $limit == 10 ? 'selected' : '' ?>>10</option>
                            <option value="20" <?= $limit == 20 ? 'selected' : '' ?>>20</option>
                            <option value="50" <?= $limit == 50 ? 'selected' : '' ?>>50</option>
                        </select>
                    </div>
                    <div id="btnSubmit" class="col">
                        <button type="submit" class="btn btn-primary">Filter</button>
                        <a id="bPesananOffline" class="btn btn-primary" href="pemilikTambahPesananOffline.php">Input Pesanan Offline</a>
                    </div>
                </div>
            </form>

            <form method="GET" action="pemilikPesanan.php">
                <input type="hidden" name="start_date" value="<?= htmlspecialchars($start_date); ?>">
                <input type="hidden" name="end_date" value="<?= htmlspecialchars($end_date); ?>">
                <input type="hidden" name="limit" value="<?= htmlspecialchars($limit); ?>">
                <?php if ($show_failed): ?>
                    <button type="submit" class="btn btn-success">Transaksi Berhasil</button>
                <?php else: ?>
                    <button type="submit" name="show_failed" value="1" class="btn btn-danger">Transaksi Gagal</button>
                <?php endif; ?>
            </form>

            <h3 class="mt-4"><?= $show_failed ? 'Transaksi Gagal' : 'Transaksi Berhasil' ?></h3>
            <table class="table table-hover text-center table-bordered table-striped">
                <thead class="<?= $show_failed ? 'table-danger' : 'table-success' ?>">
                    <tr>
                        <th>Order ID</th>
                        <th>Pembeli</th>
                        <th>Total Transaksi</th>
                        <th>Tanggal</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr onclick="window.location.href='pemilikDetailPesanan.php?id_penjualan=<?= $row['id_penjualan'] ?>'">
                            <td><?= htmlspecialchars($row['id_penjualan']) ?></td>
                            <td><?= htmlspecialchars($row['nama']) ?></td>
                            <td>Rp<?= number_format($row['total_penjualan'], 0, ',', '.') ?></td>
                            <td><?= htmlspecialchars($row['created_at']) ?></td>
                            <td><?= htmlspecialchars($row['status']) ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
<div class="mt-3">
    <nav aria-label="Pagination">
        <ul class="pagination justify-content-center">
            <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                <a class="page-link" href="pemilikPesanan.php?start_date=<?= htmlspecialchars($start_date) ?>&end_date=<?= htmlspecialchars($end_date) ?>&limit=<?= $limit ?>&page=<?= $page - 1 ?><?= $show_failed ? '&show_failed=1' : '' ?>" aria-label="Previous">
                    <span aria-hidden="true">&laquo;</span>
                </a>
            </li>

            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                    <a class="page-link" href="pemilikPesanan.php?start_date=<?= htmlspecialchars($start_date) ?>&end_date=<?= htmlspecialchars($end_date) ?>&limit=<?= $limit ?>&page=<?= $i ?><?= $show_failed ? '&show_failed=1' : '' ?>"><?= $i ?></a>
                </li>
            <?php endfor; ?>

            <li class="page-item <?= $page >= $total_pages ? 'disabled' : '' ?>">
                <a class="page-link" href="pemilikPesanan.php?start_date=<?= htmlspecialchars($start_date) ?>&end_date=<?= htmlspecialchars($end_date) ?>&limit=<?= $limit ?>&page=<?= $page + 1 ?><?= $show_failed ? '&show_failed=1' : '' ?>" aria-label="Next">
                    <span aria-hidden="true">&raquo;</span>
                </a>
            </li>
        </ul>
    </nav>
</div>
        </div>
    </div>
</body>
</html>
