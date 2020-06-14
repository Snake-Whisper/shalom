<?php

require_once "lib/TokenManager.php";
require_once "lib/userManager.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!isset($_POST["email"]) || !isset($_POST["pwd"]) || !isset($_POST["repeat"])) {
        echo "bad reqiest";
        die();
    }

    if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        echo "doesn't look like an email address";
        die();
    }

    if ($_POST['pwd'] != $_POST['repeat']) {
        echo "pwd doesn't match";
        die();
    }
    
    $usermanger = new userManager();
    $usermanger->register($_POST["email"], $_POST["pwd"]);
    } elseif (isset($_GET["token"])) {
    
    $tokenmanager = new TokenReciever();
    $tokenmanager->recvRegisterToken($_GET["token"]);
} else { ?>
<html>
<head>
    <title>Registration</title>
</head>
<body>
    <form method="POST">
        <input name="email" type="email" require><br>
        <input name="pwd" type="password" require><br>
        <input name="repeat" type="password" require><br>
        <input type="submit"><br>
    </form>
</body>
</html>
<?php } ?>