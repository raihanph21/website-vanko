<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'pemilik') {
    header('Location: login.php');
    exit();
}
require_once 'db_connection.php';

$query = "SELECT * FROM produk";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pemilik Page</title>
    <!-- link css -->
    <link rel="stylesheet" type="text/css" href="../skripsi/css/stylePemilik.css">

    <!-- link font awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- link bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">

    <!-- Link font montserrat -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat&display=swap" rel="stylesheet">

</head>

<body>
    <div class="row">
        <div class="col-3" id="sticky-sidebar">
            <div class="sticky-top">
                <div class="nav flex-column">
                    <img src="../skripsi/assests/img/150x150.png" class="rounded-circle"></img>
                    <li href="#_" class="text-center"><?= htmlspecialchars($_SESSION['nama']); ?></li>
                    <li href="#_" class="nav-link text-center active"><a href="pemilikPage.php">Stok</a></li>
                    <li href="#_" class="nav-link text-center"><a href="pemilikLaporan.php">Laporan Keuangan</a></li>
                    <li href="#_" class="nav-link text-center"><a href="pemilikPesanan.php">Pesanan</a></li>
                    <li href="#_" class="nav-link text-center"><a href="logout.php">Logout</a></li>
                </div>
            </div>
        </div>
        <div class="col-9">
            <h1 class="fw-bold">STOK PRODUK</h1>
            <a id="bTambah" href="pemilikTambahProduk.php" class="btn btn-primary">Tambah Produk</a>
            <a id="bRestock" href="pemilikRestock.php" class="btn btn-primary">Restock</a>
            <input type="text" id="search-bar" onkeyup="searchProduk()" class="form-control" placeholder="Search for items...">
            <div id="search-results"></div>
            <table id="myTable" class="table table-striped table-hover text-center table-bordered mt-3">
                <thead class="table-primary">
                    <tr>
                        <th>ID</th>
                        <th >Nama Produk</th>
                        <th  id='tHargaP'>Harga</th>
                        <th>Diskon</th>
                        <th  id='tHargaPDiskon'>Harga Diskon</th>
                        <th>Jumlah</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="table-body">
                    <?php
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            $statusClass = $row['status_produk'] == 'aktif' ? 'dot-green' : 'dot-red';
                            $harga_asli = $row['harga_produk'];
                            $diskon = $row['diskon_produk'];
                            $harga_diskon = $harga_asli - ($harga_asli * $diskon / 100);

                            echo "<tr>";
                            echo "<td>{$row['id_produk']}</td>";
                            echo "<td id= 'abc'>{$row['nama_produk']}</td>";
                            echo "<td id='hargaP'> Rp" . number_format($row['harga_produk'], 0, ',', '.') . "</td>";
                            echo "<td>{$diskon}%</td>";
                            echo "<td id='hargaPDiskon'> Rp" . number_format($harga_diskon, 0, ',', '.') . "</td>";
                            echo "<td>{$row['jumlah_produk']}</td>";
                            echo "<td><span class='status-dot $statusClass'></span> " . $row['status_produk'] . "</td>";
                            echo '<td id= "tEdit"><a href="pemilikEditProduk.php?id=' . $row['id_produk'] . '">Edit</a></td>';
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='7'>Tidak ada produk.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <script>
        function searchProduk() {
            const input = document.getElementById('search-bar').value;
            const results = document.getElementById('search-results');
            if (input.length > 0) {
                const xhr = new XMLHttpRequest();
                xhr.open('GET', 'searchProduk.php?query=' + input, true);
                xhr.onload = function () {
                    if (this.status === 200) {
                        results.style.display = 'block';
                        results.innerHTML = this.responseText;
                    }
                }
                xhr.send();
            } else {
                results.style.display = 'none';
            }
        }

        document.getElementById('search-bar').addEventListener('keydown', function (event) {
            if (event.key === 'Enter') {
                event.preventDefault();
                const query = this.value;
                const xhr = new XMLHttpRequest();
                xhr.open('GET', 'searchProdukTable.php?query=' + query, true);
                xhr.onload = function () {
                    if (this.status === 200) {
                        document.getElementById('table-body').innerHTML = this.responseText;
                        document.getElementById('search-results').style.display = 'none';
                    }
                }
                xhr.send();
            }
        });
    </script>
</body>

</html>
