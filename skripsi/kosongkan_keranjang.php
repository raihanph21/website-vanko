<?php
session_start();

$user_id = $_SESSION['user_id'];

if (isset($_SESSION['keranjang'][$user_id])) {
    unset($_SESSION['keranjang'][$user_id]); 
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'keranjang sudah kosong']);
}
?>
