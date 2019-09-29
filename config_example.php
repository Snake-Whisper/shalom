<?php

//copy this with you're modifications to config.php

return array(
    'user' => array (
        'mysql' => array (
            'host' => '127.0.0.1',
            'port' => 3306,
            'username' => 'username',
            'databasename' => 'dbName',
            'passphrase' => 'SECRETSECRET'
        )
    ),

    'aliases' => array (
        'mysql' => array (
            'host' => '127.0.0.1',
            'port' => 3306,
            'username' => 'username',
            'databasename' => 'dbName',
            'passphrase' => 'pasphrase'
        )
    ),
    
    'redis' => array (
        "host" => "127.0.0.1",
        "port" => "6379",
    ),
);

?>