<?php 
require_once('google-api/vendor/autoload.php');
$gClient = new Google_Client();
$gClient-> setClientId("");
$gClient-> setClientSecret("");
$gClient-> setApplicationName("Vanko Petshop");
$gClient-> setRedirectUri("https://5f96-2001-448a-404a-16f6-24ed-a2a6-4c99-b4eb.ngrok-free.app/google-login/controller.php");
$gClient->addScope("https://www.googleapis.com/auth/plus.login https://www.googleapis.com/auth/userinfo.email");

$login_url = $gClient->createAuthUrl();
?>  