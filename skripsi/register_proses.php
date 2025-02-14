<?php
require 'db_connection.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['nama']) && isset($_POST['email']) && isset($_POST['password'])) {
        $nama = $_POST['nama'];
        $email = $_POST['email'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $role = 'pelanggan';

        $sql = "SELECT * FROM user WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            echo "Email sudah digunakan.";
        } else {
            $sql = "INSERT INTO user (nama, email, password, role) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssss", $nama, $email, $password, $role);

            if ($stmt->execute()) {
                echo "Registrasi berhasil. Silakan login.";
                header("Location: index.php");
                exit();
            } else {
                echo "Registrasi gagal: " . $stmt->error;
            }
        }

        $stmt->close();
    } else {
        echo "Mohon isi semua data.";
    }
}

$conn->close();
