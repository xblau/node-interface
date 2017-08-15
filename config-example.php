<?php

defined( 'NODEMON_RUNNING') || die( 'Access denied.' );

$nodeconfig = array(
    'serverurl' => 'http://127.0.0.1:9332/',
    'username' => 'username',
    'password' => 'password',
    'services' => array(
        'connect' => false,
        'verify' => false,
        'broadcast' => false,
    ),
    'onionurl' => '',
    'donations' => '',
);

?>
