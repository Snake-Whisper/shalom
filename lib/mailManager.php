<?php

require "Mail.php";

class MailManager {
    function __construct () {
        define('__ROOT__', dirname(dirname(__FILE__)));
        $this->config = require (__ROOT__.'/config.php');
        $this->smtp = Mail::factory('smtp', array ('host' => $this->config['smtp']['host'],
                                    'port' => $this->config['smtp']['port'],
                                    'auth' => $this->config['smtp']['auth'],
                                    'username' => $this->config['smtp']['username'],
                                    'password' => $this->config['smtp']['password']));
        $srv = "{" . $this->config["imap"]["host"] . ":" . $this->config["imap"]["port"] . "/novalidate-cert}INBOX";
        $this->imap = imap_open($srv, $this->config["imap"]["username"], $this->config["imap"]["password"]) or die('Cannot connect to Mailserver: ' . imap_last_error());
    }

    function sendTestMails ()
    {
        $adr = ["a", "b", "c"];
        foreach ($adr as $to) {
            $to .= "@shalom.web-utils.eu";
            echo "sending to $to\n";
            $headers = array ('From' => $this->config['smtp']['username'],
                                'To' => $to,
                                'Subject' => "Test Mail");
            $mail = "Testnachricht";
            for ($i=0; $i<10; $i++) {
                $this->smtp->send($to, $headers, $mail);
            }
        }
    
    }

    function work () {
        foreach (imap_search($this->imap, 'ALL') as $m) {
            echo "fetched one\n";
            $header = imap_headerinfo($this->imap, $m);
            var_dump($header);
            if (! $this->chkDist ($header)) {
                echo "del";
                continue;
            }
            //TODO: Implement?
            //$this->chkSender ($header);

            //$this->addFooter()
            echo "sending";

        }
    }
}

$m = new MailManager();
//$m->sendTestMails();
$m->work();