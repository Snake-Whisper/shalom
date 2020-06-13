<?php
error_reporting(E_ALL ^ E_NOTICE ^ E_DEPRECATED ^ E_STRICT);
require_once "Mail.php";
class MailManager
{
    function __construct ()
    {
        $this->config = require ("../config.php");
        $this->smtp = Mail::factory('smtp', array ('host' => $this->config['smtp']['host'],
                                    'port' => $this->config['smtp']['port'],
                                    'auth' => $this->config['smtp']['auth'],
                                    'username' => $this->config['smtp']['username'],
                                    'password' => $this->config['smtp']['password']));        
    }

    private function chkError($ret)
    {
        if (PEAR::isError($ret)) {
            echo "<p>" . $ret->getMessage() . "</p>\n";
        } else {
            echo "<p>Message successfully sent!</p>\n";
        }
    }

    public function sendRegisterToken(String $token, String $to)
    {
        $headers = array ('From' => $this->config['smtp']['username'],
                            'To' => $to,
                            'Subject' => "Please Verify your Registration");
        $mail = require ("templates/register.template");
        $ret = $this->smtp->send($to, $headers, $mail);
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

//$mail = new MailManager();
//$mail->sendRegisterToken("1d56v1d5f", "verf@web-utils.eu");
//$mail->sendSubscribeToken("vbdsvdfsg", "verf@web-utils.eu", "KartoffelWG");

?>