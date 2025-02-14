<?php
session_start();
require_once 'db_connection.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'pemilik') {
    header('Location: https://5f96-2001-448a-404a-16f6-24ed-a2a6-4c99-b4eb.ngrok-free.app/google-login/skripsi/login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_produk = $_POST['id_produk'];
    $harga_restock = $_POST['harga_restock'];
    $jumlah_produk = $_POST['jumlah_produk'];
    $tanggal_restock = date('Y-m-d'); 

    $total_harga_restock = $harga_restock * $jumlah_produk;

    $query_restock = "INSERT INTO restock (id_produk, harga_restock, jumlah_produk, total_harga_restock, tanggal_restock) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query_restock);
    $stmt->bind_param("idids", $id_produk, $harga_restock, $jumlah_produk, $total_harga_restock, $tanggal_restock);

    if ($stmt->execute()) {
        $query_update_produk = "UPDATE produk SET jumlah_produk = jumlah_produk + ? WHERE id_produk = ?";
        $stmt_update = $conn->prepare($query_update_produk);
        $stmt_update->bind_param("ii", $jumlah_produk, $id_produk);

        if ($stmt_update->execute()) {
            echo "Restock berhasil dan jumlah produk diperbarui.";
        } else {
            echo "Restock berhasil, tapi gagal memperbarui jumlah produk: " . $stmt_update->error;
        }
    } else {
        echo "Terjadi kesalahan saat melakukan restock: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pemilik | Restock</title>
    <!-- link css -->
    <link rel="stylesheet" type="text/css" href="../skripsi/css/stylePemilikRestock.css">

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

<h1 class="fw-bold mt-1">Restock Produk</h1>

    <form action="pemilikRestock.php" method="post">
        <label for="id_produk">Pilih Produk:</label><br>
        <select id="id_produk" name="id_produk" required>
            <?php
            $query_produk = "SELECT id_produk, nama_produk FROM produk";
            $result = $conn->query($query_produk);

            while ($row = $result->fetch_assoc()) {
                echo "<option value='" . $row['id_produk'] . "'>" . $row['nama_produk'] . "</option>";
            }
            ?>
        </select><br><br>
        
        <label for="harga_restock">Harga Pembelian per Produk:</label><br>
        <input type="number" step="0.01" id="harga_restock" name="harga_restock" required><br>

        <label for="jumlah_produk">Jumlah Produk:</label><br>
        <input type="number" id="jumlah_produk" name="jumlah_produk" required><br><br>

        <button type="submit">Restock Produk</button>
    </form>

    <a href="pemilikPage.php" class="btn btn-primary">Kembali</a>


</body>
</html>
