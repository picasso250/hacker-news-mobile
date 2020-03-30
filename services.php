<?php

$mc = new Memcached();
$mc->addServers(array(
    array($env['memcached']['host'], $env['memcached']['port']),
));
return array(
    'cache' => $mc,
);
