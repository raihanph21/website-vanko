<?php
session_start();
require_once 'db_connection.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'pemilik') {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

$currentMonth = isset($_GET['month']) ? $_GET['month'] : date('m');
$currentYear = isset($_GET['year']) ? $_GET['year'] : date('Y');

$startDate = new DateTime("$currentYear-$currentMonth-01");
$endDate = (clone $startDate)->modify('last day of this month');

$weeksData = [];
$today = clone $endDate;
for ($i = 0; $i < 5; $i++) {
    $endOfWeek = clone $today;
    $startOfWeek = clone $today;
    $startOfWeek->modify('last monday')->modify("-$i week");
    $endOfWeek->modify('next sunday')->modify("-$i week");

    if ($startOfWeek < $startDate) {
        $startOfWeek = clone $startDate;
    }
    if ($endOfWeek > $endDate) {
        $endOfWeek = clone $endDate;
    }

    $startOfWeekFormatted = $startOfWeek->format('Y-m-d');
    $endOfWeekFormatted = $endOfWeek->format('Y-m-d');
    $bulan = $startOfWeek->format('F');
    $mingguKe = 5 - $i;

    $queryPerMinggu = "
        SELECT 
            SUM(p.total_penjualan) AS total_pendapatan, 
            (SELECT SUM(r.total_harga_restock) FROM restock r WHERE r.tanggal_restock BETWEEN '$startOfWeekFormatted' AND '$endOfWeekFormatted') AS total_pengeluaran
        FROM penjualan p 
        WHERE p.created_at BETWEEN '$startOfWeekFormatted' AND '$endOfWeekFormatted'";

    $resultPerMinggu = $conn->query($queryPerMinggu);
    $row = $resultPerMinggu->fetch_assoc();
    $totalKeuntunganMinggu = $row['total_pendapatan'] - $row['total_pengeluaran'];

    $weeksData[] = [
        'minggu' => "Minggu $mingguKe ($startOfWeekFormatted - $endOfWeekFormatted)",
        'bulan' => $bulan,
        'total_pendapatan' => $row['total_pendapatan'],
        'total_pengeluaran' => $row['total_pengeluaran'],
        'total_keuntungan' => $totalKeuntunganMinggu
    ];
}



?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Laporan Keuangan</title>
    <link rel="stylesheet" type="text/css" href="../skripsi/css/stylePemilikLaporan.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat&display=swap" rel="stylesheet">

</head>
<body>

    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3" id="sticky-sidebar">
            <div class="sticky-top">
                <div class="nav flex-column">
                    <img src="../skripsi/assests/img/150x150.png" class="rounded-circle" alt="Profile Image">
                    <li class="text-center"><?= htmlspecialchars($_SESSION['nama']); ?></li>
                    <li class="nav-link text-center"><a href="pemilikPage.php">Stok</a></li>
                    <li class="nav-link text-center active"><a href="pemilikLaporan.php">Laporan Keuangan</a></li>
                    <li class="nav-link text-center"><a href="pemilikPesanan.php">Pesanan</a></li>
                    <li class="nav-link text-center"><a href="logout.php">Logout</a></li>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-md-9 mt-4">
            <!-- Filter Form -->
            <form method="GET" class="mb-3">
                <div class="row">
                    <div class="col-md-3">
                        <select name="month" class="form-select">
                            <?php for ($m = 1; $m <= 12; $m++): ?>
                                <option value="<?= sprintf("%02d", $m); ?>" <?= $m == $currentMonth ? 'selected' : ''; ?>>
                                    <?= date("F", mktime(0, 0, 0, $m, 1)); ?>
                                </option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select name="year" class="form-select">
                            <?php for ($y = date("Y") - 5; $y <= date("Y"); $y++): ?>
                                <option value="<?= $y; ?>" <?= $y == $currentYear ? 'selected' : ''; ?>><?= $y; ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary">Filter</button>
                    </div>
                </div>
            </form>

            <h2 class="fw-bold">Laporan 5 Minggu Terakhir</h2>
            <table class="table table-bordered">
                <thead class="table-primary">
                    <tr>
                        <th>Minggu</th>
                        <th>Bulan</th>
                        <th id="tTotalPend">Total Pendapatan</th>
                        <th id="tTotalPeng">Total Pengeluaran</th>
                        <th id="tTotalKeun">Total Keuntungan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($weeksData as $week): ?>
                        <tr>
                            <td><?= $week['minggu']; ?></td>
                            <td><?= $week['bulan']; ?></td>
                            <td id="totalPend"><span>Rp</span><?= number_format($week['total_pendapatan'], 2); ?></td>
                            <td id="totalPeng"><span>Rp</span><?= number_format($week['total_pengeluaran'], 2); ?></td>
                            <td id="totalKeun"><span>Rp</span><?= number_format($week['total_keuntungan'], 2); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

        </div>
    </div>
</body>
</html>
