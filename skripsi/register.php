<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vanko Petshop</title>

    <!-- link css -->
    <link rel="stylesheet" href="../skripsi/css/styleRegister.css">

    <!-- link bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">

    <!-- Link font montserrat -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat&display=swap" rel="stylesheet">

</head>

<body>
    <div class="form-container">

        <form action="register_proses.php" method="post">
            <h3>Daftar Sekarang</h3>
            <input type="text" name="nama" required placeholder="Masukkan nama Anda">
            <input type="email" name="email" required placeholder="Masukkan email Anda">
            <input type="password" name="password" required placeholder="Masukkan kata sandi Anda">
            <input type="password" name="cpassword" required placeholder="Konfirmasi kata sandi Anda    ">
            <select name="user_type">
                <option value="user">Pengguna</option>
            </select>
            <input type="submit" name="submit" value="Daftar" class="form-btn">
            <p>Sudah punya akun? <a href="login.php">masuk sekarang</a></p>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</body>

</html>