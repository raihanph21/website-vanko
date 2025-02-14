<?php
session_start();
require_once 'db_connection.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'pemilik') {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_produk = $_POST['nama_produk'];
    $harga_produk = $_POST['harga_produk'];
    $harga_restock = $_POST['harga_restock']; 
    $deskripsi_produk = $_POST['deskripsi_produk']; 
    $jumlah_produk = $_POST['jumlah_produk'];
    $diskon_produk = $_POST['diskon_produk'];
    $status_produk = 'aktif'; 

    $total_harga_restock = $harga_restock * $jumlah_produk;

    if (isset($_FILES['gambar_produk']['tmp_name']) && !empty($_FILES['gambar_produk']['tmp_name'])) {
        $tmpFilePath = $_FILES['gambar_produk']['tmp_name'];
        if (!is_uploaded_file($tmpFilePath)) {
            die('File upload failed.');
        }
    } else {
        die('No file uploaded or upload failed.');
    }

    $conn->begin_transaction();

    try {
        $query_produk = "INSERT INTO produk (nama_produk, harga_produk, deskripsi_produk, gambar_produk, jumlah_produk, diskon_produk, status_produk) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt_produk = $conn->prepare($query_produk);
        $stmt_produk->bind_param("sdsbsis", $nama_produk, $harga_produk, $deskripsi_produk, $null, $jumlah_produk, $diskon_produk, $status_produk);

        $fp = fopen($tmpFilePath, 'rb');
        if ($fp === false) {
            throw new Exception('Failed to open file for reading.');
        }
        while (!feof($fp)) {
            $stmt_produk->send_long_data(3, fread($fp, 8192));
        }
        fclose($fp);

        if (!$stmt_produk->execute()) {
            throw new Exception("Terjadi kesalahan saat menambahkan produk: " . $stmt_produk->error);
        }

        $id_produk_baru = $conn->insert_id;

        $tanggal_restock = date('Y-m-d'); 
        $query_restock = "INSERT INTO restock (id_produk, harga_restock, jumlah_produk, total_harga_restock, tanggal_restock) VALUES (?, ?, ?, ?, ?)";
        $stmt_restock = $conn->prepare($query_restock);
        $stmt_restock->bind_param("idids", $id_produk_baru, $harga_restock, $jumlah_produk, $total_harga_restock, $tanggal_restock);

        if (!$stmt_restock->execute()) {
            throw new Exception("Terjadi kesalahan saat menambahkan restock: " . $stmt_restock->error);
        }

        $conn->commit();
        echo "Produk berhasil ditambahkan dan restock dicatat dengan total harga restock.";
    } catch (Exception $e) {
        $conn->rollback();
        echo $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pemilik | Tambah Produk</title>
    <!-- link css -->
    <link rel="stylesheet" type="text/css" href="../skripsi/css/stylePemilikTambahProduk.css">

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

<h1 class="mt-2 mb-3 fw-bold">Tambah Produk</h1>

<form action="pemilikTambahProduk.php" method="post" enctype="multipart/form-data">
    <label for="nama_produk">Nama Produk:</label><br>
    <input type="text" id="nama_produk" name="nama_produk" required><br>
    
    <label for="harga_produk">Harga Produk:</label><br>
    <input type="number" id="harga_produk" name="harga_produk" required><br>
    
    <label for="harga_restock">Harga Restock:</label><br>
    <input type="number" id="harga_restock" name="harga_restock" required><br>
    
    <label for="deskripsi_produk">Deskripsi Produk:</label><br>
    <textarea id="deskripsi_produk" name="deskripsi_produk" required></textarea>
    <small>Gunakan tanda | untuk memisahkan poin deskripsi (contoh: Deskripsi 1|Deskripsi 2|Deskripsi 3)</small><br>

    <label for="gambar_produk">Gambar Produk:</label><br>
    <input type="file" id="gambar_produk" name="gambar_produk" accept="image/*" required><br>
    
    <label for="jumlah_produk">Jumlah Produk:</label><br>
    <input type="number" id="jumlah_produk" name="jumlah_produk" required><br>
    
    <label for="diskon_produk">Diskon Produk:</label><br>
    <input type="number" id="diskon_produk" name="diskon_produk" required><br>
    
    <label for="status_produk">Status Produk:</label><br>
    <select id="status_produk" name="status_produk">
        <option value="aktif">Aktif</option>
        <option value="nonaktif">Nonaktif</option>
    </select><br><br>
    
    <button type="submit">Tambah Produk</button>
</form>
    <a href="pemilikPage.php" class="btn btn-primary">Kembali</a>
</body>
</html>
