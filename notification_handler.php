<?php
session_start();

require_once 'skripsi/db_connection.php';
require_once 'vendor/autoload.php'; 

\Midtrans\Config::$serverKey = 'SB-Mid-server-8djDjS_Clk-IG32sVZALuBco';
\Midtrans\Config::$isProduction = false;
\Midtrans\Config::$isSanitized = true;
\Midtrans\Config::$is3ds = true;

try {
    $notif = new \Midtrans\Notification();

    error_log(print_r($notif, true));

    $transaction_status = $notif->transaction_status;
    $order_id = $notif->order_id;

    error_log("Order ID: " . $order_id . " Status transaksi: " . $transaction_status);

    $stmt = $conn->prepare("SELECT id_user FROM penjualan WHERE id_penjualan = ?");
    $stmt->bind_param('s', $order_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $user_id = $row['id_user'];
        error_log("User ID ditemukan: " . $user_id);
    } else {
        error_log("Order ID tidak ditemukan di tabel penjualan.");
        die("Order ID tidak valid.");
    }

    $status = match ($transaction_status) {
        'capture', 'settlement' => 'success',
        'pending' => 'pending',
        'deny', 'expire', 'cancel' => 'failed',
        default => 'unknown',
    };

    $stmt = $conn->prepare("UPDATE penjualan SET status = ? WHERE id_penjualan = ?");
    $stmt->bind_param('ss', $status, $order_id);
    if ($stmt->execute()) {
        error_log("Status penjualan berhasil diupdate ke: " . $status);

        $stmt_detail = $conn->prepare("UPDATE detail_penjualan SET status = ? WHERE id_penjualan = ?");
        $stmt_detail->bind_param('ss', $status, $order_id);
        
        if ($stmt_detail->execute()) {
            error_log("Status detail_penjualan berhasil diupdate ke: " . $status);

            if ($status == 'success') {
                $query_detail = $conn->prepare("SELECT id_produk, jumlah_produk FROM detail_penjualan WHERE id_penjualan = ?");
                $query_detail->bind_param('s', $order_id);
                $query_detail->execute();
                $result_detail = $query_detail->get_result();

                while ($row_detail = $result_detail->fetch_assoc()) {
                    $id_produk = $row_detail['id_produk'];
                    $jumlah_dibeli = $row_detail['jumlah_produk'];

                    $update_stok = $conn->prepare("UPDATE produk SET jumlah_produk = jumlah_produk - ? WHERE id_produk = ?");
                    $update_stok->bind_param('ii', $jumlah_dibeli, $id_produk);
                    
                    if ($update_stok->execute()) {
                        error_log("Stok produk dengan id_produk $id_produk berhasil dikurangi sebanyak $jumlah_dibeli.");
                    } else {
                        error_log("Gagal mengupdate stok produk dengan id_produk $id_produk: " . $update_stok->error);
                    }
                }
            }

        } else {
            error_log("Gagal mengupdate status detail_penjualan: " . $stmt_detail->error);
        }
    } else {
        error_log("Gagal mengupdate status penjualan: " . $stmt->error);
    }

} catch (Exception $e) {
    error_log("Error pada transaksi: " . $e->getMessage());
    echo "Error pada transaksi: " . $e->getMessage();
}