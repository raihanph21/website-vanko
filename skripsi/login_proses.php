<?php
session_start();
require_once 'db_connection.php';
require_once('../config.php');
require_once('../core/controller.Class.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $query = "SELECT id_user, nama, email, password, role FROM user WHERE email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            $_SESSION['user_id'] = $row['id_user'];
            $_SESSION['nama'] = $row['nama'];
            $_SESSION['email'] = $row['email'];
            $_SESSION['role'] = $row['role'];

            echo 'Session Data:';
            echo 'User ID: ' . $_SESSION['user_id'];
            echo 'Nama: ' . $_SESSION['nama'];
            echo 'Email: ' . $_SESSION['email'];
            echo 'Role: ' . $_SESSION['role'];

            if ($row['role'] == 'pelanggan') {
                header('Location: index.php');
            } elseif ($row['role'] == 'kasir') {
                header('Location: kasirPage.php');
            } elseif ($row['role'] == 'pemilik') {
                header('Location: pemilikPage.php');
            }
            exit();
        } else {
            echo "Password salah!";
        }
    } else {
        echo "Email tidak ditemukan!";
    }
}
?>
