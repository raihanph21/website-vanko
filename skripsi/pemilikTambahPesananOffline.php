<?php
session_start();
require_once 'db_connection.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'pemilik') {
    header('Location: login.php');
    exit();
}

$queryProduk = "SELECT id_produk, nama_produk, harga_produk FROM produk WHERE status_produk = 'aktif'";
$resultProduk = $conn->query($queryProduk);
$produkList = $resultProduk->fetch_all(MYSQLI_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_pembeli = $_POST['nama_pembeli'];
    $nomor_hp = $_POST['nomor_hp'];
    $status_pembayaran = $_POST['status_pembayaran'];
    $created_at = date('Y-m-d H:i:s');
    $total_transaksi = 0;

    $queryInsertPenjualan = "INSERT INTO penjualan (id_user, total_penjualan, status, created_at, nomor_hp, nama) 
                             VALUES (NULL, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($queryInsertPenjualan);
    $stmt->bind_param('dssss', $total_transaksi, $status_pembayaran, $created_at, $nomor_hp, $nama_pembeli);
    $stmt->execute();

    $id_penjualan = $conn->insert_id;

    $produk = $_POST['produk'];
    foreach ($produk as $item) {
        $id_produk = intval($item['id_produk']);
        $jumlah_produk = intval($item['jumlah']);
        $harga_produk = floatval($item['harga']); 
        $total_harga = $jumlah_produk * $harga_produk;
        $total_transaksi += $total_harga; 

        $queryInsertDetail = "INSERT INTO detail_penjualan (id_penjualan, id_produk, jumlah_produk, harga_jual_produk, total_penjualan, status) 
                              VALUES (?, ?, ?, ?, ?, 'Offline')";
        $stmt = $conn->prepare($queryInsertDetail);
        $stmt->bind_param('iiidd', $id_penjualan, $id_produk, $jumlah_produk, $harga_produk, $total_harga);
        $stmt->execute();
    }

    $queryUpdateTotal = "UPDATE penjualan SET total_penjualan = ? WHERE id_penjualan = ?";
    $stmt = $conn->prepare($queryUpdateTotal);
    $stmt->bind_param('di', $total_transaksi, $id_penjualan);
    $stmt->execute();

    header('Location: pemilikPesanan.php?success=1');
    exit();
}



?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Pesanan Offline</title>
    <link rel="stylesheet" href="../skripsi/css/stylePemilikTambahPesananOffline.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat&display=swap" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4">Tambah Pesanan Offline</h1>
        <form method="POST" action="">
            <div class="mb-3">
                <label for="nama_pembeli" class="form-label">Nama Pembeli</label>
                <input type="text" class="form-control" id="nama_pembeli" name="nama_pembeli" required>
            </div>
            <div class="mb-3">
                <label for="nomor_hp" class="form-label">Nomor HP</label>
                <input type="text" class="form-control" id="nomor_hp" name="nomor_hp" required>
            </div>
            <div id="produk-container">
                <div class="row mb-3 produk-item">
                    <div class="col-md-5">
                        <label for="produk[0][id_produk]" class="form-label">Produk</label>
                        <select class="form-select" name="produk[0][id_produk]" required>
                            <option value="" selected>Pilih Produk</option>
                            <?php foreach ($produkList as $produk): ?>
                                <option value="<?= $produk['id_produk'] ?>" data-harga="<?= $produk['harga_produk'] ?>">
                                    <?= $produk['nama_produk'] ?> (Rp <?= number_format($produk['harga_produk'], 0, ',', '.') ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="produk[0][jumlah]" class="form-label">Jumlah</label>
                        <input type="number" class="form-control" name="produk[0][jumlah]" min="1" required>
                    </div>
                    <input type="hidden" name="produk[0][harga]" class="harga-produk">
                </div>
            </div>
            <button type="button" class="btn btn-secondary mb-3" id="add-produk">Tambah Produk</button>
            <div class="mb-3">
                <label for="status_pembayaran" class="form-label">Status Pembayaran</label>
                <select class="form-select" id="status_pembayaran" name="status_pembayaran" required>
                    <option value="Lunas">Lunas</option>
                    <option value="Belum Lunas">Belum Lunas</option>
                </select>
            </div>
            <button id="abc" type="submit" class="btn btn-success">Simpan Pesanan</button>
            <a id="def" href="pemilikPesanan.php" class="btn btn-secondary">Kembali</a>
        </form>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            let produkIndex = 1;

            document.getElementById('add-produk').addEventListener('click', function () {
                const container = document.getElementById('produk-container');
                const newProduk = document.querySelector('.produk-item').cloneNode(true);
                newProduk.querySelectorAll('input, select').forEach(input => {
                    input.name = input.name.replace(/\[0\]/, `[${produkIndex}]`);
                    if (input.type === 'number') input.value = '';
                });
                container.appendChild(newProduk);
                produkIndex++;
            });

            document.getElementById('produk-container').addEventListener('change', function (e) {
                if (e.target.tagName === 'SELECT') {
                    const selectedOption = e.target.options[e.target.selectedIndex];
                    const harga = selectedOption.getAttribute('data-harga');
                    const hargaInput = e.target.closest('.produk-item').querySelector('.harga-produk');
                    hargaInput.value = harga || '';
                }
            });
        });
    </script>
</body>
</html>
