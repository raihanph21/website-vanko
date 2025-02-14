<?php
session_start();

session_unset();

session_destroy();

header("Location: https://5f96-2001-448a-404a-16f6-24ed-a2a6-4c99-b4eb.ngrok-free.app/google-login/skripsi/login.php");
exit();
?>

<?php
setcookie('id', '', time() - 60*60*24*30, '/'); 
setcookie('sess', '', time() - 60*60*24*30, '/');
header('Location: https://5f96-2001-448a-404a-16f6-24ed-a2a6-4c99-b4eb.ngrok-free.app/google-login/skripsi/index.php');
die();
?>