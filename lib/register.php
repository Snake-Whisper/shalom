<?php
if (isset($_GET["token"])) {
    require_once "lib/TokenManager.php";
    $tokenmanager = new TokenReciever();
    $tokenmanager->recvRegisterToken();
}
?>