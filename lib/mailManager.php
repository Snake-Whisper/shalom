<?php
error_reporting(E_ALL ^ E_NOTICE ^ E_DEPRECATED ^ E_STRICT);
require_once "Mail.php";
class MailManager
{
    function __construct () {
        $this->config = require ("../config.php");
        $this->smtp = Mail::factory('smtp', array ('host' => $this->config['smtp']['host'],
                                    'port' => $this->config['smtp']['port'],
                                    'auth' => true,
                                    'username' => $this->config['smtp']['username'],
                                    'password' => $this->config['smtp']['password']));
        
    }

    function sendToken($token, $to, $reason) {
        $headers = array ('From' => $this->config['smtp']['username'], 'To' => $to, 'Subject' => "Please Verify your", 'Reply-To' => $this->config['smtp']['username']);
        $this->smtp->send($to, $headers, "Please verify the toke " . $token);
    }
}

$mail = new MailManager();
$mail->sendToken("1d56v1d5f", "verf@web-utils.eu", "Registration");
if (PEAR::isError($mail)) {
    echo("<p>" . $mail->getMessage() . "</p>");
    } else {
    echo("<p>Message successfully sent!</p>");
    }
?>