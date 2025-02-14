<?php
require_once 'db_connection.php'; 
$searchTerm = $_GET['query'] ?? '';

if ($searchTerm != '') {
    $query = "SELECT * FROM produk WHERE nama_produk LIKE ? OR id_produk LIKE ?";
    $stmt = $conn->prepare($query);
    $searchTerm = "%{$searchTerm}%";
    $stmt->bind_param('ss', $searchTerm, $searchTerm);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $output = [];
    while ($row = $result->fetch_assoc()) {
        $output[] = $row;
    }
    
    echo json_encode($output);
} else {
    echo json_encode([]);
}

$stmt->close();
$conn->close();
?>
