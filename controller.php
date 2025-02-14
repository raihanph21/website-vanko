<?php
require_once('core/controller.Class.php');
require_once('config.php');

session_start();

if (isset($_GET['code'])) {
    $token = $gClient->fetchAccessTokenWithAuthCode($_GET["code"]);

    if (isset($token['error']) != "invalid_grant") {
        $_SESSION['user_id'] = $info['id_user'];
        $_SESSION['email'] = $info['email'];
        $_SESSION['nama'] = $info['givenName'];

        session_regenerate_id(true);

        echo 'Session User ID: ' . $_SESSION['user_id'];
        echo 'Session Email: ' . $_SESSION['email'];
        echo 'Session Nama: ' . $_SESSION['nama'];
    }
} else {
    header("location: https://5f96-2001-448a-404a-16f6-24ed-a2a6-4c99-b4eb.ngrok-free.app/google-login/skripsi/login.php");
    exit();
}

if(isset($token["error"])!= "invalid_grant") { 
    $oAuth = new Google\Service\Oauth2($gClient);
    $userData = $oAuth->userinfo_v2_me->get();

    $Controller = new Controller;
    echo $Controller->insertData(
        array(
            'email' => $userData['email'],
            'givenName' => $userData['givenName']
    ));
} else {
    header("location: https://5f96-2001-448a-404a-16f6-24ed-a2a6-4c99-b4eb.ngrok-free.app/google-login/skripsi/login.php");
    exit();
}
?>