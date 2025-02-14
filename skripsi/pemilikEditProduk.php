<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'pemilik') {
    header('Location: login.php');
    exit();
}
require_once 'db_connection.php';

if (!isset($_GET['id'])) {
    die("ID produk tidak ditemukan.");
}

$id_produk = $_GET['id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_produk = $_POST['nama_produk'];
    $harga_produk = $_POST['harga_produk'];
    $deskripsi_produk = $_POST['deskripsi_produk'];
    $jumlah_produk = $_POST['jumlah_produk'];
    $diskon_produk = $_POST['diskon_produk'];
    $status_produk = $_POST['status_produk'];

    if (isset($_FILES['gambar_produk']) && $_FILES['gambar_produk']['error'] == UPLOAD_ERR_OK) {
        $query = "UPDATE produk SET nama_produk = ?, harga_produk = ?, deskripsi_produk = ?, gambar_produk = ?, jumlah_produk = ?, diskon_produk = ?, status_produk = ? WHERE id_produk = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('sdsbdisi', $nama_produk, $harga_produk, $deskripsi_produk, $null, $jumlah_produk, $diskon_produk, $status_produk, $id_produk);

        // Bagian upload gambar besar
        $fp = fopen($_FILES['gambar_produk']['tmp_name'], 'rb');
        while (!feof($fp)) {
            $stmt->send_long_data(3, fread($fp, 8192));
        }
        fclose($fp);

    } else {
        $query = "UPDATE produk SET nama_produk = ?, harga_produk = ?, deskripsi_produk = ?, jumlah_produk = ?, diskon_produk = ?, status_produk = ? WHERE id_produk = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('sdsdisi', $nama_produk, $harga_produk, $deskripsi_produk, $jumlah_produk, $diskon_produk, $status_produk, $id_produk);
    }

    if ($stmt->execute()) {
        echo "Produk berhasil diupdate!";
    } else {
        echo "Gagal mengupdate produk: " . $stmt->error;
    }
}

$query = "SELECT * FROM produk WHERE id_produk = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $id_produk);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows != 1) {
    die("Produk tidak ditemukan.");
}

$row = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pemilik Page | Edit Produk</title>
    <!-- link css -->
    <link rel="stylesheet" type="text/css" href="../skripsi/css/stylePemilikEditProduk.css">

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
        <h1 class="mt-2 mb-3 fw-bold text-center">Edit Produk</h1>
            <form action="pemilikEditProduk.php?id=<?php echo $id_produk; ?>" method="post" enctype="multipart/form-data">
                <label for="nama_produk">Nama Produk:</label><br>
                <input type="text" id="nama_produk" name="nama_produk" value="<?php echo htmlspecialchars($row['nama_produk']); ?>" required><br>
                <label for="harga_produk">Harga Produk:</label><br>
                <input type="number" id="harga_produk" name="harga_produk" value="<?php echo htmlspecialchars($row['harga_produk']); ?>" required><br>
                <label for="deskripsi_produk">Deskripsi Produk:</label><br>
                <textarea id="deskripsi_produk" name="deskripsi_produk" required><?php echo htmlspecialchars($row['deskripsi_produk']); ?></textarea><br>
                <label for="gambar_produk">Gambar Produk (biarkan kosong jika tidak ingin mengubah):</label><br>
                <input type="file" id="gambar_produk" name="gambar_produk" accept="image/*"><br>
                <label for="jumlah_produk">Jumlah Produk:</label><br>
                <input type="number" id="jumlah_produk" name="jumlah_produk" value="<?php echo htmlspecialchars($row['jumlah_produk']); ?>" required><br>
                <label for="diskon_produk">Diskon Produk:</label><br>
                <input type="number" id="diskon_produk" name="diskon_produk" value="<?php echo htmlspecialchars($row['diskon_produk']); ?>" required><br>
                <label for="status_produk">Status Produk:</label><br>
                <select id="status_produk" name="status_produk">
                    <option value="aktif" <?php if ($row['status_produk'] == 'aktif') echo 'selected'; ?>>Aktif</option>
                    <option value="nonaktif" <?php if ($row['status_produk'] == 'nonaktif') echo 'selected'; ?>>Nonaktif</option>
                </select><br><br>
                <button type="submit">Update Produk</button>
            </form>
            <a href="pemilikPage.php" class="btn btn-primary">Kembali</a>
</body>
</html>
