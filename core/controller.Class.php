<?php
class Connect extends PDO
{
    public function __construct()
    {
        parent::__construct(
            "mysql:host=localhost;dbname=vanko",
            'root',
            '',
            array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8")
        );
        $this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    }
}
class Controller
{

    function generateCode($length)
{
    $chars = "vwxyzABCD02789";
    $code = "";
    $clen = strlen($chars) - 1;
    while (strlen($code) < $length) {
        $code .= $chars[mt_rand(0, $clen)];
    }
    return $code;
}

    function insertData($data)
    {
        $db = new Connect;
        $checkUser = $db->prepare("SELECT * FROM user WHERE email=:email");
        $checkUser->execute(array(
            'email' => $data['email']
        ));
        $info = $checkUser->fetch(PDO::FETCH_ASSOC);

        $info['id_user'] ??= false;
        if (!$info["id_user"]) {
            $session = $this->generateCode(10);
            $insertNewUser = $db->prepare("INSERT INTO user (nama, email, password, role) VALUES (:f_name, :email, :password, :role)");
            $insertNewUser->execute([
                ':f_name' => $data["givenName"],
                ':email' => $data["email"],
                ':password' => $this->generateCode(5), 
                ':role' => "pelanggan"
            ]);

            if ($insertNewUser) {
                $_SESSION['user_id'] = $db->lastInsertId();
                $_SESSION['nama'] = $data['givenName'];
                $_SESSION['email'] = $data['email'];
                $_SESSION['role'] = 'pelanggan';

                header('Location: https://5f96-2001-448a-404a-16f6-24ed-a2a6-4c99-b4eb.ngrok-free.app/google-login/skripsi/index.php');
                exit();
            } else {
                return "Error inserting user!";
            }
        } else {
            $_SESSION['user_id'] = $info['id_user'];
            $_SESSION['nama'] = $info['nama'];
            $_SESSION['email'] = $info['email'];
            $_SESSION['role'] = $info['role'];

            header('Location: https://5f96-2001-448a-404a-16f6-24ed-a2a6-4c99-b4eb.ngrok-free.app/google-login/skripsi/index.php');
            exit();
        }
    }
}
?>