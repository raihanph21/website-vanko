<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'pelanggan') {
    header('Location: login.php');
    exit();
}

require 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_produk = $_POST['id_produk'];
    $jumlah_produk = $_POST['jumlah_produk'];
    $user_id = $_SESSION['user_id'];

    $query = $conn->prepare("SELECT nama_produk, harga_produk, diskon_produk FROM produk WHERE id_produk = ?");
    $query->bind_param('i', $id_produk);
    $query->execute();
    $result = $query->get_result();
    $produk = $result->fetch_assoc();

    if ($produk) {
        $harga_asli = $produk['harga_produk'];
        $diskon = $produk['diskon_produk'];

        if (!isset($_SESSION['keranjang'][$user_id])) {
            $_SESSION['keranjang'][$user_id] = [];
        }

        $found = false;
        foreach ($_SESSION['keranjang'][$user_id] as &$item) {
            if ($item['id_produk'] == $id_produk) {
                $item['jumlah_produk'] += $jumlah_produk;
                $found = true;
                break;
            }
        }

        if (!$found) {
            $_SESSION['keranjang'][$user_id][] = [
                'id_produk' => $id_produk,
                'nama_produk' => $produk['nama_produk'],
                'harga_produk' => $harga_asli,  
                'jumlah_produk' => $jumlah_produk,
                'diskon' => $diskon  
            ];
        }

        header('Location: keranjang.php'); 
        exit();
    } else {
        echo "Produk tidak ditemukan.";
    }
}
?>
