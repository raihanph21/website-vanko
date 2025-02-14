<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'pelanggan') {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$keranjang = isset($_SESSION['keranjang'][$user_id]) ? $_SESSION['keranjang'][$user_id] : [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    require_once 'keranjang_proses.php';
}

$total_harga = 0;

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="../skripsi/css/styleKeranjang.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vanko Petshop | Keranjang</title>
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="SB-Mid-client-G37Wz3Q_wdlKfr3g"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Link Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <!-- Link font montserrat -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat&display=swap" rel="stylesheet">
</head>

<body>
    <h1>Keranjang Anda</h1>

    <table>
        <tr>
            <th>Produk</th>
            <th id="thKeranjang">Jumlah</th>
            <th>Harga</th>
            <th>Total</th>
            <th>Aksi</th>
        </tr>
        <?php if (!empty($keranjang)) : ?>
            <?php foreach ($keranjang as $index => $item) : 
                $harga_asli = $item['harga_produk'];
                $diskon = $item['diskon'];
                $harga_setelah_diskon = ($diskon > 0) ? $harga_asli - ($harga_asli * $diskon / 100) : $harga_asli;

                $subtotal = $item['jumlah_produk'] * $harga_setelah_diskon;
                $total_harga += $subtotal;

                $nama_produk = htmlspecialchars($item['nama_produk']);
                if (strlen($nama_produk) > 60) {
                    $nama_produk = substr($nama_produk, 0, 60) . '...';
                }
            ?>
                <tr>
                    <td><?php echo $nama_produk; ?></td>
                    <td><?php echo htmlspecialchars($item['jumlah_produk']); ?></td>
                    <td>
                        <?php if ($diskon > 0): ?>
                            <s>Rp <?php echo number_format($harga_asli, 2, ',', '.'); ?></s><br>
                            Rp <?php echo number_format($harga_setelah_diskon, 2, ',', '.'); ?>
                        <?php else: ?>
                            Rp <?php echo number_format($harga_asli, 2, ',', '.'); ?>
                        <?php endif; ?>
                    </td>
                    <td>Rp <?php echo number_format($subtotal, 2, ',', '.'); ?></td>
                    <td>
                        <form action="keranjang_proses.php" method="post" style="display: inline;">
                            <input type="hidden" name="action" value="increase">
                            <input type="hidden" name="index" value="<?php echo $index; ?>">
                            <button type="submit" class="fa fa-plus rounded-0"></button>
                        </form>
                        <form action="keranjang.php" method="post" style="display: inline;">
                            <input type="hidden" name="action" value="decrease">
                            <input type="hidden" name="index" value="<?php echo $index; ?>">
                            <button type="submit" class="fa fa-minus"></button>
                        </form>
                        <form action="keranjang.php" method="post" style="display: inline;">
                            <input type="hidden" name="action" value="remove">
                            <input type="hidden" name="index" value="<?php echo $index; ?>">
                            <button type="submit" class="fa fa-trash"></button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            <tr class="total-row">
                <td colspan="3">Total Harga:</td>
                <td>Rp <?php echo number_format($total_harga, 2, ',', '.'); ?></td>
                <td></td>
            </tr>
        <?php else : ?>
            <tr>
                <td colspan="5">Keranjang Anda kosong.</td>
            </tr>
        <?php endif; ?>
    </table>
    
    <form action="checkout.php" method="post">
        <label for="alamat_pengiriman">Alamat Pengiriman:</label>
        <input type="text" name="alamat_pengiriman" id="alamat_pengiriman" required>

        <label for="nomor_hp">Nomor HP:</label>
        <input type="text" name="nomor_hp" id="nomor_hp" required pattern="\d{10,15}" title="Nomor HP harus terdiri dari 10-15 digit angka">

        <div class="button-container">
            <a href="produk.php" class="back-button">Kembali</a>
            <button id="pay-button" type="submit">Bayar</button>
        </div>
    </form>

    <script>
        $('#pay-button').click(function(event) {
            event.preventDefault();
            
            var alamat_pengiriman = $('#alamat_pengiriman').val();
            var nomor_hp = $('#nomor_hp').val();

            if (!alamat_pengiriman || !nomor_hp) {
                alert('Alamat pengiriman dan nomor HP harus diisi.');
                return;
            }

            $.ajax({
                url: 'checkout.php',
                method: 'POST',
                data: {
                    alamat_pengiriman: alamat_pengiriman,
                    nomor_hp: nomor_hp
                },
                dataType: 'json',
                success: function(data) {
                    if (data.snapToken) {
                        snap.pay(data.snapToken, {
                            onSuccess: function(result) {
                                alert('Pembayaran berhasil!');
                                $.ajax({
                                    url: 'kosongkan_keranjang.php',
                                    method: 'POST',
                                    success: function(response) {
                                        window.location.href = 'index.php';
                                    },
                                    error: function(error) {
                                        console.log("Gagal mengosongkan keranjang: " + error);
                                        window.location.href = 'index.php';
                                    }
                                });
                            },
                            onPending: function(result) {
                                alert('Menunggu pembayaran!');
                            },
                            onError: function(result) {
                                alert('Pembayaran gagal!');
                            }
                        });
                    } else {
                        alert('Gagal mendapatkan token pembayaran.');
                    }
                },
                error: function(xhr, status, error) {
                    alert('Terjadi kesalahan. Silakan coba lagi.');
                }
            });
        });
    </script>
</body>

</html>
