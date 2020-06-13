<?php
error_reporting(E_ALL ^ E_NOTICE ^ E_DEPRECATED ^ E_STRICT);
require_once "Mail.php";
require_once "userManager.php";

class TokenSender
{
    function __construct ()
    {
        $this->config = require ("../config.php");
        $this->smtp = Mail::factory('smtp', array ('host' => $this->config['smtp']['host'],
                                    'port' => $this->config['smtp']['port'],
                                    'auth' => $this->config['smtp']['auth'],
                                    'username' => $this->config['smtp']['username'],
                                    'password' => $this->config['smtp']['password']));
        $this->redis = new Redis();
        $this->redis->connect($this->config["redis"]["host"], $this->config["redis"]["port"]);
    }

    private function struuid() {
        $s=uniqid("", true);
        $num= hexdec(str_replace(".","",(string)$s));
        $index = '1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $base= strlen($index);
        $out = '';
        for($t = floor(log10($num) / log10($base)); $t >= 0; $t--) {
            $a = floor($num / pow($base,$t));
            $out = $out.substr($index,$a,1);
            $num = $num-($a*pow($base,$t));
        }
        return $out;
    }

    private function chkError($ret)
    {
        if (PEAR::isError($ret)) {
            echo "<p>" . $ret->getMessage() . "</p>\n";
        } else {
            echo "<p>Message successfully sent!</p>\n";
        }
    }

    public function sendRegisterToken(String $email, String $pwd)
    {
        $headers = array ('From' => $this->config['smtp']['username'],
                            'To' => $email,
                            'Subject' => "Please verify your registration");
        $load = json_encode (["email" => $email, "pwd" => password_hash($pwd, PASSWORD_DEFAULT)]);
        $token = $this->struuid();
        $this->redis->set($token, $load, $this->config["redis"]["timeout"]);
        $link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]/register.php?token=$token";
        $mail = require ("templates/register.template");
        $ret = $this->smtp->send($email, $headers, $mail);
        $this->chkError($ret);
    }

    public function sendSubscribeToken(String $token, String $to, String $distName)
    {
        $headers = array ('From' => $this->config['smtp']['username'],
                            'To' => $to,
                            'Subject' => "Please Verify your Subscription");
        $mail = require ("templates/subscribe.template");
        $ret = $this->smtp->send($to, $headers, $mail);
        $this->chkError($ret);
    }

    public function sendUnSubscribeToken(String $token, String $to, String $distName)
    {
        $headers = array ('From' => $this->config['smtp']['username'],
                            'To' => $to,
                            'Subject' => "Please Verify your Unsubscription");
        $mail = require ("templates/unsubscribe.template");
        $ret = $this->smtp->send($to, $headers, $mail);
        $this->chkError($ret);
    }
}

class TokenReciever {
    function __construct ()
    {
        $this->config = require ("../config.php");
        $this->redis = new Redis();
        $this->redis->connect($this->config["redis"]["host"], $this->config["redis"]["port"]);
    }

    function recvRegisterToken ()
    {
        $load = $this->redis->get($_GET["token"]);
        if (! $load) {
            echo "token unknown or already used";
            die();
        }
        $user = json_decode($load);
        $usermanager = new UserManager();
        if (! (isset($user["pwd"]) && isset($user["email"]))) {
            echo "token references bad values";
            die();
        }
        $usermanager->addUser($user["email"], $user["pwd"]);
        $this->redis->delete($_GET["token"]);

    }
}

//$mail = new TokenSender();
//$mail->sendRegisterToken("1d56v1d5f", "verf@web-utils.eu");
//$mail->sendSubscribeToken("vbdsvdfsg", "verf@web-utils.eu", "KartoffelWG");

?>