<?php
session_start();
ob_start(); 
require_once 'db_connection.php';
require_once '../vendor/autoload.php';

\Midtrans\Config::$serverKey = '';
\Midtrans\Config::$isProduction = false;
\Midtrans\Config::$isSanitized = true;
\Midtrans\Config::$is3ds = true;

if (!isset($_SESSION['user_id'])) {
    die('User ID tidak ditemukan di sesi. Pastikan user sudah login.');
}
$user_id = $_SESSION['user_id'];

$query = $conn->prepare('SELECT nama, email FROM user WHERE id_user = ?');
$query->bind_param('i', $user_id);
$query->execute();
$result = $query->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    error_log('Data user tidak ditemukan untuk user_id: ' . $user_id);
    die('Data user tidak ditemukan.');
}

$keranjang = isset($_SESSION['keranjang'][$user_id]) ? $_SESSION['keranjang'][$user_id] : [];

if (isset($_POST['alamat_pengiriman']) && isset($_POST['nomor_hp'])) {
    $alamat_pengiriman = $_POST['alamat_pengiriman'];
    $nomor_hp = $_POST['nomor_hp'];
} else {
    die('Alamat pengiriman atau nomor HP tidak tersedia.');
}

$total_harga = 0;
$item_details = [];

foreach ($keranjang as $item) {
    if ($item['diskon'] > 0) {
        $harga_setelah_diskon = $item['harga_produk'] * (1 - ($item['diskon'] / 100));
        $harga_setelah_diskon = round($harga_setelah_diskon, 2); 
    } else {
        $harga_setelah_diskon = $item['harga_produk'];
    }

    $item_details[] = [
        'id' => $item['id_produk'],
        'price' => $harga_setelah_diskon,  
        'quantity' => $item['jumlah_produk'],
        'name' => $item['nama_produk']
    ];

    $total_harga += round($item['jumlah_produk'] * $harga_setelah_diskon, 2);
}

$status = 'pending'; 
$insert_penjualan = $conn->prepare('INSERT INTO penjualan (total_penjualan, id_user, status, created_at, alamat_pengiriman, nomor_hp) VALUES (?, ?, ?, NOW(), ?, ?)');
$insert_penjualan->bind_param('disss', $total_harga, $user_id, $status, $alamat_pengiriman, $nomor_hp);
$insert_penjualan->execute();


$id_penjualan = $conn->insert_id;

foreach ($keranjang as $item) {
    $harga_jual_produk = 0;
    foreach ($item_details as $detail_item) {
        if ($detail_item['id'] == $item['id_produk']) {
            $harga_jual_produk = $detail_item['price']; 
            break;
        }
    }

    $insert_detail_penjualan = $conn->prepare('INSERT INTO detail_penjualan (total_penjualan, jumlah_produk, harga_jual_produk, id_penjualan, id_produk, status, created_at, alamat_pengiriman, nomor_hp) VALUES (?, ?, ?, ?, ?, ?, NOW(), ?, ?)');
    $insert_detail_penjualan->bind_param('diiissss', $total_harga, $item['jumlah_produk'], $harga_jual_produk, $id_penjualan, $item['id_produk'], $status, $alamat_pengiriman, $nomor_hp);
    $insert_detail_penjualan->execute();
}

$transaction_details = [
    'order_id' => $id_penjualan, 
    'gross_amount' => $total_harga,
];

$transaction_data = [
    'transaction_details' => $transaction_details,
    'item_details' => $item_details,
    'customer_details' => [
        'first_name' => $user['nama'],
        'last_name' => 'Pengguna',
        'email' => $user['email'],
        'phone' => $nomor_hp,  
    ],
    'callbacks' => [
        'finish' => 'https://5f96-2001-448a-404a-16f6-24ed-a2a6-4c99-b4eb.ngrok-free.app/google-login/skripsi/index.php',
    ]
];

try {
    $snapToken = \Midtrans\Snap::getSnapToken($transaction_data);
    echo json_encode(['snapToken' => $snapToken]);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}

unset($_SESSION['keranjang'][$user_id]);
?>
