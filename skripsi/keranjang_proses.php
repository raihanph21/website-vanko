<?php
require_once '../config_midtrans.php'; 
require 'db_connection.php';

session_start();

$user_id = $_SESSION['user_id'];

if (!isset($_SESSION['keranjang'][$user_id])) {
    $_SESSION['keranjang'][$user_id] = [];
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'], $_POST['index'])) {
    $action = $_POST['action'];
    $index = $_POST['index'];

    if (isset($_SESSION['keranjang'][$user_id][$index])) {
        switch ($action) {
            case 'increase':
                $_SESSION['keranjang'][$user_id][$index]['jumlah_produk'] += 1;
                break;
            case 'decrease':
                if ($_SESSION['keranjang'][$user_id][$index]['jumlah_produk'] > 1) {
                    $_SESSION['keranjang'][$user_id][$index]['jumlah_produk'] -= 1;
                } else {
                    unset($_SESSION['keranjang'][$user_id][$index]);
                }
                break;
            case 'remove':
                unset($_SESSION['keranjang'][$user_id][$index]);
                break;
        }
    }
}

header('Location: keranjang.php');
exit();
?>

