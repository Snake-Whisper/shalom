<?php
class MailManager () {
    function __construct() {
        define('__ROOT__', dirname(dirname(__FILE__)));
        $this->config = require (__ROOT__.'/config.php');
    }
}